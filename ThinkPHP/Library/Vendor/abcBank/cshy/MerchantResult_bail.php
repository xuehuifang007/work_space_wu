﻿<?php
require_once('../ebusclient/Result.php');
require_once('../../db_config/db.php');
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
    //添加保证金
    $msg_info = add_bail($con,$OrderNo,$Amount);
    mysqli_close($con);
}else{
	//3、失败
    $msg_info = "<h2 align='center'>支付接口验证失败</h2>";
	//print ("<br>ReturnCode   = [" . $tResponse->getReturnCode() . "]<br>");
	//print ("ErrorMessage = [" . $tResponse->getErrorMessage() . "]<br>");
}
    echo $msg_info;
    header("refresh:0;url=http://city.5656111.com/Member/");


//验证订单信息、添加运票、更改支付状态
function add_bail($con,$OrderNo,$Amount){
    $select_exist = "select id,user_id,recharge_money,recharge_state from `tp_cshy_recharge` where order_code = '$OrderNo'";
    $result_exist = mysqli_query($con,$select_exist);
    $e_row = mysqli_fetch_array($result_exist);
    $msg_result = "";
    if(empty($e_row)){
        $msg_result = "<h2 align='center'>此未支付订单号不存在</h2>";
    }else{
        if($e_row['recharge_state'] == '0' || $e_row['recharge_state'] == '2'){
            if($Amount < $e_row["recharge_money"]){
                $msg_result = "<h3 align='center'>支付金额验证错误、订单号 ".$OrderNo.",请联系客服</h3>";
            }else{
                $update_result = false;
                //给用户增加保证金
                if($e_row['recharge_state'] == '0'){ $update_result = add_bail_exec($e_row , $con); }

                if(!$update_result){
                    echo  "<h3 align='center'><span style='color:red'>支付失败</span>，牢记您本次的支付单号".$OrderNo."，请联系客服</h3>";
                    exit(0);
                }else{

                    //请求ThinkPHP 记账接口
                    $req_url = "http://city.5656111.com/Member/GetAjax/fnczpay_bail";
                    $post_arr["bail_mny"] = $e_row["recharge_money"];
                    $post_arr["user_id"] = $e_row["user_id"];
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_URL,$req_url);
                    curl_setopt($ch,CURLOPT_HEADER,0);
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
                    curl_setopt($ch,CURLOPT_FORBID_REUSE,1);
                    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,0);
                    curl_setopt($ch,CURLOPT_TIMEOUT,10);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_arr));
                    $req_result = curl_exec($ch);
                    curl_close($ch);
                    //....假如记账失败，写入系统日志.....

                    $msg_result = "<h2 align='center'>支付成功</h2>";
                }
            }
        }elseif($e_row['recharge_state'] == '1'){
            $msg_result = "<h2 align='center'>已经支付成功</h2>";
        }
    }
    return $msg_result;
}


//将车主的运票数量增加
function add_bail_exec($e_row,$con){
    $return_info = true;
    $recharge_money = $e_row['recharge_money'];
    $user_id = $e_row['user_id'];
    $recharge_id = $e_row["id"];
    $update_sql = "update `tp_cshy_user` set 	deposit_mny = 	deposit_mny + '$recharge_money'  where uid  = '$user_id'";

    $result_update = mysqli_query($con,$update_sql);
    if(!$result_update){
        $re_update = mysqli_query($con,$update_sql);
        if(!$re_update){
            $return_info = false;
        }
    }

    //根据是否增加运票数量，给未支付的订单该状态
    if($result_update){
        $recharge_sql = "update `tp_cshy_recharge` set recharge_state = '1' where  id = '$recharge_id'";
    }else{
        $recharge_sql = "update `tp_cshy_recharge` set recharge_state = '2' where  id = '$recharge_id'";
    }
    $recharge_update = mysqli_query($con , $recharge_sql);
    if(!$recharge_update){
        mysqli_query($con , $recharge_sql);
    }
    return $return_info;
}

?>