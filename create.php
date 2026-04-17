<?php
    include 'connection.php';
    include 'validations.php';

    $item_id = "";
    $item_name = "";
    $manufacturer = "";
    $quantity = "";
    $unit_price = "";
    

    if (isset($_POST['create'])) {
        $item_name    = sanitize_input($_POST['item_name']);
        $manufacturer = sanitize_input($_POST['manufacturer']);
        $quantity     = sanitize_input($_POST['quantity']);
        $unit_price   = sanitize_input($_POST['unit_price']);
        $date_added   = date('Y-m-d'); // Get current date in YYYY-MM-DD format for MySQL DATE type
        $date_id      = date('Ymd'); // Get current date in YYYYMMDD format for item ID generation

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

        // System generated ID
        if (empty($errors)) {
            $manu2 = strtoupper(substr($manufacturer, 0, 2)); // Get first 2 characters of manufacturer and convert to uppercase
            $item3 = strtoupper(substr($item_name, 0, 3)); // Get first 3 characters of item name and convert to uppercase
            $random = rand(1000000, 9999999);  // Generate a random 7-digit number
            // $random = str_pad(rand(0, 99999), 7, "0", STR_PAD_LEFT); // Generate a random 7-digit number and pad with leading zeros if necessary

            $item_id = $manu2 . "-" . $item3 . "-" . $date_id . "-" . $random;

            $stmt = $conn->prepare("INSERT INTO items (item_id, item_name, manufacturer, quantity, unit_price, date_added) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssids", $item_id, $item_name, $manufacturer, $quantity, $unit_price, $date_added);
            $stmt->execute();

            // No prepared statement version (vulnerable to SQL injection):
            // $query  = "INSERT INTO items (item_id, item_name, manufacturer, quantity, unit_price, date_added) 
            //    VALUES ('$item_id', '$item_name', '$manufacturer', '$quantity', '$unit_price', '$date_added')";
            // $result = mysqli_query($conn, $query);

            if ($stmt->affected_rows > 0) {
                header('Location: index.php');
                exit();
            }
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

    <h2>Add New Item</h2>

    <form action="create.php" method="POST" name="create">
        <label for="item_name">Item Name:</label><br>
        <input type="text" id="item_name" name="item_name" value="<?= htmlspecialchars($item_name) ?>"><br>
        <?php if (isset($errors['item_name'])): ?>
            <span style="color:red;"><?= $errors['item_name'] ?></span>
        <?php endif; ?>
        <br><br>

        <label for="manufacturer">Manufacturer:</label><br>
        <input type="text" id="manufacturer" name="manufacturer" value="<?= htmlspecialchars($manufacturer) ?>"><br>
        <?php if (isset($errors['manufacturer'])): ?>
            <span style="color:red;"><?= $errors['manufacturer'] ?></span>
        <?php endif; ?>
        <br><br>

        <!-- add min="1" if needed and step="0.01" or "1", depending on the decimal places needed -->
        <!-- use step="any" to accept any number of decimal places -->
        <label for="quantity">Quantity:</label><br>
        <input type="number" step="1" id="quantity" name="quantity" value="<?= htmlspecialchars($quantity) ?>"><br>
        <?php if (isset($errors['quantity'])): ?>
            <span style="color:red;"><?= $errors['quantity'] ?></span>
        <?php endif; ?>
        <br><br>

        <!-- add min="1" if needed and step="0.01" or "1", depending on the decimal places needed -->
        <!-- use step="any" to accept any number of decimal places -->
        <label for="unit_price">Unit Price:</label><br>
        <input type="number" step="0.01" id="unit_price" name="unit_price" value="<?= htmlspecialchars($unit_price) ?>"><br>
        <?php if (isset($errors['unit_price'])): ?>
            <span style="color:red;"><?= $errors['unit_price'] ?></span>
        <?php endif; ?>
        <br><br>

        <input type="submit" value="Add Item" name="create">
    </form>

</body>
</html>