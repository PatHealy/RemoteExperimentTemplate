<?php

$url = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';

if(!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != "on")
{
    //Tell the browser to redirect to the HTTPS URL.
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"], true, 301);
    //Prevent the rest of the script from executing.
    exit;
}

// array holding allowed Origin domains
$allowedOrigins = array(
  '(http(s)://)?(www\.)itch.io',
  '(http(s)://)?(www\.)pythonanywhere.com'
);
 
if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] != '') {
  foreach ($allowedOrigins as $allowedOrigin) {
    if (preg_match('#' . $allowedOrigin . '#', $_SERVER['HTTP_ORIGIN'])) {
      header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
      header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
      header('Access-Control-Max-Age: 1000');
      header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
      break;
    }
  }
}

$url = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
//'http://127.0.0.1:8000/';

// adapted from reference code, from another project
class Database{
    private $db_host = "DATABASE HOST GOES HERE";  // Change as required
    private $db_user = "DATABASE USER GOES HERE";  // Change as required
    private $db_pass = "DATABASE PASSWORD GOES HERE";  // Change as required
    private $db_name = "DEFAULT DATABASE NAME GOES HERE";   // Change as required

    private $con = false; // Check to see if the connection is active
    private $result = array(); // Any results from a query will be stored here
    private $myQuery = "";// used for debugging process with SQL return
    private $numResults = "";// used for returning the number of rows
    public $connection = "";

    // Function to make connection to database
    public function connect(){
        if(!$this->con){
            $myconn = @mysqli_connect($this->db_host,$this->db_user,$this->db_pass);  // mysqli_connect() with variables defined at the start of Database class
            $this->connection = $myconn;
            if($myconn){
                $seldb = @mysqli_select_db($myconn, $this->db_name); // Credentials have been pass through mysqli_connect() now select the database
                if($seldb){
                    $this->con = true;
                    return true;  // Connection has been made return TRUE
                }else{
                    array_push($this->result,mysqli_error($myconn)); 
                    return false;  // Problem selecting database return FALSE
                }  
            }else{
                array_push($this->result,mysqli_error($myconn));
                return false; // Problem connecting return FALSE
            }  
        }else{  
            return true; // Connection has already been made return TRUE 
        }   
    }

    // Function to disconnect from the database
    public function disconnect(){
        // If there is a connection to the database
        if($this->con){
            // We have found a connection, try to close it
            if(@mysqli_close()){
                // We have successfully closed the connection, set the connection variable to false
                $this->con = false;
                // Return true tjat we have closed the connection
                return true;
            }else{
                // We could not close the connection, return false
                return false;
            }
        }
    }
    
    public function sql($sql){
        $query = @mysqli_query($this->connection,$sql);

        if(substr( $sql, 0, 6 ) === "UPDATE")
        {
            return true;
        }
        $this->myQuery = $sql; // Pass back the SQL
        if($query){
            // If the query returns >= 1 assign the number of rows to numResults
            $this->numResults = mysqli_num_rows($query);
            // Loop through the query results by the number of rows returned
            for($i = 0; $i < $this->numResults; $i++){
                $r = mysqli_fetch_array($query);
                $key = array_keys($r);
                for($x = 0; $x < count($key); $x++){
                    // Sanitizes keys so only alphavalues are allowed
                    if(!is_int($key[$x])){
                        if(mysqli_num_rows($query) >= 1){
                            $this->result[$i][$key[$x]] = $r[$key[$x]];
                        }else{
                            $this->result = null;
                        }
                    }
                }
            }
            return true; // Query was successful
        }else{
            array_push($this->result,mysqli_error($this->connection));
            return false; // No rows where returned
        }
    }
    
    // Function to SELECT from the database
    public function select($table, $rows = '*', $join = null, $where = null, $order = null, $limit = null){
        // Create query from the variables passed to the function
        $q = 'SELECT '.$rows.' FROM '.$table;
        if($join != null){
            $q .= ' JOIN '.$join;
        }
        if($where != null){
            $q .= ' WHERE '.$where;
        }
        if($order != null){
            $q .= ' ORDER BY '.$order;
        }
        if($limit != null){
            $q .= ' LIMIT '.$limit;
        }
        $this->myQuery = $q; // Pass back the SQL
        // Check to see if the table exists
        if($this->tableExists($table)){
            // The table exists, run the query
            $query = @mysqli_query($this->connection,$q);
            if($query){
                // If the query returns >= 1 assign the number of rows to numResults
                $this->numResults = mysqli_num_rows($query);
                // Loop through the query results by the number of rows returned
                for($i = 0; $i < $this->numResults; $i++){
                    $r = mysqli_fetch_array($query);
                    $key = array_keys($r);
                    for($x = 0; $x < count($key); $x++){
                        // Sanitizes keys so only alphavalues are allowed
                        if(!is_int($key[$x])){
                            if(mysqli_num_rows($query) >= 1){
                                $this->result[$i][$key[$x]] = $r[$key[$x]];
                            }else{
                                $this->result = null;
                            }
                        }
                    }
                }
                return true; // Query was successful
            }else{
                array_push($this->result,mysqli_error($myconn));
                return false; // No rows where returned
            }
        }else{
            return false; // Table does not exist
        }
    }

    public function getnumberfinishedsubjects(){
        if ($this->select("participant", "count(*)", null, "current_stage = 4")){
            return (int)$this->getResult()[0]["count(*)"];
        }
        return 100;
    }
    
    // Function to insert into the database
    public function insert($table,$params=array()){
        // Check to see if the table exists
         if($this->tableExists($table)){
            $sql='INSERT INTO `'.$table.'` (`'.implode('`, `',array_keys($params)).'`) VALUES ("' . implode('", "', $params) . '")';
            $this->myQuery = $sql; // Pass back the SQL
            // Make the query to insert to the database
            if($ins = @mysqli_query($this->connection,$sql)){
                array_push($this->result,mysqli_insert_id($this->connection));
                return true; // The data has been inserted
            }else{
                array_push($this->result,mysqli_error($myconn));
                return false; // The data has not been inserted
            }
        }else{
            return false; // Table does not exist
        }
    }

    public function insertwithnodata($table){
        // Check to see if the table exists
        if($this->tableExists($table)){
            $sql='INSERT INTO `'.$table.'`  VALUES ()';
            $this->myQuery = $sql; // Pass back the SQL
            // Make the query to insert to the database
            if($ins = @mysqli_query($this->connection,$sql)){
                array_push($this->result,mysqli_insert_id($this->connection));
                return true; // The data has been inserted
            }else{
                array_push($this->result,mysqli_error($myconn));
                return false; // The data has not been inserted
            }
        }else{
            return false; // Table does not exist
        }
    }

    public function delete($table,$where = null){
        // Check to see if table exists
         if($this->tableExists($table)){
            // The table exists check to see if we are deleting rows or table
            if($where == null){
                // nothing
            }else{
                $delete = 'DELETE FROM '.$table.' WHERE '.$where; // Create query to delete rows
            }
            // Submit query to database
            if($del = @mysqli_query($this->connection,$delete)){
                array_push($this->result,mysqli_affected_rows($this->connection));
                $this->myQuery = $delete; // Pass back the SQL
                return true; // The query exectued correctly
            }else{
                array_push($this->result,mysqli_error($myconn));
                return false; // The query did not execute correctly
            }
        }else{
            return false; // The table does not exist
        }
    }

    // Function to update row in database
    public function update($table,$params=array(),$where){
        // Check to see if table exists
        if($this->tableExists($table)){
            // Create Array to hold all the columns to update
            $args=array();
            foreach($params as $field=>$value){
                // Seperate each column out with it's corresponding value
                $args[]=$field.'="'.$value.'"';
            }
            // Create the query
            $sql='UPDATE '.$table.' SET '.implode(',',$args).' WHERE '.$where;
            // Make query to database
            $this->myQuery = $sql; // Pass back the SQL
            if($query = @mysqli_query($this->connection,$sql)){
                array_push($this->result,mysqli_affected_rows($this->connection));
                return true; // Update has been successful
            }else{
                array_push($this->result,mysqli_error($myconn));
                return false; // Update has not been successful
            }
        }else{
            return false; // The table does not exist
        }
    }
    
    // Private function to check if table exists for use with queries
    private function tableExists($table){
        $tablesInDb = @mysqli_query($this->connection,'SHOW TABLES FROM '.$this->db_name.' LIKE "'.$table.'"');
        if($tablesInDb){
            if(mysqli_num_rows($tablesInDb)==1){
                return true; // The table exists
            }else{
                array_push($this->result,$table." does not exist in this database");
                return false; // The table does not exist
            }
        }
    }
    
    // Public function to return the data to the user
    public function getResult(){
        $val = $this->result;
        $this->result = array();
        return $val;
    }
    //Pass the SQL back for debugging
    public function getSql(){
        $val = $this->myQuery;
        $this->myQuery = array();
        return $val;
    }
    //Pass the number of rows back
    public function numRows(){
        $val = $this->numResults;
        $this->numResults = array();
        return $val;
    }
    // Escape your string
    public function escapeString($data){
        return mysqli_real_escape_string($db->connection,$data);
    }
}