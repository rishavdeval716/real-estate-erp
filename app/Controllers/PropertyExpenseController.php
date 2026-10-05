<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PropertyExpenseModel;
use App\Models\ExpenseCategoryModel;
use App\Models\PropertyModel;
use App\Models\ProjectModel;
use App\Models\PropertyUnitModel;
use App\Models\AuditLogModel;

class PropertyExpenseController extends BaseController
{
    protected $expenseModel;
    protected $categoryModel;
    protected $propertyModel;
    protected $projectModel;
    protected $unitModel;
    protected $auditModel;

    public function __construct()
    {
        $this->expenseModel  = new PropertyExpenseModel();
        $this->categoryModel = new ExpenseCategoryModel();
        $this->propertyModel = new PropertyModel();
        $this->projectModel  = new ProjectModel();
        $this->unitModel     = new PropertyUnitModel();
        $this->auditModel    = new AuditLogModel();
    }

    public function index()
    {
        $filters = [
            'category_id' => $this->request->getGet('category_id'),
            'property_id' => $this->request->getGet('property_id'),
            'status'      => $this->request->getGet('status'),
            'date_from'   => $this->request->getGet('date_from'),
            'date_to'     => $this->request->getGet('date_to'),
            'keyword'     => trim($this->request->getGet('keyword') ?? ''),
        ];

        $page   = (int)($this->request->getGet('page') ?? 1);
        $limit  = 15;
        $offset = ($page - 1) * $limit;

        $expenses = $this->expenseModel->getFilteredExpenses($filters, $limit, $offset);
        $summary  = $this->expenseModel->getExpenseSummary($filters);

        $categories = $this->categoryModel->where('status', 'active')->findAll();
        $properties = $this->propertyModel->where('deleted_at IS NULL')->findAll();

        return view('expenses/index', [
            'title'      => 'Property Expense Management',
            'expenses'   => $expenses,
            'summary'    => $summary,
            'categories' => $categories,
            'properties' => $properties,
            'filters'    => $filters,
            'page'       => $page,
        ]);
    }

    public function create()
    {
        $categories = $this->categoryModel->where('status', 'active')->findAll();
        $properties = $this->propertyModel->where('deleted_at IS NULL')->findAll();
        $projects   = $this->projectModel->findAll();

        return view('expenses/create', [
            'title'      => 'Record Property Expense',
            'categories' => $categories,
            'properties' => $properties,
            'projects'   => $projects,
        ]);
    }

    public function store()
    {
        $rules = [
            'title'          => 'required|min_length[3]|max_length[200]',
            'category_id'    => 'required|numeric',
            'expense_date'   => 'required|valid_date',
            'payee_vendor'   => 'required|max_length[150]',
            'amount'         => 'required|numeric|greater_than[0]',
            'payment_method' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $amount    = (float)$this->request->getPost('amount');
        $taxAmount = (float)($this->request->getPost('tax_amount') ?? 0);
        $total     = $amount + $taxAmount;

        // Handle receipt upload if provided
        $receiptName = null;
        $file = $this->request->getFile('receipt_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/receipts/', $newName);
            $receiptName = 'uploads/receipts/' . $newName;
        }

        $expenseCode = $this->expenseModel->generateExpenseCode();
        $userId = session()->get('user_id') ?? 1;

        $data = [
            'expense_code'     => $expenseCode,
            'category_id'      => $this->request->getPost('category_id'),
            'property_id'      => $this->request->getPost('property_id') ?: null,
            'property_unit_id' => $this->request->getPost('property_unit_id') ?: null,
            'project_id'       => $this->request->getPost('project_id') ?: null,
            'title'            => trim($this->request->getPost('title')),
            'expense_date'     => $this->request->getPost('expense_date'),
            'payee_vendor'     => trim($this->request->getPost('payee_vendor')),
            'amount'           => $amount,
            'tax_amount'       => $taxAmount,
            'total_amount'     => $total,
            'payment_method'   => $this->request->getPost('payment_method'),
            'status'           => $this->request->getPost('status') ?? 'Paid',
            'receipt_file'     => $receiptName,
            'notes'            => trim($this->request->getPost('notes') ?? ''),
            'created_by'       => $userId,
        ];

        $expenseId = $this->expenseModel->insert($data);

        $this->auditModel->log(
            'EXPENSE_CREATED',
            "Property Expense {$expenseCode} recorded for ₹" . number_format($total, 2) . " ({$data['title']})",
            'property_expenses',
            $expenseId
        );

        return redirect()->to('/expenses')->with('success', "Expense {$expenseCode} recorded successfully.");
    }

    public function show($id)
    {
        $expense = $this->expenseModel->select('property_expenses.*, expense_categories.name as category_name, properties.title as property_title, projects.name as project_name')
            ->join('expense_categories', 'expense_categories.id = property_expenses.category_id', 'left')
            ->join('properties', 'properties.id = property_expenses.property_id', 'left')
            ->join('projects', 'projects.id = property_expenses.project_id', 'left')
            ->where('property_expenses.id', $id)
            ->first();

        if (!$expense) {
            return redirect()->to('/expenses')->with('error', 'Expense record not found.');
        }

        return view('expenses/view', [
            'title'   => "Expense Details: {$expense['expense_code']}",
            'expense' => $expense,
        ]);
    }

    public function edit($id)
    {
        $expense = $this->expenseModel->find($id);
        if (!$expense) {
            return redirect()->to('/expenses')->with('error', 'Expense record not found.');
        }

        $categories = $this->categoryModel->where('status', 'active')->findAll();
        $properties = $this->propertyModel->where('deleted_at IS NULL')->findAll();
        $projects   = $this->projectModel->findAll();

        return view('expenses/edit', [
            'title'      => "Edit Expense: {$expense['expense_code']}",
            'expense'    => $expense,
            'categories' => $categories,
            'properties' => $properties,
            'projects'   => $projects,
        ]);
    }

    public function update($id)
    {
        $expense = $this->expenseModel->find($id);
        if (!$expense) {
            return redirect()->to('/expenses')->with('error', 'Expense record not found.');
        }

        $rules = [
            'title'          => 'required|min_length[3]|max_length[200]',
            'category_id'    => 'required|numeric',
            'expense_date'   => 'required|valid_date',
            'payee_vendor'   => 'required|max_length[150]',
            'amount'         => 'required|numeric|greater_than[0]',
            'payment_method' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $amount    = (float)$this->request->getPost('amount');
        $taxAmount = (float)($this->request->getPost('tax_amount') ?? 0);
        $total     = $amount + $taxAmount;

        $data = [
            'category_id'      => $this->request->getPost('category_id'),
            'property_id'      => $this->request->getPost('property_id') ?: null,
            'property_unit_id' => $this->request->getPost('property_unit_id') ?: null,
            'project_id'       => $this->request->getPost('project_id') ?: null,
            'title'            => trim($this->request->getPost('title')),
            'expense_date'     => $this->request->getPost('expense_date'),
            'payee_vendor'     => trim($this->request->getPost('payee_vendor')),
            'amount'           => $amount,
            'tax_amount'       => $taxAmount,
            'total_amount'     => $total,
            'payment_method'   => $this->request->getPost('payment_method'),
            'status'           => $this->request->getPost('status') ?? 'Paid',
            'notes'            => trim($this->request->getPost('notes') ?? ''),
        ];

        $this->expenseModel->update($id, $data);

        $this->auditModel->log(
            'EXPENSE_UPDATED',
            "Property Expense {$expense['expense_code']} updated",
            'property_expenses',
            $id
        );

        return redirect()->to('/expenses/view/' . $id)->with('success', 'Expense updated successfully.');
    }

    public function delete($id)
    {
        $expense = $this->expenseModel->find($id);
        if (!$expense) {
            return redirect()->to('/expenses')->with('error', 'Expense record not found.');
        }

        $this->expenseModel->delete($id);

        $this->auditModel->log(
            'EXPENSE_DELETED',
            "Property Expense {$expense['expense_code']} deleted",
            'property_expenses',
            $id
        );

        return redirect()->to('/expenses')->with('success', "Expense {$expense['expense_code']} deleted successfully.");
    }

    public function report()
    {
        $propertyId = $this->request->getGet('property_id');
        $year       = $this->request->getGet('year') ?? date('Y');

        $db = \Config\Database::connect();
        $builder = $db->table('property_expenses')
            ->select('expense_categories.name as category_name, COUNT(property_expenses.id) as count, SUM(property_expenses.amount) as subtotal, SUM(property_expenses.tax_amount) as tax, SUM(property_expenses.total_amount) as total')
            ->join('expense_categories', 'expense_categories.id = property_expenses.category_id', 'left')
            ->where('YEAR(property_expenses.expense_date)', $year)
            ->where('property_expenses.deleted_at IS NULL')
            ->groupBy('property_expenses.category_id');

        if ($propertyId) {
            $builder->where('property_expenses.property_id', $propertyId);
        }

        $categorySummary = $builder->get()->getResultArray();

        // Monthly breakdown
        $monthlyBuilder = $db->table('property_expenses')
            ->select('MONTH(expense_date) as month_num, SUM(total_amount) as total_amount')
            ->where('YEAR(expense_date)', $year)
            ->where('deleted_at IS NULL')
            ->groupBy('MONTH(expense_date)');

        if ($propertyId) {
            $monthlyBuilder->where('property_id', $propertyId);
        }

        $monthlyData = $monthlyBuilder->get()->getResultArray();
        $months = array_fill(1, 12, 0.00);
        foreach ($monthlyData as $m) {
            $months[(int)$m['month_num']] = (float)$m['total_amount'];
        }

        $properties = $this->propertyModel->where('deleted_at IS NULL')->findAll();

        return view('expenses/report', [
            'title'           => "Annual Expense Report - {$year}",
            'categorySummary' => $categorySummary,
            'months'          => $months,
            'year'            => $year,
            'propertyId'      => $propertyId,
            'properties'      => $properties,
        ]);
    }

    public function categories()
    {
        $categories = $this->categoryModel->findAll();

        return view('expenses/categories', [
            'title'      => 'Expense Categories',
            'categories' => $categories,
        ]);
    }

    public function storeCategory()
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
            'code' => 'required|min_length[2]|max_length[50]|is_unique[expense_categories.code]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->categoryModel->insert([
            'name'        => trim($this->request->getPost('name')),
            'code'        => strtoupper(trim($this->request->getPost('code'))),
            'description' => trim($this->request->getPost('description') ?? ''),
            'status'      => 'active',
        ]);

        return redirect()->to('/expenses/categories')->with('success', 'Category created successfully.');
    }
}
