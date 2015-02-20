<?php
if(!function_exists('create_html_from_array')) {
	/**
	 * Creates HTML from blog-array
	 * @param array $array The blog-array
	 * @return string HTML-Blog-data
	 */
	function create_html_from_array($array)
	{
		$return = '';
		if(!is_array($array)) { if(is_string($array) || is_numeric($array)) return $array; else return ''; }
		foreach($array as $k => $v)
		{
			if($k == 'before') $return .= $v;
			if($k == 'tag')
			{
				$return .= '<' . $v;
				if(isset($array['attributes']))
				{
					foreach($array['attributes'] as $ak => $av) {
						$return .= ' ' . $ak . '=';
						if(strpos($av, '"') === false) $return .= '"' . $av . '"';
						else $return .= "'" . $av . "'";
					}
				}
				$return .= '>';
			}
			if($k == 'closing_tag')
			{
				foreach($v as $cv)
				{
					$return .= '</' . $cv . '>';
				}
			}
			if($k == 'after')
			{
				$return .= call_user_func(__FUNCTION__, $v);
			}
		}
		return $return;
	}
}
if(!function_exists('create_blog_post_from_array')) {
	/**
	 * Creates a full blog-entry from a blog-array
	 * @param array $array blog-array
	 * @param string $entry_id the ID of the entry
	 * @param bool $stop_on_pagebreak TRUE: Stops on first pagebreak
	 * @param array $open_tags Open tags before the function is called (needed because of recursiveness)
	 * @param int $id The id used for inside html-ids to prevent multiple same ID's in one DOM
	 * @param bool $ireid FALSE: does not add a readmore-link on pagebreak 
	 * @return string HTML-Markup of the post
	 */
	function create_blog_post_from_array($array, $entry_id, $stop_on_pagebreak = false, $open_tags = array(), $id = false, $ireid = true)
	{
		if(!isint($entry_id)) { 
			trigger_error('No $entry_id set.'); 
			$entry_id = rand(0, 9999);
			$ireid = false;
		}
		
		if(!is_array($array)) { if(is_string($array) || is_numeric($array)) return $array; else return ''; }

		if(!is_int($id)) $id = 0;
		$singleton_tags = array(
			"img",
			"base",
			"br",
			"col",
			"command",
			"embed",
			"hr",
			"input",
			"link",
			"meta",
			"param",
			"source");
		$return = '';
		foreach($array as $k => $v)
		{
			if($k == 'before') {
				if($stop_on_pagebreak == true && strpos($v, '<!-- pagebreak -->') !== false)
				{
					$return = explode('<!-- pagebreak -->', $v)[0];
					if($ireid) $return .= '<a href="' . site_url('blog/view/' . $entry_id) . '">Read more...</a>';
					foreach($array['closing_tag'] as $cv)
					{
						unset($open_tags[count($open_tags)-1]);
						$return .= '</' . $cv . '>';
					}
					return $return;
				}
				$return .= $v;
				continue;
			}
			if($k == 'tag')
			{
				$aatot = false;		// aatot = Add Attributes To Open Tags ;-)
				if(!in_array($v, $singleton_tags)) { 
					$open_tags[] = array('tag' => $v);
					$aatot = true;
				}
				if(isset($array['attributes']['class']) && $v == 'img' && $array['attributes']['class'] != 'inline')	
				{
					for($i = (count($open_tags)-1); $i > -1; $i--) { if($open_tags[$i]['tag'] != 'a') $return .= '</' . $open_tags[$i]['tag'] . '>'; }
					$return .= '</div></div>
	<div class="blog-image ' . $array['attributes']['class'] . '" id="img-' . $entry_id . '-' . $id . '">';
	if(strpos($array['attributes']['class'], 'collapse') !== false) $return .= '<a href="#img-' . $entry_id . '-' . $id . '" class="image-expand"></a><a href="#closer" class="image-closer"></a>';
	$return .= '<svg class="image-showcase top" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="100%" height="100px" viewBox="0 0 100 101" enable-background="new 0 0 100 101" xml:space="preserve" preserveAspectRatio="none">
	<polygon fill="#CCCCCC" points="-1,0 100,0 100,101 "/>
	<polygon fill="#FFFFFF" points="0,0 100,0 100,100 "/>
	</svg>';
				}

				$return .= '<' . $v;
				if(isset($array['attributes']))
				{
					foreach($array['attributes'] as $ak => $av) {
						if($aatot) $open_tags[count($open_tags)-1]['attributes'][$ak] = $av;
						$return .= ' ' . $ak . '=';
						if(strpos($av, '"') === false) $return .= '"' . $av . '"';
						else $return .= "'" . $av . "'";
					}
				}
				$return .= '>';

				if(isset($array['attributes']['class']) && $v == 'img' && $array['attributes']['class'] != 'inline')
				{
					$return .= '<svg class="image-showcase bottom" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="100%" height="100px" viewBox="0 0 100 101" enable-background="new 0 0 100 101" xml:space="preserve" preserveAspectRatio="none">
	<polygon fill="#CCCCCC" points="101,101 0,101 0,0 "/>
	<polygon fill="#FFFFFF" points="100,101 0,101 0,1 "/>
	</svg>
	</div>
					<div class="wrapper"><div class="blog-centered">';
					foreach($open_tags as $o)
					{
						if($o['tag'] == 'a') continue;
						$return .= '<' . $o['tag'];
						if(isset($o['attributes']))
						{
							foreach($o['attributes'] as $ak => $av)
							{
								$return .= ' ' . $ak . '=';
								if(strpos($av, '"') === false) $return .= '"' . $av . '"';
								else $return .= "'" . $av . "'";
							}
						}
						$return .= '>';
					}
				}
				continue;
			}
			if($k == 'closing_tag')
			{
				foreach($v as $cv)
				{
					unset($open_tags[count($open_tags)-1]);
					$return .= '</' . $cv . '>';
				}
				continue;
			}
			if($k == 'after')
			{
				$return .= call_user_func(__FUNCTION__, $v, $entry_id, $stop_on_pagebreak, $open_tags, $id++, $ireid);
				continue;
			}
		}
		return $return;
	}
}
?>