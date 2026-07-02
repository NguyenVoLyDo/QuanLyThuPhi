<?php
$page_title = 'Thêm học sinh mới';
include __DIR__ . '/../layout/header.php';

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-user-plus"></i> Thêm học sinh mới</h5>
    </div>
    
    <div class="card-body">
        <form method="POST" action="<?php echo app_url("index.php"); ?>?controller=student&action=store">
            <div class="row">
                <!-- Thông tin cơ bản -->
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2 mb-3">Thông tin học sinh</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">Mã học sinh <span class="text-danger">*</span></label>
                        <input type="text" name="student_code" class="form-control <?php echo isset($errors['student_code']) ? 'is-invalid' : ''; ?>" 
                               value="<?php echo $old['student_code'] ?? ''; ?>" placeholder="VD: HS2024001" required>
                        <?php if (isset($errors['student_code'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['student_code']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control <?php echo isset($errors['full_name']) ? 'is-invalid' : ''; ?>" 
                               value="<?php echo $old['full_name'] ?? ''; ?>" required>
                        <?php if (isset($errors['full_name'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['full_name']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ngày sinh <span class="text-danger">*</span></label>
                            <input type="date" name="date_of_birth" class="form-control <?php echo isset($errors['date_of_birth']) ? 'is-invalid' : ''; ?>" 
                                   value="<?php echo $old['date_of_birth'] ?? ''; ?>" required>
                            <?php if (isset($errors['date_of_birth'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['date_of_birth']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Giới tính <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select <?php echo isset($errors['gender']) ? 'is-invalid' : ''; ?>" required>
                                <option value="">-- Chọn giới tính --</option>
                                <option value="Male" <?php echo ($old['gender'] ?? '') == 'Male' ? 'selected' : ''; ?>>Nam</option>
                                <option value="Female" <?php echo ($old['gender'] ?? '') == 'Female' ? 'selected' : ''; ?>>Nữ</option>
                                <option value="Other" <?php echo ($old['gender'] ?? '') == 'Other' ? 'selected' : ''; ?>>Khác</option>
                            </select>
                            <?php if (isset($errors['gender'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['gender']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Lớp <span class="text-danger">*</span></label>
                        <select name="class_id" class="form-select <?php echo isset($errors['class_id']) ? 'is-invalid' : ''; ?>" required>
                            <option value="">-- Chọn lớp --</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?php echo $class['id']; ?>" 
                                        <?php echo ($old['class_id'] ?? '') == $class['id'] ? 'selected' : ''; ?>>
                                    <?php echo $class['class_name']; ?> - Khối <?php echo $class['grade_level']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['class_id'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['class_id']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <textarea name="address" class="form-control" rows="3"><?php echo $old['address'] ?? ''; ?></textarea>
                    </div>
                </div>
                
                <!-- Thông tin phụ huynh -->
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2 mb-3">Thông tin phụ huynh</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">Họ tên phụ huynh</label>
                        <input type="text" name="parent_name" class="form-control" 
                               value="<?php echo $old['parent_name'] ?? ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="tel" name="parent_phone" class="form-control <?php echo isset($errors['parent_phone']) ? 'is-invalid' : ''; ?>" 
                               value="<?php echo $old['parent_phone'] ?? ''; ?>" placeholder="0901234567">
                        <?php if (isset($errors['parent_phone'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['parent_phone']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="parent_email" class="form-control" 
                               value="<?php echo $old['parent_email'] ?? ''; ?>" placeholder="email@example.com">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" 
                                   <?php echo (!isset($old['is_active']) || $old['is_active']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_active">
                                Đang học (Kích hoạt)
                            </label>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <strong>Lưu ý:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Mã học sinh phải là duy nhất</li>
                            <li>Các trường có dấu <span class="text-danger">*</span> là bắt buộc</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="text-end">
                <a href="<?php echo app_url("index.php"); ?>?controller=student&action=index" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu học sinh
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
