<?php

    include 'connection.php';
    include 'validations.php';

    // s string
    // i integer
    // d decimal/float

    if (isset($_POST['edit'])) {

        $item_id      = sanitize_input($_POST['item_id']);
        $item_name    = sanitize_input($_POST['item_name']);
        $manufacturer = sanitize_input($_POST['manufacturer']);
        $quantity     = sanitize_input($_POST['quantity']);
        $unit_price   = sanitize_input($_POST['unit_price']);

        $errors = [];
        if (is_empty($item_name)) {
            $errors['item_name'] = "Item name is required.";
        } else if (!validate_string($item_name)) {
            $errors['item_name'] = "Item name contains invalid characters.";
        }

        if (is_empty($manufacturer)) {
            $errors['manufacturer'] = "Manufacturer is required.";
        } else if (!validate_string($manufacturer)) {
            $errors['manufacturer'] = "Manufacturer contains invalid characters.";
        }

        if (is_empty($quantity)) {
            $errors['quantity'] = "Quantity is required.";
        } else if (!validate_quantity($quantity)) {
            $errors['quantity'] = "Quantity must be a positive number.";
        }

        if (is_empty($unit_price)) {
            $errors['unit_price'] = "Unit price is required.";
        } else if (!validate_unit_price($unit_price)) {
            $errors['unit_price'] = "Unit price must be a positive number.";
        }

        if (empty($errors)) {

            // Generate new item_id based on updated item_name and manufacturer IF NEEDED!!!
            // $query = "SELECT date_added FROM items WHERE item_id = '$item_id'";
            // $result = mysqli_query($conn, $query);
            // $row = mysqli_fetch_assoc($result);
            // $date_added = date('Ymd', strtotime($row['date_added']));
            // $manu2 = strtoupper(substr($manufacturer, 0, 2));
            // $item3 = strtoupper(substr($item_name, 0, 3));
            // $parts = explode('-', $item_id);
            // $original_random = end($parts);
            // $new_id = $manu2 . "-" . $item3 . "-" . $date_id . "-" . $original_random;

            // use the new_id variable instead of item_id in the bind_param if you want to update the item_id as well
            $stmt = $conn->prepare("UPDATE items SET item_name = ?, manufacturer = ?, quantity = ?, unit_price = ? WHERE item_id = ?");
            $stmt->bind_param("ssids", $item_name, $manufacturer, $quantity, $unit_price, $item_id);
            $stmt->execute();

            // No prepared statement version (vulnerable to SQL injection):
            // $query = "UPDATE items SET item_name = '$item_name', manufacturer = '$manufacturer', quantity = '$quantity', unit_price = '$unit_price' WHERE item_id = '$item_id'";
            // $result = mysqli_query($conn, $query);

            if ($stmt->affected_rows > 0) {
                header('Location: index.php');
                exit();
            }
        }
    }

    if (isset($_POST['id'])) {
        $item_id = $_POST['id'];
    } else {
        header('Location: index.php');
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM items where item_id = ?");
    $stmt->bind_param("s", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // No prepared statement version (vulnerable to SQL injection):
    // $query = "SELECT * FROM items WHERE item_id = '$item_id'";
    // $result = mysqli_query($conn, $query);

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
        <input type="text" id="item_name" name="item_name" value="<?= htmlspecialchars($item_name) ?>" required><br>
        <?php if (isset($errors['item_name'])): ?>
            <span style="color:red;"><?= $errors['item_name'] ?></span>
        <?php endif; ?>
        <br><br>

        <label for="manufacturer">Manufacturer:</label><br>
        <input type="text" id="manufacturer" name="manufacturer" value="<?= htmlspecialchars($manufacturer) ?>" required><br>
        <?php if (isset($errors['manufacturer'])): ?>
            <span style="color:red;"><?= $errors['manufacturer'] ?></span>
        <?php endif; ?>
        <br><br>

        <!-- add min="1" if needed and step="0.01" or "1", depending on the decimal places needed -->
        <!-- use step="any" to accept any number of decimal places -->
        <label for="quantity">Quantity:</label><br>
        <input type="number" step="1" id="quantity" name="quantity" value="<?= htmlspecialchars($quantity) ?>" min="1" required><br>
        <?php if (isset($errors['quantity'])): ?>
            <span style="color:red;"><?= $errors['quantity'] ?></span>
        <?php endif; ?>
        <br><br>

        <!-- add min="1" if needed and step="0.01" or "1", depending on the decimal places needed -->
        <!-- use step="any" to accept any number of decimal places -->
        <label for="unit_price">Unit Price:</label><br>
        <input type="number" step="0.01" id="unit_price" name="unit_price" value="<?= htmlspecialchars($unit_price) ?>" min="1" required><br>
        <?php if (isset($errors['unit_price'])): ?>
            <span style="color:red;"><?= $errors['unit_price'] ?></span>
        <?php endif; ?>
        <br><br>

        <input type="submit" value="Update Item" name="edit">
    </form>
</body>
</html>