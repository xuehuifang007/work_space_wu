<?php
namespace Common\Model;
use Think\Model;
/**
 * 手动预约起止时间计算
 * Created by PhpStorm.
 * @author baiwei
 * User: mnmnwq
 * Date: 2015/6/5
 * Time: 10:03
 */
class AppointTimeModel extends BasicModel{
    public function _initialize(){
        parent::_initialize();
    }
    /**
     * @author baiwei
     * 预约起止时间的计算
     */
    public function appoint_time($begin_time="",$end_time=""){
        if($begin_time == ""){
            $begin_time = time();
        }
        if($end_time == ""){
            $end_time = $begin_time+3600*12;
        }
        $time_arr = array();
        for($i=0;$i<12;$i++){
            $time_arr[$i] = array("val"=>date("Y-m-d H",($end_time-$i*3600)).":00:00","time"=>date("Y-m-d H",($end_time-$i*3600)).'时');
        }
        $re_time['end'] = array_reverse($time_arr);
        if(time() > strtotime(date("Y-m-d H",time()).":00:00")){
            $time_arr[] = array("val"=>date("Y-m-d H",time()).":00:00","time"=>date("Y-m-d H",time()).'时');
        }
        $time_arr = array_reverse($time_arr);
        //array_pop($time_arr);
        unset($time_arr[count($time_arr)-1]);
        $re_time['begin'] = $time_arr;
        return $re_time;
    }

    /**
     * @author baiwei
     * 验证当前的起止时间是否有效
     * @params $begin_time  时间戳
     * @params $end_time    时间戳
     */
    public function validate_appoint_time($begin_time,$end_time){
        if($begin_time >= $end_time){
            return false;
        }
        return true;
    }
}