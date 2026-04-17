<?php

    include 'connection.php';
    include 'validations.php';

    if (isset($_POST['edit'])) {
        $item_id      = validate_input($_POST['item_id']);
        $item_name    = validate_input($_POST['item_name']);
        $manufacturer = validate_input($_POST['manufacturer']);
        $quantity     = validate_input($_POST['quantity']);
        $unit_price   = validate_input($_POST['unit_price']);
        // Generate new item_id based on updated item_name and manufacturer IF NEEDED!!!
        // $query = "SELECT date_added FROM items WHERE item_id = '$item_id'";
        // $result = mysqli_query($conn, $query);
        // $row = mysqli_fetch_assoc($result);
        // $date_added = date('Ymd', strtotime($row['date_added']));
        // $manu2 = strtoupper(substr($manufacturer, 0, 2));
        // $item3 = strtoupper(substr($item_name, 0, 3));
        // $parts = explode('-', $item_id);
        // $original_random = end($parts);
        // $new_id = $manu2 . "-" . $item3 . "-" . $date_added . "-" . $original_random;

        // add item_id = '$new_id' to the query if you want to update the item_id as well
        $query = "UPDATE items SET item_name = '$item_name', manufacturer = '$manufacturer', quantity = '$quantity', unit_price = '$unit_price' WHERE item_id = '$item_id'";
        $result = mysqli_query($conn, $query);

        if ($result) {
            header('Location: index.php');
            exit();
        }
    }

    if (isset($_POST['id'])) {
        $item_id = $_POST['id'];
    } else {
        header('Location: index.php');
        exit();
    }

    $query = "SELECT * FROM items WHERE item_id = '$item_id'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $item_id = $row['item_id'];
        $item_name = $row['item_name'];
        $manufacturer = $row['manufacturer'];
        $quantity = $row['quantity'];
        $unit_price = $row['unit_price'];
    } else {
        echo "Item not found.";
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

    <h2>Edit Item</h2>

    <form action="edit.php" method="POST" name="edit">
        <input type="hidden" name="item_id" value="<?= $item_id ?>">

        <label for="item_name">Item Name:</label><br>
        <input type="text" id="item_name" name="item_name" value="<?= $item_name ?>" required><br><br>

        <label for="manufacturer">Manufacturer:</label><br>
        <input type="text" id="manufacturer" name="manufacturer" value="<?= $manufacturer ?>" required><br><br>

        <label for="quantity">Quantity:</label><br>
        <input type="number" id="quantity" name="quantity" value="<?= $quantity ?>" min="1" required><br><br>

        <!-- use step="any" to accept any number of decimal places -->
        <label for="unit_price">Unit Price:</label><br>
        <input type="number" step="0.001" id="unit_price" name="unit_price" value="<?= $unit_price ?>" min="1" required><br><br>

        <input type="submit" value="Update Item" name="edit">
    </form>
</body>
</html>