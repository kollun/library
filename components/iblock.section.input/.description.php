<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("FORM_NAME"),
	"DESCRIPTION" => GetMessage("FORM_DESC"),
	"ICON" => "/images/search_form.gif",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "mbn",
		"CHILD" => array(
			"ID" => "mbn_iblock_element_input",
			"NAME" => GetMessage("FORM_NAME")
		)
	),
);

?>