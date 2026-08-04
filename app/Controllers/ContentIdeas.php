<?php

namespace App\Controllers;

use App\Models\ContentIdea;
use App\Models\ContentPillar;
use App\Models\ContentType;
use App\Models\ContentFormat;
use App\Models\User;

class ContentIdeas extends BaseController
{
    protected $contentIdeaModel;
    protected $contentPillarModel;
    protected $contentTypeModel;
    protected $contentFormatModel;
    protected $userModel;

    public function __construct()
    {
        $this->contentIdeaModel = new ContentIdea();
        $this->contentPillarModel = new ContentPillar();
        $this->contentTypeModel = new ContentType();
        $this->contentFormatModel = new ContentFormat();
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
            'title' => 'Content Ideas - ABSYS SMMS',
            'page' => 'content-ideas',
            'css' => ['content_ideas'],
            'ideas' => $this->contentIdeaModel->limit(50)->findAll(),
            'contentPillars' => $this->contentPillarModel->where('is_active', 1)->findAll(),
            'contentTypes' => $this->contentTypeModel->where('is_active', 1)->findAll(),
            'contentFormats' => $this->contentFormatModel->where('is_active', 1)->findAll(),
            'users' => $this->userModel->where('is_active', 1)->findAll(),
        ];

        return view('content_ideas', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Content Idea - ABSYS SMMS',
            'page' => 'content-ideas',
            'css' => ['content_ideas'],
            'contentPillars' => $this->contentPillarModel->where('is_active', 1)->findAll(),
            'contentTypes' => $this->contentTypeModel->where('is_active', 1)->findAll(),
            'contentFormats' => $this->contentFormatModel->where('is_active', 1)->findAll(),
            'users' => $this->userModel->where('is_active', 1)->findAll(),
        ];

        return view('content_ideas_create', $data);
    }

    public function store()
    {
        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $ideaData = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'content_pillar_id' => $this->request->getPost('content_pillar_id'),
            'content_type_id' => $this->request->getPost('content_type_id'),
            'content_format_id' => $this->request->getPost('content_format_id'),
            'priority' => $this->request->getPost('priority') ?? 'medium',
            'status' => $this->request->getPost('status') ?? 'new',
            'assigned_to' => $this->request->getPost('assigned_to'),
            'created_by' => session()->get('user_id') ?? 1,
            'ai_generated' => $this->request->getPost('ai_generated') ?? false,
        ];

        $this->contentIdeaModel->insert($ideaData);
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Content idea created successfully']);
        }
        return redirect()->to('/content-ideas')->with('success', 'Content idea created successfully');
    }

    public function edit($id)
    {
        $idea = $this->contentIdeaModel->find($id);
        
        if (!$idea) {
            return redirect()->to('/content-ideas')->with('error', 'Content idea not found');
        }

        $data = [
            'title' => 'Edit Content Idea - ABSYS SMMS',
            'page' => 'content-ideas',
            'css' => ['content_ideas'],
            'idea' => $idea,
            'contentPillars' => $this->contentPillarModel->where('is_active', 1)->findAll(),
            'contentTypes' => $this->contentTypeModel->where('is_active', 1)->findAll(),
            'contentFormats' => $this->contentFormatModel->where('is_active', 1)->findAll(),
            'users' => $this->userModel->where('is_active', 1)->findAll(),
        ];

        return view('content_ideas_edit', $data);
    }

    public function update($id)
    {
        $idea = $this->contentIdeaModel->find($id);
        
        if (!$idea) {
            return redirect()->to('/content-ideas')->with('error', 'Content idea not found');
        }

        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $ideaData = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'content_pillar_id' => $this->request->getPost('content_pillar_id'),
            'content_type_id' => $this->request->getPost('content_type_id'),
            'content_format_id' => $this->request->getPost('content_format_id'),
            'priority' => $this->request->getPost('priority'),
            'status' => $this->request->getPost('status'),
            'assigned_to' => $this->request->getPost('assigned_to'),
        ];

        $this->contentIdeaModel->update($id, $ideaData);
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Content idea updated successfully']);
        }
        return redirect()->to('/content-ideas')->with('success', 'Content idea updated successfully');
    }

    public function delete($id)
    {
        $idea = $this->contentIdeaModel->find($id);
        
        if (!$idea) {
            return redirect()->to('/content-ideas')->with('error', 'Content idea not found');
        }

        $this->contentIdeaModel->delete($id);
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Content idea deleted successfully']);
        }
        return redirect()->to('/content-ideas')->with('success', 'Content idea deleted successfully');
    }

    public function convertToContent($id)
    {
        $idea = $this->contentIdeaModel->find($id);
        
        if (!$idea) {
            return redirect()->to('/content-ideas')->with('error', 'Content idea not found');
        }

        $plannedDate = $this->request->getPost('planned_date');
        $statusSlug = $this->request->getPost('status') ?? 'draft';

        // Get status ID
        $contentStatusModel = new \App\Models\ContentStatus();
        $status = $contentStatusModel->where('slug', $statusSlug)->first();
        if (!$status) {
            $status = $contentStatusModel->where('slug', 'draft')->first();
        }

        // Create new content
        $contentModel = new \App\Models\Content();
        $contentData = [
            'content_pillar_id' => $idea['content_pillar_id'],
            'content_type_id' => $idea['content_type_id'],
            'content_format_id' => $idea['content_format_id'],
            'status_id' => $status['id'] ?? 1, // 1 is Draft
            'title' => $idea['title'],
            'description' => $idea['description'],
            'planned_date' => $plannedDate,
            'assigned_to' => $idea['assigned_to'],
            'created_by' => session()->get('user_id'),
            'ai_assisted' => $idea['ai_generated'] ?? 0,
        ];

        $contentId = $contentModel->insert($contentData);

        if ($contentId) {
            // Update idea status
            $this->contentIdeaModel->update($id, [
                'status' => 'converted'
            ]);
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Content idea successfully converted to Content Plan']);
            }
            return redirect()->to('/content-ideas')->with('success', 'Content idea successfully converted to Content Plan');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to convert content idea']);
        }
        return redirect()->to('/content-ideas')->with('error', 'Failed to convert content idea');
    }

    public function getIdeas()
    {
        $builder = $this->contentIdeaModel->builder();
        $builder->select('content_ideas.*, content_pillars.name as pillar_name, content_types.name as type_name, content_formats.name as format_name, users.username as assign_name');
        $builder->join('content_pillars', 'content_pillars.id = content_ideas.content_pillar_id', 'left');
        $builder->join('content_types', 'content_types.id = content_ideas.content_type_id', 'left');
        $builder->join('content_formats', 'content_formats.id = content_ideas.content_format_id', 'left');
        $builder->join('users', 'users.id = content_ideas.assigned_to', 'left');
        $builder->where('content_ideas.deleted_at IS NULL');
        $builder->orderBy('content_ideas.id', 'DESC');
        
        $ideas = $builder->get()->getResultArray();
        return $this->response->setJSON($ideas);
    }
}
