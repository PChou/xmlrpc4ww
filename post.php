<?php
    include_once("txtdb/txt-db-api.php");
    include_once('util2.php');

    $id=$_GET['id'];
    $db = new Database('blog');
    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        $filename=Escape($_POST['filename']);
        $title=Escape($_POST['title']);
        $layout=Escape($_POST['layout']);
        $description=Escape($_POST['description']);
        $thumbimg=Escape($_POST['thumbimg']);
        $categories=Escape($_POST['categories']);
        $tags=Escape($_POST['tags']);

        $sql = "update blogs set filename='$filename',layout='$layout',description='$description',thumbimg='$thumbimg',categories='$categories',tags='$tags' where id=$id";
        $db->executeQuery($sql);
        $err = txtdbapi_get_last_error();

        if($err == null){
        	$sql = "select filename,content,title,layout,description,thumbimg,categories,tags from blogs where id=$id";
        	$rs = $db->executeQuery($sql);
        	$rs->next();
        	list($filename,$content,$title,$layout,$description,$thumbimg,$categories,$tags) = $rs->getCurrentValues();
        }

        if($err == null){
	        $rs = $db->executeQuery("select count(*) as count from localgit");
	        $rs->next();
	        list($count)=$rs->getCurrentValues();
	        if($count > 0){
	            $rs = $db->executeQuery("select localpath from localgit");
	            $rs->next();
	            list($localpath)=$rs->getCurrentValues();
	            if(!is_dir($localpath)){
	                $err = "$localpath is not a valid path.";
	            }
	            else{
	                $filepath = filePathCombine(filePathCombine($localpath,'_posts'),$filename);
	                $content2 = "---\n";
	                $content2 = $content2 . 'layout: ' . $layout . "\n";
	                $content2 = $content2 . 'title: ' . $title . "\n";
	                $content2 = $content2 . 'description: ' . $description . "\n";
	                $content2 = $content2 . 'thumbimg: ' . $thumbimg . "\n";
	                $content2 = $content2 . 'categories: ' . $categories . "\n";
	                $content2 = $content2 . 'tags: ' . $tags . "\n";
	                $content2 = $content2 . "---\n";
	                $content2 = $content2 . str_replace(BLOGURL,'{{ site.baseurl }}',$content);
	                //save file
	                saveFile(
	                    $filepath
	                    ,$content2);
	            }
	        }
    	}

        if($err == null)
        	$err = 'save success';
    }
    else{
		
	    $db = new Database('blog');
	    $rs = $db->executeQuery("select filename,title,layout,description,thumbimg,categories,tags from blogs where id=$id");
	    $rs->next();
	    list($filename,$title,$layout,$description,$thumbimg,$categories,$tags)=$rs->getCurrentValues();
    }
    

?>

<html>
	<meta charset='UTF-8'>
	<body>
		<form method='post'>
			<table>
				<tr><td>文件名</td><td><input tyep='text' name='filename' value=<?php echo $filename; ?> /></td></tr>
				<tr><td>标题</td><td><input tyep='text' name='title' readonly value=<?php echo $title; ?> /></td></tr>
				<tr><td>layout</td><td><input tyep='text' name='layout' value=<?php echo $layout; ?> /></td></tr>
				<tr><td>简述</td><td><input tyep='text' name='description' value=<?php echo $description; ?> /></td></tr>
				<tr><td>指纹图片</td><td><input tyep='text' name='thumbimg' value=<?php echo $thumbimg; ?> /></td></tr>
				<tr><td>分类</td><td><input tyep='text' name='categories' value=<?php echo $categories; ?> /></td></tr>
				<tr><td>标签</td><td><input tyep='text' name='tags' value=<?php echo $tags; ?> /></td></tr>
				<tr><td><input type='submit' value='保存并生成'/></td></tr>
			</table>
		</form>
		<?php echo $err; ?>
	</body>
</html>