<?php
use MVC\Controller;

class TeacherController extends Controller
{
    private $userModel;
    private $classModel;

    public function __construct()
    {
        parent::__construct();
        require_once MODELS . 'User.php';
        require_once MODELS . 'ClassModel.php';
        $this->userModel  = new User();
        $this->classModel = new ClassModel();
    }

    /** GET /api/teachers */
    public function index()
    {
        $this->auth(['Admin']);
        $search   = $this->request->get('search') ?? '';
        $page     = (int)($this->request->get('page') ?? 1);
        $per_page = 10;

        $database = new Database();
        $conn     = $database->connect();
        $offset   = ($page - 1) * $per_page;

        $sql = "SELECT u.*, GROUP_CONCAT(c.class_name SEPARATOR ', ') as classes
                FROM users u
                JOIN roles r ON u.role_id = r.id
                LEFT JOIN classes c ON c.teacher_id = u.id
                WHERE r.role_name = 'Teacher'";

        if (!empty($search)) {
            $sql .= " AND (u.username LIKE :search1 OR u.full_name LIKE :search2)";
        }
        $sql .= " GROUP BY u.id ORDER BY u.created_at DESC LIMIT :offset, :per_page";

        $stmt = $conn->prepare($sql);
        if (!empty($search)) {
            $sp = "%$search%";
            $stmt->bindValue(':search1', $sp);
            $stmt->bindValue(':search2', $sp);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':per_page', $per_page, PDO::PARAM_INT);
        $stmt->execute();
        $teachers = $stmt->fetchAll();

        $sqlCount = "SELECT COUNT(*) as total FROM users u JOIN roles r ON u.role_id = r.id WHERE r.role_name = 'Teacher'";
        if (!empty($search)) $sqlCount .= " AND (u.username LIKE :search1 OR u.full_name LIKE :search2)";
        $stmtCount = $conn->prepare($sqlCount);
        if (!empty($search)) {
            $stmtCount->bindValue(':search1', "%$search%");
            $stmtCount->bindValue(':search2', "%$search%");
        }
        $stmtCount->execute();
        $total = (int)$stmtCount->fetch()['total'];

        $this->send(200, true, 'Thành công', [
            'teachers'   => $teachers,
            'pagination' => paginate($total, $per_page, $page)
        ]);
    }

    /** GET /api/teachers/create */
    public function create()
    {
        $this->auth(['Admin']);
        $classes = $this->classModel->getAll();
        $this->send(200, true, 'Thành công', ['classes' => $classes]);
    }

    /** POST /api/teachers */
    public function store()
    {
        $this->auth(['Admin']);
        $input = $this->request->all();

        $this->userModel->username  = clean_input($input['username'] ?? '');
        $this->userModel->password  = $input['password'] ?? '';
        $this->userModel->full_name = clean_input($input['full_name'] ?? '');
        $this->userModel->email     = clean_input($input['email'] ?? '');
        $this->userModel->phone     = clean_input($input['phone'] ?? '');
        $this->userModel->role_id   = 3;
        $this->userModel->is_active = 1;

        if (empty($this->userModel->username) || empty($this->userModel->password) || empty($this->userModel->full_name)) {
            $this->send(400, false, 'Vui lòng nhập đầy đủ thông tin!');
        }

        $result = $this->userModel->create();
        if ($result['success']) {
            $teacher_id = $result['id'];
            if (!empty($input['class_ids'])) {
                foreach ($input['class_ids'] as $class_id) {
                    $this->classModel->assignTeacher($class_id, $teacher_id);
                }
            }
            $this->send(200, true, 'Thêm giáo viên thành công!');
        } else {
            $this->send(500, false, $result['message']);
        }
    }

    /** GET /api/teachers/:id/edit */
    public function edit($params)
    {
        $this->auth(['Admin']);
        $id      = $params['id'] ?? 0;
        $teacher = $this->userModel->getById($id);

        if (!$teacher || $teacher['role_name'] !== 'Teacher') {
            $this->send(404, false, 'Không tìm thấy giáo viên!');
        }

        $classes         = $this->classModel->getAll();
        $assigned_classes = $this->classModel->getClassIdsByTeacher($id);

        $this->send(200, true, 'Thành công', [
            'teacher'          => $teacher,
            'classes'          => $classes,
            'assigned_classes' => $assigned_classes
        ]);
    }

    /** PUT /api/teachers/:id */
    public function update($params)
    {
        $this->auth(['Admin']);
        $input = $this->request->all();
        $id    = $params['id'] ?? 0;

        $this->userModel->id        = $id;
        $this->userModel->username  = clean_input($input['username'] ?? '');
        $this->userModel->full_name = clean_input($input['full_name'] ?? '');
        $this->userModel->email     = clean_input($input['email'] ?? '');
        $this->userModel->phone     = clean_input($input['phone'] ?? '');
        $this->userModel->role_id   = 3;
        $this->userModel->is_active = isset($input['is_active']) ? 1 : 0;
        $this->userModel->password  = !empty($input['password']) ? $input['password'] : null;

        $result = $this->userModel->update();
        if ($result['success']) {
            $db   = new Database();
            $conn = $db->connect();
            $conn->prepare("UPDATE classes SET teacher_id = NULL WHERE teacher_id = ?")->execute([$id]);

            if (!empty($input['class_ids'])) {
                foreach ($input['class_ids'] as $class_id) {
                    $this->classModel->assignTeacher($class_id, $id);
                }
            }
            $this->send(200, true, 'Cập nhật giáo viên thành công!');
        } else {
            $this->send(500, false, $result['message']);
        }
    }

    /** DELETE /api/teachers/:id */
    public function delete($params)
    {
        $this->auth(['Admin']);
        $id = $params['id'] ?? 0;

        $db   = new Database();
        $conn = $db->connect();
        $conn->prepare("UPDATE classes SET teacher_id = NULL WHERE teacher_id = ?")->execute([$id]);

        $result = $this->userModel->delete($id);
        if ($result['success']) {
            $this->send(200, true, 'Xóa giáo viên thành công!');
        } else {
            $this->send(500, false, 'Không thể xóa giáo viên này!');
        }
    }
}
