<?php
require("userModel.php");

$act=$_REQUEST['act'];
switch ($act) {

case "register": //註冊
	$jsonStr = $_POST['dat'];
	$user = json_decode($jsonStr);
	$app=register($user->name, $user->account, $user->password, $user->role);

	return;
case "login": // 登入
    $jsonStr = $_POST['dat'];
    $user = json_decode($jsonStr);
    $result = login($user->account, $user->password);
	
    if ($result) {
        // 登入成功
        $response = ["success" => true, "role" => $result['role'], "uID" => $result['uID']];
    } else {
        // 登入失敗
        $response = ["success" => false];
    }
    echo json_encode($response);
    return;
default:
}

?>