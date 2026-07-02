<?php
$page_title = 'Chọn phương thức thanh toán';
include __DIR__ . '/../layout/header.php';

// Generate dynamic VietQR Link
// Format: https://img.vietqr.io/image/<BANK_ID>-<ACCOUNT_NO>-<TEMPLATE>.png?amount=<AMOUNT>&addInfo=<INFO>
// $qr_url generation removed - using static image
$username = isset($_SESSION['username']) ? $_SESSION['username'] : (isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Hoc sinh');
$content = "Hoc phi " . $username . " " . $fee_name;
//$qr_url = "https://img.vietqr.io/image/{$bank_id}-{$account_no}-{$template}.png?amount={$amount}&addInfo=" . urlencode($content);
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-wallet text-primary"></i> Thanh toán khoản thu: <strong>
                        <?php echo htmlspecialchars($fee_name); ?>
                    </strong></h5>
                <p class="text-muted mb-0">Số tiền: <strong class="text-danger">
                        <?php echo format_currency($amount); ?>
                    </strong></p>
            </div>
            <div class="card-body">
                <ul class="nav nav-pills mb-3 nav-fill" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-qr-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-qr" type="button" role="tab">
                            <i class="fas fa-qrcode"></i> Quét mã VietQR
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-transfer-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-transfer" type="button" role="tab">
                            <i class="fas fa-university"></i> Chuyển khoản thủ công
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-ewallet-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-ewallet" type="button" role="tab">
                            <i class="fas fa-mobile-alt"></i> Ví điện tử (Momo/Zalo)
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="pills-tabContent">
                    <!-- VIETQR -->
                    <div class="tab-pane fade show active text-center" id="pills-qr" role="tabpanel">
                        <div class="p-3 mb-2 bg-info-subtle text-info-emphasis rounded">
                            <i class="fas fa-magic"></i> Quét mã QR bên dưới để thanh toán.
                        </div>
                        <img src="<?php echo be_url(); ?>/uploads/settings/payment_qr.png" alt="Mã QR Thanh Toán"
                            class="img-fluid border p-2 rounded mb-3" style="max-width: 300px;">
                        <p class="text-muted small">Vui lòng nhập đúng nội dung chuyển khoản:
                            <strong><?php echo htmlspecialchars($username . " " . $fee_name); ?></strong>
                        </p>
                        <hr>
                        <a href="<?php echo app_url("index.php"); ?>?controller=payment&action=uploadProof&fee_type_id=<?php echo $fee_type_id; ?>&amount=<?php echo $amount; ?>&fee_name=<?php echo urlencode($fee_name); ?>"
                            class="btn btn-success">
                            <i class="fas fa-check"></i> Tôi đã thanh toán xong
                        </a>
                    </div>

                    <!-- MANUAL TRANSFER -->
                    <div class="tab-pane fade" id="pills-transfer" role="tabpanel">
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6>Thông tin chuyển khoản:</h6>
                                <p class="mb-1">Ngân hàng: <strong>MB Bank</strong></p>
                                <p class="mb-1">Số tài khoản: <strong>0000000000</strong></p>
                                <p class="mb-1">Chủ tài khoản: <strong>TRUONG HOC XXX</strong></p>
                                <p class="mb-1">Nội dung: <strong>
                                        <?php echo ($username ?? 'Hoc sinh') . " dong tien " . $fee_name; ?>
                                    </strong></p>
                            </div>
                        </div>
                        <div class="text-center">
                            <a href="<?php echo app_url("index.php"); ?>?controller=payment&action=uploadProof&fee_type_id=<?php echo $fee_type_id; ?>&amount=<?php echo $amount; ?>&fee_name=<?php echo urlencode($fee_name); ?>"
                                class="btn btn-warning text-dark">
                                <i class="fas fa-upload"></i> Upload Minh chứng thanh toán
                            </a>
                        </div>
                    </div>

                    <!-- E-WALLET -->
                    <div class="tab-pane fade text-center" id="pills-ewallet" role="tabpanel">
                        <div class="p-3 mb-2 bg-warning-subtle text-warning-emphasis rounded">
                            <i class="fas fa-mobile-alt"></i> Hệ thống sẽ chuyển hướng bạn sang ứng dụng Ví điện tử để
                            thanh toán.
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-md-5 mb-3">
                                <div class="card h-100 border-danger">
                                    <div class="card-body">
                                        <h6 class="text-danger fw-bold">MOMO</h6>
                                        <img src="https://img.mservice.io/momo_app_v2/img/MoMo_Logo.png"
                                            class="img-fluid mb-3" style="height: 60px;">
                                        <p class="small text-muted">Chuyển đến ví Momo</p>
                                        <?php
                                        // Momo Personal Link format
                                        $momo_phone = '0909000000'; // Có thể thay đổi số điện thoại nhận tiền ở đây
                                        $momo_content = clean_input("HP " . $username . " " . $fee_name);
                                        $momo_link = "https://me.momo.vn/{$momo_phone}?amount={$amount}&message=" . urlencode($momo_content);
                                        ?>
                                        <a href="<?php echo $momo_link; ?>" target="_blank"
                                            class="btn btn-danger w-100">
                                            <i class="fas fa-wallet"></i> Thanh toán Momo
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5 mb-3">
                                <div class="card h-100 border-primary">
                                    <div class="card-body">
                                        <h6 class="text-primary fw-bold">ZALOPAY</h6>
                                        <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Logo-ZaloPay-Square.png"
                                            class="img-fluid mb-3" style="height: 60px;">
                                        <p class="small text-muted">Mở ứng dụng ZaloPay</p>
                                        <?php
                                        // ZaloPay currently doesn't support direct web-to-app personal payment link easily without merchant API
                                        // Using generic link or Zalo chat link as fallback
                                        $zalopay_phone = '0909000000';
                                        $zalo_link = "https://zalo.me/{$zalopay_phone}";
                                        ?>
                                        <a href="<?php echo $zalo_link; ?>" target="_blank"
                                            class="btn btn-primary w-100">
                                            <i class="fas fa-comments"></i> Mở ZaloPay/Zalo
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="mt-3">
                            <p class="text-muted">Sau khi thanh toán thành công trên Ví, vui lòng lưu ảnh giao dịch và
                                upload minh chứng bên dưới.</p>
                            <a href="<?php echo app_url("index.php"); ?>?controller=payment&action=uploadProof&fee_type_id=<?php echo $fee_type_id; ?>&amount=<?php echo $amount; ?>&fee_name=<?php echo urlencode($fee_name); ?>"
                                class="btn btn-warning text-dark">
                                <i class="fas fa-upload"></i> Upload Minh chứng đã thanh toán
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white text-center">
                <a href="<?php echo app_url("index.php"); ?>?controller=payment&action=myDebts" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
