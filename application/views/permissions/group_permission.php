	<?php
	function _create_recursive_table($permissions, $form = false)
	{
		$return = '<table>';
		foreach($permissions as $p) {
			$return .= '<tr><td class="name';
			if(!$p['childs']) $return .= ' no-childs';
			$return .= '"><a>' . $p['name'] . '</a></td><td rowspan="3" class="contains-table">';
			if($p['childs']) $return .= '<span class="small-triangle"><span class="small-triangle-right"></span></span>' . _create_recursive_table($p['childs'], $form);
			$return .= '</td></tr>';
			$return .= '<tr><td class="description';
			if(!$p['childs']) $return .= ' no-childs';
			$return .= '">' . $p['description'] . '</td></tr>';
			$return .= '<tr><td class="permissions">';
			if($form) {
				$return .= form_input($form['pointers'][$p['id']]);

				$return .= form_label('Permission ', $form['labels']['permissions'][$p['id']]['for'], $form['labels']['permissions'][$p['id']]);
				$return .= form_input($form['permissions'][$p['id']]);

				$return .= ' - ';

				$return .= form_label('Default ', $form['labels']['defaults'][$p['id']]['for'], $form['labels']['defaults'][$p['id']]);
				$return .= form_input($form['defaults'][$p['id']]);
			} else {
				$return .= '<a title="has this permission">Permission</a> ';
				if($p['has_permission'] === true) $return .= '&#x2714;';
				elseif($p['has_permission'] === false) $return .= '&#x2716;';
				elseif($p['default'] == true) $return .= '&#x2714;';
				else $return .= '&#x2716;';
			}
			$return .= '</td></tr>';
		}
		$return .= '</table>';
		return $return;
	}
	?>

	<div class="centered">
		<h1 class="text-contrast in-eyecatcher">Group '<?=$group->name?>'</h1>
		<p><em>Description:</em> <?=$group->description;?></p>
		<h1>Permission '<?=$permissions['name']?>'</h1>
		<p><em>Description:</em> <?=$permissions['description'];?></p>
		<h2>Edit permissions for this group</h2>
	</div>
</div>
<?php if($form) : ?>
<?=form_open();?>
<?php endif; ?>
	<div class="vertical-overflow-scroll">
		<table class="vertical-editable">
			<tr>
				<td class="name">
					<a><?=$permissions['name']?></a>
				</td>
				<td rowspan="3">
					<span class="small-triangle left"><span class="small-triangle-right"></span></span>
					<?=_create_recursive_table($permissions['childs'], $form);?>
				</td>
			</tr>
			<tr>
				<td class="description">
					<?=$permissions['description'];?>
				</td>
			</tr>
			<tr>
				<td class="permissions">
				<?php
				if($form) {
					echo form_input($form['pointers'][$permissions['id']]);

					echo form_label('Permission ', $form['labels']['permissions'][$permissions['id']]['for'], $form['labels']['permissions'][$permissions['id']]);
					echo form_input($form['permissions'][$permissions['id']]);

					echo ' - ';

					echo form_label('Default ', $form['labels']['defaults'][$permissions['id']]['for'], $form['labels']['defaults'][$permissions['id']]);
					echo form_input($form['defaults'][$permissions['id']]);
				} else {
					echo '<a title="has this permission">Permission</a> ';
					if($permissions['has_permission'] === true) echo '&#x2714;';
					elseif($permissions['has_permission'] === false) echo '&#x2716;';
					elseif($permissions['default'] === true) echo '&#x2714;';
					else echo '&#x2716;';
				}
				?>
				</td>
			</tr>
		</table>
	</div>
	<?php if($form) : ?>
	<div class="wrapper">
		<div class="centered">
			<?=form_input($form['submit']);?>
		</div>
	</div>
<?=form_close();?>
<?php endif; ?>

<div class="wrapper">