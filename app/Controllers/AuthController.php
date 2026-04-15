<?php namespace App\Controllers;
use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class AuthController extends ResourceController {
    public function register() {
        $model = new UserModel();
        $data = $this->request->getJSON(true) ?? [];

        $rules = [
            'username' => 'required|is_unique[users.username]',
            'password' => 'required|min_length[5]',
        ];

        if (!$this->validateData($data, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $model->insert($data);
        return $this->respondCreated(['msg' => 'User registered']);
    }

    public function login() {
        $model = new UserModel();
        $data = $this->request->getJSON(true);
        $user = $model->where('username', $data['username'])->first();

        if ($user && password_verify($data['password'], $user['password'])) {
            session()->set(['isLoggedIn' => true, 'username' => $user['username'], 'user_id' => $user['id']]);
            return $this->respond(['msg' => 'Login successful']);
        }
        return $this->failUnauthorized('Invalid credentials');
    }

    public function logout() {
        session()->destroy();
        return redirect()->to('/');
    }
}