<?php $page_title = 'Thêm chính sách miễn giảm';
include __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-plus-circle"></i> Thêm chính sách miễn giảm mới</h5>
            </div>
            <div class="card-body">
                <form action="index.php?controller=exemption&action=store" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên chính sách <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required
                            placeholder="Ví dụ: Con thương binh, Hộ nghèo...">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="discount_type" class="form-label">Loại giảm <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="discount_type" name="discount_type" required>
                                <option value="Percent">Phần trăm (%)</option>
                                <option value="Amount">Số tiền cố định (VNĐ)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="discount_value" class="form-label">Giá trị giảm <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="discount_value" name="discount_value" required
                                min="0" placeholder="Nhập số % hoặc số tiền">
                            <small class="text-muted" id="discount_help">Nếu chọn %, nhập từ 0-100.</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                            placeholder="Mô tả chi tiết về đối tượng áp dụng..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="index.php?controller=exemption&action=index" class="btn btn-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary">Lưu chính sách</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
