<?php

namespace App\Controllers;

use App\Models\Campaign;
use App\Models\Content;
use App\Models\User;

class Campaigns extends BaseController
{
    protected $campaignModel;
    protected $contentModel;
    protected $userModel;

    public function __construct()
    {
        $this->campaignModel = new Campaign();
        $this->contentModel = new Content();
        $this->userModel = new User();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/auth/login')->with('error', 'Please login to access this page');
        }

        $campaigns = $this->campaignModel->orderBy('created_at', 'DESC')->findAll();

        foreach ($campaigns as &$camp) {
            $camp['content_count'] = $this->contentModel->where('campaign_id', $camp['id'])->countAllResults();
            $camp['creator'] = $this->userModel->find($camp['created_by']);
        }

        $data = [
            'title' => 'Campaign Management - ABSYS SMMS',
            'page' => 'campaigns',
            'css' => ['campaigns'],
            'campaigns' => $campaigns,
            'users' => $this->userModel->where('is_active', 1)->findAll(),
        ];

        return view('campaigns', $data);
    }

    public function detail($id)
    {
        $campaign = $this->campaignModel->find($id);
        if (!$campaign) {
            return redirect()->to('/campaigns')->with('error', 'Campaign not found');
        }

        $campaign['contents'] = $this->contentModel->getDetailedContents(['campaign_id' => $id]);

        $data = [
            'title' => 'Campaign Detail - ' . $campaign['name'],
            'page' => 'campaigns',
            'css' => ['campaigns'],
            'campaign' => $campaign,
        ];

        return view('campaigns_detail', $data);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'start_date' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan: mohon lengkapi nama dan tanggal mulai campaign.');
        }

        $endDate = $this->request->getPost('end_date');
        $budget = $this->request->getPost('budget');

        $campaignData = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'objective' => $this->request->getPost('objective'),
            'target_audience' => $this->request->getPost('target_audience'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => !empty($endDate) ? $endDate : null,
            'budget' => !empty($budget) ? (float)$budget : null,
            'status' => $this->request->getPost('status') ?? 'active',
            'created_by' => session()->get('user_id') ?? 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->campaignModel->insert($campaignData);
        return redirect()->to('/campaigns')->with('success', 'Campaign baru berhasil dibuat!');
    }

    public function update($id)
    {
        $campaign = $this->campaignModel->find($id);
        if (!$campaign) {
            return redirect()->to('/campaigns')->with('error', 'Campaign tidak ditemukan.');
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'start_date' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui campaign.');
        }

        $endDate = $this->request->getPost('end_date');
        $budget = $this->request->getPost('budget');

        $campaignData = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'objective' => $this->request->getPost('objective'),
            'target_audience' => $this->request->getPost('target_audience'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => !empty($endDate) ? $endDate : null,
            'budget' => !empty($budget) ? (float)$budget : null,
            'status' => $this->request->getPost('status'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->campaignModel->update($id, $campaignData);
        return redirect()->to('/campaigns')->with('success', 'Campaign berhasil diperbarui!');
    }

    public function delete($id)
    {
        $campaign = $this->campaignModel->find($id);
        if (!$campaign) {
            return redirect()->to('/campaigns')->with('error', 'Campaign tidak ditemukan.');
        }

        $this->campaignModel->delete($id);
        return redirect()->to('/campaigns')->with('success', 'Campaign berhasil dihapus.');
    }
}
