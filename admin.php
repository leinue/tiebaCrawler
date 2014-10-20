<!DOCTYPE html>
<html>
<head>
	<title>FB后台</title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="style/css.css">
</head>
<body>

	<div class="main">
		<div class="main-title">
			<h1>Administration Panel of FB Filter</h1>
		</div>
		<div class="mgr-box">
			<table width="960">
				<tr>
    				<th>选择</th>
    				<th>ID</th>
    				<th>文章内容</th>
    				<th>创建时间</th>
    				<th>更新时间</th>
    				<th>分类</th>
    				<th>操作</th>
  				</tr>
  				<tr class="mgr-content">
    				<td><input type="checkbox"/></td>
    				<td>1</td>
    				<td>哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈</td>
    				<td>2014-03-02</td>
    				<td>2014-03-02</td>
    				<td>白名单</td>
    				<td>
    					<button class="btn" name="del" value="del" onClick="deleteByPostID(postID)">删除</button><br>
						<button class="btn" name="addToWhite" value="addToWhite" onClick="addToWhiteByPostID(postID)">恢复</button>
					</td>
  				</tr>
  				<tr class="mgr-content">
    				<td><input type="checkbox"/></td>
    				<td>1</td>
    				<td>哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈</td>
    				<td>2014-03-02</td>
    				<td>2014-03-02</td>
    				<td>白名单</td>
    				<td>
    					<button class="btn" name="del" value="del" onClick="deleteByPostID(postID)">删除</button><br>
						<button class="btn" name="addToWhite" value="addToWhite" onClick="addToWhiteByPostID(postID)">恢复</button>
					</td>
  				</tr>
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
