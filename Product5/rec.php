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
	$j = 0;
	
	$list_log 	= mysql_query("SELECT * FROM `log` WHERE `user`='".$user_id."' ORDER BY `id` DESC ");
	$item_log 	= mysql_fetch_array($list_log);
	
	function Send_payment($user_id,$order_id)
	{
		$text_reply = 'جزییات سفارش شما: 

';
		$price = 0;
		$list_log 	= mysql_query("SELECT * FROM `log` WHERE `user`='".$user_id."' AND `step`='7' ORDER BY `id` ASC ");
		while($item_log 	= mysql_fetch_array($list_log))
		{
			$list_product	=	mysql_query("SELECT * FROM `product` WHERE `id`='".$item_log['product']."' ");
			$item_product	=	mysql_fetch_array($list_product);
			
			$list_group	=	mysql_query("SELECT * FROM `group` WHERE `id`='".$item_product['group']."' ");
			$item_group	=	mysql_fetch_array($list_group);
			
			$update['step'] = -1;
			$update['order_id'] = $order_id;
			
			$sql = queryUpdate('log', $update, 'WHERE `id` = '.$item_log['id'].';');
			execute($sql);
			
			$price += $item_log['price'];
			$text_reply .= 'دسته: '.$item_group['name'].'
محصول: '.$item_product['name'].'
مبلغ: '.$item_log['price'].' تومان

';
			
			
			
		}
		
		$update['step'] = 8;
		
		$sql = queryUpdate('log', $update, 'WHERE `id` = '.$order_id.';');
		execute($sql);
		
		$list_log 	= mysql_query("SELECT * FROM `log` WHERE `user`='".$user_id."' AND `step`='8' ORDER BY `id` DESC ");
		$item_log 	= mysql_fetch_array($list_log);
		
		$pay_url = 'http://'.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
		$pay_url = str_replace('rec.php','pay.php?id='.$item_log['id'],$pay_url);
		
		$text_reply .='مبلغ کل سفارش: '.$price.' تومان
آدرس: '.$item_log['address'].'
لینک پرداخت شما: 
'.$pay_url.'

لطفاً اطلاعات خرید را در فاکتور بالا بررسی نمایید و در صورت صحت اطلاعات، از طریق لینک پرداخت نسبت به پرداخت فاکتور اقدام نمایید.
توجه: تا کامل شدن عملیات پرداخت صبر کنید، پس از پرداخت اطلاعات خرید در همین ربات به شما نمایش داده خواهد شد.';
		
				
		return $text_reply;
	}
	
	if($item_log['step'] > 0 AND $item_log['step'] < 7)
	{
		if($text == 'Cancel')
		{
			$update['step'] = 0;
			
			$sql = queryUpdate('log', $update, 'WHERE `id` = '.$item_log['id'].';');
			execute($sql);
			
			$text_reply = 'سفارش شما لغو شد.جهت سفارش جدید یکی از دسته های زیر را انتخاب نمایید.';
			$j=0;
			
			$list_type = mysql_query("SELECT * FROM `group` ");
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
						array('بله'),
						array('خیر')
					)
				);
				
				$update['product'] = $item_product['id'];
				$update['price'] = $item_product['price'];
				$update['step'] = 2;
				
				$sql = queryUpdate('log', $update, 'WHERE `id` = '.$item_log['id'].';');
				execute($sql);
				
				$text_reply = 'آیا محصول دیگه ای هم می خواهید؟';
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
			if($text == 'بله')
			{
				$update['step'] = 7;
				
				$text_reply = 'نوع محصول راانتخاب نمایید.';
			
				$j = 0;
				
				$list_type = mysql_query("SELECT * FROM `group` ");
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
				$update['step'] = 3;
				
				$text_reply = 'نام تان را ارسال نمایید';
				
				$replyMarkup = array(
						'keyboard' => array(
							array('Cancel')
						)
					);
			}
			
			$sql = queryUpdate('log', $update, 'WHERE `id` = '.$item_log['id'].';');
			execute($sql);
			
			
		}elseif($item_log['step'] == 3)
		{
			$update['name'] = $text;
			$update['step'] = 4;
			
			$sql = queryUpdate('log', $update, 'WHERE `id` = '.$item_log['id'].';');
			execute($sql);
			
			$text_reply = 'موبایل تان را ارسال نمایید';
			$replyMarkup = array(
					'keyboard' => array(
						array('Cancel')
					)
				);
		}elseif($item_log['step'] == 4)
		{
			$list_logs 	= mysql_query("SELECT * FROM `log` WHERE `user`='".$user_id."' AND `address` != '' ORDER BY `id` DESC ");
			$item_logs 	= mysql_fetch_array($list_logs);
			
			if($item_logs['id'] > 0)
			{
				$update['email'] = $text;
				$update['step'] = 5;
				
				$sql = queryUpdate('log', $update, 'WHERE `id` = '.$item_log['id'].';');
				execute($sql);
			
				$text_reply = 'آدرس قبلی شما : 
'.$item_logs['address'].'

آیا این آدرس مورد تایید می باشد؟';
				$replyMarkup = array(
						'keyboard' => array(
							array('بله'),
							array('خیر، ثبت آدرس جدید')
						)
					);
			}else{
				
				$update['email'] = $text;
				$update['step'] = 6;
				
				$sql = queryUpdate('log', $update, 'WHERE `id` = '.$item_log['id'].';');
				execute($sql);
			
				$text_reply = 'آدرس تان را ارسال نمایید';
				$replyMarkup = array(
						'keyboard' => array(
							array('Cancel')
						)
					);
			}
		}
		elseif($item_log['step'] == 5)
		{
			if($text == 'بله')
			{
				$list_logs 	= mysql_query("SELECT * FROM `log` WHERE `user`='".$user_id."' AND `address` != '' ORDER BY `id` DESC ");
				$item_logs 	= mysql_fetch_array($list_logs);
			
				$update['address'] = $item_logs['address'];
				$update['step'] = 7;
				
				$sql = queryUpdate('log', $update, 'WHERE `id` = '.$item_log['id'].';');
				execute($sql);
				
				$text_reply = Send_payment($user_id,$item_log['id']);
				
				$list_type = mysql_query("SELECT * FROM `group` ");
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
				$update['step'] = 6;
				
				$sql = queryUpdate('log', $update, 'WHERE `id` = '.$item_log['id'].';');
				execute($sql);
				
				$text_reply = 'آدرس تان را ارسال نمایید';
				
				$replyMarkup = array(
						'keyboard' => array(
							array('Cancel')
						)
					);
			}
		}elseif($item_log['step'] == 6)
		{
			$update['address'] = $text;
			$update['step'] = 7;
			
			$sql = queryUpdate('log', $update, 'WHERE `id` = '.$item_log['id'].';');
			execute($sql);
			
			$text_reply = Send_payment($user_id,$item_log['id']);
			
			$list_type = mysql_query("SELECT * FROM `group` ");
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
			
			$list_type = mysql_query("SELECT * FROM `group` ");
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
		$list_group = mysql_query("SELECT * FROM `group` WHERE `id`='".$group[0]."' ");
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
			
			$list_setting = mysql_query("SELECT * FROM `setting` ");
			$item_setting = mysql_fetch_array($list_setting);
			
			$url = 'https://api.telegram.org/bot'.$item_setting['token'].'/sendPhoto';

			$array1=array('chat_id'=>$user_id);
			$array2=array('photo'=>"Pic/menu".$item_group['id'].".jpg"); //path
			$ch = curl_init();       
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_custom_postfields($ch,$array1,$array2); 

			$result = curl_exec($ch);
			curl_close($ch);
			
		}else{
			$text_reply = 'دسته انتخابی صحیح نمی باشد.';
			$j = 0;
			
			$list_type = mysql_query("SELECT * FROM `group` ");
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

	$check = curl_exec( $ch );
	
	echo('OK!');
	
?>