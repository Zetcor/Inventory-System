<?php
$conn = new mysqli('localhost', 'root', '', 'PSE');

if (!$conn) {
    die("Connection Failed" . mysqli_connect_error());
} else {
    // echo "Connection Successful";
}