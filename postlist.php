<?php
    include_once("txtdb/txt-db-api.php");
    
    $db = new Database('blog');
    $rs = $db->executeQuery('select id,filename,title,layout,description,thumbimg,categories,tags from blogs order by id desc');

?>

<html>
	<head>
		<meta charset='UTF-8'>
		<link type="text/css" rel="stylesheet" href="content/bootstrap3.css">
	</head>
	<body>
		<div class='container'>

			<table class='table table-striped'>
				<tr>
					<td>文件名</td>
					<td>标题</td>
					<td>layout</td>
					<td>简述</td>
					<td>指纹图片</td>
					<td>分类</td>
					<td>标签</td>
					<td></td>
				</tr>
				<?php while($rs->next()) { 
					list($id,$filename,$title,$layout,$description,$thumbimg,$categories,$tags) = $rs->getCurrentValues();
					?>
					<tr>
						<td><?php echo $filename; ?></td>
						<td><?php echo $title; ?></td>
						<td><?php echo $layout; ?></td>
						<td><div><?php echo $description; ?></div></td>
						<td><?php echo $thumbimg; ?></td>
						<td><?php echo $categories; ?></td>
						<td><?php echo $tags; ?></td>
						<td><a target='_blank' href=<?php echo '"post.php?id=' . $id . '"' ?>>编辑和发布</a></td>
					</tr>
				<?php } ?>
			</table>
		</div>
		<script type="text/javascript" src="content/jquery.js"></script>
		<script src="content/bootstrap3.js"></script>
	</body>
</html>