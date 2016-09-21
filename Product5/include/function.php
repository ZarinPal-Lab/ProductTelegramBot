<?php
	
	function curl_custom_postfields($ch, array $assoc = array(), array $files = array()) {

          // invalid characters for "name" and "filename"
          static $disallow = array("\0", "\"", "\r", "\n");

          // build normal parameters
          foreach ($assoc as $k => $v) {
              $k = str_replace($disallow, "_", $k);
              $body[] = implode("\r\n", array(
                  "Content-Disposition: form-data; name=\"{$k}\"",
                  "",
                  filter_var($v),
              ));
          }

          // build file parameters
          foreach ($files as $k => $v) {
              switch (true) {
                  case false === $v = realpath(filter_var($v)):
                  case !is_file($v):
                  case !is_readable($v):
                      continue; // or return false, throw new InvalidArgumentException
              }
              $data = file_get_contents($v);
              $v = call_user_func("end", explode(DIRECTORY_SEPARATOR, $v));
              $k = str_replace($disallow, "_", $k);
              $v = str_replace($disallow, "_", $v);
              $body[] = implode("\r\n", array(
                  "Content-Disposition: form-data; name=\"{$k}\"; filename=\"{$v}\"",
                  "Content-Type: image/jpeg",
                  "",
                  $data,
              ));
          }

          // generate safe boundary
          do {
              $boundary = "---------------------" . md5(mt_rand() . microtime());
          } while (preg_grep("/{$boundary}/", $body));

          // add boundary for each parameters
          array_walk($body, function (&$part) use ($boundary) {
              $part = "--{$boundary}\r\n{$part}";
          });

          // add final boundary
          $body[] = "--{$boundary}--";
          $body[] = "";

          // set options
          return @curl_setopt_array($ch, array(
              CURLOPT_POST       => true,
              CURLOPT_POSTFIELDS => implode("\r\n", $body),
              CURLOPT_HTTPHEADER => array(
                  "Expect: 100-continue",
                  "Content-Type: multipart/form-data; boundary={$boundary}", // change Content-Type
              ),
          ));
      }
	  
	function objectToArray( $object )
	{
		if( !is_object( $object ) && !is_array( $object ) )
		{
			return $object;
		}
		if( is_object( $object ) )
		{
			$object = get_object_vars( $object );
		}
		return array_map( 'objectToArray', $object );
	}
	
	function check_email($email)
	{
		if (preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $email)) {
			echo $email . " is a valid email";
		} else {
			echo $email . " is an invalid";
		}

	}
	
	function escape($string)
    {
        if (get_magic_quotes_gpc()) {
			$string = stripslashes($string);
        }
        $ret = is_numeric($string) ? $string : @mysql_real_escape_string($string);
     
		return $ret;
     
    }
	
	function queryInsert($table, $data = array())
    {
		$field='';
		$column='';
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $field .= '`' . $k . '`, ';
                $column .= '\'' . escape($v) . '\', ';
            }
            $field = substr($field, 0, -2);
            $column = substr($column, 0, -2);
            $ret = "INSERT INTO `$table` ($field) VALUES ($column);";
        } else {
            $this->error($this->errors['queryinsert']);
            $ret = false;
        }
		
        return $ret;
    }

    function queryUpdate($table, $data = array(), $other = null)
    {
		$sql='';
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $v = '\''. escape($v) .'\'';
                $sql .= '`' . $k . '`' . ' = ' . $v . ', ';
            }
            $sql = substr($sql, 0, -2);
            $other = is_null($other) ? null : ' ' . $other;
            $ret = "UPDATE `$table` SET $sql$other;";
        } else {
            $ret = false;
        }
        return $ret;
    }
	
	function execute($query)
    {
        $res = mysql_query($query);
        if (!$res) {
        	return false;
        } else {
            return $res;
        }
    }
	
	function execute2($query)
    {
        $res = mysql_query($query);
        if (!$res) {
        	return false;
        } else {
            return mysql_insert_id();
        }
    }
	
	function genRandomString() {
		
		$length = 32;
		$characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$string = '';    

		for ($p = 0; $p < $length; $p++) {
			$num  = mt_rand(0,61);
			$string .= substr($characters,$num,1);
		}

		return $string;
	} 


?>