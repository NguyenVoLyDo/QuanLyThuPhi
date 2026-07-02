<?php
$page_title = 'Sao lưu dữ liệu';
include __DIR__ . '/../layout/header.php';
?>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-database"></i> Sao lưu dữ liệu</h5>
            </div>
            <div class="card-body">
                <div class="p-3 mb-2 bg-info-subtle text-info-emphasis rounded">
                    <i class="fas fa-info-circle"></i> <strong>Lưu ý:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Sao lưu toàn bộ dữ liệu trong hệ thống</li>
                        <li>File backup có định dạng .sql</li>
                        <li>Nên backup thường xuyên để đảm bảo an toàn dữ liệu</li>
                        <li>Lưu file backup ở nơi an toàn</li>
                    </ul>
                </div>
                
                <div class="d-grid gap-2">
                    <a href="index.php?controller=admin&action=backup" class="btn btn-success btn-lg">
                        <i class="fas fa-download"></i> Tải xuống Backup ngay
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="fas fa-clock"></i> Thông tin</h6>
            </div>
            <div class="card-body">
                <p class="small mb-2"><i class="fas fa-calendar"></i> <strong>Ngày:</strong> <?php echo date('d/m/Y'); ?></p>
                <p class="small mb-2"><i class="fas fa-clock"></i> <strong>Giờ:</strong> <?php echo date('H:i:s'); ?></p>
                <p class="small mb-0"><i class="fas fa-user"></i> <strong>Người thực hiện:</strong> <?php echo $_SESSION['full_name']; ?></p>
            </div>
        </div>
        
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-warning">
                <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Hướng dẫn Restore</h6>
            </div>
            <div class="card-body small">
                <p>Để khôi phục dữ liệu:</p>
                <ol class="mb-0">
                    <li>Mở phpMyAdmin</li>
                    <li>Chọn database</li>
                    <li>Tab "Import"</li>
                    <li>Chọn file .sql</li>
                    <li>Click "Go"</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
