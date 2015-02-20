<div class="centered">
	<div id="profile_picture">
		<div class="picture" style="background-image:url(<?=avatar_image($view['id'])?>);"></div>
	</div>
	<div id="user-info">
		<h1 class="text-contrast"><?=showname($view)?></h1>
		<div class="about"><?=d($view['about'], 1)?></div>
	</div>
</div>