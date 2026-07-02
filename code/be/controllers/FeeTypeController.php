<?php
use MVC\Controller;

class FeeTypeController extends Controller
{
    private $feeTypeModel;
    private $auditLog;

    public function __construct()
    {
        parent::__construct();
        require_once MODELS . 'FeeType.php';
        require_once MODELS . 'AuditLog.php';
        $this->feeTypeModel = new FeeType();
        $this->auditLog = new AuditLog();
    }

    /** GET /api/fee-types */
    public function index()
    {
        $this->auth(['Accountant', 'Teacher']);
        $search = $this->request->get('search') ?? '';
        $category = $this->request->get('category') ?? '';
        $year = $this->request->get('year') ?? '';
        $page = (int) ($this->request->get('page') ?? 1);
        $per_page = 10;

        $total = (int) $this->feeTypeModel->countAll($search, $category, $year);
        $fee_types = $this->feeTypeModel->getAll($search, $category, $year, $page, $per_page);
        $categories = $this->feeTypeModel->getCategories();

        $this->send(200, true, 'Thành công', [
            'fee_types' => $fee_types,
            'categories' => $categories,
            'pagination' => paginate($total, $per_page, $page)
        ]);
    }

    /** GET /api/fee-types/create */
    public function create()
    {
        $this->auth(['Accountant']);
        $categories = $this->feeTypeModel->getCategories();
        $this->send(200, true, 'Thành công', ['categories' => $categories]);
    }

    /** POST /api/fee-types */
    public function store()
    {
        $api_user = $this->auth(['Accountant']);
        $input = $this->request->all();

        $this->feeTypeModel->fee_name = clean_input($input['fee_name'] ?? '');
        $this->feeTypeModel->description = clean_input($input['description'] ?? '');
        $this->feeTypeModel->amount = floatval($input['amount'] ?? 0);
        $this->feeTypeModel->fee_category = $input['fee_category'] ?? '';
        $this->feeTypeModel->is_mandatory = isset($input['is_mandatory']) ? 1 : 0;
        $this->feeTypeModel->academic_year = clean_input($input['academic_year'] ?? '');
        $this->feeTypeModel->semester = !empty($input['semester']) ? $input['semester'] : null;
        $this->feeTypeModel->start_date = !empty($input['start_date']) ? $input['start_date'] : null;
        $this->feeTypeModel->end_date = !empty($input['end_date']) ? $input['end_date'] : null;
        $this->feeTypeModel->status = $input['status'] ?? 'Active';
        $this->feeTypeModel->is_active = ($this->feeTypeModel->status === 'Active') ? 1 : 0;

        $errors = $this->validateFeeType();
        if (!empty($errors)) {
            $this->send(400, false, 'Lỗi dữ liệu', ['errors' => $errors]);
        }

        $result = $this->feeTypeModel->create();

        if ($result['success']) {
            $this->auditLog->log($api_user['user_id'], 'CREATE_FEE_TYPE', 'fee_type', $result['id'], "Tên: {$this->feeTypeModel->fee_name}, Số tiền: {$this->feeTypeModel->amount}");

            $debt_msg = '';
            if (isset($input['create_debt'])) {
                $due_date = !empty($input['due_date']) ? $input['due_date'] : null;
                $debt_result = $this->feeTypeModel->createDebtForAllStudents($result['id'], $due_date);
                if ($debt_result['success']) {
                    $this->auditLog->log($api_user['user_id'], 'CREATE_DEBT_ALL', 'fee_type', $result['id'], 'Tạo công nợ cho tất cả học sinh');
                    $debt_msg = ' ' . $debt_result['message'];
                }
            }
            $this->send(200, true, $result['message'] . $debt_msg);
        } else {
            $this->send(500, false, $result['message']);
        }
    }

    /** GET /api/fee-types/:id/edit */
    public function edit($params)
    {
        $this->auth(['Accountant']);
        $id = $params['id'] ?? 0;
        $fee_type = $this->feeTypeModel->getById($id);

        if (!$fee_type) {
            $this->send(404, false, 'Không tìm thấy khoản thu!');
        }

        $categories = $this->feeTypeModel->getCategories();
        $this->send(200, true, 'Thành công', ['fee_type' => $fee_type, 'categories' => $categories]);
    }

    /** GET /api/fee-types/:id */
    public function view($params)
    {
        $this->auth(['Accountant', 'Teacher']);
        $id = $params['id'] ?? 0;
        $fee_type = $this->feeTypeModel->getById($id);

        if (!$fee_type) {
            $this->send(404, false, 'Không tìm thấy khoản thu!');
        }

        $this->send(200, true, 'Thành công', ['fee_type' => $fee_type]);
    }

    /** PUT /api/fee-types/:id */
    public function update($params)
    {
        $api_user = $this->auth(['Accountant']);
        $input = $this->request->all();
        $id = $params['id'] ?? 0;

        $this->feeTypeModel->id = $id;
        $this->feeTypeModel->fee_name = clean_input($input['fee_name'] ?? '');
        $this->feeTypeModel->description = clean_input($input['description'] ?? '');
        $this->feeTypeModel->amount = floatval($input['amount'] ?? 0);
        $this->feeTypeModel->fee_category = $input['fee_category'] ?? '';
        $this->feeTypeModel->is_mandatory = isset($input['is_mandatory']) ? 1 : 0;
        $this->feeTypeModel->academic_year = clean_input($input['academic_year'] ?? '');
        $this->feeTypeModel->semester = !empty($input['semester']) ? $input['semester'] : null;
        $this->feeTypeModel->start_date = !empty($input['start_date']) ? $input['start_date'] : null;
        $this->feeTypeModel->end_date = !empty($input['end_date']) ? $input['end_date'] : null;
        $this->feeTypeModel->status = $input['status'] ?? 'Active';
        $this->feeTypeModel->is_active = ($this->feeTypeModel->status === 'Active') ? 1 : 0;

        $errors = $this->validateFeeType($id);
        if (!empty($errors)) {
            $this->send(400, false, 'Lỗi dữ liệu', ['errors' => $errors]);
        }

        $result = $this->feeTypeModel->update();

        if ($result['success']) {
            $this->auditLog->log($api_user['user_id'], 'UPDATE_FEE_TYPE', 'fee_type', $id, "Cập nhật khoản thu ID: $id");
            $this->send(200, true, $result['message']);
        } else {
            $this->send(500, false, $result['message']);
        }
    }

    /** DELETE /api/fee-types/:id */
    public function delete($params)
    {
        $api_user = $this->auth(['Accountant']);
        $id = $params['id'] ?? 0;
        $result = $this->feeTypeModel->delete($id);

        if ($result['success']) {
            $this->auditLog->log($api_user['user_id'], 'DELETE_FEE_TYPE', 'fee_type', $id, "Xóa khoản thu ID: $id");
            $this->send(200, true, $result['message']);
        } else {
            $this->send(500, false, $result['message']);
        }
    }

    /** POST /api/fee-types/import */
    public function processImport()
    {
        $api_user = $this->auth(['Accountant']);
        $files = $this->request->files;

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

        $validCategories = array_keys($this->feeTypeModel->getCategories());
        $categoryMap = ['Học phí' => 'Tuition', 'Tiền ăn' => 'Meal', 'Đồng phục' => 'Uniform', 'Hoạt động' => 'Activity', 'Khác' => 'Other'];
        $count = 0;
        $errors = [];
        $row = 0;

        fgetcsv($handle); // Skip header

        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $row++;
            if (count($data) < 4)
                continue;

            $fee_name = trim($data[0]);
            $amount = floatval($data[1]);
            $fee_category = trim($data[2]);
            $academic_year = trim($data[3]);
            $semester = isset($data[4]) ? trim($data[4]) : null;
            $is_mandatory = isset($data[5]) ? (int) $data[5] : 0;
            $description = isset($data[6]) ? trim($data[6]) : '';

            if (!in_array($fee_category, $validCategories)) {
                $fee_category = $categoryMap[$fee_category] ?? 'Other';
            }

            $this->feeTypeModel->fee_name = $fee_name;
            $this->feeTypeModel->description = $description;
            $this->feeTypeModel->amount = $amount;
            $this->feeTypeModel->fee_category = $fee_category;
            $this->feeTypeModel->is_mandatory = $is_mandatory;
            $this->feeTypeModel->academic_year = $academic_year;
            $this->feeTypeModel->semester = $semester;
            $this->feeTypeModel->is_active = 1;
            $this->feeTypeModel->status = 'Active';

            $result = $this->feeTypeModel->create();

            if ($result['success']) {
                $count++;
                $this->auditLog->log($api_user['user_id'], 'IMPORT_FEE_TYPE', 'fee_type', $result['id'], "Import CSV: $fee_name");
            } else {
                $errors[] = "Dòng $row: " . $result['message'];
            }
        }
        fclose($handle);

        $this->send(200, true, "Đã nhập thành công $count khoản thu.", ['errors' => $errors]);
    }

    private function validateFeeType($exclude_id = null)
    {
        $errors = [];
        if (empty($this->feeTypeModel->fee_name))
            $errors['fee_name'] = 'Vui lòng nhập tên khoản thu!';
        if (empty($this->feeTypeModel->amount) || $this->feeTypeModel->amount <= 0)
            $errors['amount'] = 'Vui lòng nhập số tiền hợp lệ!';
        if (empty($this->feeTypeModel->fee_category))
            $errors['fee_category'] = 'Vui lòng chọn loại khoản thu!';
        if (empty($this->feeTypeModel->academic_year))
            $errors['academic_year'] = 'Vui lòng nhập năm học!';
        if (!empty($this->feeTypeModel->start_date) && !empty($this->feeTypeModel->end_date)) {
            if ($this->feeTypeModel->end_date < $this->feeTypeModel->start_date) {
                $errors['end_date'] = 'Ngày kết thúc phải sau ngày bắt đầu!';
            }
        }
        return $errors;
    }
}