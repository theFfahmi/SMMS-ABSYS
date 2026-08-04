<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAIUsageLogs extends Migration
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
            'user_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
            ],
            'feature' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'model' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'tokens_input' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'tokens_output' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'response_time_ms' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['success', 'failed'],
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ai_usage_logs');
    }

    public function down()
    {
        $this->forge->dropTable('ai_usage_logs');
    }
}
