<?php
namespace Common\Model;
use Think\Model;
/**
 * @author baiwei
 * 获得表信息的模型
 * Created by PhpStorm.
 * User: mnmnwq
 * Date: 2015/6/8
 * Time: 10:28
 */
class GetTableInfoModel extends BasicModel{
    public function _initialize(){
        parent::_initialize();
    }

    /**
     * 获得表单条信息
     * @param $table_name
     * @param $where
     * @param string $field
     * @return mixed
     */
    public function get_table_info($table_name,$where,$field=""){
        if($field == ""){
            $table_info = M($table_name)->where($where)->find();
        }else{
            $table_info = M($table_name)->where($where)->field($field)->find();
        }
        return $table_info;
    }

    /**
     * 获得表多条信息
     * @param $table_name
     * @param $where
     * @param string $field
     * @return mixed
     */
    public function get_select_info($table_name,$where,$field=""){
        if($field == ""){
            $table_info = M($table_name)->where($where)->select();
        }else{
            $table_info = M($table_name)->where($where)->field($field)->select();
        }
        return $table_info;
    }
}