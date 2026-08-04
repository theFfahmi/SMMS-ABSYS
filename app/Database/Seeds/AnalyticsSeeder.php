<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnalyticsSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('content_analytics')->truncate();

        // Get actual content IDs and platform IDs
        $contentIds = array_column($this->db->table('contents')->select('id')->get()->getResultArray(), 'id');
        $platformIds = array_column($this->db->table('platforms')->select('id')->get()->getResultArray(), 'id');
        
        if (empty($contentIds) || empty($platformIds)) {
            echo "Cannot seed Analytics: contents or platforms table is empty.\n";
            return;
        }

        $data = [];
        
        // Generate realistic growth over 14 days
        for ($i = 14; $i >= 0; $i--) {
            $date = date('Y-m-d H:i:s', strtotime("-$i days + 10 hours"));
            
            // Randomly insert 2-4 analytic records per day
            $numRecords = rand(2, 4);
            
            for ($j = 0; $j < $numRecords; $j++) {
                $baseReach = rand(1000, 5000);
                // Introduce a slight upward trend as days get closer to 0
                $trendMultiplier = 1 + ((14 - $i) * 0.1); 
                $reach = (int)($baseReach * $trendMultiplier);
                
                $impressions = (int)($reach * (1 + (rand(10, 50) / 100))); // impressions 10-50% higher than reach
                
                $likes = (int)($reach * (rand(3, 8) / 100)); // 3-8% like rate
                $comments = (int)($likes * (rand(5, 15) / 100)); // 5-15% of likes
                $shares = (int)($likes * (rand(2, 10) / 100)); 
                $saves = (int)($likes * (rand(1, 5) / 100));
                
                $clicks = (int)($reach * (rand(1, 4) / 100));
                $followers = rand(1, 25);
                
                $data[] = [
                    'content_id' => $contentIds[array_rand($contentIds)],
                    'platform_id' => $platformIds[array_rand($platformIds)],
                    'reach' => $reach,
                    'impressions' => $impressions,
                    'likes' => $likes,
                    'comments' => $comments,
                    'shares' => $shares,
                    'saves' => $saves,
                    'clicks' => $clicks,
                    'followers_gained' => $followers,
                    'recorded_at' => $date,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
        }

        $this->db->table('content_analytics')->insertBatch($data);
        
        echo "Content Analytics seeded with realistic 14-days dummy data.\n";
    }
}
