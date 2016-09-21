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
				<h2>لیست سفارشات</h2>
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
					
					<table border="0" class="listTable">
						<tbody>
							<tr>
								<th>شناسه</th>
								<th>نام</th>
								<th>ایمیل</th>
								<th>دسته</th>
								<th>محصول</th>
								<th>تاریخ</th>
								<th>وضعیت</th>
								<th>عملیات</th>
							</tr>
							<?php
								$start = 0;
								if($_GET['page'] > 0)
									$start = ($_GET['page'] - 1)*30;
								
								$list	=	mysql_query("SELECT * FROM `log` WHERE `step` > '0' ORDER BY `id` DESC LIMIT $start,30  ");
								while($item = mysql_fetch_array($list))
								{
									$list_product	=	mysql_query("SELECT * FROM `product` WHERE `id`='".$item['product']."' ");
									$item_product	=	mysql_fetch_array($list_product);
									
									$list_group	=	mysql_query("SELECT * FROM `group` WHERE `id`='".$item_product['group']."' ");
									$item_group	=	mysql_fetch_array($list_group);
									
									if($item['step'] == 9)
										$status = '<td><font color="green">پرداخت شده</font></td>';
									else
										$status = '<td><font color="red">پرداخت نشده</font></td>';	

									
									
							?>
								<tr>
									<td><?= $item['id'] ?></td>
									<td><?= $item['name'] ?></td>
									<td><?= $item['email'] ?></td>
									<td><?= $item_group['name'] ?></td>
									<td><?= $item_product['name'] ?></td>
									<td><?= $item['date'] ?></td>
									<?= $status ?>
									<td><a href="ViewOrder.php?id=<?= $item['id'] ?>"><span class="label label-warning">مشاهده</span></a></td>
								</tr>
							<? } ?>
							
						</tbody>
					</table>
					<div align="center" class="paginate">
					<?php
						
						$list	=	mysql_query("SELECT * FROM `log` ");
						$records = mysql_num_rows($list);
						$pages = ceil($records/30);
						
						for($j=1;$j<= $pages;$j++)
						{
							if($_GET['page'] == $j){
					?>
							<a href="?page=<?= $j ?>"> <?= $j ?></a>
					<? 		}else{ ?>
							<div class="disabled"><?= $j ?></div>
					<? 		}
						}
					?>
					</div>
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