<div class="centered">
	<div id="change_background_picture"><!--
		--><?=form_open_multipart('user/edit/' . $edit_user->id . '/background_picture', array('class' => 'ajax-upload'));?><!--
			--><?=form_input($change_picture['background']['delete']);?><!--
		--><?=form_close();?><!--
	--></div>
	<div class="left-bar prevent-eyecatcher">
		<div id="profile_picture">
			<div class="picture" style="background-image:url(<?=avatar_image($edit_user->id)?>);"></div>
		</div>
		<div id="change_profile_picture">
			<?=form_open_multipart('user/edit/' . $edit_user->id . '/profile_picture', array('class' => 'ajax-upload'));?>
				<?=form_input($change_picture['profile']['delete']);?>
			<?=form_close();?>
		</div>
		<a href="<?=site_url("permission/user/" . $edit_user->id . "/");?>">Edit this users permissions</a>
	</div>
	<div id="user-info">
		<h1 class="text-contrast"><?=showname($edit_user)?></h1>
		<div class="message<?php if(!isset($errors['messages']) || count($errors['messages']) == 0) echo ' hidden'; ?>"><div class="container">
			<?php if(isset($errors['messages']) && count($errors['messages']) > 0) foreach($errors['messages'] as $m): ?>
				<p><?=$m?></p>
			<?php endforeach; ?>
		</div></div>
		<?=form_open(NULL, array('autocomplete' => 'off'));?>
			<!-- Workaround for Chromes autofill "feature" which still autocompletes when autocomplete is off. -->
			<input style="display:none;" type="text" name="__username_workaround"/>
			<input style="display:none;" type="password" name="__password_workaround"/>
			<div class="user-edit-item">
				<?=form_label('Active', $edit_form['active']['id']);?>
				<?=p_r(form_input($edit_form['active']));?>
			</div>
			<div class="user-edit-item">
				<?=form_label('E-Mail', $edit_form['email']['id']);?>
				<?=p_r(form_input($edit_form['email']));?>
			</div>
			<div class="user-edit-item">
				<?=form_label('Username', $edit_form['username']['id']);?>
				<?=p_r(form_input($edit_form['username']));?>
			</div>
			<div class="user-edit-item">
				<?=form_label('Ingame name', $edit_form['ingame_name']['id']);?>
				<?=p_r(form_input($edit_form['ingame_name']));?>
			</div>
			<div class="user-edit-item">
				<?=form_label('New Password', $edit_form['password']['id']);?>
				<?=p_r(form_input($edit_form['password']));?>
				<?=p_r(form_input($edit_form['password_verification']));?>
			</div>
			<div class="about user-edit-item">
				<?=form_label('About', $edit_form['about']['id']);?>
				<?=p_r(form_textarea($edit_form['about']));?>
				<div class="clear"></div>
			</div>
		<?=p_r(form_input($edit_form['submit']));?>
		<?=form_close();?>
	</div>
</div>