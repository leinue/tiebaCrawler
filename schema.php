<?php

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

	$repeat=1-ceil($ilength/2)/$ilength;

	return $flag/count($input)>=$repeat;
}

//echo matchOfEqualLength("fuckbitch","fxxkbxxxh");

//打乱字母顺序
function ruffle($depot){

	$depot=str_split($depot);
	$cnt=count($depot);

	foreach ($depot as $key => $value) {
		if($key!=$cnt-1){
			$tmp=$depot[$key];
			$depot[$key]=$depot[$key+1];
			$depot[$key+1]=$tmp;
		}
	}
	foreach ($depot as $key => $value) {
		$dtr.=$value;
	}
	return $dtr;
}

echo ruffle("fcuk");

?>