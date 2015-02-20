<?php
/*
function add_irc_colors($string) {
	

	// $pattern = "/(.{2})(?:(u[0-9f]{6})|(u[0-9f]{4}))(?:,?(\d*))/";
	// $i = 0;
	// $string = preg_replace_callback($pattern, function ($matches) use (&$i, $irccolors, $bgirccolors) {
	// 	$return = '';
	// 	if($matches[1] != '\\\\' && isset($irccolors[$matches[2]]) && $irccolors[$matches[2]] != false)
	// 	{
	// 		$return .= $matches[1][0];
	// 		if($i > 0) $return .= '</span>';
	// 		$i++;
	// 		$background = '';
	// 		$ar = '';
	// 		if($matches[4] != '' && isset($irccolors[$matches[4]])) $background = ' ' . $bgirccolors[$matches[4]] . ';';
	// 		elseif($matches[4] != '') $ar .= ',' . $matches[4];
	// 		return $return . '<span class="irc-color" style="' . $irccolors[$matches[2]] . $background . '">' . $ar;
	// 	} elseif(!empty($matches[3])) {
	// 		$return .= $matches[1][0];
	// 		if($i > 0) $return .= '</span>';
	// 		if(isset($irccolors[$matches[4]]) && $irccolors[$matches[4]] != false) {
	// 			$return .= '<span class="irc-color" style="' . $irccolors[$matches[4]] . '">';
	// 			$i++;
	// 		} else $i = 0;
	// 		return $return;
	// 	} else {

	// 		var_dump($matches);
	// 		return $matches[0];
	// 	}
	// }, $string);
	// if($i > 0) $string .= '</span>';
	// return $string;
}
*/
function add_irc_colors($string)
{
	$find_pattern = "/\\\u([0-9f]{6}|[0-9f]{4})(?:,([0-9]{0,2}))?/";
	preg_match_all($find_pattern, $string, $matches, PREG_OFFSET_CAPTURE);
	$i = 0;
	$j = 0;
	$string = preg_replace_callback($find_pattern, function ($m) use ($matches, &$i, &$j, $string) {
		$r = '';
		$offset = $matches[0][$i][1];
		if($offset > 0 && $string[$offset-1] != '\\') {
			$irccolors = array(
				'000300' => 'color: #FFFFFF; ', 
				'000301' => 'color: #000000; ', 
				'000302' => 'color: #142747; ', 
				'000303' => 'color: #11421E; ', 
				'000304' => 'color: #BF3C4C; ', 
				'000305' => 'color: #822821; ', 
				'000306' => 'color: #A8014D; ', 
				'000307' => 'color: #D95436; ', 
				'000308' => 'color: #FFCB0D; ', 
				'000309' => 'color: #8BFF83; ', 
				'000310' => 'color: #009696; ', 
				'000311' => 'color: #00FAE8; ', 
				'000312' => 'color: #00C7FF; ', 
				'000313' => 'color: #F06BF2; ', 
				'000314' => 'color: #67666A; ', 
				'000315' => 'color: #CBC9CF; ', 
				'0002' => 'font-weight: bold; ',
				'000f' => false
			);
			$bgirccolors = array(
				'1'  => 'background-color: #000000; ',
				'2'  => 'background-color: #272649; ',
				'3'  => 'background-color: #00D120; ',
				'4'  => 'background-color: #D4180A; ',
				'5'  => 'background-color: #B97E43; ',
				'6'  => 'background-color: #674EBF; ',
				'7'  => 'background-color: #277820; ',
				'8'  => 'background-color: #F5E912; ',
				'9'  => 'background-color: #10FF3F; ',
				'10' => 'background-color: #0F787F; ',
				'11' => 'background-color: #1DEFFF; ',
				'12' => 'background-color: #00008D; ',
				'13' => 'background-color: #F285B8; ',
				'14' => 'background-color: #414549; ',
				'15' => 'background-color: #BEBEBE; ',
				'16' => 'background-color: #FFFFFF; '
			);
			if(isset($irccolors[$m[1]]) && $irccolors[$m[1]] === false)
			{
				if($j != 0) $r .= '</span>';
				$j = 0;
				return $r;
			} elseif(isset($irccolors[$m[1]])) {
				if($j != 0) $r .= '</span>';
				$r .= '<span style="' . $irccolors[$m[1]];
				if(isset($m[2]) && isset($bgirccolors[$m[2]]))
				{
					$r .= $bgirccolors[$m[2]];
				}
				$r = substr($r, 0, strlen($r) - 1);
				$r .= '">';
				$j++;
				return $r;
			}
		}
		$i++;
	}, $string);
	return $string;
}
function _irc_link_links($string) {
	return preg_replace("/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,9}([^\s\\\]*)?/", '<a target="_blank" href="$0">$0</a>', $string);
}
$users_fallback = $start_users;
if(!$full_width): ?>
<div class="centered">
<?php endif; ?>
	<h1 class="text-contrast in-eyecatcher">IRC-Log</h1>
<?php if(!$full_width): ?>
	<a href="<?=site_url('irclog/full/' . $start . '/' . $results);?>">View with full screen width</a>
<?php else: ?>
	<a href="<?=site_url('irclog/' . $start . '/' . $results);?>">View with normal width</a>
<?php endif; ?>
	<div id="log">
		<div id="user-list">
			<span class="headline">Users <span class="users-count right">(<?=count((array) $start_users)?>)</span></span>
			<ul>
				<li class="title"><?=dt_tm($log[count($log)-1]->timestamp)?></li>
				<?php 
				$start_users = (array) $start_users;
				krsort($start_users);
				arsort($start_users);
				foreach($start_users as $u => $s): ?>
					<li class="default"><?=$s.$u?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<table id="irc-log">
			<thead>
				<tr>
					<td id="tbl-time">Time</td>
					<td id="tbl-msg">Message</td>
				</tr>
			</thead>
			<tbody>
				<?php foreach($log as $l) : ?>
					<tr class="<?=$l->mtype?>">
						<td title="<?=strip_tags(dt($l->timestamp))?>"><?=tm($l->timestamp);?></td>
					<?php if($l->mtype == 'user_message'): ?>
						<td>
							<?php if($l->type == 'message') : ?>
								<?=ph($l->nickname)?>: <?=add_irc_colors(_irc_link_links(ph($l->text)))?>
							<?php elseif($l->type == 'action') : ?>
								&mdash; <em><?=ph($l->nickname)?> <?=add_irc_colors(_irc_link_links(ph($l->text)))?></em>
							<?php endif; ?>
						</td>
					<?php elseif($l->mtype == 'user_connection'): ?>
						<td class="user-action user-connection" data-user-list='<?=p_r($l->text)?>'>
						<?php if($l->type == 'join') : ?>
							<em><?=ph($l->nickname)?></em> joined <?=$channel;?>.
						<?php elseif($l->type == 'part' || $l->type == 'quit') : ?>
							<em><?=ph($l->nickname)?></em> left <?=$channel?>.
						<?php elseif($l->type == 'bot-connect') : ?>
							The bot joined the channel.
						<?php endif; ?>
						</td>
					<?php elseif($l->mtype == 'user_renaming') : ?>
						<td class="user-action user-renaming" data-user-rename='<?=p_r(json_encode(array($l->nickname, $l->newnick), JSON_HEX_QUOT))?>'>
						<em><?=ph($l->nickname);?></em> is now known as <em><?=ph($l->newnick);?>.</em>
						</td>
					<?php endif; ?>
					</tr>
				<?php endforeach; ?>
				<tr style="display: none;" class="user_connection">
					<td class="user-action user-connection" data-user-list='<?=p_r(json_encode($users_fallback))?>'></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="vertical-nav">
	<?php 
	if($start <= $max_pagination):
		$fstring = ($full_width) ? 'full/' : '';
		if($start != 1):
			$i = ($start > 5) ? $start-5 : 1;
			while($start != $i) :
		?><a href="<?=site_url('irclog/' . $fstring . $i . '/' . $results);?>"><?=$i?></a><?php 
			$i++;
			endwhile;
		endif;
		?><a class="current"><?=$start?></a><?php
		if($start != $max_pagination):
			$i = ($start < ($max_pagination-5)) ? $start+5 : $max_pagination;
			while($start != $i) :
			$start++;
		?><a href="<?=site_url('irclog/' . $fstring . $start . '/' . $results);?>"><?=$start?></a><?php
			endwhile;
		endif;
	else:
		$i = ($max_pagination > 5) ? $max_pagination-5 : 1;
		while($max_pagination >= $i):
		?><a href="<?=site_url('irclog/' . $fstring . $i . '/' . $results);?>"><?=$i;?></a><?php 
		$i++;
		endwhile; 
	endif;
	?>
	</div>
<?php if(!$full_width): ?>
</div>
<?php endif; ?>