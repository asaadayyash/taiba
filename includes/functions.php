<?php
function formatPrice(float $amount): string {
    return number_format($amount, 2) . ' ' . CURRENCY;
}

function generateOrderNumber(): string {
    return 'TB-' . strtoupper(base_convert(time(), 10, 36)) . '-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
}

function parseImages(string $json): array {
    $arr = json_decode($json, true);
    return is_array($arr) ? $arr : [];
}

function slugify(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

function e(mixed $val): string {
    return htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
}

function cartCount(): int {
    if (!isLoggedIn()) return 0;
    $row = queryOne('SELECT SUM(quantity) as total FROM cart WHERE user_id = ?', [$_SESSION['user_id']]);
    return (int)($row['total'] ?? 0);
}

function getCategories(): array {
    return query('SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON p.category_id = c.id GROUP BY c.id ORDER BY c.name');
}

function statusBadge(string $status): string {
    $map = [
        'PENDING'    => 'bg-yellow-500/20 text-yellow-400',
        'CONFIRMED'  => 'bg-blue-500/20 text-blue-400',
        'PROCESSING' => 'bg-purple-500/20 text-purple-400',
        'SHIPPED'    => 'bg-indigo-500/20 text-indigo-400',
        'DELIVERED'  => 'bg-green-500/20 text-green-400',
        'CANCELLED'  => 'bg-red-500/20 text-red-400',
    ];
    $class = $map[$status] ?? 'bg-gray-500/20 text-gray-400';
    return '<span class="text-xs font-semibold px-2.5 py-0.5 rounded-full ' . $class . '">' . e($status) . '</span>';
}
