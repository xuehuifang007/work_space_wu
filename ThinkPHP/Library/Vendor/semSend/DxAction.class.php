<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 14-2-13
 * Time: 下午6:30
 *  发短信公用方法
 */
class Dx
{

//php get
    function Get ( $url ) {
        if ( function_exists ( 'file_get_contents' ) ) {
            $file_contents = file_get_contents ( $url ) ;
        } else {
            $ch = curl_init () ;
            $timeout = 5 ;
            curl_setopt ( $ch, CURLOPT_URL, $url ) ;
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 ) ;
            curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, $timeout ) ;
            $file_contents = curl_exec ( $ch ) ;
            curl_close ( $ch ) ;
        }
        return $file_contents ;

    }

// 发短信，传电话数组，内容。返回成功条数,
   public  function sendDx($telArrStr=null,$content=null){

        $url = 'http://42.121.122.61:18002/send.do?ua=rockblus&pw=657926&mb='.$telArrStr.'&ms='.$content.'&ex=01';

//        发短信
        if ( $num = DxAction::Get ( $url ) > 0 ){
            return $num;
        }

    }

// 判断手机号段 传入 手机号，返回 yiDong  lianTong
    function haoDuan($mtNum=null){
        if(!empty($mtNum)){
            $mtNum = str_split($mtNum,3);
            $mtNum = (int)$mtNum[0];
            $yiDong = array(134,135,136,137,138,139,147,150,151,152,157,158,159,182,187,188);
            if(in_array($mtNum,$yiDong)){
                return 'yiDong';
            }else{
                return 'lianTong';
            }
        }
    }

//http://42.121.122.61:18002/send.do?ua=rockblus&pw=657926&mb=15510986492,13388066759,13622034183,18947157789&ms= 物流邦IT部群发测试&ex=01


}