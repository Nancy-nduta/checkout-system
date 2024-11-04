
<?php
// Database connection
$servername = "localhost"; // Change if your database server is different
$username = "root"; // Your database username
$password = "Nanuta@123!!!!!##"; // Your database password
$dbname = "checkout"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve data from the form
$firstname = $_POST['firstname'];
$email = $_POST['email'];
$address = $_POST['address'];
$city = $_POST['city'];
$phone = $_POST['phone'];

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO orders (firstname, email, address, city, phone) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $firstname, $email, $address, $city, $phone);

// Execute the statement
if ($stmt->execute()) {
     // Get the last inserted order ID
        $orderId = $stmt->insert_id;
       // Redirect to the summary page with the order ID
      header("Location: order-summary.php?order_id=" . $orderId);
     exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
   $conn->close();
    ?>