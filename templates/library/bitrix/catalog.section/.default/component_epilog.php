<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */
use Bitrix\Main\Loader;
global $APPLICATION;

global $INTRANET_TOOLBAR; 
$INTRANET_TOOLBAR->AddButton(array( 
		'HREF' => '/library/edit.php?SECTION_ID='.$arResult["ID"] , 
		"TEXT" => 'Добавить элемент', 
		"ICON" => 'add', 
		"SORT" => 1000, 
	)); 
	$INTRANET_TOOLBAR->AddButton(array( 
		'HREF' => '/bitrix/admin/iblock_list_admin.php?IBLOCK_ID='.$arParams['IBLOCK_ID'].'&type='.$arParams['IBLOCK_TYPE'].'&lang=ru&find_section_section='.$arResult['ID'], 
		"TEXT" => 'Элементы', 
		"ICON" => '', 
		"SORT" => 1000, 
	)); 	
	
 $INTRANET_TOOLBAR = array();
 
if (isset($templateData['TEMPLATE_THEME']))
{
	$APPLICATION->SetAdditionalCSS($templateData['TEMPLATE_THEME']);
}
if (isset($templateData['TEMPLATE_LIBRARY']) && !empty($templateData['TEMPLATE_LIBRARY']))
{
	$loadCurrency = false;
	if (!empty($templateData['CURRENCIES']))
		$loadCurrency = Loader::includeModule('currency');
	CJSCore::Init($templateData['TEMPLATE_LIBRARY']);
	if ($loadCurrency)
	{
	?>
	<script type="text/javascript">
		BX.Currency.setCurrencies(<? echo $templateData['CURRENCIES']; ?>);
	</script>
<?
	}
}
?>