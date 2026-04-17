<?php

    include 'connection.php';
    include 'validations.php';

    // s string
    // i integer
    // d decimal/float

    if (isset($_GET['search']) && strlen(trim($_GET['search'])) > 0) {
        $stmt = $conn->prepare("SELECT * FROM items WHERE item_name LIKE ? OR manufacturer LIKE ? OR item_id LIKE ? ORDER BY date_added DESC");
        $search_param = '%' . sanitize_input($_GET['search']) . '%';
        $stmt->bind_param("sss", $search_param, $search_param, $search_param);
        $stmt->execute();
        $result = $stmt->get_result();

        // No prepared statement version (vulnerable to SQL injection):
        // $search = validate_input($_GET['search']);
        // $query = "SELECT * FROM items WHERE item_name LIKE '%$search%' OR manufacturer LIKE '%$search%' OR item_id LIKE '%$search%'";
        // $result = mysqli_query($conn, $query);
    } else {
        $query = "SELECT * FROM items ORDER BY date_added DESC";
        $result = mysqli_query($conn, $query);
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PSE</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h2>Inventory Items</h2>

    <a href="create.php">Add Item</a> |
    <a href="transaction.php">Order Item</a>

    <br><br>

    <form action="index.php" method="GET">
        <input type="text" name="search" placeholder="Search Items...">
        <input type="submit" value="Search">
    </form>

    <br><br>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Item ID</th>
                <th>Item Name</th>
                <th>Manufacturer</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Date Added</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row['item_id'] ?></td>
                    <td><?= $row['item_name'] ?></td>
                    <td><?= $row['manufacturer'] ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td style="text-align: right;">Php <?= number_format($row['unit_price'], 2) ?></td>
                    <td><?= $row['date_added'] ?></td>
                    <td>
                        <form action="edit.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $row['item_id'] ?>">
                            <input type="submit" value="Edit">
                        </form> |

                        <form action="delete.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this item?')">
                            <input type="hidden" name="id" value="<?= $row['item_id'] ?>">
                            <input type="submit" value="Delete">
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center;">No items found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
</body>
</html>