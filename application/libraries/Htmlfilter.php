<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Filters a string for HTML-tags and allows specific (in a filter specified) tags,
 * Example-Filter:
 * 
 * 	$filter = array(
 * 		"element" => array(
 * 			"attribute" => "content (as normal text or as array with allowed texts or as regular expression)",
 * 			"style" => array(
 * 				"attribute" => array(
 * 					"option 1",
 * 					"option 2"
 * 				),
 * 				"attribute2" => array(
 * 					"px" => array("500", "600"),
 * 					"em" => array("1", "1.1")
 * 				)
 * 			)
 * 		)
 * 	)
 */


/*

Filters a string for HTML-tags and allows specific (in a filter specified) tags,
Example-Filter:

$filter = array(
	"element" => array(
		"attribute" => "content (as normal text or as array with allowed texts or as regular expression)",
		"style" => array(
			"attribute" => array(
				"option 1",
				"option 2"
			),
			"attribute2" => array(
				"px" => array("500", "600"),
				"em" => array("1", "1.1")
			)
		)
	)
)


*/


class Htmlfilter {
	// The filter (as specified above)
	public $filter;

	// The markup to filter
	public $markup;

	// will be used as str_replace($replaces['search'], $replaces['replace'], $text); Will be executed on TEXT AND DISALLOWED TAGS ONLY, won't be executed on valid tags, classes and other attributes
	public $replaces = false;

	// Replaces for attributes
	public $attr_replaces = false;

	// Singleton tags (tags not needing a closing tag)
	public $singleton_tags = array(
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
		"source"
		);

	// CSS units
	public $css_units = array(
		"em",
		"px",
		"%",
		"ex",
		"cm",
		"mm",
		"in",
		"pt",
		"pc"
		);

	/**
	 * Magic Method __construct();
	 */
	public function __construct() {}

	/**
	 * Parses the markup
	 */
	public function parse()
	{
		if(!is_array($this->filter)) { trigger_error("No filter defined", E_USER_ERROR); return; }
		if(!is_string($this->markup)) { trigger_error("No content defined", E_USER_ERROR); return; }
		$markup = $this->markup;
		$filter = $this->filter;
		$markup = $this->_filter_tags($markup);
		$markup = $this->_validate_html($markup);
		$this->markup = $markup;
	}

	/**
	 * Creates an HTML-Markup-array which was not parsed and validated through the filter.
	 * So disallowed tags are still inside as normal HTML
	 * @param string $markup The markup which should be divided
	 * @param int $depth The depth of the current HTML-string
	 * @return array HTML-Markup-array
	 */
	private function _filter_tags($markup, $depth = 0) {
		$open_tag_regex = '/<([\/]?[A-Z][A-Z0-9]*)\b([^>]*)>/im';
		$attributes_regex = '/([^= "\'.]*)="([^"]*)"|([^= "\'.]*)=\'([^\']*)\'/';
		$original_markup = $markup;
		if(preg_match($open_tag_regex, $markup, $match, PREG_OFFSET_CAPTURE))
		{
			$markup = preg_split($open_tag_regex, $markup, 2);
			$tag = $match[1][0];
			preg_match_all($attributes_regex, $match[2][0], $attr_matches);
			$attributes = array();
			foreach($attr_matches[2] as $k => $v)
			{
				if(!empty($attr_matches[1][$k])) $attributes[$attr_matches[1][$k]] = $v;
			}
			foreach($attr_matches[4] as $k => $v)
			{
				if(!empty($attr_matches[3][$k])) $attributes[$attr_matches[3][$k]] = $v;
			}

			$return = array(
					'tag' => $tag
				);
			if(count($attributes) != 0) $return['attributes'] = $attributes;
			if(!empty($markup[0])) $return['before'] = $markup[0];
			$return['after'] = call_user_func(array($this, __FUNCTION__), $markup[1], $depth + 1);
		} else {
			if($depth === 0) 
			{
				$return['before'] = $markup;
			} else {
				$return = $markup;
			}
		}

		return $return;
	}

	/**
	 * Validates the content of an HTML-Markup-array
	 * @param array $markup HTML-Markup-array
	 * @param array $opentags Currently open tags, needed if another tag is closed
	 * @return string Validated HTML-string where disallowed tags are escaped
	 */
	private function _validate_html($markup, $opentags = array())
	{
		$return = array();
		$return['before'] = '';
		if(isset($markup['before']))
		{
			$return['before'] .= $this->_escape_html($markup['before']);
		}
		if(isset($markup['tag'])) 
		{
			$tag = $markup['tag'];
			if(isset($this->filter[$tag])) $filter = $this->filter[$tag];
			else $filter = false;
			if($tag[0] != '/')
			{
				if(!in_array($tag, $this->singleton_tags)) $opentags[] = $tag;
				if($filter)
				{
					$return['tag'] = $tag;

					if(isset($markup['attributes']))
					{
						foreach($markup['attributes'] as $k => $v)
						{
							$k = strtolower($k);
							if($k == 'style' && isset($filter['style'])) {
								$validated_css = $this->_validate_style_attr($v, $filter['style']);
								$return['attributes']['style'] = $validated_css;
							} elseif(isset($filter[$k])) {
								if($filter[$k][0] == '/') 
								{
									if(preg_match($filter[$k], $v, $matches) && $matches[0] == $v)
									{
										$return['attributes'][$k] = (is_array($this->attr_replaces)) ? str_replace($this->attr_replaces['search'], $this->attr_replaces['replace'], $v) : $v;
									}
								}
								else
								{
									if($filter[$k] == $v || (is_array($filter[$k]) && in_array($v, $filter[$k])))
									{
										$return['attributes'][$k] = (is_array($this->attr_replaces)) ? str_replace($this->attr_replaces['search'], $this->attr_replaces['replace'], $v) : $v;
									}
								}
							}
						}
					}
				} else {
					$return['before'] .= '&lt;';
					$return['before'] .= $this->_escape_html($tag);
					if(isset($markup['attributes'])) {
						foreach($markup['attributes'] as $k => $v)
						{
							if(stpos($v, '"')!==false)
							{
								$return['before'] .= " " . $this->_escape_html($k) . "=&apos;" . $this->_escape_html($v) . "&apos;";
							}
							else
							{
								$return['before'] .= ' ' . $this->_escape_html($k) . '=&quot;' . $this->_escape_html($v) . '&quot;';
							}
						}
					}
					$return['before'] .= '&gt;';
				}
			} else {
				$tag = substr($tag, 1);
				if(in_array($tag, $opentags) && isset($this->filter[$tag])) 
				{
					$opentags = array_values($opentags);
					for($i = count($opentags) - 1; $i >= 0; $i--)
					{
						if($opentags[$i] == $tag)
						{
							$return['closing_tag'][] = $this->_escape_html($opentags[$i]);
							unset($opentags[$i]);
							break;
						} else {
							$return['closing_tag'][] = $this->_escape_html($opentags[$i]);
						}
						unset($opentags[$i]);
					}
				}
				else
				{
					$return['before'] .= '&lt;/' . $this->_escape_html($tag) . '&gt;';
				}
			}
		}
		if(isset($markup['after'])) {
			$return['after'] = $this->_validate_html($markup['after'], $opentags);
		}
		if(isset($this->replaces['search']) && isset($this->replaces['replace'])) {
			$return['before'] = str_replace($this->replaces['search'], $this->replaces['replace'], $return['before']);
		}
		if(empty($return['before'])) unset($return['before']);
		if(empty($return['after'])) unset($return['after']);
		if(empty($return['tag'])) unset($return['tag']);
		return $return;
	}

	/**
	 * Correctly escapes HTML
	 * @param string $string The string to be escaped
	 * @return string The correctly escaped string
	 */
	private function _escape_html($string)
	{
		return htmlentities($string, ENT_COMPAT | ENT_HTML5, 'UTF-8', FALSE);
	}

	/**
	 * Validates and correctly formats a style attribute
	 * @param string $style The CSS-style-string
	 * @param array $filter The CSS-filter
	 * @return string Correctly formated and validated CSS-style-string
	 */
	private function _validate_style_attr($style, $filter)
	{
		$return = '';
		$style = explode(";", $style);
		foreach($style as $v)
		{
			$v = explode(":", $v);
			if(empty($v[0])) continue;
			if($v[1][0] == ' ') {
				while($v[1][0] == ' ') $v[1] = substr($v[1], 1);
			}

			if(isset($filter[$v[0]])) {
				if(is_array($filter[$v[0]]))
				{
					$allow = false;
					foreach($filter[$v[0]] as $kk => $vv)
					{
						if(is_numeric($kk) && !is_array($vv) && $vv == strtolower($v[1]))
						{
							$allow = true;
							break;
						}
						if(is_numeric($kk) && is_array($vv) && in_array($v[1], $vv))
						{
							$allow = true;
							break;
						}
						if(!is_numeric($kk) && in_array($kk, $this->css_units))
						{
							if(strpos($v[1], $kk)!==false) {
								$intval = intval($v[1]);
								if(is_int($vv[0]) && is_int($vv[1])) {
									$smaller = ($vv[0] > $vv[1]) ? $vv[1] : $vv[0];
									$bigger = ($vv[0] < $vv[1]) ? $vv[1] : $vv[0];
								} else {
									$smaller = (is_int($vv[0])) ? $vv[0] : $intval - 1;
									$bigger = (is_int($vv[1])) ? $vv[1] : $intval + 1;
								}
								if($smaller < $intval && $intval < $bigger)
								{
									$allow = true;
								}
								break;
							}
						}
					}
					if($allow == true)
					{
						$return .= ' ' . htmlentities($v[0]) . ': ' . htmlentities($v[1]) . ';';
					}
				} 
				elseif(preg_match($filter[$v[0]], $v[1], $matches) && $matches[0] == $v[1])
				{
					$return .= ' ' . htmlentities($v[0]) . ': ' . htmlentities($v[1]) . ';';
				}
			}
		}

		return substr($return, 1);	// Remove first space
	}
}