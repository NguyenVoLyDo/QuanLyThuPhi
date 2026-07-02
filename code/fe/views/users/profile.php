<?php
$page_title = 'Thông tin cá nhân';
include __DIR__ . '/../layout/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-user-circle"></i> Thông tin cá nhân</h5>
            </div>

            <div class="card-body">
                <?php if ($success = get_flash('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo $success['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error = get_flash('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?php echo $error['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php?controller=user&action=profile">
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Tên đăng nhập:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control"
                                value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Họ tên:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control"
                                value="<?php echo htmlspecialchars($user['full_name']); ?>" disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Vai trò:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control"
                                value="<?php echo htmlspecialchars($_SESSION['role_name'] ?? ''); ?>" disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Email:</label>
                        <div class="col-sm-9">
                            <input type="email" name="email" class="form-control"
                                value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Số điện thoại:</label>
                        <div class="col-sm-9">
                            <input type="text" name="phone" class="form-control"
                                value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                    </div>

                    <?php if ($student): ?>
                        <hr>
                        <h6 class="mb-3 text-secondary">Thông tin học sinh</h6>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Mã học sinh:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control"
                                    value="<?php echo htmlspecialchars($student['student_code']); ?>" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Ngày sinh:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control"
                                    value="<?php echo format_date($student['date_of_birth']); ?>" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Địa chỉ:</label>
                            <div class="col-sm-9">
                                <textarea name="address" class="form-control"
                                    rows="2"><?php echo htmlspecialchars($student['address'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row mt-4">
                        <div class="col-sm-9 offset-sm-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
