<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('is_logged_in') || !$session->get('user_id')) {
            return redirect()->to('/login')->with('error', 'Please sign in to access this resource.');
        }

        $userId = (int) $session->get('user_id');
        $userModel = new UserModel();

        // If no permission argument was specified, allow authenticated request
        if (empty($arguments)) {
            return;
        }

        $requiredPermission = $arguments[0];

        if (!$userModel->hasPermission($userId, $requiredPermission)) {
            if ($request->isAJAX()) {
                return service('response')
                    ->setStatusCode(403)
                    ->setJSON([
                        'status'  => 'error',
                        'message' => "Access denied: Missing '{$requiredPermission}' permission.",
                    ]);
            }

            // Render customized 403 forbidden page
            $data = [
                'title'      => '403 - Access Forbidden',
                'permission' => $requiredPermission,
                'message'    => "You do not have the required permission ({$requiredPermission}) to perform this action.",
            ];

            return service('response')
                ->setStatusCode(403)
                ->setBody(view('errors/html/error_403', $data));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after request
    }
}
