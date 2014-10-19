<?php
error_reporting(E_ALL & ~E_NOTICE);
/**
* 比较相等长度的字符串
* 较为严格,不考虑字符的变化位置,如fkuc匹配fuck是匹配不到的,当然如果词典里有fkuc便能匹配类似fkxx的字符串
* @param $depot 词典(单个词语)
* @param $input 用户输入(单个词语)
* @return true or false
*/
function matchOfEqualLength($depot,$input){
	$depot=str_split($depot);
	$input=str_split($input);

	$delength=count($depot);
	$ilength=count($input);

	$flag=0;

	foreach ($depot as $key => $value) {
		if($input[$key]==$value){
			$flag++;
		}
	}

	//计算某个字符串的标准值,比如fuck为0.5,bitch为0.4,这个视字符串长度而定
	$repeat=1-ceil($ilength/2)/$ilength;

	//$flag/count($input)表示在input中出现的和$depot中字符相同的概率
	//如果大于等于repeat就判断为恶意词汇
	return $flag/count($input)>=$repeat;
}

/**
* 打乱字母顺序
* @param $depot 某词比如fuck
* @return 返回字母打乱后的字符串数组
*/
function ruffle($depot){

	$depot=str_split($depot);
	$cnt=count($depot);
	
	for ($i=0; $i < $cnt; $i++) { 
		foreach ($depot as $key => $value) {
			if($key!=$cnt-1){
				//交换变量,和冒泡排序差不多
				$tmp=$depot[$key];
				$depot[$key]=$depot[$key+1];
				$depot[$key+1]=$tmp;
			}
		}
		//生成新的数组,注意是字符数组
		$arr[]=$depot;
	}

	//将字符数组转换为字符串数组
	foreach ($arr as $key => $value) {
		foreach ($value as $pos => $char) {
			$word.=$char;
		}
		$new[]=$word;
		$word="";
	}
	return $new;
}

/**
* 读入词库
* @param 词库目录
* @return 词库
*/
function loadDepot($dir){
	//$content=file_get_contents($dir);
	//$content=iconv("gb2312", "utf-8//IGNORE",$content);
	$fp=fopen($dir, "r");
	$content=fread($fp,filesize($dir));
	fclose($fp);
	$content=substr($content,3);
	return explode(',',$content);
}


function matchword($input,$matchword){
	/*for($i=0;$i<strlen($matchword);$i++)
	{
		$partten="/^";
		if($i<strlen($matchword)){
			$partten.=$matchword[$i].'.*';
		}else{
			$partten.=$matchword[$i].'+[A-Za-z]+$/u';
		}
		//echo "current=".$matchword[$i]." pattern=".$partten."<br>";
	}*/
	//preg_match($partten,$input,$r);

	if(strpos($input,$matchword)){
		return 1;
	}else{
		return 0;
	}
	
}
//这么用
/*$r=ruffle("fcuk");
foreach ($r as $key => $word) {
	$t=matchOfEqualLength($word,"fcxk");//第一个参数是词库数据,每次填入一个词,数据多的时候加个循环就行
	if($t){
		echo 'fcxk match '.$word.' not valid<br>';
	}else{
		echo 'fcxk match '.$word.' valid<br>';
	}
}*/

$r=ruffle("fcuk");

print_r($r);

?>