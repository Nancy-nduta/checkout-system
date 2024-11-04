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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    
    // Handle the image upload
    $image = $_FILES['image'];
    $imageName = $image['name'];
    $imageTmpName = $image['tmp_name'];
    $imageSize = $image['size'];
    $imageError = $image['error'];
    
    // Define the allowed file types and max size
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxFileSize = 2 * 1024 * 1024; // 2MB

    // Check for errors
    if ($imageError === 0) {
        if ($imageSize <= $maxFileSize) {
            $imageType = mime_content_type($imageTmpName);
            if (in_array($imageType, $allowedTypes)) {
                // Move the uploaded file to the desired directory
                $uploadDir = 'uploads/'; // Ensure this directory exists and is writable
                $imagePath = $uploadDir . basename($imageName);
                if (move_uploaded_file($imageTmpName, $imagePath)) {
                    // Prepare and bind
                    $stmt = $conn->prepare("INSERT INTO products (title, description, price, img, quantity) VALUES (?, ?, ?, ?,?)");
                    $stmt->bind_param("ssdss", $title, $description, $price, $imagePath,$quantity);

                    if ($stmt->execute()) {
                        echo "<p style='color: green;'>New product added successfully!</p>";
                    } else {
                        echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
                    }

                    $stmt->close();
                } else {
                    echo "<p style='color: red;'>Failed to move uploaded file.</p>";
                }
            } else {
                echo "<p style='color: red;'>Invalid file type. Only JPG, PNG, and GIF are allowed.</p>";
            }
        } else {
            echo "<p style='color: red;'>File size exceeds the maximum limit of 2MB.</p>";
        }
    } else {
        echo "<p style='color: red;'>Error uploading file.</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
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
        }
        form {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="file"] {
            margin-bottom: 15px;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
        p {
            text-align: center;
 }
    </style>
</head>
<body>
    <h1>Add a New Product</h1>
    <form action="addproduct.php" method="POST" enctype="multipart/form-data">
        <label for="title">Product Title:</label>
        <input type="text" id="title" name="title" required>
        
        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>

        <label for="quantity">Quantity:</label>
<input type="number" id="quantity" name="quantity" min="1" required>
        
        <label for="price">Price:</label>
        <input type="number" id="price" name="price" step="0.01" required>
        
        <label for="image">Image:</label>
        <input type="file" id="image" name="image" required>
        
        <input type="submit" value="Add Product">
    </form>
</body>
</html>