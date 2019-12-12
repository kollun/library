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
?>
		
	

<?
	if(CIBlock::GetPermission($arParams['IBLOCK_ID'])>='W')
	{
		$arFields = $arParams['ELEMENT']['FIELDS_VALUE'];
		$arFields['IBLOCK_ID'] = $arParams['IBLOCK_ID'];
		$arFields['PROPERTY_VALUES'] = $arParams['ELEMENT']['PROPERTY_VALUE'];
		$arFields['CODE'] = Cutil::translit($arFields['NAME'],"ru",array("replace_space"=>"-","replace_other"=>"-"));
		$el = new CIBlockElement;

		if( $arParams['ELEMENT']['ID']>0) 
		{
			$res = $el->Update($arParams['ELEMENT']['ID'], $arFields);	
			$json['MESSAGE'] = "Элемент успешно изменен";
			$json['RESULT'] = $arParams['ELEMENT'];
		}
		else
		{
			$res = $el->Add($arFields);	
			$json['MESSAGE'] = "Элемент успешно создан";
			$arParams['ELEMENT']['ID'] = $res;
			$json['RESULT'] = $arParams['ELEMENT'];
		}	
		
		if(!$res) {
			
			$json['ERROR'] = true;
			$json['MESSAGE'] = $el->LAST_ERROR; 
		}
	//	sleep(5);
	}
	else{
		$json['ERROR'] = true;
		$json['MESSAGE'] = 'Ошибка доступа.';
	}
	
	echo json_encode($json);	
//	echo "<pre>";
//	print_r($json);
//	echo "<pre>";
	//echo CTasks::STATE_DECLINED;
	
	
require_once($_SERVER["DOCUMENT_ROOT"]."/modules/main/include/epilog_after.php");?>

