<?php

	
	/** dbl section 
	*/

	
	
	
	
	
	/** возвращатель результатов
	*/
	function dbl_get($query, $conf = '') {
	
		$items = db_query($query, null, $conf);

		if (sizeof($items) == 1 and strripos($query, 'LIMIT 1') !== FALSE)
			$result =  $items[0];
		else
			$result = $items;
		
		return $result;
		
	}
	





	/**
	* хранитель конфигураций
	*/
	function dbl_config($name = '', $connect = '', $user = 'root', $pwd = '') {
		
		static $config = array();

		
		if ($connect == ''){
			if (count($config) > 0) {
				if ($name == '') {
					return current($config);
				}
				elseif (isset($config[$name])){
					return $config[$name];
				}
			}

		}	
		else {
			
			$config[$name] = ['user' => $user, 'pwd' => $pwd, 'connect' => $connect];
			
			return True;
		}

		return False;

	}	


	

	function db_config($name = '', $connect = '', $user = 'root', $pwd = ''){
		return dbl_config($name, $connect, $user, $pwd);
	}	
		

	/**
	* хранитель коннектов
	*/
	function db_conn($name = '') {

		static $connects = array();


		/* кешируем коннект */
		if (isset($connects[$name])){
			return $connects[$name];
		}


		if (is_array($config = db_config($name))) {


			$mysql = new PDO($config['connect'], $config['user'], $config['pwd']);
			//$mysql =  new mysqli($config['host'], $config['user'], $config['pwd'], $config['db']);
			//$mysql->query('SET NAMES UTF8');

			$mysql->query("SET NAMES 'utf8'");
			$connects[$name] = $mysql;
			
			return $mysql;

		}	
		
		else
			return False;

	}	


	/**
	* исполнитель запросов
	*/
	function db_query($query, $data = array(), $conf = ''){

		$conn = db_conn($conf);

		$result = $conn->prepare($query);
		
		if (is_array($data)){
			$res = $result->execute($data);
		}
		else
			$res = $result->execute();

		if (!$res) {
			return $result->errorInfo();
		}
		else {
		
    		if (strripos($query, 'INSERT INTO') === 0) {
        		return $conn->lastInsertId();
    		}
        	else
        		return $result->fetchAll(PDO::FETCH_ASSOC);
        }	

	}	



	/**
	* возвращатель результатов
	*/
	function db_get($query, $conf = '') {
	
		return dbl_get($query, $conf);
		
	}

	


	



	function db_insert($table, $items = array(), $conf = '') {

		foreach($items as $key => $item){
        
        	$item = trim($item);

        	if ($item !== '') {
            	
            	/*if (isset($columns))
            		$columns .= ',';
               	$columns .= $key;
          		
            	if (isset($values))
            		$values .= ',';
          		$values .= ':'.$key;*/

          		if (isset($columns))
            		$columns .= ',';
               	$columns .= '`'.$key.'`';
          		
            	if (isset($values))
            		$values .= ',';
          		$values .= '"'.addslashes($item).'"';
        	
        	}  
 	 

      	}

      	//$query = 'INSERT INTO `'.$table.'` ('.$columns.') VALUES('.$values.');';

      	$query = 'INSERT INTO `'.$table.'` ('.$columns.') VALUES('.$values.');';

      	//$result = db_query($query, $items, $conf);

      	$result = db_query($query, null, $conf);

      	//if ($result == 0)
      	//	echo "$query \n";


		
		return $result;
	}


		


		
