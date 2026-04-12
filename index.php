<?php

    include 'connection.php';

    $query = "SELECT * FROM items";
    $result = mysqli_query($conn, $query);

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
                    <td>Php <?= number_format($row['unit_price'], 2) ?></td>
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