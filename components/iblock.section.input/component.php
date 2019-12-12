<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentName */
/** @var string $componentPath */
/** @var string $componentTemplate */
/** @var string $parentComponentName */
/** @var string $parentComponentPath */
/** @var string $parentComponentTemplate */
/*
if(!CModule::IncludeModule("search"))
{
	ShowError(GetMessage("BSF_C_MODULE_NOT_INSTALLED"));
	return;
}

$exFILTER = CSearchParameters::ConvertParamsToFilter($arParams, "arrFILTER");
foreach($exFILTER as $i => $subFilter)
{
	if(
		is_array($subFilter)
		&& array_key_exists("PARAMS", $subFilter)
		&& is_array($subFilter["PARAMS"])
		&& array_key_exists("socnet_group", $subFilter["PARAMS"])
	)
		$exFILTER["SOCIAL_NETWORK_GROUP"] = $subFilter["PARAMS"]["socnet_group"];
}

$exFILTER["SITE_ID"] = (!empty($arParams["SITE_ID"]) ? $arParams["SITE_ID"] : SITE_ID);
$arResult["exFILTER"] = $exFILTER;

if (empty($arParams["NAME"]))
{
	$arParams["NAME"] = "TAGS";
	$arParams["~NAME"] = "TAGS";
}

$arResult["ID"] = preg_replace("/\\W/", "_", $arParams["NAME"]).$this->randString();
$arResult["NAME"] = $arParams["NAME"];
$arResult["~NAME"] = $arParams["~NAME"];
$arResult["VALUE"] = $arParams["VALUE"];
$arResult["~VALUE"] = $arParams["~VALUE"];
*/

if(!CModule::IncludeModule("iblock"))
{
	ShowError(GetMessage("CC_BIEAF_IBLOCK_MODULE_NOT_INSTALLED"));
	return;
}

$arResult["ERRORS"] = array();


$rsIBlockSectionList = CIBlockSection::GetList(
		array("left_margin"=>"asc"),
		array(
			"ACTIVE"=>"Y",
			"IBLOCK_ID"=>$arParams["IBLOCK_ID"],
		),
		false,
		array("ID", "NAME", "DEPTH_LEVEL", "IBLOCK_SECTION_ID")
	);
	
	
	$arResult["EMPTY_SECTION"] = array(
			"ID" => '',
			"VALUE" => 'Корневой раздел',
			"IBLOCK_SECTION_ID" => '',
			"NAME" => ''
	);
	
	$arResult["SECTION_LIST"][] = $arResult["EMPTY_SECTION"];
	
	$arResult["SECTION"] = $arResult["EMPTY_SECTION"];
		
	while ($arSection = $rsIBlockSectionList->GetNext())
	{
		$arItem = array(
			"ID" => $arSection["ID"],
			"VALUE" => str_repeat(" . ", $arSection["DEPTH_LEVEL"]).$arSection["NAME"],
			"IBLOCK_SECTION_ID" => $arSection["IBLOCK_SECTION_ID"],
			"NAME" => $arSection["NAME"]
		);
		
		$arResult["SECTION_LIST"][] = $arItem;
		if($arSection["ID"] == $arParams['ID']) $arResult['SECTION'] = $arItem;
	}

$this->IncludeComponentTemplate();

?>