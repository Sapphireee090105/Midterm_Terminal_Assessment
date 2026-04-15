<?php namespace App\Controllers;
use App\Models\TaskModel;
use CodeIgniter\RESTful\ResourceController;

class TaskController extends ResourceController {
    protected $modelName = 'App\Models\TaskModel';
    protected $format    = 'json';

    public function index() {
        $userId = session()->get('user_id');
        $tasks = $this->model->where('user_id', $userId)->orderBy('id', 'DESC')->findAll();
        return $this->respond($tasks);
    }

    public function create() {
        $data = $this->request->getJSON(true) ?? [];

        if (!$this->validate([
            'title' => 'required|min_length[3]',
            'priority' => 'required|in_list[Low,Medium,High]',
            'due_date' => 'required|valid_date[Y-m-d]'
        ])) {
            return $this->fail($this->validator->getErrors());
        }

        $data['user_id'] = session()->get('user_id');
        $data['status']  = 'pending';

        if ($this->model->insert($data)) {
            return $this->respondCreated(['status' => 'success', 'msg' => 'Task saved']);
        }

        return $this->fail($this->model->errors());
    }

    public function update($id = null) {
        $data = $this->request->getJSON(true) ?? [];
        $userId = session()->get('user_id');

        $task = $this->model->where('id', $id)->where('user_id', $userId)->first();
        if (!$task) {
            return $this->failNotFound('Task not found');
        }

        $allowed = ['title', 'priority', 'due_date', 'status'];
        $updateData = array_intersect_key($data, array_flip($allowed));

        if (isset($updateData['priority']) && !in_array($updateData['priority'], ['Low', 'Medium', 'High'])) {
            return $this->failValidationErrors(['priority' => 'Invalid priority']);
        }

        if (isset($updateData['due_date']) && ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $updateData['due_date'])) {
            return $this->failValidationErrors(['due_date' => 'Date must be YYYY-MM-DD']);
        }

        if ($this->model->update($id, $updateData)) {
            return $this->respond(['status' => 'success', 'msg' => 'Task updated']);
        }

        return $this->fail($this->model->errors());
    }

    public function delete($id = null) {
        $userId = session()->get('user_id');
        $task = $this->model->where('id', $id)->where('user_id', $userId)->first();

        if (!$task) {
            return $this->failNotFound('Task not found');
        }

        if ($this->model->delete($id)) {
            return $this->respondDeleted(['status' => 'success', 'msg' => 'Task deleted']);
        }

        return $this->fail('Unable to delete task');
    }
}