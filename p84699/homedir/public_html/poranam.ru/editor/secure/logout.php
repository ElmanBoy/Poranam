<?PHP
if (phpversion() >= 4) {
	// phpversion = 4
	session_start();
	session_destroy();
} else {
	// phpversion = 3
	session_destroy_php3();
}
header("Location: https://".$_SERVER['SERVER_NAME']);
?>