<?php
$page_title = 'Báo cáo Thống kê';
include __DIR__ . '/../layout/header.php';
?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Báo cáo và Thống kê</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="<?php echo app_url("index.php"); ?>" class="row g-3">
                    <input type="hidden" name="controller" value="report">
                    <input type="hidden" name="action" value="index">

                    <div class="col-md-3">
                        <label class="form-label">Từ ngày:</label>
                        <input type="date" name="from_date" class="form-control" value="<?php echo $from_date; ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Đến ngày:</label>
                        <input type="date" name="to_date" class="form-control" value="<?php echo $to_date; ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tìm học sinh nợ:</label>
                        <input type="text" name="debt_search" class="form-control" placeholder="Tên hoặc mã HS..." value="<?php echo htmlspecialchars($_GET['debt_search'] ?? ''); ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-sync"></i> Lọc dữ liệu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Thống kê tổng quan -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Tổng doanh thu</h6>
                        <h3 class="text-success mb-0"><?php echo format_currency($revenue['total_revenue'] ?? 0); ?>
                        </h3>
                        <small class="text-muted"><?php echo $revenue['total_transactions'] ?? 0; ?> giao dịch</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Debt Overview Here if available, otherwise just keep existing -->
    <div class="col-md-3">
        <div class="card shadow-sm border-start border-danger border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Tổng công nợ</h6>
                        <?php
                        // Calculate total debt from the debt list query
                        $total_debt_summary = 0;
                        if (!empty($students_with_debt)) {
                            foreach ($students_with_debt as $s)
                                $total_debt_summary += $s['total_debt'];
                        }
                        ?>
                        <h3 class="text-danger mb-0"><?php echo format_currency($total_debt_summary); ?></h3>
                        <small class="text-muted"><?php echo count($students_with_debt); ?> học sinh nợ</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm border-start border-warning border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="w-100">
                        <h6 class="text-muted mb-2">Export Báo cáo</h6>
                        <div class="d-flex gap-2">
                            <a href="<?php echo app_url("index.php"); ?>?controller=report&action=exportPayments&from_date=<?php echo $from_date; ?>&to_date=<?php echo $to_date; ?>"
                                class="btn btn-success flex-fill">
                                <i class="fas fa-file-excel"></i> Xuất Excel Thu
                            </a>
                            <a href="<?php echo app_url("index.php"); ?>?controller=report&action=exportDebts"
                                class="btn btn-danger flex-fill">
                                <i class="fas fa-file-excel"></i> Xuất Excel Nợ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Thống kê theo loại phí -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-chart-pie"></i> Doanh thu theo loại phí</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($revenue_by_category)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Loại phí</th>
                                    <th>Khoản thu</th>
                                    <th class="text-end">Số lượng</th>
                                    <th class="text-end">Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($revenue_by_category as $item): ?>
                                    <tr>
                                        <td><span class="badge bg-primary"><?php echo $item['fee_category']; ?></span></td>
                                        <td><?php echo $item['fee_name']; ?></td>
                                        <td class="text-end"><?php echo $item['payment_count']; ?></td>
                                        <td class="text-end text-success fw-bold">
                                            <?php echo format_currency($item['total_amount']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-3">Chưa có dữ liệu</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-credit-card"></i> Theo phương thức thanh toán</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($revenue_by_method)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Phương thức</th>
                                    <th class="text-end">Số lượng</th>
                                    <th class="text-end">Tổng tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($revenue_by_method as $item): ?>
                                    <tr>
                                        <td>
                                            <i
                                                class="fas fa-<?php echo $item['payment_method'] == 'Cash' ? 'money-bill' : 'credit-card'; ?>"></i>
                                            <?php echo $item['payment_method']; ?>
                                        </td>
                                        <td class="text-end"><?php echo $item['payment_count']; ?></td>
                                        <td class="text-end text-success fw-bold">
                                            <?php echo format_currency($item['total_amount']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-3">Chưa có dữ liệu</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<!-- Học sinh còn nợ -->
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-exclamation-triangle text-danger"></i> Danh sách học sinh còn nợ</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($students_with_debt)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>STT</th>
                                    <th>Mã HS</th>
                                    <th>Họ tên</th>
                                    <th>Lớp</th>
                                    <th class="text-end">Tổng nợ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stt = 1;
                                foreach ($students_with_debt as $student):
                                    ?>
                                    <tr>
                                        <td><?php echo $stt++; ?></td>
                                        <td><code><?php echo $student['student_code']; ?></code></td>
                                        <td><?php echo $student['full_name']; ?></td>
                                        <td><span class="badge bg-primary"><?php echo $student['class_name']; ?></span></td>
                                        <td class="text-end text-danger fw-bold">
                                            <?php echo format_currency($student['total_debt']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success text-center">
                        <i class="fas fa-check-circle fa-2x mb-2"></i><br>
                        Tuyệt vời! Không có học sinh nào còn nợ!
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
