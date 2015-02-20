<div class="centered">
	<h1 class="text-contrast in-eyecatcher">Permissions</h1>
	<h2>Where to go?</h2>
	<table class="info">
		<tr class="linked">
			<td><a href="<?=site_url('permission/groups');?>">Edit permission-groups</a></td>
		</tr>
		<tr>
			<td>To edit the permissions of a user go to his edit-page and click on 'Edit users permissions' or go there with pasting the users profile-link:</td>
		</td>
	</table>
	<?php if($error): ?>
	<h3>Error</h3>
	<p><?=$error;?></p>
	<?php endif; ?>
	<?=form_open();?>
	<div class="search">
		<div class="text"><?=form_input($form['input']);?></div>
		<div class="submit"><?=form_input($form['submit']);?></div>
	</div>
	<?=form_close();?>
	<h2>How does the system work?</h2>
	<h3>Levels</h3>
	<p>The system is divided in three levels: The user, the group and the default-level. <br />
	The highest-level is the user-level, the medium-level is the group-level and the lowest is the default level.</p>
	<p>For normal users without any group the default-level will be used. The defaults are preconfigured and can not be changed. If a user is in a group, the permissions default settings will be overwritten by the group (if defined in the group). If this permission is also overwritten on the user-level the settings of this level will take effect.</p>
	<h3>Group significances</h3>
	<p>For each group a user is in a significance is set. It is not possible to have two groups with the same significance. The significance defines the importance of a group for the user. So if a user is in two (or more) groups which all set permissions for the same item the setting of the group with the highest significance will take effect.</p>
	<h3>Parents &amp; Childs</h3>
	<p>Many permissions have parents and childs. If a user needs a permission the user also needs all parental permissions. The permissions for the parents do not have to be set in the same group. The childs will only displayed if the parent of it is viewed. The parents are on the very left, the childs on the right. Siblings are possible, a permission can have multiple parents on different levels (like parents and grand-parents) but not on the same level.</p>
</div>