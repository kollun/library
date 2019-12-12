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
	$arParams['FIELDS_LIST'] = unserialize($arParams['FIELDS_LIST']);
	$arParams['PROPERTY_LIST'] = unserialize($arParams['PROPERTY_LIST']);
	
		
	$json['POST'] = $arParams;

	
	$json['RESULT']['ELEMENTS'] = array();
	
	$res = CIBlockElement::GetList(	array('NAME' => 'ASC'), 
									array('NAME' => $arParams['SEARCH_NAME'].'%', 'IBLOCK_ID' =>  $arParams['IBLOCK_ID']), 
									false, 
									array("nTopCount"=>$_REQUEST['COUNT_LIMIT']) 
									//array('ID','NAME', 'IBLOCK_SECTION_ID')
									);
	
	while($arElement = $res->GetNext())
	{	
			$arResult = array();
			$arResult['ID'] = $arElement['ID'];
			//$arResult['NAME'] = $arElement['NAME'];
			foreach($arParams['FIELDS_LIST'] as $field) $arResult["FIELDS_VALUE"][$field] = $arElement[$field];	
			$arResult["FIELDS_VALUE"]['NAME'] =  $arElement['~NAME'];
			$arResult["PROPERTY_VALUE"] = array();
			
			if(!empty($arParams['PROPERTY_LIST']))
			{
				$rsElementProperties = CIBlockElement::GetProperty( $arParams['IBLOCK_ID'], $arElement["ID"], $by="sort", $order="asc");
						
						while ($arElementProperty = $rsElementProperties->Fetch())
						{
								if(!in_array($arElementProperty["CODE"], $arParams['PROPERTY_LIST'])) continue;
								
								
								
								if(!array_key_exists($arElementProperty["CODE"], $arResult["PROPERTY_VALUE"]))
									$arResult["PROPERTY_VALUE"][$arElementProperty["CODE"]] = array();

								/*if(is_array($arElementProperty["VALUE"]))
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
								}*/
								
								$arResult["PROPERTY_VALUE"][$arElementProperty["CODE"]][] = $arElementProperty["VALUE"];
						}		
			}
		$json['RESULT']['ELEMENTS'][] = $arResult;	
	}
		
	
			
		
	
		
		
		
?>
	
	

<?
	echo json_encode($json);	
//	echo "<pre>";
//	print_r($json);
//	echo "<pre>";
	//echo CTasks::STATE_DECLINED;
	
	
require_once($_SERVER["DOCUMENT_ROOT"]."/modules/main/include/epilog_after.php");?>

