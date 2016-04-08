<?php
namespace Common\Model;
use Think\Model;
/**
 * Created by PhpStorm.
 * @author baiwei
 * User: mnmnwq
 * Date: 2015/9/23
 * Time: 14:42
*/
class GoodsPubModel extends BasicModel{
    /**
     * 撤销货源model
     * @param $goods_id 要撤销的货源的id
     */
    public function del_goods($goods_id,$user_id=0){
        if($user_id == 0){
            $user_id = $_SESSION['userData']['id'];
        }
        $goods_field = "id,goods_state,goods_class,user_id,bid_count";
        $goods_info = $this->goods_table->where("id={$goods_id} and user_id={$user_id}")->field($goods_field)->find();
        if(!$goods_info){
            return array("result"=>false,'code'=>'3001');
        }
        if($goods_info['goods_state'] == 4){
            return array("result"=>false,'code'=>'3003');
        }
        if($goods_info['goods_state'] > 1){
            return array("result"=>false,'code'=>'3002');
        }
        M()->startTrans();
        $result = $this->del_normal_goods($goods_id);
        if($result == false){
            M()->rollback();
            return array('result'=>false,'code'=>"3004");
        }
        if($goods_info['goods_class'] == '11'){
            $zp_result = $this->del_zp_goods($goods_id,$user_id);
            if(!$zp_result){
                M()->rollback();
                return array('result'=>false,"code"=>"3005");
            }
        }
        if($goods_info['goods_class'] != '11'){
            //删除相应的收货地址
            $eadrs_result = $this->del_eadrs($goods_id);
            if($eadrs_result == false){
                M()->rollback();
                return array('result'=>false,"code"=>"3006");
            }
        }

        //干掉相应的订单
        $order_result = $this->del_order($goods_info);
        if($order_result == false){
            M()->rollback();
            return array("result"=>false,'code'=>"3007");
        }
        M()->commit();
        return array('result'=>true,"code"=>"3000");
    }

    /**
     *删除正常的货源
     */
    public function del_normal_goods($gid){
        $data['goods_state'] = 4;
        $del_result = $this->goods_table->where("id={$gid}")->save($data);
        if($del_result) {
            return true;
        }else{
            return false;
        }
    }

    /**
     * 删除组配的货源
     */
    public function del_zp_goods($gid,$user_id){
        $mxgoods_info = $this->mxgoods_table->where("gid={$gid} and status=1")->find();
        $mx_id = $mxgoods_info['id'];
        unset($mxgoods_info['id']);
        unset($mxgoods_info['transactor_id']);
        unset($mxgoods_info['state']);
        unset($mxgoods_info['user_id']);
        unset($mxgoods_info['status']);
        $mxgoods_info['user_id'] = $user_id;
        $mxgoods_info['gid'] = 0;
        $tmp_result = $this->zptmpgoods_table->add($mxgoods_info);
        if ($tmp_result) {
            $data['is_mix'] = 1;
            $data['mxgoods_id'] = 0;
            $data['mxtmgoods_id'] = $tmp_result;
            $zp_result = $this->zpgoods_table->where("mxgoods_id={$mx_id} and status=1")->save($data);
            if ($zp_result) {
                return true;
            }else{
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 删除收货地址
     * @param $goods_id
     * @return bool
     * g0002f0025e0000
     */
    public function del_eadrs($goods_id){
        $data['status'] = 9;
        $result = $this->eadrs_table->where("goods_id={$goods_id}")->save($data);
        if(!$result){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 干掉货物的订单
     * @param $goods_id  订单关注的货物的id
     * g0002f0124e0000
     */
    public function del_order($goods_info){
        if($goods_info['bid_count'] == 0){
            return true;
        }else{
            //当前货物有订单
            $data['is_success'] = 2;
            $data['status'] = 9;
            $map['goods_id'] = $goods_info['id'];
            $map['status'] = 1;
            $result = $this->order_table->where($map)->save($data);
        }
        if(!$result){
            return false;
        }else{
            return true;
        }
    }

    /** 更改组合货源意向价格方法
     * @author  fengqingyu
     * @param $hz_id            货主id
     * @param $goods_id         goods表组合总货源id
     * @param $zpgoods_str      组合子货源id字符串，以”_”分割,例：1245_1246_1247
     * @param $tspo_fare_str    组合子货源价格字符串(需与子货源id顺序对应)，以”_”分割,例：45_20_12
     * @param string $code      分隔符
     * @return array
     */
    public function update_zpgoods_fare($hz_id,$goods_id,$zpgoods_str,$tspo_fare_str,$code='_'){
        //查询货源表货物信息
        $goods_where['id'] = $goods_id;
        $goods_where['user_id'] = $hz_id;
        $goods_where['goods_state'] = array('elt',1);
        $goods_where['status'] = array('in','1,2');
        $goods_info = $this->find_goods_info($goods_where,'id,goods_class,pur_num');
        if(!$goods_info){   //goods表货源不存在或该货物状态不允许更改
            return array('result'=>false,"code"=>"3021");
        }
        if($goods_info['goods_class']!=GOODS_ZH_TYPE){ //该货物不是组合货源
            return array('result'=>false,"code"=>"3028");
        }
        if($goods_info['pur_num']>0){   //已更改过意向价格
            return array('result'=>false,"code"=>"3029");
        }
        //查询组合货源表组合货源信息
        $zpmxgoods_where['gid'] = $goods_id;
        $zpmxgoods_where['status'] = 1;
        $mxgoods_info = $this->find_zpmxgoods_info($zpmxgoods_where,'id');
        if(!$mxgoods_info){ //zpmxgoods表货源不存在
            return array('result'=>false,"code"=>"3022");
        }
        $zpgoods_arr = explode($code, trim($zpgoods_str) );
        $tspo_fare_arr = explode($code, trim($tspo_fare_str) );

        M()->startTrans();
        //初始化要更改的总货源价格
        $goods_fare = 0;
        foreach($zpgoods_arr as $zk=>$zv){
            $zpgoods_where[$zk]['id'] = $zv;
            $zpgoods_where[$zk]['mxgoods_id'] = $mxgoods_info['id'];
            $zpgoods_info[$zk] = $this->find_zpgoods_info($zpgoods_where[$zk],'tspo_fare');
            if(!$zpgoods_info[$zk]){    //子货源不存在
                return array('result'=>false,"code"=>"3023");
            }
            if( $tspo_fare_arr[$zk] < $zpgoods_info[$zk]['tspo_fare'] ){    //要更改的价格不能低于前次价格
                M()->rollback();
                return array('result'=>false,"code"=>"3024");
            }
            if( $tspo_fare_arr[$zk] > $zpgoods_info[$zk]['tspo_fare'] ){    //要更改的价格不能低于前次价格
                $zpgoods_data[$zk]['tspo_fare'] = $tspo_fare_arr[$zk];
                $update_zpgoods_res = $this->update_zpgoods($zpgoods_where[$zk],$zpgoods_data[$zk]);
                if(!$update_zpgoods_res){   //更改组合子货源价格失败
                    M()->rollback();
                    return array('result'=>false,"code"=>"3025");
                }
            }
            $goods_fare = $goods_fare + $tspo_fare_arr[$zk];
        }
        //更改zpmxgoods表数据
        $zpmxgoods_where2['id'] = $mxgoods_info['id'];
        $zpmxgoods_data['pur_money'] = $goods_fare;
        $update_zpmxgoods_res = $this->update_zpmxgoods($zpmxgoods_where2,$zpmxgoods_data);
        if(!$update_zpmxgoods_res){ //更改zpmxgoods表货源信息失败
            M()->rollback();
            return array('result'=>false,"code"=>"3026");
        }
        //更改goods表数据
        $goods_where2['id'] = $goods_id;
        $goods_data['pur_money'] = $goods_fare;
        $goods_data['pur_num'] = 1;
        $update_goods_res = $this->update_goods($goods_where2,$goods_data);
        if(!$update_goods_res){ //更改goods表货源信息失败
            M()->rollback();
            return array('result'=>false,"code"=>"3027");
        }
        M()->commit();
        return array('result'=>true,"code"=>"3020");
    }

    /** 查询goods表货源信息
     * @author  fengqingyu
     * @param $where_arr    查询条件
     * @param string $field 查询字段
     */
    function find_goods_info($where_arr,$field='*'){
        $res_data = $this->goods_table->field($field)->where($where_arr)->find();
        return $res_data;
    }

    /** 查询zpmxgoods表组合货源信息
     * @author  fengqingyu
     * @param $where_arr    查询条件
     * @param string $field 查询字段
     * @return mixed
     */
    function find_zpmxgoods_info($where_arr,$field='*'){
        $res_data = $this->mxgoods_table->field($field)->where($where_arr)->find();
        return $res_data;
    }

    /** 查询zpgoods表组合子货源信息
     * @author  fengqingyu
     * @param $where_arr    查询条件
     * @param string $field 查询字段
     * @return mixed
     */
    function find_zpgoods_info($where_arr,$field='*'){
        $res_data = $this->zpgoods_table->field($field)->where($where_arr)->find();
        return $res_data;
    }

    /** 更改组合子货源数据
     * @author  fengqingyu
     * @param $where_arr    更改条件
     * @param $data         要更改的数据
     * @return mixed
     */
    function update_zpgoods($where_arr,$data){
        $res_data = $this->zpgoods_table->where($where_arr)->save($data);
        return $res_data;
    }

    /** 更改zpmxgoods表组合总货源数据
     * @author  fengqingyu
     * @param $where_arr    更改条件
     * @param $data         要更改的数据
     * @return mixed
     */
    function update_zpmxgoods($where_arr,$data){
        $res_data = $this->mxgoods_table->where($where_arr)->save($data);
        return $res_data;
    }

    /** 更改goods表货源数据
     * @author  fengqingyu
     * @param $where_arr    更改条件
     * @param $data         要更改的数据
     * @return mixed
     */
    function update_goods($where_arr,$data){
        $res_data = $this->goods_table->where($where_arr)->save($data);
        return $res_data;
    }
}