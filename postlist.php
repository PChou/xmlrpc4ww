<?php
    include_once("txtdb/txt-db-api.php");

    $db = new Database('blog');
    $rs = $db->executeQuery('select id,filename,title,layout,description,thumbimg,categories,tags from blogs order by id desc');

?>

<html>
	<meta charset='UTF-8'>
	<body>
		<table>
			<tr>
				<td>文件名</td>
				<td>标题</td>
				<td>layout</td>
				<td>简述</td>
				<td>指纹图片</td>
				<td>分类[c1,c2]</td>
				<td>标签[t1,t2]</td>
				<td></td>
			</tr>
			<?php while($rs->next()) { 
				list($id,$filename,$title,$layout,$description,$thumbimg,$categories,$tags) = $rs->getCurrentValues();
				?>
				<tr>
					<td><?php echo $filename; ?></td>
					<td><?php echo $title; ?></td>
					<td><?php echo $layout; ?></td>
					<td><?php echo $description; ?></td>
					<td><?php echo $thumbimg; ?></td>
					<td><?php echo $categories; ?></td>
					<td><?php echo $tags; ?></td>
					<td><a target='_blank' href=<?php echo '"post.php?id=' . $id . '"' ?>>编辑和发布</a></td>
				</tr>
			<?php } ?>
		</table>
	</body>
</html>