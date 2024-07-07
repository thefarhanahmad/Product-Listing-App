<?php
// get_products.php

// Database connection
require_once('db_connection.php');

// Default values for pagination
$page = isset($_POST['page']) ? $_POST['page'] : 1;
$limit = 12; // Products per page

// Calculate offset
$offset = ($page - 1) * $limit;

// Query to fetch products with filters
$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM products WHERE 1=1";

if (!empty($_POST['category'])) {
    $category = $_POST['category'];
    $sql .= " AND category = '$category'";
}

if (!empty($_POST['price_min'])) {
    $price_min = $_POST['price_min'];
    $sql .= " AND price >= $price_min";
}

if (!empty($_POST['price_max'])) {
    $price_max = $_POST['price_max'];
    $sql .= " AND price <= $price_max";
}

if (isset($_POST['sale_status'])) {
    $sale_status = $_POST['sale_status'];
    if ($sale_status === '1' || $sale_status === '0') {
        $sql .= " AND sale_status = $sale_status";
    }
}

// Adjust limit for the last page if necessary
if ($page == 3) {
    $limit = 6; // Last page will display 6 products
}

$sql .= " LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $sql);
if (!$result) {
    die('Error retrieving products: ' . mysqli_error($conn));
}

// Fetch products
$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

// Total number of products for pagination
$total_result = mysqli_query($conn, "SELECT FOUND_ROWS() as total");
$total_rows = mysqli_fetch_assoc($total_result)['total'];

mysqli_free_result($result);
mysqli_close($conn);

// Prepare response
$response = [
    'products' => $products,
    'total' => $total_rows
];

header('Content-Type: application/json');
echo json_encode($response);
?>
