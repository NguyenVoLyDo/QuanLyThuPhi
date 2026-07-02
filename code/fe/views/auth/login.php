<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Quản lý Thu Phí</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }

        .card-header {
            background-color: #fff;
            border-bottom: none;
            padding-top: 2rem;
            padding-bottom: 0;
            text-align: center;
        }

        .card-header .logo-icon {
            font-size: 4rem;
            color: #1e3c72;
            margin-bottom: 1rem;
        }

        .card-body {
            padding: 2rem;
        }

        .form-control {
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            padding-left: 2.5rem;
        }

        .input-group-text {
            background: none;
            border: none;
            position: absolute;
            left: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            color: #6c757d;
        }

        .btn-login {
            border-radius: 0.5rem;
            padding: 0.75rem;
            font-weight: 600;
            background: linear-gradient(to right, #1e3c72, #2a5298);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .input-wrapper {
            position: relative;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card login-card animate__animated animate__fadeInDown">
                    <div class="card-header">
                        <div class="logo-icon">
                            <i class="fas fa-school"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Quản lý Thu Phí</h4>
                        <p class="text-muted small">Đăng nhập để tiếp tục</p>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <div><?= htmlspecialchars($error) ?></div>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="index.php?action=login">
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">TÀI KHOẢN</label>
                                <div class="input-wrapper">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" name="username" class="form-control"
                                        placeholder="Nhập tên đăng nhập" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">MẬT KHẨU</label>
                                <div class="input-wrapper position-relative">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" id="password" class="form-control pe-5"
                                        placeholder="Nhập mật khẩu" required>
                                    <button type="button"
                                        class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-decoration-none text-muted"
                                        onclick="togglePassword()" style="z-index: 20; margin-right: 5px;">
                                        <i class="fas fa-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <script>
                                function togglePassword() {
                                    const passwordField = document.getElementById('password');
                                    const toggleIcon = document.getElementById('toggleIcon');
                                    if (passwordField.type === 'password') {
                                        passwordField.type = 'text';
                                        toggleIcon.classList.remove('fa-eye');
                                        toggleIcon.classList.add('fa-eye-slash');
                                    } else {
                                        passwordField.type = 'password';
                                        toggleIcon.classList.remove('fa-eye-slash');
                                        toggleIcon.classList.add('fa-eye');
                                    }
                                }

                                // Auto clear password on error
                                <?php if (isset($error)): ?>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        const passwordField = document.getElementById('password');
                                        if (passwordField) {
                                            passwordField.value = '';
                                            passwordField.focus();
                                        }
                                    });
                                <?php endif; ?>
                            </script>

                            <button type="submit" class="btn btn-primary w-100 btn-login text-white mt-3">
                                <i class="fas fa-sign-in-alt me-2"></i> ĐĂNG NHẬP
                            </button>
                        </form>
                    </div>
                    <div class="card-footer bg-white text-center py-3 border-top-0">
                        <small class="text-muted">&copy; <?php echo date('Y'); ?> School Fee Management System</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
