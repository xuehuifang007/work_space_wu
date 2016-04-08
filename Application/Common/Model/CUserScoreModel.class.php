<?php
/** 用户积分相关模型
 * Created by PhpStorm.
 * User: Feng
 * Date: 15-12-6
 * Time: 上午11:30
 */

namespace Common\Model;
use Common\Model\CBaseModel;

class CUserScoreModel extends CBaseModel{
    protected $tableName;
    public function _initialize(){
        $this->tableName = C('user_score_table');
    }

    /** 添加用户积分数据信息
     * @author Feng
     * @param $data     要添加的数据
     * @return mixed
     */
    public function insert($data){
        $add_res = $this->add($data);
        return $add_res;
    }

    /** 根据用户id查询用户积分数据信息
     * @author Feng
     * @param $uid          用户id
     * @param string $field 要查询的字段
     * @return mixed
     */
    public function fnd_by_uid($uid,$field='*'){
        $where['uid'] = $uid;
        $res = $this->field($field)->where($where)->find();
        return $res;
    }

    /** 根据用户id修改用户积分数据信息
     * @author Feng
     * @param $uid  用户id
     * @param $data 要修改的数据
     * @return bool
     */
    public function upd_by_uid($uid,$data){
        $where['uid'] = $uid;
        $res = $this->where($where)->save($data);
        return $res;
    }

    /** 根据用户id修改用户积分数据信息
     * @author Feng
     * @param $uid      用户id
     * @param $key      要修改数据的字段名
     * @param $score    要增加的值
     * @return bool
     */
    public function inc_by_uid($uid,$key,$score){
        $where['uid'] = $uid;
        $res = $this->where($where)->setInc($key,$score);
        return $res;
    }

    /** 根据用户id删除用户积分数据
     * @author Feng
     * @param $uid  用户id
     * @return mixed
     */
    public function del_by_uid($uid){
        $where['uid'] = $uid;
        $res = $this->where($where)->delete();
        return $res;
    }
} 