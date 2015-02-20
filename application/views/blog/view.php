
<div class="blog-centered">
	<h1 class="text-contrast in-eyecatcher"><?=d($entry->headline)?></h1>
	<?=create_blog_post_from_array($entry->body, $entry->id, false);?>
	<?php if($user_edit_others || $user_may_release) : ?>
	<ul class="vertical-nav">
	<li><a href="<?=site_url('blog/edit/' . $entry->id)?>">Edit post</a></li>
	</ul><?php
	endif; ?>
	<div class="spotlight">
		<a href="<?=site_url('user/'.$creator->id)?>"><div class="avatar" style="background-image:url(<?=avatar_image($creator->id)?>);"></div><?=showname($creator);?>
		<br /><span class="date" title="<?=tm($entry->timestamp)?>"><?=dt($entry->timestamp)?></span></a>
		<div class="about">
			<?=d($creator->about);?>
		</div>
	</div>
</div>