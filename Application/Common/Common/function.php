<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15-1-14
 * Time: 下午3:20
 */

//调试写入text
function writeFile($content)
{
    if (is_array($content)) {
        $content = arrToStr($content);
    }

    $file = fopen("/home/wwwroot/city/test.txt", "a");
    $echo = fwrite($file, $content . time() . "\n");
    fclose($file);
}

//字符串to数组
function strToArr($info)
{
    if ($info == '')
        return array();
    $info = stripcslashes($info);
    eval("\$r = $info;");
    return $r;

}


//数组to字符串
function arrToStr($info)
{
    if ($info == '')
        return '';
    if (!is_array($info))
        $string = stripslashes($info);
    foreach ($info as $key => $val)
        $string[$key] = stripslashes($val);
    return addslashes(var_export($string, TRUE));

}

//get会员等级
function getMemberLevel($uid = null, $type = null)
{
    if ($uid && $type) {
        $data = M('cshy_user');
        $where['uid'] = $uid;
        $find = $data->where($where)->find();
        if ($type == '1') {
            $endRe = $find['cz_role_lv'];
            switch ($endRe) {
                case 0:
                    return '普通车主用户';
                    break;
                case 1:
                    return 'VIP车主';
                    break;
                case 2:
                    return '签约车主';
                    break;
            }
        } elseif ($type == '2') {
            $endRe = $find['hz_role_lv'];
            switch ($endRe) {
                case 1:
                    return '未签约货主';
                    break;
                case 2:
                    return '签约货主';
                    break;
            }
        }
    }
}

/** curl模拟GET请求
 * @param $url  请求地址及数据
 * @return mixed|string
 */
function my_file_get_contents($url){
    $ch = curl_init();
    $timeout = 30; // set to zero for no timeout
    $read_timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout + $read_timeout);
    $handles = curl_exec($ch);
    if(curl_errno($ch))
    {
        curl_close($ch);
        return "";
    }else{
        curl_close($ch);
        return $handles;
    }
}

/** curl模拟POST请求
 * @param $url  请求地址
 * @param $data 所传参数
 * @return mixed|string
 */
function my_file_post_contents($url,$data){
    $ch = curl_init();
    $timeout = 30;
    $read_timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout + $read_timeout);
    $handles = curl_exec($ch);
    if(curl_errno($ch))
    {
        curl_close($ch);
        return "" ;
    }else{
        curl_close($ch);
        return $handles;
    }
}

/** 生成维修订单方法
 * @param $head     头信息 (用于区分订单类型，可不写)
 * @return string
 */
function createOrder( $head ){
    $nowTime = microtime(true) * 10000 ;
    $nowTime = substr($nowTime,3);
    $order = empty($head) ? $nowTime.rand(10,99) : $head.$nowTime.rand(10,99);
    return $order;
}

/**
 * @param $error_content    错误日志内容
 * @param string $path      存储路径
 * 错误日志公众调用方法(自带日志样式)
 */
function write_log($error_content,$path='xlog'){
    $dir_result = true;
    $sign_str =  "\r\n-------------------------\r\n";
    $exec_time = "执行时间：".date("Y-m-d H:i:s",time())."\r\n";
    $error_content = $sign_str.$exec_time.$error_content.$sign_str;
    $today_logdir = date("Y-m-d",time());
    $absolute_logdir = "/home/wwwroot/city/".$path.'/'.$today_logdir;
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

/**
 * 字符串截取，支持中文和其他编码
 * static
 * access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * return string
 */
/*
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }   $s_length = strlen($str);
    $sli_length = strlen($slice);
    if($sli_length < $s_length){
        return $slice.'...';
    }else{
        return $slice;
    }
}*/

/** 字符串转换为数组
 * @param $str
 * @param string $code
 * @return array
 */
function str_arr($str,$code=','){
    $arr = explode($code,$str);
    return $arr;
}

/** 生成协议编号
 * @return string
 */
function creatDealCode(){
    return mt_rand(1000,9999).time();
}

/**
 * 获得协议的类型
 * 关于修改协议的详细注释 time 2015/7/13
 *  目前所有协议的顶层种类分为  普通  打包  组配
 * 协议种类编号
 * 1 普通协议：现结无回单 ；打包：打包拆分后子协议，现结无回单
 * 2 普通协议:现结有回单；打包：拆分后子协议，现结有回单
 * 3 普通协议：月结无回单；打包：拆分后子协议，月结无回单
 * 4 普通协议：月结有回单；打包：拆分后子协议，月结有回单
 * 5 普通协议：现场结算无回单;打包：拆分后子协议，现场结算无回单
 * 6 普通协议：现场结算有回单；打包：拆分后子协议，现场结算有回单
 * 7 打包总协议
 * 8 组配总协议和子协议
 * @author baiwei
 * @param $pay_type 支付类型
 * @param $is_receipt 是否回单
 * @param $goods_class 货物的种类
 * @param $is_splited  时候拆分
 * @return int 协议类型
 * g0002f0026e0000
 */
function get_deal_type($pay_type,$is_receipt,$goods_class="1",$is_splited="1"){
    $type = "";
    if($goods_class == "21"){
        //打包货源
        if($is_splited == "1"){
            $type = 7;
        }elseif($is_splited == "3"){
            $type =  pub_deal_type($pay_type,$is_receipt);
        }
    }
    if($goods_class == "11"){
        //组合货源
        $type = 8;
    }

    if($goods_class == "1"){
        $type = pub_deal_type($pay_type,$is_receipt);
    }
    return $type;
}

/**
 * 协议种类公共部分
 */
function pub_deal_type($pay_type,$is_receipt){
    if(($pay_type==1)&&($is_receipt==0)){
        return 1;
    }elseif(($pay_type==1)&&($is_receipt==1)){
        return 2;
    }elseif(($pay_type==2)&&($is_receipt==0)){
        return 3;
    }elseif(($pay_type==2)&&($is_receipt==1)){
        return 4;
    }elseif(($pay_type==3)&&($is_receipt==0)){
        return 5;
    }elseif(($pay_type==3)&&($is_receipt==1)){
        return 6;
    }else{
        return false;
    }
}

/**
 * @author baiwei
 * @param $pur_money 意向价格
 * @param $user_id   用户id
 * @return bool|int 返回状态
 * g0002f0045e0000
 */
function call_credit($pur_money,$user_id){
    if(!$user_id){
        $user_id = $_SESSION['userData']['id'];
    }
    $money = $pur_money;
    $user_info = M(USER_TABLE)->where("uid={$user_id} and hz_role_lv=2")->field("used_mny,credit_mny")->find();
    $used = $user_info['used_mny'] + $money;
    $credit = $user_info['credit_mny'] + OVERRUN;
    if($used < $user_info['credit_mny']){
        //不用提醒
        return 1;
    }elseif(($used >= $user_info['credit_mny']) && ($used<$credit)){
        //提醒，但是可以发货源
        return 2;
    }elseif($used >= $credit){
        //提醒，不可以发货源
        return 3;
    }
    return false;
}

/** 计算签订协议需扣除的运票数量
 * @param $goods_class  发货类型
 * @param $days_num     执行天数
 * @param $week_fqny    发货频率
 * @return float|int
 */
function calYunPiao($goods_class,$days_num,$week_fqny){
    if( $goods_class==GOODS_DB_TYPE ){
        $num = ceil ($days_num/$week_fqny);
    }else{
        $num = 1;
    }
    return $num;
}

/**
 * @author baiwei
 * @param string $info 提交方式
 * @param array $require 数据本身
 * return $re_arr 返回验证之后的数据
 */
function validate_post($info="",$require=array()){
    $re_arr = array();
    foreach($require as $k=>&$v){
        $re_arr[$k] = I($info.".".$k);;
    }
    return $re_arr;
}

/** 获取司机APP协议状态
 * @param $deal_state   协议状态
 * @param $is_transport 是否启运
 * @return int          (0：未开始；1：运输中；2：已完成；3：车主申请中)
 */
function get_driver_dealstate($deal_state,$is_transport){
    if( $deal_state==2 ){
        if($is_transport==1){
            $state = 1;
        }else{
            $state = 0;
        }
    }elseif( $deal_state==3 ){
        $state = 3;
    }elseif( $deal_state==4 || $deal_state==5 || $deal_state==10 ){ //交易已完成
        $state = 2;
    }elseif( $deal_state==6 || $deal_state==7 || $deal_state==8 || $deal_state==9 ){
        $state = 1;
    }else{
        $state = 0;
    }
    return $state;
}

/** 根据当前协议编号获取最初打包货物协议编号
 * @param $now_deal_code    当前打包货物协议编号
 * @param string $str       分隔符
 * @return string
 */
function get_db_yuan_deal_code($now_deal_code,$str='_'){
    $deal_code_arr = explode($str,$now_deal_code);
    $yuan_deal_code = $deal_code_arr[0].'_1';
    return $yuan_deal_code;
}

/** 获取当天最晚时间戳方法
 * @return int
 */
function get_day_last_time(){
    $now_day_time = date('Y-m-d',time()).' 23:59:59';
    $day_last_time = strtotime($now_day_time);
    return $day_last_time;
}

/** 获取要显示协议的最晚的签约时间戳
 * @return int
 */
function get_deal_last_time(){
    $deal_day_time = date('Y-m-d',strtotime("+1 day")).' 23:59:59';
    $deal_last_time = strtotime($deal_day_time);
    return $deal_last_time;
}

/** 二维数组转换为一维数组
 * @param $arr      要转换的数组
 * @param int $k    要转换的第几项
 * @return array
 */
function twoArrtoOneArr($arr,$k=0){
    $res_arr = array();
    foreach($arr as $arrk=>$arrv ){
        if(is_array($arrv)){
            $res_arr[] = $arrv[$k];
        }
    }
    return $res_arr;
}

/** 判断车型是否符合货源要求
 * @param $car_length       车辆长度
 * @param $ncar_length      货源所需车辆长度
 * @param $length_offset    长度判断单位(1 小于、2 等于、3 大于)
 * @return bool
 */
function check_ncar_length($car_length,$ncar_length,$length_offset){
    if($length_offset==1){
        if($ncar_length < $car_length){
            return true;
        }else{
            return false;
        }
    }elseif($length_offset==2){
        if($ncar_length == $car_length){
            return true;
        }else{
            return false;
        }
    }elseif($length_offset==3){
        if($ncar_length > $car_length){
            return true;
        }else{
            return false;
        }
    }
}

/**
 * @author duxiangyang 2015/09/29
 * 返回该时间所在星期的【周一】时间
 * timestamp 时间搓
 * return_timestamp 是否返回时间搓
 */
function this_monday($timestamp=0,$return_timestamp=true){
    if(!$timestamp) $timestamp = time();
    $monday_date = date('Y-m-d', $timestamp-86400*date('w',$timestamp)+(date('w',$timestamp)>0?86400:-518400));
    if($return_timestamp){
        $time_rlt = strtotime($monday_date);
    }else{
        $time_rlt = $monday_date;
    }
    return $time_rlt;

}

/**
 * @author duxiangyang 2015/09/29
 * 返回该时间所在星期的【周日】时间
 * timestamp 时间搓
 * return_timestamp 是否返回时间搓
 */
function this_sunday($timestamp=0,$return_timestamp=true){
    if(!$timestamp) $timestamp = time();
    $sunday = this_monday($timestamp) + 518400;
    if($return_timestamp){
        $time_rlt = $sunday;
    }else{
        $time_rlt = date('Y-m-d',$sunday);
    }
    return $time_rlt;
}

/** 根据经纬度获取百度地图API地址详细信息(不返回周边信息)
 * @author Feng
 * @param $lat  纬度
 * @param $lng  经度
 * @param string $ak    百度地图API的ak
 * @param string $output    返回格式类型(json、xml)
 * @param string $callback  json格式前缀，即jsonp格式
 * @return array    status(s请求成功；f请求失败)
 */
function get_adrs_by_coordinate($lat,$lng,$ak='Tn3pNaqSUtTr49oujSGfowDZ',$output='json',$callback=''){
    $location = $lat.','.$lng;
    $url = 'http://api.map.baidu.com/geocoder/v2/?ak='.$ak.'&callback='.$callback.'&location='.$location.'&output='.$output.'&pois=0';
    $baidu_map_json = my_file_get_contents($url);
    $baidu_map_res = json_decode($baidu_map_json,true);
    if ($baidu_map_res['status'] != 0)
    {
        return array('status'=>'f');
    }else{
        return array(
            'status'=>'s',
            'address' => $baidu_map_res['result']['formatted_address'],
            'province' => $baidu_map_res['result']['addressComponent']['province'],
            'city' => $baidu_map_res['result']['addressComponent']['city'],
            'area' => $baidu_map_res['result']['addressComponent']['district'],
            'street' => $baidu_map_res['result']['addressComponent']['street'],
            'street_number' => $baidu_map_res['result']['addressComponent']['street_number'],
            'city_code'=>$baidu_map_res['result']['cityCode'],
            'lng'=>$baidu_map_res['result']['location']['lng'],
            'lat'=>$baidu_map_res['result']['location']['lat']
        );
    }
}

/** 根据gpsid实时获取车辆坐标
 * @param $gpsid    车辆gpsID
 * @return mixed|string
 */
function getCarGps($gpsid){
    Vendor('getGps.getGps');
    $gpsObj = new \getGps();
    $re = $gpsObj->getGpsFun('',$gpsid);
    return $re;
}