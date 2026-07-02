<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Quản lý Thu Phí'; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            margin-bottom: 20px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .075);
            border: none;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, .05);
            font-weight: 600;
        }

        .table th {
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .sidebar,
            .navbar {
                display: none !important;
            }

            .content {
                margin: 0 !important;
                width: 100% !important;
            }
        }
    </style>
</head>

<body>

    <?php if (isset($_SESSION['user_id'])): ?>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 no-print">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?php echo app_url('index.php'); ?>?action=dashboard">
                    <i class="fas fa-graduation-cap"></i> Quản lý Thu Phí
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarMain">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo app_url('index.php'); ?>?action=dashboard">
                                <i class="fas fa-tachometer-alt"></i> Tổng quan
                            </a>
                        </li>

                        <?php
                        $role = $_SESSION['role_name'] ?? '';
                        $is_admin = $role === 'Admin';
                        $is_accountant = $role === 'Accountant';
                        $is_teacher = $role === 'Teacher';
                        $is_student = $role === 'Student';
                        $can_manage = in_array($role, ['Admin', 'Accountant', 'Teacher']);
                        $financial_manage = in_array($role, ['Accountant']);
                        ?>

                        <?php if ($can_manage): ?>
                            <!-- HOC SINH -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-graduate"></i> Danh sách
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="<?php echo app_url('index.php'); ?>?controller=student&action=index">
                                            <i class="fas fa-list"></i> Danh sách học sinh
                                        </a>
                                    </li>
                                    <?php if ($is_admin): ?>
                                        <li>
                                            <a class="dropdown-item" href="<?php echo app_url('index.php'); ?>?controller=class&action=index">
                                                <i class="fas fa-chalkboard"></i> Lớp học
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="<?php echo app_url('index.php'); ?>?controller=teacher&action=index">
                                                <i class="fas fa-chalkboard-teacher"></i> Giáo viên
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>

                            <!-- KHOAN THU -->
                            <?php if ($financial_manage || $is_teacher): ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                        <i class="fas fa-file-invoice-dollar"></i> Khoản thu
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="<?php echo app_url('index.php'); ?>?controller=feetype&action=index">
                                                <i class="fas fa-list-alt"></i> Danh mục khoản thu
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                                href="<?php echo app_url('index.php'); ?>?controller=exemption&action=index">
                                                <i class="fas fa-percentage"></i> Chính sách miễn giảm
                                            </a>
                                        </li>
                                        <?php if ($is_accountant): ?>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                    href="<?php echo app_url('index.php'); ?>?controller=debt&action=createBatch">
                                                    <i class="fas fa-layer-group"></i> Lập đợt thu (Gán nợ)
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </li>
                            <?php endif; ?>

                            <!-- GIAO DICH -->
                            <?php if ($financial_manage || $is_teacher): ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                        <i class="fas fa-exchange-alt"></i> Giao dịch
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="<?php echo app_url('index.php'); ?>?controller=payment&action=index">
                                                <i class="fas fa-money-bill-wave"></i> Thu phí
                                            </a>
                                        </li>
                                        <?php if ($is_accountant): ?>
                                            <li>
                                                <a class="dropdown-item"
                                                    href="<?php echo app_url('index.php'); ?>?controller=payment&action=manageProofs">
                                                    <i class="fas fa-check-double"></i> Duyệt minh chứng
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                    href="<?php echo app_url('index.php'); ?>?controller=payment&action=refunds">
                                                    <i class="fas fa-undo"></i> Hoàn tiền
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- STUDENT ONLY -->
                        <?php if ($is_student): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo app_url('index.php'); ?>?controller=payment&action=myDebts">
                                    <i class="fas fa-money-bill-wave"></i> Đóng học phí
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- REPORT -->
                        <?php if ($financial_manage): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo app_url('index.php'); ?>?controller=report&action=index">
                                    <i class="fas fa-chart-line"></i> Báo cáo
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- SYSTEM (ADMIN ONLY) -->
                        <?php if ($is_admin): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                    <i class="fas fa-cogs"></i> Hệ thống
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="<?php echo app_url('index.php'); ?>?controller=user&action=index">
                                            <i class="fas fa-users-cog"></i> Người dùng
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo app_url('index.php'); ?>?controller=auditlog">
                                            <i class="fas fa-history"></i> Nhật ký hoạt động
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                            href="<?php echo app_url('index.php'); ?>?controller=admin&action=backupPage">
                                            <i class="fas fa-database"></i> Sao lưu dữ liệu
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                            href="<?php echo app_url('index.php'); ?>?controller=admin&action=systemSettings">
                                            <i class="fas fa-cog"></i> Cài đặt hệ thống
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i>
                                <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item"
                                        href="<?php echo app_url('index.php'); ?>?controller=user&action=profile"><i
                                            class="fas fa-id-card"></i> Thông tin cá nhân</a></li>
                                <li><a class="dropdown-item"
                                        href="<?php echo app_url('index.php'); ?>?controller=user&action=changePassword"><i
                                            class="fas fa-key"></i> Đổi mật khẩu</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="<?php echo app_url('index.php'); ?>?action=logout"><i
                                            class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid px-4">
            <!-- Flash Messages -->
            <?php
            if (function_exists('get_flash')) {
                $success = get_flash('success');
                $error = get_flash('error');

                if ($success): ?>
                    <div class="alert alert-<?php echo $success['type']; ?> alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> <?php echo $success['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif;

                if ($error): ?>
                    <div class="alert alert-<?php echo $error['type']; ?> alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif;
            }
            ?>
        <?php endif; ?>

