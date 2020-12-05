<?php include("includes/header.php") ?>

<?php include("includes/nav.php") ?>

   
	<!--<php

    $sql="SELECT * FROM users";
    $result = query($sql);//sending this to predefined function query() in db.php
    confirm($result);

    $row=fetch_array($result);
    echo $row['user_name'].'<br>'.$row['first_name'];

  ?>-->
	<div class="jumbotron">
  <?php display_message(); ?>
		<h1 class="text-center"> Home</h1>
	</div>

  <?php include("includes/footer.php") ?>

	
