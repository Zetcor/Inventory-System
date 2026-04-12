<?php
    include 'connection.php';

    $item_id = "";
    $item_name = "";
    $manufacturer = "";
    $quantity = "";
    $unit_price = "";

    if (isset($_POST['create'])) {
        $item_name = trim(htmlspecialchars(strip_tags($_POST['item_name'])));
        $manufacturer = trim(htmlspecialchars(strip_tags($_POST['manufacturer'])));
        $quantity = trim(htmlspecialchars(strip_tags($_POST['quantity'])));
        $unit_price = trim(htmlspecialchars(strip_tags($_POST['unit_price'])));
        $date_added = date('Ymd');

        $manu2 = strtoupper(substr($manufacturer, 0, 2));
        $item3 = strtoupper(substr($item_name, 0, 3));
        $random = rand(1000000, 9999999);

        $item_id = $manu2 . "-" . $item3 . "-" . $date_added . "-" . $random;

        $query  = "INSERT INTO items (item_id, item_name, manufacturer, quantity, unit_price, date_added) 
           VALUES ('$item_id', '$item_name', '$manufacturer', '$quantity', '$unit_price', '$date_added')";
        $result = mysqli_query($conn, $query);

        if ($result) {
            header('Location: index.php');
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

    <h2>Add New Item</h2>

    <form action="create.php" method="POST" name="create">
        <label for="item_name">Item Name:</label><br>
        <input type="text" id="item_name" name="item_name" required><br><br>

        <label for="manufacturer">Manufacturer:</label><br>
        <input type="text" id="manufacturer" name="manufacturer" required><br><br>

        <label for="quantity">Quantity:</label><br>
        <input type="number" id="quantity" name="quantity" required><br><br>

        <label for="unit_price">Unit Price:</label><br>
        <input type="number" step="1.00" id="unit_price" name="unit_price" required><br><br>

        <input type="submit" value="Add Item" name="create">
    </form>

</body>
</html>