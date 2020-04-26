<?php
/**
*@package pXP
*@file gen-Liquidacion.php
*@author  (admin)
*@date 17-04-2020 01:54:37
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				17-04-2020				 (admin)				CREACION	

*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.Liquidacion=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.Liquidacion.superclass.constructor.call(this,config);
		this.init();
		this.load({params:{start:0, limit:this.tam_pag}})
	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_liquidacion'
			},
			type:'Field',
			form:true 
		},
        {
            config: {
                name: 'id_forma_pago',
                fieldLabel: 'Forma Pago',
                allowBlank: true,
                emptyText: 'Elija una opción...',
                store: new Ext.data.JsonStore({
                    url: '../../sis_obingresos/control/FormaPago/listarFormaPago',
                    id: 'id_forma_pago',
                    root: 'datos',
                    sortInfo: {
                        field: 'nombre',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_forma_pago', 'nombre', 'codigo', 'forma_pago'],
                    remoteSort: true,
                    baseParams: {par_filtro: 'fop.nombre#fop.codigo'}
                }),
                valueField: 'id_forma_pago',
                displayField: 'nombre',
                gdisplayField: 'desc_forma_pago',
                hiddenName: 'id_forma_pago',
                forceSelection: true,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender: true,
                mode: 'remote',
                pageSize: 15,
                queryDelay: 1000,
                anchor: '100%',
                gwidth: 150,
                minChars: 2,
                renderer : function(value, p, record) {
                    return String.format('{0}', record.data['desc_']);
                }
            },
            type: 'ComboBox',
            id_grupo: 0,
            filters: {pfiltro: 'movtip.nombre',type: 'string'},
            grid: true,
            form: true
        },
        {
            config: {
                name: 'id_tipo_doc_liquidacion',
                fieldLabel: 'Tipo doc Liqui',
                allowBlank: true,
                emptyText: 'Elija una opción...',
                store: new Ext.data.JsonStore({
                    url: '../../sis_devoluciones/control/TipoDocLiquidacion/listarTipoDocLiquidacion',
                    id: 'id_tipo_doc_liquidacion',
                    root: 'datos',
                    sortInfo: {
                        field: 'tipo_documento',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_tipo_doc_liquidacion', 'tipo_documento'],
                    remoteSort: true,
                    baseParams: {par_filtro: 'tdocliq.tipo_documento'}
                }),
                valueField: 'id_tipo_doc_liquidacion',
                displayField: 'tipo_documento',
                gdisplayField: 'desc_tipo_doc_liqui',
                hiddenName: 'id_tipo_doc_liquidacion',
                forceSelection: true,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender: true,
                mode: 'remote',
                pageSize: 15,
                queryDelay: 1000,
                anchor: '100%',
                gwidth: 150,
                minChars: 2,
                renderer : function(value, p, record) {
                    return String.format('{0}', record.data['desc_']);
                }
            },
            type: 'ComboBox',
            id_grupo: 0,
            filters: {pfiltro: 'movtip.nombre',type: 'string'},
            grid: true,
            form: true
        },

        {
            config: {
                name: 'id_tipo_liquidacion',
                fieldLabel: 'Tipo Liqui',
                allowBlank: true,
                emptyText: 'Elija una opción...',
                store: new Ext.data.JsonStore({
                    url: '../../sis_devoluciones/control/TipoLiquidacion/listarTipoLiquidacion',
                    id: 'id_tipo_liquidacion',
                    root: 'datos',
                    sortInfo: {
                        field: 'tipo_liquidacion',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_tipo_liquidacion', 'tipo_liquidacion'],
                    remoteSort: true,
                    baseParams: {par_filtro: 'tipoliqu.tipo_liquidacion'}
                }),
                valueField: 'id_tipo_liquidacion',
                displayField: 'tipo_liquidacion',
                gdisplayField: 'desc_',
                hiddenName: 'id_tipo_liquidacion',
                forceSelection: true,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender: true,
                mode: 'remote',
                pageSize: 15,
                queryDelay: 1000,
                anchor: '100%',
                gwidth: 150,
                minChars: 2,
                renderer : function(value, p, record) {
                    return String.format('{0}', record.data['desc_']);
                }
            },
            type: 'ComboBox',
            id_grupo: 0,
            filters: {pfiltro: 'movtip.nombre',type: 'string'},
            grid: true,
            form: true
        },


		{
			config:{
				name: 'estacion',
				fieldLabel: 'Estacion',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'liqui.estacion',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'nro_liquidacion',
				fieldLabel: 'Nro liquidacion',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'liqui.nro_liquidacion',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},

        {
            config:{
                name: 'tramo',
                fieldLabel: 'Tramo',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:255
            },
            type:'TextField',
            filters:{pfiltro:'liqui.tramo',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },

		{
			config:{
				name: 'estado_reg',
				fieldLabel: 'Estado Reg.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'liqui.estado_reg',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'tipo_de_cambio',
				fieldLabel: 'tipo de cambio',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:655362
			},
				type:'NumberField',
				filters:{pfiltro:'liqui.tipo_de_cambio',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'descripcion',
				fieldLabel: 'Descripcion',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'liqui.descripcion',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'nombre_cheque',
				fieldLabel: 'Nombre cheque',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'liqui.nombre_cheque',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'fecha_liqui',
				fieldLabel: 'Fecha Liqui',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'liqui.fecha_liqui',type:'date'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'tramo_devolucion',
				fieldLabel: 'Tramo devolucion',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'liqui.tramo_devolucion',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'util',
				fieldLabel: 'util',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'liqui.util',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'fecha_pago',
				fieldLabel: 'Fecha Pago',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'liqui.fecha_pago',type:'date'},
				id_grupo:1,
				grid:true,
				form:true
		},

		{
			config:{
				name: 'pv_agt',
				fieldLabel: 'pv agt',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'liqui.pv_agt',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'noiata',
				fieldLabel: 'noiata',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'liqui.noiata',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},



		{
			config:{
				name: 'nombre',
				fieldLabel: 'Nombre',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'liqui.nombre',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'moneda_liq',
				fieldLabel: 'moneda_liq',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:3
			},
				type:'TextField',
				filters:{pfiltro:'liqui.moneda_liq',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'estado',
				fieldLabel: 'estado',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'liqui.estado',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'obs_dba',
				fieldLabel: 'obs_dba',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:-5
			},
				type:'TextField',
				filters:{pfiltro:'liqui.obs_dba',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'cheque',
				fieldLabel: 'cheque',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'liqui.cheque',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'usr_reg',
				fieldLabel: 'Creado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'usu1.cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'fecha_reg',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'liqui.fecha_reg',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'usuario_ai',
				fieldLabel: 'Funcionaro AI',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:300
			},
				type:'TextField',
				filters:{pfiltro:'liqui.usuario_ai',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: 'Funcionaro AI',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'liqui.id_usuario_ai',type:'numeric'},
				id_grupo:1,
				grid:false,
				form:false
		},
		{
			config:{
				name: 'usr_mod',
				fieldLabel: 'Modificado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'usu2.cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'fecha_mod',
				fieldLabel: 'Fecha Modif.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'liqui.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,	
	title:'Liquidacion',
	ActSave:'../../sis_devoluciones/control/Liquidacion/insertarLiquidacion',
	ActDel:'../../sis_devoluciones/control/Liquidacion/eliminarLiquidacion',
	ActList:'../../sis_devoluciones/control/Liquidacion/listarLiquidacion',
	id_store:'id_liquidacion',
	fields: [
		{name:'id_liquidacion', type: 'numeric'},
		{name:'estacion', type: 'string'},
		{name:'nro_liquidacion', type: 'string'},
		{name:'estado_reg', type: 'string'},
		{name:'tipo_de_cambio', type: 'numeric'},
		{name:'descripcion', type: 'string'},
		{name:'nombre_cheque', type: 'string'},
		{name:'fecha_liqui', type: 'date',dateFormat:'Y-m-d'},
		{name:'tramo_devolucion', type: 'string'},
		{name:'util', type: 'string'},
		{name:'fecha_pago', type: 'date',dateFormat:'Y-m-d'},
		{name:'id_tipo_doc_liquidacion', type: 'numeric'},
		{name:'pv_agt', type: 'string'},
		{name:'noiata', type: 'string'},
		{name:'id_tipo_liquidacion', type: 'numeric'},
		{name:'id_forma_pago', type: 'numeric'},
		{name:'tramo', type: 'string'},
		{name:'nombre', type: 'string'},
		{name:'moneda_liq', type: 'string'},
		{name:'estado', type: 'string'},
		{name:'obs_dba', type: 'string'},
		{name:'cheque', type: 'string'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		
	],
	sortInfo:{
		field: 'id_liquidacion',
		direction: 'ASC'
	},
	bdel:true,
	bsave:true
	}
)
</script>
		
		