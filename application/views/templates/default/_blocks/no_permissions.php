<?php
	if(strlen($headline) == 0) $headline = 'No permissions';
	if(strlen($text) == 0) $text = 'You have no permissions to view this site.';
?>
<div class="centered">
	<h1 class="text-contrast in-eyecatcher"><?=d($headline)?></h1>
	<p><?=d($text)?></p>
</div>