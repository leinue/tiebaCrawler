<?php
//===============================================
//0.头部设置及全局函数配置引用
//===============================================
    header("Content-type: text/html; charset=utf-8"); 
	//php头声明中文编码方式为utf-8
	
    include 'config.php';  //包含全局参数配置

    require('schema.php');  //包含模式匹配库

    require('functions/mysql.php');  //包含filter数据库操作类

	//$url_receive变量用来接收挖掘模块返回的Json封装结果，此处json数据为测试用
    $url_receive =$_POST["data"];
	$url_receive=iconv("GB2312","UTF-8",$url_receive);
	/*$url_receive='{
"data":[
{"postid":"1000465231234_56432159665",
"link":"www.facebook.com/uniguyit/54569664fddf25644565",
"message":"my keyword is fuck",
"dangerlevel":"default",
"updatetime":"2014-10-11T07:25:14+0000",
"isapproved":"0"
},
{"postid":"1000465231234_56432159665",
"link":"www.facebook.com/uniguyit/5449865xcc296645455",
"message":"my keyword is shsdfisdft",
"dangerlevel":"default",
"updatetime":"2014-10-11T07:25:14+0000",
"isapproved":"0"
}
]}';*/
    $url_acept = json_decode($url_receive,true);//返回解码JSON数组

    if(is_array($url_acept)==0){
    	echo 'JSON数据解析失败';
    	echo json_last_error();
    }else{

		$jsonOuterDepth=count($url_acept['data']);//获得json数据的外围深度
  
    	//$myfile=fopen($wordlisturl,"r");
    	//$str=file_get_contents($wordlisturl,"r");
    	//fclose($myfile);
    
    	//打开词库文件，并以","分割,使用schema.php中的库
    	$keywordList=loadDepot($wordlisturl);

    	//使用PDO登录mysql
	    try {
			$pdo=new PDO("mysql:dbname=fbproject;host=".$GLOBALS['serverurl'],$GLOBALS['databaseuser'],$GLOBALS['databasepassword']);
		} catch (PDOException $e) {
			echo $e->getMessage();
		}

		//实例化filterSQLMgr对象
		$filter=new filterSQLMgr($pdo); 
   
    	//以json数据的外围深度遍历json内部数据
    	foreach ($url_acept['data'] as $key => $value) {
    	
   			//匹配非法字符库并得出危险程度、分析标记
  			$marktag = "default";	//分析标记
   			$dangerlevel = "default";	//危险程度

   			//初始化link数据
	    	$url_dim = $value['link'];

			$flag=array();		//新的黑名单存储数组
			$whiteFlag=array();		//新的白名单存储数组
			$unknownFlag=array();	//新的未知词汇存储数组
			$cnt=0;

			//检查链接是否已被解析过
	    	if(!$filter->linkIsChecked($url_dim)){
				//匹配非法字符库并得出危险程度、分析标记
	   			$isapproved = "default";	//分析标记
	   			$dangerlevel = "default";	//危险程度
				for($i=0;$i<count($keywordList);$i++){
					//使用schema.php库的函数进行模式匹配
					$match_one = matchword($value['message'],$keywordList[$i]);

					//echo "1.".$keywordList[$i]." match ".$value['message']." = ".$match_one."<br>";

					$r=ruffle($keywordList[$i]);
					$msgExploded=explode(" ", $value['message']);
					foreach ($r as $key2 => $word) {
						foreach ($msgExploded as $key3 => $singleMsg) {
							if($singleMsg!="is"){
								$t=matchOfEqualLength($word,$singleMsg);//返回真代表是恶意词汇
								//echo "2.".$word." match ".$singleMsg."=$t<br>";								
							}
						}
					}

					if($match_one && $t){
						$flag[]=$value;
						$cnt++;
					}else{
						if($match_one || $t){
							$unknownFlag[]=$value;
						}else{
							$whiteFlag[]=$value;
						}
					}
					
				}

				if(count($flag)!=0){
					$blackMarked=$flag[0];
					$result=$filter->addToBlackList($blackMarked['postid'],$blackMarked['link'],$blackMarked['message'],
						$blackMarked['updatetime'],count($flag),$blackMarked['isapproved']);
					if($result){
						echo '链接'.$blackMarked['link'].'加入黑名单成功';
					}else{
						echo '链接'.$blackMarked['link'].'加入黑名单失败';
					}
				}

				if(count($unknownFlag)!=0){
					$unknownMarked=$unknownFlag[0];
					$result=$filter->addToUnkown($unknownMarked['postid'],$unknownMarked['link'],$unknownMarked['message'],
						$unknownMarked['updatetime'],count($unknownFlag),$unknownMarked['isapproved']);
					if($result){
						echo '链接'.$unknownMarked['link'].'加入人工审核名单成功';
					}else{
						echo '链接'.$unknownMarked['link'].'加入人工审核名单失败';
					}
				}

				if(count($whiteFlag)!=0){
					$whiteMarked=$whiteFlag[0];
					$result=$filter->addToWhiteList($whiteMarked['postid'],$whiteMarked['link'],$whiteMarked['message'],
						$whiteMarked['updatetime'],'0',$whiteMarked['isapproved']);
					if($result){
						echo '链接'.$whiteMarked['link'].'加入白名单成功';
					}else{
						echo '链接'.$whiteMarked['link'].'加入白名单失败';
					}
				}

			}else{
				echo "$url_dim has been in the list<br>";
			}
		}	
    }

		
		
   ?>