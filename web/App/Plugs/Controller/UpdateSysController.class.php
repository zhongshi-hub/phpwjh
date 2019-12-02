<?php

namespace Plugs\Controller;
use Think\Controller;

/**
 * 数据库更新升级专用控制器
 * 如删除此文件 后续导致无法升级 请自行负责
 * Class UpdateSysController
 * @package Admin\Controller
 */
class UpdateSysController extends Controller
{
	public function sql(){
		//清除日志
		$multiFiles=glob("*.txt");
		$number=0;
		foreach($multiFiles as $v){
			unlink($v);
			$number++;
		}
		echo '删除日志文件'.$number.'个<br><hr>';
		//执行升级SQL
		$Model = D();
		$file=__ROOT__.'update.sql';
		$sql=file_get_contents($file);
		if($sql) {
			echo "当前时间" . date('Y-m-d H:i:s') . '<br><hr>';
			//echo '当前升级执行SQL为:<br>' . PHP_EOL;
			//echo $sql;
			echo '<hr><br>执行结果:<br>' . PHP_EOL;
			$sqlList=explode(';',$sql);
            $sqlCall=[];
            $j=0;
			foreach ($sqlList as $k=>$v){
				$res = $Model->execute($v.';');
				$sqlCall[$k]=[
				    '执行SQL:'=>$v,
				    '执行结果:'=>$res,
				];
				if($res){$j++;}
			}
            $count=count($sqlList);
			if($count==$j){
				unlink($file);
			}

			dump($sqlCall);
//			if ($res!=false) {
//				//处理成功 删除升级sql文件
//				unlink($file);
//				echo  '处理成功';
//			} else {
//				echo  '处理失败';
//			}
		}else{
			echo 'no update file';
		}
	}
}