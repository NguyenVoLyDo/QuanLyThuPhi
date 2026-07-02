<?php
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    /**
     * Danh sách user (chỉ Admin)
     */
    public function index() {
        check_permission(['Admin']);
        
        $search = $_GET['search'] ?? '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per_page = 10;
        
        $total = $this->userModel->countAll($search);
        $pagination = paginate($total, $per_page, $page);
        
        $users = $this->userModel->getAll($search, $page, $per_page);
        
        require_once __DIR__ . '/../views/users/index.php';
    }
    
    /**
     * Form thêm user
     */
    public function create() {
        check_permission(['Admin']);
        
        $roles = $this->userModel->getRoles();
        
        // Lấy danh sách học sinh để liên kết (cho role Student)
        require_once __DIR__ . '/../models/Student.php';
        $studentModel = new Student();
        $students = $studentModel->getAll('', '', 1, 1000);
        
        require_once __DIR__ . '/../views/users/create.php';
    }
    
    /**
     * Xử lý thêm user
     */
    public function store() {
        check_permission(['Admin']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?controller=user&action=index');
            exit();
        }
        
        $this->userModel->username = clean_input($_POST['username']);
        $this->userModel->password = $_POST['password'];
        $this->userModel->full_name = clean_input($_POST['full_name']);
        $this->userModel->email = clean_input($_POST['email']);
        $this->userModel->phone = clean_input($_POST['phone']);
        $this->userModel->role_id = $_POST['role_id'];
        $this->userModel->student_id = !empty($_POST['student_id']) ? $_POST['student_id'] : null;
        $this->userModel->is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Validate
        $errors = $this->validateUser();
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: /index.php?controller=user&action=create');
            exit();
        }
        
        $result = $this->userModel->create();
        
        if ($result['success']) {
            set_flash('success', $result['message'], 'success');
        } else {
            set_flash('error', $result['message'], 'danger');
        }
        
        header('Location: /index.php?controller=user&action=index');
        exit();
    }
    
    /**
     * Form sửa user
     */
    public function edit() {
        check_permission(['Admin']);
        
        $id = $_GET['id'] ?? 0;
        $user = $this->userModel->getById($id);
        
        if (!$user) {
            set_flash('error', 'Không tìm thấy user!', 'danger');
            header('Location: /index.php?controller=user&action=index');
            exit();
        }
        
        $roles = $this->userModel->getRoles();
        
        require_once __DIR__ . '/../models/Student.php';
        $studentModel = new Student();
        $students = $studentModel->getAll('', '', 1, 1000);
        
        require_once __DIR__ . '/../views/users/edit.php';
    }
    
    /**
     * Xử lý cập nhật
     */
    public function update() {
        check_permission(['Admin']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?controller=user&action=index');
            exit();
        }
        
        $this->userModel->id = $_POST['id'];
        $this->userModel->username = clean_input($_POST['username']);
        $this->userModel->password = !empty($_POST['password']) ? $_POST['password'] : null;
        $this->userModel->full_name = clean_input($_POST['full_name']);
        $this->userModel->email = clean_input($_POST['email']);
        $this->userModel->phone = clean_input($_POST['phone']);
        $this->userModel->role_id = $_POST['role_id'];
        $this->userModel->student_id = !empty($_POST['student_id']) ? $_POST['student_id'] : null;
        $this->userModel->is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Validate
        $errors = $this->validateUser($this->userModel->id);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: /index.php?controller=user&action=edit&id=' . $this->userModel->id);
            exit();
        }
        
        $result = $this->userModel->update();
        
        if ($result['success']) {
            set_flash('success', $result['message'], 'success');
        } else {
            set_flash('error', $result['message'], 'danger');
        }
        
        header('Location: /index.php?controller=user&action=index');
        exit();
    }
    
    /**
     * Xóa user
     */
    public function delete() {
        check_permission(['Admin']);
        
        $id = $_GET['id'] ?? 0;
        
        // Không cho phép xóa chính mình
        if ($id == $_SESSION['user_id']) {
            set_flash('error', 'Không thể xóa tài khoản đang đăng nhập!', 'danger');
            header('Location: /index.php?controller=user&action=index');
            exit();
        }
        
        $result = $this->userModel->delete($id);
        
        if ($result['success']) {
            set_flash('success', $result['message'], 'success');
        } else {
            set_flash('error', $result['message'], 'danger');
        }
        
        header('Location: /index.php?controller=user&action=index');
        exit();
    }
    
    /**
     * Đổi mật khẩu
     */
    public function changePassword() {
        check_permission();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'];
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            // Validate
            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                set_flash('error', 'Vui lòng nhập đầy đủ thông tin!', 'danger');
                header('Location: /index.php?controller=user&action=changePassword');
                exit();
            }
            
            if ($new_password !== $confirm_password) {
                set_flash('error', 'Mật khẩu mới không khớp!', 'danger');
                header('Location: /index.php?controller=user&action=changePassword');
                exit();
            }
            
            if (strlen($new_password) < 6) {
                set_flash('error', 'Mật khẩu mới phải có ít nhất 6 ký tự!', 'danger');
                header('Location: /index.php?controller=user&action=changePassword');
                exit();
            }
            
            // Verify current password
            $user = $this->userModel->getById($user_id);
            if (!password_verify($current_password, $user['password'])) {
                set_flash('error', 'Mật khẩu hiện tại không đúng!', 'danger');
                header('Location: /index.php?controller=user&action=changePassword');
                exit();
            }
            
            // Update password
            $this->userModel->id = $user_id;
            $this->userModel->password = $new_password;
            
            $result = $this->userModel->update();
            
            if ($result['success']) {
                set_flash('success', 'Đổi mật khẩu thành công!', 'success');
            } else {
                set_flash('error', 'Có lỗi xảy ra!', 'danger');
            }
            
            header('Location: /index.php?controller=user&action=changePassword');
            exit();
        }
        
        require_once __DIR__ . '/../views/users/change_password.php';
    }
    
    /**
     * Validate
     */
    private function validateUser($exclude_id = null) {
        $errors = [];
        
        if (empty($this->userModel->username)) {
            $errors['username'] = 'Vui lòng nhập tên đăng nhập!';
        } elseif (strlen($this->userModel->username) < 4) {
            $errors['username'] = 'Tên đăng nhập phải có ít nhất 4 ký tự!';
        }
        
        // Chỉ validate password khi tạo mới hoặc có nhập password
        if (empty($exclude_id)) { // Tạo mới
            if (empty($this->userModel->password)) {
                $errors['password'] = 'Vui lòng nhập mật khẩu!';
            } elseif (strlen($this->userModel->password) < 6) {
                $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự!';
            }
        } elseif (!empty($this->userModel->password) && strlen($this->userModel->password) < 6) {
            $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự!';
        }
        
        if (empty($this->userModel->full_name)) {
            $errors['full_name'] = 'Vui lòng nhập họ tên!';
        }
        
        if (!empty($this->userModel->email) && !filter_var($this->userModel->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ!';
        }
        
        if (empty($this->userModel->role_id)) {
            $errors['role_id'] = 'Vui lòng chọn vai trò!';
        }
        
        return $errors;
    }
}
