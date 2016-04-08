<?php
namespace Think;

class Finance{
    private  $fnrcd_table;
    private  $deal_table;
    private  $appeal_table;
    private  $fnoprdetail_table;
    function __construct(){
        $this->fnrcd_table = M("cshy_fnrcd",'tp_');
        $this->deal_table = M("cshy_deal",'tp_');
        $this->appeal_table = M('cshy_appeal','tp_');
        $this->fnoprdetail_table = M('cshy_fnoprdetail','tp_');
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
     *   【现结货源方_预付费、车源方未履约】
     *       一、交纳预付费
     *       二、车源方缴保证金
     *       三、现结货主、发起协议
     *       四、车源方签约
     *       五、货源方投诉（客服核实）
     *       六、财务退款
     *
     *   【月结货源方、正常执行】
     *       一、车源方交纳保证金
     *       二、货主发起协议
     *       三、车主签约
     *       四、货主确认
     *
     *    【月结货源方、车主拒签】
     *       一、车主交纳保证金
     *       二、车源方拒签
     *       三、财务理赔
     *
     *    【月结货源方、达到结算条件】
     *       一、货源方达到支付条件
     *       二、支付
     *
     *    【月结车源方、达到支付条件】
     *       一、车源方达到支付条件
     *       二、财务拨付
     *
     *     结算方式  00 不区分 01 现结 02 月结 03 预付
     *     业务类型  00 不区分 01 正常流程 02 车主拒签 03 车主未履约 04 货主未履约 05 货主投诉 06 预付
     *     业务操作  01 车源方缴纳保证金  （不区分结算/业务类型） 02 货主缴纳预付费 03 货主发起协议 04 车源方拒签 05 车主签署协议
     *              06 货主确认协议 07 客服核实 08 达成补偿 09 财务（退款/赔付/付款）10 货源达到支付条件 11 车源达到支付条件
     */


    /**
     * 现结货源方/正常流程，车源方缴纳保证金 (不限制月结现结，不限制业务模块)
     * 操作码：cw000001 （bail 保证金）
     */
    function xj_czpay_bail($user_id,$deal_id=0,$bail_mny){
        //开启事物
        $this->fnrcd_table->startTrans();
        $fn_data["user_id"] = $user_id;
        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw000001";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物收)预收保证金 【增加】
        $fn_data["subjects"] = "23";
        $fn_data["mny_qty"] = $bail_mny;
        $gpop_rlt = $this->fnrcd_table->add($fn_data);

        //(车付)竞价保证金 【增加】
        $fn_data["subjects"] = "25";
        $gptp_rlt = $this->fnrcd_table->add($fn_data);

        if($gpop_rlt && $gptp_rlt){
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
     * 现结货源方/正常流程，货主发起协议   (现结货源方，不限制业务类型)
     * cw010103
     */
    function xj_send_deal($user_id=0,$deal_id,$money){
        //开启事物
        $this->fnrcd_table->startTrans();
        $deal_arr = $this->deal_table->field("hz_id")->where(array("id"=>$deal_id))->find();
        $fn_data["user_id"] = $deal_arr["hz_id"];
        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw010103";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物收)预收担保
        $fn_data["subjects"] = "1";
        $fn_data["mny_qty"] = $money;
        $gpo_rlt = $this->fnrcd_table->add($fn_data);

        //(货付)预付担保
        $fn_data["subjects"] = "6";
        $gpt_rlt = $this->fnrcd_table->add($fn_data);

        if($gpo_rlt && $gpt_rlt){
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
     * 现结货源方/正常流程，车主签署协议  (现结货源方，不限制业务类型)
     * 操作代码：cw010005
     */
    function xj_sign_deal($user_id=0,$deal_id,$money){
        //开启事务
        $this->fnrcd_table->startTrans();
        $deal_arr = $this->deal_table->field("hz_id")->where(array("id"=>$deal_id))->find();
        $fn_data["user_id"] = $deal_arr["hz_id"];
        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw010105";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物收)预收担保 【减少】
        $fn_data["subjects"] = "1";
        $fn_data["mny_qty"] = -$money;
        $gpol_rlt = $this->fnrcd_table->add($fn_data);

        //(车收)预付担保 【减少】
        $fn_data["subjects"] = "6";
        $gptl_rlt = $this->fnrcd_table->add($fn_data);

        //(物收)协议预收 【增加】
        $fn_data["subjects"] = "2";
        $fn_data["mny_qty"] = $money;
        $gpop_rlt = $this->fnrcd_table->add($fn_data);

        //(货付)协议预付 【增加】
        $fn_data["subjects"] = "7";
        $gptp_rlt = $this->fnrcd_table->add($fn_data);

        if($gpol_rlt && $gptl_rlt && $gpop_rlt && $gptp_rlt){
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
     * 现结货源方/正常流程，货主确认协议
     * 操作代码：cw010106
     */
    function xj_confirm_deal($user_id=0,$deal_id,$money){
        //开启事物
        $this->fnrcd_table->startTrans();
        $deal_arr = $this->deal_table->field("cz_id,hz_id")->where(array("id"=>$deal_id))->find();
        $fn_data["user_id"] = $deal_arr["hz_id"];
        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw010106";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物收)协议预收  【减少】
        $fn_data["subjects"] = "2";
        $fn_data["mny_qty"] = -$money;
        $gpol_rlt = $this->fnrcd_table->add($fn_data);

        //(货付)协议预付  【减少】
        $fn_data["subjects"] = "7";
        $gptl_rlt = $this->fnrcd_table->add($fn_data);

        //(物收)协议已收  【增加】
        $fn_data["subjects"] = "4";
        $fn_data["mny_qty"] = $money;
        $gpop_rlt = $this->fnrcd_table->add($fn_data);

        //(货付)协议已付  【增加】
        $fn_data["subjects"] = "9";
        $gptp_rlt = $this->fnrcd_table->add($fn_data);

        //(物付)协议应付  【增加】
        $fn_data["user_id"] = $deal_arr["cz_id"];
        $fn_data["subjects"] = "15";
        $gpops_rlt = $this->fnrcd_table->add($fn_data);

        //(车收)协议应收  【增加】
        $fn_data["subjects"] = "19";
        $gptps_rlt = $this->fnrcd_table->add($fn_data);

        if($gpol_rlt && $gptl_rlt && $gpop_rlt && $gptp_rlt && $gpops_rlt && $gptps_rlt){
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
     * 现结货源方/正常流程，财务付款
     * 操作代码：cw010109
     */
    function xj_fnc_pay($user_id=0,$deal_id,$money){
        // 验证是否重复记账
        $fnrcd_rlt = $this->fnchk_repeat($deal_id,'cw010109');
        if($fnrcd_rlt){
            return false;
        }
        $deal_arr = $this->deal_table->field("cz_id")->where(array("id"=>$deal_id))->find();
        $fn_data["user_id"] = $deal_arr["cz_id"];
        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw010109";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物付)协议应付 【减少】
        $fn_data["subjects"] = "15";
        $fn_data["mny_qty"] = -$money;
        $gpop_result = $this->fnrcd_table->add($fn_data);

        //(物付)预付担保 【增加】
        $fn_data["subjects"] = "19";
        $gptp_result = $this->fnrcd_table->add($fn_data);

        //(物付)协议已付 【增加】
        $fn_data["subjects"] = "16";
        $fn_data["mny_qty"] = $money;
        $gpol_result = $this->fnrcd_table->add($fn_data);

        //(车收)协议应收 【减少】
        $fn_data["subjects"] = "20";
        $gptl_result = $this->fnrcd_table->add($fn_data);

        if($gpop_result && $gptp_result && $gpol_result && $gptl_result){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 现结货源方/车主拒签,车源方拒签
     * 操作代码：cw010204
     */
    function xj_czdeny_sign($user_id=0,$deal_id,$deposit_mny){
        //开启事务
        $this->fnrcd_table->startTrans();
        $deal_arr = $this->deal_table->field("cz_id,hz_id,goods_fare")->where(array("id"=>$deal_id))->find();
        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw010204";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物收)预收担保   【减少】
        $fn_data["user_id"] = $deal_arr["hz_id"];
        $fn_data["subjects"] = "1";
        $fn_data["mny_qty"] = -$deal_arr['goods_fare'];
        $gpol_r = $this->fnrcd_table->add($fn_data);

        //(货付)预付担保   【减少】
        $fn_data["subjects"] = "6";
        $gptl_r = $this->fnrcd_table->add($fn_data);

        //(物付)应退担保   【增加】
        $fn_data["subjects"] = "27";
        $fn_data["mny_qty"] = $deal_arr['goods_fare'];
        $gpop_r = $this->fnrcd_table->add($fn_data);

        //(货收)应退担保   【增加】
        $fn_data["subjects"] = "29";
        $gptp_r = $this->fnrcd_table->add($fn_data);

        //(物收)预收保证金  【减少】
        $fn_data["user_id"] = $deal_arr["cz_id"];
        $fn_data["subjects"] = "23";
        $fn_data["mny_qty"] = -$deposit_mny;
        $bzjaa_r = $this->fnrcd_table->add($fn_data);

        //(车付)竞价保证金  【减少】
        $fn_data["subjects"] = "25";
        $bzjab_r = $this->fnrcd_table->add($fn_data);

        //(物收)已罚保证金  【增加】
        $fn_data["subjects"] = "24";
        $fn_data["mny_qty"] = $deposit_mny;
        $bzjba_r = $this->fnrcd_table->add($fn_data);

        //(车付)已罚保证金  【增加】
        $fn_data["subjects"] = "26";
        $bzjbb_r = $this->fnrcd_table->add($fn_data);

        //(物付)应赔付保证金 【增加】
        $fn_data["user_id"] = $deal_arr["hz_id"];
        $fn_data["subjects"] = "31";
        $fn_data["mny_qty"] = $deposit_mny;
        $bzjca_r = $this->fnrcd_table->add($fn_data);

        //(货收)应赔付保证金 【增加】
        $fn_data["subjects"] = "33";
        $bzjcb_r = $this->fnrcd_table->add($fn_data);

        if($gpol_r && $gptl_r && $gpop_r && $gptp_r && $bzjaa_r && $bzjab_r && $bzjba_r && $bzjbb_r && $bzjca_r && $bzjcb_r){
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
     * 现结货源方/车主拒签，财务退款
     * 错误代码：cw010209
     */
    function xj_czdeny_fncpay($user_id=0,$deal_id,$deposit_mny){
        //开启事物
        $this->fnrcd_table->startTrans();
        $deal_arr = $this->deal_table->field("hz_id,goods_fare")->where(array("id"=>$deal_id))->find();
        $fn_data["user_id"] = $deal_arr["hz_id"];
        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw010209";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物付)应退担保  【减少】
        $fn_data["subjects"] = "27";
        $fn_data["mny_qty"] = -$deal_arr['goods_fare'];
        $gpol_r = $this->fnrcd_table->add($fn_data);

        //(货收)应付担保  【减少】
        $fn_data["subjects"] = "29";
        $gptl_r = $this->fnrcd_table->add($fn_data);

        //(物付)已退担保   【增加】
        $fn_data["subjects"] = "28";
        $fn_data["mny_qty"] = $deal_arr['goods_fare'];
        $gpop_r = $this->fnrcd_table->add($fn_data);

        //(货收)已退担保  【增加】
        $fn_data["subjects"] = "30";
        $gptp_r = $this->fnrcd_table->add($fn_data);

        //(物付)应赔付保证金 【减少】
        $fn_data["subjects"] = "31";
        $fn_data["mny_qty"] = -$deposit_mny;
        $bzjaa_r = $this->fnrcd_table->add($fn_data);

        //(货收)应赔付保证金 【减少】
        $fn_data["subjects"] = "33";
        $bzjab_r = $this->fnrcd_table->add($fn_data);

        //(物付)已赔付保证金  【增加】
        $fn_data["subjects"] = "32";
        $fn_data["mny_qty"] = $deposit_mny;
        $bzjba_r = $this->fnrcd_table->add($fn_data);

        //(货收)已赔付保证金  【增加】
        $fn_data["subjects"] = "34";
        $bzjbb_r = $this->fnrcd_table->add($fn_data);

        if($gpol_r && $gptl_r && $gpop_r && $gptp_r && $bzjaa_r && $bzjab_r && $bzjba_r && $bzjbb_r){
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
     * 现结货源方/车主未履约，客服核实 unarrive()
     * 操作代碼 cw010307
     */
    function xj_czua_kfcheck($user_id=0,$deal_id,$money,$deposit_mny){
        //开启事务
        $this->fnrcd_table->startTrans();
        $deal_arr = $this->deal_table->field("cz_id,hz_id")->where(array("id"=>$deal_id))->find();
        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw010307";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物收)协议预收 【减少】
        $fn_data["user_id"] = $deal_arr["hz_id"];
        $fn_data["mny_qty"] = -$money;
        $fn_data["subjects"] = "2";
        $gpol = $this->fnrcd_table->add($fn_data);

        //(货付)协议预付 【减少】
        $fn_data["subjects"] = "7";
        $gptl = $this->fnrcd_table->add($fn_data);

        //(物付)应退担保  【增加】
        $fn_data["subjects"] = "27";
        $fn_data["mny_qty"] = $money;
        $gpop = $this->fnrcd_table->add($fn_data);

        //(货收)应退担保   【增加】
        $fn_data["subjects"] = "29";
        $gptp = $this->fnrcd_table->add($fn_data);

        //(物收)预收保证金  【减少】
        $fn_data["user_id"] = $deal_arr["cz_id"];
        $fn_data["subjects"] = "23";
        $fn_data["mny_qty"] = -$deposit_mny;
        $bzjaa = $this->fnrcd_table->add($fn_data);

        //(车付)竞价保证金  【减少】
        $fn_data["subjects"] = "25";
        $fn_data["mny_qty"] = -$deposit_mny;
        $bzjab = $this->fnrcd_table->add($fn_data);

        //(物收)已罚保证金     【增加】
        $fn_data["subjects"] = "24";
        $fn_data["mny_qty"] = $deposit_mny;
        $bzjba = $this->fnrcd_table->add($fn_data);

        //(车付)已罚保证金   【增加】
        $fn_data["subjects"] = "26";
        $fn_data["mny_qty"] = $deposit_mny;
        $bzjbb = $this->fnrcd_table->add($fn_data);

        //(物付)应赔付保证金  【增加】
        $fn_data["user_id"] = $deal_arr["hz_id"];
        $fn_data["subjects"] = "31";
        $fn_data["mny_qty"] = $deposit_mny;
        $bzjca = $this->fnrcd_table->add($fn_data);

        //(货收)应赔付保证金 【增加】
        $fn_data["subjects"] = "33";
        $fn_data["mny_qty"] = $deposit_mny;
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
     * 现结货源方/车主未履约，财务退款
     * 操作代碼 cw010309
     */
    function xj_czua_fnrefund($user_id=0,$deal_id,$money){
        //开启事务
        //$this->fnrcd_table->startTrans();

        $deal_arr = $this->deal_table->field("hz_id")->where(array("id"=>$deal_id))->find();
        $fn_data["user_id"] = $deal_arr["hz_id"];
        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw010309";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物付)应赔付保证金  【减200】
        $fn_data["subjects"] = "31";
        $fn_data["mny_qty"] = -200;
        $bzjaa_r = $this->fnrcd_table->add($fn_data);

        //(货收)应赔付保证金  【减200】
        $fn_data["subjects"] = "33";
        $fn_data["mny_qty"] = -200;
        $bzjab_r = $this->fnrcd_table->add($fn_data);

        //(物付)已赔付保证金  【加200】
        $fn_data["subjects"] = "32";
        $fn_data["mny_qty"] = 200;
        $bzjba_r = $this->fnrcd_table->add($fn_data);

        //(货收)已赔付保证金 【加200】
        $fn_data["subjects"] = "34";
        $fn_data["mny_qty"] = 200;
        $bzjbb_r = $this->fnrcd_table->add($fn_data);

        //(物付)应退担保 【减】
        $fn_data["subjects"] = "27";
        $fn_data["mny_qty"] = -$money;
        $gpol_r = $this->fnrcd_table->add($fn_data);

        //(货收)应退担保 【减】
        $fn_data["subjects"] = "29";
        $fn_data["mny_qty"] = -$money;
        $gptl_r = $this->fnrcd_table->add($fn_data);

        //(物付)已退担保 【加】
        $fn_data["subjects"] = "28";
        $fn_data["mny_qty"] = $money;
        $gpop_r = $this->fnrcd_table->add($fn_data);

        //(货收)已退担保 【减】
        $fn_data["subjects"] = "30";
        $fn_data["mny_qty"] = $money;
        $gptp_r = $this->fnrcd_table->add($fn_data);

        if($bzjaa_r && $bzjab_r && $bzjba_r && $bzjbb_r && $gpol_r && $gptl_r && $gpop_r && $gptp_r){
            //$this->fnrcd_table->commit();
            return true;
        }else{
            //$this->fnrcd_table->rollback();
            return false;
        }
    }

    /**
     * 现结货源方/货主未履约，客服核实  (现结和预付)
     * 操作代碼 cw000407
     */
    function xj_hzunpms_kfcheck($user_id=0,$deal_id,$money){
        //开启事务
        $this->fnrcd_table->startTrans();
        $deal_arr = $this->deal_table->field("hz_id")->where(array("id"=>$deal_id))->find();
        $fn_data["user_id"] = $deal_arr["hz_id"];
        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw000407";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物收)协议预收 【减】
        $fn_data["subjects"] = "2";
        $fn_data["mny_qty"] = -$money;
        $gpol_r = $this->fnrcd_table->add($fn_data);

        //(货付)协议预付 【减】
        $fn_data["subjects"] = "7";
        $gptl_r = $this->fnrcd_table->add($fn_data);

        //(物付)应退担保  【加】
        $fn_data["subjects"] = "27";
        $fn_data["mny_qty"] = $money;
        $gpop_r = $this->fnrcd_table->add($fn_data);

        //(货收)应退担保  【加】
        $fn_data["subjects"] = "29";
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
     * 现结货源方/货主未履约，达成补偿   (现结和预付)
     * 操作代码cw000408 （Reach compensatory）达成赔偿  (Fail to Promise)未达成赔偿
     */
    function xj_hzunpms_rchcpsy($user_id=0,$deal_id,$money){
        //开启事务
        $this->fnrcd_table->startTrans();
        $deal_arr = $this->deal_table->field("cz_id,hz_id")->where(array("id"=>$deal_id))->find();
        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw000408";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物收)应收补偿 【增加50】
        $fn_data["user_id"] = $deal_arr["hz_id"];
        $fn_data["subjects"] = "43";
        $fn_data["mny_qty"] = 50;
        $bcjaa_r = $this->fnrcd_table->add($fn_data);

        //(货付)应付补偿 【增加50】
        $fn_data["subjects"] = "45";
        $bcjab_r = $this->fnrcd_table->add($fn_data);

        //(物付)应付补偿 【增加50】
        $fn_data["user_id"] = $deal_arr["cz_id"];
        $fn_data["subjects"] = "47";
        $fn_data["mny_qty"] = 50;
        $bcjba_r = $this->fnrcd_table->add($fn_data);

        //(车收)应收补偿
        $fn_data["subjects"] = "49";
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
     * 现结货源方/货主未履约，客服核实  (现结和预付)
     * 操作代碼 cw000407
     *
     * 现结货源方/货主未履约，达成补偿   (现结和预付)
     * 操作代码cw000408 （Reach compensatory）达成赔偿  (Fail to Promise)未达成赔偿
     */
    function xj_hzunpms_kfcheck_rchcpsy($user_id=0,$deal_id,$money){
        //开启事务
        $this->fnrcd_table->startTrans();
        $deal_arr = $this->deal_table->field("cz_id,hz_id")->where(array("id"=>$deal_id))->find();
        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw000407";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物收)协议预收 【减】
        $fn_data["user_id"] = $deal_arr["hz_id"];
        $fn_data["mny_qty"] = -$money;
        $fn_data["subjects"] = "2";
        $gpol_r = $this->fnrcd_table->add($fn_data);

        //(货付)协议预付 【减】
        $fn_data["subjects"] = "7";
        $gptl_r = $this->fnrcd_table->add($fn_data);

        //(物付)应退担保  【加】
        $fn_data["subjects"] = "27";
        $fn_data["mny_qty"] = $money;
        $gpop_r = $this->fnrcd_table->add($fn_data);

        //(货收)应退担保  【加】
        $fn_data["subjects"] = "29";
        $gptp_r = $this->fnrcd_table->add($fn_data);

        //达成赔偿操作
        $fn_data["rsn_code"] = "cw000408";

        //(物收)应收补偿 【增加50】
        $fn_data["user_id"] = $deal_arr["hz_id"];
        $fn_data["subjects"] = "43";
        $fn_data["mny_qty"] = 50;
        $bcjaa_r = $this->fnrcd_table->add($fn_data);

        //(货付)应付补偿 【增加50】
        $fn_data["subjects"] = "45";
        $fn_data["mny_qty"] = 50;
        $bcjab_r = $this->fnrcd_table->add($fn_data);

        //(物付)应付补偿 【增加50】
        $fn_data["user_id"] = $deal_arr["cz_id"];
        $fn_data["subjects"] = "47";
        $fn_data["mny_qty"] = 50;
        $bcjba_r = $this->fnrcd_table->add($fn_data);

        //(车收)应收补偿
        $fn_data["subjects"] = "49";
        $fn_data["mny_qty"] = 50;
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
     * 现结货源方/货主未履约，财务退款    (现结和预付)
     * 操作代码cw000409
     */
    function xj_hzunpms_fnrefund($user_id=0,$deal_id,$money){
        //开启事务
        //$this->fnrcd_table->startTrans();
        $deal_arr = $this->deal_table->field("hz_id,cz_id")->where(array("id"=>$deal_id))->find();
        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw000409";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物付)应退担保 【减少】
        $fn_data["user_id"] = $deal_arr["hz_id"];
        $fn_data["subjects"] = "27";
        $fn_data["mny_qty"] = -$money;
        $gpol = $this->fnrcd_table->add($fn_data);

        //(货收)应退担保  【减少】
        $fn_data["subjects"] = "29";
        $gptl = $this->fnrcd_table->add($fn_data);

        //(物付)已退担保  【增加】
        $fn_data["subjects"] = "28";
        $fn_data["mny_qty"] = $money;
        $gpop = $this->fnrcd_table->add($fn_data);

        //(货收)已退担保  【增加】
        $fn_data["subjects"] = "30";
        $gptp = $this->fnrcd_table->add($fn_data);

        //(物收)已收补偿  【增加50】
        $fn_data["subjects"] = "44";
        $fn_data["mny_qty"] = 50;
        $bcjaa = $this->fnrcd_table->add($fn_data);

        //(货付)已付补偿  【增加50】
        $fn_data["subjects"] = "46";
        $bcjab = $this->fnrcd_table->add($fn_data);

        //(物收)应收补偿  【减少50】
        $fn_data["subjects"] = "43";
        $fn_data["mny_qty"] = -50;
        $bcjaa = $this->fnrcd_table->add($fn_data);

        //(货付)应付补偿  【减少50】
        $fn_data["subjects"] = "45";
        $bcjab = $this->fnrcd_table->add($fn_data);


        //(物付)应付补偿  【减少50】
        $fn_data["user_id"] = $deal_arr["cz_id"];
        $fn_data["subjects"] = "47";
        $fn_data["mny_qty"] = -50;
        $bcjba = $this->fnrcd_table->add($fn_data);

        //(车收)应收补偿  【减少50】
        $fn_data["subjects"] = "49";
        $bcjbb = $this->fnrcd_table->add($fn_data);

        //(物付)已付补偿  【增加50】
        $fn_data["subjects"] = "48";
        $fn_data["mny_qty"] = 50;
        $bcjca = $this->fnrcd_table->add($fn_data);

        //(车收)已收补偿
        $fn_data["subjects"] = "50";
        $bcjcb = $this->fnrcd_table->add($fn_data);

        if($gpol && $gptl && $gpop && $gptp && $bcjaa && $bcjab && $bcjba && $bcjbb && $bcjca && $bcjcb){
            //操作成功
            //$this->fnrcd_table->commit();
            return true;
        }else{
            //$this->fnrcd_table->rollback();
            return false;
        }
    }

    /**
     * 现结货源方/货主申诉，达成赔偿    (现结和预付)
     * 操作代码cw000508  (complaint 投诉) Reach compensatory）达成赔偿
     * $money 运费  $cpsy_mny 赔偿金额
     */
    function xj_hzcmplt_rchcpsy($user_id=0,$deal_id,$money,$cpsy_mny){
        //开启事务
        $this->fnrcd_table->startTrans();
        $deal_arr = $this->deal_table->field("cz_id,hz_id")->where(array("id"=>$deal_id))->find();
        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw000508";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物收)协议预收  【减少】
        $fn_data["user_id"] = $deal_arr["hz_id"];
        $fn_data["subjects"] = "2";
        $fn_data["mny_qty"] = -$money;
        $gpol = $this->fnrcd_table->add($fn_data);

        //(货付)协议预付   【减少】
        $fn_data["subjects"] = "7";
        $gptl = $this->fnrcd_table->add($fn_data);

        //(物收)协议已收    【增加】
        $fn_data["subjects"] = "4";
        $fn_data["mny_qty"] = $money;
        $gpopa = $this->fnrcd_table->add($fn_data);

        //(货付)协议已付    【增加】
        $fn_data["subjects"] = "9";
        $gptpa = $this->fnrcd_table->add($fn_data);

        //(物收)应收赔偿    【增加 $cpsy_mny】
        $fn_data["user_id"] = $deal_arr["cz_id"];
        $fn_data["mny_qty"] = $cpsy_mny;
        $fn_data["subjects"] = "35";
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
        $fn_data["user_id"] = $deal_arr["hz_id"];
        $fn_data["mny_qty"] = $cpsy_mny;
        $fn_data["subjects"] = "39";
        $pcjba = $this->fnrcd_table->add($fn_data);

        //(货收)应收赔偿      【增加 $cpsy_mny】
        $fn_data["subjects"] = "41";
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
     * 现结货源方/货主投诉，财务退款   (现结和预付)
     * 操作代码cw000509 (complaint 投诉) Reach compensatory）达成赔偿
     * $money 运费 $cpsy_mny 赔偿金额
     */
    function xj_hzcmplt_fnrefund($user_id=0,$deal_id,$money=0,$cpsy_mny){
        //开启事务
        //$this->fnrcd_table->startTrans();
        $deal_arr = $this->deal_table->field("cz_id,hz_id,goods_fare")->where(array("id"=>$deal_id))->find();
        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw000509";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物收)协议应收  【减少】
        $fn_data["user_id"] = $deal_arr["cz_id"];
        $fn_data["subjects"] = "15";
        $fn_data["mny_qty"] = -$money;
        $gpol = $this->fnrcd_table->add($fn_data);

        //(货付)协议应付  【减少】
        $fn_data["subjects"] = "19";
        $gptl = $this->fnrcd_table->add($fn_data);

        //(物收)协议已收  【增加】
        $fn_data["subjects"] = "16";
        $fn_data["mny_qty"] = $money;
        $gpop = $this->fnrcd_table->add($fn_data);

        //(货付)协议已付   【增加】
        $fn_data["subjects"] = "20";
        $gptp = $this->fnrcd_table->add($fn_data);

        //(物收)应收赔偿   【减少$cpsy_mny】
        $fn_data["user_id"] = $deal_arr["cz_id"];
        $fn_data["mny_qty"] = -$cpsy_mny;
        $fn_data["subjects"] = "35";
        $psjaa = $this->fnrcd_table->add($fn_data);

        //(车付)应付赔偿    【减少$cpsy+mn】
        $fn_data["subjects"] = "37";
        $psjab = $this->fnrcd_table->add($fn_data);

        //(物收)已收赔偿    【增加$cpsy_mny】
        $fn_data["subjects"] = "36";
        $fn_data["mny_qty"] = $cpsy_mny;
        $psjba = $this->fnrcd_table->add($fn_data);

        //(车付)已付赔偿    【增加$cpsy_mny】
        $fn_data["subjects"] = "38";
        $psjbb = $this->fnrcd_table->add($fn_data);

        //(物付)应付赔偿    【减少$cpsy_mny】
        $fn_data["user_id"] = $deal_arr["hz_id"];
        $fn_data["mny_qty"] = -$cpsy_mny;
        $fn_data["subjects"] = "39";
        $psjca = $this->fnrcd_table->add($fn_data);

        //(货收)应收赔偿    【减少$cpsy_mny】
        $fn_data["subjects"] = "41";
        $pcjcb = $this->fnrcd_table->add($fn_data);

        //(物付)已付赔偿     【增加$cpsy_mny】
        $fn_data["subjects"] = "40";
        $fn_data["mny_qty"] = $cpsy_mny;
        $psjda = $this->fnrcd_table->add($fn_data);

        //(货收)已收赔偿   【增加$cpsy_mny】
        $fn_data["subjects"] = "42";
        $psjdb = $this->fnrcd_table->add($fn_data);

        if($gpol && $gptl && $gpop && $gptp && $psjaa && $psjab && $psjba && $psjbb && $psjca && $pcjcb && $psjda && $psjdb){
            //$this->fnrcd_table->commit();
            return true;
        }else{
            //$this->fnrcd_table->rollback();
            return false;
        }
    }


    /**
     * -----以下结算方式为：预付-----
     */

    /**
     * 预付货源方/正常流程，货主缴纳预付费 (不限业务模块)
     * 操作码cw030102
     */

    function yf_hzpay_prepaid($user_id=0,$deal_id,$prepaid_mny){
        //开启事务
        $this->fnrcd_table->startTrans();
        $deal_arr = $this->deal_table->field("hz_id")->where(array("id"=>$deal_id))->find();
        $fn_data["user_id"] = $deal_arr["hz_id"];
        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw030102";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物收)平台预收  【增加 $prepaid_mny】
        $fn_data["subjects"] = "5";
        $fn_data["mny_qty"] = $prepaid_mny;
        $yfjo_r = $this->fnrcd_table->add($fn_data);

        //(货付)平台预付   【增加 $prepaid_mny】
        $fn_data["subjects"] = "10";
        $yfjt_r = $this->fnrcd_table->add($fn_data);

        if($yfjo_r && $yfjt_r){
            $this->fnrcd_table->commit();
            return true;
        }else{
            $this->fnrcd_table->rollback();
            return false;
        }
    }

    /**
     * 预约货源方/正常流程，车主签约
     * 操作代码cw030105
     */
    function yf_sign_deal($user_id=0,$deal_id,$money){
        //开启事务
        $this->fnrcd_table->startTrans();
        $deal_arr = $this->deal_table->field("hz_id")->where(array("id"=>$deal_id))->find();
        $fn_data["user_id"] = $deal_arr["hz_id"];
        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw030105";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物收)协议预收 【增加】
        $fn_data["subjects"] = "2";
        $fn_data["mny_qty"] = $money;
        $gpop_r = $this->fnrcd_table->add($fn_data);

        //(货付)协议预付  【增加】
        $fn_data["subjects"] = "7";
        $gptp_r = $this->fnrcd_table->add($fn_data);

        //(物收)平台预收  【减少】
        $fn_data["subjects"] = "5";
        $fn_data["mny_qty"] = -$money;
        $gpol_r = $this->fnrcd_table->add($fn_data);

        //(货付)平台预付  【减少】
        $fn_data["subjects"] = "10";
        $gptl_r = $this->fnrcd_table->add($fn_data);

        if($gpop_r && $gptp_r && $gpol_r && $gptl_r){
            $this->fnrcd_table->commit();
            return true;
        }else{
            $this->fnrcd_table->rollback();
            return false;
        }
    }

    /**
     * 预付货源方/正常流程，货主确认
     * 操作码cw030106
     */
    function yf_confirm_deal($user_id=0,$deal_id,$money){
        //开启事物
        $this->fnrcd_table->startTrans();
        $deal_arr = $this->deal_table->field("cz_id,hz_id")->where(array("id"=>$deal_id))->find();
        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw030106";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物收)协议预收  【减少】
        $fn_data["user_id"] = $deal_arr["hz_id"];
        $fn_data["subjects"] = "2";
        $fn_data["mny_qty"] = -$money;
        $gpol_rlt = $this->fnrcd_table->add($fn_data);

        //(货付)协议预付  【减少】
        $fn_data["subjects"] = "7";
        $gptl_rlt = $this->fnrcd_table->add($fn_data);

        //(物收)协议已收  【增加】
        $fn_data["subjects"] = "4";
        $fn_data["mny_qty"] = $money;
        $gpop_rlt = $this->fnrcd_table->add($fn_data);

        //(货付)协议已付  【增加】
        $fn_data["subjects"] = "9";
        $gptp_rlt = $this->fnrcd_table->add($fn_data);

        //(物付)协议应付  【增加】
        $fn_data["user_id"] = $deal_arr["cz_id"];
        $fn_data["subjects"] = "15";
        $gpops_rlt = $this->fnrcd_table->add($fn_data);

        //(车收)协议应收  【增加】
        $fn_data["subjects"] = "19";
        $gptps_rlt = $this->fnrcd_table->add($fn_data);

        if($gpol_rlt && $gptl_rlt && $gpop_rlt && $gptp_rlt && $gpops_rlt && $gptps_rlt){
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
     * 预付货源方/正常流程,财务付款
     * 操作码cw030109
     */
    function yf_fnc_pay($user_id=0,$deal_id,$money){
        // 验证是否重复记账
        $fnrcd_rlt = $this->fnchk_repeat($deal_id,'cw030109');
        if($fnrcd_rlt){
            return false;
        }

        $deal_arr = $this->deal_table->field("cz_id")->where(array("id"=>$deal_id))->find();
        $fn_data["user_id"] = $deal_arr["cz_id"];
        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw030109";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物付)协议应付 【增加】
        $fn_data["subjects"] = "15";
        $fn_data["mny_qty"] = $money;
        $gpop_result = $this->fnrcd_table->add($fn_data);

        //(物付)预付担保 【增加】
        $fn_data["subjects"] = "19";
        $gptp_result = $this->fnrcd_table->add($fn_data);

        //(物付)协议已付 【减少】
        $fn_data["subjects"] = "16";
        $fn_data["mny_qty"] = -$money;
        $gpol_result = $this->fnrcd_table->add($fn_data);

        //(车收)协议应收 【减少】
        $fn_data["subjects"] = "20";
        $gptl_result = $this->fnrcd_table->add($fn_data);

        if($gpop_result && $gptp_result && $gpol_result && $gptl_result){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 预付货源方/车主拒签，车源方拒签
     * 操作代码：cw030204
     */
    function yf_czdeny_sign($user_id=0,$deal_id,$deposit_mny){
        //开启事务
        $this->fnrcd_table->startTrans();
        $deal_arr = $this->deal_table->field("cz_id,hz_id,goods_fare")->where(array("id"=>$deal_id))->find();
        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw030204";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物收)预收保证金  【减少】
        $fn_data["user_id"] = $deal_arr["cz_id"];
        $fn_data["mny_qty"] = -$deposit_mny;
        $fn_data["subjects"] = "23";
        $bzjaa_r = $this->fnrcd_table->add($fn_data);

        //(车付)竞价保证金  【减少】
        $fn_data["subjects"] = "25";
        $bzjab_r = $this->fnrcd_table->add($fn_data);

        //(物收)已罚保证金  【增加】
        $fn_data["subjects"] = "24";
        $fn_data["mny_qty"] = $deposit_mny;
        $bzjba_r = $this->fnrcd_table->add($fn_data);

        //(车付)已罚保证金  【增加】
        $fn_data["subjects"] = "26";
        $bzjbb_r = $this->fnrcd_table->add($fn_data);

        //(物付)应赔付保证金 【增加】
        $fn_data["user_id"] = $deal_arr["hz_id"];
        $fn_data["mny_qty"] = $deposit_mny;
        $fn_data["subjects"] = "31";
        $bzjca_r = $this->fnrcd_table->add($fn_data);

        //(货收)应赔付保证金 【增加】
        $fn_data["subjects"] = "33";
        $bzjcb_r = $this->fnrcd_table->add($fn_data);

        if($bzjaa_r && $bzjab_r && $bzjba_r && $bzjbb_r && $bzjca_r && $bzjcb_r){
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
     * 预付货源方/车主拒签，财务退款
     * 操作代码：cw030209
     */
    function yf_czdeny_fncpay($user_id=0,$deal_id,$deposit_mny){
        //开启事物
        $this->fnrcd_table->startTrans();
        $deal_arr = $this->deal_table->field("hz_id")->where(array("id"=>$deal_id))->find();
        $fn_data["user_id"] = $deal_arr["hz_id"];
        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw030209";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物付)应赔付保证金 【减少】
        $fn_data["subjects"] = "31";
        $fn_data["mny_qty"] = -$deposit_mny;
        $bzjaa_r = $this->fnrcd_table->add($fn_data);

        //(货收)应赔付保证金 【减少】
        $fn_data["subjects"] = "33";
        $bzjab_r = $this->fnrcd_table->add($fn_data);

        //(物付)已赔付保证金  【增加】
        $fn_data["subjects"] = "32";
        $fn_data["mny_qty"] = $deposit_mny;
        $bzjba_r = $this->fnrcd_table->add($fn_data);

        //(货收)已赔付保证金  【增加】
        $fn_data["subjects"] = "34";
        $bzjbb_r = $this->fnrcd_table->add($fn_data);

        if($bzjaa_r && $bzjab_r && $bzjba_r && $bzjbb_r){
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
     * 预付货源方/车主未履约，客服核实  unarrive()
     * 操作代码 cw030307
     */
    function yf_czua_kfcheck($user_id=0,$deal_id,$money,$deposit_mny){
        //开启事务
        $this->fnrcd_table->startTrans();
        $deal_arr = $this->deal_table->field("cz_id,hz_id")->where(array("id"=>$deal_id))->find();
        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw030307";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物收)协议预收 【减少】
        $fn_data["user_id"] = $deal_arr["hz_id"];
        $fn_data["subjects"] = "2";
        $fn_data["mny_qty"]  = -$money;
        $gpol = $this->fnrcd_table->add($fn_data);

        //(货付)协议预付 【减少】
        $fn_data["subjects"] = "7";
        $gptl = $this->fnrcd_table->add($fn_data);

        //(物收)平台预收  【增加】
        $fn_data["subjects"] = "5";
        $fn_data["mny_qty"] = $money;
        $gpop = $this->fnrcd_table->add($fn_data);

        //(货付)平台预付  【增加】
        $fn_data["subjects"] = "10";
        $gptp = $this->fnrcd_table->add($fn_data);

        //(物收)预收保证金  【减少】
        $fn_data["user_id"] = $deal_arr["cz_id"];
        $fn_data["mny_qty"] = -$deposit_mny;
        $fn_data["subjects"] = "23";
        $bzjaa = $this->fnrcd_table->add($fn_data);

        //(车付)竞价保证金  【减少】
        $fn_data["subjects"] = "25";
        $bzjab = $this->fnrcd_table->add($fn_data);

        //(物收)已罚保证金     【增加】
        $fn_data["subjects"] = "24";
        $fn_data["mny_qty"] = $deposit_mny;
        $bzjba = $this->fnrcd_table->add($fn_data);

        //(车付)已罚保证金   【增加】
        $fn_data["subjects"] = "26";
        $bzjbb = $this->fnrcd_table->add($fn_data);

        //(物付)应赔付保证金  【增加】
        $fn_data["user_id"] = $deal_arr["hz_id"];
        $fn_data["mny_qty"] = $deposit_mny;
        $fn_data["subjects"] = "31";
        $bzjca = $this->fnrcd_table->add($fn_data);

        //(货收)应赔付保证金 【增加】
        $fn_data["subjects"] = "33";
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
     * 预付货源方/车主未履约，财务退款
     * 操作代碼 cw030309
     */
    function yf_czua_fnrefund($user_id=0,$deal_id,$money){
        //开启事务
        //$this->fnrcd_table->startTrans();
        $deal_arr = $this->deal_table->field("hz_id")->where(array("id"=>$deal_id))->find();
        $fn_data["user_id"] = $deal_arr["hz_id"];
        $fn_data["deal_id"] = $deal_id;
        $fn_data["rsn_code"] = "cw030309";
        $fn_data["addtime"] = time();
        $fn_data["status"] = "1";

        //(物付)应赔付保证金  【减200】
        $fn_data["subjects"] = "31";
        $fn_data["mny_qty"] = -200;
        $bzjaa_r = $this->fnrcd_table->add($fn_data);

        //(货收)应赔付保证金  【减200】
        $fn_data["subjects"] = "33";
        $bzjab_r = $this->fnrcd_table->add($fn_data);

        //(物付)已赔付保证金  【加200】
        $fn_data["subjects"] = "32";
        $fn_data["mny_qty"] = 200;
        $bzjba_r = $this->fnrcd_table->add($fn_data);

        //(货收)已赔付保证金 【加200】
        $fn_data["subjects"] = "34";
        $bzjbb_r = $this->fnrcd_table->add($fn_data);

        if($bzjaa_r && $bzjab_r && $bzjba_r && $bzjbb_r){
            //$this->fnrcd_table->commit();
            return true;
        }else{
            //$this->fnrcd_table->rollback();
            return false;
        }
    }


    /**
     * 预付货源方货主未履约、货主投诉与现结货源方的一样 【新增加方法2015/7/7】
     * @$opcode 操作码
     * @$deal_id 协议ID
     * 返回值 true 发送成功,false 发送失败
     */

    //月结货主、代收款记账通用方法
    function fn_cwmny_option($opcode,$deal_id){
            //开启事物
            //$this->fnrcd_table->startTrans();
            //查出要执行的操作
            $where_fnoprdetail['rsncode'] = $opcode;
            $fnopr_arr = $this->fnoprdetail_table->where($where_fnoprdetail)->select();
            //查找协议表对应的数据
            $where_deal['id'] = $deal_id;
            $deal_arr = $this->deal_table->field('cz_id,hz_id,goods_fare,order_mny,clcn_mny')->where($where_deal)->find();
            //补充财务记录数据
            $fn_data['deal_id'] = $deal_id;
            $fn_data['rsn_code'] = $opcode;
            $fn_data['addtime'] = time();
            $fn_data['status'] = '1';

            $fn_rlt = false;
        foreach($fnopr_arr as $key=>$val){
                $fn_data['subjects'] = $val['subjects'];
            //计算交易金额
            if($val['mny_class'] == '1'){
                $fn_data['mny_qty'] = $val['mny_sign'] * $val['mny_value'];
            }elseif($val['mny_class'] == '2'){
                $table_field = $val['table_field'];
                switch($val['table_name']){
                    case 'cshy_deal':
                        $fn_data['mny_qty'] =  $val['mny_sign'] * $deal_arr[$table_field];
                        break;
                    case 'cshy_appeal':
                        $appeal_arr = $this->appeal_table->field($table_field)->where(array('deal_id'=>$deal_id))->find();
                        $fn_data['mny_qty'] =  $val['mny_sign'] * $appeal_arr[$table_field];
                        break;
                    default:
                        break;
                }

            }
            //判断操作方
            if($val['user_class'] == '1'){
                $fn_data['user_id'] = $deal_arr['cz_id'];
            }elseif($val['user_class'] == '2'){
                $fn_data['user_id'] = $deal_arr['hz_id'];
            }
            //执行添加操作
            $fn_rlt = $this->fnrcd_table->add($fn_data);
            //echo $this->fnrcd_table->getLastSql()."<br>";
            if($fn_rlt == false){
                break;
            }
        }

        return $fn_rlt;
      /* if($fn_rlt == true){
            $this->fnrcd_table->commit();
            return true;
        }else{
            $this->fnrcd_table->rollback();
            return false;
        }*/
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