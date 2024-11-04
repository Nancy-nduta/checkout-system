<?php
session_start();

// Database configuration
$servername = "localhost"; // Your database server
$username = "root"; // Your database username
$password = "Nanuta@123!!!!!##"; // Your database password
$dbname = "cart"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve cart summary from session
$cart_summary = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Display the receipt if cart summary exists
if (!empty($cart_summary)) {
    echo "<h1>Checkout Receipt</h1>";
    echo "<h2>Cart Summary</h2>";
    echo "<ul>";
    
    foreach ($cart_summary as $item) {
        echo "<li>{$item['name']} - Quantity: {$item['quantity']} - Price: \${$item['price']}</li>";
    }
    
    echo "</ul>";

    // Total amount calculation
    $total_amount = array_sum(array_map(function($item) {
        return $item['price'] * $item['quantity'];
    }, $cart_summary));
    
    echo "<h3>Total Amount: \$$total_amount</h3>";

    // Optionally, you can add a button to confirm the order
    echo '<form method="POST" action="confirm_order.php">';
    echo '<input type="hidden" name="total_amount" value="' . $total_amount . '">';
    echo '<input type="submit" value="Confirm Order">';
    echo '</form>';
} else {
    echo "<h2>Your cart is empty.</h2>";
}

// Close the database connection
$conn->close();
?>