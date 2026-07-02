<?php
$page_title = 'Tạo yêu cầu miễn giảm';
include __DIR__ . '/../layout/header.php';

// Get students in teacher's class
$classModel = new ClassModel();
$class_id = $classModel->getClassIdByTeacher($_SESSION['user_id']);
$studentModel = new Student();  
$students = $studentModel->getAll('', $class_id, 1, 1000);

// Get exemptions
$exemptionModel = new Exemption();
$exemptions = $exemptionModel->getAll('', 1, 1000);
?>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-hand-holding-usd"></i> Tạo yêu cầu miễn giảm học phí</h5>
    </div>
    <div class="card-body">
        <form action="index.php?controller=exemption&action=assign" method="POST">
            <div class="mb-3">
                <label class="form-label">Chọn học sinh <span class="text-danger">*</span></label>
                <select name="student_id" class="form-select" required>
                    <option value="">-- Chọn học sinh --</option>
                    <?php foreach ($students as $student): ?>
                        <option value="<?php echo $student['id']; ?>">
                            <?php echo $student['student_code'] . ' - ' . $student['full_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Loại miễn giảm <span class="text-danger">*</span></label>
                <select name="exemption_id" class="form-select" required id="exemption_select">
                    <option value="">-- Chọn loại miễn giảm --</option>
                    <?php foreach ($exemptions as $ex): ?>
                        <option value="<?php echo $ex['id']; ?>" 
                            data-type="<?php echo $ex['discount_type']; ?>" 
                            data-value="<?php echo $ex['discount_value']; ?>">
                            <?php echo $ex['name']; ?> 
                            (<?php echo $ex['discount_type'] == 'Percent' ? $ex['discount_value'] . '%' : format_currency($ex['discount_value']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Yêu cầu sẽ được gửi đến Ban Giám hiệu/Kế toán để duyệt</div>
            </div>
            
            <hr>
            <div class="d-flex justify-content-between">
                <a href="index.php?controller=exemption&action=requests" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Gửi yêu cầu
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
