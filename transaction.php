<?php

    include 'connection.php';

    $query = "SELECT item_name FROM items";
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
    <br><br>
    <a href="index.php">Home</a>
    <br><br>

    <h2>Order Item</h2>

    <form action="checkout.php" method="POST" name="order">
        <label for="item_name">Item Name:</label><br>
        <select id="item_name" name="item_name" required>
            <option value="">Select an item</option>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <option value="<?= $row['item_name'] ?>"><?= $row['item_name'] ?></option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select><br><br>

        <label for="quantity">Quantity:</label><br>
        <input type="number" step="1" id="quantity" name="quantity" min="1" required><br><br>

        <label for="order_date">Choose Date:</label><br>
        <input type="date" id="order_date" name="order_date" min="<?= date('Y-m-d') ?>" required><br><br>

        <label for="mod">Mode of Payment:</label><br>
        <select id="mod" name="mod" required>
            <option value="">Select a payment method</option>
            <option value="Cash">Cash</option>
            <option value="Debit/Credit Card">Debit/Credit Card</option>
            <option value="Check">Check</option>
        </select><br><br>

        <input type="submit" name="order" value="Order Item">
    </form>
</body>
</html>