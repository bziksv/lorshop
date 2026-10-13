<?php
//IBlock catalog id
define("IBLOCK_CATALOG","33");

if (
	(!empty($_REQUEST['success']) && is_string($_REQUEST['success']))
	|| (!empty($_POST['submit']) && $_SERVER['REQUEST_METHOD'] === 'POST')
) {
	if (!defined('BX_COMPOSITE_DISABLED')) {
		define('BX_COMPOSITE_DISABLED', true);
	}
}

AddEventHandler("main", "OnBeforeEndBufferContent", "OnBeforeEndBufferContent", 100500);
function OnBeforeEndBufferContent()
{
            $arPanelButtons = &$GLOBALS['APPLICATION']->arPanelButtons;
            foreach ($arPanelButtons as &$arItemPanel) {
                if ($arItemPanel['ICON'] == 'bx-panel-site-template-icon') {

                    if (isset($arItemPanel['MENU']) && is_array($arItemPanel['MENU'])) {

                        $arItemPanel['MENU'][] = array(
                            'TEXT' => "Цветовые схемы",
                            'MENU' => array(
                                array('ACTION' => "jsUtils.Redirect([], '/?THEME=schemes_1')", 'TEXT' => "schemes_1"),
                                array('ACTION' => "jsUtils.Redirect([], '/?THEME=schemes_2')", 'TEXT' => "schemes_2"),
                                array('ACTION' => "jsUtils.Redirect([], '/?THEME=schemes_3')", 'TEXT' => "schemes_3"),
                                array('ACTION' => "jsUtils.Redirect([], '/?THEME=schemes_4')", 'TEXT' => "schemes_4"),
                                array('ACTION' => "jsUtils.Redirect([], '/?THEME=schemes_5')", 'TEXT' => "schemes_5"),
                                array('ACTION' => "jsUtils.Redirect([], '/?THEME=schemes_6')", 'TEXT' => "schemes_6"),
                                array('ACTION' => "jsUtils.Redirect([], '/?THEME=schemes_7')", 'TEXT' => "schemes_7"),
                                array('ACTION' => "jsUtils.Redirect([], '/?THEME=schemes_8')", 'TEXT' => "schemes_8"),
                                array('ACTION' => "jsUtils.Redirect([], '/?THEME=schemes_9')", 'TEXT' => "schemes_9"),
                                array('ACTION' => "jsUtils.Redirect([], '/?THEME=schemes_10')", 'TEXT' => "schemes_10"),
                            ),
                        );
                    }
                }
            }
}
if($_REQUEST['THEME']){
    COption::SetOptionString("main","color_theme",$_REQUEST['THEME']);
}





function priceDiscount($id){
    global $USER;
    $ar_res_price = CCatalogProduct::GetOptimalPrice($id, 1, $USER->GetUserGroupArray(), 'N');
	$ar_res_price['DISCOUNT_PRICE'] = ($ar_res_price['DISCOUNT_PRICE']) ? CurrencyFormat($ar_res_price['DISCOUNT_PRICE'], $ar_res_price["RESULT_PRICE"]["CURRENCY"]) : "Цена по запросу";
	
	return $ar_res_price;
}

/**
 * Fix common WYSIWYG HTML issues for Nu HTML / SEO validators:
 * Cyrillic #ссс hex, missing img alt, empty src, block tags inside headings/span.
 */
function lorshopSanitizeHtml($html, $imgAlt = '')
{
	if ($html === null || $html === '') {
		return $html;
	}

	// Drop H2 that duplicates page H1 (section/product NAME passed as $imgAlt)
	if ($imgAlt !== '' && $imgAlt !== null) {
		$titleNorm = preg_replace('/\s+/u', ' ', mb_strtolower(trim(html_entity_decode(str_replace("\xC2\xA0", ' ', strip_tags((string)$imgAlt)), ENT_QUOTES, 'UTF-8')), 'UTF-8'));
		if ($titleNorm !== '') {
			$html = preg_replace_callback('/<h2\b[^>]*>(.*?)<\/h2>/is', function ($m) use ($titleNorm) {
				$t = preg_replace('/\s+/u', ' ', mb_strtolower(trim(html_entity_decode(str_replace("\xC2\xA0", ' ', strip_tags($m[1])), ENT_QUOTES, 'UTF-8')), 'UTF-8'));
				if ($t === null || $t === '') {
					return $m[0];
				}
				return ($t === $titleNorm) ? '' : $m[0];
			}, $html);
		}
	}

	// Drop empty headings (e.g. <h2><br></h2>) — they block level promotion and confuse SEO tools
	$html = preg_replace_callback('/<(h[1-6])\b[^>]*>(.*?)<\/\1>/is', function ($m) {
		$inner = $m[2];
		$text = preg_replace('/\s+/u', '', html_entity_decode(str_replace("\xC2\xA0", ' ', strip_tags($inner)), ENT_QUOTES, 'UTF-8'));
		if ($text === '' && !preg_match('/<(?:img|picture|svg|video|iframe)\b/i', $inner)) {
			return '';
		}
		return $m[0];
	}, $html);

	// Fill skipped heading levels in the fragment (H1→H3, H2→H4, …).
	// Promote higher levels as a cascade so H4 does not stay after H3→H2.
	for ($need = 2; $need <= 5; $need++) {
		if (preg_match('/<h'.$need.'\b/i', $html)) {
			continue;
		}
		$hasHigher = false;
		for ($l = $need + 1; $l <= 6; $l++) {
			if (preg_match('/<h'.$l.'\b/i', $html)) {
				$hasHigher = true;
				break;
			}
		}
		if (!$hasHigher) {
			continue;
		}
		for ($l = $need + 1; $l <= 6; $l++) {
			$html = preg_replace('/<h'.$l.'\b/i', '<h__'.$l.'__', $html);
			$html = preg_replace('/<\/h'.$l.'>/i', '</h__'.$l.'__>', $html);
		}
		for ($l = $need + 1; $l <= 6; $l++) {
			$new = $l - 1;
			$html = str_replace(
				['<h__'.$l.'__', '</h__'.$l.'__>'],
				['<h'.$new, '</h'.$new.'>'],
				$html
			);
		}
	}

	// Cyrillic "с" typed instead of Latin "c" in #ccc / #cccccc
	$html = preg_replace('/#[сС]{6}\b/u', '#cccccc', $html);
	$html = preg_replace('/#[сС]{3}\b/u', '#ccc', $html);
	$html = preg_replace('#<style\b[^>]*>.*?</style>#is', '', $html);

	// Invalid CSS: background-color: none
	$html = preg_replace('/background-color\s*:\s*none\b/i', 'background-color: transparent', $html);

	// Mistyped empty end-tag </> (usually was </span>)
	$html = str_replace('</>', '</span>', $html);

	// Unescaped "<" before a digit in text (e.g. "шум <35 дБ")
	$html = preg_replace('/<(?=\d)/', '&lt;', $html);

	// style="...;class=" loading= → style="...;" loading=  (CSS "class" parse error)
	$html = preg_replace('/(style="[^"]*?);\s*class="\s+(?=[a-z_]+=)/i', '$1;" ', $html);
	$html = preg_replace('/(style="[^"]*?);\s*class="/i', '$1;" class="', $html);
	$html = preg_replace('/\sclass="\s+(?=[a-z_]+=)/i', ' ', $html);

	// <figure margin-left: 25px;> → <figure style="margin-left: 25px;">
	$html = preg_replace_callback('/<figure\b([^>]*)>/i', function ($m) {
		$attrs = trim($m[1]);
		if ($attrs === '') {
			return '<figure>';
		}
		// Whole attribute list is raw CSS (no name="value")
		if (!preg_match('/\b[a-z0-9_-]+\s*=/i', $attrs) && preg_match('/[a-z-]+\s*:/i', $attrs)) {
			$css = rtrim($attrs, " \t;");
			return '<figure style="'.htmlspecialchars($css, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'">';
		}
		return '<figure'.$m[1].'>';
	}, $html);

	// Orphan </figure> without any opening <figure>
	if (!preg_match('/<figure\b/i', $html) && preg_match('/<\/figure>/i', $html)) {
		$html = preg_replace('/<\/figure>/i', '', $html);
	}

	$html = preg_replace_callback('/<img\b([^>]*)>/i', function ($m) use ($imgAlt) {
		$attrs = $m[1];
		if (preg_match('/\ssrc\s*=\s*(["\'])\s*\1/i', $attrs)) {
			return '';
		}
		// Escaped markup leaked into style="...&lt;/figure&gt;..."
		if (preg_match('/\sstyle\s*=\s*"([^"]*)"/i', $attrs, $sm) && stripos($sm[1], '&lt;') !== false) {
			$css = preg_replace('/&lt;.*$/i', '', $sm[1]);
			$css = rtrim($css, " \t;");
			$attrs = preg_replace('/\sstyle\s*=\s*"[^"]*"/i', ' style="'.$css.'"', $attrs);
		}
		// CSS properties wrongly emitted as attributes, including "color:=""" form
		$attrs = preg_replace('/\s[a-z-]+:\s*=\s*"[^"]*"/i', '', $attrs);
		$attrs = preg_replace(
			'/\s(?:color|display|flex-direction|gap|margin(?:-(?:top|right|bottom|left))?|padding|float|border-radius|box-shadow|background(?:-color)?|font-size|line-height|max-width)\s*=\s*"[^"]*"/i',
			'',
			$attrs
		);
		$attrs = preg_replace('/\sclass\s*=\s*""/', '', $attrs);
		if (!preg_match('/\salt\s*=/i', $attrs)) {
			$alt = htmlspecialchars((string)$imgAlt, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
			$attrs .= ' alt="'.$alt.'"';
		}
		return '<img'.$attrs.'>';
	}, $html);

	// Empty lists / empty strong (often left by WYSIWYG)
	$html = preg_replace('/<ul\b[^>]*>\s*<\/ul>/i', '', $html);
	$html = preg_replace('/<ol\b[^>]*>\s*<\/ol>/i', '', $html);
	$html = preg_replace('/<strong\b[^>]*>\s*<\/strong>/i', '', $html);

	// <strong><p>…</p></strong> → <p>…</p>
	$html = preg_replace('/<strong\b[^>]*>\s*(<(?:p|div|ul|ol|h[1-6])\b[\s\S]*?<\/(?:p|div|ul|ol|h[1-6])>)\s*<\/strong>/i', '$1', $html);

	// strong/b → span with font-weight (SEO: too many <strong>/<b>)
	$html = preg_replace_callback('/<(?:strong|b)\b([^>]*)>(.*?)<\/(?:strong|b)>/is', function ($m) {
		$attrs = $m[1];
		$inner = $m[2];
		if (preg_match('/\sstyle\s*=\s*"([^"]*)"/i', $attrs, $sm)) {
			$style = rtrim($sm[1], " \t;");
			if (!preg_match('/font-weight\s*:/i', $style)) {
				$style .= '; font-weight:700';
			}
			$attrs = preg_replace('/\sstyle\s*=\s*"[^"]*"/i', ' style="'.$style.'"', $attrs);
		} else {
			$attrs .= ' style="font-weight:700"';
		}
		return '<span'.$attrs.'>'.$inner.'</span>';
	}, $html);

	// <h2><p>…</p><div></div></h2> → <h2>…</h2>
	$html = preg_replace_callback('/<(h[1-6])(\b[^>]*)>(.*?)<\/\1>/is', function ($m) {
		$inner = $m[3];
		$inner = preg_replace('#</?p\b[^>]*>#i', '', $inner);
		$inner = preg_replace('#<div\b[^>]*>\s*</div>#i', '', $inner);
		$inner = preg_replace('#</?div\b[^>]*>#i', '', $inner);
		return '<'.$m[1].$m[2].'>'.$inner.'</'.$m[1].'>';
	}, $html);

	// Drop excess closing </div> (stray end tag)
	$openDiv = preg_match_all('/<div\b/i', $html);
	$closeDiv = preg_match_all('/<\/div>/i', $html);
	while ($closeDiv > $openDiv) {
		$pos = strripos($html, '</div>');
		if ($pos === false) {
			break;
		}
		$html = substr($html, 0, $pos).substr($html, $pos + 6);
		$closeDiv--;
	}

	// Unwrap Bitrix/WYSIWYG font-family <span> wrappers (often illegally wrap ul/h2/h3).
	// Innermost-first via non-greedy match.
	for ($i = 0; $i < 24; $i++) {
		$next = preg_replace(
			'/<span\b[^>]*\bstyle\s*=\s*"[^"]*font-family:[^"]*"[^>]*>(.*?)<\/span>/is',
			'$1',
			$html
		);
		if ($next === null || $next === $html) {
			break;
		}
		$html = $next;
	}

	// Any remaining span that directly wraps a block element
	for ($i = 0; $i < 8; $i++) {
		$next = preg_replace(
			'/<span\b[^>]*>\s*(<(?:ul|ol|h[1-6]|div|table)\b[\s\S]*?<\/(?:ul|ol|h[1-6]|div|table)>)\s*<\/span>/i',
			'$1',
			$html
		);
		if ($next === null || $next === $html) {
			break;
		}
		$html = $next;
	}

	return $html;
}

function EditData ($DATA){
    $MES = array(
        "01" => "Января",
        "02" => "Февраля",
        "03" => "Марта",
        "04" => "Апреля",
        "05" => "Мая",
        "06" => "Июня",
        "07" => "Июля",
        "08" => "Августа",
        "09" => "Сентября",
        "10" => "Октября",
        "11" => "Ноября",
        "12" => "Декабря"
    );

    $arData = explode(".", $DATA);
    $d = ($arData[0] < 10) ? substr($arData[0], 1) : $arData[0];
    $newData = $d." ".$MES[$arData[1]]." ".$arData[2];
    return $newData;
}


AddEventHandler("sale", "OnOrderNewSendEmail", "bxModifySaleMails");

function bxModifySaleMails($orderID, &$eventName, &$arFields)
{
  $arOrder = CSaleOrder::GetByID($orderID);
  $order_props = CSaleOrderPropsValue::GetOrderProps($orderID);

  $phone="";
  $delivery="";
  while ($arProps = $order_props->Fetch())
  {
    if ($arProps["CODE"] == "PHONE")
    {
       $phone = htmlspecialchars($arProps["VALUE"]);
    }
    if ($arProps["CODE"] == "ADDRESS")
    {
        $delivery = htmlspecialchars($arProps["VALUE"]);
    }
  }

  if(CModule::IncludeModule("sale") && CModule::IncludeModule("iblock"))
    {
        $strOrderList = "";
        $dbBasketItems = CSaleBasket::GetList(
            array("NAME" => "ASC"),
            array("ORDER_ID" => $orderID),
            false,
            false,
            array("PRODUCT_ID", "ID", "NAME", "QUANTITY", "PRICE", "CURRENCY")
        );
        while ($arProps = $dbBasketItems->Fetch())
        {
            $db_res_props = CSaleBasket::GetPropsList(array(),array("BASKET_ID" => $arProps['ID'],"CODE" => "CML2_ARTICLE"));
            if ($ar_res_props = $db_res_props->Fetch())
            {
                $arProps['ARTICLE'] = trim($ar_res_props['VALUE']);
            }else
                unset($arProps['ARTICLE']);

            $strOrderList .= "<tr><td style='text-align: left;padding: 5px 0;'>".$arProps['NAME']." (".$arProps['ARTICLE'].")</td><td style='padding: 5px 10px;'>".$arProps['QUANTITY']."</td><td style='padding: 5px 0;'>".CurrencyFormat($arProps['PRICE'], $arProps['CURRENCY'])."</td><tr>";
        }
    $arFields["ORDER_LIST_TABLE"] = $strOrderList;
  }

  $arFields["PHONE"] =  $phone;
  $arFields["DELIVERY"] =  $delivery;
  $arFields["USER_DESCRIPTION"] =  $arOrder['USER_DESCRIPTION'];
  if($_COOKIE['roistat_visit'])
    $arFields["ROI_VISIT"] = $_COOKIE['roistat_visit'];
}

AddEventHandler("sale", "OnOrderStatusSendEmail", "bxModifySaleStatusSendEmail");
function bxModifySaleStatusSendEmail($orderID, &$eventName, &$arFields, $status){

    $arOrder = CSaleOrder::GetByID($orderID);
    $order_props = CSaleOrderPropsValue::GetOrderProps($orderID);

    $phone="";
    $delivery="";
    while ($arProps = $order_props->Fetch())
    {
        if ($arProps["CODE"] == "PHONE")
        {
            $phone = htmlspecialchars($arProps["VALUE"]);
        }
        if ($arProps["CODE"] == "ADDRESS")
        {
            $delivery = htmlspecialchars($arProps["VALUE"]);
        }
    }

    if(CModule::IncludeModule("sale") && CModule::IncludeModule("iblock"))
    {
        $strOrderList = "";
        $dbBasketItems = CSaleBasket::GetList(
            array("NAME" => "ASC"),
            array("ORDER_ID" => $orderID),
            false,
            false,
            array("PRODUCT_ID", "ID", "NAME", "QUANTITY", "PRICE", "CURRENCY")
        );
        $sum = 0;
        while ($arProps = $dbBasketItems->Fetch())
        {
            $db_res_props = CSaleBasket::GetPropsList(array(),array("BASKET_ID" => $arProps['ID'],"CODE" => "CML2_ARTICLE"));
            if ($ar_res_props = $db_res_props->Fetch())
            {
                $arProps['ARTICLE'] = trim($ar_res_props['VALUE']);
            }else
                unset($arProps['ARTICLE']);

            $sum += $arProps['PRICE']*$arProps['QUANTITY'];
            $strOrderList .= "<tr><td style='text-align: left;padding: 5px 0;'>".$arProps['NAME']." (".$arProps['ARTICLE'].")</td><td style='padding: 5px 10px;'>".$arProps['QUANTITY']."</td><td style='padding: 5px 0;'>".CurrencyFormat($arProps['PRICE'], $arProps['CURRENCY'])."</td><tr>";
        }
        $arFields["ORDER_LIST_TABLE"] = $strOrderList;
        $arFields["PRICE"] = CurrencyFormat($sum,"RUB");
    }
    $arFields["PHONE"] =  $phone;
    $arFields["ORDER_USER"] =  $arOrder['USER_NAME'].' '.$arOrder['USER_LAST_NAME'];
    $arFields["DELIVERY"] =  $delivery;
}

AddEventHandler("sale", "OnOrderPaySendEmail", "bxModifySaleStatusPaySendEmail");
function bxModifySaleStatusPaySendEmail($orderID, &$eventName, &$arFields){

    $arOrder = CSaleOrder::GetByID($orderID);
    $order_props = CSaleOrderPropsValue::GetOrderProps($orderID);

    $phone="";
    $delivery="";
    while ($arProps = $order_props->Fetch())
    {
        if ($arProps["CODE"] == "PHONE")
        {
            $phone = htmlspecialchars($arProps["VALUE"]);
        }
        if ($arProps["CODE"] == "ADDRESS")
        {
            $delivery = htmlspecialchars($arProps["VALUE"]);
        }
    }

    if(CModule::IncludeModule("sale") && CModule::IncludeModule("iblock"))
    {
        $strOrderList = "";
        $dbBasketItems = CSaleBasket::GetList(
            array("NAME" => "ASC"),
            array("ORDER_ID" => $orderID),
            false,
            false,
            array("PRODUCT_ID", "ID", "NAME", "QUANTITY", "PRICE", "CURRENCY")
        );
        $sum = 0;
        while ($arProps = $dbBasketItems->Fetch())
        {
            $db_res_props = CSaleBasket::GetPropsList(array(),array("BASKET_ID" => $arProps['ID'],"CODE" => "CML2_ARTICLE"));
            if ($ar_res_props = $db_res_props->Fetch())
            {
                $arProps['ARTICLE'] = trim($ar_res_props['VALUE']);
            }else
                unset($arProps['ARTICLE']);

            $sum += $arProps['PRICE']*$arProps['QUANTITY'];
            $strOrderList .= "<tr><td style='text-align: left;padding: 5px 0;'>".$arProps['NAME']." (".$arProps['ARTICLE'].")</td><td style='padding: 5px 10px;'>".$arProps['QUANTITY']."</td><td style='padding: 5px 0;'>".CurrencyFormat($arProps['PRICE'], $arProps['CURRENCY'])."</td><tr>";
        }
        $arFields["ORDER_LIST_TABLE"] = $strOrderList;
        $arFields["PRICE"] = CurrencyFormat($sum,"RUB");
    }
    $arFields["PHONE"] =  $phone;
    $arFields["ORDER_USER"] =  $arOrder['USER_NAME'].' '.$arOrder['USER_LAST_NAME'];
    $arFields["DELIVERY"] =  $delivery;
}

AddEventHandler('main', 'OnBeforeEventAdd', array('MyClassTrack', 'OnTrack'));

class MyClassTrack
{
    static function OnTrack(&$event, &$lid, &$arFields, &$message_id) {
        if ($event == 'SALE_ORDER_TRACKING_NUMBER') {
            $orderID = $arFields['ORDER_REAL_ID'];
            $arOrder = CSaleOrder::GetByID($orderID);
            $order_props = CSaleOrderPropsValue::GetOrderProps($orderID);

            $phone="";
            $delivery="";
            while ($arProps = $order_props->Fetch())
            {
                if ($arProps["CODE"] == "PHONE")
                {
                    $phone = htmlspecialchars($arProps["VALUE"]);
                }
                if ($arProps["CODE"] == "ADDRESS")
                {
                    $delivery = htmlspecialchars($arProps["VALUE"]);
                }
            }

            if(CModule::IncludeModule("sale") && CModule::IncludeModule("iblock"))
            {
                $strOrderList = "";
                $dbBasketItems = CSaleBasket::GetList(
                    array("NAME" => "ASC"),
                    array("ORDER_ID" => $orderID),
                    false,
                    false,
                    array("PRODUCT_ID", "ID", "NAME", "QUANTITY", "PRICE", "CURRENCY")
                );
                $sum = 0;
                while ($arProps = $dbBasketItems->Fetch())
                {
                    $db_res_props = CSaleBasket::GetPropsList(array(),array("BASKET_ID" => $arProps['ID'],"CODE" => "CML2_ARTICLE"));
                    if ($ar_res_props = $db_res_props->Fetch())
                    {
                        $arProps['ARTICLE'] = trim($ar_res_props['VALUE']);
                    }else
                        unset($arProps['ARTICLE']);

                    $sum += $arProps['PRICE']*$arProps['QUANTITY'];
                    $strOrderList .= "<tr><td style='text-align: left;padding: 5px 0;'>".$arProps['NAME']." (".$arProps['ARTICLE'].")</td><td style='padding: 5px 10px;'>".$arProps['QUANTITY']."</td><td style='padding: 5px 0;'>".CurrencyFormat($arProps['PRICE'], $arProps['CURRENCY'])."</td><tr>";
                }
                $arFields["ORDER_LIST_TABLE"] = $strOrderList;
                $arFields["PRICE"] = CurrencyFormat($sum,"RUB");
            }
            $arFields["PHONE"] =  $phone;
            $arFields["ORDER_USER"] =  $arOrder['USER_NAME'].' '.$arOrder['USER_LAST_NAME'];
            $arFields["DELIVERY"] =  $delivery;
        }
    }
}

// регистрируем обработчик
AddEventHandler("search", "BeforeIndex", "BeforeIndexHandler");
// создаем обработчик события "BeforeIndex"
function BeforeIndexHandler($arFields)
{

    if(!CModule::IncludeModule("iblock")) // подключаем модуль
        return $arFields;
    if($arFields["MODULE_ID"] == "iblock")
    {
        $VALUES = [];
        $db_props = CIBlockElement::GetProperty(                        // Запросим свойства индексируемого элемента
            $arFields["PARAM2"],         // BLOCK_ID индексируемого свойства
            $arFields["ITEM_ID"],          // ID индексируемого свойства
            array("sort" => "asc"),       // Сортировка (можно упустить)
            Array("CODE" => "ARTICLS")); // CODE свойства (в данном случае артикул)
        while ($ar_props = $db_props->Fetch())
            $VALUES[] = $ar_props['VALUE'];

        if(is_array($VALUES) && count($VALUES) > 0)
        $arFields["TITLE"] .= " Артикул: ".implode(', ',$VALUES);   // Добавим свойство в конец заголовка индексируемого элемента
    }

    return $arFields; // вернём изменения
}
