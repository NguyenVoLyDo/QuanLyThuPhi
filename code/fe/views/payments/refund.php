<?php
$page_title = 'Hoàn tiền - ' . $payment['payment_code'];
include __DIR__ . '/../layout/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-danger"><i class="fas fa-undo"></i> Hoàn tiền học phí</h5>
            </div>
            <div class="card-body">
                <div class="p-3 mb-2 bg-info-subtle text-info-emphasis rounded">
                    <h6>Thông tin thanh toán gốc:</h6>
                    <ul class="mb-0">
                        <li><strong>Mã phiếu:</strong> <?php echo $payment['payment_code']; ?></li>
                        <li><strong>Học sinh:</strong> <?php echo $payment['student_name']; ?></li>
                        <li><strong>Khoản thu:</strong> <?php echo $payment['fee_name']; ?></li>
                        <li><strong>Ngày đóng:</strong> <?php echo format_date($payment['payment_date']); ?></li>
                        <li><strong>Số tiền đã đóng:</strong> <span class="fw-bold text-success"><?php echo format_currency($payment['amount_paid']); ?></span></li>
                    </ul>
                </div>

                <form action="<?php echo app_url("index.php"); ?>?controller=payment&action=refund" method="POST">
                    <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Số tiền hoàn <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="amount" class="form-control" 
                                   value="<?php echo $payment['amount_paid']; ?>" 
                                   max="<?php echo $payment['amount_paid']; ?>" 
                                   min="1000" step="1000" required>
                            <span class="input-group-text">VNĐ</span>
                        </div>
                        <small class="text-muted">Mặc định hoàn toàn bộ số tiền đã đóng.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ngày hoàn tiền <span class="text-danger">*</span></label>
                        <input type="date" name="refund_date" class="form-control" 
                               value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lý do hoàn tiền <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required placeholder="VD: Đóng thừa, rút hồ sơ..."></textarea>
                    </div>

                    <div class="p-3 mb-2 bg-warning-subtle text-warning-emphasis rounded border border-warning">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Cảnh báo:</strong>
                        <p class="mb-0 small">Sau khi xác nhận, số tiền này sẽ được trừ vào số tiền đã đóng của học sinh và tăng công nợ trở lại.</p>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo app_url("index.php"); ?>?controller=payment&action=index" class="btn btn-secondary me-md-2">Hủy</a>
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Xác nhận hoàn tiền?')">Xác nhận hoàn tiền</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
