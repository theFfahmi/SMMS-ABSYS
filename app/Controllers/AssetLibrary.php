<?php

namespace App\Controllers;

use App\Models\Asset;
use App\Models\User;

class AssetLibrary extends BaseController
{
    protected $assetModel;
    protected $userModel;

    public function __construct()
    {
        $this->assetModel = new Asset();
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
            'title' => 'Asset Library - ABSYS SMMS',
            'page' => 'asset-library',
            'css' => ['asset_library'],
            'assets' => $this->assetModel->limit(50)->findAll(),
            'users' => $this->userModel->where('is_active', 1)->findAll(),
        ];

        return view('asset_library', $data);
    }

    public function upload()
    {
        $validationRule = [
            'asset' => [
                'label' => 'Asset File',
                'rules' => 'uploaded[asset]|ext_in[asset,jpg,jpeg,gif,png,webp,svg,mp4,webm,pdf,doc,docx,xls,xlsx,txt]|max_size[asset,51200]',
            ],
        ];

        if (! $this->validate($validationRule)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => $this->validator->getErrors()['asset'] ?? 'Invalid file format.']);
            }
            return redirect()->back()->with('error', $this->validator->getErrors()['asset'] ?? 'Invalid file format.');
        }

        $file = $this->request->getFile('asset');
        
        if (!$file || !$file->isValid()) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid file upload']);
            }
            return redirect()->back()->with('error', 'Invalid file upload');
        }

        $fileName = $file->getRandomName();
        $file->move(FCPATH . 'uploads/assets', $fileName);

        $fileType = $file->getClientMimeType();
        $mimeCategory = explode('/', $fileType)[0];
        
        $type = 'other';
        if ($mimeCategory === 'image') $type = 'image';
        elseif ($mimeCategory === 'video') $type = 'video';
        elseif (in_array($file->getClientExtension(), ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'])) $type = 'document';

        $assetData = [
            'name' => $this->request->getPost('name') ?? $file->getClientName(),
            'file_name' => $fileName,
            'file_path' => 'uploads/assets/' . $fileName,
            'file_type' => $type,
            'mime_type' => $fileType,
            'file_size' => $file->getSize(),
            'uploaded_by' => session()->get('user_id') ?? 1,
        ];

        $this->assetModel->insert($assetData);
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Asset uploaded successfully']);
        }
        return redirect()->to('/asset-library')->with('success', 'Asset uploaded successfully');
    }

    public function delete($id)
    {
        $asset = $this->assetModel->find($id);
        
        if (!$asset) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Asset not found']);
            }
            return redirect()->to('/asset-library')->with('error', 'Asset not found');
        }

        // Delete physical file
        $filePath = FCPATH . $asset['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $this->assetModel->delete($id);
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Asset deleted successfully']);
        }
        return redirect()->to('/asset-library')->with('success', 'Asset deleted successfully');
    }

    public function getAssets()
    {
        $assets = $this->assetModel->orderBy('id', 'DESC')->findAll();
        return $this->response->setJSON($assets);
    }
}
