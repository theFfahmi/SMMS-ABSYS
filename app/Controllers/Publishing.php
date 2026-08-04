<?php

namespace App\Controllers;

use App\Models\PublishingSchedule;
use App\Models\ContentPlatform;
use App\Models\Content;
use App\Models\Platform;
use App\Models\User;

class Publishing extends BaseController
{
    protected $publishingScheduleModel;
    protected $contentPlatformModel;
    protected $contentModel;
    protected $platformModel;
    protected $userModel;

    public function __construct()
    {
        $this->publishingScheduleModel = new PublishingSchedule();
        $this->contentPlatformModel = new ContentPlatform();
        $this->contentModel = new Content();
        $this->platformModel = new Platform();
        $this->userModel = new User();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/auth/login')->with('error', 'Please login to access this page');
        }

        $data = [
            'title' => 'Publishing Schedule & Execution - ABSYS SMMS',
            'page' => 'publishing',
            'css' => ['publishing'],
            'platforms' => $this->platformModel->where('is_active', 1)->findAll(),
        ];

        return view('publishing', $data);
    }

    public function storeSchedule()
    {
        $rules = [
            'content_id' => 'required|numeric',
            'scheduled_at' => 'required',
        ];

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Pilih konten dan tanggal tayang.']);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal membuat jadwal: pilih konten dan tanggal tayang.');
        }

        $contentId = $this->request->getPost('content_id');
        $scheduledAt = $this->request->getPost('scheduled_at');
        $notes = $this->request->getPost('notes');
        $userId = session()->get('user_id') ?? 1;

        $db = \Config\Database::connect();
        
        $cp = $db->table('content_platforms')->where('content_id', $contentId)->get()->getRow();
        if (!$cp) {
            $db->table('content_platforms')->insert([
                'content_id' => $contentId,
                'platform_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $cpId = $db->insertID();
        } else {
            $cpId = $cp->id;
        }

        $db->table('publishing_schedules')->insert([
            'content_platform_id' => $cpId,
            'scheduled_at' => date('Y-m-d H:i:s', strtotime($scheduledAt)),
            'status' => 'scheduled',
            'notes' => $notes,
            'created_by' => $userId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->contentModel->update($contentId, [
            'status_id' => 7, // Scheduled
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Jadwal tayang baru berhasil disimpan!']);
        }
        return redirect()->to('/publishing')->with('success', 'Jadwal tayang baru berhasil disimpan!');
    }

    public function publishNow($scheduleId)
    {
        $db = \Config\Database::connect();
        $schedule = $db->table('publishing_schedules')->where('id', $scheduleId)->get()->getRow();
        
        if ($schedule) {
            $db->table('publishing_schedules')->where('id', $scheduleId)->update([
                'status' => 'published',
                'published_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $cp = $db->table('content_platforms')->where('id', $schedule->content_platform_id)->get()->getRow();
            if ($cp) {
                $this->contentModel->update($cp->content_id, [
                    'status_id' => 8, // Published
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Konten berhasil dipublikasikan secara live!']);
        }
        return redirect()->to('/publishing')->with('success', 'Konten berhasil dipublikasikan secara live!');
    }

    public function getData()
    {
        $db = \Config\Database::connect();

        $approvedContents = $this->contentModel->getDetailedContents(['status_id' => 6]);
        $allContents = $this->contentModel->getDetailedContents();
        $scheduledContents = $this->contentModel->getDetailedContents(['status_id' => 7]);
        $publishedContents = $this->contentModel->getDetailedContents(['status_id' => 8]);

        $schedules = $db->table('publishing_schedules')
            ->select('publishing_schedules.*, 
                     contents.title as content_title, 
                     contents.status_id,
                     platforms.name as platform_name, 
                     platforms.icon as platform_icon, 
                     platforms.color as platform_color,
                     users.full_name as creator_name')
            ->join('content_platforms', 'content_platforms.id = publishing_schedules.content_platform_id', 'left')
            ->join('contents', 'contents.id = content_platforms.content_id', 'left')
            ->join('platforms', 'platforms.id = content_platforms.platform_id', 'left')
            ->join('users', 'users.id = publishing_schedules.created_by', 'left')
            ->orderBy('publishing_schedules.scheduled_at', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'approved' => $approvedContents,
            'all' => $allContents,
            'scheduled' => $scheduledContents,
            'published' => $publishedContents,
            'schedules' => $schedules
        ]);
    }
}
