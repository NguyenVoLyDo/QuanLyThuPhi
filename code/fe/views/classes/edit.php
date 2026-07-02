<?php
$page_title = 'Sửa Lớp học';
include __DIR__ . '/../layout/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-edit"></i> Sửa lớp học</h5>
    </div>
    
    <div class="card-body">
        <form method="POST" action="index.php?controller=class&action=update">
            <input type="hidden" name="id" value="<?php echo $class['id']; ?>">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tên lớp <span class="text-danger">*</span></label>
                    <input type="text" name="class_name" class="form-control" required value="<?php echo htmlspecialchars($class['class_name']); ?>">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Khối <span class="text-danger">*</span></label>
                    <select name="grade_level" class="form-select" required>
                        <option value="">-- Chọn khối --</option>
                        <option value="10" <?php echo $class['grade_level'] == 10 ? 'selected' : ''; ?>>10</option>
                        <option value="11" <?php echo $class['grade_level'] == 11 ? 'selected' : ''; ?>>11</option>
                        <option value="12" <?php echo $class['grade_level'] == 12 ? 'selected' : ''; ?>>12</option>
                    </select>
                </div>
                
                <div class="col-md-12 mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($class['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Giáo viên chủ nhiệm</label>
                    <select name="teacher_id" class="form-select">
                        <option value="">-- Chưa gán --</option>
                        <?php foreach ($teachers as $teacher): ?>
                            <option value="<?php echo $teacher['id']; ?>" <?php echo $class['teacher_id'] == $teacher['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($teacher['full_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="text-end">
                <a href="index.php?controller=class&action=index" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
