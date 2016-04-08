<?php
/**
 * Created by duxiangyang
 * 2015/11/30 13:32
 * 对竞价车辆进行冒泡排序
 * @param $arr 竞价车辆数组
 * @param $begin_towns 待竞价货物的起运地址
 * @return mixed
 */
 function bubble_sort($array,$begin_town,$load_time){
     $load_time = $load_time - 3600;
     $count = count($array);
     //判断数组长度
     if($count <= 0){
         return false;
     }
     //对数组进行排序
     for($i=0;$i<$count;$i++){
         for($k=$count-1;$k>$i;$k--){
             //第一优先级 空闲状态
             if($array[$k]['taskundo_qty'] < $array[$k-1]['taskundo_qty']){
                 //按未完成车辆冒泡
                 $tmp = $array[$k];
                 $array[$k] = $array[$k-1];
                 $array[$k-1] = $tmp;
             }elseif($array[$k]['taskundo_qty'] == $array[$k-1]['taskundo_qty']){
                     //time_k 为负数时表示晚点的秒数 time_k1 为正数时表示提前的秒数 【对于装车前的 1 小时】
                     $time_k = $load_time - $array[$k]['endtime'];
                     $time_k1 = $load_time - $array[$k-1]['endtime'];
                 if(($time_k >= 0) && ($time_k < $time_k1)){
                     //第二优先级 时间,(车辆卸货时间早)
                     $tmp = $array[$k];
                     $array[$k] = $array[$k - 1];
                     $array[$k - 1] = $tmp;
                 }elseif(($time_k < 0) && ($time_k > $time_k1)){
                     //第二优先级 时间,(车辆卸货时间晚)
                     $tmp = $array[$k];
                     $array[$k] = $array[$k - 1];
                     $array[$k - 1] = $tmp;
                 }elseif(($time_k >= 0) && ($time_k1 < -1800)){
                     //第二优先级 时间,(车辆不能按时去装货)
                     $tmp = $array[$k];
                     $array[$k] = $array[$k - 1];
                     $array[$k - 1] = $tmp;
                 }elseif(($time_k < 0 && $time_k >= -1800) && ($time_k1 > 3600)){
                     //第二优先级 时间,(耽误车辆运输效率)
                     $tmp = $array[$k];
                     $array[$k] = $array[$k - 1];
                     $array[$k - 1] = $tmp;
                 }elseif($time_k < -1800 && $time_k1 >= 3600 ){
                     //第三优先级 距离,(两辆车时间都不合适)
                     $len_k = abs($array[$k]['end_towns'] - $begin_town);
                     $len_k1 = abs($array[$k-1]['end_towns'] - $begin_town);
                     if($len_k < $len_k1){
                         $tmp = $array[$k];
                         $array[$k] = $array[$k - 1];
                         $array[$k - 1] = $tmp;
                     }
                 }
             }
         }
     }
     return $array;
 }