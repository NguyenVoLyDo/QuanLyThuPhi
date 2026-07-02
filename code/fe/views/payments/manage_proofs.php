<?php
$page_title = 'Duyệt Minh Chứng Chuyển Khoản';
include __DIR__ . '/../layout/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-tasks"></i> Quản lý minh chứng chuyển khoản</h5>
        <div>
            <a href="?controller=payment&action=manageProofs&status=Pending"
                class="btn btn-outline-warning <?php echo ($status == 'Pending') ? 'active' : ''; ?>">Chờ duyệt</a>
            <a href="?controller=payment&action=manageProofs&status=Approved"
                class="btn btn-outline-success <?php echo ($status == 'Approved') ? 'active' : ''; ?>">Đã duyệt</a>
            <a href="?controller=payment&action=manageProofs&status=Rejected"
                class="btn btn-outline-danger <?php echo ($status == 'Rejected') ? 'active' : ''; ?>">Đã từ chối</a>
        </div>
    </div>

    <div class="card-body">
        <?php if (!empty($proofs)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Học sinh</th>
                            <th>Khoản thu</th>
                            <th>Số tiền</th>
                            <th>Ngày gửi</th>
                            <th>Minh chứng</th>
                            <th>Trạng thái</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($proofs as $proof): ?>
                            <tr>
                                <td>#<?php echo $proof['id']; ?></td>
                                <td>
                                    <strong><?php echo $proof['student_name']; ?></strong><br>
                                    <small class="text-muted"><?php echo $proof['student_code']; ?></small>
                                </td>
                                <td><?php echo $proof['fee_name']; ?></td>
                                <td class="text-danger fw-bold"><?php echo format_currency($proof['amount']); ?></td>
                                <td><?php echo format_date($proof['created_at']); ?></td>
                                <td>
                                    <a href="<?php echo be_url(); ?>/<?php echo $proof['image_path']; ?>" target="_blank"
                                        class="btn btn-sm btn-info text-white">
                                        <i class="fas fa-eye"></i> Xem ảnh
                                    </a>
                                </td>
                                <td>
                                    <?php if ($proof['status'] == 'Pending'): ?>
                                        <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                    <?php elseif ($proof['status'] == 'Approved'): ?>
                                        <span class="badge bg-success">Đã duyệt</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Đã từ chối</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($proof['status'] == 'Pending'): ?>
                                        <div class="btn-group">
                                            <a href="<?php echo app_url("index.php"); ?>?controller=payment&action=approveProof&id=<?php echo $proof['id']; ?>"
                                                class="btn btn-success btn-sm"
                                                onclick="return confirm('Xác nhận duyệt minh chứng và tạo phiếu thu?');">
                                                <i class="fas fa-check"></i> Duyệt
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#rejectModal<?php echo $proof['id']; ?>">
                                                <i class="fas fa-times"></i> Từ chối
                                            </button>
                                        </div>

                                        <!-- Modal Từ chối -->
                                        <div class="modal fade" id="rejectModal<?php echo $proof['id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="<?php echo app_url("index.php"); ?>?controller=payment&action=rejectProof"
                                                        method="POST">
                                                        <input type="hidden" name="id" value="<?php echo $proof['id']; ?>">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Từ chối minh chứng #<?php echo $proof['id']; ?>
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body text-start">
                                                            <div class="mb-3">
                                                                <label class="form-label">Lý do từ chối:</label>
                                                                <textarea name="reason" class="form-control" rows="3" required
                                                                    placeholder="Nhập lý do..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Hủy</button>
                                                            <button type="submit" class="btn btn-danger">Xác nhận từ chối</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <small class="text-muted"><?php echo $proof['admin_note']; ?></small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> Không có minh chứng nào
                <?php echo ($status == 'Pending') ? 'chờ duyệt' : ''; ?>.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
