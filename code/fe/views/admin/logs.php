<?php
$page_title = 'Nhật ký hệ thống';
include __DIR__ . '/../layout/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-history"></i> Nhật ký hệ thống</h5>
    </div>
    
    <!-- Filter Section -->
    <div class="card-body border-bottom">
        <form action="index.php" method="GET" class="row g-3">
            <input type="hidden" name="controller" value="auditlog">
            <input type="hidden" name="action" value="index">
            
            <div class="col-md-3">
                <label class="form-label small"><i class="fas fa-search"></i> Tìm kiếm</label>
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..." 
                    value="<?php echo htmlspecialchars($search ?? ''); ?>">
            </div>
            
            <div class="col-md-2">
                <label class="form-label small"><i class="fas fa-user"></i> Người dùng</label>
                <select name="user_id" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo $user['id']; ?>" <?php echo ($user_id == $user['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user['full_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label small"><i class="fas fa-bolt"></i> Hành động</label>
                <select name="log_action" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <?php foreach ($actions as $act): ?>
                        <option value="<?php echo $act['action']; ?>" <?php echo ($log_action == $act['action']) ? 'selected' : ''; ?>>
                            <?php echo $act['action']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label small"><i class="fas fa-calendar"></i> Từ ngày</label>
                <input type="date" name="from_date" class="form-control" 
                    value="<?php echo htmlspecialchars($from_date ?? ''); ?>">
            </div>
            
            <div class="col-md-2">
                <label class="form-label small"><i class="fas fa-calendar"></i> Đến ngày</label>
                <input type="date" name="to_date" class="form-control" 
                    value="<?php echo htmlspecialchars($to_date ?? ''); ?>">
            </div>
            
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter"></i> Lọc
                </button>
            </div>
        </form>
        
        <div class="mt-2">
            <a href="index.php?controller=auditlog&action=export&search=<?php echo urlencode($search ?? ''); ?>&user_id=<?php echo $user_id ?? ''; ?>&log_action=<?php echo $log_action ?? ''; ?>&from_date=<?php echo $from_date ?? ''; ?>&to_date=<?php echo $to_date ?? ''; ?>" 
                class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Xuất Excel
            </a>
            <a href="index.php?controller=auditlog&action=index" class="btn btn-secondary btn-sm">
                <i class="fas fa-redo"></i> Xóa bộ lọc
            </a>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Thời gian</th>
                        <th>Người dùng</th>
                        <th>Hành động</th>
                        <th>Đối tượng</th>
                        <th>ID</th>
                        <th>Chi tiết</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)): ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td class="small"><?php echo format_datetime($log['created_at']); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($log['full_name'] ?? 'N/A'); ?></strong>
                                    <br><small class="text-muted"><?php echo $log['role_name'] ?? ''; ?></small>
                                </td>
                                <td>
                                    <?php
                                    $badge_class = match($log['action']) {
                                        'Login' => 'bg-success',
                                        'Logout' => 'bg-secondary',
                                        'Create', 'CREATE_PAYMENT', 'CREATE_FEE_TYPE' => 'bg-primary',
                                        'Update' => 'bg-warning',
                                        'Delete', 'DELETE_FEE_TYPE' => 'bg-danger',
                                        default => 'bg-info'
                                    };
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo $log['action']; ?></span>
                                </td>
                                <td><?php echo $log['target_type'] ?? '-'; ?></td>
                                <td><?php echo $log['target_id'] ?? '-'; ?></td>
                                <td class="small" style="max-width: 300px;">
                                    <?php echo htmlspecialchars(substr($log['details'] ?? '', 0, 100)); ?>
                                    <?php if (strlen($log['details'] ?? '') > 100): ?>...<?php endif; ?>
                                </td>
                                <td class="small"><?php echo $log['ip_address'] ?? '-'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-info-circle text-muted"></i> Không tìm thấy nhật ký nào
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php if ($pagination['total_pages'] > 1): ?>
        <div class="card-footer bg-white">
            <?php 
            $filter_params = http_build_query([
                'controller' => 'auditlog',
                'action' => 'index',
                'search' => $search ?? '',
                'user_id' => $user_id ?? '',
                'log_action' => $log_action ?? '',
                'from_date' => $from_date ?? '',
                'to_date' => $to_date ?? ''
            ]);
            echo render_pagination($pagination, app_url('index.php') . '?' . $filter_params);
            ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
