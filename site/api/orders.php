<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    try {
        $db->beginTransaction();
        
        // Insert order
        $stmt = $db->prepare("INSERT INTO orders (customer_name, customer_email, customer_phone, shipping_address, total_amount) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['customer_name'],
            $data['customer_email'],
            $data['customer_phone'],
            $data['shipping_address'],
            $data['total_amount']
        ]);
        
        $orderId = $db->lastInsertId();
        
        // Insert order items
        $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($data['items'] as $item) {
            $stmt->execute([
                $orderId,
                $item['id'],
                $item['quantity'],
                $item['price']
            ]);
        }
        
        $db->commit();
        echo json_encode(['success' => true, 'order_id' => $orderId]);
    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
