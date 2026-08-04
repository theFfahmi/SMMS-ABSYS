<?php

namespace App\Controllers;

use App\Models\ContentAnalytics;
use App\Models\Platform;

class Analytics extends BaseController
{
    public function index()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/auth/login')->with('error', 'Please login to access this page');
        }

        $data = [
            'title' => 'Analytics & Performance - ABSYS SMMS',
            'page' => 'analytics',
            'css' => ['analytics'],
        ];

        return view('analytics', $data);
    }

    public function getData()
    {
        $db = \Config\Database::connect();

        // Calculate Totals from content_analytics table
        $query = $db->table('content_analytics')
            ->select('SUM(reach) as total_reach, 
                     SUM(impressions) as total_impressions, 
                     SUM(likes) as total_likes, 
                     SUM(comments) as total_comments, 
                     SUM(shares) as total_shares, 
                     SUM(saves) as total_saves, 
                     SUM(clicks) as total_clicks,
                     SUM(followers_gained) as total_followers')
            ->get()
            ->getRowArray();

        $reach = (int) ($query['total_reach'] ?? 0);
        $impressions = (int) ($query['total_impressions'] ?? 0);
        $likes = (int) ($query['total_likes'] ?? 0);
        $comments = (int) ($query['total_comments'] ?? 0);
        $shares = (int) ($query['total_shares'] ?? 0);
        $saves = (int) ($query['total_saves'] ?? 0);
        $clicks = (int) ($query['total_clicks'] ?? 0);
        $followers = (int) ($query['total_followers'] ?? 0);

        $totalEngagement = $likes + $comments + $shares + $saves;
        $engagementRate = $reach > 0 ? round(($totalEngagement / $reach) * 100, 2) : 0;

        // Content performance list
        $performanceList = $db->table('content_analytics')
            ->select('content_analytics.*, contents.title as content_title, platforms.name as platform_name, platforms.icon as platform_icon, platforms.color as platform_color')
            ->join('contents', 'contents.id = content_analytics.content_id', 'left')
            ->join('platforms', 'platforms.id = content_analytics.platform_id', 'left')
            ->orderBy('content_analytics.recorded_at', 'DESC')
            ->get()
            ->getResultArray();

        foreach ($performanceList as &$item) {
            $itemEngagement = $item['likes'] + $item['comments'] + $item['shares'] + $item['saves'];
            $item['total_engagement'] = $itemEngagement;
            $item['calc_engagement_rate'] = $item['reach'] > 0 ? round(($itemEngagement / $item['reach']) * 100, 2) : 0;
        }

        // Generate Chart Data (Last 14 days)
        $chartData = [
            'labels' => [],
            'reach' => [],
            'engagement' => []
        ];
        
        $dateStart = date('Y-m-d', strtotime('-13 days'));
        
        $chartQuery = $db->table('content_analytics')
            ->select('DATE(recorded_at) as date, SUM(reach) as daily_reach, SUM(likes + comments + shares + saves) as daily_engagement')
            ->where('recorded_at >=', $dateStart . ' 00:00:00')
            ->groupBy('DATE(recorded_at)')
            ->orderBy('DATE(recorded_at)', 'ASC')
            ->get()
            ->getResultArray();
            
        $chartMap = [];
        foreach($chartQuery as $row) {
            $chartMap[$row['date']] = $row;
        }
        
        for ($i = 13; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $chartData['labels'][] = date('d M', strtotime($date));
            if (isset($chartMap[$date])) {
                $chartData['reach'][] = (int) $chartMap[$date]['daily_reach'];
                $chartData['engagement'][] = (int) $chartMap[$date]['daily_engagement'];
            } else {
                $chartData['reach'][] = 0;
                $chartData['engagement'][] = 0;
            }
        }
        return $this->response->setJSON([
            'metrics' => [
                'reach' => $reach,
                'impressions' => $impressions,
                'likes' => $likes,
                'comments' => $comments,
                'shares' => $shares,
                'saves' => $saves,
                'clicks' => $clicks,
                'followers' => $followers,
                'engagement' => $totalEngagement,
                'engagement_rate' => $engagementRate,
            ],
            'performanceList' => $performanceList,
            'chartData' => $chartData
        ]);
    }
}
