<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	// Include-only file — direct URL must not look like a page for crawlers/validators.
	http_response_code(404);
	header('Content-Type: text/plain; charset=utf-8');
	header('X-Robots-Tag: noindex, nofollow');
	echo 'Not Found';
	die();
}
?>

<div class="sidebar">

    <?$APPLICATION->IncludeComponent("bitrix:menu", "catalog.menu", Array(
        "ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
        "CHILD_MENU_TYPE" => "left",	// Тип меню для остальных уровней
        "DELAY" => "N",	// Откладывать выполнение шаблона меню
        "MAX_LEVEL" => "4",	// Уровень вложенности меню
        "MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
        "MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
        "MENU_CACHE_TYPE" => "N",	// Тип кеширования
        "MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
        "ROOT_MENU_TYPE" => "catalog",	// Тип меню для первого уровня
        "USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
        "COMPONENT_TEMPLATE" => "tree",
        "MENU_THEME" => "site"
    ),
        false
    );?>

	<div class="sidebar-banners">
		<? require $_SERVER['DOCUMENT_ROOT'].'/catalog/banners.php'; ?>
	</div>

</div>