<?php
session_start();

// Check if product_id is set
if (isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];

    // Remove the item from the cart
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }

    // Calculate the new grand total
    $grandTotal = 0;
    foreach ($_SESSION['cart'] as $id => $quantity) {
        // Assuming you have a function to get the product price by ID
        $productPrice = getProductPriceById($id); // Implement this function based on your database
        $grandTotal += $productPrice * $quantity;
    }

    // Return the new grand total
    echo number_format($grandTotal, 2);
}

function getProductPriceById($id) {
    // Database connection
    $host = 'localhost'; // Change if necessary
    $db = 'cart';
    $user = 'root'; // Change if necessary
    $pass = 'Nanuta@123!!!!!##'; // Change if necessary

    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($price);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    return $price;
}
?>