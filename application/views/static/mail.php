<?php 
if(!isset($email) || $email != true): 
	show_404();
else: 

	function _generate_email_markup($contents)
	{
		$return = '';
		foreach($contents as $content) {
			if(isset($content['tag'])) $return .= '<' . $content['tag'];
			if(isset($content['css'])) $return .= ' style="' . p_r($content['css']) . '"';
			if(isset($content['tag'])) $return .= '>';
			foreach($content as $i => $c) {
				if($i != 'tag' && $i != 'css') {
					if(is_array($c)) $return .= _generate_email_markup($c);
					else $return .= p_r(auto_link($c));
				}
			}
			if(isset($content['tag'])) $return .= '</' . $content['tag'] . '>';
		}
		return $return;
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>{sitetitle}</title>
	<style>
		a:link {
			font-weight: bold;
			text-decoration: none;
			color: #000;
		}
		h1, h2, h3, h4, h5, h6 {
			font-weight: 100;
		}
	</style>
</head>
<body style="font-family: sans-serif;">
<header style="border-bottom: 1px solid #CCC; padding: 10px;">
	<div style="height: 70px; color: #000;">
		<a href="<?=site_url();?>" style="text-decoration: none;">
			<img src="<?=site_url('data/images/logo_small.png');?>" alt="{sitetitle}" style="height: 70px;">
		</a>
		<a href="<?=site_url();?>" style="
		vertical-align: top;
		padding: 10px; 
		text-decoration:none; 
		color: #000; 
		font-weight: 100; 
		font-size: 24px;
		line-height: 50px;
		display: inline-block;
		">
			<span style="
		color: #999;
		border-left: 1px solid #CCC;
		font-size: 24px;
		padding-left: 10px;
		line-height: 40px;
		display: inline-block;">{sitetitle}</span>
		</a>
	</div>
</header>
<div style="padding: 10px;">
	<?=_generate_email_markup($content);?>
</div>
<footer style="background-color: #EFEFEF; border-top: 1px solid #CCC; padding: 10px; color: #999;">
	&copy; by {sitetitle} in <?=date('Y');?>
</footer>
</body>
</html>
<?php
endif;
?>