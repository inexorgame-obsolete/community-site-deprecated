<div class="centered">
	<h1 class="text-contrast in-eyecatcher">Permission Groups</h1>
	<div class="search">
	<?=form_open('permission/groups/', '');?>
		<div class="text">
			<?=form_input($search['search']);?>
		</div>
		<div class="submit">
			<?=form_input($search['submit']);?>
		</div>
		<?=form_input($search['start']);?>
		<?=form_input($search['limit']);?>
	<?=form_close()?>
	</div>
	<table class="info">
		<thead>
			<tr>
				<td>Permission</td>
				<td>Description</td>
			</tr>
		</thead>
		<?php foreach($groups as $g) : ?>
		<tr class="linked">
			<td><a href="<?=site_url('permission/group/' . $g->id);?>"><?=$g->name;?></a></td>
			<td><a href="<?=site_url('permission/group/' . $g->id);?>"><?=$g->description;?></a></td>
		</tr>
		<?php endforeach; ?>

		<?php if($start > $max_pagination): ?>
		<tr class="no-results">
			<td colspan="2">There are no results.</td>
		</tr>
		<?php endif; ?>
	</table>

	<div class="vertical-nav">
	<?php 
	if($start <= $max_pagination):
		if($start != 1):
			$i = ($start > 5) ? $start-5 : 1;
			while($start != $i) :
		?><a href="<?=site_url('permission/groups/' . urlencode($searchstring) . '/' .  $i . '/' . $results);?>"><?=$i?></a><?php 
			$i++;
			endwhile;
		endif;
		?><a class="current"><?=$start?></a><?php
		if($start != $max_pagination):
			$i = ($start < ($max_pagination-5)) ? $start+5 : $max_pagination;
			while($start != $i) :
			$start++;
		?><a href="<?=site_url('permission/groups/' . urlencode($searchstring) . '/' . $start . '/' . $results);?>"><?=$start?></a><?php
			endwhile;
		endif;
	else:
		$i = ($max_pagination > 5) ? $max_pagination-5 : 1;
		while($max_pagination >= $i):
		?><a href="<?=site_url('permission/groups/' . urlencode($searchstring) . '/' . $i . '/' . $results);?>"><?=$i;?></a><?php 
		$i++;
		endwhile; 
	endif;
	?>
	</div>

	<?php if($add_group) : ?>
	<?=form_open(false, 'class="large"');?>
		<h3>Add Group</h3>
		<div class="input"><?=form_label($add_group['name_label']['content'], $add_group['name_label']['for']) . form_input($add_group['name']);?></div>
		<div class="input"><?=form_label($add_group['description_label']['content'], $add_group['description_label']['for'], array('class' => $add_group['description_label']['class'])) . form_textarea($add_group['description']);?></div>
		<?php if(isset($add_group_error)): ?>
			<p><?=$add_group_error;?></p>
		<?php endif; ?>
		<div class="input"><?=form_submit($add_group['submit']);?></div>
	<?php endif; ?>
</div>