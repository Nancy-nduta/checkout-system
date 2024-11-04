<?php
session_start();

// Database connection
$host = 'localhost'; // Change if necessary
$db = 'cart'; // Your database name
$user = 'root'; // Change if necessary
$pass = 'Nanuta@123!!!!!##'; // Change if necessary

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch cart items for display
$cartItems = [];
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $productIds = implode(',', array_keys($_SESSION['cart']));
    $cartResult = $conn->query("SELECT * FROM products WHERE id IN ($productIds)");

    // Check if query returned any results
    if ($cartResult) {
        while ($row = $cartResult->fetch_assoc()) {
            if (isset($_SESSION['cart'][$row['id']])) {
                $row['quantity'] = $_SESSION['cart'][$row['id']];
                $cartItems[] = $row;
            }
        }
    }
}

// Calculate grand total
$grandTotal = 0;
foreach ($cartItems as $item) {
    $grandTotal += $item['price'] * $item['quantity'];
}

// Retrieve shipping details from session
$shippingDetails = isset($_SESSION['shipping_details']) ? $_SESSION['shipping_details'] : null;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart Summary</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .receipt-container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }
        .summary-item:last-child {
            border-bottom: none;
        }
        .grand-total {
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
            text-align: right;
        }
        .continue-shopping {
            text-align: center;
            margin-top: 20px;
        }
        .continue-shopping a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        .continue-shopping a:hover {
            text-decoration: underline;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .receipt-header h2 {
            margin: 0;
            font-size: 24px;
            color: #28a745;
        }
        .receipt-header p {
            margin: 5px 0;
            color: #555;
        }
        .divider {
            border-top: 2px solid #28a745;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #777;
        }
        .shipping-details {
            margin-top: 20px;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .shipping-details h3 {
            margin-top: 0;
        }
        .payment-section {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .payment-section h3 {
            margin-top: 0;
        }
        .payment-section label {
            display: block;
            margin-bottom: 5px;
        }
 .payment-section input[type="tel"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .payment-section button[type="submit"] {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .payment-section button[type="submit"]:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h2>Receipt</h2>
            <p>Thank you for your purchase!</p>
            <p>Date: <?php echo date("Y-m-d H:i:s"); ?></p>
        </div>
        
       
        
        <?php if ($shippingDetails): ?>
            <div class="shipping-details">
                <h3>Shipping Details</h3>
                <p>Recipient: <?php echo $shippingDetails['firstname']; ?></p>
                <p>Email: <?php echo $shippingDetails['email']; ?></p>
                <p>Address: <?php echo $shippingDetails['address']; ?></p>
                <p>City: <?php echo $shippingDetails['city']; ?></p>
                <p>Phone Number: <?php echo $shippingDetails['phone']; ?></p>
            </div>
        <?php else: ?>
            <p>No shipping details available.</p>
        <?php endif; ?>

        <div class="divider"></div>
        <?php if (!empty($cartItems)): ?>
            <?php foreach ($cartItems as $item): ?>
                <div class="summary-item">
                    <span><?php echo htmlspecialchars($item['title']); ?> (x<?php echo $item['quantity']; ?>)</span>
                    <span>Ksh <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                </div>
            <?php endforeach; ?>
            <div class="grand-total"> Total: Ksh <?php echo number_format($grandTotal, 2); ?></div>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
        
        
        <div class="divider"></div>
        <div class="payment-section">
            <h3>Complete Payment</h3>
            <form action="completepayment.php" method="POST">
                <label for="phone">Enter your phone number:</label>
                <input type="tel" id="phone" name="phone" required>
                <button type="submit">Complete Payment</button>
            </form>
        </div>
        
        <div class="divider"></div>
        <div class="footer">
            <p>Copyright &copy; <?php echo date("Y"); ?> Ebuy</p>
        </div>
        
    </div>
</body>
</html>

