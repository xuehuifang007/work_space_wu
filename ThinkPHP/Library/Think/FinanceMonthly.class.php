<?php
namespace Think;

class FinanceMonthly{
    protected  $fnrcd_table;
    //保证金的数量
    protected $deposit_mny;
    //协议表
    protected $deal_table;
    function __construct(){
        $this->fnrcd_table = M("cshy_fnrcd",'tp_');
        $this->deal_table = M("cshy_deal",'tp_');
        $this->deposit_mny = 200;
    }
    /**
     *# 权限判断
     *# 需要开发的方法
     *
     *   【现结货源方正常】
     *     一、现结货主、发起协议
     *     二、车主签约
     *     三、货主确认
     *     四、财务付车源方钱
     *
     *   【现结货源方、车主拒签】
     *     一、车源方交纳保证金
     *     二、现结货主发起协议
     *     三、车源方拒签
     *     四、财务退款
     *
     *   【现结货源方、车主未履约】
     *     一、交纳保证金金
     *     二、车源方签约
     *     三、（货主投诉）客服核实
     *     四、财务退款
     *
     *   【现结货源方、货主未履约】
     *     一、现结货主、发起协议
     *     二、车源方签约
     *     三、（车源方投诉）客服核实
     *     四、达成补偿
     *     五、财务退款
     *
     *  【现结货源方、货主投诉】
     *     一、现结货主、发起协议
     *     二、车源方签约
     *     三、（货源方投诉、客服核实）达成赔偿
     *     四、财务付款/退款
     *
     *  【现结货源方_预付费、正常执行】
     *     一、交纳预付费
     *     二、车源方缴保证金
     *     三、现结货主、发起协议
     *     四、车主签约
     *     五、货主确认
     *     六、财务付款给车源方
     *
     *  【现结货源方_预付费、车源方拒签】
     *     一、交纳预付费
     *     二、车源方缴保证金
     *     三、货源方发起协议
     *     四、车源方拒签
     *     五、财务赔付
     *
     *  【现结货源方_预付费、车源方未履约】
     *       一、交纳预付费
     *       二、车源方缴保证金
     *       三、现结货主、发起协议
     *       四、车源方签约
     *       五、货源方投诉（客服核实）
     *       六、财务退款
     *
     *  【月结货源方、正常执行】
     *       一、车源方交纳保证金
     *       二、货主发起协议
     *       三、车主签约
     *       四、货主确认
     *
     *  【月结货源方、车主拒签】
     *       一、车主交纳保证金
     *       二、车源方拒签
     *       三、财务理赔
     *
     *  【月结货源方、达到结算条件】
     *       一、货源方达到支付条件
     *       二、支付
     *
     *   【月结车源方、达到支付条件】
     *       一、车源方达到支付条件
     *       二、财务拨付
     *
     *   【现结货源方、车主拒签】
     *     一、车源方交纳保证金
     *     二、现结货主发起协议
     *     三、车源方拒签
     *     四、财务退款
     *
     *   【月结货源方、正常执行】
     *       一、车源方交纳保证金
     *       二、货主发起协议
     *       三、车主签约
     *       四、
     */

    /**
     * 月结的货主发起协议/正常执行,货主确认
     * 操作码：cw020106
     */
    public function yjhz_make_deal($user_id=0,$deal_id,$money){
        $deal_info = $this->deal_table->where("id={$deal_id}")->field("hz_id,cz_id")->find();
        if(!$deal_info){
            return false;
        }
        //开启事物
        $this->fnrcd_table->startTrans();
        $data['rsn_code'] = 'cw020106';
        $data['deal_id'] = $deal_id;
        $data['mny_qty'] = $money;
        $data['addtime'] = time();
        //（物收）协议应收
        $data['subjects'] = 3;
        $data['user_id'] = $deal_info['hz_id'];
        $ws_result = $this->fnrcd_table->add($data);
        //（货付）协议应付
        $data['subjects'] = 8;
        $data['user_id'] = $deal_info['hz_id'];
        $hf_result = $this->fnrcd_table->add($data);
        //（物付）协议应付
        $data['user_id'] = $deal_info['cz_id'];
        $data['subjects'] = 15;
        $wf_result = $this->fnrcd_table->add($data);
        //（车收）协议应收
        $data['subjects'] = 19;
        $data['user_id'] = $deal_info['cz_id'];
        $cs_result = $this->fnrcd_table->add($data);

        if($ws_result && $hf_result && $wf_result && $cs_result){
            //操作成功
            $this->fnrcd_table->commit();
            return true;
        }else{
            //操作不成功
            $this->fnrcd_table->rollback();
            return false;
        }
    }

    /**
     * 月结货源方/正常流程，货源方运费达到支付条件
     * 操作代码：cw020111
     */
    public function yjhz_enough_pay($user_id=0,$deal_id,$money){
        #------------------------------------------------------------------------duxiangyang【添加】
        $deal_info = $this->deal_table->where(array('id'=>$deal_id))->field("hz_id,cz_id,goods_fare")->find();
        if(!$deal_info){
            return false;
        }
        #-----------------------------------------------------------------------------------------
        $data['rsn_code'] = 'cw020111';
        $data['deal_id'] = $deal_id;
        $data['addtime'] = time();
        $data['user_id'] = $deal_info['hz_id'];

        //（物收）协议应收
        $data['subjects'] = 3;
        $data['mny_qty'] = -$deal_info['goods_fare'];
        $result_yf = $this->fnrcd_table->add($data);
        //（货付）协议应付
        $data['subjects'] = 8;
        $data['mny_qty'] = -$deal_info['goods_fare'];
        $result_xyyf = $this->fnrcd_table->add($data);
        //（物收）平台应收
        $data['subjects'] = 11;
        $data['mny_qty'] = $deal_info['goods_fare'];
        $result_ptys = $this->fnrcd_table->add($data);
        //（货付）平台应付
        $data['subjects'] = 13;
        $data['mny_qty'] = $deal_info['goods_fare'];
        $result_ptyf = $this->fnrcd_table->add($data);
        if($result_yf && $result_ptyf && $result_ptys && $result_xyyf){
            //操作成功
            return true;
        }else{
            //操作不成功
            return false;
        }
    }

    /**
     * 月结货源方/正常流程，货源方支付运费
     * 操作代码：cw020112
     */
    public function yjhz_pay($user_id=0,$deal_id,$money){
        $data['deal_id'] = $deal_id;
        $data['rsn_code'] = 'cw020112';
        $data['addtime'] = time();
        $data['user_id'] = $user_id;

        //（物收）平台应收
        $data['subjects'] = 11;
        $data['mny_qty'] = -$money;
        $result_ptys = $this->fnrcd_table->add($data);
        //（货付）平台应付
        $data['subjects'] = 13;
        $data['mny_qty'] = -$money;
        $result_ptyf = $this->fnrcd_table->add($data);
        //（物收）平台已收
        $data['subjects'] = 12;
        $data['mny_qty'] = $money;
        $result_ys = $this->fnrcd_table->add($data);
        //（货付）平台已付
        $data['subjects'] = 14;
        $data['mny_qty'] = $money;
        $result_yf = $this->fnrcd_table->add($data);
        if($result_ptyf && $result_ptys && $result_yf && $result_ys){
            //操作成功
            return true;
        }else{
            //操作不成功
            return false;
        }
    }

    /**
     * 月结货源方/正常流程，车源方运费达到支付条件
     * 操作代码：cw020110
     */
    public function yjcz_enough_pay($user_id=0,$deal_id,$money){
        $deal_info = $this->deal_table->where("id={$deal_id}")->field("hz_id,cz_id")->find();
        if(!$deal_info){
            return false;
        }
        //开启事物
        //$this->fnrcd_table->startTrans();
        $data['rsn_code'] = 'cw020110';
        $data['deal_id'] = $deal_id;
        $data['addtime'] = time();

        //（物付）协议应付
        $data['subjects'] = 15;
        $data['mny_qty'] = -$money;
        $data['user_id'] = $deal_info['cz_id'];
        $result_yf = $this->fnrcd_table->add($data);
        //（车收）协议应收
        $data['subjects'] = 19;
        $data['user_id'] = $deal_info['cz_id'];
        $data['mny_qty'] = -$money;
        $result_xyys = $this->fnrcd_table->add($data);
        //（物付）平台应付
        $data['subjects'] = 17;
        $data['user_id'] = $deal_info['cz_id'];
        $data['mny_qty'] = $money;
        $result_ptyf = $this->fnrcd_table->add($data);
        //（车收）平台应收
        $data['subjects'] = 21;
        $data['user_id'] = $deal_info['cz_id'];
        $data['mny_qty'] = $money;
        $result_ptys = $this->fnrcd_table->add($data);
        if($result_yf && $result_ptyf && $result_ptys && $result_xyys){
            //操作成功
            //$this->fnrcd_table->commit();
            return true;
        }else{
            //操作不成功
            //$this->fnrcd_table->rollback();
            return false;
        }
    }

    /**
     * 月结货源方/正常流程，财务付款（车源方运费达到支付条件）
     * 操作代码：cw020109
     */
    public function yjcz_pay($user_id=0,$deal_id,$money){
        // 验证是否重复记账
        $fnrcd_rlt = $this->fnchk_repeat($deal_id,'cw020109');
        if($fnrcd_rlt){
            return false;
        }
        $deal_info = $this->deal_table->where("id={$deal_id}")->field("hz_id,cz_id")->find();
        if(!$deal_info){
            return false;
        }

        //开启事物
        //$this->fnrcd_table->startTrans();
        $data['deal_id'] = $deal_id;
        $data['rsn_code'] = 'cw020109';
        $data['addtime'] = time();

        //（物付）平台应付
        $data['subjects'] = 17;
        $data['mny_qty'] = -$money;
        $data['user_id'] = $deal_info['cz_id'];
        $result_ptyf = $this->fnrcd_table->add($data);
        //（车收）平台应收
        $data['subjects'] = 21;
        $data['mny_qty'] = -$money;
        $data['user_id'] = $deal_info['cz_id'];
        $result_ptys = $this->fnrcd_table->add($data);
        //（物付）平台已付
        $data['subjects'] = 18;
        $data['user_id'] = $deal_info['cz_id'];
        $data['mny_qty'] = $money;
        $result_yf = $this->fnrcd_table->add($data);
        //（车收）平台已收
        $data['subjects'] = 22;
        $data['user_id'] = $deal_info['cz_id'];
        $data['mny_qty'] = $money;
        $result_ys = $this->fnrcd_table->add($data);
        if($result_ptyf && $result_ptys && $result_yf && $result_ys){
            //操作成功
            //$this->fnrcd_table->commit();
            return true;
        }else{
            //操作不成功
            //$this->fnrcd_table->rollback();
            return false;
        }
    }

    /**
     * 月结货源方/车主拒签,车源方拒签
     * 操作代码：cw020204
     */
    public function yj_refuse_deal($user_id=0,$deal_id,$deposit_mny){
        $deal_info = $this->deal_table->where("id={$deal_id}")->field("hz_id,cz_id")->find();
        if(!$deal_info){
            return false;
        }
        //开启事物
        $this->fnrcd_table->startTrans();
        $data['rsn_code'] = 'cw020204';
        $data['deal_id'] = $deal_id;
        $data['addtime'] = time();
        //（物收）预收保证金
        $data['mny_qty'] = -$deposit_mny;
        $data['subjects'] = 23;
        $data['user_id'] = $deal_info['cz_id'];
        $result_ys = $this->fnrcd_table->add($data);
        //（车付）竞价保证金
        $data['mny_qty'] = -$deposit_mny;
        $data['user_id'] = $deal_info['cz_id'];
        $data['subjects'] = 25;
        $result_bid = $this->fnrcd_table->add($data);
        //（物收）已罚保证金
        $data['mny_qty'] = $deposit_mny;
        $data['user_id'] = $deal_info['cz_id'];
        $data['subjects'] = 24;
        $result_yf = $this->fnrcd_table->add($data);
        //（货收）已罚保证金
        $data['mny_qty'] = $deposit_mny;
        $data['user_id'] = $deal_info['cz_id'];
        $data['subjects'] = 26;
        $result_mny = $this->fnrcd_table->add($data);
        //（物付）应赔付保证金
        $data['mny_qty'] = $deposit_mny;
        $data['user_id'] = $deal_info['hz_id'];
        $data['subjects'] = 31;
        $result_yp = $this->fnrcd_table->add($data);
        //（货收）应赔付保证金
        $data['mny_qty'] = $deposit_mny;
        $data['user_id'] = $deal_info['hz_id'];
        $data['subjects'] = 33;
        $result_ypf = $this->fnrcd_table->add($data);
        if($result_bid && $result_yf && $result_yp && $result_ypf && $result_ys && $result_mny){
            //操作成功
            $this->fnrcd_table->commit();
            return true;
        }else{
            //操作不成功
            $this->fnrcd_table->rollback();
            return false;
        }
    }

    /**
     * 月结货源方/车主拒签,财务理赔
     * 操作代码：cw020209
     */
    public function yj_fnc_pay($user_id=0,$deal_id,$deposit_mny){
        $deal_info = $this->deal_table->where("id={$deal_id}")->field("hz_id,cz_id")->find();
        if(!$deal_info){
            return false;
        }

        $data['rsn_code'] = 'cw020209';
        $data['deal_id'] = $deal_id;
        $data['addtime'] = time();
        //（物付）应赔付保证金
        $data['subjects'] = 31;
        $data['mny_qty'] = -$deposit_mny;
        $data['user_id'] = $deal_info['hz_id'];
        $result_yp = $this->fnrcd_table->add($data);
        //（货收）应赔付保证金
        $data['subjects'] = 33;
        $data['mny_qty'] = -$deposit_mny;
        $data['user_id'] = $deal_info['hz_id'];
        $result_ypf = $this->fnrcd_table->add($data);
        //（物付）已赔付保证金
        $data['subjects'] = 32;
        $data['mny_qty'] = $deposit_mny;
        $data['user_id'] = $deal_info['hz_id'];
        $result_pb = $this->fnrcd_table->add($data);
        //（货收）已赔付保证金
        $data['subjects'] = 34;
        $data['user_id'] = $deal_info['hz_id'];
        $data['mny_qty'] = $deposit_mny;
        $result_ypb = $this->fnrcd_table->add($data);
        if($result_ypf && $result_yp && $result_pb && $result_ypb){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 月结货源方/车主未履约，客服核实 unarrive()
     * 操作代碼 cw020307
     * 货主投诉有两种: 1.车没去  2.车去了，产生纠纷
     */
    function yj_czua_kfcheck($user_id=0,$deal_id,$money,$deposit_mny){
        $deal_info = $this->deal_table->where("id={$deal_id}")->field("hz_id,cz_id")->find();
        if(!$deal_info){
            return false;
        }
        //开启事物
        $this->fnrcd_table->startTrans();

        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw020307";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物收)协议预收 【减少】
        $fn_data["subjects"] = "2";
        $fn_data["mny_qty"] = -$money;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $gpol = $this->fnrcd_table->add($fn_data);

        //(货付)协议预付 【减少】
        $fn_data["subjects"] = "7";
        $fn_data["user_id"] = $deal_info['hz_id'];
        $gptl = $this->fnrcd_table->add($fn_data);

        //(物付)应退担保  【增加】
        $fn_data["subjects"] = "27";
        $fn_data["user_id"] = $deal_info['hz_id'];
        $fn_data["mny_qty"] = $money;
        $gpop = $this->fnrcd_table->add($fn_data);

        //(货收)应退担保   【增加】
        $fn_data["subjects"] = "29";
        $fn_data["user_id"] = $deal_info['hz_id'];
        $gptp = $this->fnrcd_table->add($fn_data);

        //(物收)预收保证金  【减少】
        $fn_data["subjects"] = "23";
        $fn_data["mny_qty"] = -$deposit_mny;
        $fn_data["user_id"] = $deal_info['cz_id'];
        $bzjaa = $this->fnrcd_table->add($fn_data);

        //(车付)竞价保证金  【减少】
        $fn_data["subjects"] = "25";
        $fn_data["mny_qty"] = -$deposit_mny;
        $fn_data["user_id"] = $deal_info['cz_id'];
        $bzjab = $this->fnrcd_table->add($fn_data);

        //(物收)已罚保证金     【增加】
        $fn_data["subjects"] = "24";
        $fn_data["mny_qty"] = $deposit_mny;
        $fn_data["user_id"] = $deal_info['cz_id'];
        $bzjba = $this->fnrcd_table->add($fn_data);

        //(车付)已罚保证金   【增加】
        $fn_data["subjects"] = "26";
        $fn_data["mny_qty"] = $deposit_mny;
        $fn_data["user_id"] = $deal_info['cz_id'];
        $bzjbb = $this->fnrcd_table->add($fn_data);

        //(物付)应赔付保证金  【增加】
        $fn_data["subjects"] = "31";
        $fn_data["mny_qty"] = $deposit_mny;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $bzjca = $this->fnrcd_table->add($fn_data);

        //(货收)应赔付保证金 【增加】
        $fn_data["subjects"] = "33";
        $fn_data["mny_qty"] = 200;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $bzjcb = $this->fnrcd_table->add($fn_data);

        if($gpol && $gptl && $gpop && $gptp && $bzjaa && $bzjab && $bzjba && $bzjbb && $bzjca && $bzjca && $bzjcb){
            //操作成功
            $this->fnrcd_table->commit();
            return true;
        }else{
            //操作不成功
            $this->fnrcd_table->rollback();
            return false;
        }
    }

    /**
     * 月结货源方/车主未履约，财务退款
     * 操作代碼 cw020309
     */
    function yj_czua_fnrefund($user_id=0,$deal_id,$money){
        $deal_info = $this->deal_table->where("id={$deal_id}")->field("hz_id,cz_id")->find();
        if(!$deal_info){
            return false;
        }
        //开启事物
        $this->fnrcd_table->startTrans();

        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw020309";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物付)应赔付保证金  【减200】
        $fn_data["subjects"] = "31";
        $fn_data["user_id"] = $deal_info['hz_id'];
        $fn_data["mny_qty"] = -200;
        $bzjaa_r = $this->fnrcd_table->add($fn_data);

        //(货收)应赔付保证金  【减200】
        $fn_data["subjects"] = "33";
        $fn_data["mny_qty"] = -200;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $bzjab_r = $this->fnrcd_table->add($fn_data);

        //(物付)已赔付保证金  【加200】
        $fn_data["subjects"] = "32";
        $fn_data["user_id"] = $deal_info['hz_id'];
        $fn_data["mny_qty"] = 200;
        $bzjba_r = $this->fnrcd_table->add($fn_data);

        //(货收)已赔付保证金 【加200】
        $fn_data["subjects"] = "34";
        $fn_data["user_id"] = $deal_info['hz_id'];
        $fn_data["mny_qty"] = 200;
        $bzjbb_r = $this->fnrcd_table->add($fn_data);

        //(物付)应退担保 【减】
        $fn_data["subjects"] = "27";
        $fn_data["user_id"] = $deal_info['hz_id'];
        $fn_data["mny_qty"] = -$money;
        $gpol_r = $this->fnrcd_table->add($fn_data);

        //(货收)应退担保 【减】
        $fn_data["subjects"] = "29";
        $fn_data["user_id"] = $deal_info['hz_id'];
        $fn_data["mny_qty"] = -$money;
        $gptl_r = $this->fnrcd_table->add($fn_data);

        //(物付)已退担保 【加】
        $fn_data["subjects"] = "28";
        $fn_data["user_id"] = $deal_info['hz_id'];
        $fn_data["mny_qty"] = $money;
        $gpop_r = $this->fnrcd_table->add($fn_data);

        //(货收)已退担保 【减】
        $fn_data["subjects"] = "30";
        $fn_data["user_id"] = $deal_info['hz_id'];
        $fn_data["mny_qty"] = -$money;
        $gptp_r = $this->fnrcd_table->add($fn_data);

        if($bzjaa_r && $bzjab_r && $bzjba_r && $bzjbb_r && $gpol_r && $gptl_r && $gpop_r && $gptp_r){
            $this->fnrcd_table->commit();
            return true;
        }else{
            $this->fnrcd_table->rollback();
            return false;
        }
    }

    /**
     * 月结货源方/货主未履约，客服核实
     * 操作代碼 cw020407
     */
    function yj_hzunpms_kfcheck($user_id=0,$deal_id,$money){
        $deal_info = $this->deal_table->where("id={$deal_id}")->field("hz_id,cz_id")->find();
        if(!$deal_info){
            return false;
        }
        //开启事务
        $this->fnrcd_table->startTrans();

        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw020307";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物收)协议预收 【减】
        $fn_data["subjects"] = "2";
        $fn_data["mny_qty"] = -$money;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $gpol_r = $this->fnrcd_table->add($fn_data);

        //(货付)协议预付 【减】
        $fn_data["subjects"] = "7";
        $fn_data["mny_qty"] = -$money;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $gptl_r = $this->fnrcd_table->add($fn_data);

        //(物付)应退担保  【加】
        $fn_data["subjects"] = "27";
        $fn_data["mny_qty"] = -$money;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $gpop_r = $this->fnrcd_table->add($fn_data);

        //(货收)应退担保  【加】
        $fn_data["subjects"] = "29";
        $fn_data["user_id"] = $deal_info['hz_id'];
        $fn_data["mny_qty"] = -$money;
        $gptp_r = $this->fnrcd_table->add($fn_data);

        if($gpol_r && $gptl_r & $gpop_r && $gptp_r){
            //操作成功
            $this->fnrcd_table->commit();
            return true;
        }else{
            $this->fnrcd_table->rollback();
            return false;
        }
    }


    /**
     * 月结货源方/货主未履约，达成补偿
     * 操作代码cw020408 （Reach compensatory）达成赔偿  (Fail to Promise)未达成赔偿
     */
    function yj_hzunpms_rchcpsy($user_id=0,$deal_id,$money){
        $deal_info = $this->deal_table->where("id={$deal_id}")->field("hz_id,cz_id")->find();
        if(!$deal_info){
            return false;
        }
        //开启事务
        $this->fnrcd_table->startTrans();

        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw020408";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物收)应收补偿 【增加50】
        $fn_data["subjects"] = "43";
        $fn_data["mny_qty"] = 50;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $bcjaa_r = $this->fnrcd_table->add($fn_data);

        //(货付)应付补偿 【增加50】
        $fn_data["subjects"] = "45";
        $fn_data["mny_qty"] = 50;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $bcjab_r = $this->fnrcd_table->add($fn_data);

        //(物付)应付补偿 【增加50】
        $fn_data["subjects"] = "47";
        $fn_data["mny_qty"] = 50;
        $fn_data["user_id"] = $deal_info['cz_id'];
        $bcjba_r = $this->fnrcd_table->add($fn_data);

        //(车收)应收补偿
        $fn_data["subjects"] = "49";
        $fn_data["mny_qty"] = 50;
        $fn_data["user_id"] = $deal_info['cz_id'];
        $bcjbb_r = $this->fnrcd_table->add($fn_data);

        if($bcjaa_r && $bcjab_r && $bcjba_r && $bcjbb_r){
            //操作成功
            $this->fnrcd_table->commit();
            return true;
        }else{
            $this->fnrcd_table->rollback();
            return false;
        }
    }


    /**
     * 月结货源方/货主未履约，客服核实
     * 操作代碼 cw020407
     *
     * 月结货源方/货主未履约，达成补偿
     * 操作代码cw020408 （Reach compensatory）达成赔偿  (Fail to Promise)未达成赔偿
     */
    function yj_hzunpms_kfcheck_rchcpsy($user_id=0,$deal_id,$money){
        $deal_info = $this->deal_table->where("id={$deal_id}")->field("hz_id,cz_id")->find();
        if(!$deal_info){
            return false;
        }
        //开启事务
        $this->fnrcd_table->startTrans();

        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw020407";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物收)协议预收 【减】
        $fn_data["subjects"] = "2";
        $fn_data["mny_qty"] = -$money;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $gpol_r = $this->fnrcd_table->add($fn_data);

        //(货付)协议预付 【减】
        $fn_data["subjects"] = "7";
        $fn_data["mny_qty"] = -$money;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $gptl_r = $this->fnrcd_table->add($fn_data);

        //(物付)应退担保  【加】
        $fn_data["subjects"] = "27";
        $fn_data["mny_qty"] = $money;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $gpop_r = $this->fnrcd_table->add($fn_data);

        //(货收)应退担保  【加】
        $fn_data["subjects"] = "29";
        $fn_data["mny_qty"] = $money;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $gptp_r = $this->fnrcd_table->add($fn_data);

        //月结货主未履约达成赔偿
        $fn_data["rsn_code"] = "cw020408";


        //(物收)应收补偿 【增加50】
        $fn_data["subjects"] = "43";
        $fn_data["mny_qty"] = 50;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $bcjaa_r = $this->fnrcd_table->add($fn_data);

        //(货付)应付补偿 【增加50】
        $fn_data["subjects"] = "45";
        $fn_data["mny_qty"] = 50;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $bcjab_r = $this->fnrcd_table->add($fn_data);

        //(物付)应付补偿 【增加50】
        $fn_data["subjects"] = "47";
        $fn_data["mny_qty"] = 50;
        $fn_data["user_id"] = $deal_info['cz_id'];
        $bcjba_r = $this->fnrcd_table->add($fn_data);

        //(车收)应收补偿
        $fn_data["subjects"] = "49";
        $fn_data["mny_qty"] = 50;
        $fn_data["user_id"] = $deal_info['cz_id'];
        $bcjbb_r = $this->fnrcd_table->add($fn_data);

        if($gpol_r && $gptl_r & $gpop_r && $gptp_r && $bcjaa_r && $bcjab_r && $bcjba_r && $bcjbb_r){
            //操作成功
            $this->fnrcd_table->commit();
            return true;
        }else{
            $this->fnrcd_table->rollback();
            return false;
        }
    }


    /**
     * 月结货源方/货主未履约，财务退款
     * 操作代码cw020409
     */
    function yj_hzunpms_fnrefund($user_id=0,$deal_id,$money){
        $deal_info = $this->deal_table->where("id={$deal_id}")->field("hz_id,cz_id")->find();
        if(!$deal_info){
            return false;
        }
        //开启事务
        $this->fnrcd_table->startTrans();

        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw020409";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物付)应退担保 【减少】
        $fn_data["subjects"] = "27";
        $fn_data["mny_qty"] = -$money;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $gpol = $this->fnrcd_table->add($fn_data);

        //(货收)应退担保  【减少】
        $fn_data["subjects"] = "29";
        $fn_data["mny_qty"] = -$money;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $gptl = $this->fnrcd_table->add($fn_data);

        //(物付)已退担保  【增加】
        $fn_data["subjects"] = "28";
        $fn_data["mny_qty"] = $money;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $gpop = $this->fnrcd_table->add($fn_data);

        //(货收)已退担保  【增加】
        $fn_data["subjects"] = "30";
        $fn_data["mny_qty"] = $money;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $gptp = $this->fnrcd_table->add($fn_data);

        //(物收)已收补偿  【增加50】
        $fn_data["subjects"] = "44";
        $fn_data["mny_qty"] = 50;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $bcjaa = $this->fnrcd_table->add($fn_data);

        //(货付)已付补偿  【增加50】
        $fn_data["subjects"] = "46";
        $fn_data["mny_qty"] = 50;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $bcjab = $this->fnrcd_table->add($fn_data);

        //(物付)应付补偿  【减少50】
        $fn_data["subjects"] = "47";
        $fn_data["mny_qty"] = -50;
        $fn_data["user_id"] = $deal_info['cz_id'];
        $bcjba = $this->fnrcd_table->add($fn_data);

        //(车收)应收补偿  【减少50】
        $fn_data["subjects"] = "49";
        $fn_data["mny_qty"] = -50;
        $fn_data["user_id"] = $deal_info['cz_id'];
        $bcjbb = $this->fnrcd_table->add($fn_data);

        //(物付)已付补偿  【增加50】
        $fn_data["subjects"] = "48";
        $fn_data["mny_qty"] = 50;
        $fn_data["user_id"] = $deal_info['cz_id'];
        $bcjca = $this->fnrcd_table->add($fn_data);

        //(车收)已收补偿
        $fn_data["subjects"] = "50";
        $fn_data["mny_qty"] = 50;
        $fn_data["user_id"] = $deal_info['cz_id'];
        $bcjcb = $this->fnrcd_table->add($fn_data);

        if($gpol && $gptl && $gpop && $gptp && $bcjaa && $bcjab && $bcjba && $bcjbb && $bcjca && $bcjcb){
            //操作成功
            $this->fnrcd_table->commit();
            return true;
        }else{
            $this->fnrcd_table->rollback();
            return false;
        }
    }

    /**
     * 月结货源方/货主申诉，达成赔偿
     * 操作代码cw020508  (complaint 投诉) Reach compensatory）达成赔偿
     * $money 运费  $cpsy_mny 赔偿金额
     */
    function yj_hzcmplt_rchcpsy($user_id=0,$deal_id,$money,$cpsy_mny){
        $deal_info = $this->deal_table->where("id={$deal_id}")->field("hz_id,cz_id")->find();
        if(!$deal_info){
            return false;
        }
        //开启事务
        $this->fnrcd_table->startTrans();

        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw020508";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物收)协议预收  【减少】
        $fn_data["subjects"] = "2";
        $fn_data["mny_qty"] = -$money;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $gpol = $this->fnrcd_table->add($fn_data);

        //(货付)协议预付   【减少】
        $fn_data["subjects"] = "7";
        $gptl = $this->fnrcd_table->add($fn_data);

        //(物收)协议应收    【增加】
        $fn_data["subjects"] = "4";
        $fn_data["mny_qty"] = $money;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $gpopa = $this->fnrcd_table->add($fn_data);

        //(货付)协议应付    【增加】
        $fn_data["subjects"] = "9";
        $gptpa = $this->fnrcd_table->add($fn_data);

        //(物收)应收赔偿    【增加 $cpsy_mny】
        $fn_data["subjects"] = "35";
        $fn_data["mny_qty"] = $cpsy_mny;
        $fn_data["user_id"] = $deal_info['cz_id'];
        $pcjaa = $this->fnrcd_table->add($fn_data);

        //(车付)应付赔偿     【增加 $cpsy_mny】
        $fn_data["subjects"] = "37";
        $pcjab = $this->fnrcd_table->add($fn_data);

        //(物付)协议应付    【增加】
        $fn_data["subjects"] = "15";
        $fn_data["mny_qty"] = $money;
        $gpop = $this->fnrcd_table->add($fn_data);

        //(车收)协议应收    【增加】
        $fn_data["subjects"] = "19";
        $gptp = $this->fnrcd_table->add($fn_data);

        //(物付)应付赔偿      【增加 $cpsy_mny】
        $fn_data["subjects"] = "39";
        $fn_data["mny_qty"] = $cpsy_mny;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $pcjba = $this->fnrcd_table->add($fn_data);

        //(货收)应收赔偿      【增加 $cpsy_mny】
        $fn_data["subjects"] = "41";
        $fn_data["mny_qty"] = $cpsy_mny;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $pcjbb = $this->fnrcd_table->add($fn_data);

        if($gpol && $gptl && $gpop && $gptp && $gpopa && $gptpa && $pcjaa && $pcjab && $pcjba && $pcjbb){
            //操作成功
            $this->fnrcd_table->commit();
            return true;
        }else{
            $this->fnrcd_table->rollback();
            return false;
        }
    }

    /**
     * 月结货源方/货主投诉，财务退款
     * 操作代码cw020509 (complaint 投诉) Reach compensatory）达成赔偿
     * $money 运费 $cpsy_mny 赔偿金额
     */
    function yj_hzcmplt_fnrefund($user_id=0,$deal_id,$money,$cpsy_mny){
        $deal_info = $this->deal_table->where("id={$deal_id}")->field("hz_id,cz_id")->find();
        if(!$deal_info){
            return false;
        }
        //开启事务
        $this->fnrcd_table->startTrans();

        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw020509";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物收)协议应收  【减少】
        $fn_data["subjects"] = "3";
        $fn_data["mny_qty"] = -$money;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $gpol = $this->fnrcd_table->add($fn_data);

        //(货付)协议应付  【减少】
        $fn_data["subjects"] = "8";
        $fn_data["mny_qty"] = -$money;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $gptl = $this->fnrcd_table->add($fn_data);

        //(物收)协议已收  【增加】
        $fn_data["subjects"] = "4";
        $fn_data["mny_qty"] = $money;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $gpop = $this->fnrcd_table->add($fn_data);

        //(货付)协议已付   【增加】
        $fn_data["subjects"] = "9";
        $fn_data["mny_qty"] = $money;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $gptp = $this->fnrcd_table->add($fn_data);

        //(物收)应收赔偿   【减少$cpsy_mny】
        $fn_data["subjects"] = "35";
        $fn_data["mny_qty"] = -$cpsy_mny;
        $fn_data["user_id"] = $deal_info['cz_id'];
        $psjaa = $this->fnrcd_table->add($fn_data);

        //(车付)应付赔偿    【减少$cpsy+mn】
        $fn_data["subjects"] = "37";
        $fn_data["mny_qty"] = -$cpsy_mny;
        $fn_data["user_id"] = $deal_info['cz_id'];
        $psjab = $this->fnrcd_table->add($fn_data);

        //(物收)已收赔偿    【增加$cpsy_mny】
        $fn_data["subjects"] = "36";
        $fn_data["mny_qty"] = $cpsy_mny;
        $fn_data["user_id"] = $deal_info['cz_id'];
        $psjba = $this->fnrcd_table->add($fn_data);

        //(车付)已付赔偿    【增加$cpsy_mny】
        $fn_data["subjects"] = "38";
        $fn_data["mny_qty"] = $cpsy_mny;
        $fn_data["user_id"] = $deal_info['cz_id'];
        $psjbb = $this->fnrcd_table->add($fn_data);

        //(物付)应付赔偿    【减少$cpsy_mny】
        $fn_data["subjects"] = "39";
        $fn_data["mny_qty"] = -$cpsy_mny;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $psjca = $this->fnrcd_table->add($fn_data);

        //(货收)应收赔偿    【减少$cpsy_mny】
        $fn_data["subjects"] = "41";
        $fn_data["mny_qty"] = -$cpsy_mny;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $pcjcb = $this->fnrcd_table->add($fn_data);

        //(物付)已付赔偿     【增加$cpsy_mny】
        $fn_data["subjects"] = "40";
        $fn_data["mny_qty"] = $cpsy_mny;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $psjda = $this->fnrcd_table->add($fn_data);

        //(货收)已收赔偿   【增加$cpsy_mny】
        $fn_data["subjects"] = "42";
        $fn_data["mny_qty"] = $cpsy_mny;
        $fn_data["user_id"] = $deal_info['hz_id'];
        $psjdb = $this->fnrcd_table->add($fn_data);

        if($gpol && $gptl && $gpop && $gptp && $psjaa && $psjab && $psjba && $psjbb && $psjca && $pcjcb && $psjda && $psjdb){
            $this->fnrcd_table->commit();
            return true;
        }else{
            $this->fnrcd_table->rollback();
            return false;
        }
    }

    /**
     * 月结货源方/不限，支付预付费
     * 操作代码: cw020002
     */
    public function yf_hz_fnc($user_id,$deal_id=0,$money){
        $fn_data["user_id"] = $user_id;
        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw020002";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物收)平台预收  【增加 $prepaid_mny】
        $fn_data["subjects"] = "5";
        $fn_data["mny_qty"] = $money;
        $yfjo_r = $this->fnrcd_table->add($fn_data);

        //(货付)平台预付   【增加 $prepaid_mny】
        $fn_data["subjects"] = "10";
        $yfjt_r = $this->fnrcd_table->add($fn_data);

        if($yfjo_r && $yfjt_r){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Created by Mr.Chen.
     * 记账前检测 是否已经记账
     * @param string  $deal_id
     * @param string  $rsn_code
     */
    function fnchk_repeat($deal_id,$rsn_code){
        // 判断该账务是否记过，如果记过直接返回 false
        $where_fnrcd['deal_id'] = $deal_id;
        $where_fnrcd['rsn_code'] = $rsn_code;
        $fnrcd_arr = $this->fnrcd_table->where($where_fnrcd)->find();
        return $fnrcd_arr;
    }

}