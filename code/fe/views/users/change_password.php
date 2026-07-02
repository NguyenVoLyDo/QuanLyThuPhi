<?php
$page_title = 'Đổi mật khẩu';
include __DIR__ . '/../layout/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-key"></i> Đổi mật khẩu</h5>
            </div>
            
            <div class="card-body">
                <form method="POST" action="<?php echo app_url("index.php"); ?>?controller=user&action=changePassword">
                    
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" name="new_password" class="form-control" required minlength="6">
                        <div class="form-text">Mật khẩu phải có ít nhất 6 ký tự.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" name="confirm_password" class="form-control" required minlength="6">
                    </div>
                    
                    <hr>
                    
                    <div class="text-end">
                        <a href="<?php echo app_url("index.php"); ?>?action=dashboard" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Về Dashboard
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Đổi mật khẩu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
