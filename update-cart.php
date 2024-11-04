<?php
session_start();

// Database connection
$host = 'localhost'; // Change if necessary
$db = 'cart';
$user = 'root'; // Change if necessary
$pass = 'Nanuta@123!!!!!##'; // Change if necessary

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update cart quantity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Update the session cart
    if ($quantity > 0) {
        $_SESSION['cart'][$productId] = $quantity; // Update quantity
    } else {
        unset($_SESSION['cart'][$productId]); // Remove item if quantity is 0
    }

    // Calculate new grand total
    $grandTotal = 0;
    foreach ($_SESSION['cart'] as $id => $qty) {
        $result = $conn->query("SELECT price FROM products WHERE id = $id");
        if ($row = $result->fetch_assoc()) {
            $grandTotal += $row['price'] * $qty;
        }
    }

    echo number_format($grandTotal, 2); // Return the grand total
    exit();
}

$conn->close();
?>