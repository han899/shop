<?php
require("dbconfig.php");



function register($name, $account, $password, $role){
    global $db;
	$sql="insert into userinfo (name, account, password, role) values (? , ? , ?, ?);";
	$stmt=mysqli_prepare($db,$sql);
	mysqli_stmt_bind_param($stmt, "ssss", $name, $account, $password, $role);
	mysqli_stmt_execute($stmt);
	return True;
}

function login($account, $password){
    global $db;
    $sql = "select * from userinfo where account=? and password=?";//如果查不到就會回傳null，如果查到就會回傳一個陣列，然後我要取她的uID
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $account, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result); 
}

?>