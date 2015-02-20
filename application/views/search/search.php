<div class="centered">
	<h1 class="in-eyecatcher text-contrast">Search</h1>
	<?php if(!empty($validation_message)): ?>
	<div class="message">
		<div class="container">
			<?php $f = true; foreach($validation_message as $m) { if(!$f) echo '<br />'; echo $m; $f = false; } ?>
		</div>
	</div>
	<?php endif; ?>

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
	</div>
</div>