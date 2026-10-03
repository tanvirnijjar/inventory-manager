<?php
include "db.php";

$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM products");
$row = mysqli_fetch_assoc($result);
$totalProducts = $row['total'];

$result = mysqli_query($conn, "SELECT SUM(quantity) AS stock FROM products");
$row = mysqli_fetch_assoc($result);
$totalStock = $row['stock'] ?? 0;

$result = mysqli_query($conn, "SELECT SUM(quantity * price) AS value FROM products");
$row = mysqli_fetch_assoc($result);
$totalValue = $row['value'] ?? 0;

$lowStock = mysqli_query(
    $conn,
    "SELECT * FROM products WHERE quantity <= 5 ORDER BY quantity ASC"
);

$recentProducts = mysqli_query(
    $conn,
    "SELECT * FROM products ORDER BY id DESC LIMIT 5"
);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Inventory Manager</title>
    <link rel="stylesheet" href="style.css">

    <style>
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
        }

        .dashboard-header p,
        .section-description {
            color: #6b7280;
        }

        .header-buttons {
            display: flex;
            gap: 8px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
        }

        .card-icon {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .card h3 {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .card-value {
            font-size: 27px;
            font-weight: bold;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .dashboard-section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
        }

        .product-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 20px;
        }

        .product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            padding: 13px;
            background: #f9fafb;
            border-radius: 8px;
        }

        .product-info strong {
            display: block;
            margin-bottom: 4px;
        }

        .product-info span {
            color: #6b7280;
            font-size: 13px;
        }

        .product-price {
            font-weight: 600;
            white-space: nowrap;
        }

        .stock-warning,
        .stock-normal {
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .stock-warning {
            color: #b91c1c;
            background: #fee2e2;
        }

        .stock-normal {
            color: #15803d;
            background: #dcfce7;
        }

        .view-all {
            display: inline-block;
            margin-top: 18px;
            color: #2563eb;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .empty-message {
            text-align: center;
            padding: 25px;
            color: #6b7280;
        }

        @media (max-width: 800px) {
            .dashboard-header {
                flex-direction: column;
                align-items: stretch;
            }

            .header-buttons {
                justify-content: center;
            }

            .cards,
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 500px) {
            .header-buttons {
                flex-direction: column;
            }

            .product-item {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>

<nav class="navbar">
    <h1>📦 Inventory Manager</h1>

    <div>
        <a href="products.php" class="btn">Products</a>
    </div>
</nav>

<div class="container">

    <div class="dashboard-header">
        <div>
            <h2>Dashboard</h2>
            <p>Here's what's happening with your inventory.</p>
        </div>

        <div class="header-buttons">
            <a href="products.php" class="btn">📦 View Products</a>
            <a href="add_product.php" class="btn">+ Add Product</a>
        </div>
    </div>

    <div class="cards">

        <div class="card">
            <div class="card-icon">📦</div>
            <h3>Total Products</h3>
            <div class="card-value"><?php echo $totalProducts; ?></div>
        </div>

        <div class="card">
            <div class="card-icon">📊</div>
            <h3>Total Stock</h3>
            <div class="card-value"><?php echo $totalStock; ?></div>
        </div>

        <div class="card">
            <div class="card-icon">💰</div>
            <h3>Inventory Value</h3>
            <div class="card-value">
                ₹<?php echo number_format($totalValue, 2); ?>
            </div>
        </div>

    </div>

    <div class="dashboard-grid">

        <div class="dashboard-section">

            <h3>⚠️ Low Stock</h3>

            <p class="section-description">
                Products that need your attention.
            </p>

            <div class="product-list">

                <?php if (mysqli_num_rows($lowStock) > 0): ?>

                    <?php while ($product = mysqli_fetch_assoc($lowStock)): ?>

                        <div class="product-item">

                            <div class="product-info">
                                <strong>
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </strong>

                                <span>
                                    <?php echo htmlspecialchars($product['category']); ?>
                                </span>
                            </div>

                            <span class="stock-warning">
                                <?php echo $product['quantity']; ?> left
                            </span>

                        </div>

                    <?php endwhile; ?>

                <?php else: ?>

                    <div class="empty-message">
                        🎉<br><br>
                        No low-stock products!
                    </div>

                <?php endif; ?>

            </div>

            <a href="products.php" class="view-all">
                View all products →
            </a>

        </div>

        <div class="dashboard-section">

            <h3>🕐 Recent Products</h3>

            <p class="section-description">
                Your latest additions.
            </p>

            <div class="product-list">

                <?php if (mysqli_num_rows($recentProducts) > 0): ?>

                    <?php while ($product = mysqli_fetch_assoc($recentProducts)): ?>

                        <div class="product-item">

                            <div class="product-info">

                                <strong>
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </strong>

                                <span>
                                    <?php echo htmlspecialchars($product['category']); ?>
                                    ·
                                    <?php echo $product['quantity']; ?> units
                                </span>

                            </div>

                            <div class="product-price">
                                ₹<?php echo number_format($product['price'], 2); ?>
                            </div>

                        </div>

                    <?php endwhile; ?>

                <?php else: ?>

                    <div class="empty-message">
                        📦<br><br>
                        No products yet.
                    </div>

                <?php endif; ?>

            </div>

            <a href="products.php" class="view-all">
                Manage products →
            </a>

        </div>

    </div>

</div>

</body>
</html>