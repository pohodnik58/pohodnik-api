<?php
function err($err) {
    return json_encode(array(
        "error" => $err,
        "message" => is_string($err)
            ? $err
            : (
                isset($err['message'])
                ? $err['message']
                : 'Что-то пошло не так ...'
            )
    ));
}

function out($data) {
    return json_encode($data);
}