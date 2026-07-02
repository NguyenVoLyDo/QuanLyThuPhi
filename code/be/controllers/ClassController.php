<?php
use MVC\Controller;

class ClassController extends Controller
{
    private $classModel;

    public function __construct()
    {
        parent::__construct();
        require_once MODELS . 'ClassModel.php';
        $this->classModel = new ClassModel();
    }

    /** GET /api/classes */
    public function index()
    {
        $this->auth(['Admin', 'Accountant', 'Teacher']);
        $classes = $this->classModel->getAll();
        $this->send(200, true, 'Thành công', ['classes' => $classes]);
    }

    /** GET /api/classes/create */
    public function create()
    {
        $this->auth(['Admin']);
        $teachers  = $this->classModel->getTeachers();
        $this->send(200, true, 'Thành công', ['teachers' => $teachers]);
    }

    /** POST /api/classes */
    public function store()
    {
        $this->auth(['Admin']);
        $input = $this->request->all();

        $class_name = clean_input($input['class_name'] ?? '');
        $teacher_id = $input['teacher_id'] ?? null;
        $grade      = clean_input($input['grade'] ?? '');

        if (empty($class_name)) {
            $this->send(400, false, 'Vui lòng nhập tên lớp!');
        }

        $this->classModel->class_name = $class_name;
        $this->classModel->teacher_id = $teacher_id;
        $this->classModel->grade_level = $grade;
        $this->classModel->description = '';

        $result = $this->classModel->create();
        if ($result) {
            $this->send(200, true, 'Thêm lớp thành công!');
        } else {
            $this->send(500, false, 'Lỗi khi tạo lớp!');
        }
    }

    /** GET /api/classes/:id/edit */
    public function edit($params)
    {
        $this->auth(['Admin']);
        $id    = $params['id'] ?? 0;
        $class = $this->classModel->getById($id);

        if (!$class) {
            $this->send(404, false, 'Không tìm thấy lớp!');
        }

        $teachers  = $this->classModel->getTeachers();

        $this->send(200, true, 'Thành công', [
            'class'    => $class,
            'teachers' => $teachers
        ]);
    }

    /** PUT /api/classes/:id */
    public function update($params)
    {
        $this->auth(['Admin']);
        $input  = $this->request->all();
        $id     = $params['id'] ?? 0;

        $class_name = clean_input($input['class_name'] ?? '');
        $teacher_id = $input['teacher_id'] ?? null;
        $grade      = clean_input($input['grade'] ?? '');

        if (empty($class_name)) {
            $this->send(400, false, 'Vui lòng nhập tên lớp!');
        }

        $this->classModel->id = $id;
        $this->classModel->class_name = $class_name;
        $this->classModel->teacher_id = $teacher_id;
        $this->classModel->grade_level = $grade;
        $this->classModel->description = '';

        $result = $this->classModel->update();
        if ($result) {
            $this->send(200, true, 'Cập nhật lớp thành công!');
        } else {
            $this->send(500, false, 'Lỗi khi cập nhật lớp!');
        }
    }

    /** DELETE /api/classes/:id */
    public function delete($params)
    {
        $this->auth(['Admin']);
        $id     = $params['id'] ?? 0;
        $result = $this->classModel->delete($id);

        if ($result) {
            $this->send(200, true, 'Xóa lớp thành công!');
        } else {
            $this->send(500, false, 'Không thể xóa lớp học do đang có học sinh hoặc lỗi database!');
        }
    }
}
