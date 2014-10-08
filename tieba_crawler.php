<?php

/**
* 
*/
class tieba{

	protected $html;
	
	function __construct($url,$debug=0){
		$this->html=$this->getHTMLByCurl($url);
		$this->html=iconv('gbk','utf-8',var_export($this->html,true));//转换gbk到utf-8编码,否则输出是乱码
	}

	function getHTMLByCurl($url){
		$curl=curl_init($url);

		curl_setopt($curl, CURLOPT_FAILONERROR, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 5);
		//curl_setopt($curl, CURLOPT_POST, 1);
		//curl_setopt($curl, CURLOPT_POSTFIELDS, 'name=foo&format=csv');

		$r=curl_exec($curl);
		curl_close($curl);

		return $r;
	}

	function getLinks(){
		$pattern="<a href=\"/p/+[0-9]+\">";
		preg_match_all($pattern, $this->html, $links);
		$link=array();
		foreach($links[0] as $key => $value){
			$pattern="a href=\"";
			$href=str_replace($pattern,"",$value);
			$href=str_replace("\"","",$href);
			$href="http://tieba.baidu.com".$href;
			$link[]=$href;
		}
		print_r($link);
	}

	function getContents($url){
		
	}

	function getComments($url){

	}

	function getJson(){

	}
}

$tb=new tieba("http://tieba.baidu.com/f?kw=%B6%AF%C2%FE",1);
$tb->getLinks();
?>