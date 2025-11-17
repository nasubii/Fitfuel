<?php
/**
 * セッションカートを共通形式に変換して返す
 * カートは product_id => quantity 形式を想定
 * 戻り値: [
 *   'items' => [ [ 'product_id' => int, 'product_name' => string, 'product_price' => int, 'product_image' => string|null, 'product_stock' => int, 'quantity' => int, 'subtotal' => int ]...],
 *   'total' => int
 * ]
 */
function getCartDetails(PDO $pdo, array $sessionCart): array
{
    if (empty($sessionCart)) {
        return ['items' => [], 'total' => 0];
    }

    $items = [];
    $total = 0;

    $ids = array_map('intval', array_keys($sessionCart));
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT product_id, product_name, product_price, product_image, product_stock FROM product WHERE product_id IN ($placeholders)");
    $stmt->execute($ids);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        $pid = (int)$row['product_id'];
        $qty = isset($sessionCart[$pid]) ? max(1, (int)$sessionCart[$pid]) : 1;
        $price = (int)$row['product_price'];
        $subtotal = $price * $qty;
        $items[] = [
            'product_id' => $pid,
            'product_name' => $row['product_name'],
            'product_price' => $price,
            'product_image' => $row['product_image'],
            'product_stock' => (int)$row['product_stock'],
            'quantity' => $qty,
            'subtotal' => $subtotal,
        ];
        $total += $subtotal;
    }

    return ['items' => $items, 'total' => $total];
}
