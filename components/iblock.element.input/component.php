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

if(!is_array($arParams["PROPERTY_CODES"]))
{
	$arParams["PROPERTY_CODES"] = array();
}
else
{
	foreach($arParams["PROPERTY_CODES"] as $i=>$k)
		if(strlen($k) <= 0)
			unset($arParams["PROPERTY_CODES"][$i]);
}

$rsIBlockSectionList = CIBlockSection::GetList(
		array("left_margin"=>"asc"),
		array(
			"ACTIVE"=>"Y",
			"IBLOCK_ID"=>$arParams["IBLOCK_ID"],
		),
		false,
		array("ID", "NAME", "DEPTH_LEVEL")
	);
	$arResult["SECTION_LIST"] = array();
	while ($arSection = $rsIBlockSectionList->GetNext())
	{
		$arSection["NAME"] = str_repeat(" . ", $arSection["DEPTH_LEVEL"]).$arSection["NAME"];
		$arResult["SECTION_LIST"][$arSection["ID"]] = array(
			"VALUE" => $arSection["NAME"]
		);
	}
	$arResult["PROPERTY_LIST"] = array();
	$arResult["FIELDS_LIST"] = array();
	
	$arResult["FIELDS_LIST_FULL"] = array(
		"NAME" => array(
			"PROPERTY_TYPE" => "S",
			"MULTIPLE" => "N",
			"COL_COUNT" => $COL_COUNT,
			"NAME" => 'Название'
		),

		"TAGS" => array(
			"PROPERTY_TYPE" => "S",
			"MULTIPLE" => "N",
			"COL_COUNT" => $COL_COUNT,
			"NAME" => 'Теги'
		),

		"DATE_ACTIVE_FROM" => array(
			"PROPERTY_TYPE" => "S",
			"MULTIPLE" => "N",
			"USER_TYPE" => "DateTime",
		),

		"DATE_ACTIVE_TO" => array(
			"PROPERTY_TYPE" => "S",
			"MULTIPLE" => "N",
			"USER_TYPE" => "DateTime",
		),

		"IBLOCK_SECTION_ID" => array(
			"PROPERTY_TYPE" => "L",
			"ROW_COUNT" => "12",
			"MULTIPLE" => 'N',//$arParams["MAX_LEVELS"] == 1 ? "N" : "Y",
			"ENUM" => $arResult["SECTION_LIST"],
			"NAME" => "Группа"
		),

		"PREVIEW_TEXT" => array(
			"PROPERTY_TYPE" => ($arParams["PREVIEW_TEXT_USE_HTML_EDITOR"]? "HTML": "T"),
			"MULTIPLE" => "N",
			"ROW_COUNT" => "12",
			"COL_COUNT" => $COL_COUNT,
		),
		"PREVIEW_PICTURE" => array(
			"PROPERTY_TYPE" => "F",
			"FILE_TYPE" => "jpg, gif, bmp, png, jpeg",
			"MULTIPLE" => "N",
		),
		"DETAIL_TEXT" => array(
			"PROPERTY_TYPE" => ($arParams["DETAIL_TEXT_USE_HTML_EDITOR"]? "HTML": "T"),
			"MULTIPLE" => "N",
			"ROW_COUNT" => "5",
			"COL_COUNT" => $COL_COUNT,
		),
		"DETAIL_PICTURE" => array(
			"PROPERTY_TYPE" => "F",
			"FILE_TYPE" => "jpg, gif, bmp, png, jpeg",
			"MULTIPLE" => "N",
		),
	);
	
	foreach ($arResult["FIELDS_LIST_FULL"] as $key => $arr)
	{
		if (in_array($key, $arParams["FIELDS_CODES"])) $arResult["FIELDS_LIST"][] = $key;
	}

	// get iblock property list
	$rsIBLockPropertyList = CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arParams["IBLOCK_ID"]));
	while ($arProperty = $rsIBLockPropertyList->GetNext())
	{
		// get list of property enum values
		if ($arProperty["PROPERTY_TYPE"] == "L")
		{
			$rsPropertyEnum = CIBlockProperty::GetPropertyEnum($arProperty["ID"]);
			$arProperty["ENUM"] = array();
			while ($arPropertyEnum = $rsPropertyEnum->GetNext())
			{
				$arProperty["ENUM"][$arPropertyEnum["ID"]] = $arPropertyEnum;
			}
		}

		if ($arProperty["PROPERTY_TYPE"] == "T")
		{
			if (empty($arProperty["COL_COUNT"])) $arProperty["COL_COUNT"] = "30";
			if (empty($arProperty["ROW_COUNT"])) $arProperty["ROW_COUNT"] = "5";
		}

		if(strlen($arProperty["USER_TYPE"]) > 0 )
		{
			$arUserType = CIBlockProperty::GetUserType($arProperty["USER_TYPE"]);
			if(array_key_exists("GetPublicEditHTML", $arUserType))
				$arProperty["GetPublicEditHTML"] = $arUserType["GetPublicEditHTML"];
			else
				$arProperty["GetPublicEditHTML"] = false;
		}
		else
		{
			$arProperty["GetPublicEditHTML"] = false;
		}

		// add property to edit-list
		if (in_array($arProperty["CODE"], $arParams["PROPERTY_CODES"]))
			$arResult["PROPERTY_LIST"][] = $arProperty["CODE"];

		$arResult["PROPERTY_LIST_FULL"][$arProperty["CODE"]] = $arProperty;
	}
	
	
	
	
	
	
	
	if ($arParams["ID"] > 0)
	{
	
		$res = CIBlockElement::GetByID($arParams["ID"]);
		if($arElement = $res->GetNext());
		{
			$rsElementProperties = CIBlockElement::GetProperty($arParams["IBLOCK_ID"], $arParams["ID"], $by="sort", $order="asc");
			$arResult["ELEMENT_PROPERTIES"] = array();
			while ($arElementProperty = $rsElementProperties->Fetch())
			{
					if(!array_key_exists($arElementProperty["CODE"], $arResult["ELEMENT_PROPERTIES"]))
						$arResult["ELEMENT_PROPERTIES"][$arElementProperty["CODE"]] = array();

					if(is_array($arElementProperty["VALUE"]))
					{
						$htmlvalue = array();
						foreach($arElementProperty["VALUE"] as $k => $v)
						{
							if(is_array($v))
							{
								$htmlvalue[$k] = array();
								foreach($v as $k1 => $v1)
									$htmlvalue[$k][$k1] = htmlspecialcharsbx($v1);
							}
							else
							{
								$htmlvalue[$k] = htmlspecialcharsbx($v);
							}
						}
					}
					else
					{
						$htmlvalue = htmlspecialcharsbx($arElementProperty["VALUE"]);
					}

					$arResult["ELEMENT_PROPERTIES"][$arElementProperty["CODE"]][] = array(
						"ID" => htmlspecialcharsbx($arElementProperty["ID"]),
						"VALUE" => $htmlvalue,
						"~VALUE" => $arElementProperty["VALUE"],
						"VALUE_ID" => htmlspecialcharsbx($arElementProperty["PROPERTY_VALUE_ID"]),
						"VALUE_ENUM" => htmlspecialcharsbx($arElementProperty["VALUE_ENUM"]),
					);
				}
			}
	}
	
	
	
	$arResult["PROPERTY_VALUE"] = array(); 
	$arResult["FIELDS_VALUE"] = array();
	$arResult['EMPTY_ELEMENT'] = array(
		'ID' => '',
		'PROPERTY_VALUE' => array(),
		'FIELDS_VALUE' => array()
	);

	
	foreach($arResult["FIELDS_LIST"] as $propertyID)
	{
		
		if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
		{
			$value = $arElement[$propertyID];
		}
		else 
		{
			$value = "";
		}
		
		$arResult['EMPTY_ELEMENT']["FIELDS_VALUE"][$propertyID] = '';
		$arResult["FIELDS_VALUE"][$propertyID] = $value;
	}	
	
	foreach($arResult["PROPERTY_LIST"] as $propertyID)
	{
			
		$inputNum = ($arParams["ID"] > 0) ? count($arResult["ELEMENT_PROPERTIES"][$propertyID]) : 1;
		
		$INPUT_TYPE = $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"];

		$arResult['EMPTY_ELEMENT']["PROPERTY_VALUE"][$propertyID][0] = '';
		for ($i = 0; $i<$inputNum; $i++){
			if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
			{
				$value = $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"];
			}
			elseif ($i == 0)
			{
				$value = $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];
			}
			else
			{
				$value = "";
			}
								
								
			$arPropVal = array(
				'INPUT_NAME'	=> 'property_value._'.$propertyID.'_'.$i,
				'VALUE' 		=> $value,
				'INPUT_TYPE'	=> $INPUT_TYPE,
			);	
			
			$arResult["PROPERTY_VALUE"][$propertyID][$i] = $value;
		}
	
	}
	$arResult["FIELDS_VALUE"]['NAME'] =  $arElement['~NAME'];
	$arResult['ELEMENT'] = array(
		'ID' => $arElement['ID'],
		'FIELDS_VALUE' => $arResult["FIELDS_VALUE"],
		'PROPERTY_VALUE' => $arResult["PROPERTY_VALUE"],
	);	

	

						

$this->IncludeComponentTemplate();

?>