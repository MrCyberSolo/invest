<?php

function is_ajax()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

function json_response($data = [], $code = 200)
{
    header("Content-Type: application/json; charset=UTF-8");

    http_response_code($code);

    echo json_encode($data);

    exit();
}

?>
