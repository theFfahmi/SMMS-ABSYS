<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SampleWorkflowSeeder extends Seeder
{
    public function run()
    {
        // 1. Ensure Master Data exists
        $this->call('MasterDataSeeder');
        $this->call('UserSeeder');

        // Fetch User IDs
        $manager = $this->db->table('users')->where('username', 'manager')->get()->getRow();
        $creator = $this->db->table('users')->where('username', 'creator')->get()->getRow();
        $designer = $this->db->table('users')->where('username', 'designer')->get()->getRow();
        $reviewer = $this->db->table('users')->where('username', 'reviewer')->get()->getRow();

        $managerId = $manager ? $manager->id : 1;
        $creatorId = $creator ? $creator->id : 1;
        $designerId = $designer ? $designer->id : 1;
        $reviewerId = $reviewer ? $reviewer->id : 1;

        // 2. Insert Campaigns
        $campaigns = [
            [
                'name' => 'Promo Kemerdekaan 2026',
                'description' => 'Kampanye promosi khusus menyambut Hari Kemerdekaan RI 2026.',
                'objective' => 'Meningkatkan penjualan 30% dan brand awareness.',
                'target_audience' => 'Generasi Muda & Profesional Muda (18-35 tahun).',
                'start_date' => date('Y-08-01'),
                'end_date' => date('Y-08-31'),
                'budget' => 15000000.00,
                'status' => 'active',
                'created_by' => $managerId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Peluncuran Fitur Baru AI',
                'description' => 'Edukasi dan awareness mengenai fitur otomatisasi konten AI.',
                'objective' => 'Mendapatkan 500 pengguna baru mendaftar trial.',
                'target_audience' => 'Social Media Marketers, Agency, SMB Owners.',
                'start_date' => date('Y-09-01'),
                'end_date' => date('Y-09-15'),
                'budget' => 20000000.00,
                'status' => 'draft',
                'created_by' => $managerId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];

        foreach ($campaigns as $campData) {
            $check = $this->db->table('campaigns')->where('name', $campData['name'])->get()->getRow();
            if (!$check) {
                $this->db->table('campaigns')->insert($campData);
            }
        }

        $campaignObj = $this->db->table('campaigns')->where('name', 'Promo Kemerdekaan 2026')->get()->getRow();
        $campaignId = $campaignObj ? $campaignObj->id : null;

        // 3. Insert Sample Contents in Various Status Stages
        $contents = [
            [
                'title' => 'Teaser Diskon Promo Kemerdekaan',
                'description' => 'Post teaser visual dengan countdown promo diskon 45%.',
                'campaign_id' => $campaignId,
                'content_type_id' => 4, // Promosi
                'content_format_id' => 3, // Carousel
                'content_pillar_id' => 3, // Promotion
                'status_id' => 4, // Waiting Review
                'planned_date' => date('Y-08-10'),
                'assigned_to' => $creatorId,
                'created_by' => $creatorId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => '5 Tips Optimasi Reels untuk Engagement Tinggi',
                'description' => 'Konten carousel edukatif tentang tips membuat video Reels.',
                'campaign_id' => null,
                'content_type_id' => 1, // Edukasi
                'content_format_id' => 1, // Reels
                'content_pillar_id' => 1, // Education
                'status_id' => 6, // Approved
                'planned_date' => date('Y-08-12'),
                'assigned_to' => $creatorId,
                'created_by' => $creatorId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Video Reels Behind The Scenes Produksi',
                'description' => 'Behind the scene tim kreatif menyiapkan materi kampanye.',
                'campaign_id' => $campaignId,
                'content_type_id' => 3, // Hiburan
                'content_format_id' => 1, // Reels
                'content_pillar_id' => 2, // Entertainment
                'status_id' => 7, // Scheduled
                'planned_date' => date('Y-08-15'),
                'assigned_to' => $designerId,
                'created_by' => $creatorId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Infografis Perbandingan Fitur Utama SMMS',
                'description' => 'Post infografis membandingkan workflow manual vs otomatis.',
                'campaign_id' => null,
                'content_type_id' => 2, // Informatif
                'content_format_id' => 2, // Feed
                'content_pillar_id' => 4, // Branding
                'status_id' => 8, // Published
                'planned_date' => date('Y-07-25'),
                'assigned_to' => $designerId,
                'created_by' => $creatorId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Q&A Interaktif: Tanya Jawab Seputar Strategi Konten',
                'description' => 'Story sticker Q&A untuk interaksi dengan audience.',
                'campaign_id' => null,
                'content_type_id' => 5, // Interaktif
                'content_format_id' => 4, // Story
                'content_pillar_id' => 2, // Entertainment
                'status_id' => 5, // Revision
                'planned_date' => date('Y-08-05'),
                'assigned_to' => $creatorId,
                'created_by' => $creatorId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($contents as $cData) {
            $check = $this->db->table('contents')->where('title', $cData['title'])->get()->getRow();
            if (!$check) {
                $this->db->table('contents')->insert($cData);
                $contentId = $this->db->insertID();

                // Attach Instagram platform
                $this->db->table('content_platforms')->insert([
                    'content_id' => $contentId,
                    'platform_id' => 1, // Instagram
                    'platform_content_type' => 'Post',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                // Attach TikTok platform as well for Reels
                if ($cData['content_format_id'] == 1) {
                    $this->db->table('content_platforms')->insert([
                        'content_id' => $contentId,
                        'platform_id' => 2, // TikTok
                        'platform_content_type' => 'Video',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        // 4. Seed Content Approval records
        $waitingContent = $this->db->table('contents')->where('status_id', 4)->get()->getRow();
        if ($waitingContent) {
            $checkApp = $this->db->table('content_approvals')->where('content_id', $waitingContent->id)->get()->getRow();
            if (!$checkApp) {
                $this->db->table('content_approvals')->insert([
                    'content_id' => $waitingContent->id,
                    'reviewer_id' => $managerId,
                    'status' => 'pending',
                    'comment' => 'Draft konten sudah selesai dibuat oleh creator, mohon review manager.',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // 5. Seed Publishing Schedules
        $scheduledContent = $this->db->table('contents')->where('status_id', 7)->get()->getRow();
        if ($scheduledContent) {
            $cp = $this->db->table('content_platforms')->where('content_id', $scheduledContent->id)->get()->getRow();
            if ($cp) {
                $checkSched = $this->db->table('publishing_schedules')->where('content_platform_id', $cp->id)->get()->getRow();
                if (!$checkSched) {
                    $this->db->table('publishing_schedules')->insert([
                        'content_platform_id' => $cp->id,
                        'scheduled_at' => date('Y-08-15 10:00:00'),
                        'status' => 'scheduled',
                        'notes' => 'Jadwal tayang jam 10 pagi waktu prime time.',
                        'created_by' => $managerId,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        // 6. Seed Analytics
        $publishedContent = $this->db->table('contents')->where('status_id', 8)->get()->getRow();
        if ($publishedContent) {
            $checkAnalytic = $this->db->table('content_analytics')->where('content_id', $publishedContent->id)->get()->getRow();
            if (!$checkAnalytic) {
                $this->db->table('content_analytics')->insert([
                    'content_id' => $publishedContent->id,
                    'platform_id' => 1, // Instagram
                    'reach' => 12500,
                    'impressions' => 18400,
                    'likes' => 1240,
                    'comments' => 185,
                    'shares' => 92,
                    'saves' => 310,
                    'clicks' => 450,
                    'followers_gained' => 64,
                    'recorded_at' => date('Y-m-d'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        echo "SampleWorkflowSeeder completed successfully!\n";
    }
}

