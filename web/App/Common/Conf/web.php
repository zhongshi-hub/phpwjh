<?php
/*
 * 服务端威富通官方配置
 * Author: CCL
 * EndTime: 2017-03-09 18:00
 * */
return array(
   'WEB_NAME'=>'讯码付(移动支付)',  //网站名称
   'WEB_COPY'=>'讯码付(移动支付) XunMaFu.Com 版权所有',  //网站名称
   'CARD_AUTH_APP_CODE'=>'4b8b29244f87471bb8aa1e81f4409777',//阿里云银行卡鉴权
	'AdminDomain'=>array(  //在列表的域名直接跳转到后台管理首页
       ''
   ),
   'API_CONFIG'=>array( //API接口参数配置
        'IS_HTTPS'=>true, //是否强制httpsŒ
        'IS_API_DOMAIN'=>false, //是否开启独立API域名
        'API_DOMAIN'=>'', //接口独立域名 不含http
   )
);