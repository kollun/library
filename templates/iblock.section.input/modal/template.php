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
<div id="select-section-<?=$arParams['FORM_SELECT_ID']?>">
	<div class="input-group" >

		<select class="form-control" name="<?=$arParams['SELECT_NAME']?>"  v-model="section.ID" v-on:change="selectValue($event)">
			<option v-for="item in items" v-bind:value = "item.ID" v-on:click="selectValue(item)">{{ item.VALUE }}</option>
		</select>
		<div class="input-group-btn">
				<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				  Действия
				</button>
				<div class="dropdown-menu dropdown-menu-right">
				  <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" v-on:click = "showModal('NEW')">Добавить раздел</a>
				  <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" v-on:click = "showModal('EDIT')">Редактировать раздел</a>
				  <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" v-on:click = "deleteSection">Удалить раздел</a>
				</div>
		</div>
	</div>

	<div class = "section-edit-modal">
		<div class="modal fade" id="updateSectiontModal<?=$arParams['FORM_SELECT_ID']?>" tabindex="-1" role="dialog" aria-labelledby="updateSectiontModalLabel<?=$arParams['FORM_SELECT_ID']?>" aria-hidden="true">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<h5 class="modal-title" id="updateSectiontModalLabel<?=$arParams['FORM_SELECT_ID']?>">Добавление/редактирование</h5>
				<button type="button" class="close" data-dismiss="modal"  aria-label="Close">
				  <span aria-hidden="true">&times;</span>
				</button>
			  </div>
			  <div class="modal-body">
		 
				<div class="alert" v-bind:class="[alertClass]" role="alert" v-html = 'alertMessage' v-show = 'alertMessage'>
						
				</div>
				<div class="form-group">
						<label class="form-control-label" >
							Наименование<span class="starrequired">*</span>
						</label>
					
						<input type="text" class="form-control"  v-model="edit_section.NAME" />
					
				</div>
				
				<div class="form-group">
						<label class="form-control-label" >
							Родительский раздел
						</label>
					
						<select class="form-control" v-model="edit_section.IBLOCK_SECTION_ID" >
							<option v-for="item in items" v-bind:value = "item.ID">{{ item.VALUE }}</option>
						</select>
					
				</div>
				</div>
				<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal" v-bind:disabled="waitFlag" >Закрыть</button>
						<button type="button" class="btn btn-primary" v-on:click="updateElement" v-bind:disabled="waitFlag">Сохранить</button>
				</div>
				
			</div>
		</div>
	 </div> 
	</div>
</div>

<script>
	var selectSection<?=$arParams['FORM_SELECT_ID']?> = new Vue({
	  el: '#select-section-<?=$arParams['FORM_SELECT_ID']?>',
	  data: {
		items : <?=json_encode($arResult['SECTION_LIST'])?>,
		//section_id: '<?=$arParams['ID']?>',
		//section_name: '', 
		//parent_section_id:'',
		//SECTION_LIST
		edit_section: <?=json_encode($arResult['EMPTY_SECTION'])?>,
		section: <?=json_encode($arResult['SECTION'])?>, 
		modalAction:'EDIT',
		alertClass:'alert-success',
		alertMessage:'',
		waitFlag:false,
	  },
	  watch: {
		// эта функция запускается при любом изменении вопроса
		/*  element_name: function (newElementName) {
			  this.answer = 'Поиск "'+newElementName+'" ...'
			  this.getData()
		}*/
	  },
	  methods: {
		showModal: function(action){
			jQuery.noConflict();
			this.modalAction = action;
			this.alertMessage = '';
			if(action == 'EDIT')  
			{
				this.edit_section = _.cloneDeep(this.section);
			}
			else if (action == 'NEW') {
				this.edit_section = <?=json_encode($arResult['EMPTY_SECTION'])?>;

			}
				
			//console.log(this.items);
			var modal = $("#updateSectiontModal<?=$arParams['FORM_SELECT_ID']?>");
			modal.modal('show');
		},  
	  selectValue: function(e){
		 
		 console.log(e.target);
		 var el = $(e.target);
		 var id = el.val();
		 if(id != '')
		 {
			 var index = el.find('option[value="'+id+'"]').index();
			 console.log(index);
			 if(index >= 0 ) this.section = _.cloneDeep(this.items[index]);
		 }
		 //alert('.dhf');
		 //this.section = _.cloneDeep(item);
	  },
	  updateElement: function(){
			var vm = this; 
				var params = {
				'IBLOCK_ID'	: '<?=$arParams['IBLOCK_ID']?>',
				'SECTION' 	: vm.edit_section,
				'ACTION'	: vm.modalAction
			}
			console.log(params);
			vm.waitFlag = true;
			vm.alertClass = 'alert-warning';
			vm.alertMessage = 'Сохранение данных...';
					
			$.post('<?=$this->GetFolder()?>/ajax/updateData.php', params, function(data){
				console.log(data);
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
					vm.items = data.RESULT['SECTION_LIST'];
					vm.section = data.RESULT['SECTION'];
					vm.edit_section['ID'] = vm.section['ID'];
				//	alert(data.ERROR); 
					//vm.element = data.RESULT;	
					//vm.edit_element.ID = data.RESULT.ID;
					//vm.searchName = vm.element.FIELDS_VALUE.NAME;
				
				}
				vm.waitFlag = false;
				return;
			},'json')	
			.fail(function(data) {
				vm.alertClass = 'alert-danger';
				vm.alertMessage = 'Ошибка добавления/редактирования раздела.';
				vm.waitFlag = false;
					//vm.answer = 'Ошибка добавления/редактирования элемента.';
				console.log(data);
			});
			
	  },
	  deleteSection: function(){
			var vm = this;
			if(vm.section.ID > 0){
				
				if(confirm('Подверждение удаления раздела: '+vm.section.NAME))
				{
					var params = {
						'IBLOCK_ID'	: '<?=$arParams['IBLOCK_ID']?>',
						'ID' 		: vm.section.ID,
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
							vm.section = <?=json_encode($arResult['EMPTY_SECTION'])?>;	
						}
						return;
					},'json')	
					.fail(function(data) {
						alert('Ошибка удаления раздела.');
						
							//vm.answer = 'Ошибка добавления/редактирования элемента.';
						//console.log(data);
					});	
				}
			}
			else return;
		}
	  }
	});
  
	  
</script>

	