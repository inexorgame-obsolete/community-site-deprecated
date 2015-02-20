<div class="centered">
	<h1 class="in-eyecatcher text-contrast">Users</h1>
	<?php if(!empty($search_form['validation_message'])): ?>
	<div class="message">
		<div class="container"><?=$search_form['validation_message']; ?></div>
	</div>
	<?php endif; ?>
	<div class="search">
		<?=form_open('user');?>
		<div class="text"><?=form_input($search_form['search']);?></div>
		<div class="submit"><?=form_submit($search_form['submit']);?></div>
		<?=form_close();?>
	</div>
	<div class="user-list-container">
		<?php if(count($users)==0): ?>
			<a class="user-section"><h4 class="no-search-results">There are no results for your search.</h4></a>
		<?php else: ?>
			<?php foreach($users as $u): ?>
			<a href="<?=site_url('user/' . $u->id)?>" class="user-section<?php if($image = iimage($u->id, 2)): ?> image-mover" style="background-image:url( <?=$image?>);" <?php else: echo '"'; /* complex for correct syntax highlighting ;-) */ endif; ?>>
				<span class="avatar" style="background-image:url(<?=iimage($u->id, 'avatar');?>);"></span>
				<h3 class="text-contrast"><?=showname($u)?></h3>
			</a>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
</div>