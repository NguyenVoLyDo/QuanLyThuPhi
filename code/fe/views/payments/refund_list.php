<?php
$page_title = 'Lịch sử hoàn tiền';
include __DIR__ . '/../layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4><i class="fas fa-undo"></i> Lịch sử hoàn tiền</h4>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Ngày hoàn</th>
                        <th>Học sinh</th>
                        <th>Khoản thu</th>
                        <th>Mã phiếu gốc</th>
                        <th>Số tiền hoàn</th>
                        <th>Lý do</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($refunds)): ?>
                        <?php foreach ($refunds as $refund): ?>
                            <tr>
                                <td><?php echo format_date($refund['refunded_at']); ?></td>
                                <td><strong><?php echo $refund['student_name']; ?></strong></td>
                                <td><?php echo $refund['fee_name']; ?></td>
                                <td><code><?php echo $refund['payment_code']; ?></code></td>
                                <td class="text-danger fw-bold"><?php echo format_currency($refund['amount']); ?></td>
                                <td><?php echo htmlspecialchars($refund['reason']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Chưa có lịch sử hoàn tiền nào</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($total > $per_page): ?>
        <div class="card-footer bg-white">
            <?php echo render_pagination($pagination, app_url('index.php?controller=payment&action=refunds')); ?>
        </div>
    <?php endif; ?>
</div>

<div class="mt-3">
    <a href="<?php echo app_url('index.php?controller=payment&action=index'); ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại danh sách thanh toán
    </a>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
