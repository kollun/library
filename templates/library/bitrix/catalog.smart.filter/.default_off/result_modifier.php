<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (isset($arParams["TEMPLATE_THEME"]) && !empty($arParams["TEMPLATE_THEME"]))
{
	$arAvailableThemes = array();
	$dir = trim(preg_replace("'[\\\\/]+'", "/", dirname(__FILE__)."/themes/"));
	if (is_dir($dir) && $directory = opendir($dir))
	{
		while (($file = readdir($directory)) !== false)
		{
			if ($file != "." && $file != ".." && is_dir($dir.$file))
				$arAvailableThemes[] = $file;
		}
		closedir($directory);
	}

	if ($arParams["TEMPLATE_THEME"] == "site")
	{
		$solution = COption::GetOptionString("main", "wizard_solution", "", SITE_ID);
		if ($solution == "eshop")
		{
			$templateId = COption::GetOptionString("main", "wizard_template_id", "eshop_bootstrap", SITE_ID);
			$templateId = (preg_match("/^eshop_adapt/", $templateId)) ? "eshop_adapt" : $templateId;
			$theme = COption::GetOptionString("main", "wizard_".$templateId."_theme_id", "blue", SITE_ID);
			$arParams["TEMPLATE_THEME"] = (in_array($theme, $arAvailableThemes)) ? $theme : "blue";
		}
	}
	else
	{
		$arParams["TEMPLATE_THEME"] = (in_array($arParams["TEMPLATE_THEME"], $arAvailableThemes)) ? $arParams["TEMPLATE_THEME"] : "blue";
	}
}
else
{
	$arParams["TEMPLATE_THEME"] = "blue";
}

$arParams["FILTER_VIEW_MODE"] = (isset($arParams["FILTER_VIEW_MODE"]) && toUpper($arParams["FILTER_VIEW_MODE"]) == "HORIZONTAL") ? "HORIZONTAL" : "VERTICAL";
$arParams["POPUP_POSITION"] = (isset($arParams["POPUP_POSITION"]) && in_array($arParams["POPUP_POSITION"], array("left", "right"))) ? $arParams["POPUP_POSITION"] : "left";

function activateSections($ID, &$arResult){
	
	$arResult['SECTIONS'][$ID]['ACTIVE'] = 'Y';
	$ID_PARENTS = $arResult['SECTIONS'][$ID]['IBLOCK_SECTION_ID'];
	
	if(!empty($arResult['SECTIONS'][$ID_PARENTS])) activateSections($ID_PARENTS, $arResult); 
} 

$res = CIBlockSection::GetList(array("left_margin"=>"asc"), array('IBLOCK_ID' => $arParams['IBLOCK_ID'],/* '<DEPTH_LEVEL' => 2,*/ 'ACTIVE' => 'Y'));
while ($arSect = $res->GetNext())
{
	$arResult['SECTIONS'][$arSect['ID']] = array( 'ID' => $arSect['ID'],
									'NAME' => $arSect['NAME'],
									'SECTION_PAGE_URL' => $arSect['SECTION_PAGE_URL'],
									'DEPTH_LEVEL' => $arSect['DEPTH_LEVEL'],
									'IBLOCK_SECTION_ID'=> $arSect['IBLOCK_SECTION_ID']
								);
/*		$arResult['SECTIONS'][$arSect['ID']]['ACTIVE'] = 'Y';
		if($arSect['DEPTH_LEVEL'] > 1) $arResult['SECTIONS'][$arSect['IBLOCK_SECTION_ID']]['ACTIVE'] = 'Y';
	}*/
	
	if(!empty($arResult['SECTIONS'][$arSect['IBLOCK_SECTION_ID']]))	$arResult['SECTIONS'][$arSect['IBLOCK_SECTION_ID']]['SUBSECTIONS'] = 'Y';
}
	activateSections($arResult['SECTION']['ID'], $arResult);

