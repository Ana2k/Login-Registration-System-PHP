<?php include("includes/header.php") ?>


<?php include("includes/nav.php") ?>
	



	<div class="jumbotron">
		<h1 class="text-center">
		<?php
		if(logged_in()){
			echo "Logged in";
		}
		else{
			redirect("index.php");
		}
		?>
	</div>





  <?php include("includes/footer.php") ?>