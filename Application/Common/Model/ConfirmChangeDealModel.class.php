<?php
namespace Common\Model;
use Think\Model;
/**
 * Created by PhpStorm.
 * 修改协议操作模型
 * @ author baiwei
 * User: mnmnwq
 * Date: 2015/7/9
 * Time: 13:25
 */
class ConfirmChangeDealModel extends BasicModel{
    public function _initialize(){
        parent::_initialize();
    }

    /**
     * 车主确认协议
     */
    public function confirm_change($deal_id = '19873'){
        //查到当前要修改的协议
        $deal_info = $this->deal_table->where("id={$deal_id}")->find();
        //找到货物信息
        $goods_info = $this->goods_table->where("id={$deal_info['goods_id']}")->find();
        //找到收货地址信息
        $eadrs_info = $this->eadrs_table->where("goods_id={$deal_info['goods_id']} and status=1")->select();
        //修改协议的信息
        $change_deal_info = $this->change_deal_table->where("deal_id={$deal_info['id']}")->find();
        //修改地址的信息
        $change_eadrs_info = $this->change_eadrs_table->where("changedeal_id={$change_deal_info['id']} and status=1")->select();
        M()->startTrans();
        //货物表加入历史表
        $goods_info['goods_id'] = $goods_info['id'];
        unset($goods_info['id']);
        $goods_add_result = $this->history_goods_table->add($goods_info);
        //把收货地址加入历史表(删除)
        $deadrs = true;
        $headrs = true;
        if(count($change_eadrs_info) != 0){
            foreach($eadrs_info as $v){
                $deadrs_result = $this->eadrs_table->where("id={$v['id']}")->setField('status',9);
                if(!$deadrs_result){
                    $deadrs = false;
                }
                $v['eadrs_id'] = $v['id'];
                unset($v['id']);
                unset($v['is_complete']);
                unset($v['completetime']);
                unset($v['comcode']);
                $headrs_result = $this->history_eadrs_table->add($v);
                if(!$headrs_result){
                    $headrs = false;
                }
            }
        }
        //把协议加入历史表（车主同意）
        $deal_info['change_state'] = 3;
        unset($deal_info['id']);
        $hdeal_result = $this->history_deal_table->add($deal_info);
        $ueadrs = true;
        //修改收货地址的信息
        if(count($change_eadrs_info) != 0){
            foreach($change_eadrs_info as $v){
                unset($v['id']);
                unset($v['changedeal_id']);
                $v['goods_id'] = $deal_info['goods_id'];
                $ueadrs_result = $this->eadrs_table->add($v);
                if(!$ueadrs_result){
                    $ueadrs = false;
                }
            }
        }
        //修改协议的信息
        $change_deal_data['change_remark'] = $change_deal_info['remark'];
        $change_deal_data['goods_name'] = $change_deal_info['goods_name'];
        $change_deal_data['goods_weight'] = $change_deal_info['goods_weight'];
        $change_deal_data['order_mny'] = $deal_info['order_mny'] + $change_deal_info['extra_money'];
        if(($deal_info['pay_type'] == 2) ||(($deal_info['pay_type'] ==1 )&&($deal_info['xj_type']!=5))){
            $change_deal_data['goods_fare'] = $change_deal_data['order_mny'];
        }
        $change_deal_data['change_state'] = 3;
        $udeal_result = $this->deal_table->where("id={$deal_id}")->save($change_deal_data);
        //修改货物的信息
        if($goods_info['goods_name'] != $change_deal_info['goods_name']){
            $change_goods_data['goods_name'] = $change_deal_info['goods_name'];
        }
        if($goods_info['goods_weight'] != $change_deal_info['goods_weight']){
            $change_goods_data['goods_weight'] = $change_deal_info['goods_weight'];
        }
        if($goods_info['endtime'] != ($goods_info['endtime'] + (3600*$change_deal_info['poor_time']))){
            $change_goods_data['endtime'] = $goods_info['endtime'] + (3600*$change_deal_info['poor_time']);
        }
        if($goods_info['unload_need'] != $change_deal_info['unload_need']){
            $change_goods_data['unload_need'] = $change_deal_info['unload_need'];
        }
        if($change_deal_info['adrs_id'] != "0"){
            $adrs_field = "adrs_city,adrs_area,adrs_towns,adrs_detail";
            $adrs_info = $this->adrs_table->where("id={$change_deal_info['adrs_id']}")->field($adrs_field)->find();
            $change_goods_data['begin_adrsid'] = $change_deal_info['adrs_id'];
            $change_goods_data['begin_city'] = $adrs_info['adrs_city'];
            $change_goods_data['begin_area'] = $adrs_info['adrs_adrs'];
            $change_goods_data['begin_towns'] = $adrs_info['adrs_towns'];
            $change_goods_data['begin_sadrs'] = $adrs_info['adrs_detail'];
        }
        if(isset($change_goods_data) && !empty($change_goods_data)){
            $ugoods_result = $this->goods_table->where("id={$deal_info['goods_id']}")->save($change_goods_data);
        }else{
            $ugoods_result = true;
        }
        //修改临时表的信息
        $change_data['change_state'] = 2;
        $change_data['require_time'] = time();
        $change_result = $this->change_deal_table->where("deal_id={$deal_id} and status=1")->save($change_data);
        if($goods_add_result && $deadrs && $headrs && $hdeal_result && $ueadrs && $udeal_result && $ugoods_result && $change_result){
            M()->commit();
            $tel_num = "15022673955";
            //要发送的短信内容
            $sms_content = "车主已同意补充协议";
            //发送短信调用方法
            $send_rlt =  R("SemSend/Index/send_sms2",array($tel_num, $sms_content ));
            return true;
        }else{
            M()->rollback();
            return false;
        }
    }

    /**
     * 车主拒绝修改协议
     */
    public function refuse_change($deal_id){
        M()->startTrans();
        $deal_result = $this->deal_table->where("id={$deal_id}")->setField('change_state',4);
        $change_result = $this->change_deal_table->where("deal_id={$deal_id} and status=1")->setField('change_state',3);
        if($deal_result && $change_result){
            M()->commit();
            $tel_num = "15022673955";
            //要发送的短信内容
            $sms_content = "车主已拒绝补充协议";
            //发送短信调用方法
            $send_rlt =  R("SemSend/Index/send_sms2",array($tel_num, $sms_content ));
            return true;
        }else{
            M()->rollback();
            return false;
        }
    }
}