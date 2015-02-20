<?php function show_groups_table($group, $form = false, $errors = false) {
	$return = '<tr';
	if($errors['relateds'][$group->id]) $return .= ' class="error"';
	$return .= '>';
	$return .= '<td>' . $group->name . '</td>';
	$return .= '<td>' . $group->description . '</td>';
	if($form)
	{
		$return .= '<td>' . form_input($form['pointer'][$group->id]) . form_input($form['significance'][$group->id]) . '</td>';
		$return .= '<td>' . form_button($form['remove'][$group->id]) . '</td>';
	} else
		$return .= '<td>' . $group->significance . '</td>';
	return $return . '</tr>';
}
?>
<div class="centered">
<?php $lastchar = substr($user->username, strlen($user->username)-1); ?>
	<h1 class="text-contrast in-eyecatcher"><?=ph($user->username)?>'<?php if($lastchar != 'z' && $lastchar != 's') echo 's'; ?> groups</h1>
	<a href="<?=site_url('permission/user/' . $user->id . '/');?>" class="full-width link-button">Go to this users permissions</a>
	<?php if($add_form): ?>
		<?php if($add_group_error): ?>
			<h2>Error</h2>
			<p><?=$add_group_error;?></p>
		<?php endif; ?>
		<h2>Add to group</h2>
		The group-name must match the group (exactly; except case-sensitivness). <a href="<?=site_url('permission/groups')?>">List of groups</a> and <a href="<?=site_url('permission/groups')?>#add_group_form">Add group</a>.<br />
		The group will automatically have the lowest significance.
	<?=form_open();?>
		<div class="search">
			<div class="text">
				<?=form_input($add_form['group']);?>
			</div>
			<div class="submit">
				<?=form_input($add_form['submit']);?>
			</div>
		</div>
	<?=form_close();?>
	<?php endif; ?>
	<?php if($errors): ?>
	<h2>Error<?php if(count($errors['messages']) > 1) echo 's'; ?></h2>
	<p>Same significances are not allowed. The following groups have the same significance:</p>
	<ul>
	<?php foreach($errors['messages'] as $e): ?>
		<li><?=$e?></li>
	<?php endforeach; ?>
	</ul>
	<?php endif; ?>
	<?php if(strtolower($order) == 'desc') : ?>
		<a href="<?=site_url('permission/user/' . $user->id . '/groups/ASC');?>">Order by significance ascending.</a>
	<?php else: ?>
		<a href="<?=site_url('permission/user/' . $user->id . '/groups');?>">Order by significance descending.</a>
	<?php endif; ?> (changes will be discarded)
	<?php if($form) echo form_open(); ?>
	<table class="info">
		<thead>
			<tr>
				<td>Name</td>
				<td>Description</td>
				<td><a title="Significance: if the same permission is set by multiple groups the setting of the group with the highest significance will be used.">S</a></td>
				<?php if($form): ?>
					<td><a title="Remove user from this group.">R</a></td>
				<?php endif; ?>
			</tr>
		</thead>
		<?php foreach($groups as $g): ?>
			<?=show_groups_table($g, $form, $errors)?>
		<?php endforeach; ?>
	</table>
	<?php if($form) echo form_input($form['submit']); ?>
	<?php if($form) echo form_close(); ?>
</div>