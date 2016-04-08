<?php
/**
 * @auther baiwei
 * 上传目录的全局变量
 */
//系统目录分割符
define('DS', '/');
//公共路径
define("PUBLIC_PATH",DS."Public".DS);
//city上传路径
define("UPLOAD_PATH","./Public/Uploads/");
//农行支付插件路径
define('ABC_PATH',VENDOR_PATH.'abcBank/');
//货物图片读取路径
define('GOODS_PHOTO_PATH',CITY_URL.DS.'Public'.DS.'Uploads'.DS.'goodsPic');
//货源图片上传路径
define('GOODS_PHOTO_UP_PATH',UPLOAD_PATH.'goodsPic'.DS);
//头像上传路径
define('HEADER_UPLOADS','../api/publicIndex/upData/images/');
//读取用户头像路径
define('HEADIMAGE_PATH','publicIndex'.DS.'upData'.DS.'images');
//用户默认头像
define('HEADIMAGE_DEFAULT_PATH',DS.'headDefault.jpg');
//维修联盟域名
define('WEIXIU_DOMAIN','http://www.xiuli.5656111.com');
//App活动图片路径
define('HD_PHOTO_PATH',PUBLIC_PATH.'Img'.DS.'Hd'.DS);
//App活动一级图片路径
define('HD__FIRST_PHOTO_PATH',HD_PHOTO_PATH.'first_level'.DS);
//App活动二级图片路径
define('HD__SECOND_PHOTO_PATH',HD_PHOTO_PATH.'second_level'.DS);
//司机上传图片路径
define('DRIVER_UP_PIC_PATH',UPLOAD_PATH.'driver_up'.DS);

/*信用额度 */
define("CREDIT",100000);
/*信用超限限制*/
define('OVERRUN',10000);

/* 自定义字段 */
define('CZ_TYPE','1,3');
define('HZ_TYPE','2,3');

/*表命名的文件*/
define("ADRS_TABLE",'cshy_adrs');
define("APPEAL_TABLE",'cshy_appeal');
define('AREA_TABLE',"cshy_area_tianjin");
define('APPOINTMENT_TABLE',"cshy_appointment");
define('CAR_TABLE',"cshy_car");
define("CAR_TYPE_TABLE","cshy_car_type");
define("CITY_TABLE",'cshy_city');
define("CHANGE_DEAL_TABLE",'cshy_changedeal');
define("CHANGE_EADRS_TABLE",'cshy_changeeadrs');
define("CZAPPRAISE_TABLE",'cshy_czappraise');
define("DAPUSH_TABLE",'cshy_dapush');
define('DBGOODS_TABLE','cshy_dbgoods');
define('DEAL_TABLE',"cshy_deal");
define('DEPOTADMIN_TABLE',"cshy_depotadmin");
define('DEALRMD_TABLE',"cshy_dealrmd");
define("DRIVER_TABLE",'cshy_driver');
define("EADRS_TABLE",'cshy_eadrs');
define("FNRCD_TABLE",'cshy_fnrcd');
define("GIPUSH_TABLE","cshy_gipush");
define('GOODS_TABLE',"cshy_goods");
define('GOODS_TIME_TABLE',"goods_time");
define("HISTORY_DEAL_TABLE",'cshy_historydeal');
define("HISTORY_EADRS_TABLE",'cshy_historyeadrs');
define("HISTORY_GOODS_TABLE",'cshy_historygoods');
define("MEMBER_TABLE",'member');
define("MXGOODS_TABLE",'cshy_zpmxgoods');
define("OPERATION_TABLE",'operation');
define('ORDER_TABLE','cshy_order');
define("PRORDER_TABLE","cshy_prorder");
define("PUSH_TABLE","city_push");
define("PUSH_MT_TABLE",'city_push_mt');
define("RECHARGE_TABLE",'cshy_recharge');
define('UNLOAD_TABLE','cshy_unload');
define('USER_TABLE',"cshy_user");
define('ZPGOODS_TABLE',"cshy_zpgoods");
define('BANK_TABLE',"cshy_bank");
define('DEFIREND_TABLE',"cshy_defirend");
define('ZHADMIN_TABLE',"service_admin");
define('ZPTMGOODS_TABLE',"cshy_zptmgoods");
define('ZPMXTMGOODS_TABLE',"cshy_zpmxtmgoods");
define('USERBILL_TABLE',"cshy_userbill");
define('DOUBLEBID_TABLE',"cshy_doublebid");
define('APP_VERSION_TABLE',"cshy_version");
define('AREA_VERSION_TABLE',"cshy_area_version");
define('APP_HD_TABLE',"cshy_app_hd");
define('APP_TOKEN_TABLE',"mobile_token");
define('HZPAYLOG_TABLE','cshy_hzpaylog');

/* 财务相关 */
//车主提现
define('CZTXZD_TABLE','m_cztxzd');
//车主收支明细视图(前缀tv_)
define('CZ_SZVIEW','m_czszmx');
//车主已付款明细视图(前缀tv_)
define('CZ_YE_DETAIL','m_czyfyfk');
//车主当天可提现最大金额
define('DAY_TOTAL_MONEY',5000);
define('CZYFTMX_VIEW',"m_czyftxmx");

/* 组合货源发布相关 */
//后台账号与货主、专线、物流管关系视图(前缀tv_)
define('ADMIN_HZ_WLY','m_admin2wly');

//可提现余额类型
define('CW_TXYE',70);

/* 活动推荐奖励相关 */
//被推荐人情况视图(前缀tv_)
define('RECOMMENDED_VIEW','m_applicantrelation');

/**
 * d打包轮询错误提醒的手机号
 */
define('DB_MT','13512442396');

/* 货源相关 */
//货源普发货类型
define('GOODS_PT_TYPE',1);
//货源组合发货类型
define('GOODS_ZH_TYPE',11);
//货物打包发货类型
define('GOODS_DB_TYPE',21);

/* 货物拆分类型 */
//组合货物总货物未拆分类型
define('GOOD_ZH_ZONG_WC',1);
//组合货物总货物已拆分类型
define('GOOD_ZH_ZONG_YC',2);
//组合货物子货物拆分类型
define('GOODS_ZH_SPLITED',3);

/* 协议相关 */
//车主同意货主修改协议状态
define('DEAL_CHANGE_AGREE',3);

/* 积分相关 */
//当前积分活动倍数
define('SCORE_HD_BASE',1);
//车辆长度比重标准(小于4.2米为50%；大于等于4.2米为100%)
define('CAR_LENGTH_WEIGHT_STANDARD',4.2);
//积分引擎API接口key
define('SCORE_API_KEY','isKpHczNKZqXrrJHsHTO');

/*车辆专业等级积分相关*/
//初级车辆积分下限
define('CAR_LEVEL_1_SCORE',0);
//中级车辆积分下限
define('CAR_LEVEL_2_SCORE',5000);
//高级车辆积分下限
define('CAR_LEVEL_3_SCORE',15000);
//王牌级车辆积分下限
define('CAR_LEVEL_4_SCORE',40000);
//车辆最大专业积分
define('CAR_MAX_LEVEL_SCORE',50000);

/* 车辆评价赞星相关 */
//车辆赞星操作码-很满意(+1颗赞星)
define('CAR_EVALU_CODE_ADD','cp0001');
//车辆赞星操作码操作码-很不满意(-1颗赞星)
define('CAR_EVALU_CODE_REDUCE','cp0004');
//推荐人为车主时的推荐码
define('CZ_RECOMMEND_CODE','ct0001');
//初级车辆赞星数量最小值
define('CAR_MIN_EVALU_MIN_NUM',0);
//其他级别车辆赞星数量最小值
define('CAR_OTHER_EVALU_MIN_NUM',1);
//车辆赞星数量最大值
define('CAR_EVALU_MAX_NUM',5);
//车辆评价最小等级
define('CAR_EVALU_MIN_LEVEL',1);
//车辆评价最大等级
define('CAR_EVALU_MAX_LEVEL',5);

/* 车队级别积分相关 */
//1A级车队
define('MOTORCADE_LEVEL_1_SCORE',0);
//2A级车队
define('MOTORCADE_LEVEL_2_SCORE',2000);
//3A级车队
define('MOTORCADE_LEVEL_3_SCORE',8000);
//4A级车队
define('MOTORCADE_LEVEL_4_SCORE',18000);
//5A级车队
define('MOTORCADE_LEVEL_5_SCORE',40000);
//车队等级最大平均分
define('MOTORCADE_MAX_LEVEL_SCORE',50000);

/* 货主信誉积分相关 */
//一星信誉积分下限
define('HZ_CREDIT_1_SCORE',0);
//二星信誉积分下限
define('HZ_CREDIT_2_SCORE',1000);
//三星信誉积分下限
define('HZ_CREDIT_3_SCORE',5000);
//四星信誉积分下限
define('HZ_CREDIT_4_SCORE',12000);
//五星信誉积分下限
define('HZ_CREDIT_5_SCORE',25000);
//货主信誉积分最大值
define('HZ_MAX_CREDIT_SCORE',50000);

//api接口通用key
define('API_KEY','a6wpE5fZIkAqPfrUTdln');

return  array(
    /* 项目设定 */
    'APP_STATUS'            => 'debug',  // 应用调试模式状态 调试模式开启后有效 默认为debug 可扩展 并自动加载对应的配置文件
    'APP_FILE_CASE'         => false,    // 是否检查文件的大小写 对Windows平台有效
    'APP_AUTOLOAD_PATH'     => '@.AutoLoad',// 自动加载机制的自动搜索路径,注意搜索顺序
    'APP_TAGS_ON'           => true,    // 系统标签扩展开关
    'APP_SUB_DOMAIN_DEPLOY' => false,   // 是否开启子域名部署
    'APP_SUB_DOMAIN_RULES'  => array(), // 子域名部署规则
    'APP_SUB_DOMAIN_DENY'   => array(), //  子域名禁用列表
    'ACTION_SUFFIX'         =>  '',     // 操作方法后缀

    /*网站模块分组配置*/
    'MODULE_ALLOW_LIST'     => array('Member','Api','SemSend'),
    'DEFAULT_GROUP'         => 'Member',                        //默认分组


    /* Cookie设置 */
    'COOKIE_EXPIRE'         => 0,      // Coodie有效期
    'COOKIE_DOMAIN'         => '',      // Cookie有效域名
    'COOKIE_PATH'           => '/',     // Cookie路径
    'COOKIE_PREFIX'         => '',      // Cookie前缀 避免冲突

    /* 数据库设置 */
    'DB_TYPE'               => 'mysql',     // 数据库类型
    'DB_HOST'               => '127.0.0.1', // 服务器地址
    'DB_NAME'               => 'wuliubang',          // 数据库名
    'DB_USER'               => 'root',      // 用户名
    'DB_PWD'                => 'xy990622',          // 密码
    'DB_PORT'               => '3306',        // 端口
    'DB_PREFIX'             => 'tp_',    // 数据库表前缀
    'DB_FIELDTYPE_CHECK'    => false,       // 是否进行字段类型检查
    'DB_FIELDS_CACHE'       => true,        // 启用字段缓存
    'DB_CHARSET'            => 'utf8',      // 数据库编码默认采用utf8
    'DB_DEPLOY_TYPE'        => 0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE'        => false,       // 数据库读写是否分离 主从式有效
    'DB_MASTER_NUM'         => 1, // 读写分离后 主服务器数量
    'DB_SLAVE_NO'           => '', // 指定从服务器序号
    'DB_SQL_BUILD_CACHE'    => false, // 数据库查询的SQL创建缓存
    'DB_SQL_BUILD_QUEUE'    => 'file',   // SQL缓存队列的缓存方式 支持 file xcache和apc
    'DB_SQL_BUILD_LENGTH'   => 20, // SQL缓存的队列长度
    'DB_SQL_LOG'            => false, // SQL执行日志记录

    /* 数据缓存设置 */
    'DATA_CACHE_TIME'       => 0,      // 数据缓存有效期 0表示永久缓存
    'DATA_CACHE_COMPRESS'   => false,   // 数据缓存是否压缩缓存
    'DATA_CACHE_CHECK'      => false,   // 数据缓存是否校验缓存
    'DATA_CACHE_PREFIX'     => '',     // 缓存前缀
    'DATA_CACHE_TYPE'       => 'File',  // 数据缓存类型,支持:File|Db|Apc|Memcache|Shmop|Sqlite|Xcache|Apachenote|Eaccelerator
    'DATA_CACHE_PATH'       => TEMP_PATH,// 缓存路径设置 (仅对File方式缓存有效)
    'DATA_CACHE_SUBDIR'     => false,    // 使用子目录缓存 (自动根据缓存标识的哈希创建子目录)
    'DATA_PATH_LEVEL'       => 1,        // 子目录缓存级别

    /* 模板引擎设置 */
    'TMPL_CONTENT_TYPE'     => 'text/html', // 默认模板输出类型
    'TMPL_ACTION_SUCCESS'=>'Index::dispatch_success',
    'TMPL_ACTION_ERROR'=>'Index:dispatch_error',

    //'TMPL_EXCEPTION_FILE'   => THINK_PATH.'Tpl/think_exception.tpl',// 异常页面的模板文件
    'TMPL_DETECT_THEME'     => false,       // 自动侦测模板主题
    'TMPL_FILE_DEPR'        =>  '/', //模板文件MODULE_NAME与ACTION_NAME之间的分割符
    'DEFAULT_FILTER'        => 'strip_tags,htmlspecialchars', //I方法的过滤函数
    //定义模板变量
    'TMPL_PARSE_STRING'     => array(
        '__WEBROOT__' => __ROOT__,
        '__WEBPUBLIC__' => __ROOT__.'/Public',
        '__LOCAL__' => "现场结算",
        '__PLATE__' => '周结',
        '__CASH__' => '担保支付',
        '__WLB__' => WLB_URL,
        '__CITY__' => CITY_URL,
        '__COLD__' => COLD_URL,
        '__MATCH__' => MATCH_URL.'/index',
        '__FIX__' => FIX_URL.'/weixiu/view/',
        '__WEIGHT__' => '重量',
        '__SQUARE__' => '体积',
    ),

    /* URL设置 */
    'URL_CASE_INSENSITIVE'  => false,   // 默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'             => 2,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式，提供最好的用户体验和SEO支持
    'URL_PATHINFO_DEPR'     => '/',	// PATHINFO模式下，各参数之间的分割符号
    'URL_PATHINFO_FETCH'    =>   'ORIG_PATH_INFO,REDIRECT_PATH_INFO,REDIRECT_URL', // 用于兼容判断PATH_INFO 参数的SERVER替代变量列表
    'URL_HTML_SUFFIX'       => '',  // URL伪静态后缀设置
    'URL_DENY_SUFFIX'       =>  'ico|png|gif|jpg', // URL禁止访问的后缀设置
    'URL_PARAMS_BIND'       =>  true, // URL变量绑定到Action方法参数
    'URL_404_REDIRECT'      =>  '', // 404 跳转页面 部署模式有效

    /* 系统变量名称设置 */
    'VAR_GROUP'             => 'g',     // 默认分组获取变量
    'VAR_MODULE'            => 'm',		// 默认模块获取变量
    'VAR_ACTION'            => 'a',		// 默认操作获取变量
    'VAR_AJAX_SUBMIT'       => 'ajax',  // 默认的AJAX提交变量
//    'VAR_JSONP_HANDLER'     => 'callback',
    'VAR_PATHINFO'          => 's',	// PATHINFO 兼容模式获取变量例如 ?s=/module/action/id/1 后面的参数取决于URL_PATHINFO_DEPR
    'VAR_URL_PARAMS'        => '_URL_', // PATHINFO URL参数变量
    'VAR_TEMPLATE'          => 't',		// 默认模板切换变量
    'VAR_FILTERS'           =>  'filter_exp',     // 全局系统变量的默认过滤方法 多个用逗号分割


    'OUTPUT_ENCODE'         =>  false, // 页面压缩输出
    'HTTP_CACHE_CONTROL'    =>  'private', // 网页缓存控制

    //扩展配置
//    'LOAD_EXT_CONFIG'       => 'extra_config',
    'LOAD_EXT_CONFIG'       => 'table',
    //'LOAD_EXT_FILE'         => '',

    //验证邮箱
    'EMAIL'                 => '/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/',
    //验证手机号
    'MOBILE'                => '/^((0?1[358]\d{9})|((0(10|2[1-3]|[3-9]\d{2}))?[1-9]\d{6,7}))$/',
    //验证固话号
    'PHONE'                 => '/^\d{3,4}-\d{7,8}(-\d{3,4})?$/',
    //日期格式验证
    'DATE'                  => '/^\d{4}\-\d{2}\-\d{2}$/',
    //时间格式验证
    'TIME'                  => '/^(0\d{1}|1\d{1}|2[0-3]):([0-5]\d{1})$/',
    'NETURL'                => '/http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/',
    //api路径
    'API_URL'               => 'http://api.5656111.com',

    //支付相关
    'payWay'                => array(
        '1'=>'农行支付',
        '5'=>'其他网银支付',
        '9'=>'支付宝支付',
    ),

    //车型相关
    'car_type'              => array(
        0 => '无要求',
        1 => '平板车',
        2 => '高栏车',
        3 => '厢式车',
        4 => '危化车',
        5 => '铁笼车',
        6 => '冷藏车',
        11 => '小面包',
        12 => '平顶面包',
        13 => '高顶面包',
        14 => '厢式轻卡',
        15 => '平板轻卡',
        16 => '高栏轻卡',
        17 => '厢式中卡',
        18 => '平板中卡',
        19 => '高栏中卡',
        20 => '厢式重卡',
        21 => '平板重卡',
        22 => '高栏重卡',
    ),

    //车长相关
    'car_length'            => array(
        0 => '无要求',
        2 => '等于7.2米',
        3 => '大于7.2米',
    ),

    //车长后缀
    'length_offset'         => array(
        1 => '以下',
        2 => '左右',
        3 => '以上',
    ),

    //结算方式
    'pay_type'              => array(
        1 => '担保支付',
        2 => '周结',
        3 => '现场结算',
    ),

    //装卸需求
    'unload_need'           => array(
        1 => '装车、卸车均无需司机参与',
        2 => '装车、卸车需司机辅助',
        3 => '装车需司机负责',
        4 => '卸车需司机负责',
        5 => '装车卸车均需司机负责',
    ),

    //是否回单
    'is_receipt'            => array(
        0 => '不需要回单',
        1 => '需要回单',
    ),

    //是否有代收款
    'is_clcnmny'            => array(
        0 => '无',
        1 => '有',
    ),

    //打包货源频率
    'db_frequency'          => array(
        1 => '一天一次',
        2 => '两天一次',
        3 => '三天一次',
        4 => '四天一次',
        5 => '五天一次',
    ),

    //组合货物结算方式
    'zh_pay_type'           => array(
        1 => '现场结算',
        2 => '周结',
    ),

    //财务相关操作码
    'fnrcd_code'            => array(
        1 => array( //保证金
            //保证金充值
            1 => array('in','cw000001'),
            //保证金扣款
            2 => array('in','cw010204,cw020204,cw030204,cw010307,cw020307,cw030307'),
        ),
        2 => array( //运费
            //运费
            1 => array('in','cw010105,cw030105,cw020106'),
            //转提现帐户
            2 => array('in','cw010109,cw020109,cw030109'),
        ),
        3 => array( //提现
            //提现
            1 => array('in','cw888888,cw888880'),
            //活动奖励
            2 => array('in','cw888000'),
            //运费转入
            3 => array('in','cw010109,cw020109,cw030109'),
        ),
        4 => array( //代收款
            //协议应收款
            1 => array('in','cw000120'),
            //平台挖款成功
            2 => array('in','cw000122'),
        ),
    ),

    //财务相关---资金类型
    'mny_type_name'         => array(
        1 => '保证金',
        2 => '运费',
        3 => '提现',
        4 => '代收款',
    ),
    //自动完成时间
    'autocpl_time'           =>array(
        1 => strtotime('-1 day'),
        2 => strtotime('-2 days'),
        3 => strtotime('-3 days'),
        6 => strtotime('-6 days')
    ),
);