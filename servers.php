<?php


function get_server_callback($id) {
    $servers = array(
        "te.travelchina" => "http://travelchina.xiaoningmengkeji.com/?index/oauth",
    );

    if (isset($servers[$id])) {
        return $servers[$id];
    }
    return null;
}

