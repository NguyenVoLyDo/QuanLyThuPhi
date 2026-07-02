<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Quản lý Thu Phí</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .card-stat {
            transition: transform 0.3s;
        }

        .card-stat:hover {
            transform: translateY(-5px);
        }
    </style>
</head>

<body class="bg-light">
    <?php include __DIR__ . '/layout/header.php'; ?>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0 text-primary">
                <i class="fas fa-tachometer-alt"></i> Bảng điều khiển
            </h2>
            <div class="text-muted">
                <i class="fas fa-calendar-alt"></i> Hôm nay: <?php echo date('d/m/Y'); ?>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- STATISTICS SECTION -->
            <?php if ($_SESSION['role_name'] !== 'Student'): ?>
                <div class="col-md-4">
                    <div class="card card-stat border-0 shadow-sm text-white bg-primary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-uppercase mb-1 small opacity-75">Học sinh</h6>
                                    <h2 class="mb-0 font-weight-bold"><?php echo $stats['total_students'] ?? 0; ?></h2>
                                </div>
                                <div class="fs-1 opacity-50"><i class="fas fa-user-graduate"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (in_array($_SESSION['role_name'], ['Accountant'])): ?>
                <div class="col-md-4">
                    <div class="card card-stat border-0 shadow-sm text-white bg-success h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-uppercase mb-1 small opacity-75">Khoản thu</h6>
                                    <h2 class="mb-0 font-weight-bold"><?php echo $stats['total_fee_types'] ?? 0; ?></h2>
                                </div>
                                <div class="fs-1 opacity-50"><i class="fas fa-file-invoice-dollar"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-stat border-0 shadow-sm text-white bg-warning h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-uppercase mb-1 small opacity-75">Doanh thu</h6>
                                    <h2 class="mb-0 font-weight-bold text-dark">
                                        <?php echo format_currency($stats['total_revenue'] ?? 0); ?></h2>
                                </div>
                                <div class="fs-1 opacity-50 text-dark"><i class="fas fa-money-bill-wave"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($stats['unpaid_by_semester']) && in_array($_SESSION['role_name'], ['Accountant', 'Teacher'])): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white text-danger font-weight-bold">
                            <i class="fas fa-exclamation-circle"></i> Cảnh báo: Học sinh nợ học phí
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Năm học</th>
                                            <th>Học kỳ</th>
                                            <th class="text-end">Số lượng nợ</th>
                                            <th class="text-end">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($stats['unpaid_by_semester'] as $stat): ?>
                                            <tr>
                                                <td><?php echo $stat['academic_year']; ?></td>
                                                <td><?php echo $stat['semester']; ?></td>
                                                <td class="text-end fw-bold text-danger"><?php echo $stat['unpaid_count']; ?>
                                                </td>
                                                <td class="text-end">
                                                    <a href="index.php?controller=payment&action=index"
                                                        class="btn btn-sm btn-outline-danger">Xem chi tiết</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- QUICK ACTIONS SECTION -->
        <h5 class="mb-3 text-secondary"><i class="fas fa-bolt"></i> Truy cập nhanh</h5>
        <div class="row g-3">
            <!-- ADMIN & ACCOUNTANT ACTIONS -->
            <?php if (in_array($_SESSION['role_name'], ['Accountant'])): ?>
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-light"><strong>Học sinh & Khoản thu</strong></div>
                        <div class="card-body d-grid gap-2">
                            <a href="index.php?controller=feetype&action=create" class="btn btn-outline-success text-start">
                                <i class="fas fa-file-medical me-2"></i> Thêm khoản thu
                            </a>
                        </div>
                    </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-light"><strong>Giao dịch</strong></div>
                        <div class="card-body d-grid gap-2">
                            <a href="index.php?controller=payment&action=create" class="btn btn-outline-info text-start">
                                <i class="fas fa-cash-register me-2"></i> Thu phí ngay
                            </a>
                            <a href="index.php?controller=payment&action=manageProofs"
                                class="btn btn-outline-warning text-start text-dark">
                                <i class="fas fa-check-double me-2"></i> Duyệt minh chứng
                            </a>
                            <a href="index.php?controller=debt&action=createBatch"
                                class="btn btn-outline-secondary text-start">
                                <i class="fas fa-layer-group me-2"></i> Tạo đợt thu (Gán nợ)
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-light"><strong>Hệ thống</strong></div>
                        <div class="card-body d-grid gap-2">
                            <a href="index.php?controller=report&action=index" class="btn btn-outline-primary text-start">
                                <i class="fas fa-chart-pie me-2"></i> Xem báo cáo
                            </a>

                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ADMIN ACTIONS -->
             <?php if ($_SESSION['role_name'] === 'Admin'): ?>
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-light"><strong>Quản lý Lớp & Học sinh</strong></div>
                        <div class="card-body d-grid gap-2">
                            <a href="index.php?controller=student&action=index" class="btn btn-outline-primary text-start">
                                <i class="fas fa-users me-2"></i> Danh sách học sinh
                            </a>
                            <a href="index.php?controller=student&action=create" class="btn btn-outline-primary text-start">
                                <i class="fas fa-user-plus me-2"></i> Thêm học sinh
                            </a>
                            <a href="index.php?controller=class&action=index" class="btn btn-outline-secondary text-start">
                                <i class="fas fa-chalkboard me-2"></i> Quản lý lớp học
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-light"><strong>Hệ thống</strong></div>
                        <div class="card-body d-grid gap-2">
                             <a href="index.php?controller=user&action=index" class="btn btn-outline-dark text-start">
                                <i class="fas fa-users-cog me-2"></i> Quản lý người dùng
                            </a>
                            <a href="index.php?controller=admin&action=backupPage" class="btn btn-outline-secondary text-start">
                                <i class="fas fa-database me-2"></i> Sao lưu dữ liệu
                            </a>
                             <a href="index.php?controller=auditlog" class="btn btn-outline-warning text-dark text-start">
                                <i class="fas fa-history me-2"></i> Nhật ký hoạt động
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- SETUP ACCOUNTANT ACTIONS -->
            <?php if ($_SESSION['role_name'] === 'Setup_Accountant'): ?>
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-light"><strong>Quản lý Học sinh</strong></div>
                        <div class="card-body d-grid gap-2">
                            <a href="index.php?controller=student&action=index" class="btn btn-outline-primary text-start">
                                <i class="fas fa-users me-2"></i> Danh sách học sinh
                            </a>
                            <a href="index.php?controller=student&action=create" class="btn btn-outline-primary text-start">
                                <i class="fas fa-user-plus me-2"></i> Thêm học sinh
                            </a>
                            <a href="index.php?controller=student&action=import" class="btn btn-outline-success text-start">
                                <i class="fas fa-file-excel me-2"></i> Nhập Excel
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-light"><strong>Cấu hình Khoản thu</strong></div>
                        <div class="card-body d-grid gap-2">
                            <a href="index.php?controller=feetype&action=index" class="btn btn-outline-warning text-start">
                                <i class="fas fa-tags me-2"></i> Quản lý khoản thu
                            </a>
                            <a href="index.php?controller=exemption&action=index" class="btn btn-outline-info text-start">
                                <i class="fas fa-percent me-2"></i> Quản lý miễn giảm
                            </a>
                            <a href="index.php?controller=debt&action=createBatch" class="btn btn-outline-danger text-start">
                                <i class="fas fa-coins me-2"></i> Gán nợ đầu kỳ
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- FINANCE ACCOUNTANT ACTIONS -->
            <?php if ($_SESSION['role_name'] === 'Finance_Accountant'): ?>
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-light"><strong>Thu phí & Thanh toán</strong></div>
                        <div class="card-body d-grid gap-2">
                            <a href="index.php?controller=student&action=index" class="btn btn-outline-primary text-start">
                                <i class="fas fa-users me-2"></i> Tra cứu học sinh
                            </a>
                            <a href="index.php?controller=payment&action=index" class="btn btn-outline-success text-start">
                                <i class="fas fa-money-bill-wave me-2"></i> Thu phí
                            </a>
                            <a href="index.php?controller=payment&action=manageProofs" class="btn btn-outline-warning text-start">
                                <i class="fas fa-check-circle me-2"></i> Duyệt minh chứng
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-light"><strong>Báo cáo & Hoàn tiền</strong></div>
                        <div class="card-body d-grid gap-2">
                            <a href="index.php?controller=report&action=index" class="btn btn-outline-info text-start">
                                <i class="fas fa-chart-bar me-2"></i> Báo cáo doanh thu
                            </a>
                            <a href="index.php?controller=payment&action=refunds" class="btn btn-outline-danger text-start">
                                <i class="fas fa-undo me-2"></i> Hoàn tiền
                            </a>
                            <a href="index.php?controller=user&action=profile" class="btn btn-outline-secondary text-start">
                                <i class="fas fa-user-circle me-2"></i> Hồ sơ cá nhân
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- TEACHER ACTIONS -->
            <?php if ($_SESSION['role_name'] === 'Teacher'): ?>
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-light"><strong>Lớp chủ nhiệm</strong></div>
                        <div class="card-body d-grid gap-2">
                            <a href="index.php?controller=student&action=index&class_id=<?php echo $_SESSION['teacher_class_id'] ?? ''; ?>"
                                class="btn btn-outline-primary text-start">
                                <i class="fas fa-users me-2"></i> Danh sách học sinh
                            </a>
                            <a href="index.php?controller=payment&action=index&class_id=<?php echo $_SESSION['teacher_class_id'] ?? ''; ?>"
                                class="btn btn-outline-success text-start">
                                <i class="fas fa-money-check-alt me-2"></i> Tình hình đóng phí
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-light"><strong>Quản lý miễn giảm</strong></div>
                        <div class="card-body d-grid gap-2">

                            <a href="index.php?controller=user&action=profile" class="btn btn-outline-info text-start">
                                <i class="fas fa-user-circle me-2"></i> Hồ sơ cá nhân
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- STUDENT ACTIONS -->
            <?php if ($_SESSION['role_name'] === 'Student'): ?>
                <?php
                $total_debt = $stats['personal_total_debt'] ?? 0;
                $debts = $stats['personal_debts'] ?? [];
                ?>
                
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm border-<?php echo $total_debt > 0 ? 'danger' : 'success'; ?>">
                        <div class="card-header bg-<?php echo $total_debt > 0 ? 'danger' : 'success'; ?> text-white">
                            <strong><i class="fas fa-wallet"></i> Tình trạng học phí</strong>
                        </div>
                        <div class="card-body text-center">
                            <?php if ($total_debt > 0): ?>
                                <h1 class="display-4 text-danger fw-bold"><?php echo format_currency($total_debt); ?></h1>
                                <p class="text-muted mb-3">Tổng số tiền chưa thanh toán</p>
                                <div class="p-3 mb-2 bg-warning-subtle text-warning-emphasis rounded">
                                    <i class="fas fa-exclamation-triangle"></i> Bạn còn <strong><?php echo count(array_filter($debts, fn($d) => $d['status'] != 'Paid')); ?> khoản phí</strong> chưa đóng
                                </div>
                                <a href="index.php?controller=payment&action=myDebts" class="btn btn-danger btn-lg w-100">
                                    <i class="fas fa-credit-card"></i> Đóng học phí ngay
                                </a>
                            <?php else: ?>
                                <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                                <h4 class="text-success mt-3">Đã hoàn thành!</h4>
                                <p class="text-muted">Bạn đã thanh toán đầy đủ tất cả học phí</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm border-primary">
                        <div class="card-header bg-primary text-white">
                            <strong><i class="fas fa-history"></i> Thông tin & Lịch sử</strong>
                        </div>
                        <div class="card-body d-grid gap-2">
                            <a href="index.php?controller=payment&action=index" class="btn btn-outline-primary text-start">
                                <i class="fas fa-list me-2"></i> Lịch sử thanh toán
                            </a>
                            <a href="index.php?controller=payment&action=uploadProof" class="btn btn-outline-success text-start">
                                <i class="fas fa-cloud-upload-alt me-2"></i> Upload minh chứng CK
                            </a>
                            <a href="index.php?controller=user&action=profile" class="btn btn-outline-info text-start">
                                <i class="fas fa-id-card me-2"></i> Hồ sơ cá nhân
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
