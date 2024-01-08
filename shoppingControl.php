<?php
require('shoppingModel.php');

$act=$_REQUEST['act'];
switch ($act) {

// 3a
// 【客戶顯示商品列表】
case "listProduct":
	$products=listProduct();
	echo json_encode($products);
	return;

// 【客戶將商品放入購物車】
case "addCart":	
	$id=(int)$_REQUEST['id'];
    $uID=(int)$_REQUEST['uID'];//商品編號
	addCart($id, $uID);
	return;

// 【客戶顯示商品詳細內容】
case "getProductDetail":	
	$id=(int)$_REQUEST['id'];//前端傳來的商品編號
	$product=getProductDetail($id);
	echo json_encode($product);
	return;
    
// 【客戶顯示自己的購物車】
case "listCart":
    $uID=(int)$_REQUEST['uID'];
	$cart=listCart($uID);
	echo json_encode($cart);
	return;
    
// 【客戶刪除購物車的內容】
case "delCart":
	$id=(int)$_REQUEST['id'];
    $uID=(int)$_REQUEST['uID'];
	delCart($id, $uID);
	return;
    
// 【計算客戶購物車的總金額】
case "cartTotal":
    $uID=(int)$_REQUEST['uID']; 
    $total_amount=cartTotal($uID);
    if ($total_amount['total_amount'] === null)
        echo json_encode(['total_amount' => 0]);
    else
        echo json_encode($total_amount);
    return;

// 【商家刪除商品】
case "delProduct":
	$id=(int)$_REQUEST['id'];
	delProduct($id);
	return;
    
// 【商家更新商品內容】
case "updateProduct":
    $jsonStr = $_POST['dat'];
    $product = json_decode($jsonStr);
    updateProduct($product->id, $product->name, $product->price, $product->stock, $product->content);
    return;

// 【商家新增商品】
case "addProduct":
    $jsonStr = $_POST['dat'];
    $product = json_decode($jsonStr);
    addProduct($product->name, $product->price, $product->stock, $product->content, $product->id);
    return;
    
// 【商家查看商品詳細內容】
case "listProductInfo":
    $uID=(int)$_REQUEST['uID'];
	$products=listProductInfo($uID);
	echo json_encode($products);
	return;
    
// 3b
// 【客戶結帳】
// 先在orders增加一筆紀錄, 再用orders的oID與orders_item的項目連結，最後清空購物車
case "finishOrder":
	$uID=(int)$_REQUEST['uID'];
    $totalPrice=(int)$_REQUEST['totalPrice'];
    // 判斷購物車總金額是否為0，避免訂單項目為空但有訂單紀錄
    if ($totalPrice == 0) {
        error_log("Total Price is 0. No further processing.");

    } else {
        addOrder($uID, $totalPrice);
        saveItems($uID);
        clearCart($uID);
        error_log("Order successfully processed. Cleared cart and saved items.");
    }
    return;
    
// 【客戶查看訂單】
case "listorder":
    $uID=(int)$_REQUEST['uID'];
	$order=listorder($uID);
	echo json_encode($order);
	return;
    
// 【客戶查看訂單詳細內容】
case "getOrderDetail":	
	$oID=(int)$_REQUEST['oID'];
	$orderItems=getOrderDetail($oID);
	echo json_encode($orderItems);
	return;
    
// 【客戶評價訂單】
case "evaluateOrder":	
	$itemID=(int)$_REQUEST['itemID'];
    $evaluation=(int)$_REQUEST['evaluation'];
	evaluateOrder($itemID, $evaluation);
	return;

// 【客戶完成訂單】
case "completeOrder":	
	$itemID = (int)$_REQUEST['itemID'];
    completeOrder($itemID);
    return;

// 【商家查看訂單】
case "selllistAllorder":
	$uID=(int)$_REQUEST['uID'];
	$order=selllistAllorder($uID);
	echo json_encode($order);
	return;
// 【商家處理訂單】
case "dealProduct":	
	$oID = (int)$_REQUEST['oID'];
    $uID = (int)$_REQUEST['uID'];
    dealProduct($oID, $uID);
    return;

// 【商家寄送出貨】
case "sendProduct":	
	$oID = (int)$_REQUEST['oID'];
    $uID = (int)$_REQUEST['uID'];
    sendProduct($oID, $uID);
    return;
// 【商家出貨】
case "achieveProduct":	
	$oID = (int)$_REQUEST['oID'];
    achieveProduct($oID);
    return;
    
// 3c
// 【商家查看訂單詳細內容】
case "sellgetOrderDetail":	
	$oID=(int)$_REQUEST['oID'];
	$uID=(int)$_REQUEST['uID'];
	$orderItems=sellgetOrderDetail($oID, $uID);
	echo json_encode($orderItems);
	return;
default:
  

// 【物流查看訂單】
case "carlistAllorder":
	$order=carlistAllorder();
	echo json_encode($order);
	return;

// 【物流查看該筆訂單的詳細內容】
case "cargetOrderDetail":	
	$oID=(int)$_REQUEST['oID'];
	$orderItems=cargetOrderDetail($oID);
	echo json_encode($orderItems);
	return;
// 【物流處理訂單】
case "cardealProduct":	
	$oID = (int)$_REQUEST['oID'];
    $uID = (int)$_REQUEST['uID'];
    cardealProduct($oID, $uID);
    return;

}

?>