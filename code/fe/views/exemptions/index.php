<?php
$page_title = 'Quản lý miễn giảm';
include __DIR__ . '/../layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4><i class="fas fa-percentage"></i> Chính sách miễn giảm</h4>
    <?php if (in_array($_SESSION['role_name'], ['Admin', 'Accountant'])): ?>
        <a href="<?php echo app_url("index.php"); ?>?controller=exemption&action=create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm chính sách
        </a>
    <?php endif; ?>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo app_url("index.php"); ?>" class="row g-3">
            <input type="hidden" name="controller" value="exemption">
            <input type="hidden" name="action" value="index">

            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tên chính sách..."
                        value="<?php echo htmlspecialchars($search); ?>">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">Tìm kiếm</button>
            </div>
        </form>
    </div>
</div>

<?php echo get_flash('success'); ?>
<?php echo get_flash('error'); ?>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Tên chính sách</th>
                        <th>Loại giảm</th>
                        <th>Giá trị</th>
                        <th>Mô tả</th>
                        <th width="150" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($exemptions)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Không có dữ liệu nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($exemptions as $index => $ex): ?>
                            <tr>
                                <td><?php echo ($pagination['current_page'] - 1) * $pagination['per_page'] + $index + 1; ?></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($ex['name']); ?></td>
                                <td>
                                    <?php if ($ex['discount_type'] === 'Percent'): ?>
                                        <span class="badge bg-info text-dark">Phần trăm (%)</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Số tiền (VNĐ)</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold text-primary">
                                    <?php
                                    if ($ex['discount_type'] === 'Percent') {
                                        echo $ex['discount_value'] . '%';
                                    } else {
                                        echo format_currency($ex['discount_value']);
                                    }
                                    ?>
                                </td>
                                <td><small><?php echo htmlspecialchars($ex['description']); ?></small></td>
                                <td class="text-center">
                                    <?php if (in_array($_SESSION['role_name'], ['Admin', 'Accountant'])): ?>
                                        <div class="btn-group">
                                            <a href="<?php echo app_url("index.php"); ?>?controller=exemption&action=edit&id=<?php echo $ex['id']; ?>"
                                                class="btn btn-sm btn-outline-primary" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?php echo app_url("index.php"); ?>?controller=exemption&action=delete&id=<?php echo $ex['id']; ?>"
                                                class="btn btn-sm btn-outline-danger" title="Xóa"
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa chính sách này?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted"><i class="fas fa-lock"></i></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($pagination['total_pages'] > 1): ?>
        <div class="card-footer bg-white">
            <nav>
                <ul class="pagination justify-content-center mb-0">
                    <?php if ($pagination['current_page'] > 1): ?>
                        <li class="page-item">
                            <a class="page-link"
                                href="?controller=exemption&action=index&page=<?php echo $pagination['current_page'] - 1; ?>&search=<?php echo $search; ?>">Trước</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                        <li class="page-item <?php echo ($i == $pagination['current_page']) ? 'active' : ''; ?>">
                            <a class="page-link"
                                href="?controller=exemption&action=index&page=<?php echo $i; ?>&search=<?php echo $search; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                        <li class="page-item">
                            <a class="page-link"
                                href="?controller=exemption&action=index&page=<?php echo $pagination['current_page'] + 1; ?>&search=<?php echo $search; ?>">Sau</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
