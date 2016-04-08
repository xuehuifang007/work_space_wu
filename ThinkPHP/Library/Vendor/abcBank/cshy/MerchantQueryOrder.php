<?php
require_once(ABC_PATH.'ebusclient/QueryOrderRequest.php');

//1.接收参数
$get_arr = I('get.');
$payTypeID = "ImmediatePay";
$QueryType = "true";
$sucStutas = array("03","04");
//2.构造请求对象
$tRequest = new QueryOrderRequest();
$tRequest->request["PayTypeID"] = $payTypeID; //设定交易类型
$tRequest->request["OrderNo"] = $get_arr['odc']; //设定订单编号 （必要信息）
$tRequest->request["QueryDetail"] = $QueryType; //设定查询方式
//3.传送请求
$tResponse = $tRequest->postRequest();
//4.支付请求提交成功，返回结果信息
if ($tResponse->isSuccess()){
    //5.获取订单信息
    $orderVality = $tResponse->GetValue("Order");
    if($orderVality != null){
        //6.解析返回信息
        $orderDetail = base64_decode($orderVality);
        $orderDetail = iconv("GB2312", "UTF-8", $orderDetail);
        $orderObj = new Json($orderDetail);
        //7.获取订订单状态
        $OrderStatus = $orderObj->GetValue("Status");
        //8.判断订单是否支付成功
        if(in_array($OrderStatus,$sucStutas)){
            $return_arr['isSuccess'] = true;
            $return_arr['Amount'] = $orderObj->GetValue("OrderAmount");
            $return_arr['OrderNo'] = $orderObj->GetValue("OrderNo");
        }else{
            $return_arr['isSuccess'] = false;
            $return_arr['code'] = $orderObj->getReturnCode();
            $return_arr['msg'] = $orderObj->getErrorMessage();
        }
    }else{
        $return_arr['isSuccess'] = false;
        $return_arr['code'] = $tResponse->getReturnCode();
        $return_arr['msg'] = $tResponse->getErrorMessage();
    }
}else{
    $return_arr['isSuccess'] = false;
    $return_arr['code'] = $tResponse->getReturnCode();
    $return_arr['msg'] = $tResponse->getErrorMessage();
}
//将返回数组加入全局中
$GLOBALS['return_arr'] = $return_arr;

?>