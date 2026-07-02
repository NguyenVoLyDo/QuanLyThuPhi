<?php

function clean($data) {
    return trim(htmlspecialchars($data, ENT_COMPAT, 'UTF-8'));
}

function cleanUrl($url) {
    return str_replace(['%20', ' '], '-', $url);
}

function format_date($date) {
    if (empty($date) || $date === '0000-00-00') return '';
    try {
        return date('d/m/Y', strtotime($date));
    } catch (Exception $e) {
        return $date;
    }
}

function format_datetime($datetime) {
    if (empty($datetime)) return '';
    try {
        return date('d/m/Y H:i', strtotime($datetime));
    } catch (Exception $e) {
        return $datetime;
    }
}

function format_currency($amount) {
    return number_format($amount, 0, ',', '.') . ' đ';
}

function paginate($total, $per_page, $current_page) {
    $total_pages = ceil($total / $per_page);
    return [
        'total'        => $total,
        'per_page'     => $per_page,
        'current_page' => $current_page,
        'total_pages'  => $total_pages,
        'has_prev'     => $current_page > 1,
        'has_next'     => $current_page < $total_pages,
    ];
}

function clean_input($data) {
    return trim(htmlspecialchars($data, ENT_COMPAT, 'UTF-8'));
}
