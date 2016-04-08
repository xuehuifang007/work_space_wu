<?php
class_exists('TrxRequest') or require(dirname(__FILE__) . '/core/TrxRequest.php');
class_exists('Json') or require(dirname(__FILE__) . '/core/Json.php');
class_exists('IChannelType') or require(dirname(__FILE__) . '/core/IChannelType.php');
class_exists('IPaymentType') or require(dirname(__FILE__) . '/core/IPaymentType.php');
class_exists('INotifyType') or require(dirname(__FILE__) . '/core/INotifyType.php');
class_exists('DataVerifier') or require(dirname(__FILE__) . '/core/DataVerifier.php');
class_exists('ILength') or require(dirname(__FILE__) . '/core/ILength.php');
class_exists('IPayTypeID') or require(dirname(__FILE__) . '/core/IPayTypeID.php');
class_exists('IInstallmentmark') or require(dirname(__FILE__) . '/core/IInstallmentmark.php');
class_exists('ICommodityType') or require(dirname(__FILE__) . '/core/ICommodityType.php');
class_exists('IQueryType') or require(dirname(__FILE__) . '/core/IQueryType.php');
class QueryOrderRequest extends TrxRequest {
	public $request = array (
		"TrxType" => IFunctionID :: TRX_TYPE_QUERY,
		"PayTypeID" => "",
		"OrderNo" => "",
		"QueryDetail" => ""
	);
	function __construct() {
	}

	protected function getRequestMessage() {
		Json :: arrayRecursive($this->request, "urlencode", false);
		$tMessage = json_encode($this->request);
		$tMessage = urldecode($tMessage);
		return $tMessage;
	}

	/// 支付请求信息是否合法
	protected function checkRequest() {
		if (!DataVerifier :: isValidString($this->request["OrderNo"], ILength :: ORDERID_LEN))
			throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1100, "未设定交易编号！");
		if (!($this->request["QueryDetail"] === IQueryType :: QUERY_TYPE_STATUS) && !($this->request["QueryDetail"] === IQueryType :: QUERY_TYPE_DETAIL))
			throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1100, "QueryType设定非法！");
	}

    //验证订单信息，修改订单的状态，修改协议的状态
    function get_deal_info($con,$OrderNo,$Amount,$OrderNoa){
        $return_msg = "";
        $select_sql = "select id,goods_id,hz_id,cz_id,goods_fare from `tp_cshy_deal` where deal_code='{$OrderNo}' and deal_state = '0' and pay_state = '0' limit 1";
        $deal_result = mysqli_query($con,$select_sql);
        $deal_info = mysqli_fetch_array($deal_result);

        if(empty($deal_info)){
            //$msg = "<h2 align='center'>此订单已支付!</h2>";
        }else{
            //判断运费
            if($deal_info['goods_fare'] > $Amount){
                //$return_msg="<h2 align='center'>运费不够!</h2>";
            }else{
                $return_msg = $this->do_pay_success($con,$OrderNo,$deal_info,$Amount,$OrderNoa);
            }
        }
        return $return_msg;
    }

//支付成功
    function do_pay_success($con,$OrderNo,$deal_info,$Amount,$OrderNoa){
        $msg = "";
        //修改订单的状态
        $change_order_state = $this->change_order_state($con,$OrderNo,$deal_info);
        //修改协议的状态
        $change_deal_state = $this->change_deal_state($con,$OrderNo,$deal_info);
        //写入现结用户的资金
        $change_user = $this->change_user($con,$OrderNo,$deal_info,$Amount);
        //货源的状态修改
        $change_goods_state = $this->change_goods_state($con,$deal_info);
        //财务信息写入
        $fnc_state = $this->write_fnc($deal_info);
        //写入支付日志

        //echo $change_order_state."---".$change_deal_state."---".$change_user."---".$change_goods_state."---".$fnc_state."eeee".false."<br>";

        if(($change_order_state == true) && ($change_deal_state == true) && ($change_user == true) && ($change_goods_state == true) ){
            $this->change_recharge($con,$OrderNoa,$Amount);
        }else{
            $this->change_recharge($con,$OrderNoa,$Amount,2);
            //$msg = "<h2 align='center'>支付失败，请牢记订单号'{$OrderNoa}'!</h2>";
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
        mysqli_query($con,$sql);
        $affected_rows = mysqli_affected_rows($con);
        if($affected_rows == false){
            return false;
        }else{
            return true;
        }
    }

    //修改协议状态
    function change_deal_state($con,$OrderNo,$deal_info){
        $time = time();
        $sql = "update `tp_cshy_deal` set deal_state='1',state_time='{$time}',pay_state='1',xj_type='1',fnpay_state='2',fnpay_time='{$time}',sendtime='{$time}',status='1' where id='{$deal_info['id']}'";
        mysqli_query($con,$sql);

        $affected_rows = mysqli_affected_rows($con);
        if($affected_rows == false){
            return false;
        }else{
            return true;
        }
    }

    //修改资金
    function change_user($con,$OrderNo,$deal_info,$Amount){
        $sql = "update `tp_cshy_user` set tspo_fee = tspo_fee + {$Amount}  where uid={$deal_info['hz_id']}";
        mysqli_query($con,$sql);
        $affected_rows = mysqli_affected_rows($con);

        if($affected_rows == false){
            return false;
        }else{
            return true;
        }
    }

    //修改货源的状态
    function change_goods_state($con,$deal_info){
        $sql = "update `tp_cshy_goods` set goods_state=2 where id={$deal_info['goods_id']}";
        mysqli_query($con,$sql);
        $affected_rows = mysqli_affected_rows($con);

        if($affected_rows == false){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 添加资金记录
     */
    function change_recharge($con,$OrderNoa,$Amount,$state=1){
        $sql = "update `tp_cshy_recharge` set recharge_state='1',recharge_money='{$Amount}',status='{$state}' where operation='2' and order_code='{$OrderNoa}'";
        mysqli_query($con,$sql);
        $affected_rows = mysqli_affected_rows($con);

        if($affected_rows == false){
            return false;
        }else{
            return true;
        }
    }

    
    /**
     * 【购买邦票】
     * 验证订单信息、添加运票、更改支付状态
     * @param $con
     * @param $OrderNo
     * @param $Amount
     * @return string
     */
    function add_ticket($con,$OrderNo,$Amount){
        $select_exist = "select id,user_id,recharge_money,ticket_num,recharge_state from `tp_cshy_recharge` where order_code = '$OrderNo'";
        $result_exist = mysqli_query($con,$select_exist);
        $e_row = mysqli_fetch_array($result_exist);
        $msg_result = "";
        if(empty($e_row)){
            //$msg_result = "<h2 align='center'>此未支付订单号不存在</h2>";
        }else{
            if($e_row['recharge_state'] == '0' || $e_row['recharge_state'] == '2'){
                if($Amount < $e_row["recharge_money"]){
                    //$msg_result = "<h2 align='center'>支付金额验证错误，请联系客服</h2>";
                }else{
                    $update_result = false;
                    //给用户增加运票
                    if($e_row['recharge_state'] == '0'){ $update_result = $this->add_ticket_exec($e_row,$con); }
                    if(!$update_result){
                        //echo  "<h3 align='center'><span style='color:red'>支付失败</span>，牢记您本次的支付单号".$OrderNo."，请联系客服</h3>";
                    }else{
                        //$msg_result = "<h2 align='center'>支付成功</h2>";
                    }
                }
            }elseif($e_row['recharge_state'] == '1'){
                //$msg_result = "<h2 align='center'>已经支付成功</h2>";
            }
        }
        return $msg_result;
    }


    /**
     * 将车主的运票数量增加
     * @param $e_row
     * @param $con
     * @return bool
     */
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


    /**
     * 支付保证金
     * 验证订单信息、添加运票、更改支付状态
     */
    function add_bail($con,$OrderNo,$Amount){
        $select_exist = "select id,user_id,recharge_money,recharge_state from `tp_cshy_recharge` where order_code = '$OrderNo'";
        $result_exist = mysqli_query($con,$select_exist);
        $e_row = mysqli_fetch_array($result_exist);
        $msg_result = "";
        if(empty($e_row)){
            //$msg_result = "<h2 align='center'>此未支付订单号不存在</h2>";
        }else{
            if($e_row['recharge_state'] == '0' || $e_row['recharge_state'] == '2'){
                if($Amount < $e_row["recharge_money"]){
                    //$msg_result = "<h3 align='center'>支付金额验证错误、订单号 ".$OrderNo.",请联系客服</h3>";
                }else{
                    $update_result = false;
                    //给用户增加保证金
                    if($e_row['recharge_state'] == '0'){  $update_result = $this->add_bail_exec($e_row , $con); }
                    if(!$update_result){
                        //$msg_code =  "<h3 align='center'><span style='color:red'>支付失败</span>，牢记您本次的支付单号".$OrderNo."，请联系客服</h3>";
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
                        //$msg_result = "<h2 align='center'>支付成功</h2>";
                    }
                }
            }elseif($e_row['recharge_state'] == '1'){
                //$msg_result = "<h2 align='center'>已经支付成功</h2>";
            }
        }
        return $msg_result;
    }


    /**
     * 将车主的运票数量增加
     * @param $e_row
     * @param $con
     * @return bool
     */
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
}
?>