<?php
use MVC\Controller;

class StudentController extends Controller
{
    private $studentModel;

    public function __construct()
    {
        parent::__construct();
        require_once MODELS . 'Student.php';
        require_once MODELS . 'ClassModel.php';
        $this->studentModel = new Student();
    }

    private function resolveClassFilter($api_user, $class_id = '')
    {
        if (strcasecmp(trim($api_user['role_name']), 'Teacher') === 0) {
            $classModel = new ClassModel();
            $teacherClassIds = $classModel->getClassIdsByTeacher($api_user['user_id']);
            $validIds = array_map('strval', $teacherClassIds);

            if (!empty($class_id) && in_array((string)$class_id, $validIds)) {
                return $class_id;
            }
            return empty($teacherClassIds) ? [-1] : $teacherClassIds;
        }
        return $class_id;
    }

    /** GET /api/students */
    public function index()
    {
        $api_user = $this->auth(['Admin', 'Accountant', 'Teacher']);
        $search   = trim($this->request->get('search') ?? '');
        $class_id = $this->request->get('class_id') ?? '';
        $page     = (int)($this->request->get('page') ?? 1);
        $per_page = 10;

        $class_id = $this->resolveClassFilter($api_user, $class_id);

        $total    = (int)$this->studentModel->countAll($search, $class_id);
        $students = $this->studentModel->getAll($search, $class_id, $page, $per_page);
        $classes  = $this->studentModel->getClasses();

        if (strcasecmp(trim($api_user['role_name']), 'Teacher') === 0) {
            $classModel      = new ClassModel();
            $teacherClassIds = $classModel->getClassIdsByTeacher($api_user['user_id']);
            $classes = array_values(array_filter($classes, fn($c) => in_array($c['id'], $teacherClassIds)));
        }

        $this->send(200, true, 'Thành công', [
            'students'   => $students,
            'classes'    => $classes,
            'pagination' => paginate($total, $per_page, $page)
        ]);
    }

    /** GET /api/students/create */
    public function create()
    {
        $api_user = $this->auth(['Admin', 'Accountant', 'Teacher']);
        $classes  = $this->studentModel->getClasses();

        if ($api_user['role_name'] === 'Teacher') {
            $classModel      = new ClassModel();
            $teacherClassIds = $classModel->getClassIdsByTeacher($api_user['user_id']);
            $classes = array_values(array_filter($classes, fn($c) => in_array($c['id'], $teacherClassIds)));
        }

        $this->send(200, true, 'Thành công', ['classes' => $classes]);
    }

    /** POST /api/students */
    public function store()
    {
        $api_user = $this->auth(['Admin', 'Accountant']);
        $input    = $this->request->all();

        $this->studentModel->student_code = clean_input($input['student_code'] ?? '');
        $this->studentModel->full_name    = clean_input($input['full_name'] ?? '');
        $this->studentModel->date_of_birth = $input['date_of_birth'] ?? '';
        $this->studentModel->gender       = $input['gender'] ?? '';
        $this->studentModel->class_id     = $input['class_id'] ?? '';
        $this->studentModel->parent_name  = clean_input($input['parent_name'] ?? '');
        $this->studentModel->parent_phone = clean_input($input['parent_phone'] ?? '');
        $this->studentModel->parent_email = clean_input($input['parent_email'] ?? '');
        $this->studentModel->address      = clean_input($input['address'] ?? '');
        $this->studentModel->is_active    = isset($input['is_active']) ? 1 : 0;

        $errors = $this->validateStudent();
        if (!empty($errors)) {
            $this->send(400, false, 'Lỗi xác thực dữ liệu', ['errors' => $errors]);
        }

        $result = $this->studentModel->create();
        if ($result['success']) {
            $this->send(200, true, $result['message']);
        } else {
            $this->send(500, false, $result['message']);
        }
    }

    /** GET /api/students/:id */
    public function view($params)
    {
        $api_user = $this->auth(['Admin', 'Accountant', 'Teacher']);
        $id       = $params['id'] ?? 0;
        $student  = $this->studentModel->getById($id);

        if (!$student) {
            $this->send(404, false, 'Không tìm thấy học sinh!');
        }

        if ($api_user['role_name'] === 'Teacher') {
            $classModel      = new ClassModel();
            $teacherClassIds = $classModel->getClassIdsByTeacher($api_user['user_id']);
            if (!in_array($student['class_id'], $teacherClassIds)) {
                $this->send(403, false, 'Bạn không có quyền xem học sinh này!');
            }
        }

        $debts   = $this->studentModel->getDebts($id);
        $payments = $this->studentModel->getPaymentHistory($id);

        require_once MODELS . 'Exemption.php';
        $exemptionModel   = new Exemption();
        $studentExemptions = $exemptionModel->getStudentExemptions($id);
        $allExemptions    = $exemptionModel->getAll('', 1, 100);

        $this->send(200, true, 'Thành công', [
            'student'          => $student,
            'debts'            => $debts,
            'payments'         => $payments,
            'studentExemptions' => $studentExemptions,
            'allExemptions'    => $allExemptions
        ]);
    }

    /** GET /api/students/:id/edit */
    public function edit($params)
    {
        $api_user = $this->auth(['Admin', 'Accountant', 'Teacher']);
        $id       = $params['id'] ?? 0;
        $student  = $this->studentModel->getById($id);

        if (!$student) {
            $this->send(404, false, 'Không tìm thấy học sinh!');
        }

        if ($api_user['role_name'] === 'Teacher') {
            $classModel      = new ClassModel();
            $teacherClassIds = $classModel->getClassIdsByTeacher($api_user['user_id']);
            if (!in_array((int)$student['class_id'], $teacherClassIds)) {
                $this->send(403, false, 'Bạn chỉ có thể sửa học sinh trong lớp mà bạn chủ nhiệm!');
            }
        }

        $classes = $this->studentModel->getClasses();
        if ($api_user['role_name'] === 'Teacher') {
            $classModel      = new ClassModel();
            $teacherClassIds = $classModel->getClassIdsByTeacher($api_user['user_id']);
            $classes = array_values(array_filter($classes, fn($c) => in_array($c['id'], $teacherClassIds)));
        }

        $this->send(200, true, 'Thành công', ['student' => $student, 'classes' => $classes]);
    }

    /** PUT /api/students/:id */
    public function update($params)
    {
        $api_user = $this->auth(['Admin', 'Accountant', 'Teacher']);
        $input    = $this->request->all();
        $id       = $params['id'] ?? ($input['id'] ?? 0);

        $this->studentModel->id           = $id;
        $this->studentModel->student_code = clean_input($input['student_code'] ?? '');
        $this->studentModel->full_name    = clean_input($input['full_name'] ?? '');
        $this->studentModel->date_of_birth = $input['date_of_birth'] ?? '';
        $this->studentModel->gender       = $input['gender'] ?? '';
        $this->studentModel->class_id     = $input['class_id'] ?? '';
        $this->studentModel->parent_name  = clean_input($input['parent_name'] ?? '');
        $this->studentModel->parent_phone = clean_input($input['parent_phone'] ?? '');
        $this->studentModel->parent_email = clean_input($input['parent_email'] ?? '');
        $this->studentModel->address      = clean_input($input['address'] ?? '');
        $this->studentModel->is_active    = isset($input['is_active']) ? 1 : 0;

        $errors = $this->validateStudent($id);
        if (!empty($errors)) {
            $this->send(400, false, 'Lỗi xác thực dữ liệu', ['errors' => $errors]);
        }

        $result = $this->studentModel->update();
        if ($result['success']) {
            $this->send(200, true, $result['message']);
        } else {
            $this->send(500, false, $result['message']);
        }
    }

    /** DELETE /api/students/:id */
    public function delete($params)
    {
        $api_user = $this->auth(['Admin']);
        $id       = $params['id'] ?? 0;

        $result = $this->studentModel->delete($id);
        if ($result['success']) {
            $this->send(200, true, $result['message']);
        } else {
            $this->send(500, false, $result['message']);
        }
    }

    /** POST /api/students/:id/note */
    public function updateNote($params)
    {
        $api_user = $this->auth(['Admin', 'Accountant', 'Teacher']);
        $input    = $this->request->all();
        $id       = $params['id'] ?? 0;
        $notes    = clean_input($input['notes'] ?? '');

        $res = $this->studentModel->updateNote($id, $notes);
        if ($res['success']) {
            $this->send(200, true, $res['message']);
        } else {
            $this->send(500, false, $res['message']);
        }
    }

    /** GET /api/students/export */
    public function export()
    {
        $api_user = $this->auth(['Admin', 'Accountant', 'Teacher']);
        $search   = $this->request->get('search') ?? '';
        $class_id = $this->request->get('class_id') ?? '';

        $class_id = $this->resolveClassFilter($api_user, $class_id);
        $students = $this->studentModel->getAll($search, $class_id, 1, 100000);

        $this->send(200, true, 'Thành công', ['students' => $students]);
    }

    /** POST /api/students/import */
    public function processImport()
    {
        $api_user = $this->auth(['Admin', 'Accountant']);
        $files    = $this->request->files;

        if (!isset($files['file']['name']) || $files['file']['name'] === '') {
            $this->send(400, false, 'Vui lòng chọn file!');
        }

        $extension = strtolower(pathinfo($files['file']['name'], PATHINFO_EXTENSION));
        if ($extension !== 'csv') {
            $this->send(400, false, 'Chỉ chấp nhận file CSV!');
        }

        $handle = fopen($files['file']['tmp_name'], 'r');
        if ($handle === false) {
            $this->send(500, false, 'Không thể mở file!');
        }

        fgetcsv($handle); // Skip header
        $count = 0;
        $errors = [];
        $row = 0;

        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $row++;
            if (count($data) < 5) continue;

            $this->studentModel->student_code  = trim($data[0]);
            $this->studentModel->full_name     = trim($data[1]);
            $this->studentModel->date_of_birth = trim($data[2]);
            $this->studentModel->gender        = trim($data[3]);
            $this->studentModel->class_id      = trim($data[4]);
            $this->studentModel->parent_name   = isset($data[5]) ? trim($data[5]) : '';
            $this->studentModel->parent_phone  = isset($data[6]) ? trim($data[6]) : '';
            $this->studentModel->parent_email  = isset($data[7]) ? trim($data[7]) : '';
            $this->studentModel->address       = isset($data[8]) ? trim($data[8]) : '';
            $this->studentModel->is_active     = 1;

            $result = $this->studentModel->create();
            if ($result['success']) {
                $count++;
            } else {
                $errors[] = "Dòng $row: " . $result['message'];
            }
        }
        fclose($handle);

        require_once MODELS . 'AuditLog.php';
        $auditLog = new AuditLog();
        $auditLog->log($api_user['user_id'], 'IMPORT_STUDENT', 'students', 0, "Nhập thành công $count học sinh từ CSV");

        $this->send(200, true, "Đã nhập thành công $count học sinh.", ['errors' => $errors]);
    }


    private function validateStudent($exclude_id = null)
    {
        $errors = [];
        if (empty($this->studentModel->student_code)) $errors['student_code'] = 'Vui lòng nhập mã học sinh!';
        if (empty($this->studentModel->full_name))    $errors['full_name']    = 'Vui lòng nhập họ tên!';
        if (empty($this->studentModel->date_of_birth)) {
            $errors['date_of_birth'] = 'Vui lòng chọn ngày sinh!';
        } else {
            $dob = strtotime($this->studentModel->date_of_birth);
            if ($dob > time()) {
                $errors['date_of_birth'] = 'Ngày sinh không thể ở tương lai!';
            }
        }
        if (empty($this->studentModel->gender))       $errors['gender']       = 'Vui lòng chọn giới tính!';
        if (empty($this->studentModel->class_id))     $errors['class_id']     = 'Vui lòng chọn lớp!';
        if (!empty($this->studentModel->parent_phone)) {
            if (!preg_match('/^0[0-9]{8,10}$/', $this->studentModel->parent_phone)) {
                $errors['parent_phone'] = 'Số điện thoại phụ huynh không hợp lệ!';
            }
        }
        return $errors;
    }
}
