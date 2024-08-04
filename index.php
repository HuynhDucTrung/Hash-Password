<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}
require_once "database.php";

// Handling form submission for adding a product
if (isset($_POST["add_product"])) {
    $productName = htmlspecialchars($_POST["product_name"]);
    $productDescription = htmlspecialchars($_POST["product_description"]);
    $productPrice = $_POST["product_price"];
    
    $errors = array();
    
    if (empty($productName) || empty($productDescription) || empty($productPrice)) {
        array_push($errors, "All fields are required");
    }
    
    if (!is_numeric($productPrice) || $productPrice <= 0) {
        array_push($errors, "Price must be a positive number");
    }
    
    if (count($errors) == 0) {
        $sql = "INSERT INTO products (name, description, price) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssd", $productName, $productDescription, $productPrice);
        if (mysqli_stmt_execute($stmt)) {
            $successMessage = "Product added successfully";
        } else {
            $errorMessage = "Error adding product";
        }
    }
}

// Fetching products from database
$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title>Product Management</title>
</head>
<body>
    <div class="container">
        <h1>Welcome to Product Management</h1>
        <p>Hello, <?php echo htmlspecialchars($_SESSION["user"]); ?>!</p>
        <a href="logout.php" class="btn btn-warning">Logout</a>
        
        <h2>Add Product</h2>
        <?php
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<div class='alert alert-danger'>" . htmlspecialchars($error) . "</div>";
            }
        }
        if (isset($successMessage)) {
            echo "<div class='alert alert-success'>" . htmlspecialchars($successMessage) . "</div>";
        }
        if (isset($errorMessage)) {
            echo "<div class='alert alert-danger'>" . htmlspecialchars($errorMessage) . "</div>";
        }
        ?>
        <form action="index.php" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="product_name" placeholder="Product Name" required>
            </div>
            <div class="form-group">
                <textarea class="form-control" name="product_description" placeholder="Product Description" required></textarea>
            </div>
            <div class="form-group">
                <input type="number" step="0.01" class="form-control" name="product_price" placeholder="Product Price" required>
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Add Product" name="add_product">
            </div>
        </form>
        
        <h2>Product List</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['id']); ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['description']); ?></td>
                    <td><?php echo htmlspecialchars($product['price']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
