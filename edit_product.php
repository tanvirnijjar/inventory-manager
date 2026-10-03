<?php
include "db.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$id = (int) $_GET['id'];

$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    die("Product not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $category = trim($_POST["category"]);
    $quantity = (int) $_POST["quantity"];
    $price = (float) $_POST["price"];

    if ($name != "" && $category != "" && $quantity >= 0 && $price >= 0) {

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE products
             SET name = ?, category = ?, quantity = ?, price = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssidi",
            $name,
            $category,
            $quantity,
            $price,
            $id
        );

        mysqli_stmt_execute($stmt);

        header("Location: products.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Product</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<nav class="navbar">

    <h1>📦 Inventory Manager</h1>

    <a href="products.php" class="btn">
        ← Products
    </a>

</nav>

<div class="container">

    <div class="section">

        <h2>Edit Product</h2>

        <br>

        <form method="POST">

            <label>Product Name</label>
            <br><br>

            <input
                type="text"
                name="name"
                value="<?php echo htmlspecialchars($product['name']); ?>"
                required
            >

            <br><br>

            <label>Category</label>
            <br><br>

            <input
                type="text"
                name="category"
                value="<?php echo htmlspecialchars($product['category']); ?>"
                required
            >

            <br><br>

            <label>Quantity</label>
            <br><br>

            <input
                type="number"
                name="quantity"
                min="0"
                value="<?php echo $product['quantity']; ?>"
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
                value="<?php echo $product['price']; ?>"
                required
            >

            <br><br>

            <button type="submit" class="btn">
                Update Product
            </button>

        </form>

    </div>

</div>

</body>
</html>