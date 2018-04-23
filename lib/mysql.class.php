<?php
	include_once("config.php");
	class mysql{
	  /**  
       * ?¥è总æ敡ã
       *  
       * @var int  
       */  
      var $querynum = 0;   
      /**  
       * 连?¥句柄  
       *  
       * @var object  
       */  
      var $link;   
      
      /**  
       * 定义一些默认的?¨¦  
       */  
      private $dbhost = g_dbhost;
      private $dbname = g_dbname;
      private $dbuser = g_dbuser;
      private $dbpw = g_dbpassword;
      private $dbcharset = g_dbcharset;
      
      /**  
       * ?¨¦??½敡ã
       *  
       * @param string $dbhost 主机名  
       * @param string $dbuser ?¨æ  
       * @param string $dbpw   密??  
       * @param string $dbname ?°æ库?  
       * @param int $pconnect ?????ç连??  
       */  
      function mysql($dbhost='', $dbuser='', $dbpw='', $dbname = '', $pconnect = 0) {   
      
		
          $dbhost==''?$dbhost=$this->dbhost:$dbhost;   
          $dbuser==''?$dbuser=$this->dbuser:$dbuser;   
          $dbpw==''?$dbpw=$this->dbpw:$dbpw;   
          $dbname==''?$dbname=$this->dbname:$dbname;   
      	
          if($pconnect) {   
              if(!$this->link = mysql_pconnect($dbhost, $dbuser, $dbpw)) {   
                printf(error());  
				$this->halt('Can not connect to MySQL server');   
              }   
          } else {
              if(!$this->link = mysql_connect($dbhost, $dbuser, $dbpw)) {   
                  
				$this->halt('Can not connect to MySQL server');   
              }   
          }   
          if($this->version() > '4.1') {   
              if($this->dbcharset) {   
                  mysql_query("SET character_set_connection=$this->dbcharset, character_set_results=$this->dbcharset, character_set_client=binary", $this->link);   
              }   
      
              if($this->version() > '5.0.1') {   
                  mysql_query("SET sql_mode=''", $this->link);   
              }   
          }   
      
          if($dbname) {   
              mysql_select_db($dbname, $this->link);   
          }   
      
      }   
      /**  
       * ¨¦?©数æ库  
       *  
       * @param string $dbname  
       * @return  
       */  
      function select_db($dbname) {   
          return mysql_select_db($dbname, $this->link);   
      }   
      /**  
       * ??ºç?¨¦ä一?¡èå  
       *  
       * @param object $query  
       * @param int $result_type  
       * @return array  
       */  
      function fetch_array($query, $result_type = MYSQL_ASSOC) {   
          return mysql_fetch_array($query, $result_type);   
      }   
      
      /**  
       * ??º所?结?  
       *  
       * @param object $query  
       * @param int $result_type  
       * @return array  
       */  
      function fetch_all($query, $result_type = MYSQL_ASSOC) {   
          $result = array();   
          $num = 0;   
      
          while($ret = mysql_fetch_array($query, $result_type))   
          {   
              $result[$num++] = $ret;   
          }   
          return $result;   
      
      }   
      
      /**  
       * 从结?¨¦ä?得一行作为枚举数ç  
       *  
       * @param object $query  
       * @return array  
       */  
      function fetch_row($query) {   
          $query = mysql_fetch_row($query);   
          return $query;   
      }   
      
      /**  
       * 返??¥èç?  
       *  
       * @param object $query  
       * @param string $row  
       * @return mixed  
       */  
      function result($query, $row) {   
          $query = @mysql_result($query, $row);   
          return $query;   
      }   
      
      
      /**  
       * ?¥èSQL  
       *  
       * @param string $sql  
       * @param string $type  
       * @return object  
       */  
      function query($sql, $type = '') {   
      
          $func = $type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query') ?   
              'mysql_unbuffered_query' : 'mysql_query';   
          if(!($query = $func($sql, $this->link)) && $type != 'SILENT') {   
               $this->halt('MySQL Query Error: ', $sql);
              
          }   
      
          $this->querynum++;   
          return $query;   
      }   
      /**  
       * ?影响?¡敡ã
       *  
       * @return int  
       */  
      function affected_rows() {   
          return mysql_affected_rows($this->link);   
      }   
      /**  
       * 返?¨¦è信æ  
       *  
       * @return array  
       */  
      function error() {   
          return (($this->link) ? mysql_error($this->link) : mysql_error());   
      }   
      /**  
       * 返?¨¦è代ç  
       *  
       * @return int  
       */  
      function errno() {   
          return intval(($this->link) ? mysql_errno($this->link) : mysql_errno());   
      }   
      
      /**  
       * 结??¡敡ã
       *  
       * @param object $query  
       * @return int  
       */  
      function num_rows($query) {   
          $query = mysql_num_rows($query);   
          return $query;   
      }   
      /**  
       * ?字段总敡ã
       *  
       * @param object $query  
       * @return int  
       */  
      function num_fields($query) {   
          return mysql_num_fields($query);   
      }   
      /**  
       * ¨¦?¾ç?¨¦  
       *  
       * @param object $query  
       * @return bool  
       */  
      function free_result($query) {   
          return mysql_free_result($query);   
      }   
      /**  
       * 返?¨¨?增ID  
       *  
       * @return int  
       */  
      function insert_id() {   
          return ($id = mysql_insert_id($this->link)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);   
      }   
      
      /**  
       * 从结?¨¦ä?得?信æ并ä为å象返?  
       *  
       * @param object $query  
       * @return object  
       */  
      function fetch_fields($query) {   
          return mysql_fetch_field($query);   
      }   
      /**  
       * 返?mysql???  
       *  
       * @return string  
       */  
      function version() {   
          return mysql_get_server_info($this->link);   
      }   
      /**  
       * ?³é连??  
       *  
       * @return bool  
       */  
      function close() {   
          return mysql_close($this->link);   
      }   
      /**  
       * 输?º错è信æ  
       *  
       * @param string $message  
       * @param string $sql  
       */  
      function halt($message = '', $sql = '') {   
          // echo $message . ' ' . $sql."<br>";  
         //  echo mysql_errno($this->link) . ": " . mysql_error($this->link) . "\n"; 
     
      }   
  }   
?>
