<?php

#
#             ┏┓      ┏┓
#            ┏┛┻━━━━━━┛┻┓
#            ┃               ☃              ┃
#            ┃  ┳┛   ┗┳ ┃
#            ┃     ┻    ┃
#            ┗━┓      ┏━┛
#              ┃      ┗━━━━━┓
#              ┃  神兽保佑              ┣┓
#              ┃ 永无BUG！            ┏┛
#              ┗┓┓┏━┳┓┏━━━━━┛
#               ┃┫┫ ┃┫┫
#               ┗┻┛ ┗┻┛


set_time_limit(0);
// 应用入口文件
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',true);
// 自动生成模块
//define('BIND_MODULE','Agent');

//define('BUILD_CONTROLLER_LIST','Login');
// 定义应用目录
define('APP_PATH','./App/');

addErrorLog("server info :".json_encode($_SERVER));
addErrorLog("get info :".json_encode($_GET));
addErrorLog("post info :".json_encode($_POST));

//define('DIR_SECURE_FILENAME', 'index.html');
//define('DIR_SECURE_CONTENT', 'deny Access!');

// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单

	//写日志
	function addErrorLog($logstr,$dayflag=false){
		$dir = "./log";
		if (!file_exists($dir)){
			mkdir ($dir,0777,true);
		}
		if($dayflag){
			$dir .= "/".date("Y-m-d");
			if (!file_exists($dir)){
				mkdir ($dir,0777,true);
			}
		}
		$logstr = date("Y-m-d H:i:s"). " " . $logstr . "\n";
		//print_r($logstr);
		$filename = $dir."/".date("Y-m-d")."_error.log";
		//print_r($filename);print_r($logstr);
		$file = fopen($filename,"a");
		fwrite($file,$logstr);
		fclose($file);
	}