<?php

namespace App\Controllers;

use App\Models\User;

class UserManagement extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function index()
    {
        // Check authentication
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/auth/login')->with('error', 'Please login to access this page');
        }
        
        $data = [
            'title' => 'User Management - ABSYS SMMS',
            'page' => 'user-management',
            'css' => ['user_management'],
            'users' => $this->userModel->limit(50)->findAll(),
        ];

        return view('user_management', $data);
    }

    // Removed create view method as we use modals

    public function store()
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'full_name' => 'required|min_length[2]|max_length[100]',
            'role' => 'required|in_list[admin,social_media_manager,content_creator,designer,reviewer]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'full_name' => $this->request->getPost('full_name'),
            'role' => $this->request->getPost('role'),
            'is_active' => $this->request->getPost('is_active') ?? 1,
        ];

        $this->userModel->insert($userData);
        return redirect()->to('/user-management')->with('success', 'User created successfully');
    }

    public function getUser($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found']);
        }
        
        // Remove password hash from response
        unset($user['password']);
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $user
        ]);
    }

    public function update($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/user-management')->with('error', 'User not found');
        }

        $rules = [
            'username' => "required|min_length[3]|max_length[50]|is_unique[users.username,id,{$id}]",
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
            'full_name' => 'required|min_length[2]|max_length[100]',
            'role' => 'required|in_list[admin,social_media_manager,content_creator,designer,reviewer]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'full_name' => $this->request->getPost('full_name'),
            'role' => $this->request->getPost('role'),
            'is_active' => $this->request->getPost('is_active') ?? 1,
        ];

        // Update password if provided
        if ($this->request->getPost('password')) {
            $userData['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $userData);
        return redirect()->to('/user-management')->with('success', 'User updated successfully');
    }

    public function delete($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/user-management')->with('error', 'User not found');
        }

        $this->userModel->delete($id);
        return redirect()->to('/user-management')->with('success', 'User deleted successfully');
    }

    public function toggleStatus($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/user-management')->with('error', 'User not found');
        }

        $newStatus = $user['is_active'] ? 0 : 1;
        $this->userModel->update($id, ['is_active' => $newStatus]);
        
        $statusText = $newStatus ? 'activated' : 'deactivated';
        return redirect()->to('/user-management')->with('success', "User {$statusText} successfully");
    }
}
