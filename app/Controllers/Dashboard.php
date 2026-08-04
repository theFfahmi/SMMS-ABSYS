<?php
namespace App\Controllers;

use App\Models\User;

class Dashboard extends BaseController
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
        
        // Check if user account is active
        $user = $this->userModel->find($userId);
        if (!$user || $user['is_active'] != 1) {
            session()->destroy();
            return redirect()->to('/auth/login')->with('error', 'Your account is inactive or has been deleted');
        }
        
        // Get statistics from database (placeholder for now)
        $data = [
            'title' => 'Dashboard - ABSYS SMMS',
            'page' => 'dashboard',
            'css' => ['dashboard'],
            'stats' => [
                'total_content' => 0,
                'content_ideas' => 0,
                'draft' => 0,
                'in_production' => 0,
                'waiting_review' => 0,
                'revision' => 0,
                'approved' => 0,
                'scheduled' => 0,
                'published' => 0,
            ],
        ];
        
        // Debug: Check if data is passed correctly
        log_message('debug', 'Dashboard data: ' . json_encode($data));
        
        return view('dashboard', $data);
    }
}
