<?php

require('facebook/autoload.php'); 

class tieba{

	protected $html;
	protected $postHTML;
	
	function __construct($url){
		$this->html=$this->getHTMLByCurl($url);
	}

	function getHTMLByCurl($url){
		$curl=curl_init();

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_FAILONERROR, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 5);

		$r=curl_exec($curl);
		curl_close($curl);
		$info=curl_getinfo($curl);
		$r=iconv('gbk','utf-8',var_export($r,true));//转换gbk到utf-8编码,否则输出是乱码
		return $info;
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
			$jsonData.='",],"dangerlevel":"0"],';
		}
		return "{".$jsonData."}";
	}
}

//$tb=new tieba("http://tieba.baidu.com/f?ie=utf-8&kw=2333333333333333333333333333333333333333333333");
//echo $tb->getJson();

/**
* 
*/
class csdn extends tieba{

	protected $html1;

	function __construct($url){
		//csdn似乎禁用了CURL,反正我用CURL读取不了数据,所以改用这个函数了
		$this->html1=file_get_contents($url);
	}

	function getLinks(){
		$pattern="^[a-zA-z]+://[^\s]*";
		//print_r($this->html1);
		preg_match_all($pattern,$this->html1,$links);
		print_r($links);
	}
}

//引入facebook SDK
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookCanvasLoginHelper;

//用在facebook开发者平台申请到的app ID和secret初始化SDK
FacebookSession::setDefaultApplication('382257391925534','ec3f6b1703dd14e4d7b640a315fe413e');

$helper = new FacebookCanvasLoginHelper();
try {
  $session = $helper->getSession();
} catch(FacebookRequestException $ex) {
  // When Facebook returns an error
} catch(\Exception $ex) {
  // When validation fails or other local issues
}

// Get the GraphUser object for the current user:

if($session){
	try {
  		$me = (new FacebookRequest(
    		$session, 'GET', '/me'
  		))->execute()->getGraphObject(GraphUser::className());
  		echo $me->getName();
	} catch (FacebookRequestException $e) {
  		// The Graph API returned an error
	} catch (\Exception $e) {
  		// Some other error occurred
	}
}




?>