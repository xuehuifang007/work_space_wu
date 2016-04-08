<?php
/** 车辆积分模型
 * Created by PhpStorm.
 * User: Feng
 * Date: 15-12-4
 * Time: 下午5:59
 */

namespace Common\Model;
use Common\Model\CBaseModel;

class CCarScoreModel extends CBaseModel{
    protected $tableName;
    public function _initialize(){
        $this->tableName = C('car_score_table');
    }

    /** 添加车辆积分相关数据
     * @author Feng
     * @param $data     要添加的数据
     * @return mixed
     */
    public function insert($data){
        $add_res = $this->add($data);
        return $add_res;
    }

    /** 根据车辆id查询车辆积分相关数据
     * @author Feng
     * @param $car_id       积分编码
     * @param string $field 要查询的字段
     * @return mixed
     */
    public function fnd_by_car_id($car_id,$field='*'){
        $where['car_id'] = $car_id;
        $res = $this->field($field)->where($where)->find();
        return $res;
    }

    /** 根据车辆id数组批量查询车辆积分相关数据
     * @author Feng
     * @param $car_id_arr   车辆id数组
     * @param string $field 要查询的字段
     * @param $isnew        是否是新车
     * @return mixed
     */
    public function slt_by_car_id_arr($car_id_arr,$field='*',$isnew){
        $where['car_id'] = array('in',$car_id_arr);
        if($isnew){
            $where['isnew'] = $isnew;
        }
        $res = $this->field($field)->where($where)->select();
        return $res;
    }

    /** 根据车辆id查询车辆积分相关数据
     * @author Feng
     * @param $uid          用户id
     * @param string $field 要查询的字段
     * @param $isnew        是否是新车
     * @return mixed
     */
    public function slt_by_uid($uid,$field='*',$isnew){
        $where['user_id'] = $uid;
        if($isnew){
            $where['isnew'] = $isnew;
        }
        $res = $this->field($field)->where($where)->select();
        return $res;
    }

    /** 根据车辆id修改车辆积分相关数据
     * @author Feng
     * @param $car_id   车辆id
     * @param $data     要修改的数据
     * @return bool
     */
    public function upd_by_car_id($car_id,$data){
        $where['car_id'] = $car_id;
        $res = $this->where($where)->save($data);
        return $res;
    }

    /** 根据用户id修改车辆积分相关数据
     * @author Feng
     * @param $uid   用户id
     * @param $data  要修改的数据
     * @return bool
     */
    public function upd_by_uid($uid,$data){
        $where['user_id'] = $uid;
        $res = $this->where($where)->save($data);
        return $res;
    }

    /** 根据用户id计算旗下所有车辆某项总值
     * @author Feng
     * @param $uid   用户id
     * @param $field   用户id
     * @return bool
     */
    public function sum_by_uid($uid,$field){
        $where['user_id'] = $uid;
        $res = $this->where($where)->sum($field);
        return $res;
    }

    /** 根据车辆id数组批量查询车辆积分某项总值
     * @author Feng
     * @param $car_id_arr   车辆id数组
     * @param string $field 要查询的字段
     * @param $isnew        是否是新车
     * @return mixed
     */
    public function sum_by_car_id_arr($car_id_arr,$field,$isnew){
        $where['car_id'] = array('in',$car_id_arr);
        if($isnew){
            $where['isnew'] = $isnew;
        }
        $res = $this->where($where)->sum($field);
        return $res;
    }

    /** 根据车辆id增加车辆积分相关数值
     * @author Feng
     * @param $car_id   车辆id
     * @param $field    要修改数据的字段名
     * @param $score    要增加的值
     * @return bool
     */
    public function inc_by_car_id($car_id,$field,$score){
        $where['car_id'] = $car_id;
        $res = $this->where($where)->setInc($field,$score);
        return $res;
    }

    /** 根据车辆id修减少辆积分相关数值
     * @author Feng
     * @param $car_id   车辆id
     * @param $field    要修改数据的字段名
     * @param $score    要增加的值
     * @return bool
     */
    public function dec_by_car_id($car_id,$field,$score){
        $where['car_id'] = $car_id;
        $res = $this->where($where)->setDec($field,$score);
        return $res;
    }

    /** 根据车辆id删除车辆积分相关数据
     * @author Feng
     * @param $car_id   车辆id
     * @return mixed
     */
    public function del_by_car_id($car_id){
        $where['car_id'] = $car_id;
        $res = $this->where($where)->delete();
        return $res;
    }
} 