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

// Handle product deletion
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Prepare the delete statement
    $deleteQuery = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $id);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect to manage products page after deletion
        header("Location: manageproducts.php?success=1");
        exit();
    } else {
        echo "Error deleting record: " . $stmt->error;
    }

    $stmt->close();
} else {
    die("No product ID specified.");
}

$conn->close();
?>