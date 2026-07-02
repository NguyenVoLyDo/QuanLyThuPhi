<?php $page_title = 'Reset mật khẩu';
include __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-key"></i> Reset mật khẩu cho user:
                    <?php echo htmlspecialchars($user['username']); ?></h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="new_password" required minlength="6">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="confirm_password" required minlength="6">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?php echo app_url("index.php"); ?>?controller=user&action=index" class="btn btn-secondary">Hủy
                            bỏ</a>
                        <button type="submit" class="btn btn-warning">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
