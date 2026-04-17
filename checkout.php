<?php

    session_start();
    include 'connection.php';

    if (isset($_SESSION['order'])) {
        $item_id = $_SESSION['order']['item_id'];
        $item_name = $_SESSION['order']['item_name'];
        $quantity = $_SESSION['order']['quantity'];
        $mod = $_SESSION['order']['mod'];
        $order_date = $_SESSION['order']['order_date'];

        $stmt = $conn->prepare("SELECT * FROM items WHERE item_id = ?");
        $stmt->bind_param("s", $item_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // No prepared statement version (vulnerable to SQL injection):
        // $query = "SELECT * FROM items WHERE item_id = '$item_id'";
        // $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $row          = mysqli_fetch_assoc($result);
            $item_id      = $row['item_id'];
            $item_name    = $row['item_name'];
            $manufacturer = $row['manufacturer'];
            $stock        = $row['quantity'];
            $t_date       = date('Y-m-d');
            $unit_price   = $row['unit_price'];
            $subtotal     = $quantity * $unit_price;
            $order_day    = date('N', strtotime($order_date));

            // Calculate service fee based on quantity and unit price
            $service_fee = 0;

            $first_20    = min($quantity, 20) * $unit_price * 0.04;
            $remaining   = max($quantity - 20, 0) * $unit_price * 0.07;
            $service_fee = $first_20 + $remaining;

            // Calculate payment fee based on mode of payment
            $payment_fee = 0;

            if ($mod === "Debit/Credit Card") {
                $payment_fee = ($subtotal + $service_fee) * 0.04;
            } else if ($mod === "Check") {
                $payment_fee = ($subtotal + $service_fee) * 0.07;
            }

            $total = $subtotal + $service_fee + $payment_fee;

            // Calculate total with discount based on the date (MWF or TTh) (optional, can be removed if not needed)
                // if ($order_day == 1 || $order_day == 3 || $order_day == 5) { // Monday, Wednesday, Friday
                //     $total = $total * (1 - 0.10); // 10% discount for orders placed on MWF
                // } else if ($order_day == 2 || $order_day == 4) { // Tuesday, Thursday
                //     $total = $total * (1 - 0.05); // 5% discount for orders placed on TTh
                // }

            // Calculate discount based on order date (optional, can be removed if not needed)
                // $discount = 0;

                // if ($order_day >= 1 && $order_day <= 5) {
                //     $discount = 0.1; // 10% discount for orders placed on weekdays
                //     $total = $subtotal * (1 - $discount); // 10% discount for orders placed on weekdays
                // } else {
                //     $discount = 0.05; // 5% discount for orders placed on weekends
                //     $total = $subtotal * (1 - $discount); // 5% discount for orders placed on weekends
                // }

            // Calculate total with surcharge based on order date (optional, can be removed if not needed)
                // if ($order_day == 6 || $order_day == 7) { // Saturday, Sunday
                //     $total = $total * (1 + 0.05); // 5% surcharge for orders placed on weekends
                // }

            // Calculate grand total with vat (VAT-EXCLUSIVE) VAT not included in the total
                // $tax_rate = 0.12;
                // $VATable = $subtotal;
                // $VATamount = $VATable * $tax_rate;
                // $total = $VATable + $VATamount; 
                // or
                // $total = $subtotal + ($subtotal * 0.12); // Adding 12% tax to the total

            // Calculate grand total with vat (VAT-INCLUSIVE)  VAT already included in the total, so we need to extract the VAT amount from the total
                // $tax_rate = 0.12;
                // $VATable = $subtotal / (1 + $tax_rate); // Calculate VATable amount from total
                // $VATamount = $subtotal - $VATable; // Calculate VAT amount from total
                // $total = $subtotal;


            $success = false;

            if ($stock >= $quantity) {

                $stmt = $conn->prepare("INSERT INTO transactions (item_id, quantity, mode_of_payment, transaction_date, service_fee, payment_fee, subtotal, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sissdddd", $item_id, $quantity, $mod, $t_date, $service_fee, $payment_fee, $subtotal, $total);
                $stmt->execute();

                // No prepared statement version (vulnerable to SQL injection):
                // $query = "INSERT INTO transactions (item_id, quantity, mode_of_payment, transaction_date, service_fee, payment_fee, subtotal, total_amount) VALUES ('$item_id', '$quantity', '$mod', '$t_date', '$service_fee', '$payment_fee', '$subtotal', '$total')";
                // mysqli_query($conn, $query);

                $new_quantity = $row['quantity'] - $quantity;

                $stmt = $conn->prepare("UPDATE items SET quantity = ? WHERE item_id = ?");
                $stmt->bind_param("is", $new_quantity, $item_id);
                $stmt->execute();

                unset($_SESSION['order']);
                $success = true;

                // No prepared statement version (vulnerable to SQL injection):
                // $query = "UPDATE items SET quantity = '$new_quantity' WHERE item_id = '$item_id'";
                // mysqli_query($conn, $query);
            }
            
        } else {
            echo "Item not found.";
        }

    } else {
        header('Location: transaction.php');
        exit();
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
    <?php if ($success): ?>
        <br><br>
        <a href="index.php">Home</a>
        <br><br>

        <h2>Order Details</h2>

        <p><strong>Transaction Date: </strong> <?= date('F d, Y', strtotime($t_date)); ?></p> <!-- Format transaction date as "Month Day, Year" --> 

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
                    <td>Php <?= number_format($unit_price, 2) ?></td> <!-- Format unit price with 2 decimal places and "Php" prefix --> 
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