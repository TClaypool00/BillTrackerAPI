<?php
function custom_array($message) {
    return json_encode(array('message' => $message));
}

function get_isset($get_name) {
    if (isset($_GET[$get_name])) {
        return true;
    }

    return false;
}

function set_get_variable($get_name) {
    return $_GET[$get_name];
}

function display_list($array) {
    return json_encode($array);
}

function set_id() {
    if (get_isset('id')) {
        $id = set_get_variable('id');
        if (is_numeric($id)) {
            return intval($id);
        } else {
            echo custom_array('id must be a number');
            die();
        }
    } else {
        echo custom_array('id cannot be null');
        die();
    }
}

function currency($value) {
    return '$' . number_format($value, 2);
}

function first_index(string $value) {
    return $value[0] . '.';
}

function same_user_id($decoded, int $user_id) {
    return $decoded->userId === $user_id;
}