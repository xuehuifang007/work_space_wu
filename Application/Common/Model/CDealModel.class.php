<?php
namespace Common\Model;
/**
 * @author baiwei
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/7
 * Time: 9:00
 */
class CDealModel extends CBaseModel{
    //声明变量
    protected $tableName;
    protected $uid;    //用户的ID
    //初始化
    function _initialize(){
        $this->tableName = DEAL_TABLE;
        $this->uid = $_SESSION['userData']['id'];
    }

    /**
     * 当日成交量的根据货物的id查询相应的协议
     */
    public function fnd_by_gid($goods_id){
        $field = "id,maketime";
        $map['goods_id'] = $goods_id;
        $map['status'] = array('neq','9');
        $info = $this->where($map)->field($field)->find();
        return $info;
    }
}