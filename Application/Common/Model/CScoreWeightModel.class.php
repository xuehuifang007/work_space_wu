<?php
/** 积分权重Model
 * Created by PhpStorm.
 * User: Feng
 * Date: 15-12-7
 * Time: 下午2:32
 */

namespace Common\Model;
use Common\Model\CBaseModel;

class CScoreWeightModel extends CBaseModel{
    protected $tableName;
    public function _initialize(){
        $this->tableName = C('score_weight_table');
    }

    /** 添加积分权重数据信息
     * @author Feng
     * @param $data     要添加的数据
     * @return mixed
     */
    public function insert($data){
        $add_res = $this->add($data);
        return $add_res;
    }

    /** 根据积分权重id查询积分权重数据信息
     * @author Feng
     * @param $id           积分权重id
     * @param string $field 要查询的字段
     * @return mixed
     */
    public function fnd_by_id($id,$field='*'){
        $where['id'] = $id;
        $where['status'] = 1;
        $res = $this->field($field)->where($where)->find();
        return $res;
    }

    /** 根据积分权重id修改积分权重数据信息
     * @author Feng
     * @param $id   积分权重id
     * @param $data 要修改的数据
     * @return bool
     */
    public function upd_by_id($id,$data){
        $where['id'] = $id;
        $res = $this->where($where)->save($data);
        return $res;
    }

    /** 根据积分权重id删除积分权重数据
     * @author Feng
     * @param $id   积分权重idid
     * @return mixed
     */
    public function del_by_id($id){
        $where['id'] = $id;
        $res = $this->where($where)->delete();
        return $res;
    }
} 