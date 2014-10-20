<?php

include 'config.php';

function deleteByPostID($id){
	$mysqli = new mysqli($GLOBALS['serverurl'],$GLOBALS['databaseuser'],$GLOBALS['databasepassword'],$GLOBALS['database']);
	$sqlVer="SELECT FROM `blacklist` WHERE `postID`=$id";
	$resV=$mysqli->query($sqlVer);
	if($sqlVer){
		$sql="DELETE FROM `blacklist` WHERE `postID`=$id";
		$res=$mysqli->query($sql);
		if($res){
			return true;
		}else{
			printf("delete Error: %s\n", $mysqli->error);
		}
		$res->close();
		$mysqli->close();
	}
}

function addToWhiteByPostID($id){
	$mysqli = new mysqli($GLOBALS['serverurl'],$GLOBALS['databaseuser'],$GLOBALS['databasepassword'],$GLOBALS['database']);
	$sqlV="SELECT FROM `whitelist` WHERE `postID`=$id";
	$resV=$mysqli->query($sqlV);
	if($resV){
		$sqlBlack="SELECT FROM `blacklist` WHERE `postID`=$id";
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
			printf("insert Error: %s\n", $mysqli->error);
		}
	}else{
		return '-3';//加入白名单重复或失败
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

function getReq(){
	$reqstart=$_GET['start'];
	$postid=$_GET['postid'];
	$method=$_GET['method'];

	if(strlen($reqstart)==0){return 'close';}

	if(strlen($postid)==0 || !postidIsValid($postid) || strlen($method)==0){
		return '0';//参数postid为空或不是数字或method为空
	}else{
		switch ($method) {
			case 'del':
				$res=deleteByPostID($postid);
				if($res){
					return '1';//成功
				}else{
					return '-1';//失败
				}
				break;
			case 'add':
				$res=addToWhiteByPostID($postid);
				return $res;
				break;
			default:
				return '-2';//method参数不对,只能为del或add
				break;
		}
	}
}

function getData(){
	$mysqli = new mysqli($GLOBALS['serverurl'],$GLOBALS['databaseuser'],$GLOBALS['databasepassword'],$GLOBALS['database']);
	$sql="SELECT * FROM `blacklist`";
	$result=$mysqli->query($sql);
	$row=$result->fetch_array(MYSQLI_ASSOC);
	$result->free();
	$mysqli->close();
	return $row;
}

function printTable(){
	$row=getData();
	foreach ($row as $key => $value) {
		echo '  				<tr class="mgr-content">
    				<td><input type="checkbox"/></td>
    				<td>'.$row['postid'].'</td>
    				<td>'.$row['message'].'</td>
    				<td>'.$row['updateTime'].'</td>
    				<td><a href="'.$row['link'].'" target="_blank">'.$row['link'].'</a></td>
    				<td>黑名单</td>
    				<td>'.$row['dangerlevel'].'</td>
    				<td>
    					<a href="admin.php?start=open&method=del&postid='.$row['postid'].'"><button class="btn" name="del" value="del"">删除</button></a>	<br>
						<a href="admin.php?start=open&method=add&postid='.$row['postid'].'"><button class="btn" name="addToWhite" value="addToWhite"">恢复</button></a>
					</td>
  				</tr>';
	}
}

?>