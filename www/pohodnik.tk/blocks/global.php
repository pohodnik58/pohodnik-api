<?php
function err($err, $data=array()) {
    return json_encode(array(
        "error" => $err,
        "message" => is_string($err)
            ? $err
            : (
                isset($err['message'])
                ? $err['message']
                : 'Что-то пошло не так ...'
            ),
            "error_data" => isset($data) ? $data : array()
    ));
}

function out($data) {
    return json_encode($data);
}