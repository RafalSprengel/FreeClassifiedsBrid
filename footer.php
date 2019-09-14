	</content>
	<footer>
		<div id='copyright-info'>Copyrights Rafał Sprengel, 2018. 
			<?php 
				if (!isset($_GET['id'])) 
					echo" Ilość odwiedzin: ".countAll().", dzisiaj: ".counterLastDay(null,1);
			?>
		</div>
	</footer>
<!--end footer -->
</body>
</html>
