<?php

include_once(dirname(__FILE__) . "/config.php");

function read($url, $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    if ($data != null) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    $out = curl_exec($ch);
    curl_close($ch);
    return $out;
}



function dispatch() {
    $id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : null;
    if ($id == null) {
        logging::e("Dispatcher", "No id");
        die("Invalid Parameter.");
    }
    $callback = get_server_callback($id);
    if ($callback == null) {
        logging::e("Dispatcher", "No Callback for ID: $id.");
        die("No Callback.");
    }

    $code = isset($_REQUEST["code"]) ? $_REQUEST["code"] : null;
    if ($code == null) {
        logging::e("Dispatcher", "No Code.");
        die("No Code.");
    }

    $userinfo = "";

    $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . WECHAT_H5_APPID . "&secret=" . WECHAT_H5_APPSECRET . "&code=$code&grant_type=authorization_code";
    $tmp = read($url);
    logging::d("Dispatcher", "userinfo 1 return: $tmp");
    $json = json_decode($tmp, true);

    if (isset($json['scope']) && $json['scope'] == "snsapi_userinfo") {
        logging::d("Dispatcher", "refresh userinfo");
        $access_token = $json["access_token"];
        $openid = $json["openid"];
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid";
        $result = read($url);
        logging::d("Disptcher", "userinfo 2 return: $result");
        $userinfo = $result;
    } else {
        $userinfo = $tmp;
    }

    $delemiter = "&";
    if (strchr($callback, "?") == null) {
        $delemiter = '?';
    }
    $url = $callback . $delemiter . "userinfo=" . $userinfo;
    logging::d("Dispatch", "direct url: $url");
    header("Location: $url");
}

dispatch();

