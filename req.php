<?php

include 'config.php';

function deleteByPostID($id){
	$mysqli = new mysqli($GLOBALS['serverurl'],$GLOBALS['databaseuser'],$GLOBALS['databasepassword'],$GLOBALS['database']);
	$sqlVer="SELECT FROM `blacklist` WHERE `postID`=$postID";
	$resV=$mysqli->query($sqlVer);
	if($sqlVer){
		$sql="DELETE FROM `blacklist` WHERE `postID`=$postID";
		$res=$mysqli->query($sql);
		if($res){
			return true;
		}else{
			printf("Error: %s\n", $mysqli->error);
		}
		$res->close();
		$mysqli->close();
	}
}

function addToWhiteByPostID($id){
	$mysqli = new mysqli($GLOBALS['serverurl'],$GLOBALS['databaseuser'],$GLOBALS['databasepassword'],$GLOBALS['database']);
	$sqlV="SELECT FROM `whitelist` WHERE `postID`=$postID";
	$resV=$mysqli->query($sqlVer);
	if($resV){
		$sqlBlack="SELECT FROM `blacklist` WHERE `postID`=$postID";
		$resV=$mysqli->query($sqlBlack);
		while($obj = $resV->fetch_object()){
     	    $link=$obj->link;
     	    $message=$obj->message;
     	    $dangerlevel=$obj->dangerlevel;
     	    $updatetime=$obj->updatetime;
     	    $isapproved=1;
    	}	
		$sql="INSERT INTO ".$GLOBALS['tablename_white']. "(postid, link, message, dangerlevel, updatetime, isapproved) VALUES 
		('{$id}', '{$link}', '{$message}','{$dangerlevel}','{$updatetime},'{$isapproved}')";
		if($mysqli->query($sql)){
			return '1';
		}else{
			printf("Error: %s\n", $mysqli->error);
		}
	}else{
		return '-3';//加入白名单重复
	}
}

function postidIsValid($id){
	$reg="[0-9]+\_+[0-9]+";
	$res=ereg($reg,$id);
	if($res){
		return true;
	}else{
		return false;
	}
}

$postid=$_GET['postid'];
$method=$_GET['method'];

if(strlen($postid)==0 || !postidIsValid($postid) || strlen($methos)==0){
	echo '0';//参数postid为空或不是数字或method为空
}else{
	switch ($method) {
		case 'del':
			$res=deleteByPostID($postid);
			if($res){
				echo '1';//成功
			}else{
				echo '-1';//失败
			}
			break;
		case 'add':
			$res=addToWhiteByPostID($postID);
			echo $res;
			break;
		default:
			echo '-2';//method参数不对,只能为del或add
			break;
	}

}

?>