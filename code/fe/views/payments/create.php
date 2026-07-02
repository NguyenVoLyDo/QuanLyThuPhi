<?php
$page_title = 'Thu phí học sinh';
include __DIR__ . '/../layout/header.php';

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="fas fa-cash-register"></i> 
            <?php echo ($_SESSION['role_name'] === 'Student') ? 'Thanh toán học phí của tôi' : 'Thu phí học sinh'; ?>
        </h5>
    </div>
    
    <div class="card-body">
        <form method="POST" action="<?php echo app_url("index.php"); ?>?controller=payment&action=store" id="paymentForm">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2 mb-3">Thông tin thanh toán</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">Học sinh <span class="text-danger">*</span></label>
                        <?php if ($_SESSION['role_name'] === 'Student' && isset($_SESSION['student_id'])): ?>
                            <!-- Student View -->
                            <div class="alert alert-info py-2">
                                <strong>Học sinh:</strong> <?php echo $_SESSION['full_name']; ?>
                            </div>
                            <input type="hidden" name="student_id" id="student_id" value="<?php echo $_SESSION['student_id']; ?>">
                        <?php else: ?>
                            <!-- Admin View -->
                            <select name="student_id" id="student_id" class="form-select <?php echo isset($errors['student_id']) ? 'is-invalid' : ''; ?>" required>
                                <option value="">-- Chọn học sinh --</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?php echo $student['id']; ?>" 
                                            <?php echo ($selected_student_id == $student['id'] || ($old['student_id'] ?? '') == $student['id']) ? 'selected' : ''; ?>>
                                        <?php echo $student['student_code']; ?> - <?php echo $student['full_name']; ?> (<?php echo $student['class_name']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['student_id'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['student_id']; ?></div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div id="debtInfo" class="mb-4" style="display: none;">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark py-2">
                                <h6 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> Danh sách công nợ học sinh</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0" id="debtTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="40"><input type="checkbox" id="selectAllDebts"></th>
                                                <th>Khoản thu</th>
                                                <th>Còn nợ</th>
                                                <th width="150">Số tiền thu</th>
                                            </tr>
                                        </thead>
                                        <tbody id="debtList">
                                            <!-- AJAX dynamic content -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer bg-light py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>Tổng cộng chọn:</strong>
                                    <span id="selectedTotal" class="text-danger fw-bold">0 ₫</span>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="is_multi" id="is_multi" value="0">
                    </div>
                    
                    <div id="singleFeeSection">
                        <div class="mb-3">
                            <label class="form-label">Khoản thu <span class="text-danger">*</span></label>
                            <select name="fee_type_id" id="fee_type_id" class="form-select <?php echo isset($errors['fee_type_id']) ? 'is-invalid' : ''; ?>">
                                <option value="">-- Chọn khoản thu --</option>
                                <?php 
                                $selected_fee = $_GET['fee_type_id'] ?? ($old['fee_type_id'] ?? '');
                                foreach ($fee_types as $fee): 
                                ?>
                                    <option value="<?php echo $fee['id']; ?>" 
                                            data-amount="<?php echo $fee['amount']; ?>"
                                            <?php echo ($selected_fee == $fee['id']) ? 'selected' : ''; ?>>
                                        <?php echo $fee['fee_name']; ?> - <?php echo format_currency($fee['amount']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['fee_type_id'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['fee_type_id']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Số tiền thanh toán <span class="text-danger">*</span></label>
                            <input type="number" name="amount_paid" id="amount_paid" class="form-control <?php echo isset($errors['amount_paid']) ? 'is-invalid' : ''; ?>" 
                                   value="<?php echo $_GET['amount'] ?? ($old['amount_paid'] ?? ''); ?>" 
                                   min="1000" step="1000">
                            <?php if (isset($errors['amount_paid'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['amount_paid']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Ngày thanh toán <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" 
                               value="<?php echo $old['payment_date'] ?? date('Y-m-d'); ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2 mb-3">Chi tiết phiếu thu</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">Phương thức thanh toán <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <?php foreach ($payment_methods as $key => $method): ?>
                                <option value="<?php echo $key; ?>" <?php echo ($old['payment_method'] ?? 'Cash') == $key ? 'selected' : ''; ?>>
                                    <?php echo $method; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Số biên lai</label>
                        <input type="text" name="receipt_number" class="form-control" 
                               value="<?php echo $old['receipt_number'] ?? ''; ?>" 
                               placeholder="VD: BL001 (tùy chọn)">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="notes" class="form-control" rows="3"><?php echo $old['notes'] ?? ''; ?></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <strong>Lưu ý:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Kiểm tra kỹ thông tin trước khi lưu</li>
                            <?php if ($_SESSION['role_name'] === 'Student'): ?>
                            <li>Thanh toán của bạn sẽ được chuyển sang trạng thái <strong>Chờ duyệt</strong>.</li>
                            <li>Vui lòng chờ kế toán xác nhận.</li>
                            <?php else: ?>
                            <li>Sau khi lưu sẽ tự động tạo biên lai</li>
                            <li>Có thể in biên lai sau khi hoàn tất</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="text-end">
                <a href="<?php echo app_url("index.php"); ?>?controller=payment&action=index" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
                <?php if ($_SESSION['role_name'] === 'Student'): ?>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Gửi thanh toán
                </button>
                <?php else: ?>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Lưu và in biên lai
                </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<script>
// Load student debts via AJAX
document.getElementById('student_id').addEventListener('change', function() {
    const studentId = this.value;
    const debtInfo = document.getElementById('debtInfo');
    const debtList = document.getElementById('debtList');
    const singleFeeSection = document.getElementById('singleFeeSection');
    
    if (!studentId) {
        debtInfo.style.display = 'none';
        singleFeeSection.style.display = 'block';
        return;
    }
    
    // AJAX call to get debts
    fetch('<?php echo app_url("index.php"); ?>?controller=payment&action=getStudentDebts&student_id=' + studentId)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.debts.length > 0) {
                let html = '';
                
                let hasUnpaid = false;
                data.debts.forEach(debt => {
                    if (debt.remaining_amount > 0) {
                        hasUnpaid = true;
                        html += `<tr>
                            <td><input type="checkbox" name="fee_type_ids[]" value="${debt.fee_type_id}" class="debt-check"></td>
                            <td>${debt.fee_name}</td>
                            <td class="text-danger">${formatCurrency(debt.remaining_amount)}</td>
                            <td>
                                <input type="number" name="amounts[${debt.fee_type_id}]" 
                                       class="form-control form-control-sm debt-amount" 
                                       value="${debt.remaining_amount}" 
                                       min="0" max="${debt.remaining_amount}" disabled>
                            </td>
                        </tr>`;
                    }
                });
                
                if (hasUnpaid) {
                    debtList.innerHTML = html;
                    debtInfo.style.display = 'block';
                    singleFeeSection.style.display = 'none'; // Hide single select if there are debts
                    
                    // Add listeners to new elements
                    attachDebtListeners();
                } else {
                    debtInfo.style.display = 'none';
                    singleFeeSection.style.display = 'block';
                }
            } else {
                debtInfo.style.display = 'none';
                singleFeeSection.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
});

function attachDebtListeners() {
    const checks = document.querySelectorAll('.debt-check');
    const amounts = document.querySelectorAll('.debt-amount');
    const selectAll = document.getElementById('selectAllDebts');
    const isMulti = document.getElementById('is_multi');
    const singleFeeId = document.getElementById('fee_type_id');
    const singleAmount = document.getElementById('amount_paid');

    selectAll.addEventListener('change', function() {
        checks.forEach(c => {
            c.checked = this.checked;
            c.dispatchEvent(new Event('change'));
        });
    });

    checks.forEach(check => {
        check.addEventListener('change', function() {
            const amountInput = this.closest('tr').querySelector('.debt-amount');
            amountInput.disabled = !this.checked;
            
            // Check if multi
            const checkedCount = document.querySelectorAll('.debt-check:checked').length;
            isMulti.value = checkedCount > 0 ? "1" : "0";
            
            // Make single required attributes conditional
            if (checkedCount > 0) {
                singleFeeId.removeAttribute('required');
                singleAmount.removeAttribute('required');
            } else {
                singleFeeId.setAttribute('required', '');
                singleAmount.setAttribute('required', '');
            }
            
            calculateTotal();
        });
    });

    amounts.forEach(amount => {
        amount.addEventListener('input', calculateTotal);
    });
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.debt-check:checked').forEach(check => {
        const amountInput = check.closest('tr').querySelector('.debt-amount');
        total += parseFloat(amountInput.value) || 0;
    });
    document.getElementById('selectedTotal').innerText = formatCurrency(total);
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', { 
        style: 'currency', 
        currency: 'VND' 
    }).format(amount);
}

// Trigger on page load if student selected
<?php if ($selected_student_id): ?>
    document.getElementById('student_id').dispatchEvent(new Event('change'));
<?php endif; ?>
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
