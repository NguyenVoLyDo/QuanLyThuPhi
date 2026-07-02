<?php $page_title = 'Sửa chính sách miễn giảm';
include __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-edit"></i> Sửa chính sách miễn giảm</h5>
            </div>
            <div class="card-body">
                <form action="index.php?controller=exemption&action=update" method="POST">
                    <input type="hidden" name="id" value="<?php echo $exemption['id']; ?>">

                    <div class="mb-3">
                        <label for="name" class="form-label">Tên chính sách <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required
                            value="<?php echo htmlspecialchars($exemption['name']); ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="discount_type" class="form-label">Loại giảm <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="discount_type" name="discount_type" required>
                                <option value="Percent" <?php echo $exemption['discount_type'] == 'Percent' ? 'selected' : ''; ?>>Phần trăm (%)</option>
                                <option value="Amount" <?php echo $exemption['discount_type'] == 'Amount' ? 'selected' : ''; ?>>Số tiền cố định (VNĐ)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="discount_value" class="form-label">Giá trị giảm <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="discount_value" name="discount_value" required
                                min="0" value="<?php echo $exemption['discount_value']; ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="description"
                            rows="3"><?php echo htmlspecialchars($exemption['description']); ?></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="index.php?controller=exemption&action=index" class="btn btn-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary">Cập nhật chính sách</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
