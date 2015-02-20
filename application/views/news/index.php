<div class="centered">
	<h1 class="text-contrast in-eyecatcher">News</h1>
	<ul class="news-feed">
	<?php
	foreach ($items as $item)
	{
		echo "\n";
		echo '<li>';
		echo '<a target="_blank" href="'.$item->link.'">'.$item->title.' <span class="date">'.$item->pubDate.'</span><span class="description">'.strip_tags($item->description).'</span></a>';
		echo '</li>';
	}

	?>
	</ul>
</div>
