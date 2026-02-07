<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

$database = new Database();
$db = $database->connect();

$featured = isset($_GET['featured']) ? $_GET['featured'] : false;
$category = isset($_GET['category']) ? $_GET['category'] : null;

$query = "SELECT * FROM products WHERE 1=1";

if ($featured === 'true') {
    $query .= " AND featured = 1";
}

if ($category) {
    $query .= " AND category = :category";
}

$query .= " ORDER BY created_at DESC";

$stmt = $db->prepare($query);

if ($category) {
    $stmt->bindParam(':category', $category);
}

$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($products);
?>
