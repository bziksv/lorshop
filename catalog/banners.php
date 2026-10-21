<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	http_response_code(404);
	header('Content-Type: text/plain; charset=utf-8');
	header('X-Robots-Tag: noindex, nofollow');
	echo 'Not Found';
	die();
}
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:news.line",
	"banners",
	array(
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "300",
		"CACHE_TYPE" => "A",
		"DETAIL_URL" => "",
		"FIELD_CODE" => array(
			0 => "CODE",
			1 => "PREVIEW_PICTURE",
			2 => "",
		),
		"IBLOCKS" => array(
			0 => "6",
		),
		"IBLOCK_TYPE" => "sliders",
		"NEWS_COUNT" => "3",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_BY2" => "SORT",
		"SORT_ORDER1" => "DESC",
		"SORT_ORDER2" => "ASC",
		"COMPONENT_TEMPLATE" => "banners"
	),
	false
);?>
