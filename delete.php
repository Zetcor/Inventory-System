<?php

    include 'connection.php';

    if (isset($_POST['id'])) {
        $id = $_POST['id'];
    } else {
        header('Location: index.php');
        exit();
    }

    $query = "DELETE FROM items WHERE item_id = '$id'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        header('Location: index.php');
        exit();
    }

?>