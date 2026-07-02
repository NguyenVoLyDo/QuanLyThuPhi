<?php
$page_title = 'Nhập danh sách học sinh';
include __DIR__ . '/../layout/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Nhập danh sách học sinh từ Excel (CSV)</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?controller=student&action=index" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-file-upload"></i> Upload File CSV</h5>
            </div>
            <div class="card-body">
                <div class="p-3 mb-2 bg-info-subtle text-info-emphasis rounded">
                    <h6><i class="fas fa-info-circle"></i> Hướng dẫn định dạng file CSV:</h6>
                    <p class="mb-1">Vui lòng chuẩn bị file CSV (UTF-8) với các cột theo thứ tự sau:</p>
                    <ol>
                        <li><strong>Mã học sinh</strong> (Bắt buộc, Duy nhất)</li>
                        <li><strong>Họ và tên</strong> (Bắt buộc)</li>
                        <li><strong>Ngày sinh</strong> (DD/MM/YYYY, Ví dụ: 15/03/2010)</li>
                        <li><strong>Giới tính</strong> (Nam/Nữ hoặc Male/Female)</li>
                        <li><strong>Tên lớp</strong> (Phải khớp chính xác với tên lớp trong hệ thống)</li>
                        <li><strong>Tên phụ huynh</strong> (Tùy chọn)</li>
                        <li><strong>Số điện thoại PH</strong> (Tùy chọn)</li>
                        <li><strong>Email PH</strong> (Tùy chọn)</li>
                        <li><strong>Địa chỉ</strong> (Tùy chọn)</li>
                    </ol>
                    <p class="mb-2 text-danger small">* Lưu ý: Dòng đầu tiên của file CSV sẽ được bỏ qua (tiêu đề cột).</p>
                    <a href="<?php echo be_url(); ?>/templates/import_students_template.csv" class="btn btn-sm btn-success" download>
                        <i class="fas fa-download"></i> Tải file mẫu
                    </a>
                </div>

                <form action="index.php?controller=student&action=processImport" method="POST"
                    enctype="multipart/form-data">
                    <div class="mb-4">
                        <label for="file" class="form-label">Chọn file CSV:</label>
                        <input class="form-control form-control-lg" type="file" id="file" name="file" accept=".csv"
                            required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-cloud-upload-alt"></i> Tiến hành nhập dữ liệu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
