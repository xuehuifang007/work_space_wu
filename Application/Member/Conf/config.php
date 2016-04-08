<?php
/**
 * @auther baiwei
 * 上传目录的全局变量
 */
//city上传路径
define("UPLOAD_PATH","./Public/Uploads/");
//头像上传路径
define('HEADER_UPLOADS','../api/publicIndex/upData/images/');
//维修联盟域名
define('WEIXIU_DOMAIN','http://www.xiuli.5656111.com');
return  array(
    /* 项目设定 */

    /* 修理联盟相关 */
    //对外api接口密钥
    'fix_key' => '0oZMcZtCJxUU1CYjLEuU',
    //打包周期
    'db_circle' => array(
        1 => "一天一次",
        2 => "两天一次",
        3 => '三天一次',
    ),
    //打包总天数
    'db_totalday' => array(
        5 => "五天",
        10 => "十天",
        20 => "二十天",
        30 => "三十天"
    ),
);

