<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-tasks text-primary"></i> Duyệt miễn giảm</h2>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if (empty($requests)): ?>
            <div class="alert alert-info">Chưa có yêu cầu miễn giảm nào.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Học sinh</th>
                            <th>Chính sách miễn giảm</th>
                            <th>Giá trị</th>
                            <th>Ngày gán</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $req): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($req['student_name']); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($req['student_code']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($req['exemption_name']); ?></td>
                                <td>
                                    <?php if ($req['discount_type'] === 'Percent'): ?>
                                        <span class="badge bg-info text-dark"><?php echo $req['discount_value']; ?>%</span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?php echo number_format($req['discount_value']); ?> đ</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($req['assigned_date'])); ?></td>
                                <td>
                                    <?php
                                    $statusClass = [
                                        'Pending' => 'bg-warning text-dark',
                                        'Approved' => 'bg-success',
                                        'Rejected' => 'bg-danger'
                                    ];
                                    ?>
                                    <span class="badge <?php echo $statusClass[$req['status']] ?? 'bg-secondary'; ?>">
                                        <?php echo $req['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($req['status'] === 'Pending'): ?>
                                        <a href="index.php?controller=exemption&action=approve&student_id=<?php echo $req['student_id']; ?>&exemption_id=<?php echo $req['exemption_id']; ?>"
                                            class="btn btn-sm btn-success" onclick="return confirm('Bạn có chắc chắn duyệt?');">
                                            <i class="fas fa-check"></i> Duyệt
                                        </a>
                                        <a href="index.php?controller=exemption&action=reject&student_id=<?php echo $req['student_id']; ?>&exemption_id=<?php echo $req['exemption_id']; ?>"
                                            class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn từ chối?');">
                                            <i class="fas fa-times"></i> Từ chối
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted"><i class="fas fa-lock"></i> Đã khóa</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="mt-3">
    <a href="index.php?action=dashboard" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại Dashboard
    </a>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
