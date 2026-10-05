<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\BranchModel;
use App\Models\AuditLogModel;

class AuthController extends BaseController
{
    /**
     * Display login form
     */
    public function login()
    {
        // If already authenticated, redirect straight to dashboard
        if (session()->get('is_logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login', [
            'title' => 'Sign In - Real Estate ERP',
        ]);
    }

    /**
     * Process login authentication attempt
     */
    public function attemptLogin()
    {
        $session = session();

        // Validation rules
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[4]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email    = trim($this->request->getPost('email'));
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        // Check if user exists
        if (!$user) {
            AuditLogModel::record('FAILED_LOGIN', 'Authentication', null, "Failed login attempt for unknown email: {$email}");
            return redirect()->back()->withInput()->with('error', 'Invalid email address or password.');
        }

        // Check user status
        if ($user['status'] !== 'active') {
            AuditLogModel::record('BLOCKED_LOGIN', 'Authentication', $user['id'], "Login attempt on {$user['status']} account: {$email}", $user['id']);
            return redirect()->back()->withInput()->with('error', "Your account is {$user['status']}. Please contact system support.");
        }

        // Verify password hash
        if (!password_verify($password, $user['password'])) {
            AuditLogModel::record('FAILED_PASSWORD', 'Authentication', $user['id'], "Incorrect password attempt for user: {$email}", $user['id']);
            return redirect()->back()->withInput()->with('error', 'Invalid email address or password.');
        }

        // Fetch User Roles & Permissions
        $db = \Config\Database::connect();
        $userRoles = $db->table('user_roles')
                        ->select('roles.id, roles.name')
                        ->join('roles', 'roles.id = user_roles.role_id')
                        ->where('user_roles.user_id', $user['id'])
                        ->get()
                        ->getResultArray();

        $roleNames = array_column($userRoles, 'name');
        $primaryRoleName = !empty($roleNames) ? $roleNames[0] : 'Standard User';
        $isSuperAdmin = in_array('Super Admin', $roleNames, true);

        // Fetch branch info if assigned
        $branchName = 'Corporate Headquarters';
        if (!empty($user['branch_id'])) {
            $branchModel = new BranchModel();
            $branch = $branchModel->find($user['branch_id']);
            if ($branch) {
                $branchName = $branch['name'];
            }
        }

        // Fetch user permission slugs
        $permissions = $userModel->getUserPermissions((int) $user['id']);

        // Update last login timestamp
        $userModel->update($user['id'], [
            'last_login_at' => date('Y-m-d H:i:s'),
        ]);

        // Establish Authenticated Session
        $sessionData = [
            'user_id'        => (int) $user['id'],
            'user_name'      => $user['name'],
            'user_email'     => $user['email'],
            'role_name'      => $primaryRoleName,
            'role_names'     => $roleNames,
            'is_super_admin' => $isSuperAdmin,
            'branch_id'      => $user['branch_id'] ? (int) $user['branch_id'] : null,
            'branch_name'    => $branchName,
            'permissions'    => $permissions,
            'is_logged_in'   => true,
            'logged_at'      => time(),
        ];
        $session->set($sessionData);

        // Record successful login in audit log
        AuditLogModel::record('LOGIN', 'Authentication', (int) $user['id'], "User logged into system successfully", (int) $user['id']);

        // Redirect to intended URL or dashboard
        $redirectUrl = $session->get('redirect_url');
        if ($redirectUrl) {
            $session->remove('redirect_url');
            return redirect()->to($redirectUrl)->with('success', "Welcome back, {$user['name']}!");
        }

        return redirect()->to('/dashboard')->with('success', "Welcome back, {$user['name']}!");
    }

    /**
     * Destroy user session and redirect to login
     */
    public function logout()
    {
        $session = session();
        $userId  = $session->get('user_id');

        if ($userId) {
            AuditLogModel::record('LOGOUT', 'Authentication', (int) $userId, "User initiated sign out", (int) $userId);
        }

        $session->destroy();

        return redirect()->to('/login')->with('success', 'You have been safely signed out.');
    }
}
