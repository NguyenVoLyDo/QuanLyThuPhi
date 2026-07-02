<?php
$page_title = 'Quản lý Khoản thu';
include __DIR__ . '/../layout/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> Danh sách khoản thu</h5>
        <?php if (in_array($_SESSION['role_name'], ['Admin', 'Accountant'])): ?>
            <a href="<?php echo app_url("index.php"); ?>?controller=feetype&action=import" class="btn btn-success me-2">
                <i class="fas fa-file-excel"></i> Nhập Excel
            </a>
            <a href="<?php echo app_url("index.php"); ?>?controller=feetype&action=create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm khoản thu
            </a>
        <?php endif; ?>
    </div>

    <div class="card-body">
        <!-- Bộ lọc -->
        <form method="GET" action="<?php echo app_url("index.php"); ?>" class="row g-3 mb-4">
            <input type="hidden" name="controller" value="feetype">
            <input type="hidden" name="action" value="index">

            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm khoản thu..."
                    value="<?php echo htmlspecialchars($search ?? ''); ?>">
            </div>

            <div class="col-md-3">
                <select name="category" class="form-select">
                    <option value="">-- Tất cả loại --</option>
                    <?php foreach ($categories as $key => $cat): ?>
                        <option value="<?php echo $key; ?>" <?php echo ($category ?? '') == $key ? 'selected' : ''; ?>>
                            <?php echo $cat; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <input type="text" name="year" class="form-control" placeholder="Năm học (VD: 2024-2025)"
                    value="<?php echo htmlspecialchars($year ?? ''); ?>">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Tìm
                </button>
            </div>
        </form>

        <!-- Bảng danh sách -->
        <?php if (!empty($fee_types)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>STT</th>
                            <th>Tên khoản thu</th>
                            <th>Loại</th>
                            <th>Số tiền</th>
                            <th>Năm học</th>
                            <th>Học kỳ</th>
                            <th>Bắt buộc</th>
                            <th>Trạng thái</th>
                            <th class="text-center no-print">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stt = ($pagination['current_page'] - 1) * $pagination['per_page'] + 1;
                        foreach ($fee_types as $fee):
                            ?>
                            <tr>
                                <td><?php echo $stt++; ?></td>
                                <td>
                                    <strong><?php echo $fee['fee_name']; ?></strong>
                                    <?php if ($fee['description']): ?>
                                        <br><small class="text-muted"><?php echo $fee['description']; ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $badge_colors = [
                                        'Tuition' => 'primary',
                                        'Meal' => 'success',
                                        'Uniform' => 'info',
                                        'Activity' => 'warning',
                                        'Other' => 'secondary'
                                    ];
                                    $color = $badge_colors[$fee['fee_category']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?php echo $color; ?>">
                                        <?php echo $categories[$fee['fee_category']] ?? $fee['fee_category']; ?>
                                    </span>
                                </td>
                                <td class="fw-bold text-success"><?php echo format_currency($fee['amount']); ?></td>
                                <td><?php echo $fee['academic_year']; ?></td>
                                <td class="text-center">
                                    <?php if ($fee['semester']): ?>
                                        <span class="badge bg-info">HK <?php echo $fee['semester']; ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($fee['is_mandatory']): ?>
                                        <i class="fas fa-check-circle text-success" title="Bắt buộc"></i>
                                    <?php else: ?>
                                        <i class="fas fa-times-circle text-muted" title="Không bắt buộc"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($fee['is_active']): ?>
                                        <span class="badge bg-success">Đang áp dụng</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Tạm dừng</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center no-print">
                                    <div class="btn-group btn-group-sm">
                                        <?php if (in_array($_SESSION['role_name'], ['Admin', 'Accountant'])): ?>
                                            <a href="<?php echo app_url("index.php"); ?>?controller=feetype&action=edit&id=<?php echo $fee['id']; ?>"
                                                class="btn btn-warning" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($_SESSION['role_name'] == 'Admin'): ?>
                                            <a href="<?php echo app_url("index.php"); ?>?controller=feetype&action=delete&id=<?php echo $fee['id']; ?>"
                                                class="btn btn-danger btn-delete" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($pagination['total_pages'] > 1): ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <li class="page-item <?php echo ($i == $pagination['current_page']) ? 'active' : ''; ?>">
                                <a class="page-link"
                                    href="?controller=feetype&action=index&page=<?php echo $i; ?>&search=<?php echo $search; ?>&category=<?php echo $category; ?>&year=<?php echo $year; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> Chưa có khoản thu nào!
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
