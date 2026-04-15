<?php namespace App\Models;
use CodeIgniter\Model;

class TaskModel extends Model {
    protected $table = 'tasks';
    protected $primaryKey = 'id';
    // Status and user_id MUST be here for the API to save them
    protected $allowedFields = ['title', 'priority', 'due_date', 'status', 'user_id'];
}