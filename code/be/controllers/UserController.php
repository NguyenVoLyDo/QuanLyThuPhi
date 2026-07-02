<?php
use MVC\Controller;

class UserController extends Controller
{
    private $userModel;
    private $classModel;

    public function __construct()
    {
        parent::__construct();
        require_once MODELS . 'User.php';
        require_once MODELS . 'ClassModel.php';
        $this->userModel = new User();
        $this->classModel = new ClassModel();
    }

    /**
     * GET /api/users
     */
    public function index()
    {
        $this->auth(['Admin']);

        $search = $this->request->get('search') ?? '';
        $page = (int)($this->request->get('page') ?? 1);
        $per_page = 10;

        $total = (int) $this->userModel->countAll($search);
        $users = $this->userModel->getAll($search, $page, $per_page);

        $this->send(200, true, "Thành công", [
            'users' => $users,
            'pagination' => paginate($total, $per_page, $page)
        ]);
    }

    /**
     * GET /api/users/create
     */
    public function create()
    {
        $this->auth(['Admin']);
        $roles = $this->userModel->getRoles();
        
        require_once MODELS . 'Student.php';
        $studentModel = new Student();
        $students = $studentModel->getAll('', '', 1, 1000);
        $classes = $this->classModel->getAll();

        $this->send(200, true, "Thành công", [
            'roles' => $roles,
            'students' => $students,
            'classes' => $classes
        ]);
    }

    /**
     * POST /api/users
     */
    public function store()
    {
        $this->auth(['Admin']);
        $input = $this->request->all();

        $this->userModel->username = clean_input($input['username'] ?? '');
        $this->userModel->password = $input['password'] ?? '';
        $this->userModel->full_name = clean_input($input['full_name'] ?? '');
        $this->userModel->email = clean_input($input['email'] ?? '');
        $this->userModel->phone = clean_input($input['phone'] ?? '');
        $this->userModel->role_id = $input['role_id'] ?? 0;
        $this->userModel->student_id = !empty($input['student_id']) ? $input['student_id'] : null;
        $this->userModel->is_active = isset($input['is_active']) ? 1 : 0;

        $errors = $this->validateUser();
        if (!empty($errors)) {
            $this->send(400, false, "Lỗi dữ liệu", ['errors' => $errors]);
        }

        $result = $this->userModel->create();

        if ($result['success'] && !empty($input['class_id']) && !empty($input['role_id'])) {
            $newUser = $this->userModel->getByUsername($this->userModel->username);
            if ($newUser) {
                $this->classModel->assignTeacher($input['class_id'], $newUser['id']);
            }
        }

        if ($result['success']) {
            $this->send(200, true, $result['message']);
        } else {
            $this->send(500, false, $result['message']);
        }
    }

    /**
     * GET /api/users/:id/edit
     */
    public function edit($params)
    {
        $this->auth(['Admin']);
        $id = $params['id'] ?? 0;
        $user = $this->userModel->getById($id);

        if (!$user) {
            $this->send(404, false, 'Không tìm thấy user!');
        }

        $roles = $this->userModel->getRoles();
        
        require_once MODELS . 'Student.php';
        $studentModel = new Student();
        $students = $studentModel->getAll('', '', 1, 1000);
        $classes = $this->classModel->getAll();
        $current_class_id = $this->classModel->getClassIdByTeacher($id);

        $this->send(200, true, "Thành công", [
            'user' => $user,
            'roles' => $roles,
            'students' => $students,
            'classes' => $classes,
            'current_class_id' => $current_class_id
        ]);
    }

    /**
     * PUT /api/users/:id
     */
    public function update($params)
    {
        $this->auth(['Admin']);
        $input = $this->request->all();
        $id = $params['id'] ?? 0;

        $this->userModel->id = $id;
        $this->userModel->username = clean_input($input['username'] ?? '');
        $this->userModel->password = !empty($input['password']) ? $input['password'] : null;
        $this->userModel->full_name = clean_input($input['full_name'] ?? '');
        $this->userModel->email = clean_input($input['email'] ?? '');
        $this->userModel->phone = clean_input($input['phone'] ?? '');
        $this->userModel->role_id = $input['role_id'] ?? 0;
        $this->userModel->student_id = !empty($input['student_id']) ? $input['student_id'] : null;
        $this->userModel->is_active = isset($input['is_active']) ? 1 : 0;

        $errors = $this->validateUser($this->userModel->id);
        if (!empty($errors)) {
            $this->send(400, false, "Lỗi dữ liệu", ['errors' => $errors]);
        }

        $result = $this->userModel->update();
        
        if ($result['success'] && isset($input['class_id']) && !empty($input['class_id'])) { 
            $this->classModel->assignTeacher($input['class_id'], $this->userModel->id);
        }

        if ($result['success']) {
            $this->send(200, true, $result['message']);
        } else {
            $this->send(500, false, $result['message']);
        }
    }

    /**
     * DELETE /api/users/:id
     */
    public function delete($params)
    {
        $this->auth(['Admin']);
        $id = $params['id'] ?? 0;
        
        $result = $this->userModel->delete($id);
        if ($result['success']) {
            $this->send(200, true, $result['message']);
        } else {
            $this->send(500, false, $result['message']);
        }
    }

    /**
     * GET /api/profile
     */
    public function profile()
    {
        $api_user = $this->auth();
        $user = $this->userModel->getById($api_user['user_id']);
        if (!$user) {
            $this->send(404, false, "Không tìm thấy người dùng");
        }
        $this->send(200, true, "Thành công", ['user' => $user]);
    }

    /**
     * PUT /api/profile
     */
    public function updateProfile()
    {
        $api_user = $this->auth();
        $input = $this->request->all();

        $this->userModel->id = $api_user['user_id'];
        $this->userModel->full_name = clean_input($input['full_name'] ?? '');
        $this->userModel->email = clean_input($input['email'] ?? '');
        $this->userModel->phone = clean_input($input['phone'] ?? '');
        
        // Cần đảm bảo update() trong Model không reset các field khác nếu null
        $result = $this->userModel->updateProfile();
        
        if ($result['success']) {
            $this->send(200, true, "Cập nhật tài khoản thành công");
        } else {
            $this->send(500, false, "Lỗi: " . $result['message']);
        }
    }

    /**
     * POST /api/change-password
     */
    public function changePassword()
    {
        $api_user = $this->auth();
        $input = $this->request->all();

        $old_pass = $input['old_password'] ?? '';
        $new_pass = $input['new_password'] ?? '';
        $confirm_pass = $input['confirm_password'] ?? '';

        if (empty($old_pass) || empty($new_pass)) {
            $this->send(400, false, "Vui lòng nhập đầy đủ thông tin");
        }

        if ($new_pass !== $confirm_pass) {
            $this->send(400, false, "Mật khẩu xác nhận không khớp");
        }

        $result = $this->userModel->changePassword($api_user['user_id'], $old_pass, $new_pass);
        if ($result['success']) {
            $this->send(200, true, "Đổi mật khẩu thành công");
        } else {
            $this->send(400, false, "Lỗi: " . $result['message']);
        }
    }

    /**
     * POST /api/users/:id/reset-password
     */
    public function resetPassword($params)
    {
        $this->auth(['Admin']);
        $id = $params['id'] ?? 0;
        
        $result = $this->userModel->resetPassword($id);
        if ($result['success']) {
            $this->send(200, true, "Đã đặt lại mật khẩu về mặc định (123456)");
        } else {
            $this->send(500, false, "Lỗi: " . $result['message']);
        }
    }

    private function validateUser($exclude_id = null)
    {
        $errors = [];
        if (empty($this->userModel->username)) $errors['username'] = 'Vui lòng nhập tên đăng nhập!';
        if ($exclude_id === null && empty($this->userModel->password)) $errors['password'] = 'Vui lòng nhập mật khẩu!';
        if (empty($this->userModel->full_name)) $errors['full_name'] = 'Vui lòng nhập họ tên!';
        if (empty($this->userModel->role_id)) $errors['role_id'] = 'Vui lòng chọn vai trò!';
        return $errors;
    }
}