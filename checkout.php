<?php

    include 'connection.php';

    if (isset($_POST['order'])) {
        $item_name = trim(htmlspecialchars(strip_tags($_POST['item_name'])));
        $quantity = trim(htmlspecialchars(strip_tags($_POST['quantity'])));
        $mod = trim(htmlspecialchars(strip_tags($_POST['mod'])));
        $order_date = trim(htmlspecialchars(strip_tags($_POST['order_date'])));

        $query = "SELECT * FROM items WHERE item_name = '$item_name'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $row          = mysqli_fetch_assoc($result);
            $item_id      = $row['item_id'];
            $manufacturer = $row['manufacturer'];
            $stock        = $row['quantity'];
            $t_date       = date('Y-m-d');
            $unit_price   = $row['unit_price'];
            $subtotal     = $quantity * $unit_price;
            $order_day    = date('N', strtotime($order_date));

            $service_fee = 0;

            $first_20    = min($quantity, 20) * $unit_price * 0.04;
            $remaining   = max($quantity - 20, 0) * $unit_price * 0.07;
            $service_fee = $first_20 + $remaining;

            $payment_fee = 0;

            if ($mod === "Debit/Credit Card") {
                $payment_fee = ($subtotal + $service_fee) * 0.04;
            } else if ($mod === "Check") {
                $payment_fee = ($subtotal + $service_fee) * 0.07;
            }

            $total = $subtotal + $service_fee + $payment_fee;

            // $discount = 0;

            // if ($order_day >= 1 && $order_day <= 5) {
            //     $discount = 0.1; // 10% discount for orders placed on weekdays
            //     $total = $subtotal * (1 - $discount); // 10% discount for orders placed on weekdays
            // } else {
            //     $discount = 0.05; // 5% discount for orders placed on weekends
            //     $total = $subtotal * (1 - $discount); // 5% discount for orders placed on weekends
            // }

            // $grand_total = $total + ($total * 0.12); // Adding 12% tax to the total

            if ($stock >= $quantity) {

                $query = "INSERT INTO transactions (item_id, quantity, mode_of_payment, transaction_date, service_fee, payment_fee, subtotal, total_amount) VALUES ('$item_id', '$quantity', '$mod', '$t_date', '$service_fee', '$payment_fee', '$subtotal', '$total')";
                mysqli_query($conn, $query);

                $new_quantity = $row['quantity'] - $quantity;
                $query = "UPDATE items SET quantity = '$new_quantity' WHERE item_id = '$item_id'";
                mysqli_query($conn, $query);
            } else {
                echo "There are not enough units for $item_name.";
                exit();
            }
        } else {
            echo "Item not found.";
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
    <?php if (isset($_POST['order']) && $stock >= $quantity): ?>
        <br><br>
        <a href="index.php">Home</a>
        <br><br>

        <h2>Order Details</h2>

        <p><strong>Transaction Date: </strong> <?= date('F d, Y', strtotime($t_date)); ?></p>

        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $item_name ?></td>
                    <td><?= $quantity ?></td>
                    <td>Php <?= number_format($unit_price, 2) ?></td>
                    <td>Php <?= number_format($subtotal, 2) ?></td>
                </tr>
            </tbody>
        </table>

        <p><strong>Service Fee: </strong> Php <?= number_format($service_fee, 2) ?></p>
        <p><strong>Mode of Payment: </strong> <?= $mod ?></p>
        <p><strong>Order Date: </strong> <?= date('F d, Y', strtotime($order_date)) ?></p>
        <p><strong>Payment Fee: </strong> Php <?= number_format($payment_fee, 2) ?></p>
        <p><strong>Total Amount: </strong> Php <?= number_format($total, 2) ?></p>
    <?php endif; ?>

</body>
</html>