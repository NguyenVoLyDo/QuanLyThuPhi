<?php
$page_title = 'Quản lý Giáo viên';
include __DIR__ . '/../layout/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-chalkboard-teacher"></i> Danh sách Giáo viên</h5>
        <a href="index.php?controller=teacher&action=create" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Thêm giáo viên mới
        </a>
    </div>

    <div class="card-body">
        <!-- Tìm kiếm -->
        <form method="GET" action="index.php" class="row g-3 mb-4">
            <input type="hidden" name="controller" value="teacher">
            <input type="hidden" name="action" value="index">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control"
                        placeholder="Tìm theo tên hoặc tên đăng nhập..."
                        value="<?php echo htmlspecialchars($search ?? ''); ?>">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50">STT</th>
                        <th>Họ tên</th>
                        <th>Tên đăng nhập</th>
                        <th>Liên hệ</th>
                        <th>Lớp chủ nhiệm</th>
                        <th width="100">Trạng thái</th>
                        <th width="150" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($teachers)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Không tìm thấy giáo viên nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php
                        $stt = $pagination['offset'] + 1;
                        foreach ($teachers as $t):
                            ?>
                            <tr>
                                <td>
                                    <?php echo $stt++; ?>
                                </td>
                                <td>
                                    <div class="fw-bold">
                                        <?php echo htmlspecialchars($t['full_name']); ?>
                                    </div>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($t['email'] ?? ''); ?>
                                    </small>
                                </td>
                                <td><code><?php echo htmlspecialchars($t['username']); ?></code></td>
                                <td>
                                    <?php echo htmlspecialchars($t['phone'] ?? '---'); ?>
                                </td>
                                <td>
                                    <?php if ($t['classes']): ?>
                                        <span class="badge bg-info text-dark">
                                            <?php echo htmlspecialchars($t['classes']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">Chưa có lớp</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($t['is_active']): ?>
                                        <span class="badge bg-success">Hoạt động</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Khóa</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="index.php?controller=teacher&action=edit&id=<?php echo $t['id']; ?>"
                                            class="btn btn-sm btn-outline-primary" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?controller=teacher&action=delete&id=<?php echo $t['id']; ?>"
                                            class="btn btn-sm btn-outline-danger" title="Xóa"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa giáo viên này?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Phân trang -->
        <?php if ($pagination['total_pages'] > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                        <li class="page-item <?php echo ($i == $pagination['current_page']) ? 'active' : ''; ?>">
                            <a class="page-link"
                                href="index.php?controller=teacher&action=index&page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
