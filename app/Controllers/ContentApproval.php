<?php

namespace App\Controllers;

use App\Models\ContentApproval as ContentApprovalModel;
use App\Models\Content;
use App\Models\User;

class ContentApproval extends BaseController
{
    protected $contentApprovalModel;
    protected $contentModel;
    protected $userModel;

    public function __construct()
    {
        $this->contentApprovalModel = new ContentApprovalModel();
        $this->contentModel = new Content();
        $this->userModel = new User();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/auth/login')->with('error', 'Please login to access this page');
        }

        $data = [
            'title' => 'Review & Approval Workflow - ABSYS SMMS',
            'page' => 'content-approval',
            'css' => ['content_approval']
        ];

        return view('content_approval', $data);
    }

    public function approve($contentId)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Unauthorized. Please login first.',
                    'csrf_token' => csrf_hash()
                ]);
            }
            return redirect()->to('/auth/login')->with('error', 'Please login to access this page');
        }

        $content = $this->contentModel->find($contentId);
        if (!$content) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Content not found',
                    'csrf_token' => csrf_hash()
                ]);
            }
            return redirect()->to('/content-approval')->with('error', 'Content not found');
        }

        // Validasi status asal harus Waiting Review (status_id = 4)
        if ((int)$content['status_id'] !== 4) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Konten tidak dalam status Waiting Review',
                    'csrf_token' => csrf_hash()
                ]);
            }
            return redirect()->to('/content-approval')->with('error', 'Konten tidak dalam status Waiting Review');
        }

        $comment = $this->request->getPost('comment') ?? 'Disetujui oleh Reviewer';
        $fromStatusId = (int)$content['status_id'];

        // Update content status to Approved (status_id = 6)
        $this->contentModel->update($contentId, [
            'status_id' => 6,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $db = \Config\Database::connect();

        // Insert or update approval record
        $existing = $db->table('content_approvals')->where('content_id', $contentId)->get()->getRow();
        if ($existing) {
            $db->table('content_approvals')->where('id', $existing->id)->update([
                'reviewer_id' => $userId,
                'status' => 'approved',
                'comment' => $comment,
                'reviewed_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $db->table('content_approvals')->insert([
                'content_id' => $contentId,
                'reviewer_id' => $userId,
                'status' => 'approved',
                'comment' => $comment,
                'reviewed_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // Record history in content_status_histories
        $db->table('content_status_histories')->insert([
            'content_id'     => $contentId,
            'from_status_id' => $fromStatusId,
            'to_status_id'   => 6,
            'changed_by'     => $userId,
            'note'           => $comment,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        // Insert notification for content creator / assigned user
        $targetUserId = $content['assigned_to'] ?: $content['created_by'];
        if ($targetUserId) {
            $db->table('notifications')->insert([
                'user_id'        => $targetUserId,
                'type'           => 'content_approved',
                'title'          => 'Konten Disetujui',
                'message'        => 'Konten "' . $content['title'] . '" telah disetujui oleh Reviewer.',
                'reference_type' => 'content',
                'reference_id'   => $contentId,
                'is_read'        => 0,
                'created_at'     => date('Y-m-d H:i:s'),
            ]);
        }

        // Insert activity log
        $db->table('activity_logs')->insert([
            'user_id'        => $userId,
            'action'         => 'approved',
            'module'         => 'content_approval',
            'reference_type' => 'content',
            'reference_id'   => $contentId,
            'description'    => 'Reviewer menyetujui konten: ' . $content['title'],
            'ip_address'     => $this->request->getIPAddress(),
            'user_agent'     => (string)$this->request->getUserAgent(),
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Konten telah disetujui!',
                'csrf_token' => csrf_hash()
            ]);
        }
        return redirect()->to('/content-approval')->with('success', 'Konten berhasil disetujui!');
    }

    public function requestRevision($contentId)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Unauthorized. Please login first.',
                    'csrf_token' => csrf_hash()
                ]);
            }
            return redirect()->to('/auth/login')->with('error', 'Please login to access this page');
        }

        $content = $this->contentModel->find($contentId);
        if (!$content) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Content not found',
                    'csrf_token' => csrf_hash()
                ]);
            }
            return redirect()->to('/content-approval')->with('error', 'Content not found');
        }

        // Validasi status asal harus Waiting Review (status_id = 4)
        if ((int)$content['status_id'] !== 4) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Konten tidak dalam status Waiting Review',
                    'csrf_token' => csrf_hash()
                ]);
            }
            return redirect()->to('/content-approval')->with('error', 'Konten tidak dalam status Waiting Review');
        }

        // Validasi wajib: catatan/komentar revisi tidak boleh kosong
        $comment = trim($this->request->getPost('comment') ?? '');
        if (empty($comment)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Catatan / alasan revisi wajib diisi.',
                    'csrf_token' => csrf_hash()
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Catatan / alasan revisi wajib diisi.');
        }

        $fromStatusId = (int)$content['status_id'];

        // Update content status to Revision (status_id = 5)
        $this->contentModel->update($contentId, [
            'status_id' => 5,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $db = \Config\Database::connect();

        // Insert/update approval record
        $existing = $db->table('content_approvals')->where('content_id', $contentId)->get()->getRow();
        if ($existing) {
            $db->table('content_approvals')->where('id', $existing->id)->update([
                'reviewer_id' => $userId,
                'status' => 'revision',
                'comment' => $comment,
                'reviewed_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $db->table('content_approvals')->insert([
                'content_id' => $contentId,
                'reviewer_id' => $userId,
                'status' => 'revision',
                'comment' => $comment,
                'reviewed_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // Record in content_revisions table
        $db->table('content_revisions')->insert([
            'content_id' => $contentId,
            'requested_by' => $userId,
            'comment' => $comment,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Record history in content_status_histories
        $db->table('content_status_histories')->insert([
            'content_id'     => $contentId,
            'from_status_id' => $fromStatusId,
            'to_status_id'   => 5,
            'changed_by'     => $userId,
            'note'           => $comment,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        // Insert notification for content creator / assigned user
        $targetUserId = $content['assigned_to'] ?: $content['created_by'];
        if ($targetUserId) {
            $db->table('notifications')->insert([
                'user_id'        => $targetUserId,
                'type'           => 'revision_requested',
                'title'          => 'Permintaan Revisi Konten',
                'message'        => 'Konten "' . $content['title'] . '" memerlukan revisi: ' . $comment,
                'reference_type' => 'content',
                'reference_id'   => $contentId,
                'is_read'        => 0,
                'created_at'     => date('Y-m-d H:i:s'),
            ]);
        }

        // Insert activity log
        $db->table('activity_logs')->insert([
            'user_id'        => $userId,
            'action'         => 'requested_revision',
            'module'         => 'content_approval',
            'reference_type' => 'content',
            'reference_id'   => $contentId,
            'description'    => 'Reviewer meminta revisi konten: ' . $content['title'],
            'ip_address'     => $this->request->getIPAddress(),
            'user_agent'     => (string)$this->request->getUserAgent(),
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Permintaan revisi berhasil dikirim',
                'csrf_token' => csrf_hash()
            ]);
        }
        return redirect()->to('/content-approval')->with('success', 'Catatan revisi berhasil dikirim ke creator!');
    }

    public function getData()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();
        $waitingContents = $this->contentModel->getDetailedContents(['status_id' => 4]);
        
        $approvals = $db->table('content_approvals')
            ->select('content_approvals.*, contents.title as content_title, reviewer.full_name as reviewer_name')
            ->join('contents', 'contents.id = content_approvals.content_id', 'left')
            ->join('users as reviewer', 'reviewer.id = content_approvals.reviewer_id', 'left')
            ->orderBy('content_approvals.id', 'DESC')
            ->get()
            ->getResultArray();
            
        return $this->response->setJSON([
            'waiting' => $waitingContents,
            'history' => $approvals
        ]);
    }
}
