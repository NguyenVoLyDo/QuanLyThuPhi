<?php
$page_title = 'Quản lý Người dùng';
include __DIR__ . '/../layout/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-users-cog"></i> Danh sách người dùng</h5>
        <a href="<?php echo app_url("index.php"); ?>?controller=user&action=create" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Thêm người dùng
        </a>
    </div>

    <div class="card-body">
        <!-- Bộ lọc -->
        <form method="GET" action="<?php echo app_url("index.php"); ?>" class="row g-3 mb-4">
            <input type="hidden" name="controller" value="user">
            <input type="hidden" name="action" value="index">

            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control"
                        placeholder="Tìm theo tên đăng nhập, họ tên, email..."
                        value="<?php echo htmlspecialchars($search ?? ''); ?>">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                </div>
            </div>
        </form>

        <!-- Bảng danh sách -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Tên đăng nhập</th>
                        <th>Họ và tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email'] ?? ''); ?></td>
                                <td>
                                    <?php
                                    $badge_class = 'bg-secondary';
                                    if ($user['role_name'] == 'Admin')
                                        $badge_class = 'bg-danger';
                                    elseif ($user['role_name'] == 'Accountant')
                                        $badge_class = 'bg-primary';
                                    elseif ($user['role_name'] == 'Teacher')
                                        $badge_class = 'bg-info';
                                    elseif ($user['role_name'] == 'Student')
                                        $badge_class = 'bg-success';
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo $user['role_name']; ?></span>
                                </td>
                                <td>
                                    <?php if ($user['is_active']): ?>
                                        <span class="badge bg-success">Hoạt động</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Khóa</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo app_url("index.php"); ?>?controller=user&action=edit&id=<?php echo $user['id']; ?>"
                                            class="btn btn-warning" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <a href="<?php echo app_url("index.php"); ?>?controller=user&action=delete&id=<?php echo $user['id']; ?>"
                                                class="btn btn-danger btn-delete" title="Xóa"
                                                onclick="return confirm('Bạn có chắc muốn xóa user này?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <a href="<?php echo app_url("index.php"); ?>?controller=user&action=resetPassword&id=<?php echo $user['id']; ?>"
                                                class="btn btn-dark" title="Reset Mật khẩu">
                                                <i class="fas fa-key"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle"></i> Không tìm thấy người dùng nào!
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Phân trang -->
        <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
            <nav class="mt-3">
                <ul class="pagination justify-content-center">
                    <?php if ($pagination['has_prev']): ?>
                        <li class="page-item">
                            <a class="page-link"
                                href="?controller=user&action=index&page=<?php echo $pagination['current_page'] - 1; ?>&search=<?php echo $search; ?>">Trước</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                        <li class="page-item <?php echo ($i == $pagination['current_page']) ? 'active' : ''; ?>">
                            <a class="page-link"
                                href="?controller=user&action=index&page=<?php echo $i; ?>&search=<?php echo $search; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($pagination['has_next']): ?>
                        <li class="page-item">
                            <a class="page-link"
                                href="?controller=user&action=index&page=<?php echo $pagination['current_page'] + 1; ?>&search=<?php echo $search; ?>">Sau</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
