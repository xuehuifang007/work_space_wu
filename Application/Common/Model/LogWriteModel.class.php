<?php
namespace Common\Model;
use Think\Model;
/**
 * 日志写入模型
 * Created by PhpStorm.
 * @author baiwei
 * User: mnmnwq
 * Date: 2015/6/5
 * Time: 14:48
 */
class LogWriteModel extends BasicModel{
    public function _initialize(){
        parent::_initialize();
    }
    /**
     * @param $error_content 错误日志内容
     * 错误日志公众调用方法(自带日志样式)
     */
    function write_log($error_content){
        $dir_result = true;
        $sign_str =  "\r\n-------------------------\r\n";
        $exec_time = "执行时间：".date("Y-m-d H:i:s",time())."\r\n";
        $error_content = $sign_str.$exec_time.$error_content.$sign_str;
        $today_logdir = date("Y-m-d",time());
        $absolute_logdir = "/home/wwwroot/city/xlog/".$today_logdir;
        if (!is_dir($absolute_logdir)){
            //第三个参数为TURE 即可以创建多极目录
            $dir_result = mkdir($absolute_logdir,"0755",true);
        }

        if($dir_result){
            $now_hours = date("H",time());
            $now_logfile = $absolute_logdir."/0".intval($now_hours/4).".log";
            //判断文件是否存在
            if(file_exists($now_logfile)){
                $handle_file = fopen($now_logfile,"a") or die("open file error");
            }else{
                $handle_file = fopen($now_logfile,"w") or die("open file error");
            }
            fwrite($handle_file,$error_content);
            fclose($handle_file);
        }

    }
}