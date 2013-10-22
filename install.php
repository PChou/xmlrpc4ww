<?php
	include('txtdb/txt-db-api.php');
	include('util2.php');

	$db = new Database('blog');
	$out_localpath = '';
	$out_layout = '';
	$err = '';
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$out_localpath = $_POST['config_file_path'];
		$out_layout = $_POST['config_layout'];
		if(!is_dir($out_localpath)){
			$err = "$out_localpath is not a valid path.";
		}
		else{
			$new_localpath = Escape($out_localpath);
			$new_layout = Escape($out_layout);
			$rs = $db->executeQuery("select count(*) as count from localgit");
			$rs->next();
			list($count)=$rs->getCurrentValues();
			$sql = '';
			if($count > 0){
				$sql = "update localgit set localpath = '$new_localpath',layout = '$new_layout'";
			}
			else{
				$sql = "insert into localgit (localpath,layout) values('$new_localpath','$new_layout')";
			}

			$db->executeQuery($sql);

			$err = txtdbapi_get_last_error();
			if($err == null){
				$err = 'config success';
			}
		}
	}
	else
	{
		$rs = $db->executeQuery('select localpath,layout from localgit');
		$rs->next();
		list($localpath,$layout)=$rs->getCurrentValues();
		$out_localpath = $localpath;
		$out_layout = $layout;
		if(!is_dir($out_localpath)){
			$err = "$out_localpath is not a valid path.";
		}
	}

?>

<html>
<head>
	<meta charset='utf-8'/>
	<script type="text/javascript" src="content/jquery.js"></script>
	<link type="text/css" rel="stylesheet" href="content/bootstrap3.css">
</head>
<body>
	<div class='container'>
	<pre><?php echo $err; ?></pre>
	<form method='post'>
		<table>
			<tr>
				<td>choose git project path:</td>
				<td><input id='config_file_path' type='text' name='config_file_path' value='<?php echo $out_localpath; ?>' /></td>
			</tr>
			<tr>
				<td>default layout:</td>
				<td><input id='config_layout' type='text' name='config_layout' value='<?php echo $out_layout; ?>' /></td>
			</tr>
			<tr>
				<td><input type='submit' value='submit'/></td>
			</tr>
		</table>
	</form>
	</div>
	<script src="content/bootstrap3.js"></script>
</body>
</html>