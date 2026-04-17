<?php

    session_start();
    include 'connection.php';
    include 'validations.php';

    $query = "SELECT item_name FROM items WHERE quantity > 0";
    $result = mysqli_query($conn, $query);

    $errors = [];
    $quantity = "";
    $order_date = "";
    $mod = "";
    $stock = 0;

    if (isset($_POST['order'])) {
        $quantity   = sanitize_input($_POST['quantity']);
        $order_date = sanitize_input($_POST['order_date']);
        $mod        = sanitize_input($_POST['mod']);
        $item_name  = sanitize_input($_POST['item_name']);

        $stmt = $conn->prepare("SELECT quantity FROM items WHERE item_name = ?");
        $stmt->bind_param("s", $item_name);
        $stmt->execute();
        $stock_result = $stmt->get_result();

        if (mysqli_num_rows($stock_result) > 0) {
            $row = mysqli_fetch_assoc($stock_result);
            $stock = $row['quantity'];
        }

        if (is_empty($quantity)) {
            $errors['quantity'] = "Quantity is required.";
        } else if (!validate_quantity($quantity)) {
            $errors['quantity'] = "Quantity must be a positive whole number.";
        }

        if (is_empty($order_date)) {
            $errors['order_date'] = "Order date is required.";
        } else if (!validate_date($order_date)) {
            $errors['order_date'] = "Order date cannot be in the past.";
        }

        if ($stock < $quantity) {
            $errors['quantity'] = "There are not enough units for $item_name.";
        }

        // if no errors pass to checkout.php
        if (empty($errors)) {
            $_SESSION['order'] = [
                'item_name'  => $item_name,
                'quantity'   => $quantity,
                'order_date' => $order_date,
                'mod'        => $mod
            ];
            header('Location: checkout.php');
            exit();
        }
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
    <br><br>
    <a href="index.php">Home</a>
    <br><br>

    <h2>Order Item</h2>

    <form action="transaction.php" method="POST" name="order">
        <label for="item_name">Item Name:</label><br>
        <select id="item_name" name="item_name" required>
            <option value="">Select an item</option>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <option value="<?= $row['item_name'] ?>"><?= $row['item_name'] ?></option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select><br><br>

        <!-- add min="1" if needed and step="0.01" or "1", depending on the decimal places needed -->
        <!-- use step="any" to accept any number of decimal places -->
        <label for="quantity">Quantity:</label><br>
        <input type="number" id="quantity" name="quantity" value="<?= htmlspecialchars($quantity) ?>" required><br>
        <?php if (isset($errors['quantity'])): ?>
            <span style="color:red;"><?= $errors['quantity'] ?></span>
        <?php endif; ?>
        <br><br>

        <!-- To make past dates unselectable, min="<?= date('Y-m-d') ?>" -->
        <label for="order_date">Choose Date:</label><br>
        <input type="date" id="order_date" name="order_date" value="<?= htmlspecialchars($order_date) ?>" required><br>
        <?php if (isset($errors['order_date'])): ?>
            <span style="color:red;"><?= $errors['order_date'] ?></span>
        <?php endif; ?>
        <br><br>

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