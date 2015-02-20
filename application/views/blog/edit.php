<div class="centered">
	<h1 class="text-contrast in-eyecatcher">Edit Blog Post</h1>
</div>
	<?=form_open(NULL, $form['form']);?>
		<div class="centered">
			<div class="nojs-create">
				<?=form_input($form['headline']);?>
				<?=form_textarea($form['text']);?>
			</div>
			<h1 class="editable hidden" id="edit-headline" data-create="headline"><?php if(strlen($form['headline']['value']) > 0): echo $form['headline']['value']; else: ?><?=$form['headline']['value']?><?php endif; ?></h1>

			<div class="editable hidden" id="edit-body" data-create="text">
			<?=$form['text']['value']?>
</div>
		</div>
	<hr />
	<div class="centered">
		<table class="settings">
			<tr>
				<td><?=form_label($form['slug_label']['content'], $form['slug_label']['for'])?></td>
				<td><?=form_input($form['slug'])?></td>
			</tr>
		<?php if(isset($form['enable'])) {
			echo '<tr><td>' . form_label($form['enable_label']['content'], $form['enable_label']['for']) . '</td>';
			echo '<td>' . form_checkbox($form['enable']) . '</td></tr>';
		}
		?>
			<tr>
				<td><?=form_input($form['validate']);?></td>
				<td><?=$form['validate']['title'];?></td>
			</tr>
			<tr>
				<td><?=form_input($form['submit']);?></td>
				<td><?=$form['submit']['title'];?></td>
			</tr>



		</table>
	<?=form_close();?>
	</div>
</div>