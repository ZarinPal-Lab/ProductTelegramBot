<?php
	error_reporting ( 0 );
	include('include/config.php');
	
	if($_GET['action'] == 'group')
	{
		$list	=	mysql_query("SELECT * FROM `product` WHERE `group`='".$_REQUEST['name']."' ORDER BY `id` ASC ");
		while($item	=	mysql_fetch_array($list))
			$text .= $item['id'].',,,'.$item['name'].',,,';
		
		echo($text);
	}
	
?>
