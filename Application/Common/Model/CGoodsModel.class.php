<?php
namespace Common\Model;
use Common\Logic\CBaseLogic;

/**
 * @author baiwei
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/6
 * Time: 15:26
 */
class CGoodsModel extends CBaseModel{
    //声明变量
    protected $tableName;
    protected $uid;
    //初始化
    function _initialize(){
        $this->tableName = GOODS_TABLE;
        $this->uid = $_SESSION['userData']['id'];
    }

    /**
     * 结算完为发起竞价，修改已处理
     */
    public function upd_by_gid($goods_id){
        $data['is_dispose'] = 2;
        $result = $this->where("id={$goods_id}")->save($data);
        return $result;
    }

    /**
     * 通过货物id查找货源信息
     * @param $goods_id
     * @param string $field
     * @return mixed
     */
    public function fnd_by_gid($goods_id,$field=""){
        if($field == ""){
            $info = $this->where("id={$goods_id}")->find();
        }else{
            $info = $this->where("id={$goods_id}")->field($field)->find();
        }
        return $info;
    }

    /**
     * 发起竞价未发起协议的货源
     */
    public function fnd_by_order_make(){
        $map['bid_count'] = array("gt",0);
        $map['is_dispose'] = 1;
        $map['status'] = 1;
        $map['goods_state'] = array('in',"1,5");
        $map['load_time'] = array("egt",1449883631);
        $map['load_time'] = array("elt",time());
        $field = "id,user_id";
        $info = $this->where($map)->field($field)->select();
        return $info;
    }

    /**
     * 当天成交率的计算
     */
    public function fnd_by_deal_make($uid=""){
        $field = "id,user_id";
        $time = date("Y-m-d",strtotime("-1 day"));
        //$time = date("Y-m-d",time());
        $b_time = strtotime($time." 00:00:00");
        $e_time = strtotime($time." 23:59:59");
        $map['addtime'] = array("between",$b_time.",".$e_time);
        $map['status'] = array('in','1');
        if($uid != ""){
            $map['user_id'] = $uid;
            $info = $this->where($map)->field($field)->select();
        }else{
            $info = $this->where($map)->field($field)->group("user_id")->select();
        }
        return $info;
    }
}