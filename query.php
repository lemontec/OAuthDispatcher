<?php

include_once(dirname(__FILE__) . "/config.php");
function query() {
    logging::d("Query", "query");
    $fn = isset($_REQUEST["fn"]) ? $_REQUEST["fn"] : null;
    if ($fn == null) {
        logging::e("Query", "No fn");
        die("Invalid Parameter.");
    }
    $cachefile = dirname(__FILE__) . "/cache/$fn.userinfo";
    $userinfo = file_get_contents($cachefile);
    logging::d("Query", "query userinfo for $fn: $userinfo");
    echo $userinfo;
}

query();

