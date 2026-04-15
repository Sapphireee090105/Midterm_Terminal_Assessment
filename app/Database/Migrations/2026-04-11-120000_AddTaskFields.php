<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTaskFields extends Migration
{
    public function up()
    {
        $fields = [
            'priority' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'Low',
                'null'       => false,
            ],
            'due_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
            ],
        ];

        $this->forge->addColumn('tasks', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tasks', ['priority', 'due_date', 'user_id']);
    }
}
