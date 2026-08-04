<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'username'   => 'admin',
                'email'      => 'admin@absys.com',
                'password'   => password_hash('admin123', PASSWORD_DEFAULT),
                'full_name'  => 'System Administrator',
                'role'       => 'admin',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username'   => 'manager',
                'email'      => 'manager@absys.com',
                'password'   => password_hash('manager123', PASSWORD_DEFAULT),
                'full_name'  => 'Social Media Manager',
                'role'       => 'social_media_manager',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username'   => 'creator',
                'email'      => 'creator@absys.com',
                'password'   => password_hash('creator123', PASSWORD_DEFAULT),
                'full_name'  => 'Content Creator',
                'role'       => 'content_creator',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username'   => 'designer',
                'email'      => 'designer@absys.com',
                'password'   => password_hash('designer123', PASSWORD_DEFAULT),
                'full_name'  => 'Graphic Designer',
                'role'       => 'designer',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username'   => 'reviewer',
                'email'      => 'reviewer@absys.com',
                'password'   => password_hash('reviewer123', PASSWORD_DEFAULT),
                'full_name'  => 'Content Reviewer',
                'role'       => 'reviewer',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($users as $userData) {
            // Upsert (insert if not exists, update if exists)
            $existing = $this->db->table('users')->where('username', $userData['username'])->get()->getRow();
            if ($existing) {
                $this->db->table('users')->where('username', $userData['username'])->update($userData);
                echo "User updated: {$userData['username']} ({$userData['role']})\n";
            } else {
                $this->db->table('users')->insert($userData);
                echo "User created: {$userData['username']} ({$userData['role']})\n";
            }
        }

        echo "\nUserSeeder completed successfully for all 5 roles.\n";
    }
}
