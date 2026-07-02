<?php
$page_title = 'Thêm giáo viên mới';
include __DIR__ . '/../layout/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-user-plus"></i> Thêm giáo viên mới</h5>
            </div>
            <div class="card-body">
                <form action="index.php?controller=teacher&action=store" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tên đăng nhập <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" required
                                placeholder="Ví dụ: gv_hung">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required
                                placeholder="Ít nhất 6 ký tự">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" required
                                placeholder="Nhập họ tên đầy đủ">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="example@school.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" placeholder="Nhập số điện thoại">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Phân công lớp chủ nhiệm</label>
                            <div class="row">
                                <?php foreach ($classes as $c): ?>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="class_ids[]"
                                                value="<?php echo $c['id']; ?>" id="class_<?php echo $c['id']; ?>">
                                            <label class="form-check-label" for="class_<?php echo $c['id']; ?>">
                                                Lớp
                                                <?php echo htmlspecialchars($c['class_name']); ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="form-text mt-0">Một giáo viên có thể chủ nhiệm nhiều lớp (nếu cần).</div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                        <a href="index.php?controller=teacher&action=index" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu thông tin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
