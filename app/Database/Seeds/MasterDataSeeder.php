<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Content Statuses
        $statuses = [
            ['name' => 'Idea', 'slug' => 'idea', 'color' => '#6b7280', 'sort_order' => 1, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Draft', 'slug' => 'draft', 'color' => '#f59e0b', 'sort_order' => 2, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'In Production', 'slug' => 'in-production', 'color' => '#3b82f6', 'sort_order' => 3, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Waiting Review', 'slug' => 'waiting-review', 'color' => '#8b5cf6', 'sort_order' => 4, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Revision', 'slug' => 'revision', 'color' => '#ef4444', 'sort_order' => 5, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Approved', 'slug' => 'approved', 'color' => '#1d4ed8', 'sort_order' => 6, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Scheduled', 'slug' => 'scheduled', 'color' => '#14b8a6', 'sort_order' => 7, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Published', 'slug' => 'published', 'color' => '#10b981', 'sort_order' => 8, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('content_statuses')->ignore(true)->insertBatch($statuses);

        // 2. Platforms
        $platforms = [
            ['name' => 'Instagram', 'slug' => 'instagram', 'icon' => 'bi-instagram', 'color' => '#e1306c', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'TikTok', 'slug' => 'tiktok', 'icon' => 'bi-tiktok', 'color' => '#000000', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Facebook', 'slug' => 'facebook', 'icon' => 'bi-facebook', 'color' => '#1877f2', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'YouTube', 'slug' => 'youtube', 'icon' => 'bi-youtube', 'color' => '#ff0000', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'X', 'slug' => 'x', 'icon' => 'bi-twitter-x', 'color' => '#000000', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'LinkedIn', 'slug' => 'linkedin', 'icon' => 'bi-linkedin', 'color' => '#0a66c2', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('platforms')->ignore(true)->insertBatch($platforms);

        // 3. Content Types
        $types = [
            ['name' => 'Edukasi', 'slug' => 'edukasi', 'description' => 'Konten edukatif', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Informatif', 'slug' => 'informatif', 'description' => 'Info dan berita', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Hiburan', 'slug' => 'hiburan', 'description' => 'Hiburan ringan', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Promosi', 'slug' => 'promosi', 'description' => 'Promosi jualan', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Interaktif', 'slug' => 'interaktif', 'description' => 'Q&A, Kuis, dll', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('content_types')->ignore(true)->insertBatch($types);

        // 4. Content Formats
        $formats = [
            ['name' => 'Reels', 'slug' => 'reels', 'description' => 'Video pendek vertikal', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Feed', 'slug' => 'feed', 'description' => 'Post image single', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Carousel', 'slug' => 'carousel', 'description' => 'Post multiple images', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Story', 'slug' => 'story', 'description' => '24 hours post', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Video', 'slug' => 'video', 'description' => 'Long form video', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('content_formats')->ignore(true)->insertBatch($formats);

        // 5. Content Pillars
        $pillars = [
            ['name' => 'Education', 'slug' => 'education', 'description' => 'Edukasi audience', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Entertainment', 'slug' => 'entertainment', 'description' => 'Hiburan ringan', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Promotion', 'slug' => 'promotion', 'description' => 'Jualan produk', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Branding', 'slug' => 'branding', 'description' => 'Company image', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('content_pillars')->ignore(true)->insertBatch($pillars);

        echo "Master data seeded successfully!\n";
    }
}
