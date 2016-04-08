<?php
/** 司机公共模型
 * Created by PhpStorm.
 * User: Feng
 * Date: 15-12-22
 * Time: 上午9:42
 */

namespace Common\Model;

class CDriverModel extends CBaseModel{
    protected $tableName;
    public function _initialize(){
        $this->tableName = C('driver_table');
    }

    /** 为车主添加司机
     * @author Feng
     * @param $data     要添加的数据
     * @return mixed
     */
    public function insert($data){
        $add_res = $this->add($data);
        return $add_res;
    }

    /** 根据司机id查询司机信息
     * @author Feng
     * @param $id           司机id
     * @param string $field 要查询的字段
     * @return mixed
     */
    public function fnd_by_id($id,$field='*'){
        $where['id'] = $id;
        $where['status'] = 1;
        $res = $this->field($field)->where($where)->find();
        return $res;
    }

    /** 根据司机手机号查询司机信息
     * @author Feng
     * @param $driver_tel   司机电话
     * @param string $field 要查询的字段
     * @return mixed
     */
    public function fnd_by_tel($driver_tel,$field='*'){
        $where['driver_tel'] = $driver_tel;
        $where['status'] = 1;
        $res = $this->field($field)->where($where)->find();
        return $res;
    }

    /** 根据车主id批量查询旗下司机数据
     * @author Feng
     * @param $uid          车主id
     * @param string $field 要查询的字段
     * @return mixed
     */
    public function slt_by_uid($uid,$field='*'){
        $where['cz_id'] = $uid;
        $where['status'] = 1;
        $res = $this->field($field)->where($where)->select();
        return $res;
    }

    /** 根据查询条件查询司机信息
     * @author Feng
     * @param $where    查询条件
     * @param string $field 要查询的字段
     * @return mixed
     */
    public function fnd_by_where($where,$field='*'){
        $fnd_where['status'] = 1;
        foreach($where as $wk=>$wv){
            $fnd_where[$wk] = $wv;
        }
        $res = $this->field($field)->where($fnd_where)->find();
        return $res;
    }

    /** 根据司机id修改司机相关数据
     * @author Feng
     * @param $id    司机id
     * @param $data  要修改的数据
     * @return bool
     */
    public function upd_by_id($id,$data){
        $where['id'] = $id;
        $res = $this->where($where)->save($data);
        return $res;
    }

    /** 根据司机id删除司机相关数据
     * @author Feng
     * @param $id   司机id
     * @return mixed
     */
    public function del_by_id($id){
        $where['id'] = $id;
        $res = $this->where($where)->delete();
        return $res;
    }
} 