<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SocialInboxSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('social_inboxes')->truncate();

        $messages = [
            [
                'platform_id' => 1, // Instagram
                'type' => 'comment',
                'status' => 'unread',
                'sender_name' => 'Budi Santoso',
                'sender_handle' => '@budisnts',
                'message' => 'Wah, produk barunya keren banget min! Kapan mulai tersedia di marketplace?',
                'received_at' => date('Y-m-d H:i:s', strtotime('-15 minutes')),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'platform_id' => 1, // Instagram
                'type' => 'message',
                'status' => 'unread',
                'sender_name' => 'Sari Indah',
                'sender_handle' => '@sarindah',
                'message' => 'Halo kak, mau tanya untuk pengiriman ke Surabaya bisa pakai kargo nggak ya? Terima kasih.',
                'received_at' => date('Y-m-d H:i:s', strtotime('-1 hours')),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'platform_id' => 3, // Facebook
                'type' => 'comment',
                'status' => 'read',
                'sender_name' => 'Agus Yulianto',
                'sender_handle' => 'Agus Yulianto',
                'message' => 'Harganya berapa min? Tolong inbox ya.',
                'received_at' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'platform_id' => 2, // TikTok
                'type' => 'mention',
                'status' => 'unread',
                'sender_name' => 'TikTokers Gaul',
                'sender_handle' => '@gaulbanget',
                'message' => 'Coba liat nih guys, racun tiktok terbaru! Wajib banget punya @absys_official',
                'received_at' => date('Y-m-d H:i:s', strtotime('-3 hours')),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'platform_id' => 5, // X / Twitter
                'type' => 'message',
                'status' => 'replied',
                'sender_name' => 'Tech Review ID',
                'sender_handle' => '@techreviewid',
                'message' => 'Bisa minta brosur spesifikasi lengkapnya untuk di-review?
                
--- [Replied by Manager] ---
Halo Tech Review ID, tentu saja. Brosur sudah kami lampirkan di link berikut. Terima kasih!',
                'received_at' => date('Y-m-d H:i:s', strtotime('-1 days')),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'platform_id' => 1, // Instagram
                'type' => 'comment',
                'status' => 'resolved',
                'sender_name' => 'Spammer123',
                'sender_handle' => '@spammer123',
                'message' => 'Jual followers murah, cek IG kita kak!',
                'received_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'platform_id' => 4, // YouTube
                'type' => 'comment',
                'status' => 'unread',
                'sender_name' => 'Gaming Mania',
                'sender_handle' => '@gamingmania',
                'message' => 'Bang bikin tutorial cara settingnya dong, masih bingung nih.',
                'received_at' => date('Y-m-d H:i:s', strtotime('-30 minutes')),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('social_inboxes')->insertBatch($messages);
        
        echo "Social Inbox seeded with dummy data.\n";
    }
}
