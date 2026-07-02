<?php
$page_title = 'Quản lý Lớp học';
include __DIR__ . '/../layout/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-chalkboard-teacher"></i> Danh sách lớp học</h5>
        <?php if ($_SESSION['role_name'] === 'Admin'): ?>
            <a href="index.php?controller=class&action=create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm lớp
            </a>
        <?php endif; ?>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="5%">STT</th>
                        <th>Tên lớp</th>
                        <th>Khối</th>
                        <th>Giáo viên chủ nhiệm</th>
                        <?php if ($_SESSION['role_name'] === 'Admin'): ?>
                            <th width="15%" class="text-center">Thao tác</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($classes)): ?>
                        <tr>
                            <td colspan="5" class="text-center">Chưa có dữ liệu</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($classes as $i => $class): ?>
                            <tr>
                                <td><?php echo $i + 1; ?></td>
                                <td><?php echo htmlspecialchars($class['class_name']); ?></td>
                                <td><?php echo htmlspecialchars($class['grade_level'] ?? ''); ?></td>
                                <td>
                                    <?php if ($class['teacher_name']): ?>
                                        <span
                                            class="badge bg-success"><?php echo htmlspecialchars($class['teacher_name']); ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Chưa gán</span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($_SESSION['role_name'] === 'Admin'): ?>
                                    <td class="text-center">
                                        <a href="index.php?controller=class&action=edit&id=<?php echo $class['id']; ?>"
                                            class="btn btn-sm btn-warning" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?controller=class&action=delete&id=<?php echo $class['id']; ?>"
                                            class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa?');"
                                            title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
