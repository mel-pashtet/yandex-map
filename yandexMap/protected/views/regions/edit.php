<div  id="ext-content">
	<script type="text/javascript">
		Ext.onReady(function(){
			Ext.define('Price', {
				extend: 'Ext.data.Model',
				fields: ['id', 'cost']
			});

			Ext.define('PriceRegion', {
				extend: 'Ext.data.Model',
				fields: ['title', 'cost']
			});

			Ext.define('Region', {
				extend: 'Ext.data.Model',
				fields: ['id', 'title']
			});

			var priceUrl = '<?= Yii::app()->createUrl('regions/getPrice'); ?>'
			var priceStore = Ext.create('Ext.data.Store', {
				model: 'Price',
				proxy: {
					type: 'ajax',
					url : priceUrl
				},
				autoLoad: true
			   
			});

			var regionUrl = '<?= Yii::app()->createUrl('regions/getRegions'); ?>'
			var regionStore = Ext.create('Ext.data.Store', {
				model: 'Region',
				proxy: {
					type: 'ajax',
					url : regionUrl
				},
				autoLoad: true
			   
			});

			var priceRegionUrl = '<?= Yii::app()->createUrl('regions/getPriceRegion'); ?>'
			var priceRegionStore = Ext.create('Ext.data.Store', {
				model: 'PriceRegion',
				proxy: {
					type: 'ajax',
					url : priceRegionUrl
				},
				autoLoad: true
			   
			});

			var grid = Ext.create('Ext.grid.Panel', {
				title: 'Отредактируйте значения цены',
				store: priceStore,
			
				columns: [
					{ header: 'id',  dataIndex: 'id' },
					{ header: 'Цена доставки', dataIndex: 'cost', flex: 1, field: 'textfield' },
				],
				tbar: [{
				    text: 'Добавить новую цену',
				    handler : function() {
				        var r = Ext.create('Price', {
				            cost: 'New Price',
				            
				        });

				        priceStore.insert(0, r);
				        priceStore.commitChanges();
				        
				    }
				}],
				plugins: [
				        Ext.create('Ext.grid.plugin.RowEditing', {
				            clicksToEdit: 2,
				            clicksToMoveEditor: 1
				        })
				],

				height: 200,
				renderTo: Ext.get("ext-content"),
			});

			grid.on('edit', function(editor, e){
				
			    Ext.Ajax.request({
			        url: '<?= Yii::app()->createUrl('price/update'); ?>',
			        params: {
			            id: e.record.data.id,
			            cost: e.record.data.cost
			        },
			        success: function(response){
		        		var msgd = Ext.decode(response.responseText);
		        		Ext.Msg.alert('Сохранение', msgd.success);
			        }
			    });

			})
			
			var grid = Ext.create('Ext.form.Panel', {
				title: 'Сопоставление цен к регионам',
				id: 'my-form',
				items: [{
						xtype: 'combo',
						name: 'price_id',
						displayField: 'cost',
						valueField: 'id',
						fieldLabel: 'Цена',
						width: 400,
						emptyText: 'Выберите цену...',
						editable: false,
						allowBlank: false,
						store: priceStore
					},{
						xtype: 'combo',
						name: 'region_id',
						displayField: 'title',
						valueField: 'id',
						fieldLabel: 'Регион',
						width: 400,
						emptyText: 'Выберите регион...',
						editable: false,
						allowBlank: false,
						store: regionStore
					},

				],
				dockedItems: [{
						xtype: 'toolbar',
						dock: 'bottom',
						id: 'new-record',
						items: [{
							xtype: 'button',
							text: 'Загрузить данные',
							formBind: true,
							handler: function() {
								form = Ext.getCmp('my-form').getForm();
								
								if (form.isValid()) {
									var updatePriceRegionUrl = '<?= Yii::app()->createUrl('autoCount/updatePriceRegion'); ?>'
									form.submit({
									    url: updatePriceRegionUrl,
									    success: function(form, action) {
									    	var msgd = Ext.decode((action.response.responseText));
									    	location.reload();
									        Ext.Msg.alert('Сохранение', msgd.success);
									    },
									    
									});
								}
							}
						}]
					}],
			
				height: 200,
				renderTo: Ext.get("ext-content"),
			});

			var gridPriceRegion = Ext.create('Ext.grid.Panel', {
				title: 'Сводная таблица Регион - Цена',
				store: priceRegionStore,
			
				columns: [
					{ header: 'Название региона',  dataIndex: 'title' },
					{ header: 'Цена доставки', dataIndex: 'cost', flex: 1, field: 'textfield' },
				],
				
				height: 200,
				renderTo: Ext.get("ext-content"),
			});


		});
	</script>
</div>