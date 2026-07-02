<?php
$page_title = 'Nhập danh sách khoản thu';
include __DIR__ . '/../layout/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Nhập khoản thu từ Excel (CSV)</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?controller=feetype&action=index" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-file-upload"></i> Upload File CSV</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> Hướng dẫn định dạng file CSV:</h6>
                    <p class="mb-1">Vui lòng chuẩn bị file CSV (UTF-8) với các cột theo thứ tự sau:</p>
                    <ol>
                        <li><strong>Tên khoản thu</strong> (Bắt buộc)</li>
                        <li><strong>Số tiền</strong> (Bắt buộc, VNĐ)</li>
                        <li><strong>Phân loại</strong> (Bắt buộc: Tuition, Meal, Uniform, Activity, Other - hoặc tiếng
                            Việt tương ứng)</li>
                        <li><strong>Năm học</strong> (Bắt buộc, VD: 2023-2024)</li>
                        <li><strong>Học kỳ</strong> (1, 2, hoặc để trống)</li>
                        <li><strong>Bắt buộc</strong> (1: Có, 0: Không)</li>
                        <li><strong>Mô tả</strong> (Tùy chọn)</li>
                    </ol>
                    <p class="mb-0 text-danger small">* Lưu ý: Dòng đầu tiên của file CSV sẽ được bỏ qua (tiêu đề cột).
                    </p>
                </div>

                <form action="index.php?controller=feetype&action=processImport" method="POST"
                    enctype="multipart/form-data">
                    <div class="mb-4">
                        <label for="file" class="form-label">Chọn file CSV:</label>
                        <input class="form-control form-control-lg" type="file" id="file" name="file" accept=".csv"
                            required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-cloud-upload-alt"></i> Tiến hành nhập dữ liệu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
