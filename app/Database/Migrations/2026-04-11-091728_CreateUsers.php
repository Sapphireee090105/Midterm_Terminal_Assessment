<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsers extends Migration
{
    public function up() {
    $this->forge->addField([
        'id'       => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
        'username' => ['type' => 'VARCHAR', 'constraint' => '100', 'unique' => true],
        'password' => ['type' => 'VARCHAR', 'constraint' => '255'], // Hashed password storage 
    ]);
    $this->forge->addKey('id', true);
    $this->forge->createTable('users');
}

    public function down()
    {
        //
    }
}
