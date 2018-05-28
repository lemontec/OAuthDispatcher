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
    $id = isset($_SESSION["id"]) ? $_SESSION["id"] : null;
    if ($id == null) {
        die("Invalid Parameter.");
    }
    $callback = get_server_callback($id);
    if ($callback == null) {
        die("No Callback.");
    }

    $result = array("openid" => null);

    $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . WECHAT_H5_APPID . "&secret=" . WECHAT_H5_APPSECRET . "&code=$code&grant_type=authorization_code";
    $result = read($url);
    $json = json_decode($result, true);

    if (!isset($json["openid"])) {
        logging::e('WeChat', $json['errcode'] . '            msg: ' . $json['errmsg']);
    } else {
        $openid = $json["openid"];
        $result["openid"] = $openid;

        if (isset($json['scope']) && $json['scope'] == "snsapi_userinfo") {
            $access_token = $json["access_token"];
            $url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid";
            $plain = read($url);
            $json = json_decode($plain, true);
            $openid = $json["openid"];
            $result["nickname"] = $json["nickname"];
            $result["sex"] = $json["sex"];
            $result["language"] = $json["language"];
            $result["city"] = $json["city"];
            $result["province"] = $json["province"];
            $result["country"] = $json["country"];
            $result["headimgurl"] = $json["headimgurl"];
        }
    }
    $plain = json_encode($result);
    $plain = urlencode($plain);
    $url = $callback . "&result=" . $plain;
    header("Location: $url");
}

dispatch();

