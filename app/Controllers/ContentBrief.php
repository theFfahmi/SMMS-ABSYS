<?php

namespace App\Controllers;

use App\Models\ContentBrief as ContentBriefModel;
use App\Models\Content;
use App\Models\User;

class ContentBrief extends BaseController
{
    protected $contentBriefModel;
    protected $contentModel;
    protected $userModel;

    public function __construct()
    {
        $this->contentBriefModel = new ContentBriefModel();
        $this->contentModel = new Content();
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
            'title' => 'Content Brief - ABSYS SMMS',
            'page' => 'content-brief',
            'css' => ['content_brief'],
            'briefs' => $this->contentBriefModel->limit(50)->findAll(),
            'contents' => $this->contentModel->findAll(),
            'users' => $this->userModel->where('is_active', 1)->findAll(),
        ];

        return view('content_brief', $data);
    }

    public function create($contentId = null)
    {
        $content = null;
        if ($contentId) {
            $content = $this->contentModel->find($contentId);
        }

        $data = [
            'title' => 'Create Content Brief - ABSYS SMMS',
            'page' => 'content-brief',
            'css' => ['content_brief'],
            'content' => $content,
            'contents' => $this->contentModel->findAll(),
            'users' => $this->userModel->where('is_active', 1)->findAll(),
        ];

        return view('content_brief_create', $data);
    }

    public function store()
    {
        $rules = [
            'content_id' => 'required|numeric|is_unique[content_briefs.content_id]',
            'objective' => 'required|min_length[10]',
        ];

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'errors' => $this->validator->getErrors()]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $briefData = [
            'content_id' => $this->request->getPost('content_id'),
            'objective' => $this->request->getPost('objective'),
            'target_audience' => $this->request->getPost('target_audience'),
            'key_message' => $this->request->getPost('key_message'),
            'call_to_action' => $this->request->getPost('call_to_action'),
            'tone' => $this->request->getPost('tone'),
            'reference_url' => $this->request->getPost('reference_url'),
            'notes' => $this->request->getPost('notes'),
            'ai_generated' => $this->request->getPost('ai_generated') ?? false,
            'created_by' => session()->get('user_id') ?? 1,
        ];

        $this->contentBriefModel->insert($briefData);
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Content brief created successfully']);
        }
        return redirect()->to('/content-brief')->with('success', 'Content brief created successfully');
    }

    public function edit($id)
    {
        $brief = $this->contentBriefModel->find($id);
        
        if (!$brief) {
            return redirect()->to('/content-brief')->with('error', 'Content brief not found');
        }

        $data = [
            'title' => 'Edit Content Brief - ABSYS SMMS',
            'page' => 'content-brief',
            'css' => ['content_brief'],
            'brief' => $brief,
            'contents' => $this->contentModel->findAll(),
            'users' => $this->userModel->where('is_active', 1)->findAll(),
        ];

        return view('content_brief_edit', $data);
    }

    public function update($id)
    {
        $brief = $this->contentBriefModel->find($id);
        
        if (!$brief) {
            return redirect()->to('/content-brief')->with('error', 'Content brief not found');
        }

        $rules = [
            'content_id' => "required|numeric|is_unique[content_briefs.content_id,id,{$id}]",
            'objective' => 'required|min_length[10]',
        ];

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'errors' => $this->validator->getErrors()]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $briefData = [
            'content_id' => $this->request->getPost('content_id'),
            'objective' => $this->request->getPost('objective'),
            'target_audience' => $this->request->getPost('target_audience'),
            'key_message' => $this->request->getPost('key_message'),
            'call_to_action' => $this->request->getPost('call_to_action'),
            'tone' => $this->request->getPost('tone'),
            'reference_url' => $this->request->getPost('reference_url'),
            'notes' => $this->request->getPost('notes'),
        ];

        $this->contentBriefModel->update($id, $briefData);
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Content brief updated successfully']);
        }
        return redirect()->to('/content-brief')->with('success', 'Content brief updated successfully');
    }

    public function delete($id)
    {
        $brief = $this->contentBriefModel->find($id);
        
        if (!$brief) {
            return redirect()->to('/content-brief')->with('error', 'Content brief not found');
        }

        $this->contentBriefModel->delete($id);
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Content brief deleted successfully']);
        }
        return redirect()->to('/content-brief')->with('success', 'Content brief deleted successfully');
    }

    public function generateWithAI($contentId)
    {
        // This would integrate with AI service to generate brief
        // For now, redirect to create with AI flag
        return redirect()->to('/content-brief/create/' . $contentId)->with('ai_generate', true);
    }

    public function getBriefs()
    {
        $builder = $this->contentBriefModel->builder();
        $builder->select('content_briefs.*, contents.title as contentTitle');
        $builder->join('contents', 'contents.id = content_briefs.content_id', 'left');
        $builder->orderBy('content_briefs.id', 'DESC');
        
        $briefs = $builder->get()->getResultArray();
        return $this->response->setJSON($briefs);
    }
}
