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
    $stmt = $conn->prepare("INSERT INTO cartitems (product_id, quantity) VALUES (?, ?)");
    $stmt->bind_param("ii", $productId, $quantity);

    if ($stmt->execute()) {
        // Redirect to cart.php after adding to cart
        header("Location: cart.php");
        exit; // Make sure to exit after the redirect
    } else {
        $message = "<p style='color: red;'>Error: " . $stmt->error . "</p>";
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
    <title>E-Shop - Your One-Stop Shop</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #007bff;
            color: white;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
        }
        .search-bar {
            padding: 10px;
            width: 300px;
        }
        nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
        }
        nav ul li {
            display: inline;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
        }
        .shopping-cart {
            font-size: 24px;
        }
        .banner {
            text-align: center;
            margin: 20px 0;
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
            width: calc(25% - 20px); /* 4 products in a row with gap adjustment */
            transition: transform 0.2s;
            box-sizing: border-box; /* Ensure padding is included in width */
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
            color: white ;
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
    <header>
        <div class="logo">E-Shop</div>
        <input type="text" placeholder="Search for products..." class="search-bar">
        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">Products</a></li>
                <li><a href="#">Deals</a></li>
                <li><a href="#">Contact</a></li>
                <li><a href="#">Signup</a></li>
                <li><a href="#">Login</a></li>
                <div class="shopping-cart">
                    <a href="cart.php"> <!-- Updated link to cart.php -->
                        <i class="fas fa-shopping-cart"></i> <!-- Font Awesome -->
                    </a>
                </div>
            </ul>
        </nav>
    </header>

    <main>
        <section class="banner">
            <h1>Welcome to E-Shop</h1>
            <p>Your one-stop shop for everything!</p>
        </section>

        <section class="products">
            <h2>Featured Products</h2>
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
                    <p>No products available.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 E-Shop. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
