<ul class="list-group">

<?php
foreach ($Items as $Item)
{
	echo '<li class="list-group-item">';
	//echo '<span><i class="fa fa-clock-o">'.$Item->pubDate.'</i></span>';
	echo '<a target="_blank" href="'.$Item->link.'">'.$Item->title.'</>';
	echo '</li>';
}

?>

</ul>
