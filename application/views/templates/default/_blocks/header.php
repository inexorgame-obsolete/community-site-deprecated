<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
 	<title>{title}</title>
	<?php
		dcss('style');
		dcss('nav');
		dcss('OpenSans');
		dcss('big-device', '(min-width: 1100px)');
		dcss('medium-device', '(max-width: 960px)');
		dcss('small-device', '(max-width: 800px)');
		dcss('mini-device', '(max-width: 600px)');
	?>
	<script src="<?=js('jquery')?>"></script>
	<script>
		var default_search_api = '<?=$default_search_api;?>';
		var base_url = <?="'".$base."'";?>;
		var user_dir = <?php if(isset($user['id'])) echo "'data/user_upload/".$user['id']."/'"; else echo 'false'; ?>;
	</script>
	<script src="<?=js('search')?>"></script>
	<script src="<?=js('nav')?>"></script>
	<link rel="prerender" href="<?=data('images/logo_extrasmall.png')?>" />
	<link rel="prefetch" href="<?=data('images/logo_extrasmall.png')?>" />
	{head}
</head>
<body>
<?php if ($logged_in !== false): ?>
	<ul id="users-nav">
		<li class="user-profile">
			<a href="#closer" class="right">
				<span class="avatar" style="background-image:url(<?=avatar_image($user['id'])?>);"></span>
			</a>
			<a class="profile-name" href="<?=site_url('user/'.$user['id'])?>"><?=showname($user);?></a>
			<div class="clear"></div>
		</li>
		<?php foreach($menu_links as $m): ?>
			<?php if($m->link != NULL && count($m->childs) == 0): ?>
				<li><a class="item" href="<?=site_url($m->link)?>"><?=$m->name?></a></li>
			<?php else: ?>
				<li class="headline">
				<?php if($m->link != NULL) : ?>
					<a class="item" href="<?=site_url($m->link)?>"><?=$m->name?></a>
				<?php else: ?>
					<?=$m->name?>
				<?php endif; ?>
				</li>
			<?php endif; ?>
			<?php foreach($m->childs as $c) : ?>
				<li><a class="item" href="<?=site_url($c->link)?>"><?=$c->name?></a></li>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
	<a id="closer"></a>
	<div id="loader"><div></div></div>
<?php if ($logged_in !== false): ?>
	<div id="browse-data" class="hidden">
		<div class="browse-data close background"></div>
		<div class="browse-data window">
			<h3 class="browse-data window-title">Select data<span class="browse-data close">&times;</span></h3>
			<div class="browse-data content"></div>
			<div class="browse-data edit">
				<div class="browse-data upload">
					<div class="browse-data label">Upload file in this directory</div>
					<?=form_open_multipart('data/api/upload', array('class' => 'ajax-upload'));?>
						<input type="hidden" name="directory">
						<input type="hidden" name="type" value="file">
						<input type="file" name="file" class="browse-data upload-input">
						<input type="submit" class="browse-data upload-submit">
					<?=form_close();?>
				</div>
				<div class="browse-data create-dir">
					<div class="borwse-data label">Create folder</div>
					<?=form_open('data/api/createdir', array('class' => 'ajax-submit')); ?>
						<input type="hidden" name="parent_dirs">
						<input type="text" class="browse-data create-dir-input" name="dir"><!--
						--><input type="submit" class="browse-data create-dir-submit">
					<?=form_close();?>
				</div>
				<div class="browse-data message-container">
					<div class="browse-data label">Message</div>
					<div class="browse-data messages"></div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
	<header id="top-main-menu">
		<div class="border menu">
			<div class="navigation">
				<div class="helper">
					<a href="<?=site_url()?>"><img src="<?=image('logo_small.png'); ?>" alt="{sitetitle}" /></a>
					<span class="title"><a href="<?=site_url()?>">{sitetitle}</a></span>
				</div>
				<ul id="main-menu">
					<li><a href="<?=site_url();?>">Project</a></li>
					<li><a href="<?=site_url('team')?>">Team</a></li>
					<li><a href="<?=site_url()?>#main-download-game">Download</a></li>
					<li><a href="<?=site_url('blog')?>">Blog</a></li>
				</ul>
			</div>
			<?php if ($logged_in === false): ?>
				<div class="user-login">
					<div class="login-field">
						<?=form_open('user/login');?>
							<div class="user-login-field"><input type="text" name="username_email" placeholder="E-Mail or Username" /></div>
							<div class="user-login-field"><input type="password" name="password" placeholder="Password" /></div>
							<div class="right user-login-submit">
								<label for="stay_logged_in_quick_login">Stay logged in </label><input type="checkbox" checked="checked" name="stay_logged_in" id="stay_logged_in_quick_login" />
								<input type="submit" name="submit" value="Login" />
							</div>
						<?=form_close();?>
					</div>
					<div class="login-link">
						<a href="<?=site_url('user/login');?>">Login</a>
					</div>
				</div>
			<?php else: ?>
			<div class="user-showcase">
				<a href="#users-nav"><span class="avatar" style="background-image:url(<?=avatar_image($user['id'])?>);"></span></a>
			</div>
			<?php endif; ?>
			<div class="clear"></div>
		</div>
		<a class="opener" href="#top-main-menu">&#9776; Menu</a>
		<a class="closer" href="#closer">&#9776; Close</a>
	</header>
	<div id="search-window">
		<a href="#" class="close-background"></a>
		<div class="centered content">
			<div class="search">
				<?=form_open('search');?>
					<?php if(count($search_form['radio']['inputs'])>1) : ?>
						<div class="radio">
							<noscript>Search for:</noscript>
							<?php foreach($search_form['radio']['inputs'] as $i => $f): ?>
								<?=form_label($search_form['radio']['labels'][$i]['value'], $search_form['radio']['labels'][$i]['for']);?>
								<?=form_radio($f);?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<div class="text"><?=form_input($search_form['search']);?></div>
					<div class="submit"><?=form_submit($search_form['submit']);?></div>
				<?=form_close();?>
				<div class="result" data-searchid="<?=$main_search_id;?>"></div>
			</div>
		</div>
	</div>
	<div id="header_placeholder">
	</div>
	<div id="main-eyecatcher" class="eyecatcher image-mover" style="background-image:url({eyecatcher_image:<?=iimage('eyecatcher');?>});">
	</div>
	<div class="outfader">
	</div>
	<div class="wrapper">