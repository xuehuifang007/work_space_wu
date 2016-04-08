<?php
require_once('../ebusclient/Result.php');
require_once('../../db_config/db.php');
//1、取得MSG参数，并利用此参数值生成验证结果对象
$tResult = new Result();
$tResponse = $tResult->init($_POST['MSG']);

if ($tResponse->isSuccess()){
	//2、支付成功
    $OrderNo = $tResponse->getValue('OrderNo');
    $OrderNoa = $OrderNo;
    $Amount = $tResponse->getValue('Amount');
    $order_arr = explode('_',$OrderNo);
    $OrderNo = $order_arr[0];
    //改变协议状态
    $msg_info = get_deal_info($con,$OrderNo,$Amount,$OrderNoa);
    mysqli_close($con);
}else{
	//3、失败
    $msg_info = "<h2 align='center'>支付接口验证失败</h2>";
}
    header("Content-type:text/html;charset=utf-8");
    echo $msg_info;
    if($msg_info != "<h2 align='center'>支付失败，请牢记订单号'{$OrderNoa}'!</h2>"){
        header("Content-type:text/html;charset=utf-8");
        header("refresh:3;url=http://city.5656111.com/Member/Goods/send_push_msg/code/".$OrderNo);
    }




//验证订单信息，修改订单的状态，修改协议的状态
function get_deal_info($con,$OrderNo,$Amount,$OrderNoa){
    $select_sql = "select id,goods_id,hz_id,cz_id,goods_fare from `tp_cshy_deal` where deal_code='{$OrderNo}' and deal_state = '0' and pay_state = '0' limit 1";
    $deal_result = mysqli_query($con,$select_sql);
    $deal_info = mysqli_fetch_array($deal_result);
    if(empty($deal_info)){
        $msg = "<h2 align='center'>此订单已支付!</h2>";
    }else{
        //判断运费
        if($deal_info['goods_fare'] > $Amount){
            $msg="<h2 align='center'>运费不够!</h2>";
        }else{
            $msg = do_pay_success($con,$OrderNo,$deal_info,$Amount,$OrderNoa);
        }

    }
    return $msg;
}

//支付成功
function do_pay_success($con,$OrderNo,$deal_info,$Amount,$OrderNoa){
    $msg = "";
    $select_sql = "select status,recharge_state from `tp_cshy_recharge` where operation=2 and order_code={$OrderNo}";
    $recharge_result = mysqli_query($con,$select_sql);
    $recharge_info = mysqli_fetch_array($recharge_result);
    if($recharge_info['status'] == 2){
        $msg = "<h2 align='center'>支付失败，请牢记订单号'{$OrderNo}'!</h2>";
    }elseif(($recharge_info['recharge_state'] == 0) && ($recharge_info['status'] == 1)){
        //修改订单的状态
        $change_order_state = change_order_state($con,$OrderNo,$deal_info);
        //修改协议的状态
        $change_deal_state = change_deal_state($con,$OrderNo,$deal_info);
        //写入现结用户的资金
        $change_user = change_user($con,$OrderNo,$deal_info,$Amount);
        //货源的状态修改
        $change_goods_state = change_goods_state($con,$deal_info);
        //财务信息写入
        write_fnc($deal_info);
        //写入支付日志
        if( ($change_order_state == true) && ($change_deal_state == true) && ($change_user == true) && ($change_goods_state == true) ){
            change_recharge($con,$OrderNoa,$Amount);
        }else{
            change_recharge($con,$OrderNoa,$Amount,2);
            $msg = "<h2 align='center'>支付失败，请牢记订单号'{$OrderNo}'!</h2>";
        }
    }else{
        $msg = "<h2 align='center'>订单支付完成!</h2>";
    }
    return $msg;
}

//支付成功，写入财务信息
function write_fnc($deal_info){
    $uri = "http://city.5656111.com/Member/GetAjax/get_pay";
    $pass = md5(md5($deal_info['id'].$deal_info['hz_id']."123456"));
    // 参数数组
    $data = array (
        'deal_id' => $deal_info['id'],
        'money'=>$deal_info['goods_fare'],
        'uid'=>$deal_info['hz_id'],
        'pass'=>$pass
    );
    $data = http_build_query($data);

    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_URL, $uri );
    curl_setopt ( $ch, CURLOPT_POST, 1 );
    curl_setopt ( $ch, CURLOPT_HEADER, 0 );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
    $return = curl_exec ( $ch );
    curl_close ( $ch );
    if($return == 1){
        return true;
    }else{
        //错误日志
    }
}



//修改订单的状态
function change_order_state($con,$OrderNo,$deal_info){
    $sql = "update `tp_cshy_order` set deal_id='{$deal_info['id']}',is_success='1',deal_state='1' where goods_id='{$deal_info['goods_id']}' and cz_id='{$deal_info['cz_id']}' and is_success='0' and status='1'";
    $result = mysqli_query($con,$sql);
    $update_result = mysqli_affected_rows($con);
    if(!$update_result){
        return false;
    }else{
        return true;
    }
}

//修改协议状态
function change_deal_state($con,$OrderNo,$deal_info){
    $time = time();
    $sql = "update `tp_cshy_deal` set deal_state='1',state_time='{$time}',pay_state='1',xj_type='1',fnpay_state='2',fnpay_time='{$time}',sendtime='{$time}',status='1' where id='{$deal_info['id']}'";
    $result = mysqli_query($con,$sql);
    $update_result = mysqli_affected_rows($con);
    if(!$update_result){
        return false;
    }else{
        return true;
    }
}

//修改资金
function change_user($con,$OrderNo,$deal_info,$Amount){
    $sql = "update `tp_cshy_user` set tspo_fee = tspo_fee + {$Amount}  where uid={$deal_info['hz_id']}";
    $result = mysqli_query($con,$sql);
    $update_result = mysqli_affected_rows($con);
    if(!$update_result){
        return false;
    }else{
        return true;
    }
}

//修改货源的状态
function change_goods_state($con,$deal_info){
    $sql = "update `tp_cshy_goods` set goods_state=2 where id={$deal_info['goods_id']}";
    $result = mysqli_query($con,$sql);
    $update_result = mysqli_affected_rows($con);
    if(!$update_result){
        return false;
    }else{
        return true;
    }
}

//添加资金记录
function change_recharge($con,$OrderNo,$Amount,$state=1){
    $sql = "update `tp_cshy_recharge` set recharge_state=1,recharge_money={$Amount},status={$state} where operation=2 and order_code='{$OrderNo}'";
    $result = mysqli_query($con,$sql);
    $update_result = mysqli_affected_rows($con);
    if(!$update_result){
        return false;
    }else{
        return true;
    }
}

?>