<?php
$page_title = 'Quản lý Thanh toán';
include __DIR__ . '/../layout/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-cash-register"></i> Danh sách thanh toán</h5>
        <?php if (in_array($_SESSION['role_name'], ['Admin', 'Accountant'])): ?>
            <a href="<?php echo app_url("index.php"); ?>?controller=payment&action=create" class="btn btn-success">
                <i class="fas fa-plus"></i> Thanh toán mới
            </a>
        <?php endif; ?>
    </div>

    <div class="card-body">
        <!-- Bộ lọc -->
        <form method="GET" action="<?php echo app_url("index.php"); ?>" class="row g-3 mb-4">
            <input type="hidden" name="controller" value="payment">
            <input type="hidden" name="action" value="index">

            <div class="col-md-2">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Mã phiếu..."
                        value="<?php echo htmlspecialchars($search ?? ''); ?>">
                </div>
            </div>

            <?php if (isset($classes)): ?>
                <div class="col-md-2">
                    <select name="class_id" class="form-select">
                        <option value="">-- Lớp --</option>
                        <?php foreach ($classes as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo (isset($class_id) && !is_array($class_id) && $class_id == $c['id']) ? 'selected' : ''; ?>>
                                <?php echo $c['class_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <?php if (isset($feeTypes)): ?>
                <div class="col-md-2">
                    <select name="fee_type_id" class="form-select">
                        <option value="">-- Khoản thu --</option>
                        <?php foreach ($feeTypes as $ft): ?>
                            <option value="<?php echo $ft['id']; ?>" <?php echo (isset($fee_type_id) && $fee_type_id == $ft['id']) ? 'selected' : ''; ?>>
                                <?php echo $ft['fee_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="col-md-2">
                <input type="date" name="from_date" class="form-control" placeholder="Từ ngày"
                    value="<?php echo $from_date ?? ''; ?>">
            </div>

            <div class="col-md-2">
                <input type="date" name="to_date" class="form-control" placeholder="Đến ngày"
                    value="<?php echo $to_date ?? ''; ?>">
            </div>

            <div class="col-md-2">
                <div class="d-flex gap-2 w-100">
                    <button type="submit" class="btn btn-primary flex-grow-1" title="Tìm kiếm">
                        <i class="fas fa-filter"></i>
                    </button>
                    <a href="<?php echo app_url("index.php"); ?>?controller=payment&action=index" class="btn btn-secondary"
                        title="Reset">
                        <i class="fas fa-sync"></i>
                    </a>
                    <?php if (in_array($_SESSION['role_name'], ['Admin', 'Accountant', 'Teacher'])): ?>
                        <a href="<?php echo app_url("index.php"); ?>?controller=payment&action=export&search=<?php echo urlencode($search ?? ''); ?>&class_id=<?php echo (isset($class_id) && is_array($class_id)) ? '' : ($class_id ?? ''); ?>&fee_type_id=<?php echo $fee_type_id ?? ''; ?>&from_date=<?php echo $from_date ?? ''; ?>&to_date=<?php echo $to_date ?? ''; ?>&student_id=<?php echo urlencode($_GET['student_id'] ?? ''); ?>"
                            class="btn btn-success" title="Xuất Excel">
                            <i class="fas fa-file-excel"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <!-- Bảng danh sách -->
        <?php if (!empty($payments)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>STT</th>
                            <th>Mã phiếu</th>
                            <th>Học sinh</th>
                            <th>Lớp</th>
                            <th>Khoản thu</th>
                            <th>Số tiền</th>
                            <th>Ngày đóng</th>
                            <th>Phương thức</th>
                            <th>Người thu</th>
                            <?php if (strcasecmp(trim($_SESSION['role_name']), 'Teacher') !== 0): ?>
                                <th class="text-center no-print">Thao tác</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stt = ($pagination['current_page'] - 1) * $pagination['per_page'] + 1;
                        $total = 0;
                        foreach ($payments as $payment):
                            $total += $payment['amount_paid'];
                            ?>
                            <tr>
                                <td><?php echo $stt++; ?></td>
                                <td><code><?php echo $payment['payment_code']; ?></code></td>
                                <td>
                                    <strong><?php echo $payment['student_name']; ?></strong><br>
                                    <small class="text-muted"><?php echo $payment['student_code']; ?></small>
                                </td>
                                <td><span class="badge bg-primary"><?php echo $payment['class_name']; ?></span></td>
                                <td><?php echo $payment['fee_name']; ?></td>
                                <td class="text-success fw-bold"><?php echo format_currency($payment['amount_paid']); ?></td>
                                <td><?php echo format_date($payment['payment_date']); ?></td>
                                <td><?php echo $payment['payment_method']; ?></td>
                                <td><?php echo $payment['collector_name']; ?></td>
                                <?php if (strcasecmp(trim($_SESSION['role_name']), 'Teacher') !== 0): ?>
                                    <td class="text-center no-print">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?php echo app_url("index.php"); ?>?controller=payment&action=receipt&id=<?php echo $payment['id']; ?>"
                                                class="btn btn-primary" target="_blank" title="In biên lai">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <?php if (in_array($_SESSION['role_name'], ['Admin', 'Accountant'])): ?>
                                                <a href="<?php echo app_url("index.php"); ?>?controller=payment&action=refund&id=<?php echo $payment['id']; ?>"
                                                    class="btn btn-warning" title="Hoàn tiền">
                                                    <i class="fas fa-undo"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($_SESSION['role_name'] == 'Admin'): ?>
                                                <a href="<?php echo app_url("index.php"); ?>?controller=payment&action=delete&id=<?php echo $payment['id']; ?>"
                                                    class="btn btn-danger btn-delete" title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="table-success fw-bold">
                            <td colspan="5" class="text-end">TỔNG CỘNG:</td>
                            <td
                                colspan="<?php echo (strcasecmp(trim($_SESSION['role_name']), 'Teacher') !== 0) ? '5' : '4'; ?>">
                                <?php echo format_currency($total); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            <?php if ($pagination['total_pages'] > 1): ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php if ($pagination['has_prev']): ?>
                            <li class="page-item">
                                <a class="page-link"
                                    href="?controller=payment&action=index&page=<?php echo $pagination['current_page'] - 1; ?>&search=<?php echo $search; ?>&from_date=<?php echo $from_date; ?>&to_date=<?php echo $to_date; ?>&class_id=<?php echo is_array($class_id) ? '' : $class_id; ?>&fee_type_id=<?php echo $fee_type_id; ?>">Trước</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <li class="page-item <?php echo ($i == $pagination['current_page']) ? 'active' : ''; ?>">
                                <a class="page-link"
                                    href="?controller=payment&action=index&page=<?php echo $i; ?>&search=<?php echo $search; ?>&from_date=<?php echo $from_date; ?>&to_date=<?php echo $to_date; ?>&class_id=<?php echo is_array($class_id) ? '' : $class_id; ?>&fee_type_id=<?php echo $fee_type_id; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($pagination['has_next']): ?>
                            <li class="page-item">
                                <a class="page-link"
                                    href="?controller=payment&action=index&page=<?php echo $pagination['current_page'] + 1; ?>&search=<?php echo $search; ?>&from_date=<?php echo $from_date; ?>&to_date=<?php echo $to_date; ?>&class_id=<?php echo is_array($class_id) ? '' : $class_id; ?>&fee_type_id=<?php echo $fee_type_id; ?>">Sau</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

            <div class="text-muted text-center">
                Hiển thị <?php echo count($payments); ?> trong tổng số <?php echo $pagination['total_records']; ?> thanh
                toán
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> Không tìm thấy thanh toán nào!
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
