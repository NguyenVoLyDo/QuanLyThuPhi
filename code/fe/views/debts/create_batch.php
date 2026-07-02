<?php
$page_title = 'Tạo công nợ hàng loạt';
include __DIR__ . '/../layout/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-layer-group text-primary"></i> Gán nợ hàng loạt theo học kỳ</h5>
            </div>
            <div class="card-body">
                <div class="p-3 mb-2 bg-info-subtle text-info-emphasis rounded">
                    <i class="fas fa-info-circle"></i> Chức năng này sẽ gán tất cả các khoản thu của Học kỳ được chọn
                    cho <strong>tất cả học sinh</strong> đang hoạt động.
                    <br>
                    Các học sinh đã có công nợ này rồi sẽ được tự động bỏ qua.
                </div>

                <form action="index.php?controller=debt&action=storeBatch" method="POST">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Năm học</label>
                            <select name="academic_year" class="form-select" required>
                                <option value="">-- Chọn Năm học --</option>
                                <?php foreach ($semesters as $sem): ?>
                                    <option value="<?php echo $sem['academic_year']; ?>">
                                        <?php echo $sem['academic_year']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Học kỳ</label>
                            <select name="semester" class="form-select" required>
                                <option value="">-- Chọn Học kỳ --</option>
                                <option value="1">Học kỳ 1</option>
                                <option value="2">Học kỳ 2</option>
                                <option value="3">Học kỳ Hè</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Lớp học (Tùy chọn)</label>
                            <select name="class_id" class="form-select">
                                <option value="">-- Tất cả học sinh --</option>
                                <?php if (!empty($classes)): ?>
                                    <?php foreach ($classes as $class): ?>
                                        <option value="<?php echo $class['id']; ?>">
                                            <?php echo $class['class_name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="form-text">Để trống để gán cho toàn trường</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Khoản thu cụ thể (Tùy chọn)</label>
                            <select name="fee_type_id" class="form-select">
                                <option value="">-- Tất cả khoản thu của Học kỳ --</option>
                                <?php if (!empty($feeTypes)): ?>
                                    <?php foreach ($feeTypes as $ft): ?>
                                        <option value="<?php echo $ft['id']; ?>">
                                            <?php echo $ft['fee_name']; ?> (<?php echo format_currency($ft['amount']); ?>)
                                            - [<?php echo $ft['academic_year']; ?> - HK<?php echo $ft['semester']; ?>]
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="form-text text-primary">Nếu chọn mục này, hệ thống sẽ CHỈ gán khoản thu này, bỏ qua lựa chọn Năm học/Học kỳ ở trên.</div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary"
                            onclick="return confirm('Bạn có chắc chắn muốn gán nợ hàng loạt? Hành động này có thể mất vài giây.');">
                            <i class="fas fa-magic"></i> Bắt đầu Gán nợ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Simple logic to remove duplicate years in select if needed, 
    // or backend should group by year. For now, basic list is fine.
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
