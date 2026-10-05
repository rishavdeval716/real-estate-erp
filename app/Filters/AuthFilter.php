<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('is_logged_in') || !$session->get('user_id')) {
            // Store redirect URL for post-login return
            $session->set('redirect_url', current_url());
            return redirect()->to('/login')->with('error', 'Session expired or unauthorized. Please sign in to continue.');
        }

        // Verify user exists and is active
        $userModel = new UserModel();
        $user = $userModel->find($session->get('user_id'));

        if (!$user || $user['status'] !== 'active') {
            $session->destroy();
            return redirect()->to('/login')->with('error', 'Your account is inactive or suspended. Please contact the administrator.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after request
    }
}
