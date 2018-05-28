<?php

include_once(dirname(__FILE__) . "/config.php");

$id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : null;
$userinfo = isset($_REQUEST["userinfo"]) ? $_REQUEST["userinfo"] : "0";
if ($id == null) {
    die("Invalid Parameter.");
}
$userinfo = (int)$userinfo;

$domain = $_SERVER["HTTP_HOST"];
$redirectUri = "http://$domain/dispatcher.php?id=$id";
$redirectUri = urlencode($redirectUri);


$snsapi = ($userinfo != 0) ? "snsapi_userinfo" : "snsapi_base";
$checkUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . WECHAT_H5_APPID . "&redirect_uri={$redirectUri}&response_type=code&scope={$snsapi}&state=1#wechat_redirect";
logging::d('WeChat', "doOAuth, checkurl is: $checkUrl");
header('location:' . $checkUrl);


