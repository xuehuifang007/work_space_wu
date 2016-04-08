<?php
/** 司机上传图片模型
 * Created by PhpStorm.
 * User: Feng
 * Date: 15-12-17
 * Time: 上午9:06
 */

namespace Common\Model;

class CDriverUpPicModel extends CBaseModel{
    protected $tableName;
    public function _initialize(){
        $this->tableName = C('driver_up_pic_table');
    }

    /** 添加司机上传图片数据
     * @author Feng
     * @param $data     要添加的数据
     * @return mixed
     */
    public function insert($data){
        $add_res = $this->add($data);
        return $add_res;
    }

    /** 根据id查询司机上传图片数据
     * @author Feng
     * @param $id           协议id
     * @param string $field 要查询的字段
     * @return mixed
     */
    public function fnd_by_id($id,$field='*'){
        $where['id'] = $id;
        $where['status'] = 1;
        $res = $this->field($field)->where($where)->find();
        return $res;
    }

    /** 根据司机电话查询司机上传图片数据
     * @author Feng
     * @param $driver_tel   协议id
     * @param string $field 要查询的字段
     * @return mixed
     */
    public function fnd_by_driver_tel($driver_tel,$field='*'){
        $where['driver_tel'] = $driver_tel;
        $where['status'] = 1;
        $res = $this->field($field)->where($where)->find();
        return $res;
    }

    /** 根据司机电话批量查询司机上传图片数据
     * @author Feng
     * @param $driver_tel   协议id
     * @param string $field 要查询的字段
     * @param string $order 排序规则
     * @return mixed
     */
    public function slt_by_driver_tel($driver_tel,$field='*',$order){
        $where['driver_tel'] = $driver_tel;
        $where['status'] = 1;
        $res = $this->field($field)->where($where)->order($order)->select();
        return $res;
    }

    /** 根据协议id查询司机上传图片数据
     * @author Feng
     * @param $deal_id      协议id
     * @param string $field 要查询的字段
     * @return mixed
     */
    public function fnd_by_dealid($deal_id,$field='*'){
        $where['deal_id'] = $deal_id;
        $where['status'] = 1;
        $res = $this->field($field)->where($where)->find();
        return $res;
    }

    /** 根据协议id批量查询司机上传图片数据
     * @author Feng
     * @param $deal_id      协议id
     * @param string $field 要查询的字段
     * @param string $order 排序规则
     * @return mixed
     */
    public function slt_by_dealid($deal_id,$field='*',$order){
        $where['deal_id'] = $deal_id;
        $where['status'] = 1;
        $res = $this->field($field)->where($where)->order($order)->select();
        return $res;
    }

    /** 根据id修改司机上传图片数据
     * @author Feng
     * @param $id    id
     * @param $data  要修改的数据
     * @return bool
     */
    public function upd_by_id($id,$data){
        $where['id'] = $id;
        $res = $this->where($where)->save($data);
        return $res;
    }

    /** 根据id删除司机上传图片数据
     * @author Feng
     * @param $id   id
     * @return mixed
     */
    public function del_by_id($id){
        $where['id'] = $id;
        $res = $this->where($where)->delete();
        return $res;
    }
} 