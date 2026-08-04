<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSocialInboxes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'platform_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['comment', 'message', 'mention'],
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['unread', 'read', 'replied', 'resolved'],
                'default'    => 'unread',
            ],
            'sender_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'sender_handle' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'external_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'external_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ],
            'received_at' => [
                'type' => 'DATETIME',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('platform_id', 'platforms', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('social_inboxes');
    }

    public function down()
    {
        $this->forge->dropTable('social_inboxes');
    }
}
