<?php
/* *
 * 功能：支付宝服务器异步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 */
require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();

if($verify_result) {//验证成功
        $return_arr['isSuccess'] = true;
        $return_arr['OrderNo'] = $_POST['out_trade_no'];
        $return_arr['Amount']  = $_POST['total_fee'];
        $return_arr['subject'] = $_POST['subject'];
        $return_arr['trade_status'] = $_POST['trade_status'];
}else{
        $return_arr['isSuccess'] = false;
}
        //将返回值加入全局数组
        $GLOBALS['return_arr'] = $return_arr;
?>