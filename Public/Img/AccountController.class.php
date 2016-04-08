<?php
namespace Member\Controller;
class AccountController extends MemberBasicController{
    protected $member_table;
    protected $banshichu_table;
    protected $bank_table;
    protected $user_id;
    protected $verify_table;
    protected $lottery_table;

    public function _initialize(){
        parent::_initialize();
        $this->member_table    = M('member');
        $this->banshichu_table = M('banshichu');
        $this->verify_table    = M('cshy_verify');
        $this->user_id = $_SESSION['userData']['id'];
        $this->bank_table      = M("cshy_bank");
        $this->user_id         = session("userData.id");
        $this->lottery_table   = M("cshy_lottery");
    }

    //登录密码修改
    function login_pass(){
        if(IS_POST){
                //加密post过来的旧密码
                $oldpwd_post = I("post.old_passpwd");
                $where_arr["password"] = MD5($oldpwd_post);
                $where_arr["id"] = $this->user_id;
                $member_password = $this->member_table->field('id,password')->where($where_arr)->find();
            if($member_password){
                $newpwd_post["new_password"] = I("post.new_passpwd");
                $newpwd_post["re_password"] = I("post.re_passpwd");
                $post_result = $this->post_null($newpwd_post);
                if($post_result == false){
                    $this->error('参数不能为空');
                }
                $data_arr["password"] = md5($newpwd_post["re_password"]);
                $result = $this->member_table->where($where_arr)->save($data_arr);
                if($result){
                    $this->success("密码修改成功");
                }else{
                    $this->error("修改密码失败");
                }
            }else{
                    $this->error("原始密码不正确");
            }
        }else{
                    $this->display();
        }
    }


    //支付密码修改
    function pay_pass(){
        if(IS_POST){
                $action_post = I("post.action");
            if($action_post == "pass_edit"){
                //加密post过来的旧密码
                $oldpwd_post = I("post.old_passpwd");
                $oldpwd_post_md5 = MD5($oldpwd_post);
                //查找数据库中的密码
                $where_arr["id"] = $this->user_id;
                $member_arr1 = $this->member_table->field("pay_password")->where($where_arr)->find();
                $oldppwd_find = $member_arr1["pay_password"];
                if($oldppwd_find == $oldpwd_post_md5){
                    $newpwd_post["new_password"] = I("post.new_passpwd");
                    $newpwd_post["re_password"] = I("post.re_passpwd");
                    $post_result = $this->post_null($newpwd_post);
                    if($post_result == false){
                        $this->error('参数不能为空');
                    }
                    $data_arr["pay_password"] = md5($newpwd_post["re_password"]);
                    $result = $this->member_table->where($where_arr)->save($data_arr);
                    if($result){
                        $this->success("支付密码修改成功");
                    }else{
                        $this->error("支付密码修改失败");
                    }
                }else{
                        $this->error("原始密码不正确！");
                }
            }elseif($action_post == "pass_add"){
                $where_arr["id"] = $this->user_id;
                $passpwd_post["new_password"] = I("post.new_passpwd");
                $passpwd_post["re_password"] = I("post.re_passpwd");
                $post_result = $this->post_null($passpwd_post);
                if($post_result == false){
                    $this->error('参数不能为空');
                }
                $data_arr["pay_password"] = md5($passpwd_post["re_password"]);
                $result = $this->member_table->where($where_arr)->save($data_arr);
                if($result){
                    $this->success("保存密码成功！");
                }else{
                    $this->error("保存密码失败！");
                }
            }
        }else{
                //查找数据库中的密码
                $where_arr["id"] = $this->user_id;
                $member_arr = $this->member_table->field("pay_password")->where($where_arr)->find();
                $this->assign("member_arr",$member_arr);
                $this->display();
        }
    }

    //收款方式 显示 添加与修改 do_com_pay
    public function bank_pay(){
            $msg_info = "";
            $where_user["uid"] = $this->user_id;
            $bank_info = $this->bank_table->where($where_user)->find();
            if(!empty($bank_info["bank_type"])){
                $info_sign = "1";
            }else{
                $info_sign = "0";
            }

        if(IS_POST){
            $data_arr["bank_type"] = I("post.bank_type");
            $data_arr["bank_user"] = I("post.bank_user");
            $data_arr["bank_account"] = I("post.bank_account");
            $data_arr["branch_info"] = I("post.branch_info");
            $data_arr["addtime"] = time();
            $data_arr["status"] = "1";
            $post_result = $this->post_null($data_arr);
            if($post_result == false){
                $this->error("参数不能为空");
            }

                //查找银行表中有没有记录
            if($info_sign == "1"){
                $add_result = $this->bank_table->where($where_user)->save($data_arr);
                if($add_result !== false){
                    $msg_info = "编辑银行账户成功";
                }else{
                    $msg_info = "编辑银行账户失败";
                }
            }else{
                $data_arr["uid"] = $this->user_id;
                $edit_result = $this->bank_table->add($data_arr);
                if($edit_result !== false){
                    $msg_info = "添加银行账户成功";
                }else{
                    $msg_info = "添加银行账户失败";
                }
            }
                $jump_url = U("Account/bank_pay");
                $this->success($msg_info , $jump_url);
        }else{
            if($info_sign == "1"){
                //生成带星号数据
                $bank_data["bank_type"] = $bank_info["bank_type"];
                $bank_data["bank_user"] = substr($bank_info["bank_user"],0,3)."**";
                $bank_data["bank_account"] = substr($bank_info["bank_account"],0,3)."****".substr($bank_info["bank_account"],-4);
                $this->assign("bank_data",$bank_data);
            }
                $this->assign("info_sign",$info_sign);
                $this->assign("list",$bank_info);
                $this->display('Account/bank_pay');
        }
    }

    /*
     * 添加银行卡获取验证码操作
     */
/*    function get_code(){
        $where_arr["id"] = $this->user_id;
        $member_arr = $this->member_table->field('mt')->where($where_arr)->find();

        $verify_data["type"] = "2";
        $verify_data["sendtime"] = time();
        $verify_data["uid"] = $this->user_id;
        $verify_data["vertify_code"] = rand(000000,999999);
        $verify_data["user_tel"]  = $num = $member_arr["mt"];

        $str = '【物流邦】您的物流邦验证码:'.$verify_data["vertify_code"].' (10分钟内有效呦^!^)';
        $sent_result = $this->sentSm($num,$str);
        $verify_result = $this->verify_table->add($verify_data);
        if($sent_result && $verify_result){

        }else{

        }
    }*/

    /*
     *发送短信验证码
     */
    function sentSm($num='',$str=''){
        $call =  A('SemSend/Index');
        $result = $call->sendSm($num,$str);
        return $result;
    }


    //公共方法，看传过来的参数是否为空
    public function post_null($get_arr){
        $msg_data["sign"] = true;
        foreach($get_arr as $key=>$val){
            if(empty($val)){
                unset($get_arr[$key]);
                $msg_data["sign"] = false;
            }
        }
        if($msg_data["sign"] == true){
            $msg_data["code"] = $get_arr;
        }
            return  $msg_data;
    }

    /*
    * 账户中心 找回密码操作
    */
    function find_passpwd(){
        if(IS_POST){
            $now_time = time();
            $member_pwd["paypwd"] = I('post.paypwd');
            $member_pwd["re_paypwd"] = I('post.re_paypwd');
            $where_verify["vertify_code"] = I('post.sms_code');
            $where_verify["uid"] = $_SESSION["userData"]["id"];
            $verify_arr = $this->verify_table->field("sendtime")->where($where_verify)->find();
            $jump_url = U('Account/pay_pass');
            if($verify_arr){
                if(($verify_arr["sendtime"]+600)>$now_time){
                    $verify_result = $this->modify_paypwd( $where_verify["uid"],$member_pwd["re_paypwd"],$where_verify["vertify_code"]);
                    if($verify_result){
                        $this->success("密码修改成功",$jump_url);
                    }else{
                        $this->error("密码修改失败",$jump_url);
                    }
                }else{
                        $this->error("验证码已超时，请重新获取");
                }
            }else{
                        $this->error("验证码错误");
            }
        }else{
            $member_mt = $_SESSION["userData"]["mt"];
            $this->assign('member_mt',$member_mt);
            $this->display();
        }

    }

    /*
     * 执行修改支付密码操作
     */
    function modify_paypwd($id_str,$pay_password,$sms_code){
        $where_member["id"] = $id_str;
        $member_data["pay_password"] = md5($pay_password);
        $member_result = $this->member_table->where($where_member)->save($member_data);
        if($member_result !==false){
            $where_verify["uid"] = $id_str;
            $where_verify["vertify_code"] = $sms_code;
            $verify_data["status"] = "9";
            $verify_data["sendtime"] = "0";
            $verify_result = $this->verify_table->where($where_verify)->save($verify_data);
            return $verify_result;
        }
    }

    /*
     * 找回密码 获取验证码 并把数据插入verify表
     */
    function send_sms(){
        $sms_code =rand(500000,999999);
        $sms_str = '【物流邦】您的验证码:'.$sms_code.' (10分钟内有效呦^!^)';
        $where_member["id"] = $_SESSION["userData"]["id"];
        $member_arr = $this->member_table->field('mt')->where($where_member)->find();
        // 调用发送短信接口
        $sms_obj =  A('SemSend/Index');
        $sms_rlt = $sms_obj->sendSm($member_arr['mt'],$sms_str);
        if($sms_rlt){
            $verify_data["type"] = "3";
            $verify_data["sendtime"] = time();
            $verify_data["vertify_code"] = $sms_code;
            $verify_data["uid"] = $where_member["id"];
            $verify_data["user_tel"] = $member_arr["mt"];
            $verify_result = $this->verify_table->add($verify_data);
            if($verify_result){
                $msg_data["sign"] ="k0";
            }else{
                $msg_data["sign"] ="a0";
            }
            $this->ajaxReturn($msg_data,"JSON");
        }
    }

    /*
     * 首页抽奖活动
     * 查询可以抽奖的协议 并插入到抽奖记录表
     */

/*    function search_deal($deal_arr)
    {
        if (!empty($deal_arr)) {
            $where_lottery["status"] = "1";
            $lottery_sum = $this->lottery_table->sum("lottery_money");
            if (($deal_arr["sendtime"] > "1432051200") && ($lottery_sum < 115000)) {
                $lottery_data["lottery_state"] = "0";
                $lottery_data["user_id"] = $deal_arr["hz_id"];
                $lottery_data["deal_id"] = $deal_arr["id"];
                $lottery_result = $this->lottery_table->add($lottery_data);
                if ($lottery_result) {
                    return true;
                } else {
                    return false;
                }
            } else {
                $lottery_data["status"] = "9";
                $lottery_data["is_valid"] = "1";
                $where_lottery["status"] = "1";
                $where_lottery["lottery_state"] = "0";
                $lottery_result = $this->lottery_table->where($where_lottery)->save($lottery_data);
                if ($lottery_result) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }*/

    /*
     * 计算奖池中各个档次剩余的抽奖次数
     */
    function lottery_surplus(){
        $where_lottery["status"] = "1";
        $lottery_field = "count(lottery_money) as num";
        $lottery_num = $this->lottery_table->field($lottery_field)->where($where_lottery)->group("lottery_money")->select();
        $count_money = $this->lottery_table->sum('lottery_money');
        $lottery["100"] = 200  - $lottery_num[4]['num'];
        $lottery["50"]  = 500  - $lottery_num[3]['num'];
        $lottery["30"]  = 1000 - $lottery_num[2]['num'];
        $lottery["20"]  = 2000 - $lottery_num[1]['num'];
        $lottery["count_money"] = $count_money;
        $lottery["total"] = $lottery["100"] + $lottery["50"] + $lottery["30"] + $lottery["20"];
        return $lottery;
    }

    /*
     * 查询用户的抽奖信息
     * 总抽奖次数 未抽奖次数 已抽奖总数 抽奖总金额
     */
    function member_lottery(){
        $user_id = I("get.uid");
        $member_lottery = $this->lottery_info($user_id);
        $hour_ago = strtotime("-1 minutes");
        //$hour_ago = strtotime("-1 hours");
        if($_SESSION['ly_time'] <= $hour_ago && $member_lottery["wait"] > 0){
            $_SESSION['ly_time'] = time();
            $member_lottery["sign"] = "k0";
        }else{
            $member_lottery["sign"] = "a0";
        }
        if($_GET['from']){
            $this->ajaxReturn($member_lottery,"JSON");
        }else{
            $this->ajaxReturn($member_lottery,"JSONP");

        }
    }

    /**
     * 查询用户抽奖信息
     * @param $lottery
     * @return bool
     */
    function lottery_info($user_id){
        $where_lottery["user_id"] = $user_id;
        $where_lottery["status"] = "1";
        //抽奖总数
        $member_lottery["sum"] = $this->lottery_table->where($where_lottery)->count();
        //待抽奖数
        $where_lottery["lottery_state"] = "0";
        $member_lottery["wait"] = $this->lottery_table->where($where_lottery)->count();
        //已抽奖数
        $member_lottery["already"] = $member_lottery["sum"] - $member_lottery["wait"];
        //中奖金额
        $where_lottery["lottery_state"] = "1";
        $member_lottery["lottery_money"] = $this->lottery_table->where($where_lottery)->sum("lottery_money");
        $member_lottery["lottery_money"] = empty($member_lottery["lottery"])?$member_lottery["lottery_money"]:0;
        return $member_lottery;

    }

    /*
     * 把抽奖金额 抽奖时间 记录到lottery表
     */
    function lottery_save($lottery){
        $user_id = $lottery["user_id"];
        $lottery_money = $lottery["lottery_money"];
        $lottery_time = time();
        $where_lottery["status"] = "1";
        $where_lottery["lottery_state"] = "0";
        $where_lottery["user_id"] = $user_id;
        $lottery_data["is_check"] = "1";
        $lottery_data["lottery_state"] = "1";
        $lottery_data["lottery_time"] = $lottery_time;
        $lottery_data["lottery_money"] = $lottery_money;

        $lottery_arr = $this->lottery_table->field("id")->where($where_lottery)->find();
        if($lottery_arr){
            $where_id["id"] = $lottery_arr["id"];
            $lottery_result = $this->lottery_table->where($where_id)->save($lottery_data);
            if($lottery_result){
                $result = true;
            }else{
                $result = false;
            }
        }else{
                $result = false;
        }
                return $result;
    }

    /*
     * 查询所有用户的中奖信息
     */
    function search_lottery(){
        $where_lottery["status"] = "1";
        $where_lottery["is_valid"] = "0";
        $where_lottery["lottery_state"] = "1";
        $lottery_field = "user_id,lottery_time,lottery_money";
        $lottery_arr = $this->lottery_table->field($lottery_field)->where($where_lottery)->select();
        foreach($lottery_arr as $key=>&$val){
            $member_arr = $this->member_table->where("id={$val["user_id"]}")->getField("name");
            $val["name"] = $member_arr;
            unset($val["user_id"]);
        }
            $this->ajaxReturn($lottery_arr,"JSONP");
    }

    /*
     * 根据剩余的抽奖次数 随机产生抽奖金额
     * 100元 50元 30元 20元
     */
    function rand_money(){
        // 限制 node 请求次数
        if(($_SESSION["time"]["time"] + 1)>time()){
            $this->ajaxReturn($_SESSION["time"],"JSONP");
        }

        // 验证奖池中的抽奖总次数，总金额，用户的抽奖资格
        $surplus = $this->lottery_detect();

        do{
            $rand_num = array(100,50,50,50,30,30,30,30,30,20,20,20,20,20,20,20,20,20,20,);
            $key = array_rand($rand_num);
        }while($surplus[$rand_num[$key]] <= 0);
            $lottery["user_id"] = $surplus["user_id"];
            $lottery["lottery_money"] = $rand_num[$key];
            switch($lottery["lottery_money"]){
                case 20:
                    $lottery["lottery_order"] = "参与奖";
                    break;
                case 30:
                    $lottery["lottery_order"] = "三等奖";
                    break;
                case 50:
                    $lottery["lottery_order"] = "二等奖";
                    break;
                case 100:
                    $lottery["lottery_order"] = "一等奖";
                    break;
            }
            // 把抽奖的金额插入到数据库（修改）
            $lottery["result"] = $this->lottery_save($lottery);
            $lottery["time"] = time();
            $_SESSION["time"] = $lottery;
            $this->ajaxReturn($lottery,"JSONP");
    }

    /**
     * 抽奖的验证
     *  1.验证奖池的总金额，总次数
     *  2.验证用户是否有抽奖资格
     */
    function lottery_detect(){
        $where_lottery["status"] = "1";
        $where_lottery["is_check"] = "0";
        $where_lottery["is_valid"] = "0";
        $where_lottery["user_id"] = I("get.uid");
        $where_lottery["lottery_state"] = "0";

        // 计算奖池中有奖金的档次和剩余的抽奖次数
        $surplus = $this->lottery_surplus();
        $surplus["user_id"] = $where_lottery["user_id"];
        $lottery_count = $this->lottery_table->where($where_lottery)->count();

        //验证奖池中的抽奖总次数，总金额，用户的抽奖资格
        if(($surplus["total"] <= 0) || ($surplus["count_money"] >= 115000) || ($lottery_count <= 0)){
            /**奖金发放完毕
             * 1：奖金发放完毕
             * 2：用户没有抽奖资格
             */
            $this->ajaxReturn(array("result"=>false),"JSONP");
        }
        if($lottery_count <= 0){

        }
            return $surplus;
    }

    /*
     * 活动首页 总的抽奖信息(目前没用到)
     * 查询活动可抽奖总次数 已抽奖次数 剩余抽奖次数
     */
    function lottery_count(){
        $where_lottery["status"] = "1";
        $where_lottery["is_valid"] = "0";
        $where_lottery["lottery_state"] = "1";
        $lottery_count = $this->lottery_table->where($where_lottery)->count();
        $lottery_money = $this->lottery_table->where($where_lottery)->sum("lottery_money");
        $lottery_arr["total"] = 3700;
        $lottery_arr["already"] = $lottery_count;
        $lottery_arr["wait"] = 3700 - $lottery_count;
        $lottery_arr["money"] = !empty($lottery_money)?$lottery_money:0;
        $this->ajaxReturn($lottery_arr,"JSONP");
    }

    /**
     * 抽奖页面权限验证
     * 判断get的值是否与 SESSION 的值一致
     */
    function get_login(){
        $get_uid = I("get.uid");
        $session_id = $this->user_id;
        if($get_uid != $session_id){
            $msg['sign'] = "a0";
            $msg['code'] = $this->user_id;
            $this->ajaxReturn($msg,"JSON");
        }
    }















































}

