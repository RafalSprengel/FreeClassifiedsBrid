<html>
<body>
	<?php
		require('lib.php');
		$opt = $_GET['opt'];	//1,2,3,4,5
		isset($opt)? $number=$opt : $number=0;
		$lev2 = sql('SELECT name FROM menu WHERE level1=? AND level2>0', array($number));
		if(count($lev2)!==0){
			echo"<SELECT id='subcat-sel' name='level2' required >";
			echo"<OPTION value='' hidden disabled selected>Podkategoria</option>";
			$i=0;
			foreach($lev2 as $line){
				$i++;
				echo"<option value=$i> $line->name </option>";
			}
			echo"</select>";
		}
	?>
</body>
</html>