<?php
/* * 
 * 功能：支付宝页面跳转同步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：支付宝标准双接口
 */
//引入配置文件、服务器通知类
require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();

if($verify_result) {//验证成功
    $return_arr['isSuccess'] = true;
    $return_arr['OrderNo'] = $_GET['out_trade_no'];
    $return_arr['Amount'] = $_GET['out_trade_no'];
}else{
	$return_arr['isSuccess'] = false;
}

?>
