<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biên lai thu tiền - <?php echo $payment['payment_code']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .receipt-container { box-shadow: none !important; margin: 0 !important; }
        }
        
        body {
            background: #f5f5f5;
            padding: 20px;
        }
        
        .receipt-container {
            background: white;
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .receipt-header {
            text-align: center;
            border-bottom: 3px double #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .receipt-title {
            font-size: 28px;
            font-weight: bold;
            color: #0d6efd;
            margin: 10px 0;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed #ddd;
        }
        
        .info-label {
            font-weight: 600;
            width: 200px;
        }
        
        .amount-box {
            background: #f8f9fa;
            border: 2px solid #0d6efd;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
            border-radius: 10px;
        }
        
        .amount-number {
            font-size: 36px;
            font-weight: bold;
            color: #0d6efd;
        }
        
        .signature-area {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            text-align: center;
            width: 45%;
        }
        
        .dotted-line {
            border-top: 1px dotted #333;
            margin: 80px 0 10px 0;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="receipt-header">
            <div class="row">
                <div class="col-6 text-start">
                    <strong>TRƯỜNG THPT ABC</strong><br>
                    <small>Địa chỉ: 123 Đường XYZ, Hà Nội</small><br>
                    <small>ĐT: 024.1234.5678</small>
                </div>
                <div class="col-6 text-end">
                    <div style="border: 2px solid #333; padding: 10px; display: inline-block;">
                        <strong>Mã số: <?php echo $payment['payment_code']; ?></strong>
                    </div>
                </div>
            </div>
            
            <div class="receipt-title mt-3">
                PHIẾU THU TIỀN HỌC PHÍ
            </div>
            
            <p class="mb-0">
                <em>Ngày <?php echo date('d', strtotime($payment['payment_date'])); ?> 
                tháng <?php echo date('m', strtotime($payment['payment_date'])); ?> 
                năm <?php echo date('Y', strtotime($payment['payment_date'])); ?></em>
            </p>
        </div>
        
        <!-- Thông tin -->
        <div class="receipt-body">
            <div class="info-row">
                <div class="info-label">Họ và tên học sinh:</div>
                <div class="info-value"><strong><?php echo $payment['student_name']; ?></strong></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Mã học sinh:</div>
                <div class="info-value"><?php echo $payment['student_code']; ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Lớp:</div>
                <div class="info-value"><?php echo $payment['class_name']; ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Phụ huynh:</div>
                <div class="info-value"><?php echo $payment['parent_name']; ?> - <?php echo $payment['parent_phone']; ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Nội dung thu:</div>
                <div class="info-value"><strong><?php echo $payment['fee_name']; ?></strong></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Phương thức thanh toán:</div>
                <div class="info-value"><?php echo $payment['payment_method']; ?></div>
            </div>
            
            <?php if ($payment['notes']): ?>
                <div class="info-row">
                    <div class="info-label">Ghi chú:</div>
                    <div class="info-value"><em><?php echo $payment['notes']; ?></em></div>
                </div>
            <?php endif; ?>
            
            <!-- Số tiền -->
            <div class="amount-box">
                <div>Số tiền thanh toán:</div>
                <div class="amount-number"><?php echo format_currency($payment['amount_paid']); ?></div>
                <div class="mt-2">
                    <em>(Bằng chữ: 
                        <?php 
                        // Convert number to Vietnamese text
                        function numberToVietnamese($number) {
                            $number = intval($number);
                            if ($number == 0) return "Không đồng";
                            
                            $units = ["", "một", "hai", "ba", "bốn", "năm", "sáu", "bảy", "tám", "chín"];
                            $levels = ["", "nghìn", "triệu", "tỷ"];
                            
                            $result = "";
                            $level = 0;
                            
                            while ($number > 0) {
                                $temp = $number % 1000;
                                if ($temp != 0) {
                                    $hundred = intval($temp / 100);
                                    $ten = intval(($temp % 100) / 10);
                                    $unit = $temp % 10;
                                    
                                    $tempStr = "";
                                    if ($hundred > 0) $tempStr .= $units[$hundred] . " trăm ";
                                    if ($ten > 1) $tempStr .= $units[$ten] . " mươi ";
                                    else if ($ten == 1) $tempStr .= "mười ";
                                    if ($unit > 0) $tempStr .= $units[$unit] . " ";
                                    
                                    $result = $tempStr . $levels[$level] . " " . $result;
                                }
                                $number = intval($number / 1000);
                                $level++;
                            }
                            
                            return ucfirst(trim($result)) . " đồng";
                        }
                        
                        echo numberToVietnamese($payment['amount_paid']);
                        ?>
                    )</em>
                </div>
            </div>
            
            <!-- Chữ ký -->
            <div class="signature-area">
                <div class="signature-box">
                    <strong>NGƯỜI NỘP TIỀN</strong>
                    <div class="dotted-line"></div>
                    <small>(Ký và ghi rõ họ tên)</small>
                </div>
                
                <div class="signature-box">
                    <strong>NGƯỜI THU TIỀN</strong>
                    <div class="dotted-line"></div>
                    <small><?php echo $payment['collector_name']; ?></small>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <small class="text-muted">
                    * Vui lòng giữ lại biên lai này để đối chiếu khi cần thiết *
                </small>
            </div>
        </div>
        
        <!-- Buttons -->
        <div class="text-center mt-4 no-print">
            <button onclick="window.print()" class="btn btn-primary btn-lg me-2">
                <i class="fas fa-print"></i> In biên lai
            </button>
            <a href="<?php echo app_url("index.php"); ?>?controller=payment&action=index" class="btn btn-secondary btn-lg">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
