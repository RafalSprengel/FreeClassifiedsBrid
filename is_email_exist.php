<?php
	require('../lib.php');
	$login = $_POST['email'];
	$result = sql('select userId from users where email=?', array($login));
	if (isset($result[0]->userId)) echo"yes";
	else echo"no";
	
?>