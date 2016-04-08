<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/html">
<head>
    <title><?php echo ($title); ?></title>
    <meta name="keywords" content=""/>
    <meta name="description" content=""/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    
    <link rel="stylesheet" href="http://182.92.192.250:8080/publicIndex/bootstrap/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="http://182.92.192.250:8080/publicIndex/stylesheets/style.css"/>
    <link rel="stylesheet" href="http://182.92.192.250:8080/publicIndex/stylesheets/index.css"/>
    <link rel="stylesheet" href="http://182.92.192.250:8080/publicIndex/header/header.css"/>
    <link rel="stylesheet" href="/Public/Css/public/index.css"/>
    <link rel="stylesheet" href="/Public/Css/Member/block/bin_index_alertDiv.css"/>
    
        
    
</head>
<body class="clearThis" ng-controller='cityBody'>

<div class="row m0 clearThis p0 c ">
    <div id="headContent"  ng-controller="topHeader">
        <div id="headTop" class="clearThis ">
            <div class="headTopSon clearThis">
                <div class="left topContentSuperMember">
                    <img src="/Public/Img/Member/block/member_top_icon_1.jpg">当前超级用户：<span>周小舟</span>
                    <div class="header_member_manger">子用户管理
                        <ul>
                            <?php if(is_array($subusers_arr)): $i = 0; $__LIST__ = $subusers_arr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><span class="name"><?php echo ($vo['name_true']); ?></span><span class="phone"><?php echo ($vo['mt']); ?></span><i>></i></li><?php endforeach; endif; else: echo "" ;endif; ?>
                            <img src="/Public/Img/Member/block/member_top_arrow2.jpg"/>
                        </ul>
                    </div>
                </div>
                <div class="left topContent linkMouse">
                    <img src="http://182.92.192.250:8080/publicIndex/header/imgs/dingbu_03.png" class="left">
                    <div href="http://wap.5656111.com:3001" class="left ">客服电话：</div>
                    <div style="font-weight:bolder" class="left">400-811-8311</div>
                    <div class="left ml10">|</div>
                    <img src="http://182.92.192.250:8080/publicIndex/header/imgs/dingbu_05.gif" class="left ml10">
                    <a href="http://qiao.baidu.com/v3/?module=default&amp;controller=im&amp;action=index&amp;ucid=7483065&amp;type=n&amp;siteid=5045540" target="_blank" class="left">
                        在线客服
                    </a>
                </div>
            </div>
        </div>
        <div id="headMid" style="margin-left: 300px;">
            <div id="logo" class="left linkMouse">
                <a href="/">
                    <img style="margin-top: -9px"  src="http://182.92.192.250:8080/publicIndex/header/imgs/logo.png">
                </a>
            </div>
            <div id="aboutGoods" class="left">
                <?php if(($_SESSION['userData']['user_type']) == "2"): ?><div icon="http://182.92.192.250:8080/publicIndex/header/imgs/sendGoods.png"
                     iconr="http://182.92.192.250:8080/publicIndex/header/imgs/sendGoodsR.png"
                     class="left goodsInfo linkMouse"><img
                        src="http://182.92.192.250:8080/publicIndex/header/imgs/sendGoods.png" class="left goodsImg">
                    <p class="left goodsFont" style="color: rgb(51, 51, 51); text-decoration: none;"><a
                            href="/Member/Goods/add_goods">发布货源</a></p></div><?php endif; ?>
                <?php if(($_SESSION['userData']['user_type']) == "1"): ?><div icon="http://182.92.192.250:8080/publicIndex/header/imgs/findGoods.png"
                     iconr="http://182.92.192.250:8080/publicIndex/header/imgs/findGoodsR.png"
                     class="left goodsInfo linkMouse"><img
                        src="http://182.92.192.250:8080/publicIndex/header/imgs/findGoods.png" class="left goodsImg">
                    <p class="left goodsFont" style="color: rgb(51, 51, 51); text-decoration: none;"><a
                            href="/Member/Bid/index">寻找货源</a></p></div><?php endif; ?>
            </div>
            <div id="appCode" class="left linkMouse" style="visibility: visible;"><img
                    src="http://182.92.192.250:8080/publicIndex/header/imgs/appCode.png"></div>
            <div id="navDownload" class="left">
                <div id="navWeixin" icon="http://182.92.192.250:8080/publicIndexnavWeixin.png"
                     iconr="http://182.92.192.250:8080/publicIndexnavApp.png" class="headerApp linkMouse"><img
                        src="http://182.92.192.250:8080/publicIndex/header/imgs/navWeixinG.png" class="left navImg">
                </div>
                <div id="navApp" class="headerApp linkMouse"><a href="http://www.5656111.com/news/12"><img
                        src="http://182.92.192.250:8080/publicIndex/header/imgs/navApp.png" class="left"></a></div>
            </div>
            <div id="loginInAlery" class="left">
                <div class="left">
                    <a href="/member/index">
                        <img style="margin-top: -6px"
                             src="http://182.92.192.250:8080/publicIndex/header/imgs/navMemberB.png"
                                ></a></div>
                <div style="width:110px" class="left" id="userData" userid="<?php echo ($_SESSION['userData']['id']); ?>" ip="<?php echo ($_SESSION['userIp']); ?>" city="<?php echo ($_SESSION['defaultCity']); ?>">
                    <div style="text-align: left;margin-top: 10px;margin-left: 5px" class="clearThis">
                        <div class="clearThis">欢迎您</div>
                        <div style="color: red;overflow-x:hidden"><?php echo ($_SESSION['userData']['name']); ?></div>
                        <div class="clearThis">
                            <span class="linkMouse" ng-click="logOut()">退出</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="headNav" class="clearThis">
            <ul class="clearThis homeNavUl">
                <li class="homePage firstNav clearThis"><a href="/">首页</a></li>
                <li class="navRighrBorder clearThis"></li>
                <li class="firstNav clearThis memberNavThis"><a
                        href="http://city.5656111.com/Home/Policy">政策发布</a>
                    <!--<ul class="secondNav">-->
                        <!--<li iconr="http://182.92.192.250:8080/publicIndex/header/imgs/icon2R.png"-->
                            <!--icon="http://182.92.192.250:8080/publicIndex/header/imgs/icon2.png"><a-->
                                <!--href="/news1920/78"><img-->
                                <!--src="http://182.92.192.250:8080/publicIndex/header/imgs/icon2.png" class="navImg liImg">-->

                            <!--<p class="navFont">VIP特权</p></a></li>-->
                        <!--<li class="lineB"></li>-->
                        <!--<li iconr="http://182.92.192.250:8080/publicIndex/header/imgs/icon3R.png"-->
                            <!--icon="http://182.92.192.250:8080/publicIndex/header/imgs/icon3.png"><a-->
                                <!--href="/member/item/xiYiWaitChe"><img-->
                                <!--src="http://182.92.192.250:8080/publicIndex/header/imgs/icon3.png"-->
                                <!--class="navImg liImg"><span class="navFont">协议中心</span></a></li>-->
                        <!--<li class="lineB"></li>-->
                        <!--<li iconr="http://182.92.192.250:8080/publicIndex/header/imgs/icon4R.png"-->
                            <!--icon="http://182.92.192.250:8080/publicIndex/header/imgs/icon4.png"><a-->
                                <!--href="/member/item/yaoQingCode"><img-->
                                <!--src="http://182.92.192.250:8080/publicIndex/header/imgs/icon4.png"-->
                                <!--class="navImg liImg"><span class="navFont">账户中心</span></a></li>-->
                        <!--<li class="lineB"></li>-->
                    <!--</ul>-->
                </li>
                <li class="navRighrBorder"></li>
                <li class="firstNav aboutItem"><a href="http://www.5656111.com/index">干线物流</a>
                    <!--<ul class="secondNav">-->
                        <!--<li iconr="http://182.92.192.250:8080/publicIndex/header/imgs/icon6R.png"-->
                            <!--icon="http://182.92.192.250:8080/publicIndex/header/imgs/icon6.png"><a href="http://www.5656111.com/news/7"><img-->
                                <!--src="http://182.92.192.250:8080/publicIndex/header/imgs/icon6.png"-->
                                <!--class="navImg liImg"><span class="navFont">公司概述</span></a></li>-->
                        <!--<li class="lineB"></li>-->
                        <!--<li iconr="http://182.92.192.250:8080/publicIndex/header/imgs/icon7R.png"-->
                            <!--icon="http://182.92.192.250:8080/publicIndex/header/imgs/icon7.png"><a-->
                                <!--href="http://www.5656111.com/articleList/107"><img-->
                                <!--src="http://182.92.192.250:8080/publicIndex/header/imgs/icon7.png"-->
                                <!--class="navImg liImg"><span class="navFont">新闻公告</span></a></li>-->
                        <!--<li class="lineB"></li>-->
                        <!--<li iconr="http://182.92.192.250:8080/publicIndex/header/imgs/icon8R.png"-->
                            <!--icon="http://182.92.192.250:8080/publicIndex/header/imgs/icon8.png"><a href="http://www.5656111.com/news/8"><img-->
                                <!--src="http://182.92.192.250:8080/publicIndex/header/imgs/icon8.png"-->
                                <!--class="navImg liImg"><span class="navFont">企业视频</span></a></li>-->
                        <!--<li class="lineB"></li>-->
                    <!--</ul>-->
                </li>
                <li class="navRighrBorder"></li>
                <li class="firstNav"><a href="http://city.5656111.com">同城货运</a>
                    <ul class="secondNav">
                        <li iconr="http://182.92.192.250:8080/publicIndex/header/imgs/icon10R.png"
                            icon="http://182.92.192.250:8080/publicIndex/header/imgs/icon10.png"><a href="http://city.5656111.com"><img
                                src="http://182.92.192.250:8080/publicIndex/header/imgs/icon10.png" class="navImg liImg">

                            <p class="navFont">普货物流</p></a></li>
                        <li class="lineB"></li>
                        <li iconr="http://182.92.192.250:8080/publicIndex/header/imgs/icon11R.png"
                            icon="http://182.92.192.250:8080/publicIndex/header/imgs/icon11.png"><a href="http://cold.5656111.com"><img
                                src="http://182.92.192.250:8080/publicIndex/header/imgs/icon11.png" class="navImg liImg">

                            <p class="navFont">冷链物流</p></a></li>
                        <!--<li class="lineB"></li>-->
                        <!--<li iconr="http://182.92.192.250:8080/publicIndex/header/imgs/icon12R.png"-->
                            <!--icon="http://182.92.192.250:8080/publicIndex/header/imgs/icon12.png"><a-->
                                <!--href="http://qiao.baidu.com/v3/?module=default&amp;controller=im&amp;action=index&amp;ucid=7483065&amp;type=n&amp;siteid=5045540"-->
                                <!--target="_blank"><img src="http://182.92.192.250:8080/publicIndex/header/imgs/icon12.png"-->
                                                     <!--class="navImg liImg">-->

                            <!--<p class="navFont">咨询建议</p></a></li>-->
                        <!--<li class="lineB"></li>-->
                    </ul>
                </li>
                <li class="navRighrBorder"></li>
                <li class="firstNav bbsItem"><a href="#">零担物流</a>
                    <!--<ul class="secondNav">-->
                        <!--<li iconr="http://182.92.192.250:8080/publicIndex/header/imgs/icon12R.png"-->
                            <!--icon="http://182.92.192.250:8080/publicIndex/header/imgs/icon12.png"><a-->
                                <!--href="http://www.5656111.com/Bbs/item/category/cid/103"><img-->
                                <!--src="http://182.92.192.250:8080/publicIndex/header/imgs/icon12.png" class="navImg liImg">-->

                            <!--<p class="navFont">公众点评</p></a></li>-->
                        <!--<li class="lineB"></li>-->
                    <!--</ul>-->
                </li>
                <li class="navRighrBorder"></li>
                <li class="firstNav newHuodongItem"><a href="http://www.5656111.com:8080/weixiu/view">维修联盟</a></li>
                <li class="navRighrBorder"></li>
                <li id="number" class="" >
                    <div class="left"><img src="http://182.92.192.250:8080/publicIndex/header/imgs/succeed.png"
                                           class="left"><span class="left ng-binding">成交：{{chengjiao1}}</span></div>
                    <div class="left"><img src="http://182.92.192.250:8080/publicIndex/header/imgs/memberNum.png"
                                           class="left"><span class="left ng-binding">会员：{{memberNum1}}</span></div>
                    <div class="left"><img src="http://182.92.192.250:8080/publicIndex/header/imgs/carNav.png"
                                           class="left"><span class="left ng-binding">在线车辆：{{onLineCarCount}}</span></div>
                </li>
            </ul>
        </div>
    </div>
</div>


<div class="clearThis homeContent mc" style="width: 1250px;">

    
    
        
        <link rel="stylesheet" href="http://182.92.192.250:8080/newEdition/public/css/leftNav.css">
<style>
    .thisFirst {
        font-weight: bold;
    }

    .third_title {
        padding-left: 40px;
    }

    .gaiBack:hover {
        background-color: #e3e3e3;
    }

    .oneNavTitle {
        font-size: 16px;
    }
</style>
<div class="left_nav left mt3 ml30" id="newLeftNav">
<div class="nav_title clearThis">
    <div class="left">
        <img src="http://182.92.192.250:8080/newEdition/public/imgs/block/navIcon.png" alt=""/>
    </div>
    <div class="left title_cont ml20">会员中心</div>
</div>
<div class="clearThis"></div>



<?php if(($_SESSION['userData']['user_type']) == "1"): ?><div class="nav_cont clearThis">


        
        
        <div class="first_nav clearThis" id="oneNav1">
            <div class="first_title clearThis gaiBack">
                <div class="left ml40 oneNavTitle">
                	<img class='leftNavImg' src='http://city.5656111.com//Public/Img/Member/block/ganxianNav.png' style='margin-top:-5px;margin-left:-27px'>
                	干线物流
                </div>
                <img class="right jianTou" style="margin-right: 43px"
                     src="http://182.92.192.250:8080/publicIndex/images/jdleft.png" alt=""/>
            </div>

            
            <?php if(is_array($oneCarList)): $i = 0; $__LIST__ = $oneCarList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo1): $mod = ($i % 2 );++$i; if(($vo1["pid"]) == "0"): if(($vo1["title_27"]) != "维修联盟"): ?><div class="send_nav">
                            <div class="nav_line"></div>
                            <div class="send_title ">
                                <div class="clearThis send_titleDiv gaiBack">
                                    <span class="ml40"><?php echo ($vo1["title_27"]); ?></span>
                                    <img class="right jianTou" style="margin-right: 43px"
                                         src="http://182.92.192.250:8080/publicIndex/images/jdleft.png" alt=""/>
                                </div>

                                
                                <div class="third_nav clearThis">
                                    <?php if(is_array($oneCarList)): $i = 0; $__LIST__ = $oneCarList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$subVo1): $mod = ($i % 2 );++$i; if(($subVo1["pid"]) == $vo1["id"]): ?><div class="nav_line"></div>
                                            <div class="third_title gaiBack"
                                                 onclickData="http://www.5656111.com/member/item/<?php echo ($subVo1["url"]); ?>">
                                                <?php echo ($subVo1["title_27"]); ?>
                                            </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                </div>
                                

                            </div>
                        </div><?php endif; endif; endforeach; endif; else: echo "" ;endif; ?>
            

        </div>
        


        
        
        <div class="first_nav clearThis" id="oneNav2">
            <div class="first_title clearThis gaiBack">
                <div class="left ml40 oneNavTitle">
                	<img class='leftNavImg' src='http://city.5656111.com//Public/Img/Member/block/puhuoNav.png' style='margin-top:-5px;margin-left:-27px'>
                同城货运(普货)</div>
                <img class="right jianTou" style="margin-right: 43px"
                     src="http://182.92.192.250:8080/publicIndex/images/jdleft.png" alt=""/>
            </div>

            
            <?php if(is_array($twoCarList)): $i = 0; $__LIST__ = $twoCarList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo1): $mod = ($i % 2 );++$i; if(($vo1["pid"]) == "0"): if(($vo1["title_27"]) != "维修联盟"): ?><div class="send_nav">
                            <div class="nav_line"></div>
                            <div class="send_title ">
                                <div class="clearThis send_titleDiv gaiBack">
                                    <span class="ml40"><?php echo ($vo1["title_27"]); ?></span>
                                    <img class="right jianTou" style="margin-right: 43px"
                                         src="http://182.92.192.250:8080/publicIndex/images/jdleft.png" alt=""/>
                                </div>

                                
                                <div class="third_nav clearThis">
                                    <?php if(is_array($twoCarList)): $i = 0; $__LIST__ = $twoCarList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$subVo1): $mod = ($i % 2 );++$i; if(($subVo1["pid"]) == $vo1["id"]): ?><div class="nav_line"></div>
                                            <div class="third_title gaiBack"
                                                 onclickData="http://city.5656111.com<?php echo ($subVo1["url"]); ?>">
                                                <?php echo ($subVo1["title_27"]); ?>
                                            </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                </div>
                                

                            </div>
                        </div><?php endif; endif; endforeach; endif; else: echo "" ;endif; ?>
            

        </div>
        

        
        

        <div class="first_nav clearThis" style="display: none;" id="oneNav3">
            <div class="first_title clearThis gaiBack">
                <div class="left ml40 oneNavTitle">
                	<img class='leftNavImg' src='http://city.5656111.com//Public/Img/Member/block/lenglianNav.png' style='margin-top:-5px;margin-left:-27px'>
                同城货运(冷链)</div>
                <img class="right jianTou" style="margin-right: 43px"
                     src="http://182.92.192.250:8080/publicIndex/images/jdleft.png" alt=""/>
            </div>

            
            <?php if(is_array($twoCarList)): $i = 0; $__LIST__ = $twoCarList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo1): $mod = ($i % 2 );++$i; if(($vo1["pid"]) == "0"): if(($vo1["title_27"]) != "维修联盟"): ?><div class="send_nav">
                            <div class="nav_line"></div>
                            <div class="send_title ">
                                <div class="clearThis send_titleDiv gaiBack">
                                    <span class="ml40"><?php echo ($vo1["title_27"]); ?></span>
                                    <img class="right jianTou" style="margin-right: 43px"
                                         src="http://182.92.192.250:8080/publicIndex/images/jdleft.png" alt=""/>
                                </div>

                                
                                <div class="third_nav clearThis">
                                    <?php if(is_array($twoCarList)): $i = 0; $__LIST__ = $twoCarList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$subVo1): $mod = ($i % 2 );++$i; if(($subVo1["pid"]) == $vo1["id"]): ?><div class="nav_line"></div>
                                            <div class="third_title gaiBack"
                                                 onclickData="http://cold.5656111.com<?php echo ($subVo1["url"]); ?>">
                                                <?php echo ($subVo1["title_27"]); ?>
                                            </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                </div>
                                

                            </div>
                        </div><?php endif; endif; endforeach; endif; else: echo "" ;endif; ?>
            

        </div>
        

        <!---->
        <!---->
        <!--<div class="first_nav clearThis" id="oneNav4">-->
            <!--<div class="first_title clearThis gaiBack">-->
                <!--<div class="left ml40 oneNavTitle">智慧物流</div>-->
                <!--<img class="right jianTou" style="margin-right: 43px"-->
                     <!--src="http://182.92.192.250:8080/publicIndex/images/jdleft.png" alt=""/>-->
            <!--</div>-->
        <!--</div>-->
        <!---->


        
        
        <div class="first_nav clearThis" id="oneNav5">
            <div class="first_title clearThis gaiBack">
                <div class="left ml40 oneNavTitle">维修联盟</div>
                <img class="right jianTou" style="margin-right: 43px"
                     src="http://182.92.192.250:8080/publicIndex/images/jdleft.png" alt=""/>
            </div>

            
                        <?php if(is_array($twoCarList)): $i = 0; $__LIST__ = $twoCarList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$subVo1): $mod = ($i % 2 );++$i; if(($subVo1["pid"]) == "84"): ?><div class="send_nav">
                                    <div class="nav_line"></div>
                                    <div class="send_title ">
                                        <div class="clearThis send_titleDiv gaiBack">
                                            <span class="ml40" onclickData="http://city.5656111.com<?php echo ($subVo1["url"]); ?>"><?php echo ($subVo1["title_27"]); ?></span>
                                            <img class="right jianTou" style="margin-right: 43px"
                                                 src="http://182.92.192.250:8080/publicIndex/images/jdleft.png" alt=""/>
                                        </div>
                                    </div>
                                </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
            

        </div>
        

    </div><?php endif; ?>





<?php if(($_SESSION['userData']['user_type']) == "2"): ?><div class="nav_cont clearThis">

        
        
        <div class="first_nav clearThis" id="oneNav1">
            <div class="first_title clearThis gaiBack">
                <div class="left ml40 oneNavTitle">
                    <img class='leftNavImg' src='http://city.5656111.com//Public/Img/Member/block/ganxianNav.png' style='margin-top:-5px;margin-left:-27px'>
                干线物流</div>
                <img class="right jianTou" style="margin-right: 43px"
                     src="http://182.92.192.250:8080/publicIndex/images/jdleft.png" alt=""/>
            </div>

            
            <?php if(is_array($oneHuoList)): $i = 0; $__LIST__ = $oneHuoList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo1): $mod = ($i % 2 );++$i; if(($vo1["pid"]) == "0"): ?><div class="send_nav">
                            <div class="nav_line"></div>
                            <div class="send_title ">
                                <div class="clearThis send_titleDiv gaiBack">
                                    <span class="ml40"><?php echo ($vo1["title_27"]); ?></span>
                                    <img class="right jianTou" style="margin-right: 43px"
                                         src="http://182.92.192.250:8080/publicIndex/images/jdleft.png" alt=""/>
                                </div>

                                
                                <div class="third_nav clearThis">
                                    <?php if(is_array($oneHuoList)): $i = 0; $__LIST__ = $oneHuoList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$subVo1): $mod = ($i % 2 );++$i; if(($subVo1["pid"]) == $vo1["id"]): ?><div class="nav_line"></div>
                                            <div class="third_title gaiBack"
                                                 onclickData="http://www.5656111.com/member/item/<?php echo ($subVo1["url"]); ?>">
                                                <?php echo ($subVo1["title_27"]); ?>
                                            </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                </div>
                                

                            </div>
                        </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
            

        </div>
        


        
        
        <div class="first_nav clearThis" id="oneNav2">
            <div class="first_title clearThis gaiBack">
                <div class="left ml40 oneNavTitle">
                    <img class='leftNavImg' src='http://city.5656111.com//Public/Img/Member/block/puhuoNav.png' style='margin-top:-5px;margin-left:-27px'>
                同城货运(普货)</div>
                <img class="right jianTou" style="margin-right: 43px"
                     src="http://182.92.192.250:8080/publicIndex/images/jdleft.png" alt=""/>
            </div>

            
            <?php if(is_array($twoHuoList)): $i = 0; $__LIST__ = $twoHuoList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo1): $mod = ($i % 2 );++$i; if(($vo1["pid"]) == "0"): if(($vo1["title_27"]) != "维修联盟"): ?><div class="send_nav">
                            <div class="nav_line"></div>
                            <div class="send_title ">
                                <div class="clearThis send_titleDiv gaiBack">
                                    <span class="ml40"><?php echo ($vo1["title_27"]); ?></span>
                                    <img class="right jianTou" style="margin-right: 43px"
                                         src="http://182.92.192.250:8080/publicIndex/images/jdleft.png" alt=""/>
                                </div>

                                
                                <div class="third_nav clearThis">
                                    <?php if(is_array($twoHuoList)): $i = 0; $__LIST__ = $twoHuoList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$subVo1): $mod = ($i % 2 );++$i; if(($subVo1["pid"]) == $vo1["id"]): ?><div class="nav_line"></div>
                                            <div class="third_title gaiBack"
                                                 onclickData="http://city.5656111.com<?php echo ($subVo1["url"]); ?>">
                                                <?php echo ($subVo1["title_27"]); ?>
                                            </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                </div>
                                

                            </div>
                        </div><?php endif; endif; endforeach; endif; else: echo "" ;endif; ?>
            

        </div>
        

        
        
        <div class="first_nav clearThis" id="oneNav3">
            <div class="first_title clearThis gaiBack">
                <div class="left ml40 oneNavTitle">
                    <img class='leftNavImg' src='http://city.5656111.com//Public/Img/Member/block/lenglianNav.png' style='margin-top:-5px;margin-left:-27px'>
                同城货运(冷链)</div>
                <img class="right jianTou" style="margin-right: 43px"
                     src="http://182.92.192.250:8080/publicIndex/images/jdleft.png" alt=""/>
            </div>

            
            <?php if(is_array($twoHuoList)): $i = 0; $__LIST__ = $twoHuoList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo1): $mod = ($i % 2 );++$i; if(($vo1["pid"]) == "0"): if(($vo1["title_27"]) != "维修联盟"): ?><div class="send_nav">
                            <div class="nav_line"></div>
                            <div class="send_title ">
                                <div class="clearThis send_titleDiv gaiBack">
                                    <span class="ml40"><?php echo ($vo1["title_27"]); ?></span>
                                    <img class="right jianTou" style="margin-right: 43px"
                                         src="http://182.92.192.250:8080/publicIndex/images/jdleft.png" alt=""/>
                                </div>

                                
                                <div class="third_nav clearThis">
                                    <?php if(is_array($twoHuoList)): $i = 0; $__LIST__ = $twoHuoList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$subVo1): $mod = ($i % 2 );++$i; if(($subVo1["pid"]) == $vo1["id"]): ?><div class="nav_line"></div>
                                            <div class="third_title gaiBack"
                                                 onclickData="http://cold.5656111.com<?php echo ($subVo1["url"]); ?>">
                                                <?php echo ($subVo1["title_27"]); ?>
                                            </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                </div>
                                

                            </div>
                        </div><?php endif; endif; endforeach; endif; else: echo "" ;endif; ?>
            

        </div>
        



    </div><?php endif; ?>






<?php if(($_SESSION['userData']['user_type']) == "3"): ?><div class="nav_cont clearThis">


        
        
        <div class="first_nav clearThis" id="oneNav2">
            <div class="first_title clearThis gaiBack">
                <div class="left ml40 oneNavTitle">
                	<img class='leftNavImg' src='http://city.5656111.com//Public/Img/Member/block/puhuoNav.png' style='margin-top:-5px;margin-left:-27px'>
                城市货运(普货)</div>
                <img class="right jianTou" style="margin-right: 43px"
                     src="http://182.92.192.250:8080/publicIndex/images/jdleft.png" alt=""/>
            </div>

            
            <?php if(is_array($twoBigList)): $i = 0; $__LIST__ = $twoBigList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo1): $mod = ($i % 2 );++$i; if(($vo1["pid"]) == "0"): if(($vo1["title_27"]) != "维修联盟"): ?><div class="send_nav">
                            <div class="nav_line"></div>
                            <div class="send_title ">
                                <div class="clearThis send_titleDiv gaiBack">
                                    <span class="ml40"><?php echo ($vo1["title_27"]); ?></span>
                                    <img class="right jianTou" style="margin-right: 43px"
                                         src="http://182.92.192.250:8080/publicIndex/images/jdleft.png" alt=""/>
                                </div>

                                
                                <div class="third_nav clearThis">
                                    <?php if(is_array($twoBigList)): $i = 0; $__LIST__ = $twoBigList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$subVo1): $mod = ($i % 2 );++$i; if(($subVo1["pid"]) == $vo1["id"]): ?><div class="nav_line"></div>
                                            <div class="third_title gaiBack"
                                                 onclickData="http://city.5656111.com<?php echo ($subVo1["url"]); ?>">
                                                <?php echo ($subVo1["title_27"]); ?>
                                            </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                </div>
                                

                            </div>
                        </div><?php endif; endif; endforeach; endif; else: echo "" ;endif; ?>
            

        </div>
        

        
        
        <div class="first_nav clearThis" id="oneNav3">
            <div class="first_title clearThis gaiBack">
                <div class="left ml40 oneNavTitle">
                    <img class='leftNavImg' src='http://city.5656111.com//Public/Img/Member/block/lenglianNav.png' style='margin-top:-5px;margin-left:-27px'>
                城市货运(冷链)</div>
                <img class="right jianTou" style="margin-right: 43px"
                     src="http://182.92.192.250:8080/publicIndex/images/jdleft.png" alt=""/>
            </div>

            
            <?php if(is_array($twoBigList)): $i = 0; $__LIST__ = $twoBigList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo1): $mod = ($i % 2 );++$i; if(($vo1["pid"]) == "0"): if(($vo1["title_27"]) != "维修联盟"): ?><div class="send_nav">
                            <div class="nav_line"></div>
                            <div class="send_title ">
                                <div class="clearThis send_titleDiv gaiBack">
                                    <span class="ml40"><?php echo ($vo1["title_27"]); ?></span>
                                    <img class="right jianTou" style="margin-right: 43px"
                                         src="http://182.92.192.250:8080/publicIndex/images/jdleft.png" alt=""/>
                                </div>

                                
                                <div class="third_nav clearThis">
                                    <?php if(is_array($twoBigList)): $i = 0; $__LIST__ = $twoBigList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$subVo1): $mod = ($i % 2 );++$i; if(($subVo1["pid"]) == $vo1["id"]): ?><div class="nav_line"></div>
                                            <div class="third_title gaiBack"
                                                 onclickData="http://cold.5656111.com<?php echo ($subVo1["url"]); ?>">
                                                <?php echo ($subVo1["title_27"]); ?>
                                            </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                </div>
                                

                            </div>
                        </div><?php endif; endif; endforeach; endif; else: echo "" ;endif; ?>
            

        </div>
        


        
        
        <div class="first_nav clearThis" id="oneNav5">
            <div class="first_title clearThis gaiBack">
                <div class="left ml40 oneNavTitle">维修联盟</div>
                <img class="right jianTou" style="margin-right: 43px"
                     src="http://182.92.192.250:8080/publicIndex/images/jdleft.png" alt=""/>
            </div>

            
            <?php if(is_array($twoCarList)): $i = 0; $__LIST__ = $twoCarList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$subVo1): $mod = ($i % 2 );++$i; if(($subVo1["pid"]) == "84"): ?><div class="send_nav">
                        <div class="nav_line"></div>
                        <div class="send_title ">
                            <div class="clearThis send_titleDiv gaiBack">
                                <span class="ml40" onclickData="http://city.5656111.com<?php echo ($subVo1["url"]); ?>"><?php echo ($subVo1["title_27"]); ?></span>
                                <img class="right jianTou" style="margin-right: 43px"
                                     src="http://182.92.192.250:8080/publicIndex/images/jdleft.png" alt=""/>
                            </div>
                        </div>
                    </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
            

        </div>
        

    </div><?php endif; ?>






</div>

    

    <div class="colR ">
        
    <div key-show>            // 搜索协议编号
    <!--<div page></div>   放到keyShow的html代码块-->
    </div>
    <div search-box></div>
    <div class="tabTitle">
        <ul class="tabLine">
            <li class="tabTit">货物信息</li>
            <li class="tabTit">地址信息</li>
            <li class="tabTit">车源信息</li>
            <li class="tabTit">运费信息</li>
            <li class="tabTit">状态</li>
        </ul>
    </div>
    <div class="contain">
        <div tab-msg>
            <!--&lt;!&ndash;<div msg></div>&ndash;&gt;放到tabMsg代码-->
        </div>
    </div>
    <div page></div>

    <link href="/Public/resCreate/css/src/rightContent.index.css" rel="stylesheet" type="text/css">

    </div>
</div>


<div class="homeFootBanner clearThis mc" style="width: 1250px;">
    <style type="text/css">
        .link_img{
            overflow: hidden;
            float: left;
            width: 165px;
            height: auto;
        }
    </style>
    <div style="width:1250px;height:120px" class="clearThis">
        <div class="left">
             <img src="http://182.92.192.250:8080/publicIndex/images/footerBanner_01.png" width="750px" height="120px" border="0px" alt="">
        </div>
        <div class="left">
                <img src="http://182.92.192.250:8080/publicIndex/images/footerBanner_02.jpg" width="499" height="120" border="0" alt="">
        </div>
    </div>
    <div class="clearThis">
        <link rel="stylesheet" href="http://182.92.192.250:8080/publicIndex/stylesheets/block/homeLink.css">
        <div class="clearThis homeLink">
            <div class="linkContent">
                <div class="topLink clearThis">
                    <div class="leftTitleContent left">
                        <div class="">
                            <img src="http://182.92.192.250:8080/publicIndex/images/link2.png">
                        </div>
                    </div>
                    <div class="rightLinkContent left">
                        <div class="prevS prev1 linkHoverButton linkMouse" style="display: none;">
                            <img src="http://182.92.192.250:8080/publicIndex/images/leftJiantou.png">
                        </div>
                        <div class="carousel" style="visibility: visible; overflow: hidden; position: relative; z-index: 2; left: 0px; width: 1020px;">

                            <ul>
                                <li class="link_img">
                                    <a href="http://www.chinawuliu.com.cn/" target="_blank" >
                                    <img src="http://182.92.192.250:8080/publicIndex/images/link/2.png" class="carouselImg" >
                                    </a>
                                </li>
                                <li class="link_img">
                                    <a href="http://www.tj56.com/" target="_blank">
                                        <img src="http://182.92.192.250:8080/publicIndex/images/link/1.png" class="carouselImg">
                                    </a>
                                </li>
                                <li class="link_img">
                                    <a href="http://www.tsia.com.cn/" target="_blank">
                                        <img src="http://182.92.192.250:8080/publicIndex/images/link/3.png" class="carouselImg">
                                    </a>
                                </li>
                                <li class="link_img">
                                    <a href="http://www.chinawuliu.com.cn/" target="_blank">
                                    <img src="http://182.92.192.250:8080/publicIndex/images/linkLogo/link3.png" class="carouselImg">
                                    </a>
                                </li>
                                <li class="link_img">
                                    <a href="https://www.alipay.com/" target="_blank">
                                    <img src="http://182.92.192.250:8080/publicIndex/images/linkLogo/link2.png" class="carouselImg">
                                    </a>
                                </li>
                                <li class="link_img" style="width:190px;">
                                    <a href="http://www.abchina.com/cn/" target="_blank">
                                    <img src="http://182.92.192.250:8080/publicIndex/images/linkLogo/link1.png" class="carouselImg">
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="nextS linkHoverButton next1 linkMouse" style="display: none;">
                            <img src="http://182.92.192.250:8080/publicIndex/images/rightJiantou.png">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-3" ></div>
    <div class="col-xs-6 c" style="margin-top: 10px;">
        <div class="footNav c">
            <a href="http://www.5656111.com/news/92#93" style="color:#000000;">关于我们</a> &nbsp;
            <a href="http://www.5656111.com:8080/index.php/Pro/" style="color: #000000;">货源历史</a>
        </div>
    </div>
    <div class="col-xs-3"></div>
</div>
<div style="text-align:center" class="clearThis copy col-xs-12 m0 p0">版权所有：天津正易物通网络科技有限公司 津ICP备14006887号-1</div>


<div class="alertDivAll">
    <div class="topItem"></div>
    <div class="contentItem3">
        <div class="closeAlert2 linkMouse right">
        </div>
        <div class="clearThis alertDivAllContent ">内容</div>
    </div>
    <div class="bomItem"></div>
</div>
<div id="gai"></div>
<div id="messDiv"></div>
<div id="close" style="width:30px;height:30px;" class="linkMouse" ng-click="closeMessDiv()"></div>
<div id="carSelectClose" onclick="carSelectClose()" class="linkMouse"></div>






    <script>
        var dist = false;
        //生产环境
        if (dist) {
            document.write('<script src="/Public/resCreate/js/app/dist/app.js"><\/script>');
        }
        //开发环境
        else {
            document.write('<script src="/Public/resCreate/js/app/dist/appDev.js"><\/script>');
        }
    </script>






</body>
</html>