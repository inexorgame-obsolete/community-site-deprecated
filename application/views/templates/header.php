<!doctype html>
<html lang="<?php echo $lang?>">
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	
	<title><?php echo $title?></title>
	<?php echo meta($meta); ?>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<?php
		echo link_tag("data/css/styles.css");
		echo link_tag("data/css/normalize.css");
		echo link_tag("data/css/font_awesome/css/font-awesome.min.css"); 
	?>
	
	<!--[if lt IE 9]>
		<?php echo script_tag("data/js/html5shiv.js"); ?>
	<![endif]-->
</head>
<body>
	<div class="header-container">
    	<header class="wrapper clearfix">
        	<h1 class="title"><?php echo $menu['title']?></h1>
            <nav>
           		<?php
           			echo ul($menu['items'], false); 
           		?>
			</nav>
        </header>
	</div> <!-- #header -->

	<div class="main-container">
		<div class="main wrapper clearfix">