<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("IBLOCK_ELEMENT_ADD_FORM_NAME"),
	"DESCRIPTION" => GetMessage("IBLOCK_ELEMENT_ADD_FORM_DESCRIPTION"),
	"ICON" => "/images/eaddform.gif",
	"PATH" => array(
		"ID" => "mbn",
		"CHILD" => array(
			"ID" => "mbn_iblock_element_add",
			"NAME" => GetMessage("T_IBLOCK_DESC_ELEMENT_ADD"),
			"CHILD" => array(
				"ID" => "element_add_cmpx",
			),
		),
	),
);
?>