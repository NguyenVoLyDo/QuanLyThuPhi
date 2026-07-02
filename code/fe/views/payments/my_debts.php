<?php
$page_title = 'Khoản thu cần đóng';
include __DIR__ . '/../layout/header.php';
?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-start border-danger border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1 text-danger"><i class="fas fa-exclamation-circle"></i> Khoản thu cần đóng</h5>
                        <p class="text-muted mb-0">Vui lòng hoàn thành các khoản phí dưới đây.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <?php if (!empty($grouped_debts)): ?>
            <?php foreach ($grouped_debts as $year => $semesters): ?>
                <div class="mb-4">
                    <h5 class="text-primary border-bottom pb-2 mb-3">
                        <i class="fas fa-calendar-alt"></i> Năm học: <?php echo htmlspecialchars($year); ?>
                    </h5>

                    <?php foreach ($semesters as $semester => $debts): ?>
                        <div class="card mb-3 border-light shadow-sm">
                            <div class="card-header bg-light">
                                <strong><?php echo htmlspecialchars($semester); ?></strong>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Khoản thu</th>
                                                <th>Phân loại</th>
                                                <th class="text-end">Tiền phải đóng</th>
                                                <th class="text-end">Đã đóng</th>
                                                <th class="text-end">Còn nợ</th>
                                                <th>Hạn đóng</th>
                                                <th class="text-center">Trạng thái</th>
                                                <th class="text-center" width="200">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($debts as $debt): ?>
                                                <tr>
                                                    <td><strong><?php echo htmlspecialchars($debt['fee_name']); ?></strong></td>
                                                    <td><span
                                                            class="badge bg-secondary"><?php echo htmlspecialchars($debt['fee_category']); ?></span>
                                                    </td>
                                                    <td class="text-end"><?php echo format_currency($debt['total_amount']); ?></td>
                                                    <td class="text-end text-success">
                                                        <?php echo format_currency($debt['paid_amount']); ?></td>
                                                    <td class="text-end text-danger fw-bold">
                                                        <?php echo format_currency($debt['remaining_amount']); ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $due_date = $debt['due_date'] ? strtotime($debt['due_date']) : 0;
                                                        $is_overdue = $due_date > 0 && $due_date < time() && $debt['remaining_amount'] > 0;
                                                        ?>
                                                        <span class="<?php echo $is_overdue ? 'text-danger fw-bold' : 'text-muted'; ?>">
                                                            <?php echo $debt['due_date'] ? format_date($debt['due_date']) : 'Không thời hạn'; ?>
                                                            <?php if ($is_overdue): ?>
                                                                <i class="fas fa-exclamation-triangle" title="Quá hạn"></i>
                                                            <?php endif; ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($debt['remaining_amount'] == 0): ?>
                                                            <span class="badge bg-success">Hoàn thành</span>
                                                        <?php elseif ($debt['paid_amount'] > 0): ?>
                                                            <span class="badge bg-warning text-dark">Còn nợ</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Chưa đóng</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($debt['remaining_amount'] > 0): ?>
                                                            <?php if (!empty($debt['has_pending_proof']) && $debt['has_pending_proof'] > 0): ?>
                                                                <button class="btn btn-secondary btn-sm" disabled>
                                                                    <i class="fas fa-clock"></i> Chờ duyệt
                                                                </button>
                                                            <?php else: ?>
                                                                <a href="<?php echo app_url("index.php"); ?>?controller=payment&action=paymentMethod&fee_type_id=<?php echo $debt['fee_type_id']; ?>&amount=<?php echo $debt['remaining_amount']; ?>&fee_name=<?php echo urlencode($debt['fee_name']); ?>"
                                                                    class="btn btn-primary btn-sm">
                                                                    <i class="fas fa-credit-card"></i> Thanh toán
                                                                </a>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <i class="fas fa-check text-success"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="p-3 mb-2 bg-warning-subtle text-warning-emphasis rounded mt-3">
            <i class="fas fa-info-circle"></i> Vui lòng liên hệ Văn phòng nhà trường hoặc chuyển khoản để hoàn tất thanh
            toán.
        </div>

    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
            <h4>Tuyệt vời!</h4>
            <p class="text-muted">Bạn đã hoàn thành tất cả các khoản học phí.</p>
            <a href="<?php echo app_url("index.php"); ?>?controller=payment&action=index" class="btn btn-outline-primary mt-2">
                <i class="fas fa-history"></i> Xem lịch sử thanh toán
            </a>
        </div>
    <?php endif; ?>
</div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
