<?php
/* 
 * This class is used for logging purposes
 * It also contains useful user functions, to get ip for example
 *
 * Written by: Alejandro U Alvarez http://urbanoalvarez.es
 * Last Updated: March 21, 2008
 *
 */

class Logs
{
	//init of class update Logs
	
	//Function send mail, notifies an admin of log changes.
	function newLog($msg,$by){
		$from = 'From: <'.EMAIL_FROM_ADDR.'>';
		$subject = 'Log of '.SITE_NAME.' changed';
		$body = 'The log of '.SITE_NAME.' has changed:\n $msg\n Hi, '.EMAIL_FROM_ADDR;
		return mail(EMAIL_TO_ADDR,$subject,$body,$from);
	}
	
	//the following function will log any activity in the pages with the code "$log->logg(parameters);" in them:
	function logg($page=1,$msg=1,$priority='Низкий',$color='blue',$mail='no'){
		if($page == 1 || $msg == 1){
			if($page == 1){
				$page = $_SERVER['PHP_SELF']; //get full page direction (Ej. /index.php
				$pages = explode('/',$page); //explode the / and take 1 (Only suitable for first level pages)
				$name = explode('.',$pages[1]); //explode the .php, and leave only the "name" ($name[0])
				$page = $name[0]; //Now the page name is in the form of "log" for the page "/log.php"
			}
			//Use the following arrays to store the default pages:
			//
			$high = array('log'); //for the example the important page is log.php
			$medium = array('test'); //for the example the medium is test.php
			//
			if($priority == 'notSet'){ //If priority was left blank
				//Now perform the check to see if page is important:
				if(in_array($page,$high)){
					$priority = 'Высокий';
					$color = 'red';
				}else if(in_array($page,$medium)){
					$priority = 'Средний';
					$color = 'yellow';
				}else{
					$priority = 'Низкий';
				}
			}
			if($msg == 1){ //This are the default messages to use when no arguments are given.
				$msg = 'Посетил страницу '.$page;
			}
			//
		}
		if($mail=='yes'){
			$this->newLog($msg,$_SESSION['username']);
		}
		return $this->addLog($msg,$_SESSION['username'],time(),ip(),$priority,$color);
	}
	
	function addLog($msg,$user,$timetamp,$ip,$priority,$class){
		$database = new MySQLDB;
		$sql = 'INSERT INTO `'.TBL_LOG.'` (`id`, `msg`, `user`, `timestamp`, `ip`, `priority`, `class`) VALUES (\'\', \''.$msg.'\', \''.$user.'\', \''.$timetamp.'\', \''.$ip.'\', \''.$priority.'\', \''.$class.'\')';
		$do = $database->query($sql);
		if($do){
			return true;
		}else{
			return false;
		}
	}
	function error($msg){
		echo '<tr>';
    	echo '<td colspan="6">'.$msg.'</td>';
    	echo '</tr>';
	}
	
	//This function generates the icon code depending on the class (color)
	//In the folder "icons/" there are some other optional icons that you can asign to make it more flexible
	function genIcon($c){
		switch($c){
			case 'red':
				$img = 'exclamation';
				break;
			case 'danger':
				$img = 'cancel';
				break;
			case 'yellow':
				$img = 'shield';
				break;
			case 'green':
				$img = 'accept';
				break;
			case 'blue':
				$img = 'help';
				break;
			default:
				$img = 'resultset_next';
		}
		return '<img src="icons/'.$img.'.png" alt="Icon '.$c.'" width="16" height="16" border="0" class="wp-smiley" />';	
	}
	
	//This functions displays the logs in a table
	function displayLogs(){
		$database = new MySQLDB;
		$q = 'SELECT * FROM `'.TBL_LOG.'` ORDER BY timestamp DESC,priority';
   		$result = $database->query($q);
		$num_rows = mysqli_num_rows($result);
		if(!$result || ($num_rows < 0)){
		   $this->error('Ошибка отображения информации');
		   return;
	    }
	    if($num_rows == 0){
		   $this->error('Нет записей за этот период');
		   return;
	    }
		while($rl = mysqli_fetch_assoc($result)){
			$time = date("g:ia - j/m/Y",$rl['timestamp']);
			$icon = $this->genIcon($rl['class']);
			$user = $rl['user'];
			$ip = $rl['ip'];
			
			//loop is set up:
			echo '<tr class="'.$rl['class'].'">';
			echo '<td width="16">'.$icon.'</td>'; //icon
			echo '<td>'.$rl['msg'].'</td>'; //msg
			echo '<td>'.$user.'</td>'; //user
			echo '<td>'.$time.'</td>'; //timestamp
			echo '<td>'.$ip.'</td>'; //ip
			echo '<td>'.$rl['priority'].'</td>'; //priority
		    echo '</tr>';	
		}
	}
	//This function empties the log (Be careful with this one!)
	function emptyLog($pass){
		$database = new MySQLDB;
		if(sha1($pass) == LOG_PASS){
			$sql = 'TRUNCATE TABLE `'.TBL_LOG.'`';
			$database->query($sql);
			//Store the time and ip of visitor who emptied it.
			$this->logg('log','Журнал очищен','Высокий','red');
		}else{
			//log emptying not succesful
			$this->logg('deleteLog','Неудачная попытка очистки журнала','Высокий','red'); //pretend that the page is deleteLog.php, the last parameter "mail" is set to default (no)
		}
		return true;
	}
	//This function displays messages:
	function displayMsg($name){
		if(isset($_SESSION[$name.'_msg'])){ //there is a message stored:
			echo '<p class="alertMsg">'.$_SESSION[$name.'_msg'].'.</p>';
			unset($_SESSION[$name.'_msg']);
		}
	}
};

?>
