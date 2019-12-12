<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
//CJSCore::Init(array("ajax"));
?><!--
<script>
	BX.ready(function(){
		var input = BX("<?echo $arResult["ID"]?>");
		if (input)
			new JsTc(input, '<?echo $arParams["ADDITIONAL_VALUES"]?>');
	});
</script>-->

<?
//pr($arParams);
//pr($arResult);
//	pr($arResult);	

$this->addExternalJS("/bitrix/js/main/lodash/lodash.min.js");
$this->addExternalJS("/bitrix/js/main/vue/vue.min.js");
$this->addExternalJS("/bitrix/js/main/jquery/jquery-2.1.3.min.js");


?>


<div id="input-element-<?=$arParams['FORM_INPUT_ID']?>" v-on:keyup.27 = "outInput" >

 
 
	<input type="hidden" name = "<?=$arParams['INPUT_NAME']?>"  v-model="element.ID">
	<div class="input-group">

	<input type="text" class="form-control" v-model="searchName" v-on:input = "getData" v-on:blur = "outInput" v-on:dblclick = "getData">
		  <div class="input-group-btn">
			<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			  Действия
			</button>
			<div class="dropdown-menu dropdown-menu-right">
			  <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" v-on:click = "showModal('NEW')" >Добавить элемент</a>
			  <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" v-on:click = "showModal('EDIT')" >Редактировать элемент</a>
			  <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" v-on:click = "deleteElement">Удалить элемент</a>
			 </div>
		  </div>
	</div>
	<div class = 'input-find-list-wrap' v-show = 'showList' >
		<div class = 'input-find-list list-group'>
			   <a v-for="item in items" href="javascript:void(0)" class="list-group-item list-group-item-action" v-bind:data-id = "item.ID" v-on:click.stop="selectValue(item, $event)" > {{ item.FIELDS_VALUE.NAME }} </a>
		</div>
	</div>
	<small class="form-text text-muted">{{ answer }}</small>
	
	<br/>

	<!--<div class="modal fade " id="infoModal<?=$arParams['FORM_INPUT_ID']?>" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title">Подверждение</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
		  {{infoMessage}}
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
			<button type="button" class="btn btn-primary">Подтвердить</button>
      </div>
	  
    </div>
  </div>
</div>
-->
<div class = "element-edit-modal">
	<div class="modal fade" id="updateElementModal<?=$arParams['FORM_INPUT_ID']?>" tabindex="-1" role="dialog" aria-labelledby="updateElementModalLabel<?=$arParams['FORM_INPUT_ID']?>" aria-hidden="true">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="updateElementModalLabel<?=$arParams['FORM_INPUT_ID']?>">Добавление/редактирование</h5>
			<button type="button" class="close" data-dismiss="modal" v-on:click="cancelEdit" v-bind:disabled="waitFlag"  aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
	 
				<div class="alert" v-bind:class="[alertClass]" role="alert" v-html = 'alertMessage' v-show = 'alertMessage'>
					
				</div>
	
			  <div name="modal_iblock_add" >
				<?//=bitrix_sessid_post()?>
				<input type = 'hidden' v-model = 'modalAction' >
				
				
				<?foreach ($arResult["FIELDS_LIST"] as $propertyID):?>
				<div class="form-group">
					<label class="form-control-label" >
						<?=$arResult["FIELDS_LIST_FULL"][$propertyID]["NAME"]?>
						<?if(in_array($propertyID, $arResult["PROPERTY_REQUIRED"])):?><span class="starrequired">*</span><?endif?>
					</label>
				
					<?
						$INPUT_TYPE = $arResult["FIELDS_LIST_FULL"][$propertyID]["PROPERTY_TYPE"];
						switch ($INPUT_TYPE):
							case "S":
							case "N":
								?>
								<input type="text" class="form-control"  v-model="edit_element.FIELDS_VALUE.<?=$propertyID?>" />
								<?
							break;

							case "L":
								?>
								<select class="form-control" v-model="edit_element.FIELDS_VALUE.<?=$propertyID?>">
										<option value=""><?echo GetMessage("CT_BIEAF_PROPERTY_VALUE_NA")?></option>
										<?
										foreach ($arResult["FIELDS_LIST_FULL"][$propertyID]["ENUM"] as $key => $arEnum)
										{
											$checked = $arResult["FIELDS_VALUE"][$propertyID] == $key;?>
											<option value="<?=$key?>"><?=$arEnum["VALUE"]?></option>
										<?}?>
								</select>
					<?
							break;
						endswitch;?>
					</div>	
							
							
					<?endforeach;?>	
				
				
				
				<?foreach ($arResult["PROPERTY_LIST"] as $propertyID):?>
				<div class="form-group">
					
					<label for="property<?=$propertyID?>" class="form-control-label" >
						<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"]?>
						<?if(in_array($propertyID, $arResult["PROPERTY_REQUIRED"])):?><span class="starrequired">*</span><?endif?>
					</label>
				
				<?
					
					$inputNum = ($arParams["ID"] > 0) ? count($arResult["ELEMENT_PROPERTIES"][$propertyID]) : 1;
					$INPUT_TYPE = $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"];
					switch ($INPUT_TYPE):
							case "S":
							case "N":
							for ($i = 0; $i<$inputNum; $i++)
							{
							?>
								<input type="text" class="form-control"  data-name="PROPERTY_VALUE.<?=$propertyID?>[<?=$i?>]"  v-model="edit_element.PROPERTY_VALUE.<?=$propertyID?>[<?=$i?>]"  value="<?=$arResult["PROPERTY_VALUE"][$propertyID][$i]?>" />
							<?
							}
							break;

							/*case "E":
								
								$arFieldsCodes = array("NAME");
								$arPropertyCodes = array();
							//	if(in_array($propertyID,array(142,151))) $arFieldsCodes = array("NAME"); // издательство, серия
							//	if($propertyID == 151) $arPropertyCodes = array("TYPE", "PUBLICATION", "START_YAER", "END_YAER" ); 
								//pr($arResult["ELEMENT_PROPERTIES"][$propertyID]);
								for($i = 0; $i < $inputNum; $i++)
								//foreach($arResult["ELEMENT_PROPERTIES"][$propertyID] as $i => $el_val)
								{		
									$APPLICATION->IncludeComponent(
										"custom:iblock.element.input",
										"modal",
										array(
											'IBLOCK_ID' => $arResult['PROPERTY_LIST_FULL'][$propertyID]['LINK_IBLOCK_ID'],
											//'FORM_NAME' => 'iblock_add',
											'INPUT_NAME' => "PROPERTY[".$propertyID."][".$i."]",
											'ID' => $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"],
											'FORM_INPUT_ID' => md5($propertyID.$i.$arResult['PROPERTY_LIST_FULL'][$propertyID]['LINK_IBLOCK_ID']),
											'FIELDS_CODES' => $arFieldsCodes,
											'PROPERTY_CODES' => $arPropertyCodes,
											//'ELEMENT_NAME' => $arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"]
										), 
										null, 
										array("HIDE_ICONS"=>"Y")
									);
								}
								
								
							break;*/
							
							case "L":
								if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["LIST_TYPE"] == "C")
									$type = $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" ? "checkbox" : "radio";
								else
								$type = $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" ? "multiselect" : "dropdown";
								switch ($type):
												case "checkbox":
												case "radio":
													foreach ($arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"] as $key => $arEnum)
													{
													
														$checked = false;
														if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
														{
															$checked = $arResult["PROPERTY_VALUE"][$propertyID][$i] == $key;
															
														}
														else
														{
															if ($arEnum["DEF"] == "Y") $checked = true;
														}

														?>
														<input type="<?=$type?>" class="form-control" data-model="edit_element.PROPERTY_VALUE.<?=$propertyID?>[<?=$i?>]" <?=$type == "checkbox" ? "[".$key."]" : ""?>" value="<?=$key?>" id="property_<?=$key?>"<?=$checked ? " checked=\"checked\"" : ""?> /><label for="property_<?=$key?>"><?=$arEnum["VALUE"]?></label><br />
														<?
													}
												break;
									
									case "dropdown":
									case "multiselect":
									?>
									<select class="form-control" v-model="edit_element.PROPERTY_VALUE.<?=$propertyID?>[0]<?//=$type=="multiselect" ? "[]\" size=\"".$arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"]."\" multiple=\"multiple" : ""?>">
										<option value=""><?echo GetMessage("CT_BIEAF_PROPERTY_VALUE_NA")?></option>
											<?
														foreach ($arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"] as $key => $arEnum)
														{
											?>
														<option value="<?=$key?>"><?=$arEnum["VALUE"]?></option>
														<?
														}
													?>
												</select>
												<?
												break;

											endswitch;
										break;
									endswitch;?>
								</div>	
							
							
					<?endforeach;?>				
				</div>
				
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal" v-on:click="cancelEdit" v-bind:disabled="waitFlag" >Закрыть</button>
					<button type="button" class="btn btn-primary" v-on:click="updateElement" v-bind:disabled="waitFlag">Сохранить</button>
				</div>
			  </div>
		  </div>
		 	  
		 </div> 

	
	
</div>

</div>
<script>

var inputElement<?=$arParams['FORM_INPUT_ID']?> = new Vue({
  el: '#input-element-<?=$arParams['FORM_INPUT_ID']?>',
  data: {
	searchName: '<?=$arResult['ELEMENT']['FIELDS_VALUE']['NAME']?>',
	answer: 'Начните печатать название элемента',
	items : [],
	edit_element: <?=json_encode($arResult['EMPTY_ELEMENT'])?>,
	element: <?=json_encode($arResult['ELEMENT'])?>, 
	modalAction:'EDIT',
	showList:false,
	alertClass:'alert-success',
	alertMessage:'',
	waitFlag:false,
	//infoMessage:''
  },
  watch: {
    // эта функция запускается при любом изменении вопроса
	/*  element_name: function (newElementName) {
		  this.answer = 'Поиск "'+newElementName+'" ...'
		  this.getData()
	}*/
  },
  methods: {
    // _.debounce — это функция из lodash, позволяющая ограничить
    // то, насколько часто может выполняться определённая операция.
    // В данном случае, мы хотим ограничить частоту обращений к yesno.wtf/api,
    // дожидаясь завершения печати вопроса перед тем как послать ajax-запрос.
    // Чтобы узнать больше о функции _.debounce (и её родственнице _.throttle),
    // см. документацию: https://lodash.com/docs#debounce
    getData: _.debounce( function () {
        var vm = this;
		//vm.element.ID = '';
		var params = {
			'IBLOCK_ID': '<?=$arParams['IBLOCK_ID']?>',
			'SEARCH_NAME' : vm.searchName,
			'COUNT_LIMIT' : 10,
			'FIELDS_LIST' : '<?=serialize($arResult['FIELDS_LIST'])?>',
			'PROPERTY_LIST' : '<?=serialize($arResult['PROPERTY_LIST'])?>',
		}
		//console.log(params);
		$.post('<?=$this->GetFolder()?>/ajax/getData.php', params, function(data){
			//console.log(data);
			vm.answer = '';
			if(data.ERROR) {alert(data.MESSAGE); return;}
			vm.items = [];
			if(data.RESULT.ELEMENTS.length > 0){ 
				data.RESULT.ELEMENTS.forEach(function(el){
					//console.log(el.NAME);	
					vm.items.push(el);
				})
				vm.showList = true;
			}else vm.answer = 'Значение "'+vm.searchName+'" не найдено'; 
		}, "json")
		.fail(function(data) {
			vm.answer = 'Ошибка поиска.';
			//console.log(data);
		});
      }, 100),
	selectValue: function(item, event){
		 this.element = _.cloneDeep(item);
		 this.searchName = item.FIELDS_VALUE.NAME;
		 this.showList = false;
		//return;
	  },
	showModal: function(action, new_name){
		//console.log(new_name);
		jQuery.noConflict();
		this.modalAction = action;
		this.alertMessage = '';
		if(action == 'EDIT')  
		{
			this.edit_element = _.cloneDeep(this.element);
		}
		else if (action == 'NEW') {
			this.edit_element = <?=json_encode($arResult['EMPTY_ELEMENT'])?>;

			if(!!new_name) {
				this.edit_element.FIELDS_VALUE.NAME = new_name;
			}
		}
			
		//console.log(this.items);
		var modal = $("#updateElementModal<?=$arParams['FORM_INPUT_ID']?>");
		modal.modal('show');
	  },
	  
	outInput: function(event){
		
		var vm = this;
		 setTimeout(function () {
			vm.showList = false;
			if(vm.searchName == ''){
				vm.element = <?=json_encode($arResult['EMPTY_ELEMENT'])?>;	
			}
			else if(vm.element.FIELDS_VALUE.NAME != vm.searchName) {
				var find_val = vm.items.some(function(el){
					if(vm.searchName == el.FIELDS_VALUE.NAME)
					{
					//	console.log(el);
						vm.element = _.cloneDeep(el);
						vm.searchName = el.FIELDS_VALUE.NAME;
						return true;
					}
					else return false;	
					
				});
				if(!find_val){
					vm.showModal('NEW', vm.searchName);
					vm.alertClass = 'alert-warning';
					vm.alertMessage = 'Элемент "'+vm.searchName+'" не найден, нажмите сохранить, чтобы создать элемент';
				}
			}
		}, 250);
	  },
	cancelEdit: function(){
		 this.searchName = this.element.FIELDS_VALUE.NAME; 
	  },
	updateElement: function(){
			 var vm = this; 
				var params = {
				'IBLOCK_ID'	: '<?=$arParams['IBLOCK_ID']?>',
				'ELEMENT' 	: vm.edit_element,
				'ACTION'	: vm.modalAction
			}
			//console.log(params);
			vm.waitFlag = true;
			vm.alertClass = 'alert-warning';
			vm.alertMessage = 'Сохранение данных...';
					
			$.post('<?=$this->GetFolder()?>/ajax/updateData.php', params, function(data){
				//console.log(data);
				//vm.answer = ' ';
				if(data.ERROR) 
				{
					vm.alertClass = 'alert-danger';
					vm.alertMessage = data.MESSAGE;
				}
				else
				{
					vm.alertClass = 'alert-success';
					vm.alertMessage = data.MESSAGE;
				//	alert(data.ERROR); 
					vm.element = data.RESULT;	
					vm.edit_element.ID = data.RESULT.ID;
					vm.searchName = vm.element.FIELDS_VALUE.NAME;
				
				}
				vm.waitFlag = false;
				return;
			},'json')	
			.fail(function(data) {
				vm.alertClass = 'alert-danger';
				vm.alertMessage = 'Ошибка добавления/редактирования элемента.';
				vm.waitFlag = false;
					//vm.answer = 'Ошибка добавления/редактирования элемента.';
				//console.log(data);
			});
			
	  },
	deleteElement: function(){
			var vm = this;
			if(vm.element.ID > 0){
				
				if(confirm('Подтверждение удаления элемента: '+vm.element.FIELDS_VALUE.NAME))
				{
					var params = {
						'IBLOCK_ID'	: '<?=$arParams['IBLOCK_ID']?>',
						'ID' 		: vm.element.ID,
						'ACTION'	: vm.modalAction
					}
					$.post('<?=$this->GetFolder()?>/ajax/deleteData.php', params, function(data){
						//console.log(data);
						//vm.answer = ' ';
						if(data.ERROR) 
						{
							alert(data.MESSAGE);
						}
						else
						{
							vm.element = <?=json_encode($arResult['EMPTY_ELEMENT'])?>;	
							vm.searchName = '';
						
						}
						return;
					},'json')	
					.fail(function(data) {
						alert('Ошибка удаления элемента.');
						
							//vm.answer = 'Ошибка добавления/редактирования элемента.';
						//console.log(data);
					});	
				}
			}
			else return;
	  }
	 
  }
})
</script>
	