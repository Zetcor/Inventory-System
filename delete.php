<?php

    include 'connection.php';

    if (isset($_POST['id'])) {
        $id = $_POST['id'];
    } else {
        header('Location: index.php');
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM items WHERE item_id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();

    // No prepared statement version (vulnerable to SQL injection):
    // $query = "DELETE FROM items WHERE item_id = '$id'";
    // $result = mysqli_query($conn, $query);

    if ($stmt->affected_rows > 0) {
        header('Location: index.php');
        exit();
    }
