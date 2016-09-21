<?php
	
	session_start();
	error_reporting ( 0 );
	include('include/config.php');
	
	if($_GET['action']=='exit'){
	
		session_destroy();
		?>
			<BODY ONLOAD="window.location='login.php'"></BODY>
		<?php
	
	}else{

		if($_SESSION['is_login'] == 1)
		{
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<link href="favicon.ico" type="image/x-icon" rel="icon" /><link href="favicon.ico" type="image/x-icon" rel="shortcut icon" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="stylesheet" type="text/css" href="themed/default/css/style.css" />

	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/functions.js"></script>
	<script type="text/javascript" src="js/pngfix.js"></script>
	<script type="text/javascript" src="js/tiny_mce/tiny_mce.js"></script>

<script type="text/javascript">
	tinyMCE.init({
		mode : "exact",
		elements : "editor",
		theme : "advanced",
		skin : "o2k7",
		plugins : "safari,table,advimage,advlink,emotions,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,inlinepopups,falang",
		theme_advanced_buttons1 : "faen,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect,code",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,|,ltr,rtl,|,fullscreen",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "center",
		directionality : "rtl",
		theme_advanced_statusbar_location : "bottom",
		force_br_newlines: true,
		forced_root_block: "",
		convert_urls : false,
		verify_html : false
	});
</script>
<title>مديريت سامانه پرداخت زرين پال - پنل مديريت</title>
</head>
<body>

<div class="wrapper">
	

	<div class="top_corners"></div>
	
	<div class="main">
		
		<div class="right">
			<div class="block">
				<div class="block_title">
					<h2>منوی مديريت</h2>
				</div>
				<div class="block_content">
				<? include('include/menu.php'); ?>
				</div>
			</div>		</div>
		
		<div class="left">
			<div class="content">
				<div class="content_title">
				<h2>لیست محصولات</h2>
				</div>
				<div class="content_content">
					<div class="msg-error">
						<?php
							if(isset($error))
								echo('<font color="red">'.$error.'</font>');
							if(isset($success))
								echo('<font color="green">'.$success.'</font>');
						?>
					</div>
					<p align="right"><a href="AddProduct.php" class="button">افزودن محصول جديد</a></p>
					<table border="0" class="listTable">
						<tbody>
							<tr>
								<th>شناسه</th>
								<th>نام</th>
								<th>دسته</th>
								<th>قیمت</th>
								<th>ویرایش</th>
								<th>حذف</th>
							</tr>
							<?php
								
								if($_GET['action'] == 'Delete')
									mysql_query("DELETE FROM `product` WHERE `id`='".$_GET['id']."' ");
								
								$list	=	mysql_query("SELECT * FROM `product` ORDER BY `id` DESC   ");
								while($item = mysql_fetch_array($list))
								{
									$list_group	=	mysql_query("SELECT * FROM `group` WHERE `id`='".$item['group']."' ");
									$item_group	=	mysql_fetch_array($list_group);
							?>
								<tr>
									<td><?= $item['id'] ?></td>
									<td><?= $item['name'] ?></td>
									<td><?= $item_group['name'] ?></td>
									<td><?= $item['price'] ?></td>
									<td><a href="ViewProduct.php?id=<?= $item['id'] ?>"><span class="label label-warning">ویرایش</span></a></td>
									<td><a onclick="return confirm('آیا این رکورد حذف شود؟');" href="?action=Delete&id=<?= $item['id'] ?>">حذف</a></td>
								</tr>
							<? } ?>
							
						</tbody>
					</table>
					
				</div>
			</div>
		</div>
		
		<div class="clear"></div>
		
	</div>
	<div class="clear"></div>
	
	
	<div class="bottom_corners"></div>
	
</div>

</body>

</html>
<?php
		
	}else{
	?>
		<BODY ONLOAD="window.location='login.php'"></BODY>
	<?php
	}
}
?>