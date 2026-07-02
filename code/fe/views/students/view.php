<?php
$page_title = 'Chi tiết học sinh - ' . $student['full_name'];
include __DIR__ . '/../layout/header.php';
?>

<div class="row">
    <!-- Thông tin học sinh -->
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-user-circle fa-5x text-primary mb-3"></i>
                <h4><?php echo $student['full_name']; ?></h4>
                <p class="text-muted"><?php echo $student['student_code']; ?></p>

                <hr>

                <div class="text-start">
                    <p class="mb-2"><strong><i class="fas fa-calendar"></i> Ngày sinh:</strong>
                        <?php echo format_date($student['date_of_birth']); ?></p>
                    <p class="mb-2">
                        <strong><i class="fas fa-venus-mars"></i> Giới tính:</strong>
                        <?php echo $student['gender'] == 'Male' ? 'Nam' : ($student['gender'] == 'Female' ? 'Nữ' : 'Khác'); ?>
                    </p>
                    <p class="mb-2"><strong><i class="fas fa-school"></i> Lớp:</strong>
                        <?php echo $student['class_name']; ?></p>
                    <p class="mb-2">
                        <strong><i class="fas fa-circle"></i> Trạng thái:</strong>
                        <?php if ($student['is_active']): ?>
                            <span class="badge bg-success">Đang học</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Nghỉ học</span>
                        <?php endif; ?>
                    </p>
                </div>

                <hr>

                <h6 class="text-start">Thông tin phụ huynh:</h6>
                <div class="text-start">
                    <p class="mb-2"><strong><i class="fas fa-user"></i> Họ tên:</strong>
                        <?php echo $student['parent_name']; ?></p>
                    <p class="mb-2"><strong><i class="fas fa-phone"></i> SĐT:</strong>
                        <?php echo $student['parent_phone']; ?></p>
                    <p class="mb-2"><strong><i class="fas fa-envelope"></i> Email:</strong>
                        <?php echo $student['parent_email'] ?: 'Chưa có'; ?></p>
                    <p class="mb-0"><strong><i class="fas fa-map-marker-alt"></i> Địa chỉ:</strong>
                        <?php echo $student['address'] ?: 'Chưa có'; ?></p>
                </div>

                <hr>

                <h6 class="text-start">Ghi chú (Giáo viên):</h6>
                <div class="p-3 mb-2 bg-warning-subtle text-warning-emphasis rounded text-start">
                    <?php echo !empty($student['notes']) ? nl2br(htmlspecialchars($student['notes'])) : '<em>Chưa có ghi chú</em>'; ?>
                </div>

                <?php if (in_array($_SESSION['role_name'], ['Admin', 'Teacher'])): ?>
                    <div class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                            data-bs-target="#editNotesModal">
                            <i class="fas fa-edit"></i> Sửa ghi chú
                        </button>
                    </div>
                <?php endif; ?>

                <?php if ($_SESSION['role_name'] === 'Admin'): ?>
                    <hr>
                    <div class="d-grid gap-2">
                        <a href="<?php echo app_url("index.php"); ?>?controller=student&action=edit&id=<?php echo $student['id']; ?>"
                            class="btn btn-warning">
                            <i class="fas fa-edit"></i> Chỉnh sửa
                        </a>
                    </div>
                <?php endif; ?>
                
                <?php if (in_array($_SESSION['role_name'], ['Admin', 'Accountant'])): ?>
                    <?php if ($_SESSION['role_name'] === 'Accountant'): ?>
                        <hr>
                    <?php endif; ?>
                    <div class="d-grid gap-2">
                        <a href="<?php echo app_url("index.php"); ?>?controller=payment&action=create&student_id=<?php echo $student['id']; ?>"
                            class="btn btn-success">
                            <i class="fas fa-dollar-sign"></i> Thu phí
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Công nợ và thanh toán -->
    <div class="col-md-8">
        <!-- Công nợ -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> Công nợ học phí</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($debts)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Khoản thu</th>
                                    <th>Loại</th>
                                    <th>Tổng tiền</th>
                                    <th>Đã đóng</th>
                                    <th>Còn nợ</th>
                                    <th>Hạn đóng</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total_debt = 0;
                                foreach ($debts as $debt):
                                    $total_debt += $debt['remaining_amount'];
                                    ?>
                                    <tr>
                                        <td><?php echo $debt['fee_name']; ?></td>
                                        <td><span class="badge bg-info"><?php echo $debt['fee_category']; ?></span></td>
                                        <td><?php echo format_currency($debt['total_amount']); ?></td>
                                        <td class="text-success"><?php echo format_currency($debt['paid_amount']); ?></td>
                                        <td class="text-danger fw-bold">
                                            <?php echo format_currency($debt['remaining_amount']); ?>
                                        </td>
                                        <td><?php echo format_date($debt['due_date']); ?></td>
                                        <td>
                                            <?php if ($debt['status'] == 'Paid'): ?>
                                                <span class="badge bg-success">Đã đóng</span>
                                            <?php elseif ($debt['status'] == 'Partial'): ?>
                                                <span class="badge bg-warning">Đã đóng 1 phần</span>
                                            <?php elseif ($debt['status'] == 'Overdue'): ?>
                                                <span class="badge bg-danger">Quá hạn</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Chưa đóng</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="table-info fw-bold">
                                    <td colspan="4" class="text-end">TỔNG CÔNG NỢ:</td>
                                    <td colspan="3" class="text-danger"><?php echo format_currency($total_debt); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success text-center">
                        <i class="fas fa-check-circle fa-3x mb-2"></i><br>
                        Học sinh đã hoàn thành tất cả khoản phí!
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Lịch sử thanh toán -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-history"></i> Lịch sử thanh toán</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($payments)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã phiếu</th>
                                    <th>Khoản thu</th>
                                    <th>Số tiền</th>
                                    <th>Ngày đóng</th>
                                    <th>Phương thức</th>
                                    <th>Người thu</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td><code><?php echo $payment['payment_code']; ?></code></td>
                                        <td><?php echo $payment['fee_name']; ?></td>
                                        <td class="text-success fw-bold"><?php echo format_currency($payment['amount_paid']); ?>
                                        </td>
                                        <td><?php echo format_date($payment['payment_date']); ?></td>
                                        <td><?php echo $payment['payment_method']; ?></td>
                                        <td><?php echo $payment['collector_name']; ?></td>
                                        <td>
                                            <a href="<?php echo app_url("index.php"); ?>?controller=payment&action=receipt&id=<?php echo $payment['id']; ?>"
                                                class="btn btn-sm btn-primary" target="_blank">
                                                <i class="fas fa-print"></i> In
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-4">Chưa có lịch sử thanh toán nào</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Miễn giảm -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-percentage"></i> Miễn giảm được áp dụng</h5>
                <?php if (in_array($_SESSION['role_name'], ['Admin', 'Accountant'])): ?>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                        data-bs-target="#assignExemptionModal">
                        <i class="fas fa-plus"></i>
                        Gán miễn giảm
                    </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (!empty($studentExemptions)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Tên chính sách</th>
                                    <th>Giảm</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày gán</th>
                                    <?php if (in_array($_SESSION['role_name'], ['Admin', 'Accountant'])): ?>
                                        <th width="100"></th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($studentExemptions as $se): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($se['name']); ?></strong></td>
                                        <td>
                                            <?php
                                            if ($se['discount_type'] === 'Percent') {
                                                echo $se['discount_value'] . '%';
                                            } else {
                                                echo format_currency($se['discount_value']);
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusMap = [
                                                'Pending' => '<span class="badge bg-warning text-dark">Chờ duyệt</span>',
                                                'Approved' => '<span class="badge bg-success">Đã duyệt</span>',
                                                'Rejected' => '<span class="badge bg-danger">Từ chối</span>'
                                            ];
                                            echo $statusMap[$se['status'] ?? 'Approved'] ?? '';
                                            ?>
                                        </td>
                                        <td><?php echo format_date($se['assigned_date']); ?></td>
                                        <?php if (in_array($_SESSION['role_name'], ['Admin', 'Accountant'])): ?>
                                            <td class="text-end">
                                                <a href="<?php echo app_url("index.php"); ?>?controller=exemption&action=revoke&student_id=<?php echo $student['id']; ?>&exemption_id=<?php echo $se['id']; ?>"
                                                    class="text-danger" title="Gỡ bỏ"
                                                    onclick="return confirm('Bạn có chắc muốn gỡ bỏ chính sách này?')">
                                                    <i class="fas fa-times-circle"></i>
                                                </a>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-3 mb-0">Chưa có chính sách miễn giảm nào được áp dụng.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Gán Miễn Giảm -->
<div class="modal fade" id="assignExemptionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo app_url("index.php"); ?>?controller=exemption&action=assign" method="POST">
                <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Gán chính sách miễn giảm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Chọn chính sách</label>
                        <select name="exemption_id" class="form-select" required>
                            <option value="">-- Chọn chính sách --</option>
                            <?php foreach ($allExemptions as $ex): ?>
                                <option value="<?php echo $ex['id']; ?>">
                                    <?php
                                    $val = ($ex['discount_type'] === 'Percent') ? $ex['discount_value'] . '%' : format_currency($ex['discount_value']);
                                    echo htmlspecialchars($ex['name']) . " ($val)";
                                    ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Gán ngay</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="<?php echo app_url("index.php"); ?>?controller=student&action=index" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại danh sách
    </a>
</div>

<!-- Modal Sửa Ghi Chú -->
<div class="modal fade" id="editNotesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo app_url("index.php"); ?>?controller=student&action=update_note" method="POST">
                <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                
                <div class="modal-header">
                    <h5 class="modal-title">Ghi chú về học sinh</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <textarea name="notes" class="form-control" rows="5"
                            placeholder="Nhập ghi chú của giáo viên..."><?php echo htmlspecialchars($student['notes'] ?? ''); ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu ghi chú</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
