<?php
$page_title = 'Thêm khoản thu';
include __DIR__ . '/../layout/header.php';

$old = $_SESSION['old'] ?? [];
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['old'], $_SESSION['errors']);
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> Thêm khoản thu mới</h5>
            </div>
            
            <div class="card-body">
                <form method="POST" action="<?php echo app_url("index.php"); ?>?controller=feetype&action=store">
                    
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Tên khoản thu <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" name="fee_name" class="form-control <?php echo isset($errors['fee_name']) ? 'is-invalid' : ''; ?>" 
                                   value="<?php echo htmlspecialchars($old['fee_name'] ?? ''); ?>" placeholder="Ví dụ: Học phí HK1 2023-2024">
                            <?php if (isset($errors['fee_name'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['fee_name']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Mô tả</label>
                        <div class="col-md-9">
                            <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($old['description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Số tiền <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <input type="number" name="amount" class="form-control <?php echo isset($errors['amount']) ? 'is-invalid' : ''; ?>" 
                                       value="<?php echo htmlspecialchars($old['amount'] ?? ''); ?>" min="0" step="1000">
                                <span class="input-group-text">VNĐ</span>
                                <?php if (isset($errors['amount'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['amount']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Loại khoản thu <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select name="fee_category" class="form-select <?php echo isset($errors['fee_category']) ? 'is-invalid' : ''; ?>">
                                <option value="">-- Chọn loại --</option>
                                <?php foreach ($categories as $key => $label): ?>
                                    <option value="<?php echo $key; ?>" 
                                            <?php echo (isset($old['fee_category']) && $old['fee_category'] == $key) ? 'selected' : ''; ?>>
                                        <?php echo $label; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['fee_category'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['fee_category']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Năm học <span class="text-danger">*</span></label>
                        <div class="col-md-4">
                            <input type="text" name="academic_year" class="form-control <?php echo isset($errors['academic_year']) ? 'is-invalid' : ''; ?>" 
                                   value="<?php echo htmlspecialchars($old['academic_year'] ?? date('Y') . '-' . (date('Y')+1)); ?>" placeholder="2023-2024">
                            <?php if (isset($errors['academic_year'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['academic_year']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <label class="col-md-2 col-form-label text-end">Học kỳ</label>
                        <div class="col-md-3">
                            <select name="semester" class="form-select">
                                <option value="">-- Tất cả --</option>
                                <option value="1" <?php echo (isset($old['semester']) && $old['semester'] == '1') ? 'selected' : ''; ?>>Học kỳ I</option>
                                <option value="2" <?php echo (isset($old['semester']) && $old['semester'] == '2') ? 'selected' : ''; ?>>Học kỳ II</option>
                                <option value="Summer" <?php echo (isset($old['semester']) && $old['semester'] == 'Summer') ? 'selected' : ''; ?>>Học kỳ Hè</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Thời gian áp dụng</label>
                        <div class="col-md-4">
                            <input type="date" name="start_date" class="form-control" 
                                   value="<?php echo htmlspecialchars($old['start_date'] ?? ''); ?>" placeholder="Từ ngày">
                            <div class="form-text">Ngày bắt đầu (Tùy chọn)</div>
                        </div>
                        <div class="col-md-5">
                            <input type="date" name="end_date" class="form-control" 
                                   value="<?php echo htmlspecialchars($old['end_date'] ?? ''); ?>" placeholder="Đến ngày">
                            <div class="form-text">Ngày kết thúc (Tùy chọn)</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Tùy chọn</label>
                        <div class="col-md-9">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="is_mandatory" id="isMandatory" value="1"
                                       <?php echo (isset($old['is_mandatory']) || !isset($old)) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="isMandatory">Đây là khoản thu bắt buộc</label>
                            </div>
                            
                            <div class="mt-3">
                                <label class="form-label">Trạng thái:</label>
                                <select name="status" class="form-select w-auto d-inline-block ms-2">
                                    <option value="Active" <?php echo (isset($old['status']) && $old['status'] == 'Active') ? 'selected' : ''; ?>>Hoạt động</option>
                                    <option value="Inactive" <?php echo (isset($old['status']) && $old['status'] == 'Inactive') ? 'selected' : ''; ?>>Ngừng thu</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="alert alert-info">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="create_debt" id="createDebt" value="1">
                            <label class="form-check-label fw-bold" for="createDebt">
                                Tạo công nợ cho tất cả học sinh đang hoạt động ngay lập tức?
                            </label>
                            <div class="form-text text-dark">
                                Nếu chọn, hệ thống sẽ tự động thêm khoản nợ này cho tất cả học sinh có trạng thái "Active".
                            </div>
                        </div>
                        
                        <div class="mt-3 ps-4" id="dueDateGroup" style="display: none;">
                            <label class="form-label">Hạn đóng:</label>
                            <input type="date" name="due_date" class="form-control w-auto d-inline-block">
                        </div>
                    </div>
                    
                    <div class="text-end">
                        <a href="<?php echo app_url("index.php"); ?>?controller=feetype&action=index" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu khoản thu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const createDebtCheckbox = document.getElementById('createDebt');
        const dueDateGroup = document.getElementById('dueDateGroup');
        
        createDebtCheckbox.addEventListener('change', function() {
            dueDateGroup.style.display = this.checked ? 'block' : 'none';
        });
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
