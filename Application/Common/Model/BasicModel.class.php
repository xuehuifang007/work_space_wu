<?php
namespace Common\Model;
use Think\Model;

/**
 * Class BasicModel
 * @package Admin\Controller
 */
class BasicModel extends Model{
    protected $area_table;
    protected $appointment_table;
    protected $deal_table;
    protected $goods_table;
    protected $order_table;
    protected $gipush_table;
    protected $user_table;
    protected $change_eadrs_table;
    protected $change_deal_table;
    protected $history_eadrs_table;
    protected $history_deal_table;
    protected $eadrs_table;
    protected $adrs_table;
    protected $history_goods_table;
    protected $prorder_table;
    protected $id;
    protected $member_table;
    protected $defirend_table;
    protected $car_table;
    protected $dbgoods_table;
    //混合货源的表
    protected $mxgoods_table;
    //组陪货源的表
    protected $zpgoods_table;
    protected $zptmpgoods_table;
    public function _initialize(){
        $this->area_table = M(AREA_TABLE);
        $this->appointment_table = M(APPOINTMENT_TABLE);
        $this->deal_table = M(DEAL_TABLE);
        $this->goods_table = M(GOODS_TABLE);
        $this->order_table = M(ORDER_TABLE);
        $this->gipush_table = M(GIPUSH_TABLE);
        $this->user_table = M(USER_TABLE);
        $this->change_deal_table = M(CHANGE_DEAL_TABLE);
        $this->change_eadrs_table = M(CHANGE_EADRS_TABLE);
        $this->history_deal_table = M(HISTORY_DEAL_TABLE);
        $this->history_eadrs_table = M(HISTORY_EADRS_TABLE);
        $this->eadrs_table = M(EADRS_TABLE);
        $this->history_goods_table = M(HISTORY_GOODS_TABLE);
        $this->adrs_table = M(ADRS_TABLE);
        $this->prorder_table = M(PRORDER_TABLE);
        $this->id = $_SESSION['userData']['id'];
        $this->member_table = M(MEMBER_TABLE);
        $this->defirend_table = M(DEFIREND_TABLE);
        $this->car_table = M(CAR_TABLE);
        $this->dbgoods_table = M(DBGOODS_TABLE);
        $this->mxgoods_table=M(MXGOODS_TABLE);
        $this->zpgoods_table=M(ZPGOODS_TABLE);
        $this->zptmpgoods_table=M(ZPMXTMGOODS_TABLE);
    }
    /**
     * @param        $table_name
     * @param array  $where
     * @param        $num(值为空时不分页)
     * @param string $order(默认按ctime排序，值为no时不排序)注：如果表中没有ctime字段会出错
     *
     * @return mixed
     */
    public function basicList($table_name,$where=array(),$num='',$order=''){;
        if(empty($table_name))return false;
        //if(!isset($where['status']))$where['status']=array('neq','9');
        if($where['status']=='')$where['status']=array('neq','9');
        if(empty($order))$order='ctime desc';
        if($order=='no')$order='';
        //if(empty($num))$num=15;
        $obj=M($table_name);
        if(empty($num)){
            $list=$obj->where($where)->order($order)->select();
        }else{
            $count=$obj->where($where)->count();
            $page=new \Think\Page($count,$num);
            $list['list']=$obj->where($where)->order($order)->limit($page->firstRow,$page->listRows)->select();
            $list['page']=$page->show();
        }
        return $list;
    }

    /**
     * @param       $table_name
     * @param array $where
     *
     * @return bool
     */
    public function basicInfo($table_name,$where=array()){
        if(empty($table_name))return false;
        $obj=M($table_name);
        if(!isset($where['status']))$where['status']=array('neq','9');
        //if($where['status']=='')$where['status']=array('neq','9');
        return $obj->where($where)->find();
    }

    /**
     * @param       $table_name
     * @param array $where
     * @param array $data
     *
     * @return bool
     */
    public function basicSave($table_name,$where=array(),$data=array()){
        if(empty($table_name))return false;
        //if(!isset($where['status']))$where['status']=array('neq','9');
        //if($where['status']=='')$where['status'] = array('neq','9');
        $obj=M($table_name);

        $result = $obj->where($where)->save($data);
        echo $obj->getLastSql()."<br>";

        return $result;
    }

    /**
     * @param $table_name
     * @param $data
     *
     * @return bool
     */
    public function basicAdd($table_name,$data){
        if(empty($table_name))return false;
        if(empty($data))return false;
        $obj=M($table_name);
        return $obj->add($data);
    }

    /**删除方法
     * @param        $table_name
     * @param array  $where
     * @param string $type删除类型，默认修改status为9,如果为‘real’则彻底删除
     *
     * @return bool
     */
    public function basicDel($table_name,$where=array(),$type=''){
        if(empty($table_name))return false;
        $obj=M($table_name);
        if($type=='real'){
            return $obj->where($where)->delete();
        }else{
            return $obj->where($where)->save(array('status'=>9));
        }
    }

    //公共文件上传函数
    /**
     * @param savePath 存储路径
     * @return 返回上传之后的信息
     */
    public function basicUpload($savePath,$state=0){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =      $savePath; // 设置附件上传目录
        $upload->saveName = time().rand(10000,99999);
        if($state == 1){
            $upload->subName = array('date','ymd');
        }
        // 上传文件
        $info   =   $upload->upload();
        if(!$info) {
            // 上传错误提示错误信息
            $info=$upload->getError();
        }
        return $info;
    }

    /**
     * URL短地址生成函数
     * @param $longurl_str 要转换的长url,
     * 返回值为：一维数组 array('status'=>'true/false','tinyurl'=>'...')
     * status:true 生成成功，false 生成失败，tinyurl：为短url地址
     */
    function tinyurl_create($longurl_str){
        $ch = curl_init();
        $url_str = "http://dwz.cn/create.php";
        curl_setopt($ch,CURLOPT_URL,$url_str);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_ENCODING, 'gzip,deflate');
        $post_data=array('url'=>$longurl_str);
        curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($post_data));
        $curl_rlt = curl_exec($ch);
        curl_close($ch);
        $respo_arr = json_decode($curl_rlt,true);
        if($respo_arr['status'] == 0){
            $respo_arr['status'] = true;
        }else{
            $respo_arr['status'] = false;
        }
        return $respo_arr;
    }
}
