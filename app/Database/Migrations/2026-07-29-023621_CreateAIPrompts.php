<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAIPrompts extends Migration
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
            'prompt' => [
                'type' => 'LONGTEXT',
            ],
            'model' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ai_prompts');
    }

    public function down()
    {
        $this->forge->dropTable('ai_prompts');
    }
}
