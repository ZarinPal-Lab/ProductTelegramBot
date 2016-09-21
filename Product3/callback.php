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
	if($item_log['step'] == 4)
	{
		$Authority = $_GET['Authority'];
		if ($_GET['Status'] == 'OK')
		{
			$list_setting 	= mysql_query("SELECT * FROM `setting` ");
			$item_setting 		= mysql_fetch_array($list_setting);
		
			$client = new SoapClient('https://www.zarinpal.com/pg/services/WebGate/wsdl');

			$result = $client->PaymentVerification(array(
				'MerchantID'     => $item_setting['merchant'],
				'Authority'      => $Authority,
				'Amount'         => $item_log['price'],
			));
			
			if ($result->Status == 100)
			{
				include ('include/jdf.php');
	
				$day_number = jdate('j'); 
				$month_number = jdate('n'); 
				$year_number = jdate('Y'); 
				$time = jdate ('H:i:s');
				
				$day = $year_number.'/'.$month_number.'/'.$day_number.' - '.$time;
	
				echo 'Transation success. RefID:'.$result->RefID;
				
				$list_cart 	= mysql_query("SELECT * FROM `cart` WHERE `product`='".$item_log['product']."' AND `status`='0' ");
				$item_cart 		= mysql_fetch_array($list_cart);
				
				if($item_cart['id'] > 0)
				{
					$update['order_id'] = $item_log['id'];
					$update['status'] = 1;
					$update['date'] = $day;
				
					$sql = queryUpdate('cart', $update, 'WHERE `id` = '.$item_cart['id'].';');
					execute($sql);
					unset($update);
					
					if($item_cart['serial'] != '')
						$status ='سریال: '.$item_cart['serial'].'
';
					if($item_cart['code'] != '')
						$status .='کد: '.$item_cart['code'].'
';
					
				}else
					$status ='در دریافت کارت مشکلی به وجود آمد.';
				
				$update['step'] = 5;
				$update['res1'] = $result->RefID;
				$update['log_sharj'] = $res->TranId;
			
				$sql = queryUpdate('log', $update, 'WHERE `id` = '.$item_log['id'].';');
				execute($sql);
				
				$text_reply = 'پرداخت شما با موفقیت به پایان رسید.
کد پیگیری شما: '.$result->RefID.'
شناسه پرداخت: '.$item_log['id'] .'
'.$status ;
				
				$j = 0;
				
				$list_type = mysql_query("SELECT * FROM `group` WHERE `status`='1' ");
				while($item_type = mysql_fetch_array($list_type))
				{
					$keys[$j] = array($item_type['id'].'. '.$item_type['name']);
					$j++;
				}
				$replyMarkup = array(
					'keyboard' => 
						$keys
					
				);
				
				$encodedMarkup = json_encode($replyMarkup);
				$url = 'https://api.telegram.org/bot'.$item_setting['token'].'/sendMessage';

				$ch = curl_init( );
				curl_setopt( $ch, CURLOPT_URL, $url );
				curl_setopt( $ch, CURLOPT_POST, 1 );
				curl_setopt( $ch, CURLOPT_POSTFIELDS, "text=".$text_reply."&chat_id=".$item_log['user']."&reply_markup=" .$encodedMarkup);
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $ch, CURLOPT_TIMEOUT, 500 );
				$agent = $_SERVER["HTTP_USER_AGENT"];
				curl_setopt($ch, CURLOPT_USERAGENT, $agent);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

				$check = curl_exec( $ch );
			} else {
				echo 'Transation failed. Status:'.$result->Status;
			}
		} else {
			echo 'Transaction canceled by user';
		}
	}else
		echo('این پرداخت قبلا انجام شده است');
?>