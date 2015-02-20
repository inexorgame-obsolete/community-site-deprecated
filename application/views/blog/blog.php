<div class="centered">
	<h1 class="text-contrast in-eyecatcher">Blog</h1>
</div>
<?php foreach($posts as $entry): ?>
	<?php $creator = $creators[$entry['user_id']]; ?>
<div class="blog-centered">
	<h1><a href="<?=site_url('blog/view/' . $entry['id'])?>"><?=d($entry['headline'])?></a></h1>
	<?=create_blog_post_from_array($entry['body'], $entry['id'], true);?>
	<ul class="vertical-nav">
		<li><a href="<?=site_url('blog/view/' . $entry['id'])?>">View post</a></li><li><a href="<?=site_url('blog/view/' . $entry['id']) . '#comments'?>">Comments</a></li><?php 
	if($user_edit_others || $user_may_release) : 
	?><li><a href="<?=site_url('blog/edit/' . $entry['id'])?>">Edit post</a></li><?php
	endif; ?>
	</ul>
	<div class="spotlight">
		<a href="<?=site_url('user/'.$entry['user_id'])?>"><div class="avatar" style="background-image:url(<?=avatar_image($entry['user_id'])?>);"></div><?=showname($creator);?>
		<br /><span class="date" title="<?=tm($entry['timestamp'])?>"><?=dt($entry['timestamp'])?></span></a>
		<div class="about">
			<?=d($creator->about);?>
		</div>
	</div>
</div>
<?php endforeach; ?>
<div class="vertical-nav">
<?php if($current_page != 1) : ?>
	<a href="<?=site_url('blog')?>">newest</a><?php 
	endif; 

	$i = $current_page - 5;
	if($i < 2) $i = 2;
	while($i < $current_page) {
		echo '<a href="' .site_url('blog/' . $i) . '">' . $i . '</a>';
		$i++;
	}

?><a class="current">current</a><?php

	$i = $current_page + 1;
	$i_max = $current_page + 6;
	if($i_max > $max_pagination) $i_max = $max_pagination;
	while($i < $i_max) {
		echo '<a href="' .site_url('blog/' . $i) . '">' . $i . '</a>';
		$i++;
	}
if($max_pagination != $current_page) : ?><a href="<?=site_url('blog/' . $max_pagination)?>">oldest</a>
<?php endif; ?>
</div>