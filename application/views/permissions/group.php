<div class="centered">
	<h1 class="text-contrast in-eyecatcher"><?=$group->name;?></h1>
	<p><em>Description:</em> <?=$group->description;?></p>
	<h1>Permissions</h1>
	<div class="search">

	</div>
	<?php if($edit_permission_groups): ?>
		<?=form_open();?>
	<?php endif; ?>
	<table class="info">
		<thead>
			<tr>
				<td>Name</td>
				<td>Description</td>
				<td><a title="has permission-children">C</a></td>
				<td><a title="has this permission">P</a></td>
				<?php if($edit_permission_groups): ?>
				<td><a title="reset to default">D</a></td>
				<?php endif; ?>
			</tr>
		</thead>
		<?php $i = 0; foreach($permissions as $p) : ?>
			<tr class="linked">
				<td><a href="<?=site_url('permission/group/' . $id . '/' . urlencode($p->name));?>"><?=$p->name;?></a></td>
				<td><a href="<?=site_url('permission/group/' . $id . '/' . urlencode($p->name));?>"><?=$p->description;?></a></td>
				<td><?=($p->has_childrens) ? '&#x2714;' : '&#x2716;';?></td>
				<?php if($edit_permission_groups) : ?>
					<td><?=form_input($form['pointers'][$i]) . form_input($form['permissions'][$i]);?></td>
					<td><?=form_input($form['defaults'][$i]);?></td>
				<?php else: ?>
					<td><?php if($p->has_permission === true || $p->has_permission === false): ?>
					<a title="specifically set for this group"><?=($p->has_permission) ? '&#x2714;' : '&#x2716;';?></a>
					<?php elseif($p->has_permission === 0): ?>
					<a title="by default"><?=($p->default) ? '&#x2714;' : '&#x2716;';?></a>
					<?php endif; ?></td>
				<?php endif; ?>
			</tr>
		<?php $i++; 
		endforeach; ?>
	</table>
	<?php if($edit_permission_groups): ?>
		<?=form_input($form['submit']);?>
		<?=form_close();?>
	<?php endif; ?>
	<div class="vertical-nav">
	<?php 
	if($start <= $max_pagination):
		if($start != 1):
			$i = ($start > 5) ? $start-5 : 1;
			while($start != $i) :
		?><a href="<?=site_url('permission/group/' . $id . '/' .  $i . '/' . $results);?>"><?=$i?></a><?php 
			$i++;
			endwhile;
		endif;
		?><a class="current"><?=$start?></a><?php
		if($start != $max_pagination):
			$i = ($start < ($max_pagination-5)) ? $start+5 : $max_pagination;
			while($start != $i) :
			$start++;
		?><a href="<?=site_url('permission/group/' . $id . '/' .  $start . '/' . $results);?>"><?=$start?></a><?php
			endwhile;
		endif;
	else:
		$i = ($max_pagination > 5) ? $max_pagination-5 : 1;
		while($max_pagination >= $i):
		?><a href="<?=site_url('permission/group/' . $id . '/' .  $i . '/' . $results);?>"><?=$i;?></a><?php 
		$i++;
		endwhile; 
	endif;
	?>
	</div>
</div>