<div class="centered">
	<h1 class="text-contrast in-eyecatcher">Activity Log</h1>
	<?php if($may_submit) : ?>
	<div class="spotlight update_activity">
	<a href="<?=site_url('user/' . $user->id);?>" class="fixed full"><div class="avatar" style="background-image:url(<?=avatar_image($user->id)?>);"></div><?=showname($user)?></a>	
	<?=form_open('activity');?>
		<?=form_textarea($form['text']);?>
		<div class="submit_form">	
			<?=form_label($form['public_label']['value'], $form['public_label']['for'], $form['public_label']['attributes']);?>
			<?=form_checkbox($form['public']);?>
			<?=form_submit($form['submit']);?>
		</div>
	<?=form_close();?>
	</div>
	<?php endif; ?>
	<?php
	if(count($posts) == 0):
	?>
		<div class="spotlight" style="text-align: center;">
			<h4>There is no activity right now.</h4>
		</div>
	<?php endif; ?>
	<?php foreach($posts as $p): $youtube = get_youtube_links($p['changes']); ?>
		<div class="spotlight">
			<a href="<?=site_url('user/' . $p['user']->id);?>" class="full"><div class="avatar" style="background-image:url(<?=avatar_image($p['user']->id)?>);"></div><?=showname($p['user'], 'user one-line')?><span class="date" title="<?=tm($p['timestamp'])?>"><?=dt($p['timestamp'])?></span></a>
			<?php if($edit_all || $p['user']->id == $user->id): ?>
				<span class="edit"><a href="<?=site_url('activity/edit/' . $p['id'] . '/' . $current_page)?>" class="edit-icon"></a></span>
			<?php endif; ?>
			<div class="topics">
				<?=nl2br(link_links(ph($p['changes'])))?>
			</div>
			<?php foreach($youtube as $y) : ?>
				<div class="youtube-container">
					<iframe width="100%" height="315" src="//www.youtube-nocookie.com/embed/<?=$y?>" frameborder="0" allowfullscreen></iframe>
				</div>
			<?php endforeach; ?>
		</div>

	<?php endforeach; ?>
	<div class="vertical-nav">
	<?php if($current_page != 1) : ?>
		<a href="<?=site_url('activity')?>">newest</a><?php 
		endif; 

		$i = $current_page - 5;
		if($i < 2) $i = 2;
		while($i < $current_page) {
			echo '<a href="' .site_url('activity/' . $i) . '">' . $i . '</a>';
			$i++;
		}

	?><a class="current">current</a><?php

		$i = $current_page + 1;
		$i_max = $current_page + 6;
		if($i_max > $max_pagination) $i_max = $max_pagination;
		while($i < $i_max) {
			echo '<a href="' .site_url('activity/' . $i) . '">' . $i . '</a>';
			$i++;
		}
	if($max_pagination != $current_page) : ?><a href="<?=site_url('activity/' . $max_pagination)?>">oldest</a>
	<?php endif; ?>
	</div>
</div>