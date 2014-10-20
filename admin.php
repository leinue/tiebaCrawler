<!DOCTYPE html>
<html>
<head>
	<title>FB后台</title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="style/css.css">
</head>
<body>
<?php

require('req.php');

$res=getReq();
switch ($res) {
	case 'close':
		$monitor="一切就绪";
		break;
	case '0':
		$monitor="参数不合法";
		break;
	case '1';
		$monitor="操作成功";
		break;
	case '-1';
		$monitor="操作失败";
		break;
	case '-2':
		$monitor="参数错误";
		break;
	case '-3':
		$monitor="重复操作";
		break;
	default:
		$monitor="未知错误";
		break;
}
?>
	<div class="main">
		<div class="main-title">
			<h1>Administration Panel of FB Filter</h1>
		</div>
		<div class="wrong-display">
			<div class="wrong-header">错误监控台</div>
			<div class="wrong-content">
				<span>目前状态：<?php echo $monitor; ?></span>
			</div>
		</div>
		<div class="mgr-box">
			<table width="960">
				<tr>
    				<th>选择</th>
    				<th>ID</th>
    				<th>文章内容</th>
    				<th>创建时间</th>
    				<th>链接</th>
    				<th>分类</th>
    				<th>危险等级</th>
    				<th>操作</th>
  				</tr>
  				<tr class="mgr-content">
    				<td><input type="checkbox"/></td>
    				<td>1</td>
    				<td>哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈</td>
    				<td>2014-03-02</td>
    				<td><a href="" target="_blank">http://tieba.baidu.com/f?kw=%C3%A8%C4%EF%BC%C6%BB%AE</a></td>
    				<td>白名单</td>
    				<td>0</td>
    				<td>
    					<a href="admin.php?start=open&method=del&postid=654564156_58546846"><button class="btn" name="del" value="del" onClick="deleteByPostID(postID)">删除</button></a>	<br>
						<a href="admin.php?start=open&method=add&postid=654564156_58546846"><button class="btn" name="addToWhite" value="addToWhite" onClick="addToWhiteByPostID(postID)">恢复</button></a>
					</td>
  				</tr>
  				<?php printTable(); ?>
			</table>
		</div>
		<div class="whole-control">
			<input type="checkbox"/>全选</li>
    		<button class="btn" name="del" value="del" onClick="deleteByPostID(postID)">删除</button>
			<button class="btn" name="addToWhite" value="addToWhite" onClick="addToWhiteByPostID(postID)">恢复</button>
		</div>
	</div>
	<div class="footer">
		<div class="info">
			@2014 FB filter group
		</div>
	</div>
</body>
</html>
