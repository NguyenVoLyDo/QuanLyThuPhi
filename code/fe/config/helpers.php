<?php
/**
 * Helper Functions - Các hàm tiện ích dành cho Frontend
 */

/**
 * Get dynamic application URL path
 */
function app_url($path = '')
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptPath = $_SERVER['SCRIPT_NAME']; // e.g., /QuanLyThuPhi/fe/index.php
    $basePath = str_replace('/index.php', '', $scriptPath); // e.g., /QuanLyThuPhi/fe
    
    return $basePath . ($path ? '/' . $path : '');
}

/**
 * Get dynamic backend URL path
 */
function be_url($path = '')
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptPath = $_SERVER['SCRIPT_NAME']; // e.g., /QuanLyThuPhi/fe/index.php
    
    // Assume /be/ is at the same level as /fe/
    $basePath = str_replace('/fe/index.php', '/be', $scriptPath);
    
    return $basePath . ($path ? '/' . $path : '');
}

function clean_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function format_currency($amount)
{
    return number_format($amount, 0, ',', '.') . ' đ';
}

function format_date($date, $format = 'd/m/Y')
{
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

function format_datetime($date, $format = 'd/m/Y H:i:s')
{
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

function generate_code($prefix, $length = 6)
{
    $timestamp = time();
    $random = str_pad(rand(0, 999999), $length, '0', STR_PAD_LEFT);
    return $prefix . date('Ymd') . $random;
}

function check_permission($allowed_roles = [])
{
    if (!isset($_SESSION['user_id']) || empty($_SESSION['api_token'])) {
        header('Location: ' . app_url('index.php?action=login'));
        exit();
    }

    $user_role = $_SESSION['role_name'] ?? $_SESSION['role'] ?? '';

    if (!empty($allowed_roles) && !in_array($user_role, $allowed_roles)) {
        header('Location: ' . app_url('index.php?action=dashboard&error=no_permission'));
        exit();
    }

    return true;
}

function set_flash($key, $message, $type = 'success')
{
    $_SESSION['flash'][$key] = [
        'message' => $message,
        'type' => $type
    ];
}

function get_flash($key)
{
    if (isset($_SESSION['flash'][$key])) {
        $flash = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $flash;
    }
    return null;
}

function paginate($total_records, $per_page = 10, $current_page = 1)
{
    $total_pages = ceil($total_records / $per_page);
    $offset = ($current_page - 1) * $per_page;

    return [
        'total_records' => $total_records,
        'per_page' => $per_page,
        'current_page' => $current_page,
        'total_pages' => $total_pages,
        'offset' => $offset,
        'has_prev' => $current_page > 1,
        'has_next' => $current_page < $total_pages
    ];
}

/**
 * Render pagination HTML
 */
function render_pagination($pagination, $base_url)
{
    if ($pagination['total_pages'] <= 1) return '';

    $html = '<nav aria-label="Page navigation"><ul class="pagination pagination-sm justify-content-end mb-0">';

    // Previous
    $prev_class = $pagination['has_prev'] ? '' : 'disabled';
    $query_char = (strpos($base_url, '?') !== false) ? '&' : '?';
    $prev_url = $pagination['has_prev'] ? $base_url . $query_char . 'page=' . ($pagination['current_page'] - 1) : '#';
    $html .= "<li class='page-item $prev_class'><a class='page-link' href='$prev_url'><i class='fas fa-chevron-left'></i></a></li>";

    // Page numbers
    $start = max(1, $pagination['current_page'] - 2);
    $end = min($pagination['total_pages'], $start + 4);
    if ($end - $start < 4) {
        $start = max(1, $end - 4);
    }

    if ($start > 1) {
        $html .= "<li class='page-item'><a class='page-link' href='{$base_url}{$query_char}page=1'>1</a></li>";
        if ($start > 2) $html .= "<li class='page-item disabled'><span class='page-link'>...</span></li>";
    }

    for ($i = $start; $i <= $end; $i++) {
        $active = ($i == $pagination['current_page']) ? 'active' : '';
        $html .= "<li class='page-item $active'><a class='page-link' href='{$base_url}{$query_char}page=$i'>$i</a></li>";
    }

    if ($end < $pagination['total_pages']) {
        if ($end < $pagination['total_pages'] - 1) $html .= "<li class='page-item disabled'><span class='page-link'>...</span></li>";
        $html .= "<li class='page-item'><a class='page-link' href='{$base_url}{$query_char}page={$pagination['total_pages']}'>{$pagination['total_pages']}</a></li>";
    }

    // Next
    $next_class = $pagination['has_next'] ? '' : 'disabled';
    $next_url = $pagination['has_next'] ? $base_url . $query_char . 'page=' . ($pagination['current_page'] + 1) : '#';
    $html .= "<li class='page-item $next_class'><a class='page-link' href='$next_url'><i class='fas fa-chevron-right'></i></a></li>";

    $html .= '</ul></nav>';
    return $html;
}
