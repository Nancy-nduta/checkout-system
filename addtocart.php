<?php
session_start();

// Check if the cart session exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Retrieve product ID and quantity from the form
$product_id = $_POST['product_id'];
$quantity = $_POST['quantity'];

// Add or update the product in the cart
if (array_key_exists($product_id, $_SESSION['cart'])) {
    $_SESSION['cart'][$product_id] += $quantity; // Update quantity
} else {
    $_SESSION['cart'][$product_id] = $quantity; // Add new product
}

// Redirect back to the cart page
header("Location: cart.php");
exit();
?>