<?php
namespace Common\Model;
use Think\Model;
/**
 * @author baiwei
 * 设置表内容 相当于update
 * Created by PhpStorm.
 * User: mnmnwq
 * Date: 2015/6/8
 * Time: 17:03
 */
class SetTableInfoModel extends BasicModel{
    public function _initialize(){
        parent::_initialize();
    }

    /**
     * 修改对应表的信息
     */
    public function update_table_info($table_name,$where,$data){
        $result = M($table_name)->where($where)->save($data);
        if($result){
            return true;
        }else{
            return false;
        }
    }
}