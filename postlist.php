<?php
    include_once("txtdb/txt-db-api.php");

    $db = new Database('blog');
    $rs = $db->executeQuery('select id,filename,title,layout,description,thumbimg,categories,tags from blogs order by id desc');

?>

<html>
	<head>
		<meta charset='UTF-8'>
		<script type="text/javascript" src="content/jquery.js"></script>
		<script type="text/javascript" src="jquery.qrcode.min.js"></script>
		<script type="text/javascript" src="Modernizr.js"></script>
		<link type="text/css" rel="stylesheet" href="content/bootstrap3.css">
		
		<script type="text/javascript">
			function showqrcode() {
				var opt = { text : window.location.href,width:100,height:100 };
				$('#gEwm').show();
				if(!Modernizr.canvas){
					$.extend(opt,{ render : "table" });
				}
				if($('#qrcode').html() == ''){
					$('#qrcode').qrcode(opt);
				}
				return false;
			}

			$(function(){
				
				$('.close').click(function(){
					$('#gEwm').hide();
				});
				$('#gEwm').hide();
			})
		</script>
		<style type="text/css">

			td div{
				width: 300px;
				white-space: nowrap;
				text-overflow: ellipsis;
				overflow: hidden;
			}

			.gbtn {
				top: 188px;
				position: fixed;
				right: 0;
				width: 38px;
				z-index: 10009;
				-position: absolute;
			}

				.gbtn .ewm {
					display: block;
					background-position: -39px -106px;
				}
				.gbtn .ewm:hover {
					background-position: -156px -106px;
				}

				.gbtn a {
					position: relative;
					display: block;
					margin: 0 0 5px 0;
					padding: 0;
					width: 38px;
					height: 38px;
					background: url(global4_29893.png) no-repeat 0 -106px;
					text-decoration: none;
				}

				.gbtn #qrcode{
					position: relative;
					top:20px;
				}
			.gewm {
				display: block;
				position: fixed;
				bottom: 20px;
				width: 102px;
				border: 1px solid #EDEDED;
				z-index: 10009;
				-position: absolute;
				-bottom: auto;
			}

				.gewm .close {
					position: absolute;
					right: 8px;
					top: 8px;
					display: block;
					width: 8px;
					height: 8px;
					background: url("global4_29893.png") no-repeat -43px -68px;
					z-index: 2;
					overflow: hidden;
					text-decoration: none;
				}
		</style>
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
			<div id="gBtn" class="gbtn">
				<a href="#" class="ewm" title="在手机上慢慢读" onclick="showqrcode(this);" >&nbsp;</a>
				<div id="gEwm" class="gewm" style="left: 1165px;">
					<div id="qrcode"></div>
					<a href="#" class="close" title="关闭"></a>
				</div>
			</div>
		</div>
		<script src="content/bootstrap3.js"></script>
	</body>
</html>