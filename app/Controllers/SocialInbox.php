<?php

namespace App\Controllers;

use App\Models\SocialInbox as SocialInboxModel;
use App\Models\Platform;

class SocialInbox extends BaseController
{
    protected $socialInboxModel;
    protected $platformModel;

    public function __construct()
    {
        $this->socialInboxModel = new SocialInboxModel();
        $this->platformModel = new Platform();
    }

    public function index()
    {
        // Check authentication
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/auth/login')->with('error', 'Please login to access this page');
        }
        
        $data = [
            'title' => 'Social Inbox - ABSYS SMMS',
            'page' => 'social-inbox',
            'css' => ['social_inbox'],
            'platforms' => $this->platformModel->where('is_active', 1)->findAll(),
        ];

        return view('social_inbox', $data);
    }

    public function markAsRead($id)
    {
        $message = $this->socialInboxModel->find($id);
        
        if (!$message) {
            if ($this->request->isAJAX()) return $this->response->setJSON(['status' => 'error', 'message' => 'Message not found']);
            return redirect()->to('/social-inbox')->with('error', 'Message not found');
        }

        $this->socialInboxModel->update($id, ['status' => 'read']);
        
        if ($this->request->isAJAX()) return $this->response->setJSON(['status' => 'success']);
        return redirect()->to('/social-inbox')->with('success', 'Message marked as read');
    }

    public function archive($id)
    {
        $message = $this->socialInboxModel->find($id);
        
        if (!$message) {
            if ($this->request->isAJAX()) return $this->response->setJSON(['status' => 'error', 'message' => 'Message not found']);
            return redirect()->to('/social-inbox')->with('error', 'Message not found');
        }

        $this->socialInboxModel->update($id, ['status' => 'resolved']);
        
        if ($this->request->isAJAX()) return $this->response->setJSON(['status' => 'success', 'message' => 'Message archived/resolved']);
        return redirect()->to('/social-inbox')->with('success', 'Message archived');
    }

    public function delete($id)
    {
        $message = $this->socialInboxModel->find($id);
        
        if (!$message) {
            if ($this->request->isAJAX()) return $this->response->setJSON(['status' => 'error', 'message' => 'Message not found']);
            return redirect()->to('/social-inbox')->with('error', 'Message not found');
        }

        $this->socialInboxModel->delete($id);
        
        if ($this->request->isAJAX()) return $this->response->setJSON(['status' => 'success', 'message' => 'Message deleted']);
        return redirect()->to('/social-inbox')->with('success', 'Message deleted successfully');
    }

    public function reply($id)
    {
        $message = $this->socialInboxModel->find($id);
        if (!$message) {
            if ($this->request->isAJAX()) return $this->response->setJSON(['status' => 'error', 'message' => 'Message not found']);
            return redirect()->to('/social-inbox')->with('error', 'Message not found');
        }

        $replyText = $this->request->getPost('reply_text');
        
        // Append reply to message (simulate threading since we don't have a separate replies table)
        $newMsg = $message['message'] . "\n\n--- [Replied by Manager] ---\n" . $replyText;

        $this->socialInboxModel->update($id, [
            'status' => 'replied',
            'message' => $newMsg,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        if ($this->request->isAJAX()) return $this->response->setJSON(['status' => 'success', 'message' => 'Reply sent']);
        return redirect()->to('/social-inbox')->with('success', 'Reply sent');
    }

    public function getData()
    {
        $db = \Config\Database::connect();
        $messages = $db->table('social_inboxes')
            ->select('social_inboxes.*, platforms.name as platform_name, platforms.icon as platform_icon, platforms.color as platform_color')
            ->join('platforms', 'platforms.id = social_inboxes.platform_id', 'left')
            ->orderBy('social_inboxes.received_at', 'DESC')
            ->get()
            ->getResultArray();
            
        return $this->response->setJSON($messages);
    }
}
