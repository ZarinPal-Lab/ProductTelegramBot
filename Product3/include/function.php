<?php
	
	function check_number($list_nums)
	{
		$num = trim($list_nums);
		if(strlen($num) == 11)
			$num = substr($num,1);
		if(strlen($num) == 12)
			$num = substr($num,2);
		if(strlen($num) == 13)
			$num = substr($num,3);
		
		return $num;
	
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