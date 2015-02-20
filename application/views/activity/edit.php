<div class="centered">
	<h1 class="text-contrast in-eyecatcher">Edit Activity</h1>
	<div class="spotlight update_activity">
	<a href="<?=site_url('user/' . $creator->id);?>" class="fixed full"><div class="avatar" style="background-image:url(<?=avatar_image($creator->id)?>);"></div><?=showname($creator)?> <span title="<?=tm($post->timestamp)?>"><?=dt($post->timestamp)?></span></a>
	<?=form_open();?>
		<?=form_textarea($form['text']);?>
		<div class="submit_form">
			<?=form_submit($form['delete']);?>
			<?=form_label($form['public_label']['value'], $form['public_label']['for'], $form['public_label']['attributes']);?>
			<?=form_checkbox($form['public']);?>
			<?=form_submit($form['submit']);?>
		</div>
	<?=form_close();?>
	</div>
</div>