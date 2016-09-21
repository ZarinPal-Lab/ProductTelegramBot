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
			if($_POST)
			{
				include('include/function.php');
				
				$update['serial'] 	= $_POST['serial'];
				$update['code'] 	= $_POST['code'];
				$update['product'] 	= $_POST['product'];
				$update['status']	= 0;
				
				$sql = queryUpdate('cart', $update, 'WHERE `id` = '.$_GET['id'].';');
				execute($sql);
			}
			
			$list	=	mysql_query("SELECT * FROM `cart` WHERE `id`='".$_GET['id']."' ");
			$item 	= 	mysql_fetch_array($list);
			
			$list_product	=	mysql_query("SELECT * FROM `product` WHERE `id`='".$item['product']."' ");
			$item_product 	= 	mysql_fetch_array($list_product);
			
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
	<script type="text/javascript" src="js/Product.js"></script>

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
					<form id="UserAddForm" method="post" action="">
						<div class="input text">
							<label for="username">سریال</label>
							<input name="serial" type="text" maxlength="35" value="<?= $item['serial'] ?>" id="username" />
						</div>
						<div class="input text">
							<label for="username">کد شارژ</label>
							<input name="code" type="text" maxlength="35" value="<?= $item['code'] ?>" id="username" />
						</div>
						<div class="input text">
							<label for="username">دسته</label>
							<select name="group" id="group" class="hlf-circle i">
								<option value="0">- None - </option>
								<?php
									
									$list_type = mysql_query("SELECT * FROM `group` ");
									while($item_type = mysql_fetch_array($list_type)){
								?>
									 <option value="<?= $item_type['id'] ?>" <? if($item_type['id'] == $item_product['group']) echo('selected="selected"'); ?> " ><?= $item_type['name'] ?></option>
								<? } ?>
							</select>
						</div>
						<div class="input text">
							<label for="username">محصول</label>
							<select name="product" id="product" class="hlf-circle i">
								<?php
									$list_type = mysql_query("SELECT * FROM `product` WHERE `group`='".$item_product['group']."' ");
									while($item_type = mysql_fetch_array($list_type)){
								?>
									 <option value="<?= $item_type['id'] ?>" <? if($item_type['id'] == $item['product']) echo('selected="selected"'); ?> " ><?= $item_type['name'] ?></option>
								<? } ?>
							</select>
						</div>
						<div class="submit"><input type="submit" value="ثبت" /></div>
					</form>	
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