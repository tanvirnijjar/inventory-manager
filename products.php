<?php

include "db.php";

/* =========================
   SEARCH & FILTER
========================= */

$search = "";
$category = "";


if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}


if (isset($_GET['category'])) {
    $category = trim($_GET['category']);
}


/* =========================
   BUILD QUERY
========================= */

$sql = "SELECT * FROM products WHERE 1=1";

$params = [];
$types = "";


/* Search */

if ($search !== "") {

    $sql .= " AND (name LIKE ? OR category LIKE ?)";

    $searchValue = "%" . $search . "%";

    $params[] = $searchValue;
    $params[] = $searchValue;

    $types .= "ss";
}


/* Category */

if ($category !== "") {

    $sql .= " AND category = ?";

    $params[] = $category;

    $types .= "s";
}


$sql .= " ORDER BY id DESC";


/* =========================
   PREPARE QUERY
========================= */

$stmt = mysqli_prepare($conn, $sql);


if (!empty($params)) {

    mysqli_stmt_bind_param(
        $stmt,
        $types,
        ...$params
    );
}


mysqli_stmt_execute($stmt);


$result = mysqli_stmt_get_result($stmt);


/* =========================
   GET CATEGORIES
========================= */

$categoryResult = mysqli_query(
    $conn,
    "SELECT DISTINCT category
     FROM products
     ORDER BY category ASC"
);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Products - Inventory Manager
    </title>

    <link
        rel="stylesheet"
        href="style.css"
    >


    <style>

        /* =========================
           SEARCH AREA
        ========================= */

        .search-area {

            background: #f9fafb;

            padding: 18px;

            border-radius: 10px;

            margin-bottom: 20px;

        }


        .search-form {

            display: grid;

            grid-template-columns: 2fr 1fr auto auto;

            gap: 10px;

            align-items: center;

        }


        .search-form input,
        .search-form select {

            width: 100%;

            padding: 11px;

            border: 1px solid #d1d5db;

            border-radius: 7px;

            background: white;

            font-size: 14px;

        }


        .search-btn {

            background: #2563eb;

            color: white;

            border: none;

            padding: 11px 18px;

            border-radius: 7px;

            cursor: pointer;

            font-weight: 600;

        }


        .search-btn:hover {

            background: #1d4ed8;

        }


        .reset-btn {

            background: #6b7280;

            color: white;

            text-decoration: none;

            padding: 11px 18px;

            border-radius: 7px;

            font-weight: 600;

            text-align: center;

        }


        .reset-btn:hover {

            background: #4b5563;

        }


        /* =========================
           RESULTS INFO
        ========================= */

        .results-info {

            color: #6b7280;

            font-size: 14px;

            margin-bottom: 15px;

        }


        /* =========================
           TABLE
        ========================= */

        .table-container {

            width: 100%;

            overflow-x: auto;

            margin-top: 20px;

            border-radius: 10px;

            -webkit-overflow-scrolling: touch;

        }


        table {

            width: 100%;

            min-width: 780px;

            border-collapse: collapse;

            background: white;

        }


        th {

            background: #111827;

            color: white;

            padding: 11px 10px;

            text-align: left;

            font-size: 13px;

            white-space: nowrap;

        }


        td {

            padding: 11px 10px;

            border-bottom: 1px solid #e5e7eb;

            font-size: 13px;

            white-space: nowrap;

        }


        tbody tr:hover {

            background: #f9fafb;

        }


        /* =========================
           STOCK STATUS
        ========================= */

        .low-stock,
        .normal-stock {

            display: inline-flex;

            align-items: center;

            padding: 6px 9px;

            border-radius: 20px;

            font-size: 12px;

            font-weight: 600;

            white-space: nowrap;

        }


        .low-stock {

            color: #b91c1c;

            background: #fee2e2;

        }


        .normal-stock {

            color: #15803d;

            background: #dcfce7;

        }


        /* =========================
           ACTIONS
        ========================= */

        .actions {

            display: flex;

            align-items: center;

            gap: 6px;

            white-space: nowrap;

        }


        .edit-btn,
        .delete-btn {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding: 7px 9px;

            border-radius: 7px;

            text-decoration: none;

            font-size: 12px;

            font-weight: 600;

            transition: 0.2s ease;

        }


        .edit-btn {

            background: #16a34a;

            color: white;

        }


        .edit-btn:hover {

            background: #15803d;

        }


        .delete-btn {

            background: #dc2626;

            color: white;

        }


        .delete-btn:hover {

            background: #b91c1c;

        }


        /* =========================
           EMPTY STATE
        ========================= */

        .empty-state {

            text-align: center;

            padding: 40px;

            color: #6b7280;

        }


        .empty-state h3 {

            color: #1f2937;

            margin-bottom: 10px;

        }


        /* =========================
           MOBILE
        ========================= */

        @media (max-width: 800px) {

            .search-form {

                grid-template-columns: 1fr;

            }


            .actions {

                flex-direction: column;

                align-items: stretch;

            }


            .edit-btn,
            .delete-btn {

                width: 100%;

            }

        }

    </style>

</head>


<body>


<!-- =========================
     NAVBAR
========================= -->

<nav class="navbar">


    <h1>
        📦 Inventory Manager
    </h1>


    <div>

        <a
            href="index.php"
            class="btn"
        >
            Dashboard
        </a>


        <a
            href="add_product.php"
            class="btn"
        >
            + Add Product
        </a>

    </div>


</nav>



<!-- =========================
     MAIN CONTENT
========================= -->

<div class="container">


    <div class="welcome">


        <h2>
            Products
        </h2>


        <p>
            View and manage all your products.
        </p>


    </div>



    <div class="section">


        <!-- =========================
             SEARCH & FILTER
        ========================= -->

        <div class="search-area">


            <form
                method="GET"
                class="search-form"
            >


                <!-- Search -->

                <input
                    type="text"
                    name="search"
                    placeholder="🔍 Search product or category..."
                    value="<?php echo htmlspecialchars($search); ?>"
                >


                <!-- Category -->

                <select name="category">

                    <option value="">
                        All Categories
                    </option>


                    <?php while ($cat = mysqli_fetch_assoc($categoryResult)): ?>


                        <option
                            value="<?php echo htmlspecialchars($cat['category']); ?>"

                            <?php

                            if (
                                $category === $cat['category']
                            ) {

                                echo "selected";

                            }

                            ?>
                        >

                            <?php

                            echo htmlspecialchars(
                                $cat['category']
                            );

                            ?>

                        </option>


                    <?php endwhile; ?>


                </select>


                <!-- Search button -->

                <button
                    type="submit"
                    class="search-btn"
                >

                    🔍 Search

                </button>


                <!-- Reset -->

                <a
                    href="products.php"
                    class="reset-btn"
                >

                    Reset

                </a>


            </form>


        </div>



        <!-- =========================
             RESULTS INFO
        ========================= -->

        <div class="results-info">

            <?php

            $resultCount =
                mysqli_num_rows($result);

            echo $resultCount;

            ?>

            product(s) found.

        </div>



        <!-- =========================
             PRODUCT TABLE
        ========================= -->

        <div class="table-container">


            <table>


                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Product</th>

                        <th>Category</th>

                        <th>Quantity</th>

                        <th>Price</th>

                        <th>Total Value</th>

                        <th>Stock Status</th>

                        <th>Actions</th>

                    </tr>

                </thead>



                <tbody>


                <?php if ($resultCount > 0): ?>


                    <?php while ($product = mysqli_fetch_assoc($result)): ?>


                        <tr>


                            <!-- ID -->

                            <td>

                                <?php

                                echo $product['id'];

                                ?>

                            </td>



                            <!-- Product -->

                            <td>

                                <strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $product['name']
                                    );

                                    ?>

                                </strong>

                            </td>



                            <!-- Category -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $product['category']
                                );

                                ?>

                            </td>



                            <!-- Quantity -->

                            <td>

                                <?php

                                echo $product['quantity'];

                                ?>

                            </td>



                            <!-- Price -->

                            <td>

                                ₹<?php

                                echo number_format(
                                    $product['price'],
                                    2
                                );

                                ?>

                            </td>



                            <!-- Total Value -->

                            <td>

                                <strong>

                                    ₹<?php

                                    $totalValue =
                                        $product['quantity']
                                        *
                                        $product['price'];

                                    echo number_format(
                                        $totalValue,
                                        2
                                    );

                                    ?>

                                </strong>

                            </td>



                            <!-- Stock -->

                            <td>


                                <?php

                                if (
                                    $product['quantity'] <= 5
                                ):

                                ?>

                                    <span class="low-stock">

                                        ⚠️ Low Stock

                                    </span>


                                <?php else: ?>


                                    <span class="normal-stock">

                                        ✓ In Stock

                                    </span>


                                <?php endif; ?>


                            </td>



                            <!-- Actions -->

                            <td>


                                <div class="actions">


                                    <a
                                        href="edit_product.php?id=<?php echo $product['id']; ?>"
                                        class="edit-btn"
                                    >

                                        ✏️ Edit

                                    </a>


                                    <a
                                        href="delete_product.php?id=<?php echo $product['id']; ?>"
                                        class="delete-btn"

                                        onclick="
                                            return confirm(
                                                'Are you sure you want to delete this product?'
                                            );
                                        "
                                    >

                                        🗑️ Delete

                                    </a>


                                </div>


                            </td>


                        </tr>


                    <?php endwhile; ?>


                <?php else: ?>


                    <tr>


                        <td
                            colspan="8"
                            class="empty-state"
                        >


                            <h3>
                                📦 No products found
                            </h3>


                            <p>
                                Try a different search or category.
                            </p>


                            <br>


                            <a
                                href="products.php"
                                class="btn"
                            >

                                Clear Filters

                            </a>


                        </td>


                    </tr>


                <?php endif; ?>


                </tbody>


            </table>


        </div>


    </div>


</div>


</body>

</html>