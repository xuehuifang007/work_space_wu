<?php
namespace Think;
/**
 * 普货推送功能类
 * Created by PhpStorm.
 * User: baiwei
 * Date: 2015/5/14
 * Time: 17:14
 */
class CityPush{
    //货物推送表
    private $gipush_table;
    //货物表
    private $goods_table;
    //车辆表
    private $car_table;
    //用户表
    private $member_table;
    //地址表
    private $adrs_table;
    //目的地表
    private $eadrs_table;
    //协议表
    private $deal_table;
    //关于协议的推送表
    private $dealrmd_table;
    //预约推送表
    private $appointment_table;
    //当前用户的id
    protected $id;
    public function __construct(){
        $this->gipush_table = M("cshy_gipush");
        $this->goods_table = M("cshy_goods");
        $this->car_table = M("cshy_car");
        $this->member_table = M("member");
        $this->adrs_table = M("cshy_adrs");
        $this->eadrs_table = M("cshy_eadrs");
        $this->deal_table = M("cshy_deal");
        $this->dealrmd_table = M("cshy_dealrmd");
        $this->appointment_table = M("cshy_appointment");
        $this->id = $_SESSION['userData']['id'];
    }

    /**
     * 新添加货源的推送
     */
    public function push_new_goods($goods_id=131,$operation=1){
        $goods_class = $this->goods_table->where("id={$goods_id}")->getField("goods_class");
        //出发地推送信息
        $begin_count_info = $this->push_goods($goods_id,$operation);
        $end_count_info = array();
        if($goods_class != 11){
            //目的地推送信息
            $end_count_info = $this->push_goods($goods_id,$operation,1);
        }
        /*生成推送队列*/
        //连接两个数组
        if(!empty($begin_count_info['info']) && !empty($end_count_info['info'])){
            $end_count_info['info'] = array_diff($begin_count_info['info'],$end_count_info['info']);
            $merge_arr['info'] = array_merge($begin_count_info['info'],$end_count_info['info']);
        }elseif(!empty($begin_count_info['info']) && empty($end_count_info['info'])){
            $merge_arr['info'] = $begin_count_info['info'];
        }else{
            $merge_arr['info'] =$end_count_info['info'];
        }
        if($operation == 3){
            $gipush_info = $this->gipush_table->where("goods_id={$goods_id} and status=1")->field("cz_id")->select();
            $merge_arr['info'] = array_diff($merge_arr['info'],$gipush_info);
        }
        $merge_arr['count'] = count($merge_arr);
        if(!empty($merge_arr['info'])){
            //推送操作
            $this->do_push($merge_arr,$goods_id,$operation);
        }
    }

    /**
     * 调用推送的操作
     * 推送信息大数组
     * 货源的id
     * 推送的类型
     */
    public function do_push($count_info,$goods_id,$operation){
        if($count_info["count"] > 0){
            //推送成功的数量
            $push_count = $this->push_car_message($count_info['info'],$goods_id,$operation);
            if($push_count > 0){
                //给货源 push_count 增加推送数量
                $where_goods["id"] = $goods_id;
                $goods_result = $this->goods_table->where($where_goods)->setInc("push_count",$push_count);
                if(!$goods_result){
                    $this->goods_table->where($where_goods)->setInc("push_count",$push_count);
                }
            }
        }
    }

    /**
     * @param int $goods_id 货源id
     * @param int $operation 发送类型
     * @param int $is_end 是否是目的地推送
     * g0002f0013e0000
     */
    public function push_goods($goods_id=131,$operation=1,$is_end=0){
        //半径
        $radius = 0.15;
        ($is_end == 1)? $count_info = $this->get_end_count($radius,$goods_id):$count_info = $this->get_push_count($radius,$goods_id);
        //大于150条记录的要继续进行筛选 (150到100算符合条件)
        while($count_info['count']>150){
            if($radius == 0.05){
                break;
            }
            $radius = $radius - 0.05;
            ($is_end == 1)? $count_info = $this->get_end_count($radius,$goods_id):$count_info = $this->get_push_count($radius,$goods_id);
            if(($count_info['count']>=100) && ($count_info['count']<=150)){
                break;
            }elseif($count_info['count'] < 100){
                $radius = $radius + 0.05;
                ($is_end == 1)? $count_info = $this->get_end_count($radius,$goods_id):$count_info = $this->get_push_count($radius,$goods_id);
                break;
            }
        }
        return $count_info;
    }

    /**
     * 获得查询数量，和相应的查询数据
     * @param float $radius
     * @param int $goods_id
     * @return mixed
     * g0002f0014e0000
     */
    public function get_push_count($radius=0.8,$goods_id=144){
        //符合要求的车辆的信息
        $goods_info = $this->get_goods_info($goods_id);
        if($goods_info['goods_weight'] != "0.000"){
            $map_car['gps_lat&gps_lng&car_weight&is_smg&status'] = array(
                array("between",array($goods_info['gps_lat']-$radius,$goods_info['gps_lat']+$radius)),
                array("between",array($goods_info['gps_lng']-$radius,$goods_info['gps_lng']+$radius)),
                array("egt",$goods_info["goods_weight"]),'1','1','_multi'=>true
            );
        }else{
            $map_car['gps_lat&gps_lng&car_square&is_smg&status'] = array(
                array("between",array($goods_info['gps_lat']-$radius,$goods_info['gps_lat']+$radius)),
                array("between",array($goods_info['gps_lng']-$radius,$goods_info['gps_lng']+$radius)),
                array("egt",$goods_info["goods_weight"]),'1','1','_multi'=>true
            );
        }
        $map_car['id&is_smg'] = array(
            array('in',$this->appoint_map($goods_info['end_towns'])),
            '1','_multi'=>true
        );
        $map_car['_logic'] = 'or';
        $car_info=$this->car_table->where($map_car)->group("user_id")->field("user_id")->select();
        $content = "起始地货源的推送语句:".$this->car_table->getLastSql()." 时间：".date('Y-m-d H:i:s',time());
        write_log($content);
        $car_arr['info'] = $car_info;
        $car_arr['count'] = count($car_info);
        return $car_arr;
    }

    /**
     * 获得查询数量，和相应的查询数据
     * @param float $radius
     * @param int $goods_id
     * @return mixed
     * g0002f0014e0000
     */
    public function get_end_count($radius=0.15,$goods_id=144){
        //$num=0;
        //获得货物目的地的gps信息
        $goods_info = $this->goods_table->where(array("id"=>$goods_id))->field("goods_weight,end_towns")->find();
        $gps_info = $this->get_gps($goods_id);
        $map_car['gps_lat'] = array("between",array($gps_info['lat']-$radius,$gps_info['lat']+$radius));
        $map_car['gps_lng'] = array("between",array($gps_info['lng']-$radius,$gps_info['lng']+$radius));
        if($goods_info['goods_weight'] != '0.000'){
            $map_car["car_weight"] = array("egt",$goods_info["goods_weight"]);
        }else{
            $map_car["car_square"] = array("egt",$goods_info["goods_weight"]);
        }
        $map_car['status'] = 1;
        $map_car['is_smg'] = 1;
        $car_info=$this->car_table->where($map_car)->group("user_id")->field("user_id")->select();
        $content = "目的地货源的推送语句:".$this->car_table->getLastSql()." 时间：".date('Y-m-d H:i:s',time());
        write_log($content);
        $car_arr['info'] = $car_info;
        $car_arr['count'] = count($car_info);
        return $car_arr;
    }

    /**
     * 预约推送搜索条件
     */
    public function appoint_map($end_town){
        //当前有效的预约
        $appoint_map['end_time'] = array("egt",time());
        $appoint_map['begin_time'] = array('elt',time());
        $appoint_map['town_id'] = $end_town;
        //$appoint_map['appoint_state'] = 2;
        $appoint_map['status'] = 1;
        $info = $this->appointment_table->where($appoint_map)->field("car_id")->select();
        //要返回的搜索条件
        $str = "";
        if(empty($info)){
            return $str;
        }
        foreach($info as $v){
                $str .=",".$v['car_id'];
        }
        return trim(',',$str);
    }

    /**
     * 推送消息
     * @param $car_info  在线车辆信息
     * @param $goods_id  货物的id
     * @param int $operation 推送类型
     * @param int $push_state 推送类型 1 app 2 短信 (预留)
     * @return int
     * g0002f0015e0000
     */
    public function push_car_message($car_info,$goods_id,$operation=1,$push_state){
        $num = 0;
        foreach($car_info as $v){
            //短信推送消息
            $push_msg = "";
            //推送码
            $push_code = "";
            $goods_info = $this->goods_table->where("id={$goods_id}")->field("user_id,goods_class")->find();
            $hz_id = $goods_info['user_id'];
            $goods_class = $goods_info['goods_class'];
            //推送的信息
            switch($operation){
                case 1:
                    //发布货源
                    $goods_name = $this->goods_table->where("id={$goods_id}")->getField("goods_name");
                    $message = "({\"push_code\":100,\"goods_id\":$goods_id,\"goods_class\":$goods_class})";
                    $push_code = 100;
                    $push_msg = "您周围有新的货源发布，货物为：".$goods_name."，快去抢单吧!如暂时不需要平台向您推送货源短信，请在APP<个人中心>-><车辆管理>中设置";
                    break;
                case 2:
                    //修改货源的价格
                    $cz_id = $v['user_id'];
                    $message = "({\"push_code\":101,\"goods_id\":$goods_id,\"cz_id\":$cz_id,\"goods_class\":$goods_class)";
                    $push_code = 101;
                    $push_msg = "您周围有货物的运输价格被调高，快去查看吧!"."如暂时不需要平台向您推送货源短信，请在APP<个人中心>-><车辆管理>中设置";
                    break;
                case 3:
                    //自动更新
                    $goods_info = $this->get_goods_info($goods_id);
                    $message = "({\"push_code\":102,\"goods_id\":$goods_id,\"goods_class\":$goods_class})";
                    $push_msg = "您周围有新的货源发布，货物为：".$goods_info['goods_name']."，快去抢单吧!如暂时不需要平台向您推送货源短信，请在APP<个人中心>-><车辆管理>中设置";
                    break;
                case 5:
                default:
                    $message = "({\"push_code\":400,\"error\":\"错误\"})";
                    break;
            }
            //发送 APP 提醒
            $push_result = R("SemSend/Index/postSend",array( $v["user_id"],$message));
            //发送短信提醒
            if($operation != 4){
                //给车主推送
                $member_info = $this->member_table->where("id={$v['user_id']}")->field("mt")->find();
                $mt = $member_info['mt'];
                //$sms_result = R("SemSend/Index/send_sms3",array($mt,$push_msg));
                $sms_result = false;
            }else{
               continue;
            }
            $content = "num:".$num." push_code:".$push_code." goods_id".$goods_id." tel:".$mt." success".$sms_result." ".date("Y-m-d H:i:s",time());
            write_log($content);
            if(($push_result == true)&&($sms_result == false)){
                $data['state'] = 4;
                $num++;
            }elseif(($push_result == false)&&($sms_result == true)){
                $data['state'] = 3;
                $num++;
            }elseif(($push_result == true)&&($sms_result == true)){
                $data['state'] = 1;
                $num++;
            }else{
                $data['state'] = 0;
            }
            $data['operation'] = $operation;
            $data['cz_id'] = $v['user_id'];
            $data['goods_id'] = $goods_id;
            $data['push_time'] = time();
            $gipush_resule=$this->gipush_table->add($data);
            if(!$gipush_resule){
                $this->gipush_table->add($data);
            }
        }
        return $num;
    }

    /**
     * 协议提醒的方法
     * @param int $to_id 发送人得id
     * @param int $deal_code  协议的编号
     * @param int $operation  发送的类型  1：提醒车主签订协议 2：提醒货主车主拒签
     * 3：提醒货主车主已经确认协议 4.通知其它车主竞标失败 5.给货主提醒有车主抢单
     * g0002f0037e0000
     */
    public function push_deal_message($to_id=17588,$deal_id=490,$operation=1){
        $deal_info = $this->deal_table->where("id={$deal_id}")->field("id,hz_id,cz_id,cz_tel")->find();
        $push_msg = "";
        $send_msg = "";
        switch($operation){
            case 1:
                $cz_id = $deal_info['cz_id'];
                $hz_id = $deal_info['hz_id'];
                $send_msg = "({\"push_code\":110,\"deal_id\":$deal_id,\"cz_id\":$cz_id,\"hz_id\":$hz_id})";
                $push_msg = "您收到了货物运输协议，请在三十分钟之内完成签署。如未完成，将扣除您200元竞价保证金【物流邦】";
                $push_message = "货主发起协议，车主电话：".$deal_info['cz_tel'];
                $push_result = R("SemSend/Index/send_sms2",array('18322529639',$push_message));
                $content = "给宋经理发送提醒,是否成功:".$push_result." 时间:".date('Y-m-d',time());
                D("LogWrite")->write_log($content);
                break;
            case 2: break;
            default: break;
        }
        if(1){
            R("SemSend/Index/postSend",array( $to_id,$send_msg));
            //短信发送
            $member_info = $this->member_table->where("id={$to_id}")->field("mt")->find();
            $mt = $member_info['mt'];
            $send_result = R("SemSend/Index/send_sms3",array($mt,$push_msg));
            $data['state'] = 1;
            if($send_result){
                $data['sms_success'] = 1;
                $this->deal_table->where("id={$deal_id}")->save(array('is_sendmsg'=>1));
            }
        }else{
            $data['state'] = 0;
            $this->deal_table->where("id={$deal_id}")->save(array('is_sendmsg'=>2));
        }
        $data['from_id'] = $this->id;
        $data['to_id'] = $to_id;
        $data['send_time'] = time();
        $data['operation'] = $operation;
        $l=$this->dealrmd_table->add($data);
        if(!$l){
            $this->dealrmd_table->add($data);
        }
        if($data['state'] == 1){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 获得货物的坐标
     * @param $goods_id
     * @return bool
     * g0002f0008e0000
     */
    public function get_goods_info($goods_id){
        $goods_field = "goods_weight,gps_lat,gps_lng,goods_name,end_towns";
        $goods_info=$this->goods_table->where("id={$goods_id}")->field($goods_field)->find();
        if($goods_info){
            return $goods_info;
        }else{
            return false;
        }
    }

    /**
     * 获得相应货物的目的地信息
     */
    public function get_gps($goods_id){
        $adrs_id = $this->eadrs_table->where("status=1 and goods_id={$goods_id} and is_estation=1")->getField("adrs_id");
        $adrs_info = $this->adrs_table->where("id={$adrs_id}")->field("lat,lng")->find();
        return $adrs_info;
    }
}