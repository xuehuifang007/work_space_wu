<?php
namespace Common\Model;
use Think\Model;
/**
 * 协议发送模型
 * @author baiwei
 * Created by PhpStorm.
 * User: mnmnwq
 * Date: 2015/6/8
 * Time: 9:02
 */
class SendDealModel extends BasicModel{
    public function _initialize(){
        parent::_initialize();
    }

    /**
     * @param $order_id
     * @param string $cz_id
     * @param string $car_id
     * @param string $goods_id
     * @param string $is_web 是否是web发起 1 web 2 app
     * @return int|string|void
     */
    public function send_deal($order_id,$cz_id="",$car_id="",$goods_id='',$is_web='1'){
        //获得订单的信息
        $order_field = "id,deal_state,status,goods_id,car_id,cz_id,load_time,deal_id,bid_money";
        if($order_id != ""){
            $order_info = $this->order_table->where("id={$order_id}")->field($order_field)->find();
        }else{
            $order_map['cz_id'] = $cz_id;
            $order_map['car_id'] = $car_id;
            $order_map['goods_id'] = $goods_id;
            $order_info = $this->order_table->where($order_map)->field($order_field)->find();
        }
        //判断订单的合法性
        $order_state = $this->deal_order($order_info);
        if($order_state != "T"){
            return $order_state;
        }
        //获得货物的信息
        $goods_field = "id,pay_type,goods_name,goods_weight,goods_square,load_time,endtime,is_receipt,user_id,goods_class";
        $goods_info = $this->goods_table->where("id={$order_info['goods_id']}")->field($goods_field)->find();
        $get_info_obj = new \Common\Model\TableInfoModel();
        //货主的信息
        $hz_field = "id,mt,shenfenzheng,name_true,com_name";
        $hz_info = $get_info_obj->get_table_info(MEMBER_TABLE,"id={$goods_info['user_id']}",$hz_field);
        //车主的信息
        $cz_field = "id,name_true,shenfenzheng,mt";
        $cz_info = $get_info_obj->get_table_info(MEMBER_TABLE,"id={$order_info['cz_id']}",$cz_field);
        //司机信息
        $car_field = "car_driver,car_num";
        $car_info = $get_info_obj->get_table_info(CAR_TABLE,"id={$order_info['car_id']}",$car_field);
        $driver_field = "id,driver_name,driver_tel";
        $driver_info = $get_info_obj->get_table_info(DRIVER_TABLE,"id={$car_info['car_driver']}",$driver_field);
        M()->startTrans();
        //app端不能发起现结的协议
        if(($is_web==2)&&($goods_info['pay_type']==1)){
            return 1014;
        }
        //判断是否有已发的协议
        $repeat_result = $this->deal_repeat($order_info);
        if($repeat_result){
            M()->rollback();
            return '1001';
        }
        //获得处理完的数据
        $data = $this->handele_data($order_info,$goods_info,$hz_info,$cz_info,$driver_info,$car_info,$is_web);
        //执行添加的操作
        $intodeal_result = $this->deal_table->filter('strip_tags')->add($data);
        if(!$intodeal_result){
            M()->rollback();
            return '1002';
        }else{
            //跳转到支付接口,月结扣费
            $pay_type = $goods_info['pay_type'];
            //判断结算方式
            if($pay_type == 2){
                //月结
                return $this->monthly_send($order_info,$intodeal_result,$goods_info,$data);
            }elseif($pay_type == 1){
                //现结
                return $this->currently_send($data,$intodeal_result);
            }else{
                //现场结算
                return $this->spot_send($order_info,$intodeal_result,$goods_info,$data);
            }

        }
    }

    /**
     * 月结货主发送协议的操作
     */
    public function monthly_send($order_info,$intodeal_result,$goods_info,$data){
        //判断信用额度
        $credit = call_credit($order_info['bid_money']);
        if($credit == 3){
            M()->rollback();
            return '1003';
        }
        //修改协议状态
        $deal_data['deal_state'] = 1;
        $deal_data['state_time'] = time();
        $deal_data['pay_state'] = 1;
        $deal_data['fnpay_state'] = 1;
        $deal_data['fnpay_time'] = time();
        $result = $this->deal_table->where("id={$intodeal_result}")->save($deal_data);
        if(!$result){
            M()->rollback();
            return '1004';
       }
        //订单的状态修改
        $change_result = $this->change_order_state($order_info['id'],1,$intodeal_result);
        if(!$change_result){
            M()->rollback();
            return '1005';
        }
        //货源的状态修改
        $cgoods_result = $this->change_goods_state($goods_info['id'],2);
        if(!$cgoods_result){
            M()->rollback();
            return '1006';
        }
        //订单冲突
        $clash_result = $this->clash_order($data['deal_code'],'1');
        if(!$clash_result){
            M()->rollback();
            return '1007';
        }
        //发送推送消息
        $push_obj = new \Think\CityPush();
        $push_obj->push_deal_message($order_info['cz_id'],$intodeal_result,1);
        M()->commit();
        return '1000';
    }

    /**
     * 现结的货主发起协议
     */
    public function currently_send($data,$intodeal_result){
        //订单冲突
        $clash_result = $this->clash_order($data['deal_code'],'1');
        if(!$clash_result){
            M()->rollback();
            return 1008;
        }
        M()->commit();
        //现结跳转到支付界面
        header("Location:/index.php/Member/Goods/pay_tspo_fee/dealid/{$intodeal_result}");
    }

    /**
     * 现场结算的货主发起协议
     */
    public function spot_send($order_info,$intodeal_result,$goods_info,$data){
        //现场结算
        //修改协议状态
        $deal_data['deal_state'] = 1;
        $deal_data['state_time'] = time();
        $deal_data['pay_state'] = 1;
        $deal_data['fnpay_state'] = 0;
        $deal_data['fnpay_time'] = 0;
        $result = $this->deal_table->where("id={$intodeal_result}")->save($deal_data);
        if(!$result){
            M()->rollback();
            return 1009;
        }
        //订单的状态修改
        $change_result = $this->change_order_state($order_info['id'],1,$intodeal_result);
        if(!$change_result){
            M()->rollback();
            return 1010;
        }
        //货源的状态修改
        $cgoods_result = $this->change_goods_state($goods_info['id'],2);
        if(!$cgoods_result){
            M()->rollback();
            return 1011;
        }
        //订单冲突
        $clash_result = $this->clash_order($data['deal_code'],'1');
        if(!$clash_result){
            M()->rollback();
            return 1012;
        }
        //发送推送消息
        $push_obj = new \Think\CityPush();
        $push_obj->push_deal_message($order_info['cz_id'],$intodeal_result,1);
        M()->commit();
        return 1000;
    }

    /**
     * 判断是否重复发送协议
     */
    public function deal_repeat($order_info){
        $map['cz_id'] = $order_info['cz_id'];
        $map['goods_id'] = $order_info['goods_id'];
        $map['status'] = 1;
        $map['pay_state'] = 1;
        $deal_result = $this->deal_table->where($map)->field("id")->find();
        return $deal_result;
    }

    /**
     * 发送协议的时候判断当前的订单是否合法
     */
    public function deal_order($order_info){
        if($order_info['deal_state'] == 1){
            return 1015;;
        }
        if($order_info['deal_state'] == 3 || $order_info['deal_state'] == 9){
            return 1016;
        }
        if($order_info['load_time'] < time()){
            return 1017;
        }
        if($order_info['deal_id'] != 0){
            return 1018;
        }
        return "T";
    }

    /**
     * 处理协议表要添加的数据
     */
    public function handele_data($order_info,$goods_info,$hz_info,$cz_info,$driver_info,$car_info,$is_web){
        $data['launch_type'] = $is_web;
        //获得协议的种类
        $data['deal_type']=get_deal_type($goods_info['pay_type'],$goods_info['is_receipt']);
        //加入货物信息
        $data['goods_id'] = $goods_info['id'];
        $data['goods_name'] = $goods_info['goods_name'];
        $data['is_receipt'] = $goods_info['is_receipt'];
        $data['goods_class'] = $goods_info['goods_class'];
        //协议状态
        $data['deal_state'] = 0;
        //发起时间
        $data['sendtime'] = time();
        $data['status'] = 1;
        //协议编号
        $data['deal_code'] = creatDealCode();
        $data['order_mny'] = $order_info['bid_money'];
        //判断当前书否是现场结算，修改现结的类型
        if($goods_info['pay_type'] == 3){
            $data['pay_type'] = 1;
            $data['xj_type'] = 5;
            $data['goods_fare'] = 0;
        }else{
            $data['goods_fare'] = $order_info['bid_money'];
            $data['pay_type'] = $goods_info['pay_type'];
        }
        //加入货主信息
        $data['hz_id'] = $hz_info['id'];
        //判断个体户还是公司
        if($hz_info['com_name'] == 0){
            //个体户
            $data['hz_company'] = $hz_info['name_true'];
        }else{
            //公司
            $data['hz_company'] = $hz_info['com_name'];
        }
        $data['goods_weight'] = $goods_info['goods_weight'];
        $data['goods_square'] = $goods_info['goods_square'];
        $data['hz_name'] = $hz_info['name_true'];
        $data['hz_code'] = $hz_info['shenfenzheng'];
        $data['hz_tel'] = $hz_info['mt'];
        //加入车主信息
        $data['cz_id'] = $cz_info['id'];
        $data['cz_name'] = $cz_info['name_true'];
        $data['cz_card'] = $cz_info['shenfenzheng'];
        $data['cz_tel'] = $cz_info['mt'];
        //加入车辆和司机信息
        $data['car_num'] = $car_info['car_num'];
        $data['car_id'] = $order_info['car_id'];
        $data['driver_id'] = $driver_info['id'];
        $data['driver_name'] = $driver_info['driver_name'];
        $data['driver_tel'] = $driver_info['driver_tel'];
        return $data;
    }

    /**
     * 删除冲突的订单
     * @param $deal_code 协议的编号
     * @param int $k
     * @return bool
     * g0002f0034e0000
     */
    public function  clash_order($deal_code,$k=2){
        $deal_field = "id,cz_id,goods_id,car_id";
        $deal_where = "deal_code={$deal_code} and hz_id={$this->id} and status=1";
        $deal_info = $this->deal_table->where($deal_where)->field($deal_field)->find();
        $goods_info = $this->goods_table->where("id={$deal_info['goods_id']}")->field("endtime,load_time")->find();
        $map['load_time'] = array('ELT',$goods_info['endtime']);
        $map['endtime'] = array('EGT',$goods_info['load_time']);
        $map['status'] = 1;
        $map['car_id'] = $deal_info['car_id'];
        $map['is_success'] = 0;
        $map['cz_id'] = $deal_info['cz_id'];
        $map['goods_id'] = array('neq',$deal_info['goods_id']);
        $map['deal_state'] = 0;
        $order_info = $this->order_table->where($map)->field('id,goods_id')->select();
        $a="";
        if($order_info){
            foreach($order_info as $v){
                //订单临时表数据的添加
                $pro_data['deal_id'] = $deal_info['id'];
                $pro_data['goods_id'] = $v['goods_id'];
                $pro_data['order_id'] = $v['id'];
                $pro_data['car_id'] = $deal_info['car_id'];
                $pro_data['addtime'] = time();
                $pro_result = $this->prorder_table->add($pro_data);
                if(!$pro_result){
                    $content = "删除冲突订单:deal_id = ".$deal_info['id'].'##$pro_result = '.$pro_result."##".$this->prorder_table->getLastSql();
                    D("LogWrite")->write_log($content);
                    return false;
                }
                //要删除的协议的id
                $a = $a.$v['id'].',';
                $result = $this->goods_table->where("id={$v['goods_id']}")->setDec('bid_count');
                if(!$result){
                    $content = "删除冲突订单:deal_id = ".$deal_info['id'].'##$result = '.$result."##".$this->goods_table->getLastSql();
                    D("LogWrite")->write_log($content);
                    return false;
                }
            }
            $a = trim($a,',');
            $order_map['id'] = array('in',$a);
            $order_data['deal_state'] = 3;
            $order_data['status'] = 9;
            $order_result = $this->order_table->where($order_map)->save($order_data);
            if(!$order_result){
                $content = "删除冲突订单:deal_id = ".$deal_info['id'].'##$order_result = '.$order_result."##".$this->order_table->getLastSql();
                D("LogWrite")->write_log($content);
                return false;
            }
        }
        return true;
    }

    /**
     * 修改货物的状态
     * @param $goods_id 货物的id
     * @param $state  修改的状态
     * @return bool
     * g0002f0040e0000
     */
    public function change_goods_state($goods_id,$state){
        $data['goods_state'] = $state;
        $result = $this->goods_table->where("id={$goods_id}")->save($data);
        if(!$result){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 修改订单状态
     * @param $order_id 需要修改状态的订单id
     * @param $state   要修改的状态
     * @param $deal_id 需要恢复的协议的id
     * @return bool
     */
    public function change_order_state($order_id,$state,$deal_id){
        $data['deal_state'] = $state;
        if($state == 0){
            $data['deal_id'] = 0;
        }else{
            $data['deal_id'] = $deal_id;
        }
        $result = $this->order_table->where("id={$order_id}")->save($data);
        if(!$result){
            return false;
        }
        return true;
    }

}