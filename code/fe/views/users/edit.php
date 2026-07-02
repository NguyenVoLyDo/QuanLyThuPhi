<?php
$page_title = 'Chỉnh sửa người dùng';
include __DIR__ . '/../layout/header.php';

$old = $_SESSION['old'] ?? [];
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['old'], $_SESSION['errors']);

// Use user data if no old data passed back
if (empty($old)) {
    $old = $user;
    // Password should be empty
    $old['password'] = '';
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-user-edit"></i> Chỉnh sửa người dùng</h5>
            </div>
            
            <div class="card-body">
                <form method="POST" action="<?php echo app_url("index.php"); ?>?controller=user&action=update">
                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                    
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" name="username" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" 
                                   value="<?php echo htmlspecialchars($old['username'] ?? ''); ?>">
                            <?php if (isset($errors['username'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['username']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Mật khẩu mới</label>
                        <div class="col-md-9">
                            <input type="password" name="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>"
                                   placeholder="Để trống nếu không đổi mật khẩu">
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Họ và tên <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" name="full_name" class="form-control <?php echo isset($errors['full_name']) ? 'is-invalid' : ''; ?>" 
                                   value="<?php echo htmlspecialchars($old['full_name'] ?? ''); ?>">
                            <?php if (isset($errors['full_name'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['full_name']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Email</label>
                        <div class="col-md-9">
                            <input type="email" name="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                   value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>">
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Số điện thoại</label>
                        <div class="col-md-9">
                            <input type="text" name="phone" class="form-control" 
                                   value="<?php echo htmlspecialchars($old['phone'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Vai trò <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select name="role_id" id="role_select" class="form-select <?php echo isset($errors['role_id']) ? 'is-invalid' : ''; ?>">
                                <option value="">-- Chọn vai trò --</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?php echo $role['id']; ?>" 
                                            <?php echo (isset($old['role_id']) && $old['role_id'] == $role['id']) ? 'selected' : ''; ?>>
                                        <?php echo $role['role_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['role_id'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['role_id']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row mb-3" id="student_select_group" style="display: none;">
                        <label class="col-md-3 col-form-label">Liên kết học sinh</label>
                        <div class="col-md-9">
                            <select name="student_id" class="form-select">
                                <option value="">-- Chọn học sinh (nếu có) --</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?php echo $student['id']; ?>" 
                                            <?php echo (isset($old['student_id']) && $old['student_id'] == $student['id']) ? 'selected' : ''; ?>>
                                        <?php echo $student['student_code'] . ' - ' . $student['full_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Chỉ dành cho tài khoản Phụ huynh/Học sinh để xem thông tin riêng.</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3" id="class_select_group" style="display: none;">
                        <label class="col-md-3 col-form-label">Phân công lớp chủ nhiệm</label>
                        <div class="col-md-9">
                            <select name="class_id" class="form-select">
                                <option value="">-- Chọn lớp chủ nhiệm (nếu có) --</option>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?php echo $class['id']; ?>" 
                                            <?php echo (isset($current_class_id) && $current_class_id == $class['id']) ? 'selected' : ''; ?>>
                                        <?php echo $class['class_name'] . ' (' . ($class['teacher_name'] ? 'GV: ' . $class['teacher_name'] : 'Chưa có GV') . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Chọn lớp để phân công làm giáo viên chủ nhiệm.</div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <label class="col-md-3 col-form-label">Trạng thái</label>
                        <div class="col-md-9">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" 
                                       <?php echo (isset($old['is_active']) && $old['is_active']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="isActive">Kích hoạt</label>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-end">
                        <a href="<?php echo app_url("index.php"); ?>?controller=user&action=index" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role_select');
        const studentGroup = document.getElementById('student_select_group');
        const classGroup = document.getElementById('class_select_group');
        
        function toggleStudentSelect() {
            const selectedText = roleSelect.options[roleSelect.selectedIndex].text;
            if (selectedText === 'Student' || selectedText === 'Parent') {
                studentGroup.style.display = 'flex';
                classGroup.style.display = 'none';
            } else if (selectedText === 'Teacher') {
                studentGroup.style.display = 'none';
                classGroup.style.display = 'flex';
            } else {
                studentGroup.style.display = 'none';
                classGroup.style.display = 'none';
            }
        }
        
        roleSelect.addEventListener('change', toggleStudentSelect);
        toggleStudentSelect(); // Run on load
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
