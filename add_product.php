<?php
include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $category = trim($_POST["category"]);
    $quantity = (int) $_POST["quantity"];
    $price = (float) $_POST["price"];

    if ($name == "" || $category == "" || $quantity < 0 || $price < 0) {
        $message = "Please enter valid product details.";
    } else {

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO products (name, category, quantity, price)
             VALUES (?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssid",
            $name,
            $category,
            $quantity,
            $price
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php");
            exit;
        } else {
            $message = "Error adding product.";
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Product</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<nav class="navbar">

    <h1>📦 Inventory Manager</h1>

    <a href="index.php" class="btn">
        ← Dashboard
    </a>

</nav>

<div class="container">

    <div class="section">

        <h2>Add New Product</h2>

        <br>

        <?php if ($message != ""): ?>

            <p style="color: red;">
                <?php echo $message; ?>
            </p>

            <br>

        <?php endif; ?>


        <form method="POST">

            <label>Product Name</label>

            <br><br>

            <input
                type="text"
                name="name"
                placeholder="Enter product name"
                required
            >

            <br><br>


            <label>Category</label>

            <br><br>

            <input
                type="text"
                name="category"
                placeholder="Enter category"
                required
            >

            <br><br>


            <label>Quantity</label>

            <br><br>

            <input
                type="number"
                name="quantity"
                min="0"
                placeholder="Enter quantity"
                required
            >

            <br><br>


            <label>Price</label>

            <br><br>

            <input
                type="number"
                name="price"
                min="0"
                step="0.01"
                placeholder="Enter price"
                required
            >

            <br><br>

            <button type="submit" class="btn">
                Add Product
            </button>

        </form>

    </div>

</div>

</body>

</html>