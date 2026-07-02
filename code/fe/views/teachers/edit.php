<?php
$page_title = 'Cập nhật giáo viên';
include __DIR__ . '/../layout/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-edit"></i> Cập nhật thông tin giáo viên</h5>
            </div>
            <div class="card-body">
                <form action="index.php?controller=teacher&action=update" method="POST">
                    <input type="hidden" name="id" value="<?php echo $teacher['id']; ?>">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tên đăng nhập <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($teacher['username']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Mật khẩu mới (Để trống nếu không đổi)</label>
                            <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($teacher['full_name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($teacher['email'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($teacher['phone'] ?? ''); ?>">
                        </div>
                        
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Phân công lớp chủ nhiệm</label>
                            <div class="row">
                                <?php foreach ($classes as $c): ?>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="class_ids[]" value="<?php echo $c['id']; ?>" id="class_<?php echo $c['id']; ?>"
                                                <?php echo (in_array($c['id'], $assigned_classes)) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="class_<?php echo $c['id']; ?>">
                                                Lớp <?php echo htmlspecialchars($c['class_name']); ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Trạng thái</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?php echo $teacher['is_active'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_active">Hoạt động</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                        <a href="index.php?controller=teacher&action=index" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Cập nhật ngay
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
