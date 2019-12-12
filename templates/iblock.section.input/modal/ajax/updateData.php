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
		$arFields = array(
			'NAME' => $arParams['SECTION']['NAME'],
			'CODE' => Cutil::translit($arParams['SECTION']['NAME'],"ru",array("replace_space"=>"-","replace_other"=>"-")),
			'IBLOCK_SECTION_ID' => $arParams['SECTION']['IBLOCK_SECTION_ID'],
			'IBLOCK_ID' => $arParams['IBLOCK_ID']
		);
		
		$json['$arFields'] = $arFields;
		$el = new CIBlockSection;

		if( $arParams['SECTION']['ID']>0) 
		{
			$res = $el->Update($arParams['SECTION']['ID'], $arFields);	
			$json['MESSAGE'] = "Раздел успешно изменен";
			//$json['RESULT'] = $arParams['ELEMENT'];
		}
		else
		{
			$res = $el->Add($arFields);	
			$arParams['SECTION']['ID'] = $res;
			$json['MESSAGE'] = "Раздел успешно создан";
		}	
		
		if($res)
		{
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
					if($arSection["ID"] == $arParams['SECTION']['ID'] ) $arResult['SECTION'] = $arItem;
				}
			$json['RESULT'] = $arResult;
		}
		else
		{
			
			$json['ERROR'] = true;
			$json['MESSAGE'] = $el->LAST_ERROR; 
		}
		//sleep(5);
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

