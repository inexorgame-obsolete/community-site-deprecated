<div class="centered">
	<h1 class="text-contrast in-eyecatcher">Activation <?=$success ? 'S' : 'Uns'?>uccessful</h1>
	<?php if($success): ?>
		<p>Your activation was successful. You are now able to login at <a href="<?=site_url('user/login')?>">here</a>.<p>
		<p>Congratulations.</p>
	<?php else: ?>
		<p>Your activation was not successful. There are multiple possibilities why this can happen:</p>
		<ul>
			<li>The username in the url is wrong.</li>
			<li>The verification-code is wrong.</li>
			<li>The verification-code is expired.</li>
		</ul>
	<?php endif; ?>
</div>