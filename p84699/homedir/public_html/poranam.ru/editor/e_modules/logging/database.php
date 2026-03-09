<?
/**
 * Database.php
 * 
 * The Database class is meant to simplify the task of accessing
 * information from the website's database.
 *
 * Written by: Alejandro U Alvarez http://urbanoalvarez.es
 * Last Updated: March 21, 2008
 */
include($_SERVER['DOCUMENT_ROOT']."/editor/e_modules/logging/config.php");
      
class MySQLDB
{
   var $connection;         //The MySQL database connection

   /* Class constructor */
   function MySQLDB(){
      /* Make connection to database */
      $this->connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME) or die(mysqli_error());
   }
   
   /**
    * query - Performs the given query on the database and
    * returns the result, which may be false, true or a
    * resource identifier.
    */
   function query($query){
      return mysqli_query($this->connection, $query);
   }
	//clean a string
   function clean($string) {
	  $string = stripslashes($string);
	  $string = htmlentities($string);
	  $string = strip_tags($string);
	  return $string;
   }
};

/* Create database connection */
$database = new MySQLDB;

?>
