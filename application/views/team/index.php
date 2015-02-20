<div class="centered">
	<h1 class="text-contrast in-eyecatcher">The {sitetitle}-Team</h1>
	<p>The {sitetitle}-team consists of multiple projects.
		<ul>
			<li>The <a href="#sauerfork-main">main-project</a> with all the new features like the ambient-occlusion, new particle-system or atom-shell.</li>
			<li>The <a href="#sauerfork-data">data-part</a> of the project with its new maps, models and textures.</li>
			<li>The <a href="#sauerfork-website">website</a> with all its features and community system.</li>
		</ul>
	</p>

	<h2 id="sauerfork-main">{sitetitle}</h2>
	
	<?php $has_section = false; 
	$section = 'main';
	foreach($developer[$section] as $uid => $d): 
		$this_user = $devusers[$uid];
		$pedit = false;
		if($user && $this_user->id == $user->id && $edit) { $pedit = true; $has_section = true; }
	?>
		<div class="dev-spotlight">
			<a href="<?=site_url('user/' . $uid);?>" target="_blank" class="center">
				<div class="avatar" style="background-image:url(<?=avatar_image($uid)?>);"></div>
				<span class="user"><?=showname($this_user, '')?></span>
			</a>
			<div class="dev-info">
				<span>
					Working on:
				</span><span>
					Already done:
				</span>
			</div>
			<div class="clear"></div>
			<div class="topics">
				<div class="working-on">
					<ul>
						<?php foreach($d['undone'] as $id => $w): ?>
							<li><?=ph($w)?><?php if($pedit) echo ' <a title="delete" href="' . site_url('team/delete/' . $id) . '">&times;</a> <a title="set as done" href="' . site_url('team/done/' . $id) . '">&rarr;</a>'; ?></li>
						<?php endforeach; ?>
						<?php if($edit) : ?>
							<li>
								<?=form_open().form_input($form['input']).form_input($form['hidden_' . $section]).form_input($form['submit']).form_close()?><?php if($error) { echo '<br />' . $error; } ?>
							</li>
						<?php endif; ?>
					</ul>
				</div><div class="work-finished">
					<ul>
						<?php foreach($d['done'] as $id => $w): ?>
							<li><?=ph($w)?><?php if($pedit) echo ' <a title="delete" href="' . site_url('team/delete/' . $id) . '">&times;</a> <a title="set as working on" href="' . site_url('team/undone/' . $id) . '">&larr;</a>'; ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
	<?php if($edit && !$has_section): ?>
		<p>Add me to the entry and create following entry:
			<?=form_open().form_input($form['input']).form_input($form['hidden_' . $section]).form_input($form['submit']).form_close()?>
			<?php if($error) { echo '<br />' . $error; } ?>
		</p>
	<?php endif; ?>

	<h2 id="sauerfork-data">{sitetitle} Data</h2>

	<?php $has_section = false; 
	$section = 'data';
	foreach($developer[$section] as $uid => $d): 
		$this_user = $devusers[$uid];
		$pedit = false;
		if($user && $this_user->id == $user->id && $edit) { $pedit = true; $has_section = true; }
	?>
		<div class="dev-spotlight">
			<a href="<?=site_url('user/' . $uid);?>" target="_blank" class="center">
				<div class="avatar" style="background-image:url(<?=avatar_image($uid)?>);"></div>
				<span class="user"><?=showname($this_user, '')?></span>
			</a>
			<div class="dev-info">
				<span>
					Working on:
				</span><span>
					Already done:
				</span>
			</div>
			<div class="clear"></div>
			<div class="topics">
				<div class="working-on">
					<ul>
						<?php foreach($d['undone'] as $id => $w): ?>
							<li><?=ph($w)?><?php if($pedit) echo ' <a title="delete" href="' . site_url('team/delete/' . $id) . '">&times;</a> <a title="set as done" href="' . site_url('team/done/' . $id) . '">&rarr;</a>'; ?></li>
						<?php endforeach; ?>
						<?php if($edit) : ?>
							<li>
								<?=form_open().form_input($form['input']).form_input($form['hidden_' . $section]).form_input($form['submit']).form_close()?><?php if($error) { echo '<br />' . $error; } ?>
							</li>
						<?php endif; ?>
					</ul>
				</div><div class="work-finished">
					<ul>
						<?php foreach($d['done'] as $id => $w): ?>
							<li><?=ph($w)?><?php if($pedit) echo ' <a title="delete" href="' . site_url('team/delete/' . $id) . '">&times;</a> <a title="set as working on" href="' . site_url('team/undone/' . $id) . '">&larr;</a>'; ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
	<?php if($edit && !$has_section): ?>
		<p>Add me to the entry and create following entry:
			<?=form_open().form_input($form['input']).form_input($form['hidden_' . $section]).form_input($form['submit']).form_close()?>
			<?php if($error) { echo '<br />' . $error; } ?>
		</p>
	<?php endif; ?>

	<h2 id="sauerfork-website">{sitetitle} Website</h2>

	<?php $has_section = false; 
	$section = 'website';
	foreach($developer[$section] as $uid => $d): 
		$this_user = $devusers[$uid];
		$pedit = false;
		if($user && $this_user->id == $user->id && $edit) { $pedit = true; $has_section = true; }
	?>
		<div class="dev-spotlight">
			<a href="<?=site_url('user/' . $uid);?>" target="_blank" class="center">
				<div class="avatar" style="background-image:url(<?=avatar_image($uid)?>);"></div>
				<span class="user"><?=showname($this_user, '')?></span>
			</a>
			<div class="dev-info">
				<span>
					Working on:
				</span><span>
					Already done:
				</span>
			</div>
			<div class="clear"></div>
			<div class="topics">
				<div class="working-on">
					<ul>
						<?php foreach($d['undone'] as $id => $w): ?>
							<li><?=ph($w)?><?php if($pedit) echo ' <a title="delete" href="' . site_url('team/delete/' . $id) . '">&times;</a> <a title="set as done" href="' . site_url('team/done/' . $id) . '">&rarr;</a>'; ?></li>
						<?php endforeach; ?>
						<?php if($edit) : ?>
							<li>
								<?=form_open().form_input($form['input']).form_input($form['hidden_' . $section]).form_input($form['submit']).form_close()?><?php if($error) { echo '<br />' . $error; } ?>
							</li>
						<?php endif; ?>
					</ul>
				</div><div class="work-finished">
					<ul>
						<?php foreach($d['done'] as $id => $w): ?>
							<li><?=ph($w)?><?php if($pedit) echo ' <a title="delete" href="' . site_url('team/delete/' . $id) . '">&times;</a> <a title="set as working on" href="' . site_url('team/undone/' . $id) . '">&larr;</a>'; ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
	<?php if($edit && !$has_section): ?>
		<p>Add me to the entry and create following entry:
			<?=form_open().form_input($form['input']).form_input($form['hidden_' . $section]).form_input($form['submit']).form_close()?>
			<?php if($error) { echo '<br />' . $error; } ?>
		</p>
	<?php endif; ?>
</div>