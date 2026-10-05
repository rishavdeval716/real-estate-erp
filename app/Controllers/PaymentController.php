<?php

namespace App\Controllers;

use App\Models\PaymentModel;
use App\Models\BookingModel;
use App\Models\PaymentScheduleModel;
use App\Models\PaymentScheduleItemModel;
use App\Models\ReceiptModel;
use App\Models\AuditLogModel;

class PaymentController extends BaseController
{
    protected PaymentModel $paymentModel;
    protected BookingModel $bookingModel;
    protected PaymentScheduleItemModel $itemModel;
    protected ReceiptModel $receiptModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
        $this->bookingModel = new BookingModel();
        $this->itemModel    = new PaymentScheduleItemModel();
        $this->receiptModel = new ReceiptModel();
    }

    /**
     * List all recorded payments
     */
    public function index()
    {
        $filters = [
            'search'         => trim((string)$this->request->getGet('search')),
            'status'         => trim((string)$this->request->getGet('status')),
            'payment_method' => trim((string)$this->request->getGet('payment_method')),
            'booking_id'     => $this->request->getGet('booking_id'),
            'sort'           => $this->request->getGet('sort') ?? 'payments.payment_date',
            'order'          => $this->request->getGet('order') ?? 'DESC',
        ];

        $result = $this->paymentModel->getPaymentsWithDetails($filters, 15);

        return view('payments/index', [
            'title'    => 'Payment Management',
            'payments' => $result['payments'],
            'pager'    => $result['pager'],
            'filters'  => $filters,
        ]);
    }

    /**
     * Record a new payment against a booking
     */
    public function store()
    {
        $rules = [
            'booking_id'               => 'required|is_not_unique[bookings.id]',
            'payment_date'             => 'required|valid_date',
            'amount'                   => 'required|numeric|greater_than[0]',
            'payment_method'           => 'required|in_list[Cash,Bank Transfer,NEFT,RTGS,IMPS,UPI,Cheque,Online Gateway,Other]',
            'status'                   => 'required|in_list[Pending,Received,Failed,Cancelled,Refunded]',
            'payment_schedule_item_id' => 'permit_empty|is_not_unique[payment_schedule_items.id]',
            'transaction_reference'    => 'permit_empty|max_length[100]',
            'bank_name'                => 'permit_empty|max_length[100]',
            'cheque_number'            => 'permit_empty|max_length[50]',
            'remarks'                  => 'permit_empty|max_length[500]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $bookingId = (int)$this->request->getPost('booking_id');
        $booking = $this->bookingModel->find($bookingId);
        if (!$booking) {
            return redirect()->back()->withInput()->with('error', 'Booking record not found.');
        }

        $amount = (float)$this->request->getPost('amount');
        $status = $this->request->getPost('status');
        $itemId = (int)($this->request->getPost('payment_schedule_item_id') ?: 0);

        $db = \Config\Database::connect();

        // Check outstanding amount
        $receivedSumRow = $db->table('payments')
            ->select('COALESCE(SUM(amount), 0) as total_received')
            ->where('booking_id', $bookingId)
            ->where('status', 'Received')
            ->get()
            ->getRowArray();
        $totalPaid = (float)($receivedSumRow['total_received'] ?? 0);
        $outstanding = max(0, (float)$booking['final_amount'] - $totalPaid);

        // Validation rule 5: Payment cannot exceed outstanding amount without authorized adjustment
        if ($status === 'Received' && $amount > ($outstanding + 0.01)) {
            return redirect()->back()->withInput()->with(
                'error',
                "Payment amount (₹" . number_format($amount, 2) . ") exceeds the total outstanding balance (₹" . number_format($outstanding, 2) . "). Excess payment allocation is not permitted."
            );
        }

        $paymentNumber = $this->paymentModel->generatePaymentNumber();
        $userId        = session()->get('user_id');

        $db->transStart();

        $paymentData = [
            'payment_number'           => $paymentNumber,
            'booking_id'               => $bookingId,
            'customer_id'              => $booking['customer_id'],
            'payment_schedule_item_id' => $itemId > 0 ? $itemId : null,
            'payment_date'             => $this->request->getPost('payment_date'),
            'amount'                   => $amount,
            'payment_method'           => $this->request->getPost('payment_method'),
            'transaction_reference'    => $this->request->getPost('transaction_reference'),
            'bank_name'                => $this->request->getPost('bank_name'),
            'cheque_number'            => $this->request->getPost('cheque_number'),
            'remarks'                  => $this->request->getPost('remarks'),
            'status'                   => $status,
            'received_by'              => $userId,
        ];

        $paymentId = $this->paymentModel->insert($paymentData);

        // If received, allocate payment to schedule item & generate official receipt
        $receiptNumber = null;
        if ($status === 'Received') {
            // Allocate to specific or auto-selected milestone
            if ($itemId > 0) {
                $this->itemModel->allocatePayment($itemId, $amount);
            } else {
                // Find first unpaid milestone for this booking
                $schedule = $db->table('payment_schedules')->where('booking_id', $bookingId)->get()->getRowArray();
                if ($schedule) {
                    $nextMilestone = $db->table('payment_schedule_items')
                        ->where('payment_schedule_id', $schedule['id'])
                        ->where('status !=', 'Paid')
                        ->orderBy('due_date', 'ASC')
                        ->orderBy('id', 'ASC')
                        ->get()
                        ->getRowArray();

                    if ($nextMilestone) {
                        $this->itemModel->allocatePayment($nextMilestone['id'], $amount);
                        // Update payment record with assigned item
                        $this->paymentModel->update($paymentId, ['payment_schedule_item_id' => $nextMilestone['id']]);
                    }
                }
            }

            // Generate Receipt
            $receiptNumber = $this->receiptModel->generateReceiptNumber();
            $this->receiptModel->insert([
                'receipt_number'        => $receiptNumber,
                'payment_id'            => $paymentId,
                'booking_id'            => $bookingId,
                'customer_id'           => $booking['customer_id'],
                'receipt_date'          => $paymentData['payment_date'],
                'amount'                => $amount,
                'payment_method'        => $paymentData['payment_method'],
                'transaction_reference' => $paymentData['transaction_reference'],
                'remarks'               => "Official receipt for payment {$paymentNumber}",
                'created_at'            => date('Y-m-d H:i:s'),
            ]);
        }

        // Audit Log
        AuditLogModel::record(
            'Payment Recorded',
            'payments',
            $paymentId,
            "Recorded payment {$paymentNumber} of ₹" . number_format($amount, 2) . " for Booking #{$booking['booking_number']} (Status: {$status})" . ($receiptNumber ? ", Receipt: {$receiptNumber}" : ""),
            $userId
        );

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Failed to record payment due to a database error.');
        }

        $msg = "Payment {$paymentNumber} recorded successfully.";
        if ($receiptNumber) {
            $msg .= " Receipt {$receiptNumber} generated automatically.";
        }

        return redirect()->to(base_url("bookings/view/{$bookingId}"))->with('success', $msg);
    }

    /**
     * Cancel payment and reverse milestone allocation
     */
    public function cancel(int $id)
    {
        $payment = $this->paymentModel->find($id);
        if (!$payment) {
            return redirect()->back()->with('error', 'Payment record not found.');
        }

        if ($payment['status'] === 'Cancelled') {
            return redirect()->back()->with('error', 'Payment is already cancelled.');
        }

        $userId = session()->get('user_id');
        $db = \Config\Database::connect();
        $db->transStart();

        // Reverse allocation if it was Received and had milestone
        if ($payment['status'] === 'Received' && !empty($payment['payment_schedule_item_id'])) {
            $item = $this->itemModel->find($payment['payment_schedule_item_id']);
            if ($item) {
                $newPaid = max(0, (float)$item['paid_amount'] - (float)$payment['amount']);
                $newRemaining = (float)$item['amount'] - $newPaid;
                $newStatus = $newPaid <= 0 ? 'Pending' : 'Partially Paid';
                if ($item['due_date'] && $item['due_date'] < date('Y-m-d') && $newStatus !== 'Paid') {
                    $newStatus = 'Overdue';
                }

                $this->itemModel->update($item['id'], [
                    'paid_amount'      => $newPaid,
                    'remaining_amount' => $newRemaining,
                    'status'           => $newStatus,
                ]);
            }
        }

        // Update payment status
        $this->paymentModel->update($id, [
            'status' => 'Cancelled',
        ]);

        // Audit Log
        AuditLogModel::record(
            'Payment Cancelled',
            'payments',
            $id,
            "Cancelled payment {$payment['payment_number']} of ₹" . number_format($payment['amount'], 2),
            $userId
        );

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Failed to cancel payment due to database error.');
        }

        return redirect()->back()->with('success', "Payment {$payment['payment_number']} has been cancelled.");
    }
}
