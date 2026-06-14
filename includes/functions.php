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
        'PENDING'    => 'bg-yellow-100 text-yellow-700 border border-yellow-300',
        'CONFIRMED'  => 'bg-blue-100 text-blue-700 border border-blue-300',
        'PROCESSING' => 'bg-purple-100 text-purple-700 border border-purple-300',
        'SHIPPED'    => 'bg-indigo-100 text-indigo-700 border border-indigo-300',
        'DELIVERED'  => 'bg-green-100 text-green-700 border border-green-300',
        'CANCELLED'  => 'bg-red-100 text-red-700 border border-red-300',
    ];
    $class = $map[$status] ?? 'bg-stone-100 text-stone-600 border border-stone-300';
    return '<span class="text-xs font-semibold px-2.5 py-0.5 rounded-full ' . $class . '">' . e($status) . '</span>';
}
