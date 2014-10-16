<?php
//===============================================
//0.头部设置及全局函数配置引用
//===============================================
    header("Content-type: text/html; charset=utf-8"); 
	//php头声明中文编码方式为utf-8
	
    include 'config.php';  //包含全局参数配置

    require('schema.php');  //包含模式匹配库

//===============================================
//1.预置数据库操作函数
//===============================================
	//更新黑名单函数
  function updatablacklist($postid, $link, $message, $dangerlevel, $updatetime, $isapproved){
    $mysqli = new mysqli($GLOBALS['serverurl'],$GLOBALS['databaseuser'],$GLOBALS['databasepassword'],$GLOBALS['database']);
    $mysqli->query("INSERT INTO ".$GLOBALS['tablename_black']. "(postid, link, message, dangerlevel, updatetime, isapproved) VALUES ('{$postid}', '{$link}', '{$message}','{$dangerlevel}','{$updatetime},'{$isapproved}')");
	echo $postid."已被加入黑名单</br>";
  }
	//更新白名单函数
  function updatawhitelist($postid, $link, $message, $dangerlevel, $updatetime, $isapproved){
    $mysqli = new mysqli($GLOBALS['serverurl'],$GLOBALS['databaseuser'],$GLOBALS['databasepassword'],$GLOBALS['database']);
    $mysqli->query("INSERT INTO ".$GLOBALS['tablename_white']. "(postid, link, message, dangerlevel, updatetime, isapproved) VALUES ('{$postid}', '{$link}', '{$message}','{$dangerlevel}','{$updatetime},'{$isapproved}')");
	echo $postid."已被加入白名单</br>";
	}
	  function updataunknownlist($postid, $link, $message, $dangerlevel, $updatetime, $isapproved){
    $mysqli = new mysqli($GLOBALS['serverurl'],$GLOBALS['databaseuser'],$GLOBALS['databasepassword'],$GLOBALS['database']);
    $mysqli->query("INSERT INTO ".$GLOBALS['tablename_unknown']. "(postid, link, message, dangerlevel, updatetime, isapproved) VALUES ('{$postid}', '{$link}', '{$message}','{$dangerlevel}','{$updatetime},'{$isapproved}')");
	echo $postid."已被加入人工审核名单</br>";
	}
//===============================================
//2.通过post提交数据到过滤器中，最终输出过滤结果
//===============================================
	//$url_receive变量用来接收挖掘模块返回的Json封装结果，此处json数据为测试用
    $url_receive =$_POST["data"];
	$url_receive=iconv("GB2312","UTF-8",$url_receive);
    $url_acept = json_decode($url_receive,true);//返回解码JSON数组

    if(is_array($url_acept)==0){
    	echo 'JSON数据解析失败';
    }else{

		$jsonOuterDepth=count($url_acept['data']);//获得json数据的外围深度
  
    	//$myfile=fopen($wordlisturl,"r");
    	//$str=file_get_contents($wordlisturl,"r");
    	//fclose($myfile);
    
    	//打开词库文件，并以","分割,使用schema.php中的库
    	$keywordList=loadDepot($wordlisturl);
   
    	//以json数据的外围深度遍历json内部数据
    	foreach ($url_acept['data'] as $key => $value) {
    	
   			//匹配非法字符库并得出危险程度、分析标记
  			$marktag = "default";	//分析标记
   			$dangerlevel = "default";	//危险程度

   			//判断该url是否已经审核过
	    	$url_dim = $value['link'];
	    	$con = mysql_connect($GLOBALS['serverurl'],$GLOBALS['databaseuser'],$GLOBALS['databasepassword']);
	    	if (!$con)
	    	{
	    		die('Could not connect: ' . mysql_error());
	    	}	
	    	mysql_select_db("fbproject", $con);
	  		$query_black="select * from ".$GLOBALS['tablename_black']." where link = '{$url_dim}'";
	   		$query_white="select * from ".$GLOBALS['tablename_white']." where link = '{$url_dim}'";
	    	$result_black=mysql_query($query_black,$con);
	    	$result_white=mysql_query($query_white,$con);
	    	if(mysql_num_rows($result_black)==0 && mysql_num_rows($result_white)==0)
	    	{
				//匹配非法字符库并得出危险程度、分析标记
	   			$isapproved = "default";	//分析标记
	   			$dangerlevel = "default";	//危险程度
				for($i=0;$i<count($keywordList);$i++)
				{
				
					//使用schema.php库的函数进行模式匹配
					$match_one = matchword($value['message'],$keywordList[$i]);
					$r=ruffle($keywordList[$i]);
					foreach ($r as $key => $word) {
						$t=matchOfEqualLength($word,$value['message']);//返回真代表是恶意词汇
					}
					$match_two=(strstr($value['message'],$keywordList[$i]) or $t);//加入对$t的判断,也就是多次判断
					$finalValue=$match_one+$match_two;
					switch($finalValue)
					{
						case 2:
				    	$dangerlevel="2";  
						$isapproved = "1";	//标记为黑名单
	   					echo "该URL含有非法词汇".$keywordList[$i]."</br>";
						break;
						case 1:
						$dangerlevel = "1";
						$isapproved = "0";
						break;
						case 0:
						$dangerlevel = "0";
						break;
					}
					if(($finalValue)==2){
						break;
					}
				}
			}else{
				echo "the url has been in the list";
				break;
			}
				//根据分析标记及危险程度处理结果
				if($dangerlevel=="2"&&$isapproved=="1")
				{
					updatablacklist($value['id'],$value['link'],"",$value['dangerlevel'],$value['updatetime'],$value['isapproved']);
				}else{
				 	if($dangerlevel=="1"&&$isapproved=="0"){
						updataunknownlist($value['id'],$value['link'],$value['message'],$value['dangerlevel'],$value['updatetime'],$value['isapproved']);
					}else if($dangerlevel=="0"){
						updatawhitelist($value['id'],$value['link'],"",$value['dangerlevel'],$value['updatetime'],$value['isapproved']);
					}
				}
		}	
    }

		
		
   ?>