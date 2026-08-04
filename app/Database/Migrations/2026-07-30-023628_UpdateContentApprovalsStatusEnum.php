<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateContentApprovalsStatusEnum extends Migration
{
    public function up()
    {
        $fields = [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'revision', 'rejected'],
                'default'    => 'pending',
            ],
        ];
        $this->forge->modifyColumn('content_approvals', $fields);
    }

    public function down()
    {
        $fields = [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default'    => 'pending',
            ],
        ];
        $this->forge->modifyColumn('content_approvals', $fields);
    }
}
