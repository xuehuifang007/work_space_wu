<?php
/** 竞价相关公共模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-7-27
 * Time: 上午11:22
 */
namespace Common\Model;
use Think\Model;

class BidGoodsModel extends BasicModel{
    public function _initialize(){
        parent::_initialize();
    }

    /** 查询user表用户信息
     * @param $userArr      查询条件
     * @param string $field 查询字段
     * @return mixed
     */
    public function findUserInfo($userArr,$field='*'){
        $userInfo = $this->user_table->field($field)->where($userArr)->find();
        return $userInfo;
    }

    /** 查询member表用户信息
     * @param $memberArr    查询条件
     * @param string $field 查询字段
     * @return mixed
     */
    public function findMemberInfo($memberArr,$field='*'){
        $memberInfo = $this->member_table->field($field)->where($memberArr)->find();
        return $memberInfo;
    }

    /** 查询货物信息
     * @param $goodsArr     查询条件
     * @param string $field 查询字段
     * @return mixed
     */
    public function findGoodsInfo($goodsArr,$field='*'){
        $goodsInfo = $this->goods_table->field($field)->where($goodsArr)->find();
        return $goodsInfo;
    }

    /** 查询车主是否被货主拉黑
     * @param $hz_id    货主id
     * @param $cz_id    车主id
     * @return string   y被拉黑；n未被拉黑
     */
    public function isDefriend($hz_id,$cz_id){
        $friend['defirend_id'] = $hz_id;
        $friend['defirended_id'] = $cz_id;
        $defriend_result = $this->defirend_table->field('id')->where($friend)->find();
        if($defriend_result['id']){
            return 'y';
        }else{
            return 'n';
        }
    }

    /** 查询车辆信息
     * @param $carArr       查询条件
     * @param string $field 查询字段
     * @return mixed
     */
    public function findCarInfo($carArr,$field='*'){
        $carInfo = $this->car_table->field($field)->where($carArr)->find();
        return $carInfo;
    }

    /** 查询打包货物信息
     * @param $dbgoodsArr   查询条件
     * @param string $field 查询字段
     * @return mixed
     */
    public function findDbgoodsInfo($dbgoodsArr,$field='*'){
        $dbgoodsArr['status'] = 1;
        $dbgoodsInfo = $this->dbgoods_table->field($field)->where($dbgoodsArr)->find();
        return $dbgoodsInfo;
    }

    /** 查询竞价信息
     * @param $orderArr     查询条件
     * @param string $field 查询字段
     * @return mixed
     */
    public function findOrderInfo($orderArr,$field='*'){
        $orderInfo = $this->order_table->field($field)->where($orderArr)->find();
        return $orderInfo;
    }

    /** 检测是否有竞价资格
     * @param $goods_id     货物id
     * @param $cz_id        车主id
     * @param $car_id       车辆id
     * @param $bid_money    竞价金额
     * @return int
     */
    public function check_qualifications($goods_id,$cz_id,$car_id,$bid_money){
        //查询member表信息
        $memberWhere['id'] = $cz_id;
        $memberInfo = $this->findMemberInfo($memberWhere,'user_type');
        if(!$memberInfo['user_type']){
            //用户不存在或查询失败
            $res_date['code'] = 101;
            return $res_date;
        }
        if($memberInfo['user_type']==2){
            //用户是货主
            $res_date['code'] = 102;
            return $res_date;
        }

        //查询车主信息
        $userWhere['uid'] = $cz_id;
        $userInfo = $this->findUserInfo($userWhere,'cz_role_lv,settle_type,clcngd_bidlimits,deposit_mny,ticket,gvticket');
        if(!$userInfo){
            //查询车主信息失败
            $res_date['code'] = 201;
            return $res_date;
        }
        if($userInfo['cz_role_lv']==0){
            //未签约用户无法抢单
            $res_date['code'] = 202;
            return $res_date;
        }
        if($userInfo['deposit_mny']<200){
            //保证金不足
            $res_date['code'] = 203;
            return $res_date;
        }

        //查询是否已竞价
        $orderWhere['goods_id'] = $goods_id;
        $orderWhere['cz_id'] = $cz_id;
        $orderInfo = $this->findOrderInfo($orderWhere,'id');
        if($orderInfo['id']){
            //竞价信息已存在
            $res_date['code'] = 301;
            return $res_date;
        }

        //查询货物信息
        $goodsWhere['id'] = $goods_id;
        $goodsWhere['status'] = 1;
        $goodsInfo = $this->findGoodsInfo($goodsWhere,'user_id,goods_name,goods_weight,goods_class,pur_money,ncar_type,ncar_length,length_offset,pay_type,is_clcnmny,goods_state,load_time,endtime');
        if(!$goodsInfo){
            //货物不存在
            $res_date['code'] = 401;
            return $res_date;
        }
        if($bid_money>=10){
            if($bid_money>$goodsInfo['pur_money']){
                //竞价金额大于货主的意向价格
                $res_date['code'] = 402;
                return $res_date;
            }
        }else{
            //竞价金额不能小于10元
            $res_date['code'] = 403;
            return $res_date;
        }
        if($goodsInfo['load_time']<time()){
            //装货时间已过
            $res_date['code'] = 404;
            return $res_date;
        }
        if($goodsInfo['pay_type']==2 && $userInfo['settle_type']==1){
            //现结车主不能抢月结货物
            $res_date['code'] = 405;
            return $res_date;
        }
        if($goodsInfo['is_clcnmny']==1 && $userInfo['clcngd_bidlimits']!=1){
            //没有代收款资格
            $res_date['code'] = 406;
            return $res_date;
        }

        //查询车主是否被拉黑
        $defriend = $this->isDefriend($goodsInfo['user_id'],$cz_id);
        if($defriend=='y'){
            //车主被拉黑
            $res_date['code'] = 501;
            return $res_date;
        }

        //查询车辆信息
        $carWhere['user_id'] = $cz_id;
        $carWhere['id'] = $car_id;
        $carWhere['status'] = 1;
        $carInfo = $this->findCarInfo($carWhere,'car_type,car_weight,car_length,car_driver');
        if(!$carInfo){
            //车辆不存在
            $res_date['code'] = 601;
            return $res_date;
        }
        if(empty($carInfo['car_driver'])){
            //未绑定司机
            $res_date['code'] = 602;
            return $res_date;
        }
//        if($goodsInfo['ncar_type']!=0){
//            if($carInfo['car_type'] != $goodsInfo['ncar_type']){
//                //车型不符
//                $res_date['code'] = 603;
//                return $res_date;
//            }
//        }
//        if($carInfo['car_weight'] < $goodsInfo['goods_weight']){
//            //车辆载重不足
//            $res_date['code'] = 604;
//            return $res_date;
//        }
//        if(!empty($goodsInfo['ncar_length'])){
//            $check_ncar_length = check_ncar_length($carInfo['car_length'],$goodsInfo['ncar_length'],$goodsInfo['length_offset']);
//            if(!$check_ncar_length){
//                //车长不符
//                $res_date['code'] = 605;
//                return $res_date;
//            }
//        }
        if($goodsInfo['goods_class']==GOODS_DB_TYPE){
            $dbgoodsWhere['gid'] = $goods_id;
            $dbgoodsInfo = $this->findDbgoodsInfo($dbgoodsWhere,'days_num,week_fqny');
            if(!$dbgoodsInfo){
                //查询打包货物频率、执行天数失败
                $res_date['code'] = 606;
                return $res_date;
            }
            $days_num = $dbgoodsInfo['days_num'];
            $week_fqny = $dbgoodsInfo['week_fqny'];
        }else{
            $days_num = '';
            $week_fqny = '';
        }
        $yunpiao_num = calYunPiao($goodsInfo['goods_class'],$days_num,$week_fqny);
        if($userInfo['ticket']<$yunpiao_num && $userInfo['gvticket']<$yunpiao_num){
            //邦票余额不足
            $res_date['code'] = 607;
            return $res_date;
        }

        $res_date['code'] = 100;
        $res_date['userInfo'] = $userInfo;
        $res_date['goodsInfo'] = $goodsInfo;
        $res_date['carInfo'] = $carInfo;
        return $res_date;
    }

}