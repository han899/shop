<?php
require('dbconfig.php');

// 3a
// 【客戶查看商品列表】
function listProduct(){
	global $db;
	$sql="select product.pID as pID, product.name as pName, product.price as price, product.stock as stock, userinfo.name as uName from product inner join userinfo on product.uID = userinfo.uID;";
	$stmt=mysqli_prepare($db,$sql);
	mysqli_stmt_execute($stmt);
	$result=mysqli_stmt_get_result($stmt);
	$rows=array();
	while($r=mysqli_fetch_assoc($result)){
		$rows[]=$r;
	}
	return $rows;
}
// 【客戶將商品放入購物車】
function addCart($pID, $uID){

	global $db;
	//假如購物車裡面有此商品，則數量+1
	$sql1 = "select count(*) from cart where pID = ? and uID = ?;";
	$stmt = mysqli_prepare($db, $sql1);
	mysqli_stmt_bind_param($stmt, "ii", $pID, $uID);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $count); //會將結果存到count變數中
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt); // 因為 prepare 所以需要用 close 關閉 statement，才能執行下一個 prepare

	if ($count > 0) {
		// 購物車裡已經有此商品，數量+1
		$sql2 = "update cart set amount = amount + 1 where pID = ? and uID = ?;";
		$stmt = mysqli_prepare($db, $sql2);
		mysqli_stmt_bind_param($stmt, "ii", $pID, $uID);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt); // Close the first statement

	} else {
		// 購物車裡沒有此商品，新增一筆資料
		$sql3 = "insert into cart (pID, amount, uID) values (?, 1, ?);";
		$stmt = mysqli_prepare($db, $sql3);
		mysqli_stmt_bind_param($stmt, "ii", $pID, $uID);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt); // Close the first statement

	}

	return True;
}

// 【客戶查看指定商品詳細資訊】	
function getProductDetail($pID){
	global $db;
	$sql="select name, price, stock, content from product where pID = ?;";
	$stmt=mysqli_prepare($db,$sql);
	mysqli_stmt_bind_param($stmt,"i",$pID);
	mysqli_stmt_execute($stmt);
	$result=mysqli_stmt_get_result($stmt);
	$rows=array();
	while($r=mysqli_fetch_assoc($result)){
		$rows[]=$r;
	}
	return $rows;
}

// 【客戶查看購物車內容】
function listCart($uID){
	global $db;
	$sql="select product.pID, product.name, product.price, cart.amount from product inner join cart on product.pID = cart.pID where cart.uID = ?;";
	$stmt=mysqli_prepare($db,$sql);
    mysqli_stmt_bind_param($stmt,"i",$uID);
	mysqli_stmt_execute($stmt);
	$result=mysqli_stmt_get_result($stmt);
	$rows=array();
	while($r=mysqli_fetch_assoc($result)){
		$rows[]=$r;
	}
	return $rows;
}

//【客戶刪除購物車內容】
function delCart($pID, $uID){
	global $db;
	$sql="delete from cart where pID=? and uID = ?;";
	$stmt=mysqli_prepare($db,$sql);
	mysqli_stmt_bind_param($stmt,"ii",$pID, $uID);
	mysqli_stmt_execute($stmt);
	return True;
}
// 【客戶計算購物車總金額】
function cartTotal($uID){
    global $db;
    $sql="select sum(c.amount * p.price) as total_amount from cart c inner join product p on c.pID = p.pID where c.uID = ?;";
    $stmt=mysqli_prepare($db,$sql);
    mysqli_stmt_bind_param($stmt,"i",$uID);
    mysqli_stmt_execute($stmt);
    
    $total_amount = 0; // 新增的變數
    
    mysqli_stmt_bind_result($stmt, $total_amount); // 將結果繫結到變數
    mysqli_stmt_fetch($stmt);
    
    mysqli_stmt_close($stmt);
    return ['total_amount' => $total_amount];
}

//【商家刪除商品項目】
function delProduct($pID){
	global $db;
	$sql="delete from product where pID = ?;";
	$stmt=mysqli_prepare($db,$sql);
	mysqli_stmt_bind_param($stmt,"i",$pID);
	mysqli_stmt_execute($stmt);
	return True;
}

// 【商家更新商品】
function updateProduct($pID, $name, $price, $stock, $content){
    global $db;
	$sql="update product set name=? , price=? , stock=? , content=? where pID = ?;";
	$stmt=mysqli_prepare($db,$sql);
	mysqli_stmt_bind_param($stmt, "ssssi", $name, $price, $stock, $content, $pID);
	mysqli_stmt_execute($stmt);
	return True;
}

// 【商家新增商品】
function addProduct($name, $price, $stock, $content, $uID){
    global $db;
	$sql="insert into product (name, price, stock, content, uID) values (? , ? , ? , ?,?);";
	$stmt=mysqli_prepare($db,$sql);
	mysqli_stmt_bind_param($stmt, "ssssi", $name, $price, $stock, $content,$uID);
	mysqli_stmt_execute($stmt);
	return True;
}

// 【商家查看商品詳細內容】
function listProductInfo($uID){
	global $db;
	$sql="select * from product where uID = ?;";
	$stmt=mysqli_prepare($db,$sql);
	mysqli_stmt_bind_param($stmt,"i", $uID);
	mysqli_stmt_execute($stmt);
	$result=mysqli_stmt_get_result($stmt);
	$rows=array();
	while($r=mysqli_fetch_assoc($result)){
		$rows[]=$r;
	}
	return $rows;
}




// 3b
// 【客戶進行結帳】
// 新增訂單紀錄到orders紀錄表
function addOrder($uID, $totalPrice){ 
    global $db;
	$sql="insert into orders(userID, sum) values(?, ?);";
	$stmt=mysqli_prepare($db,$sql);
	mysqli_stmt_bind_param($stmt,"ii",$uID, $totalPrice);
	mysqli_stmt_execute($stmt);
	return True;
}

// 儲存購物車項目到order_items資料表
function saveItems($uID){
    global $db;
	$sql="insert into order_item (oID, pID, amount) select (select max(oID) from orders) as M, pID, amount from cart where uID = ?;";
	$stmt=mysqli_prepare($db,$sql);
	mysqli_stmt_bind_param($stmt,"i",$uID);
	mysqli_stmt_execute($stmt);
	return True;
}

// 清空購物車
function clearCart($uID){
    global $db;
	$sql="delete from cart where uID = ?;";
	$stmt=mysqli_prepare($db,$sql);
	mysqli_stmt_bind_param($stmt,"i",$uID);
	mysqli_stmt_execute($stmt);
	return True;
}

// 【客戶查看訂單】
function listorder($uID){ 
	global $db;
	$sql="select oID, sum from orders where userID = ?;";
	$stmt=mysqli_prepare($db,$sql);
    mysqli_stmt_bind_param($stmt,"i", $uID);
	mysqli_stmt_execute($stmt);
	$result=mysqli_stmt_get_result($stmt);
	$rows=array();
	while($r=mysqli_fetch_assoc($result)){
		$rows[]=$r;
	}
	return $rows;
}

// 【客戶查看訂單詳細內容】
function getOrderDetail($oID){
	global $db;
	$sql="select * from product inner join order_item on product.pID = order_item.pID where oID = ?;";
	$stmt=mysqli_prepare($db,$sql);
	mysqli_stmt_bind_param($stmt,"i",$oID);
	mysqli_stmt_execute($stmt);
	$result=mysqli_stmt_get_result($stmt);
	$rows=array();
	while($r=mysqli_fetch_assoc($result)){
		$rows[]=$r;
	}
	return $rows;
}

// 【客戶評價訂單】
function evaluateOrder($itemID, $evaluation){
    global $db;
	$sql="update order_item set evaluation = ? where itemID = ?";
	$stmt=mysqli_prepare($db,$sql);
	mysqli_stmt_bind_param($stmt,"ii",$evaluation, $itemID);
	mysqli_stmt_execute($stmt);
	return True;
}

// 【客戶完成訂單】
function completeOrder($itemID){
    global $db;
	$sql="update order_item set status = '已收貨' where itemID = ?";
	$stmt=mysqli_prepare($db,$sql);
	mysqli_stmt_bind_param($stmt,"i", $itemID);
	mysqli_stmt_execute($stmt);
	return True;
}

// 【商家查看自己訂單】
function selllistAllorder($uID){
	global $db;
	$sql="select distinct order_item.oID,order_item.status from product inner join order_item on product.pID = order_item.pID where product.uID = ?;";
	$stmt=mysqli_prepare($db,$sql);
	mysqli_stmt_bind_param($stmt,"i", $uID);
	mysqli_stmt_execute($stmt);
	$result=mysqli_stmt_get_result($stmt);
	$rows=array();
	while($r=mysqli_fetch_assoc($result)){
		$rows[]=$r;
	}
	return $rows;
}
// 【商家處理訂單】
function dealProduct($oID, $uID){
    global $db;
	$sql="update order_item set status = '已處理' where oID = ? and pID in (select pID from product where uID = ?);";
	$stmt=mysqli_prepare($db,$sql);
	mysqli_stmt_bind_param($stmt,"ii", $oID, $uID);
	mysqli_stmt_execute($stmt);
	return True;
}
// 【商家寄送訂單】
function sendProduct($oID, $uID){
    global $db;
	$sql="update order_item set status = '已寄送' where oID = ? and pID in (select pID from product where uID = ?);";
	$stmt=mysqli_prepare($db,$sql);
	mysqli_stmt_bind_param($stmt,"ii", $oID, $uID);
	mysqli_stmt_execute($stmt);
	return True;
}
// 【商家送訂單】
function achieveProduct($oID){
    global $db;
	$sql="update orders set status = '已送達' where oID = ?";
	$stmt=mysqli_prepare($db,$sql);
	mysqli_stmt_bind_param($stmt,"i", $oID);
	mysqli_stmt_execute($stmt);
	return True;
}

// 3c 
//【商家查看訂單詳細內容】
function sellgetOrderDetail($oID, $uID){
	global $db;
	$sql=" select product.name, product.price, product.content, order_item.amount, order_item.evaluation, order_item.status, order_item.itemID from product inner join order_item on product.pID = order_item.pID where order_item.oID = ? and product.uID = ?;";
	$stmt=mysqli_prepare($db,$sql);
	mysqli_stmt_bind_param($stmt,"ii",$oID,$uID);
	mysqli_stmt_execute($stmt);
	$result=mysqli_stmt_get_result($stmt);
	$rows=array();
	while($r=mysqli_fetch_assoc($result)){
		$rows[]=$r;
	}
	return $rows;
}

// 【物流查看訂單】
function carlistAllorder(){
	global $db;
	$sql="select * from orders where 1";
	$stmt=mysqli_prepare($db,$sql);
	mysqli_stmt_execute($stmt);
	$result=mysqli_stmt_get_result($stmt);
	$rows=array();
	while($r=mysqli_fetch_assoc($result)){
		$rows[]=$r;
	}
	return $rows;
}

//【物流查看該筆訂單詳細內容】
function cargetOrderDetail($oID){
	global $db;
	$sql="select distinct product.uID, order_item.status from product inner join order_item on product.pID = order_item.pID where order_item.oID=?;";
	$stmt=mysqli_prepare($db,$sql);
	mysqli_stmt_bind_param($stmt,"i",$oID);
	mysqli_stmt_execute($stmt);
	$result=mysqli_stmt_get_result($stmt);
	$rows=array();
	while($r=mysqli_fetch_assoc($result)){
		$rows[]=$r;
	}
	return $rows;
}
// 【物流處理訂單】
function cardealProduct($oID, $uID){
    global $db;
	$sql="update order_item set status = '已送達' where oID = ? and pID in (select pID from product where uID = ?);";
	$stmt=mysqli_prepare($db,$sql);
	mysqli_stmt_bind_param($stmt,"ii", $oID, $uID);
	mysqli_stmt_execute($stmt);
	return True;
}
?>