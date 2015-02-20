<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['headlinefilter'] = array(
	"span" => array(
		"style" => array(
			"text-decoration" => array("underline", "line-through")
		)
	),
	"strong" => true,
	"em" => true,
	"sup" => true,
	"sub" => true,
	);

// The following are (more or less) wildcards for already escaped strings but can be used for other strings as well.
// It will be used like str_replace($config['replaces']['search'], $config['replaces']['replace'], $escaped_string);
$config['replaces'] = array(
	"search" => array("&lt;&excl;-- pagebreak --&gt;"),
	"replace" => array(       "<!-- pagebreak -->"   )
);

$config['bodyfilter'] = array(
	"p" => array(
		"style" => array(
			"text-align" => array("left", "center", "right", "justify")
		)
	),
	"a" => array(
		"href" => "/(.*)/",
		"target" => array("_blank", "_self", "_parent", "_top"),
		"title" => "/(.*)/"
	),
	"img" => array(
		"class" => array(
			"presentation",
			"presentation-collapse",
			"inline"
		),
		"src" => "/(.*)/",
		"alt" => "/(.*)/",
	),
	"strong" => true,
	"em" => true,
	"span" => array(
		"style" => array(
			"text-decoration" => array("underline", "line-through")
		)
	),
	"sup" => true,
	"sub" => true,
	"h1" => true,
	"h2" => true,
	"h3" => true,
	"h4" => true,
	"h5" => true,
	"h6" => true,
	"blockquote" => true,
	"div" => true,
	"table" => true,
	"tbody" => true,
	"tr" => true,
	"td" => true,
	"ol" => true,
	"ul" => true,
	"li" => true,
	"br" => true
);