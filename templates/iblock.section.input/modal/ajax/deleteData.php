<?
header('Cache-Control: no-cache, must-revalidate');
header('Content-type: application/json; charset=UTF-8');
define("NO_KEEP_STATISTIC", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
?>
<?
	$json = array('ERROR' => false);
	
	$arParams =  $_REQUEST;
	
	$json['POST'] = $arParams;
	//$arParams['ID'] = -1;

?>
	
	

<?
	if(CIBlock::GetPermission($arParams['IBLOCK_ID'])>='W')
	{
		$DB->StartTransaction();
		if(!CIBlockSection::Delete($arParams['ID']))
		{
			$json['ERROR'] = true;
			$json['MESSAGE'] = 'Ошибка удаление раздела.';
			$DB->Rollback();
		}
		else
		$DB->Commit();
	}else{
		$json['ERROR'] = true;
		$json['MESSAGE'] = 'Ошибка доступа.';
	}
	

	echo json_encode($json);	
//	echo "<pre>";
//	print_r($json);
//	echo "<pre>";
	//echo CTasks::STATE_DECLINED;
	
	
require_once($_SERVER["DOCUMENT_ROOT"]."/modules/main/include/epilog_after.php");?>

