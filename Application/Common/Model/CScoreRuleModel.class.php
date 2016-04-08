<?php
/** 积分规则模型
 * Created by PhpStorm.
 * User: Feng
 * Date: 15-12-1
 * Time: 下午3:23
 */

namespace Common\Model;
use Common\Model\CBaseModel;

class CScoreRuleModel extends CBaseModel{
    protected $tableName;
    public function _initialize(){
        $this->tableName = C('score_rule_table');
    }
    // 定义表结构
    public $fields = array(
        'id',
        'event_code',
        'pid',
        'exchange_score',
        'growth_score',
        'credit_score',
        'explain',
        'is_used',
        's_time',
        'e_time',
        '_pk' => 'id',
        'type' => array(
            'id' => 'int',
            'event_code' => 'varchar',
            'pid' => 'int',
            'exchange_score' => 'int',
            'growth_score' => 'int',
            'credit_score' => 'int',
            'explain' => 'varchar',
            'is_used' => 'tinyint',
            's_time' => 'int',
            'e_time' => 'int',
        )
    );

    /** 添加积分规则
     * @author Feng
     * @param $data     要添加的数据
     * @return mixed
     */
    public function insert($data){
        $add_res = $this->add($data);
        return $add_res;
    }

    /** 根据积分编码查询对应积分
     * @author Feng
     * @param $event_code   积分编码
     * @param string $field 要查询的字段
     * @return mixed
     */
    public function fnd_by_code($event_code,$field='*'){
        $where['is_used'] = 1;
        $where['s_time'] = array('elt',time());
        $where['e_time'] = array('egt',time());
        $where['event_code'] = $event_code;
        $res = $this->field($field)->where($where)->find();
        return $res;
    }

    /** 根据积分编码修改积分数据
     * @author Feng
     * @param $event_code   类别id
     * @param $data         要修改的数据
     * @return bool
     */
    public function upd_by_code($event_code,$data){
        $where['event_code'] = $event_code;
        $res = $this->where($where)->save($data);
        return $res;
    }

    /** 根据积分编码删除积分数据
     * @author Feng
     * @param $event_code   类别id
     * @return mixed
     */
    public function del_by_code($event_code){
        $where['event_code'] = $event_code;
        $res = $this->where($where)->delete();
        return $res;
    }
} 