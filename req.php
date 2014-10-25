<?php

include 'config.php';

function deleteByPostID($id){
	$mysqli = new mysqli($GLOBALS['serverurl'],$GLOBALS['databaseuser'],$GLOBALS['databasepassword'],$GLOBALS['database']);
	$sqlVer="SELECT FROM `unknown` WHERE `postID`='$id'";
	$resV=$mysqli->query($sqlVer);
	if($sqlVer){
		$sql="DELETE FROM `unknown` WHERE `postID`='$id'";
		$res=$mysqli->query($sql);
		if($res){
			return true;
		}else{
			printf("delete Error: %s\n", $mysqli->error);
		}
		$mysqli->close();
	}
}

function addToWhiteByPostID($id){
	$mysqli = new mysqli($GLOBALS['serverurl'],$GLOBALS['databaseuser'],$GLOBALS['databasepassword'],$GLOBALS['database']);
	$sqlV="SELECT * FROM `whitelist` WHERE `postID`='$id'";
	$resV=$mysqli->query($sqlV);
	if($resV){
		$sqlBlack="SELECT * FROM `unknown` WHERE `postID`='$id'";
		$resV=$mysqli->query($sqlBlack);
		while($obj = $resV->fetch_object()){
     	    $link=$obj->link;
     	    $message=$obj->message;
     	    $dangerlevel=$obj->dangerlevel;
     	    $updatetime=$obj->updatetime;
     	    $isapproved=1;
    	}	
		$sql="INSERT INTO ".$GLOBALS['tablename_white']. "(`postID`, `link`, `message`, `Dangerlevel`, `updateTime`, `isapproved`) VALUES 
		('$id', '$link', '$message','$dangerlevel','$updatetime','$isapproved')";
		if($mysqli->query($sql) && deleteByPostID($id)){
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
	$sql="SELECT * FROM `unknown`";
	$result=$mysqli->query($sql);
	$row=$result->fetch_all(MYSQLI_ASSOC);
	$result->free();
	$mysqli->close();
	return $row;
}

function printTable(){
	$row=getData();
	if (is_array($row)) {
		foreach ($row as $key => $value) {
		echo '  				<tr class="mgr-content">
    				<td><input type="checkbox"/></td>
    				<td>'.$value['postID'].'</td>
    				<td>'.$value['message'].'</td>
    				<td>'.$value['updateTime'].'</td>
    				<td><a href="'.$value['link'].'" target="_blank">'.$value['link'].'</a></td>
    				<td>黑名单</td>
    				<td>'.$value['Dangerlevel'].'</td>
    				<td>
    					<a href="admin.php?start=open&method=del&postid='.$value['postID'].'"><button class="btn" name="del" value="del"">删除</button></a>	<br>
						<a href="admin.php?start=open&method=add&postid='.$value['postID'].'"><button class="btn" name="addToWhite" value="addToWhite"">恢复</button></a>
					</td>
  				</tr>';
		}
	}else{
		echo '  				<tr class="mgr-content">
    				<td>暂无数据</td>
    				<td>暂无数据</td>
    				<td>暂无数据</td>
    				<td>暂无数据</td>
    				<td>暂无数据</td>
    				<td>暂无数据</td>
    				<td>暂无数据</td>
    				<td>暂无数据</td>
  				</tr>';
	}

}

?>