<?php

    include 'connection.php';

    function validate_input($data) {
        return trim(htmlspecialchars(strip_tags($data)));
    }

    // If date validation is needed, you can use this function to check if the date is in the past
    function validate_date($date) {
        $current_date = date('Y-m-d');
        if (strtotime($date) < strtotime($current_date)) {
            return false; // Date is in the past
        }
        return true;
    }

?>