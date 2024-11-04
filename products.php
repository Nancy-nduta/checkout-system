<?php
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

// Handle adding to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Insert the product into the cart
    $stmt = $conn->prepare("INSERT INTO cart (product_id, quantity) VALUES (?, ?)");
    $stmt->bind_param("ii", $productId, $quantity);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Product added to cart successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// Fetch products from the database
$result = $conn->query("SELECT * FROM products");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
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
        .product-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .product {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px; /* Fixed width for uniformity */
            transition: transform 0.2s;
        }
        .product:hover {
            transform: scale(1.03); /* Slight zoom effect on hover */
        }
        .product img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }
        .product h2 {
            margin: 10px 0;
            font-size: 20px;
            color: #333;
        }
        .product p {
            margin: 10px 0;
            color: #555;
        }
        .product .price {
            font-weight: bold;
            font-size: 18px;
            color: #28a745;
        }
        .product form {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .product input[type="number"] {
            width: 60px;
            margin-right: 10px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .product input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .product input[type="submit"]:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <h1>Products</h1>
    <div class="product-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($product = $result->fetch_assoc()): ?>
                <div class="product">
                    <img src="<?php echo $product['img']; ?>" alt="<?php echo $product['title']; ?>">
                    <h2><?php echo $product['title']; ?></h2>
                    <p><?php echo $product['description']; ?></p>
                    <p class="price">Price: Ksh<?php echo number_format($product['price'], 2); ?></p>
                    <form action="cart.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="number" name="quantity" value="1" min="1" required>
                        <input type="submit" name="add_to_cart" value="Add to Cart">
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p >No products available.</p>
        <?php endif; ?>
    </div>
</body>
</html>