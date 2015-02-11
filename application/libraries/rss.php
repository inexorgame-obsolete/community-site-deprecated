<?php
/*
	RSS Extractor and Displayer
	(c) 2007-2010  Scriptol.com - Licence Mozilla 1.1.
	rsslib.php
	
	Requirements:
	- PHP 5.
	- A RSS feed.
	
	Using the library:
	Insert this code into the page that displays the RSS feed:
	
	<?php
	require_once("rsslib.php");
	echo RSS_Display("http://www.xul.fr/rss.xml", 15);
	? >
	
*/

//$RSS_Content = array();

class RSS {
	
	public $Content;
	
	public function __construct()
	{
		$this->Content = array();
	}
	
	function Tags($item, $type)
	{
		$y = array();
	
		$tnl = $item->getElementsByTagName("title");
		if ($tnl->length == 1)
		{
			$tnl = $tnl->item(0);
			$title = $tnl->firstChild->textContent;
		}
	
		$tnl = $item->getElementsByTagName("link");
		if ($tnl->length == 1)
		{
			$tnl = $tnl->item(0);
			$link = $tnl->firstChild->textContent;
		}
	
		$tnl = $item->getElementsByTagName("pubDate");
		if ($tnl->length == 1)
		{
			$tnl = $tnl->item(0);
			$pubDate = $tnl->firstChild->textContent;
		}
			
		$tnl = $item->getElementsByTagName("lastBuiltDate");
		if ($tnl->length == 1)
		{
			$tnl = $tnl->item(0);
			$lastBuiltDate = $tnl->firstChild->textContent;
		}
			
		$tnl = $item->getElementsByTagName("description");
		if ($tnl->length == 1)
		{
			$tnl = $tnl->item(0);
			$description = $tnl->firstChild->textContent;
		}
	
		if (isset($title)) $y["title"] = $title;
		if (isset($link)) $y["link"] = $link;
		if (isset($pubDate)) $y["pubDate"] = $pubDate;
		if (isset($lastBuiltDate)) $y["lastBuiltDate"] = $lastBuiltDate;
		if (isset($description)) $y["description"] = $description;
		if (isset($type)) $y["type"] = $type;
	
		return $y;
	}
	
	
	function Channel($channel)
	{
	
		$items = $channel->getElementsByTagName("item");
	
		// Processing channel
	
		$y = $this->Tags($channel, 0);		// get description of channel, type 0
		array_push($this->Content, $y);
	
		// Processing articles
	
		foreach($items as $item)
		{
			$y =  $this->Tags($item, 1);	// get description of article, type 1
			array_push($this->Content, $y);
		}	
	}
	
	function Retrieve($url)
	{	
		$this->Content = array();
		$doc  = new DOMDocument();
		$doc->load($url);
	
		$channels = $doc->getElementsByTagName("channel");
	
		foreach($channels as $channel)
		{
			array_push($this->Content, $this->Channel($channel));
		}	
	}
	
	
	function RetrieveLinks($url)
	{
		//global $RSS_Content;
	
		$doc  = new DOMDocument();
		$doc->load($url);
	
		$channels = $doc->getElementsByTagName("channel");
	
		$this->Content = array();
	
		foreach($channels as $channel)
		{
			$items = $channel->getElementsByTagName("item");
			foreach($items as $item)
			{
				$y = $this->Tags($item, 1);	// get description of article, type 1
				array_push($this->Content, $y);
			}
				
		}	
	}
}

?>
