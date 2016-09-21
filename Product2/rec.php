<?php

	include ('include/config.php');
	include ('include/function.php');
	include ('include/jdf.php');
	
	$day_number = jdate('j'); 
	$month_number = jdate('n'); 
	$year_number = jdate('Y'); 
	$time = jdate ('H:i:s');
	
	$day = $year_number.'/'.$month_number.'/'.$day_number.' - '.$time;
	
	
	$string 	= json_decode(file_get_contents('php://input'));
	$result 	= objectToArray($string);
	$user_id 	= $result['message']['from']['id'];
	$text 		= $result['message']['text'];
	
	$list_log 	= mysql_query("SELECT * FROM `log` WHERE `user`='".$user_id."' ORDER BY `id` DESC ");
	$item_log 		= mysql_fetch_array($list_log);
	
	if($item_log['step'] > 0 AND $item_log['step'] < 4)
	{
		if($text == 'Cancel')
		{
			$update['step'] = 0;
			
			$sql = queryUpdate('log', $update, 'WHERE `id` = '.$item_log['id'].';');
			execute($sql);
			
			$text_reply = 'سفارش شما لغو شد.جهت سفارش جدید یکی از دسته های زیر را انتخاب نمایید.';
			$j=0;
			
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
		}elseif($item_log['step'] == 1)
		{
			$product = explode('.',$text);
			$list_product = mysql_query("SELECT * FROM `product` WHERE `id`='".$product[0]."' ");
			$item_product = mysql_fetch_array($list_product);
			
			if($item_product['id'] > 0)
			{
				$replyMarkup = array(
					'keyboard' => array(
						array('Cancel')
					)
				);
				
				$update['product'] = $item_product['id'];
				$update['price'] = $item_product['price'];
				$update['step'] = 2;
				
				$sql = queryUpdate('log', $update, 'WHERE `id` = '.$item_log['id'].';');
				execute($sql);
				
				$text_reply = 'شماره موبایل تان را ارسال نمایید.';
			}else{
				$text_reply = 'محصول انتخابی صحیح نمی باشد.';
				$j = 0;
				
				$list_type = mysql_query("SELECT * FROM `product` WHERE `group`='".$item_log['group']."' ");
				while($item_type = mysql_fetch_array($list_type))
				{
					$keys[$j] = array($item_type['id'].'. '.$item_type['name']);
					$j++;
				}
				$replyMarkup = array(
					'keyboard' => 
						$keys
					
				);
			}
		}elseif($item_log['step'] == 2)
		{
			$update['mobile'] = check_number($text);
			$update['step'] = 3;
			
			$sql = queryUpdate('log', $update, 'WHERE `id` = '.$item_log['id'].';');
			execute($sql);
			
			$text_reply = 'ایمیل تان راارسال نمایید.';
			
			$replyMarkup = array(
					'keyboard' => array(
						array('Cancel')
					)
				);
		}elseif($item_log['step'] == 3)
		{
			$update['email'] = $text;
			$update['step'] = 4;
			
			$sql = queryUpdate('log', $update, 'WHERE `id` = '.$item_log['id'].';');
			execute($sql);
			
			$pay_url = 'http://'.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
			
			$pay_url = str_replace('rec.php','pay.php?id='.$item_log['id'],$pay_url);
			
			$list_product	=	mysql_query("SELECT * FROM `product` WHERE `id`='".$item_log['product']."' ");
			$item_product	=	mysql_fetch_array($list_product);
			
			$list_group	=	mysql_query("SELECT * FROM `group`  WHERE `status`='1' AND `id`='".$item_product['group']."' ");
			$item_group	=	mysql_fetch_array($list_group);
			
			$text_reply = 'فاکتور خرید
شماره: '.$item_log['mobile'].'
ایمیل: '.$text.'
دسته: '.$item_group['name'].'
محصول: '.$item_product['name'].'
مبلغ: '.$item_log['price'].' تومان
لینک پرداخت شما: 
'.$pay_url.'

لطفاً اطلاعات خرید را در فاکتور بالا بررسی نمایید و در صورت صحت اطلاعات، از طریق لینک پرداخت نسبت به پرداخت فاکتور اقدام نمایید.
توجه: تا کامل شدن عملیات پرداخت صبر کنید، پس از پرداخت اطلاعات خرید در همین ربات به شما نمایش داده خواهد شد.';

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
		}else{
			$text_reply = 'نوع محصول راانتخاب نمایید.';
			
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
		}
		
	}else{
		
		$group = explode('.',$text);
		$list_group = mysql_query("SELECT * FROM `group` WHERE `status`='1' AND `id`='".$group[0]."' ");
		$item_group = mysql_fetch_array($list_group);
		
		if($item_group['id'] > 0)
		{
			$j = 0;
			
			$list_product = mysql_query("SELECT * FROM `product` WHERE `group` = '".$item_group['id']."' ");
			while($item_type = mysql_fetch_array($list_product))
			{
				$keys[$j] = array($item_type['id'].'. '.$item_type['name']);
				$j++;
			}
			$replyMarkup = array(
				'keyboard' => 
					$keys
				
			);
			
			$insert['user'] = $user_id;
			$insert['group'] = $item_group['id'];
			$insert['date'] = $day;
			$insert['step'] = 1;
			
			$sql 	= queryInsert('log', $insert);
			execute($sql);
			
			$text_reply = 'محصول درخواستی را انتخاب نمایید.';
		}else{
			$text_reply = 'دسته انتخابی صحیح نمی باشد.';
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
		}
			
	}
	
	$list_setting = mysql_query("SELECT * FROM `setting` ");
	$item_setting = mysql_fetch_array($list_setting);
	
	$encodedMarkup = json_encode($replyMarkup);
	$url = 'https://api.telegram.org/bot'.$item_setting['token'].'/sendMessage';

	$ch = curl_init( );
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_POST, 1 );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, "text=".$text_reply."&chat_id=".$user_id."&reply_markup=" .$encodedMarkup);
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_TIMEOUT, 500 );
	$agent = $_SERVER["HTTP_USER_AGENT"];
	curl_setopt($ch, CURLOPT_USERAGENT, $agent);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

	$check = curl_exec( $ch );
	
	echo('OK!');
	
?>