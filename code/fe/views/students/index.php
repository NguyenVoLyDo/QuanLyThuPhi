<?php
$page_title = 'Quản lý Học sinh';
include __DIR__ . '/../layout/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-users"></i> Danh sách học sinh</h5>
        <div>
            <?php if (in_array($_SESSION['role_name'], ['Admin', 'Accountant'])): ?>
                <a href="<?php echo app_url("index.php"); ?>?controller=student&action=import" class="btn btn-success me-2">
                    <i class="fas fa-file-excel"></i> Nhập Excel
                </a>
            <?php endif; ?>
            <?php if (in_array($_SESSION['role_name'], ['Admin', 'Accountant', 'Teacher'])): ?>
                <a href="<?php echo app_url("index.php"); ?>?controller=student&action=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Thêm học sinh
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card-body">
        <!-- Bộ lọc tìm kiếm -->
        <form method="GET" action="<?php echo app_url("index.php"); ?>" class="row g-3 mb-4">
            <input type="hidden" name="controller" value="student">
            <input type="hidden" name="action" value="index">

            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control"
                        placeholder="Tìm theo mã HS, họ tên, phụ huynh, SĐT..."
                        value="<?php echo htmlspecialchars($search ?? ''); ?>">
                </div>
            </div>

            <div class="col-md-3">
                <select name="class_id" class="form-select">
                    <option value="">-- Tất cả lớp --</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>" <?php echo (!is_array($class_id) && (string) $class_id === (string) $class['id']) ? 'selected' : ''; ?>>
                            <?php echo $class['class_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
                <?php if (in_array($_SESSION['role_name'], ['Admin', 'Accountant', 'Teacher'])): ?>
                    <a href="<?php echo app_url("index.php"); ?>?controller=student&action=export&search=<?php echo urlencode($search ?? ''); ?>&class_id=<?php echo is_array($class_id) ? '' : ($class_id ?? ''); ?>"
                        class="btn btn-success ms-2">
                        <i class="fas fa-file-excel"></i> Xuất Excel
                    </a>
                <?php endif; ?>
            </div>
        </form>

        <!-- Bảng danh sách -->
        <?php if (!empty($students)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>STT</th>
                            <th>Mã HS</th>
                            <th>Họ và tên</th>
                            <th>Ngày sinh</th>
                            <th>Giới tính</th>
                            <th>Lớp</th>
                            <th>Phụ huynh</th>
                            <th>SĐT</th>
                            <th>Trạng thái</th>
                            <th>Đóng phí</th>
                            <th class="text-center no-print">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stt = ($pagination['current_page'] - 1) * $pagination['per_page'] + 1;
                        foreach ($students as $student):
                            ?>
                            <tr>
                                <td><?php echo $stt++; ?></td>
                                <td><code><?php echo $student['student_code']; ?></code></td>
                                <td>
                                    <strong><?php echo $student['full_name']; ?></strong>
                                </td>
                                <td><?php echo format_date($student['date_of_birth']); ?></td>
                                <td>
                                    <?php if ($student['gender'] == 'Male'): ?>
                                        <i class="fas fa-mars text-primary"></i> Nam
                                    <?php else: ?>
                                        <i class="fas fa-venus text-danger"></i> Nữ
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-primary"><?php echo $student['class_name']; ?></span></td>
                                <td><?php echo $student['parent_name']; ?></td>
                                <td><?php echo $student['parent_phone']; ?></td>
                                <td>
                                    <?php if ($student['is_active']): ?>
                                        <span class="badge bg-success">Đang học</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Nghỉ học</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    if ($student['total_debt'] > 0) {
                                        echo '<span class="badge bg-danger">Nợ: ' . format_currency($student['total_debt']) . '</span>';
                                    } else {
                                        echo '<span class="badge bg-success">Đã hoàn thành</span>';
                                    }
                                    ?>
                                </td>
                                <td class="text-center no-print">
                                    <div class="btn-group btn-group-sm table-actions">
                                        <a href="<?php echo app_url("index.php"); ?>?controller=student&action=view&id=<?php echo $student['id']; ?>"
                                            class="btn btn-info" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (in_array($_SESSION['role_name'], ['Admin', 'Accountant', 'Teacher'])): ?>
                                            <a href="<?php echo app_url("index.php"); ?>?controller=student&action=edit&id=<?php echo $student['id']; ?>"
                                                class="btn btn-warning" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (in_array($_SESSION['role_name'], ['Accountant'])): ?>
                                            <a href="<?php echo app_url("index.php"); ?>?controller=payment&action=create&student_id=<?php echo $student['id']; ?>"
                                                class="btn btn-success" title="Thu phí">
                                                <i class="fas fa-dollar-sign"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($_SESSION['role_name'] == 'Admin'): ?>
                                            <a href="<?php echo app_url("index.php"); ?>?controller=student&action=delete&id=<?php echo $student['id']; ?>"
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

            <!-- Phân trang -->
            <?php if ($pagination['total_pages'] > 1): ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php if ($pagination['has_prev']): ?>
                            <li class="page-item">
                                <a class="page-link"
                                    href="?controller=student&action=index&page=<?php echo $pagination['current_page'] - 1; ?>&search=<?php echo $search; ?>&class_id=<?php echo is_array($class_id) ? '' : $class_id; ?>">
                                    Trước
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <li class="page-item <?php echo ($i == $pagination['current_page']) ? 'active' : ''; ?>">
                                <a class="page-link"
                                    href="?controller=student&action=index&page=<?php echo $i; ?>&search=<?php echo $search; ?>&class_id=<?php echo is_array($class_id) ? '' : $class_id; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($pagination['has_next']): ?>
                            <li class="page-item">
                                <a class="page-link"
                                    href="?controller=student&action=index&page=<?php echo $pagination['current_page'] + 1; ?>&search=<?php echo $search; ?>&class_id=<?php echo is_array($class_id) ? '' : $class_id; ?>">
                                    Sau
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

            <div class="text-muted text-center">
                Hiển thị <?php echo count($students); ?> trong tổng số <?php echo $pagination['total_records']; ?> học sinh
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> Không tìm thấy học sinh nào!
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
