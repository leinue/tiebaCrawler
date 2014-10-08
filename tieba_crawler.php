<?php

class tieba{

	protected $html;
	protected $postHTML;
	
	function __construct($url){
		$this->html=$this->getHTMLByCurl($url);
	}

	function getHTMLByCurl($url){
		$curl=curl_init($url);

		curl_setopt($curl, CURLOPT_FAILONERROR, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 5);

		$r=curl_exec($curl);
		curl_close($curl);
		$r=iconv('gbk','utf-8',var_export($r,true));//转换gbk到utf-8编码,否则输出是乱码
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
		return $link;
	}

	function getContents($url){
		$pattern="/<div[^>\/]*class=\"d_post_content j_d_post_content \">(.*?)<\/div>/i";
		$this->postHTML=$this->getHTMLByCurl($url);
		preg_match_all($pattern,$this->postHTML,$contents);
		return $contents;
	}

	/*function getComments($url){
		$pattern="/<span class=\"[^\"]+\">[^>]+>/u";
		preg_match_all($pattern,$this->getHTMLByCurl($url),$remarks);
		return $remarks;
	}*/

	function getJson(){
		$linksList=$this->getLinks();
		foreach($linksList as $key => $url){
			$contentsList=$this->getContents($url);
			$jsonData.='"'.$url.'":["html":"'.$this->postHTML.'","contentlist":"[';
			foreach ($contentsList as $key1 => $contentOuter) {
				foreach ($contentOuter as $key2 => $contentInner) {
					$jsonData.='"content-'.$key2.'":"'.$contentInner.'",';
				}
			}
			$jsonData.='",],"dangerlevel":"0"],<br>';
		}
		return "{".$jsonData."}";
	}
}

$tb=new tieba("http://tieba.baidu.com/f?ie=utf-8&kw=2333333333333333333333333333333333333333333333");
echo $tb->getJson();
?>