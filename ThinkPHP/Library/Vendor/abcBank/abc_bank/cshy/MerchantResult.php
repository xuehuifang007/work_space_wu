<?php
require_once(ABC_PATH.'ebusclient/Result.php');
//1、取得MSG参数，并利用此参数值生成验证结果对象
$tResult = new Result();
$tResponse = $tResult->init($_POST['MSG']);

if ($tResponse->isSuccess()){
	//2、、支付成功
    $OrderNo = $tResponse->getValue("OrderNo");
    $Amount = $tResponse->getValue("Amount");
    $BatchNo = $tResponse->getValue("BatchNo");
    $intro = $tResponse->getValue("MerchantRemarks");
    $PayType = $tResponse->getValue("PayType");


}else{
	//3、失败
    $msg_info = "<h2 align='center'>支付接口验证失败</h2>";
	//print ("<br>ReturnCode   = [" . $tResponse->getReturnCode() . "]<br>");
	//print ("ErrorMessage = [" . $tResponse->getErrorMessage() . "]<br>");
}


?>