<?php
/** 积分记录Model
 * Created by PhpStorm.
 * User: Feng
 * Date: 15-12-8
 * Time: 下午7:33
 */

namespace Common\Model;

class CScoreLogModel extends CBaseModel{
    protected $tableName;
    public function _initialize(){
        $this->tableName = C('score_log_table');
    }

    /** 添加积分操作日志
     * @author Feng
     * @param $data     要添加的数据
     * @return mixed
     */
    public function insert($data){
        $add_res = $this->add($data);
        return $add_res;
    }

    /** 根据记录id查询对应日志详情
     * @author Feng
     * @param $id           记录id
     * @param string $field 要查询的字段
     * @return mixed
     */
    public function fnd_by_id($id,$field='*'){
        $where['id'] = $id;
        $res = $this->field($field)->where($where)->find();
        return $res;
    }

    /** 根据日志id修改日志
     * @author Feng
     * @param $id   记录id
     * @param $data 要修改的数据
     * @return bool
     */
    public function upd_by_id($id,$data){
        $where['id'] = $id;
        $res = $this->where($where)->save($data);
        return $res;
    }

    /** 根据日志id删除日志
     * @author Feng
     * @param $id   记录id
     * @return mixed
     */
    public function del_by_id($id){
        $where['id'] = $id;
        $res = $this->where($where)->delete();
        return $res;
    }
} 