<?php
/** 城市货运车型表
 * Created by PhpStorm.
 * User: Feng
 * Date: 15-12-7
 * Time: 下午1:25
 */

namespace Common\Model;
use Common\Model\CBaseModel;

class CCarModel extends CBaseModel{
    protected $tableName;
    public function _initialize(){
        $this->tableName = C('car_table');
    }

    /** 添加车辆相关数据
     * @author Feng
     * @param $data     要添加的数据
     * @return mixed
     */
    public function insert($data){
        $add_res = $this->add($data);
        return $add_res;
    }

    /** 根据车辆id查询车辆相关数据
     * @author Feng
     * @param $car_id           车辆id
     * @param string $field     要查询的字段
     * @param string $status    查询状态
     * @return mixed
     */
    public function fnd_by_car_id($car_id,$field='*',$status=''){
        $where['id'] = $car_id;
        if(!empty($status)){
            $where['status'] = $status;
        }
        $res = $this->field($field)->where($where)->find();
        return $res;
    }

    /** 根据车主id查询车辆相关数据
     * @author Feng
     * @param $uid              车主id
     * @param string $field     要查询的字段
     * @param string $status    查询状态
     * @return mixed
     */
    public function slt_by_uid($uid,$field='*',$status){
        $where['user_id'] = $uid;
        $where['car_type'] = array('neq',6);
        $where['status'] = $status;
        $res = $this->field($field)->where($where)->select();
        return $res;
    }

    /** 根据车辆id、车主id查询车辆相关数据
     * @author Feng
     * @param $car_id           车辆id
     * @param $uid              车主id
     * @param string $field     要查询的字段
     * @param string $status    查询状态
     * @return mixed
     */
    public function fnd_by_carid_uid($car_id,$uid,$field='*',$status=''){
        $where['id'] = $car_id;
        $where['user_id'] = $uid;
        if(!empty($status)){
            $where['status'] = $status;
        }
        $res = $this->field($field)->where($where)->find();
        return $res;
    }

    /** 根据司机id批量查询车辆相关数据
     * @author Feng
     * @param $driver_id        司机id
     * @param string $field     要查询的字段
     * @param string $status    查询状态
     * @return mixed
     */
    public function slt_by_driver_id($driver_id,$field='*',$status=''){
        $where['car_driver'] = $driver_id;
        $where['car_type'] = array('neq',6);
        if(!empty($status)){
            $where['status'] = $status;
        }
        $res = $this->field($field)->where($where)->select();
        return $res;
    }

    /** 根据车辆id修改车辆相关数据
     * @author Feng
     * @param $car_id   车辆id
     * @param $data     要修改的数据
     * @return bool
     */
    public function upd_by_car_id($car_id,$data){
        $where['id'] = $car_id;
        $res = $this->where($where)->save($data);
        return $res;
    }

    /** 根据车辆id删除车辆相关数据
     * @author Feng
     * @param $car_id   车辆id
     * @return mixed
     */
    public function del_by_car_id($car_id){
        $where['id'] = $car_id;
        $res = $this->where($where)->delete();
        return $res;
    }
} 