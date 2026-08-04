<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContentHashtags extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'content_platform_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
            ],
            'hashtag_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
            ],
            'is_ai_recommended' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
        ]);
        $this->forge->addPrimaryKey(['content_platform_id', 'hashtag_id']);
        $this->forge->addForeignKey('content_platform_id', 'content_platforms', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('hashtag_id', 'hashtags', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('content_hashtags');
    }

    public function down()
    {
        $this->forge->dropTable('content_hashtags');
    }
}
