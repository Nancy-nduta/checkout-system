<?php
session_start();

// Database connection
$host = 'localhost'; // Change if necessary
$db = 'checkout'; // Your database name
$user = 'root'; // Change if necessary
$pass = 'Nanuta@123!!!!!##'; // Change if necessary

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: products.php"); // Redirect to products if cart is empty
    exit();
}

// Initialize variables to avoid undefined index warnings
$shippingFirstName = $shippingEmail = $shippingAddress = $shippingCity = $shippingPhone = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the form fields are set before accessing them
    if (isset($_POST['firstname'], $_POST['email'], $_POST['address'], $_POST['city'], $_POST['phone'])) {
        // Process the shipping details
        $shippingFirstName = $_POST['firstname'];
        $shippingEmail = $_POST['email'];
        $shippingAddress = $_POST['address'];
        $shippingCity = $_POST['city'];
        $shippingPhone = $_POST['phone'];

        // Store shipping details in session
        $_SESSION['shipping_details'] = [
            'firstname' => $shippingFirstName,
            'email' => $shippingEmail,
            'address' => $shippingAddress,
            'city' => $shippingCity,
            'phone' => $shippingPhone,
        ];

        // Insert order into the database
        $stmt = $conn->prepare("INSERT INTO orders (firstname, email, address, city, phone) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $shippingFirstName, $shippingEmail, $shippingAddress, $shippingCity, $shippingPhone);

        if ($stmt->execute()) {
            // Redirect to a confirmation page (you can create a confirmation.php)
            header("Location: cartsummary.php");
            exit();
        } else {
            echo "Error: " . $stmt->error; // Handle error
        }

        $stmt->close();
    } else {
        echo "Please fill out all fields."; // Handle missing fields
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
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
        .checkout-container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .submit-button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
        }
        .submit-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <h1>Checkout</h1>
    <div class="checkout-container">
        <form action="" method="POST"> <!-- Submit to the same page -->
            <div class="form-group">
                <label for="firstname">First Name</label>
                <input type="text" id="firstname" name="firstname" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email " id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" required>
            </div>
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" required>
            </div>
            <button type="submit" class="submit-button">Complete Order</button>
        </form>
    </div>
</body>
</html>

