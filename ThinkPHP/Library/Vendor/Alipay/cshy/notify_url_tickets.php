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
$msg_info = "";

$con=mysqli_connect("wuliubang.mysql.rds.aliyuncs.com","wuliubang","xy990622","wuliubang");
if (mysqli_connect_errno($con)){
	$msg_info = "Failed to connect to MySQL: " . mysqli_connect_error();
}
	mysqli_query($con,'set names utf8');
		
if($verify_result) {//验证成功
		$OrderNo = $_POST['out_trade_no'];
		$Amount = $_POST['total_fee'];
		$intro = $_POST['subject'];
	if($_POST['trade_status'] == 'WAIT_BUYER_PAY') {	
		//请不要修改或删除
		echo "success";		
		
    }else if($_POST['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
		//担保交易	
		$msg_info = add_ticket($con,$OrderNo,$Amount);
		//请不要修改或删除
		echo "success";		
			
    }else if($_POST['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS') {
		//等待确认收货
		//请不要修改或删除
        echo "success";		
		
    }else if($_POST['trade_status'] == 'TRADE_FINISHED') {
		//针对及时到账，仅通知1次	
		$msg_info = add_ticket($con,$OrderNo,$Amount);
		//请不要修改或删除
		echo "success";	
		
    }else {
		//其他状态判断
		echo "success";
		
    }
}else{
		//验证失败
		echo "fail";
}
		//关闭数据库
		mysqli_close($con);
					
//验证订单信息、添加运票、更改支付状态				
function add_ticket($con,$OrderNo,$Amount){
		$select_exist = "select id,user_id,recharge_money,ticket_num,recharge_state from `tp_cshy_recharge` where order_code = '$OrderNo'";
		$result_exist = mysqli_query($con,$select_exist);
		$e_row = mysqli_fetch_array($result_exist);
		$msg_result = "";
	if(empty($e_row)){
					$msg_result = "<h2 align='center'>此未支付订单号不存在</h2>";
	}else{
		if($e_row['recharge_state'] == '0' || $e_row['recharge_state'] == '2'){
			if($Amount < $e_row["recharge_money"]){
					$msg_result = "<h1 align='center'>支付金额验证错误，请联系客服</h1>";
			}else{
					$update_result = false;
					//给用户增加运票
					if($e_row['recharge_state'] == '0'){ $update_result = add_ticket_exec($e_row , $con); }
				if(!$update_result){
				    echo  "<h3 align='center'><span style='color:red'>支付失败</span>，牢记您本次的支付单号".$OrderNo."，请联系客服</h3>";
					exit(0);
				}else{
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
function add_ticket_exec($e_row,$con){
	$return_info = true;
	$ticket_num = $e_row['ticket_num'];
	$user_id = $e_row['user_id'];
	$recharge_id = $e_row["id"];
	$update_sql = "update `tp_cshy_user` set ticket = ticket + '$ticket_num',total_ticket = total_ticket + '$ticket_num' where uid  = '$user_id'";
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