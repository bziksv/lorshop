<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$page = $APPLICATION->GetCurPage();
$code = explode('/', $page);

if(empty($code[3])){
	$dbRes = CIBlockSection::GetList(array(), ["IBLOCK_ID" => IBLOCK_CATALOG, "CODE" => $code[2]], false, array("ID", "UF_*"));
	if ($arCurSection = $dbRes->Fetch())
		$arResult['PROPERTIES'] = $arCurSection;
}else{

	$arSelect = Array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM","PROPERTY_*");
	$arFilter = Array("IBLOCK_ID" => IBLOCK_CATALOG, "CODE" => $code[3]);
	$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
	if($ob = $res->GetNextElement()){ 
	 $arFields = $ob->GetFields();  
	 $arProps = $ob->GetProperties();
	 $arResult['PROPERTIES']['UF_DELETE_INDEX'] = $arProps['DELETE_INDEX']['VALUE'];
	}
}
//determine if child selected

$bWasSelected = false;
$arParents = array();
$depth = 1;
foreach($arResult as $i=>$arMenu)
{
	$depth = $arMenu['DEPTH_LEVEL'];

	if($arMenu['IS_PARENT'] == true)
	{
		$arParents[$arMenu['DEPTH_LEVEL']-1] = $i;
	}
	elseif($arMenu['SELECTED'] == true)
	{
		$bWasSelected = true;
		break;
	}
}

if($bWasSelected)
{
	for($i=0; $i<$depth-1; $i++)
		$arResult[$arParents[$i]]['CHILD_SELECTED'] = true;
}
?>
