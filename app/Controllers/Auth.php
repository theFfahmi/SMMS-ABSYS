<?php

namespace App\Controllers;

use App\Models\User;

class Auth extends BaseController
{
    // Disable authentication for auth controller
    protected $requireAuth = false;
    
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function login()
    {
        // If already logged in, redirect to dashboard
        if (session()->get('user_id')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function authenticate()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember');

        $user = $this->userModel->where('username', $username)->orWhere('email', $username)->first();

        if ($user) {
            if (password_verify($password, $user['password'])) {
                if (!$user['is_active']) {
                    return redirect()->back()->with('error', 'Account is inactive. Please contact administrator.');
                }

                // Set session data
                $sessionData = [
                    'user_id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role'],
                    'logged_in' => true,
                ];

                session()->set($sessionData);

                // Update last login
                $this->userModel->update($user['id'], ['updated_at' => date('Y-m-d H:i:s')]);

                return redirect()->to('/dashboard')->with('success', 'Welcome back, ' . $user['full_name']);
            }
        }

        return redirect()->back()->with('error', 'Invalid username or password');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login')->with('success', 'You have been logged out successfully');
    }

    public function register()
    {
        // If already logged in, redirect to dashboard
        if (session()->get('user_id')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/register');
    }

    public function store()
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[password]',
            'full_name' => 'required|min_length[2]|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'full_name' => $this->request->getPost('full_name'),
            'role' => 'viewer', // Default role for new registrations
            'is_active' => 1,
        ];

        $this->userModel->insert($userData);
        
        return redirect()->to('/login')->with('success', 'Registration successful. Please login with your credentials.');
    }

    public function forgotPassword()
    {
        return view('auth/forgot_password');
    }

    public function sendResetLink()
    {
        $email = $this->request->getPost('email');
        $user = $this->userModel->where('email', $email)->first();

        if ($user) {
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store token in session or database (simplified here)
            session()->set('reset_token', [
                'token' => $token,
                'user_id' => $user['id'],
                'expiry' => $expiry,
            ]);

            // In production, send email with reset link
            // For now, return success message
            return redirect()->back()->with('success', 'Password reset link has been sent to your email.');
        }

        return redirect()->back()->with('error', 'Email not found');
    }

    public function resetPassword($token = null)
    {
        if (!$token) {
            return redirect()->to('/login')->with('error', 'Invalid reset token');
        }

        $resetToken = session()->get('reset_token');
        
        if (!$resetToken || $resetToken['token'] !== $token || strtotime($resetToken['expiry']) < time()) {
            return redirect()->to('/login')->with('error', 'Invalid or expired reset token');
        }

        return view('auth/reset_password', ['token' => $token]);
    }

    public function updatePassword()
    {
        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('confirm_password');

        $rules = [
            'password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $resetToken = session()->get('reset_token');
        
        if (!$resetToken || $resetToken['token'] !== $token || strtotime($resetToken['expiry']) < time()) {
            return redirect()->to('/login')->with('error', 'Invalid or expired reset token');
        }

        // Update password
        $this->userModel->update($resetToken['user_id'], [
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        // Clear reset token
        session()->remove('reset_token');

        return redirect()->to('/login')->with('success', 'Password updated successfully. Please login with your new password.');
    }
}
