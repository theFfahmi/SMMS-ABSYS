<?php

namespace App\Controllers;

use App\Models\Content;
use App\Models\Platform;
use App\Models\ContentType;
use App\Models\ContentFormat;
use App\Models\ContentPillar;
use App\Models\ContentStatus;

class ContentPlan extends BaseController
{
    protected $contentModel;
    protected $platformModel;
    protected $contentTypeModel;
    protected $contentFormatModel;
    protected $contentPillarModel;
    protected $contentStatusModel;
    protected $campaignModel;
    protected $contentBriefModel;

    public function __construct()
    {
        $this->contentModel = new Content();
        $this->platformModel = new Platform();
        $this->contentTypeModel = new ContentType();
        $this->contentFormatModel = new ContentFormat();
        $this->contentPillarModel = new ContentPillar();
        $this->contentStatusModel = new ContentStatus();
        $this->campaignModel = new \App\Models\Campaign();
        $this->contentBriefModel = new \App\Models\ContentBrief();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/auth/login')->with('error', 'Please login to access this page');
        }

        $filters = [
            'status_id' => $this->request->getGet('status_id'),
            'campaign_id' => $this->request->getGet('campaign_id'),
            'search' => $this->request->getGet('search'),
        ];

        $data = [
            'title' => 'Content Plan & Calendar - ABSYS SMMS',
            'page' => 'content-plan',
            'css' => ['content_plan'],
            'contents' => $this->contentModel->getDetailedContents($filters),
            'platforms' => $this->platformModel->findAll(),
            'contentTypes' => $this->contentTypeModel->findAll(),
            'contentFormats' => $this->contentFormatModel->findAll(),
            'contentPillars' => $this->contentPillarModel->findAll(),
            'contentStatuses' => $this->contentStatusModel->findAll(),
            'campaigns' => $this->campaignModel->findAll(),
            'stats' => $this->getStatsArray(),
        ];

        return view('content_plan', $data);
    }

    public function getContents()
    {
        $month = $this->request->getGet('month');
        $year = $this->request->getGet('year');
        $statusId = $this->request->getGet('status_id');
        $campaignId = $this->request->getGet('campaign_id');

        $filters = [];
        if ($month && $year) {
            $filters['month'] = $month;
            $filters['year'] = $year;
        }
        if ($statusId) {
            $filters['status_id'] = $statusId;
        }
        if ($campaignId) {
            $filters['campaign_id'] = $campaignId;
        }

        $contents = $this->contentModel->getDetailedContents($filters);

        return $this->response->setJSON($contents);
    }

    public function getStats()
    {
        return $this->response->setJSON($this->getStatsArray());
    }

    private function getStatsArray()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('contents')->where('deleted_at', null);

        $total = (clone $builder)->countAllResults();
        $idea = (clone $builder)->where('status_id', 1)->countAllResults();
        $draft = (clone $builder)->where('status_id', 2)->countAllResults();
        $inProd = (clone $builder)->where('status_id', 3)->countAllResults();
        $waitingReview = (clone $builder)->where('status_id', 4)->countAllResults();
        $revision = (clone $builder)->where('status_id', 5)->countAllResults();
        $approved = (clone $builder)->where('status_id', 6)->countAllResults();
        $scheduled = (clone $builder)->where('status_id', 7)->countAllResults();
        $published = (clone $builder)->where('status_id', 8)->countAllResults();

        return [
            'total' => $total,
            'idea' => $idea,
            'draft' => $draft,
            'in_production' => $inProd,
            'waiting_review' => $waitingReview,
            'revision' => $revision,
            'approved' => $approved,
            'scheduled' => $scheduled,
            'published' => $published,
        ];
    }

    public function store()
    {
        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'planned_date' => 'required',
        ];

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'errors' => $this->validator->getErrors()]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $contentData = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'campaign_id' => $this->request->getPost('campaign_id') ?: null,
            'content_type_id' => $this->request->getPost('content_type_id') ?: 1,
            'content_format_id' => $this->request->getPost('content_format_id') ?: 1,
            'content_pillar_id' => $this->request->getPost('content_pillar_id') ?: 1,
            'status_id' => $this->request->getPost('status_id') ?? 2,
            'planned_date' => $this->request->getPost('planned_date'),
            'assigned_to' => $this->request->getPost('assigned_to') ?: null,
            'created_by' => session()->get('user_id') ?? 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->contentModel->insert($contentData);
        $contentId = $this->contentModel->getInsertID();

        // Handle platforms
        $platforms = $this->request->getPost('platforms');
        if (is_array($platforms)) {
            foreach ($platforms as $platformId) {
                $this->contentModel->insertContentPlatform($contentId, $platformId);
            }
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Konten berhasil ditambahkan ke Kalender!']);
        }

        return redirect()->to('/content-plan')->with('success', 'Konten berhasil ditambahkan ke Kalender!');
    }

    public function update($id)
    {
        $content = $this->contentModel->find($id);
        if (!$content) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Konten tidak ditemukan']);
        }

        $contentData = [
            'title' => $this->request->getPost('title') ?: $content['title'],
            'description' => $this->request->getPost('description') ?: $content['description'],
            'campaign_id' => $this->request->getPost('campaign_id') ?: $content['campaign_id'],
            'content_type_id' => $this->request->getPost('content_type_id') ?: $content['content_type_id'],
            'content_format_id' => $this->request->getPost('content_format_id') ?: $content['content_format_id'],
            'planned_date' => $this->request->getPost('planned_date') ?: $content['planned_date'],
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->contentModel->update($id, $contentData);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Konten berhasil diperbarui']);
    }

    public function delete($id)
    {
        $content = $this->contentModel->find($id);
        if ($content) {
            $this->contentModel->delete($id);
        }
        return $this->response->setJSON(['status' => 'success', 'message' => 'Konten berhasil dihapus']);
    }

    // --- MASTER DATA MANAGEMENT ---

    public function storePlatform()
    {
        $name = $this->request->getPost('name');
        if (!$name) {
            return redirect()->to('/content-plan')->with('error', 'Nama platform wajib diisi.');
        }

        $slug = url_title($name, '-', true);
        $icon = $this->request->getPost('icon') ?: 'bi-share';
        $color = $this->request->getPost('color') ?: '#2d6cdf';

        $db = \Config\Database::connect();
        $db->table('platforms')->insert([
            'name' => $name,
            'slug' => $slug,
            'icon' => $icon,
            'color' => $color,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/content-plan')->with('success', "Platform '$name' berhasil ditambahkan!");
    }

    public function storeType()
    {
        $name = $this->request->getPost('name');
        if (!$name) {
            return redirect()->to('/content-plan')->with('error', 'Nama Content Type wajib diisi.');
        }

        $slug = url_title($name, '-', true);
        $description = $this->request->getPost('description');

        $db = \Config\Database::connect();
        $db->table('content_types')->insert([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/content-plan')->with('success', "Content Type '$name' berhasil ditambahkan!");
    }

    public function storePillar()
    {
        $name = $this->request->getPost('name');
        if (!$name) {
            return redirect()->to('/content-plan')->with('error', 'Nama Content Pillar wajib diisi.');
        }

        $slug = url_title($name, '-', true);
        $description = $this->request->getPost('description');

        $db = \Config\Database::connect();
        $db->table('content_pillars')->insert([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/content-plan')->with('success', "Content Pillar '$name' berhasil ditambahkan!");
    }
}