<?php

    include 'connection.php';

    function sanitize_input($data) {
        return trim(strip_tags($data));
    }

    // If date validation is needed, you can use this function to check if the date is in the past
    function validate_date($date) {
        $current_date = date('Y-m-d');
        if (strtotime($date) < strtotime($current_date)) {
            return false; // Date is in the past
        }
        return true;
    }

    function is_empty($text_field) {
        return empty($text_field);
    }

    function validate_quantity($quantity) {
        return ctype_digit(strval($quantity)) && $quantity > 0;
    }

    function validate_unit_price($unit_price) {
        $money_pattern = '/^\d+(?:\.\d{1,2})?$/';
        return preg_match($money_pattern, $unit_price) && $unit_price > 0;
    }

    function validate_string($data) {
        $string_pattern = "/^[a-zA-Z0-9\s\-'.&()]+$/";
        return preg_match($string_pattern, $data);
    }


