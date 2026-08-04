<?php

namespace App\Models;

use CodeIgniter\Model;

class Content extends Model
{
    protected $table            = 'contents';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['campaign_id', 'content_pillar_id', 'content_type_id', 'content_format_id', 'status_id', 'title', 'description', 'planned_date', 'assigned_to', 'created_by', 'ai_assisted', 'created_at', 'updated_at', 'deleted_at'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getDetailedContents(array $filters = [])
    {
        $builder = $this->db->table('contents')
            ->select('contents.*, 
                     content_statuses.name as status_name, content_statuses.slug as status_slug, content_statuses.color as status_color,
                     content_types.name as type_name,
                     content_formats.name as format_name,
                     content_pillars.name as pillar_name,
                     campaigns.name as campaign_name,
                     assignee.full_name as assigned_user_name,
                     creator.full_name as created_user_name')
            ->join('content_statuses', 'content_statuses.id = contents.status_id', 'left')
            ->join('content_types', 'content_types.id = contents.content_type_id', 'left')
            ->join('content_formats', 'content_formats.id = contents.content_format_id', 'left')
            ->join('content_pillars', 'content_pillars.id = contents.content_pillar_id', 'left')
            ->join('campaigns', 'campaigns.id = contents.campaign_id', 'left')
            ->join('users as assignee', 'assignee.id = contents.assigned_to', 'left')
            ->join('users as creator', 'creator.id = contents.created_by', 'left');

        if (!empty($filters['status_id'])) {
            $builder->where('contents.status_id', $filters['status_id']);
        }
        if (!empty($filters['campaign_id'])) {
            $builder->where('contents.campaign_id', $filters['campaign_id']);
        }
        if (!empty($filters['search'])) {
            $builder->like('contents.title', $filters['search']);
        }

        $builder->orderBy('contents.planned_date', 'DESC')
                ->orderBy('contents.id', 'DESC');

        $contents = $builder->get()->getResultArray();

        foreach ($contents as &$c) {
            $c['platforms'] = $this->getPlatformsByContent($c['id']);
        }

        return $contents;
    }

    public function insertContentPlatform($contentId, $platformId)
    {
        $this->db->table('content_platforms')->insert([
            'content_id' => $contentId,
            'platform_id' => $platformId,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function updateContentPlatforms($contentId, array $platformIds)
    {
        $this->db->table('content_platforms')->where('content_id', $contentId)->delete();
        foreach ($platformIds as $platformId) {
            $this->insertContentPlatform($contentId, $platformId);
        }
    }

    public function getPlatformsByContent($contentId)
    {
        return $this->db->table('content_platforms')
                  ->select('platforms.*')
                  ->join('platforms', 'platforms.id = content_platforms.platform_id')
                  ->where('content_platforms.content_id', $contentId)
                  ->get()
                  ->getResultArray();
    }
}
