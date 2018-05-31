<?php
/* Bitrix version */

//assume file is in server root directory
$_SERVER['DOCUMENT_ROOT'] = pathinfo(__FILE__, PATHINFO_DIRNAME);
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];
define('NO_AGENT_CHECK', TRUE);
define("STATISTIC_SKIP_ACTIVITY_CHECK", TRUE);
define("STOP_STATISTICS", TRUE);
define("NO_KEEP_STATISTIC", TRUE);
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");

$news_limit = 5;
$rss_result = CIBlockRSS::GetNewsEx('lenta.ru', 80, '/rss');
$list = CIBlockRSS::FormatArray($rss_result);
$list = array_chunk($list['item'], $news_limit);
foreach ($list[0] as $item) {
	//in case if Bitrix site is in windows-1251 encoding (assume linux console is utf-8 always)
	//echo iconv('cp1251', 'utf-8', "{$item['title']}\n{$item['link']}\n{$item['description']}\n\n");
	echo "{$item['title']}\n{$item['link']}\n{$item['description']}\n\n";
}

require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/epilog_after.php");


/* Non Bitrix version */

/*

$xml_string = file_get_contents('https://lenta.ru/rss');
$xml = new SimpleXMLElement($xml_string, LIBXML_NOCDATA);
$items = (array)$xml->channel;
$items = array_chunk($items['item'], 5);

foreach($items[0] as $item) {
	$decription = trim($item->description);
	echo "{$item->title}\n{$item->link}\n{$decription}\n\n";
}

*/