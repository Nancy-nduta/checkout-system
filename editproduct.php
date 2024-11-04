<?php
session_start();

// Database connection
$host = 'localhost'; // Change if necessary
$db = 'cart'; // Change to your database name
$user = 'root'; // Change if necessary
$pass = 'Nanuta@123!!!!!##'; // Change if necessary

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for editing a product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $id = intval($_POST['id']);
    $title = $conn->real_escape_string($_POST['title']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $description = $conn->real_escape_string($_POST['description']);

    $updateQuery = "UPDATE products SET title = '$title', price = $price, quantity = $quantity, description = '$description' WHERE id = $id";

    // Redirect to manage products page after update
    if ($conn->query($updateQuery) === TRUE) {
        // Redirect to manage products page after update
        header("Location: manageproducts.php?success=1");
         exit();
      } else {
          echo "Error updating record: " . $conn->error;
     }
}

// Fetch the product to edit
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM products WHERE id = $id");
    $product = $result->fetch_assoc();
    if (!$product) {
        die("Product not found.");
    }
} else {
    die("No product ID specified.");
}

    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: auto;
        }
        input[type="text"], input[type="number"], textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <h1>Edit Product</h1>
    <form action="editproduct.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($product['title']); ?>" required>

        <label for="price">Price:</label>
        <input type="number" name="price" id="price" value="<?php echo htmlspecialchars($product['price']); ?>" step="0.01" required>
        <label for="quantity">Quantity:</label>

        <input type="number" name="quantity" id="quantity" value="<?php echo htmlspecialchars($product['quantity']); ?>" step="0.02" required>

        <label for="description">Description:</label>
        <textarea name="description" id="description" rows="4" required><?php echo htmlspecialchars($product['description']); ?></textarea>

        <input type="submit" name="update_product" value="Update Product">
    </form>
</body>
</html>

<?php
$conn->close();
?>