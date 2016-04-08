<?php
require_once(ABC_PATH.'ebusclient/Result.php');
//1、取得MSG参数，并利用此参数值生成验证结果对象
$tResult = new Result();
$tResponse = $tResult->init($_POST['MSG']);

if ($tResponse->isSuccess()){
    //2.支付成功
    $return_arr['isSuccess'] = true;
    $return_arr['OrderNo']   = $tResponse->getValue("OrderNo");
	$return_arr['Amount']   = $tResponse->getValue("Amount");
	$return_arr['BatchNo']   = $tResponse->getValue("BatchNo");
	$return_arr['MerchantRemarks']   = $tResponse->getValue("MerchantRemarks");
	$return_arr['PayType']   = $tResponse->getValue("PayType");
}else{
	//3、失败
    $return_arr['isSuccess'] = $tResponse->getReturnCode();
    $return_arr['code'] = $tResponse->getReturnCode();
    $return_arr['msg'] = $tResponse->getErrorMessage();
}
$GLOBALS['return_arr'] = $return_arr;
//dump($return_arr);
//global $return_arr;

?>