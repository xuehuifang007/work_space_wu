<?php
/** 车队公共Model层
 * Created by PhpStorm.
 * User: Feng
 * Date: 15-12-10
 * Time: 上午8:41
 */

namespace Common\Model;

class CMotorcadeModel extends CBaseModel{
    protected $tableName;
    public function _initialize(){
        $this->tableName = C('motorcade_table');
    }

    /** 添加车队相关数据
     * @author Feng
     * @param $data     要添加的数据
     * @return mixed
     */
    public function insert($data){
        $add_res = $this->add($data);
        return $add_res;
    }

    /** 根据车队id查询车队相关数据
     * @author Feng
     * @param $id           车队id
     * @param string $field 要查询的字段
     * @param $status       要查询的车队状态(1启用；9废弃)
     * @return mixed
     */
    public function fnd_by_id($id,$field='*',$status){
        $where['id'] = $id;
        if($status){
            $where['status'] = $status;
        }
        $res = $this->field($field)->where($where)->find();
        return $res;
    }

    /** 根据用户id查询车队相关数据
     * @author Feng
     * @param $uid          用户id
     * @param string $field 要查询的字段
     * @param $status       要查询的车队状态(1启用；9废弃)
     * @return mixed
     */
    public function fnd_by_uid($uid,$field='*',$status){
        $where['user_id'] = $uid;
        $where['status'] = $status;
        $res = $this->field($field)->where($where)->find();
        return $res;
    }

    /** 根据车队id修改车队数据
     * @author Feng
     * @param $id       用户id
     * @param $data     要修改的数据
     * @return bool
     */
    public function upd_by_id($id,$data){
        $where['id'] = $id;
        $res = $this->where($where)->save($data);
        return $res;
    }

    /** 根据用户id修改车队数据
     * @author Feng
     * @param $uid      用户id
     * @param $data     要修改的数据
     * @return bool
     */
    public function upd_by_uid($uid,$data){
        $where['user_id'] = $uid;
        $res = $this->where($where)->save($data);
        return $res;
    }
} 