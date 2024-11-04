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

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle adding to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Check if the product is already in the cart
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity; // Increase quantity
        // Update database
        $stmt = $conn->prepare("UPDATE cartitems SET quantity = quantity + ? WHERE product_id = ?");
        $stmt->bind_param("ii", $quantity, $productId);
    } else {
        $_SESSION['cart'][$productId] = $quantity; // Add new product
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO cartitems (product_id, quantity) VALUES (?, ?)");
        $stmt->bind_param("ii", $productId, $quantity);
    }
    $stmt->execute();
    $stmt->close();

    // Redirect to cart page
    header("Location: cart.php");
    exit();
}

// Fetch products from the database
$result = $conn->query("SELECT * FROM products");

// Fetch cart items for display
$cartItems = [];
if (!empty($_SESSION['cart'])) {
    $productIds = implode(',', array_keys($_SESSION['cart']));
    $cartResult = $conn->query("SELECT * FROM products WHERE id IN ($productIds)");
    while ($row = $cartResult->fetch_assoc()) {
        $row['quantity'] = $_SESSION['cart'][$row['id']];
        $cartItems[] = $row;
    }
}

// Handle quantity updates via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    // Update the quantity in the database
    $stmt = $conn->prepare("UPDATE cartitems SET quantity = ? WHERE product_id = ?");
    $stmt->bind_param("ii", $quantity, $productId);
    $stmt->execute();

    // Calculate new grand total for response
    $result = $conn->query("SELECT SUM(quantity * price) AS grand_total FROM cartitems INNER JOIN products ON cartitems.product_id = products.id");
    $row = $result->fetch_assoc();
    echo number_format($row['grand_total'], 2);
    $stmt->close();
    exit(); // Exit after handling AJAX request
}

// Handle removing items from the cart via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    $productId = $_POST['product_id'];

    // Remove the item from the session
    unset($_SESSION['cart'][$productId]);

    // Remove the item from the database
    $stmt = $conn->prepare("DELETE FROM cartitems WHERE product_id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $stmt->close();

    // Calculate new grand total for response
    $result = $conn->query("SELECT SUM(cartitems.quantity * products.price) AS grand_total FROM cartitems INNER JOIN products ON cartitems.product_id = products.id");
    $row = $result->fetch_assoc();
    echo number_format($row['grand_total'], 2);
    exit(); // Exit after handling AJAX request
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .cart-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .cart-item {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px; /* Fixed width for uniformity */
            margin: 10px 0;
        }
        .cart-item h2 {
            margin: 10px 0;
            font-size: 20px;
            color: #333;
        }
        .cart-item p {
            margin: 10px 0;
            color: #555;
        }
        .cart-item .price {
            font-weight: bold;
            font-size: 18px;
            color: #28a745;
        }
        .cart-item .quantity {
            margin: 10px 0;
        }
        .checkout-button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 20px;
        }
        .checkout-button:hover {
            background-color: #218838;
        }
        .grand-total {
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
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
    </style>
    <script>
      function updatePrice(itemPrice, quantityInput) {
            const priceDisplay = document.getElementById('total-price-' + quantityInput.dataset.productId);
            const quantity = quantityInput.value;
            const totalPrice = itemPrice * quantity;
            priceDisplay.innerText = 'Ksh ' + totalPrice.toFixed(2);
            updateGrandTotal(quantityInput.dataset.productId, quantity);
        }

        function updateGrandTotal(productId, quantity) {
            const xhr = new XMLHttpRequest();
           xhr.open("POST", "update-cart.php", true);
          xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
           xhr.onreadystatechange = function () {
               if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById('grand-total').innerText = 'Ksh ' + xhr.responseText;
                }
           };
          xhr.send("product_id=" + productId + "&quantity=" + quantity);
       }
       function removeItem(productId) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "", true); // Send to the same page
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Remove the item from the DOM
            document.getElementById('cart-item-' + productId).remove();
            // Update the grand total
            document.getElementById('grand-total').innerText = 'Ksh ' + xhr.responseText;
        }
    };
    xhr.send("remove_item=true&product_id=" + productId); // Include the product ID in the request
}
</script>
</head>
<body>
    <h1>Your Cart</h1>
    <div class="cart-container">
        <form action="checkout.php" method="POST">
            <?php if (!empty($cartItems)): ?>
                <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item" id="cart-item-<?php echo $item['id']; ?>">
                        <h2><?php echo htmlspecialchars($item['title']); ?></h2>
                        <p class="quantity">
                            Quantity: 
                            <input type="number" name="quantities[<?php echo $item['id']; ?>]" 
                                   value="<?php echo htmlspecialchars($item['quantity']); ?>" 
                                   min="0" 
                                   oninput="updatePrice(<?php echo $item['price']; ?>, this)" 
                                   data-product-id="<?php echo $item['id']; ?>" 
                                   required>
                        </p>
                        <p class="price">Price: <span id="total-price-<?php echo $item['id']; ?>" class="total-price">
                            Ksh <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </p>
                        <button type="button" onclick="removeItem(<?php echo $item['id']; ?>)">Remove</button>
                    </div>
                <?php endforeach; ?>
                <p class="grand-total">Grand Total: <span id="grand-total">
                    Ksh <?php 
                        $grandTotal = 0;
                        foreach ($cartItems as $item) {
                            $grandTotal += $item['price'] * $item['quantity'];
                        }
                        echo number_format($grandTotal, 2);
                    ?></span></p>
                    <input type="submit" class="checkout-button" value="Proceed to Checkout">
               
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </form>
    </div>
    <div class="continue-shopping">
        <a href="index.php">Continue Shopping</a>
    </div>
</body>
</html>

