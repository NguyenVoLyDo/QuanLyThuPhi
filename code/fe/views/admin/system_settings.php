<?php
$page_title = 'Cài đặt hệ thống';
include __DIR__ . '/../layout/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-cog"></i> Cài đặt hệ thống</h5>
    </div>
    <div class="card-body">
        <form action="index.php?controller=admin&action=systemSettings" method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-calendar-alt"></i> Năm học</label>
                        <input type="text" name="academic_year" class="form-control" 
                            value="<?php echo htmlspecialchars($settings['academic_year'] ?? ''); ?>" 
                            placeholder="2025-2026" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-book"></i> Học kỳ hiện tại</label>
                        <select name="semester" class="form-select" required>
                            <option value="HK1" <?php echo ($settings['semester'] ?? '') == 'HK1' ? 'selected' : ''; ?>>Học kỳ 1</option>
                            <option value="HK2" <?php echo ($settings['semester'] ?? '') == 'HK2' ? 'selected' : ''; ?>>Học kỳ 2</option>
                            <option value="Cả năm" <?php echo ($settings['semester'] ?? '') == 'Cả năm' ? 'selected' : ''; ?>>Cả năm</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-school"></i> Tên trường</label>
                <input type="text" name="school_name" class="form-control" 
                    value="<?php echo htmlspecialchars($settings['school_name'] ?? ''); ?>" 
                    placeholder="Trường THPT ABC">
            </div>
            
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-map-marker-alt"></i> Địa chỉ trường</label>
                <textarea name="school_address" class="form-control" rows="2" 
                    placeholder="Số nhà, đường, quận/huyện, tỉnh/thành phố"><?php echo htmlspecialchars($settings['school_address'] ?? ''); ?></textarea>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="index.php?controller=admin&action=backupPage" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu cài đặt
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
