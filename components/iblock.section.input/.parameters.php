<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("search"))
	return;

$arComponentParameters = array(
	"PARAMETERS" => array(
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => "TAG",
		),
		"INPUT_VALUE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("INPUT_VALUE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"INPUT_NAME" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("INPUT_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		)
	),
);

//CSearchParameters::AddFilterParams($arComponentParameters, $arCurrentValues, "arrFILTER", "DATA_SOURCE", "N");
?>