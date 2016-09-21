<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head id="Head1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<title>پرداخت</title>
<?php
	
	include('include/config.php');
	include('include/function.php');
	
	$list_log 	= mysql_query("SELECT * FROM `log` WHERE `id`='".$_GET['id']."' ");
	$item_log 		= mysql_fetch_array($list_log);
	if($item_log['step'] != 8)
		echo('این پرداخت قبلا انجام شده است یا سفارش هنوز کامل نشده است');
	else{
		
		$price = 0;
		$list_log 	= mysql_query("SELECT * FROM `log` WHERE `order_id`='".$item_log['order_id']."' ORDER BY `id` ASC ");
		while($item_logs 	= mysql_fetch_array($list_log))
			$price += $item_logs['price'];
		
		$update['step'] = 8;
		
		$sql = queryUpdate('log', $update, 'WHERE `id` = '.$item_log['id'].';');
		execute($sql);
		
		$list_setting 	= mysql_query("SELECT * FROM `setting` ");
		$item_setting 		= mysql_fetch_array($list_setting);
	
		$callBackUrl = 'http://'.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
		$callBackUrl = str_replace('pay.php','callback.php',$callBackUrl);
		
		$client = new SoapClient('https://www.zarinpal.com/pg/services/WebGate/wsdl');

		$result = $client->PaymentRequest(
											array(
												'MerchantID' => $item_setting['merchant'],
												'Amount' => $price,
												'Description' => 'پرداخت فاکتور شماره '.$item_log['id'],
												'Email' => $item_log['email'],
												'Mobile' => $item_log['mobile'],
												'CallbackURL' => $callBackUrl,
											)
		);
		
		mysql_query("UPDATE `log` SET `res2`='".$result->Authority."'  WHERE `id`='".$item_log['id']."'");
		?>		
		<BODY ONLOAD="window.location='https://www.zarinpal.com/pg/StartPay/<?= $result->Authority ?>'"></BODY>				
	<? } ?>