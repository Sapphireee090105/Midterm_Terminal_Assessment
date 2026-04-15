<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTasks extends Migration
{
    public function up() {
    $this->forge->addField([
        'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
        'title'       => ['type' => 'VARCHAR', 'constraint' => '255'],
        'status'      => ['type' => 'ENUM', 'constraint' => ['pending', 'completed'], 'default' => 'pending'],
        'created_at'  => ['type' => 'DATETIME', 'null' => true],
    ]);
    $this->forge->addKey('id', true);
    $this->forge->createTable('tasks');
}

    public function down()
    {
        //
    }
}
