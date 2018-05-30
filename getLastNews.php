<?php
$xml_string = file_get_contents('https://lenta.ru/rss');
$xml = new SimpleXMLElement($xml_string, LIBXML_NOCDATA);
$items = (array)$xml->channel;
$items = array_chunk($items['item'], 5);

foreach($items[0] as $item) {
	$decription = trim($item->description);
	echo "{$item->title}\n{$item->link}\n{$decription}\n\n";
}

