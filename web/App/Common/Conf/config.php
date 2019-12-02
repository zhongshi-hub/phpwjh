<?php
return array(
    #附加设置
    'SHOW_PAGE_TRACE' => true,                           // 是否显示调试面板
    //'URL_CASE_INSENSITIVE'   => false,                           // url区分大小写
    'TAGLIB_BUILD_IN' => 'Cx,Common\Tag\My',              // 加载自定义标签
    'LOAD_EXT_CONFIG' => 'db,web,yb',               // 加载网站设置文件
    'TMPL_PARSE_STRING' => array(                           // 定义常用路径
        '__PUBLIC__' => '/Source',
        '__YUI__'=>'/Source/ydui',
        '__RUI__'=>'/Source/RegUI',
        '__F7__'=>'/Source/Framework7'
    ),
    #系统默认的变量过滤机制
    'DEFAULT_FILTER' => 'strip_tags,htmlspecialchars',
    #URL设置
    'MODULE_ALLOW_LIST' => array('Home', 'Console', 'Tasks', 'Manage','Plugs','Pays','Mch','Business','Mp','Wap'), //允许访问列表
    'DEFAULT_MODULE' => 'Home',
    'URL_MODULE_MAP' => array('console' => 'admin','manage'=>'system','business'=>'agent'), //模块绑定
    'URL_HTML_SUFFIX' => '',  // URL伪静态后缀设置
    'URL_MODEL' => 2,  //启用rewrite
    #SESSION设置
    'SESSION_OPTIONS' => array(
        'name' => 'XunMaFu_Com',//设置session名
        //'expire'             => 24*3600*15, //SESSION保存15天
        'expire' => 24 * 3600 * 1, //SESSION保存1天
        'use_trans_sid' => 1,//跨页传递
        'use_only_cookies' => 0,//是否只开启基于cookies的session的会话方式
    ),
    #页面设置
    'TMPL_EXCEPTION_FILE' => APP_DEBUG ? THINK_PATH . 'Tpl/think_exception.tpl' : './Template/default/Home/Public/404.html',
    // 'TMPL_ACTION_ERROR'      => TMPL_PATH.'/Public/dispatch_jump.tpl', // 默认错误跳转对应的模板文件
    // 'TMPL_ACTION_SUCCESS'    => TMPL_PATH.'/Public/dispatch_jump.tpl', // 默认成功跳转对应的模板文件
    #auth设置
    'AUTH_CONFIG' => array(
        'AUTH_USER' => 'users'                         //用户信息表
    ),
    #邮件服务器
    'EMAIL_FROM_NAME' => '',   // 发件人
    'EMAIL_SMTP' => '',   // smtp
    'EMAIL_USERNAME' => '',   // 账号
    'EMAIL_PASSWORD' => '',   // 密码  注意: 163和QQ邮箱是授权码；不是登录的密码
    'EMAIL_SMTP_SECURE' => '',   // 链接方式 如果使用QQ邮箱；需要把此项改为  ssl
    'EMAIL_PORT' => '25', // 端口 如果使用QQ邮箱；需要把此项改为  465
    #缓存设置
    'DATA_CACHE_KEY' => 'XunMaFu',
    'DATA_CACHE_TIME' => 1800,        // 数据缓存有效期s
    'DATA_CACHE_PREFIX' => 'mem_',      // 缓存前缀
    'DATA_CACHE_TYPE' => 'File', // 数据缓存类型,
    'MEMCACHED_SERVER' => '127.0.0.1', // 服务器ip
    'NEED_UPLOAD_OSS' => array( // 需要上传的目录
        '/Upload/avatar',
        '/Upload/cover',
        '/Upload/attachment',
        '/Upload/video',
    ),
    #路由规则
    'URL_ROUTER_ON' => true,



    'URL_MAP_RULES' => array(
		'Pay/memberPay' => 'Pays/Member/cardPay',
        'MsnNotify' => 'Tasks/AliMns/Notify',
        'Ext' => 'Home/Minify/index',
        'System' => 'Manage/Login/index',
        'Admins' => 'Console/Login/index',
        'Agent'=>'Business/Login/index',
        'AuthReturn'=>'Plugs/UserAuth/return_url',
        'sppay-interface-war/gateway' => 'Sppay/Gateway/index',
		'aliOauth'=>'Pays/PAliisv/mchOauth',
		'aliOauthUrl'=>'Pays/PAliisv/getOauthUrl',
		'api/face'=>'Pays/FaceApi/gateway',
		'api/login'=>'Pays/FaceUser/login',
		'api/dl'=>'Wap/ApiDl/index'
    ),

    'URL_ROUTE_RULES' => array(
    	'invites/:code'=>'Wap/Dl/invite',
		'api/users/:data' =>'Pays/FaceUser/:1',//用户信息Api接口
        'EQ/:codes' => 'Pays/Eq/index', //云码入口
        'EQM/:codes' => 'Pays/Eq/codes', //O单商户码入口
		'Pay/:codes' => 'Pays/Qrcode/index', //O单商户码入口
        'Api/:data' =>'Pays/Notify/:1',
        'Token/:token' => 'Home/WxApi/index', //O单商户码入口
        'WxImg/:attach' => 'Plugs/Other/WxImg', //O单商户码入口
        'CardApi/:data' =>'Pays/CardNotify/:1',
        'SApi/:data' =>'Pays/SftNotify/:1',
        //'Notify/:data' =>'Pays/:1/notify_url',
        'codes/:codes' => 'Pays/Eq/index', //旧收款码 云码入口
        'Gateway/:data' =>'Pays/Gateway/:1',//外部接口网关

    ),




    'DB_SQL_BUILD_CACHE' => false,

    #日志记录
    'LOG_RECORD' => false, // 开启日志记录
    'LOG_TYPE' => 'File', // 日志记录类型 默认为文件方式

    #表单令牌
    'TOKEN_ON' => true,  // 是否开启令牌验证 默认关闭
    'TOKEN_NAME' => '__TokenHash__',    // 令牌验证的表单隐藏字段名称，默认为__hash__
    'TOKEN_TYPE' => 'md5',  //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET' => true,  //令牌验证出错后是否重置令牌 默认为true


    #默认主题
    'DEFAULT_THEME' => 'Default',


    #阿里云消息服务
    'Ali_MNS' => array(
        'topName' => '',
        'accessId' => '',
        'accessKey' => '',
        'endPoint' => '',
    ),

    #码云域名
    'MA_DATA_URL' => 'http://'.$_SERVER['HTTP_HOST'].'/EQ',

    #阿里云OSS
    'FILE_SIZE_MAX'=>'3', //最大上传单位 M
    'FILE_UPLOAD_TYPE' => 'local',
    'ALI_OSS_CONFIG'=>array(
        'access_id' => '', //阿里云Access Key ID
        'access_key' => '', //阿里云Access Key Secret
        'endpoint'=>'',
        'oss_domain'=>'', //默认OSS绑定域名
        'oss_bucket'=>'', //默认bucket
    ),
);
