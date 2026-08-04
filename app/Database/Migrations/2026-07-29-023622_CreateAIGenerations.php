<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAIGenerations extends Migration
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
            'prompt_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
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
            'input_data' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'output_data' => [
                'type' => 'JSON',
                'null' => true,
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
        $this->forge->addForeignKey('prompt_id', 'ai_prompts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ai_generations');
    }

    public function down()
    {
        $this->forge->dropTable('ai_generations');
    }
}
