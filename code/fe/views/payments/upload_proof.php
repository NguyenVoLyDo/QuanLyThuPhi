<?php
$page_title = 'Upload Minh Chứng Thanh Toán';
include __DIR__ . '/../layout/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-upload"></i> Gửi minh chứng thanh toán</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo app_url("index.php"); ?>?controller=payment&action=storeProof" method="POST"
                    enctype="multipart/form-data">
                    <input type="hidden" name="fee_type_id" value="<?php echo htmlspecialchars($fee_type_id); ?>">
                    <input type="hidden" name="amount" value="<?php echo htmlspecialchars($amount); ?>">

                    <div class="mb-3">
                        <label class="form-label">Khoản thu:</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($fee_name); ?>"
                            readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số tiền cần đóng:</label>
                        <input type="text" class="form-control fw-bold text-danger"
                            value="<?php echo format_currency($amount); ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ảnh minh chứng (Chuyển khoản thành công):</label>
                        <input type="file" name="proof_image" class="form-control" accept="image/*,.pdf" required>
                        <small class="text-muted">Chấp nhận file ảnh (JPG, PNG) hoặc PDF. Tối đa 5MB.</small>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Gửi ngay
                        </button>
                        <a href="<?php echo app_url("index.php"); ?>?controller=payment&action=myDebts"
                            class="btn btn-secondary">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="p-3 mb-2 bg-info-subtle text-info-emphasis rounded mt-3">
            <i class="fas fa-info-circle"></i> <strong>Hướng dẫn:</strong><br>
            1. Chuyển khoản theo số tài khoản nhà trường.<br>
            2. Chụp ảnh màn hình giao dịch thành công.<br>
            3. Upload ảnh vào form trên.<br>
            4. Kế toán sẽ xác nhận và cập nhật trạng thái "Đã đóng" cho bạn.
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
