<?php
/**
*@package pXP
*@file gen-DescuentoLiquidacion.php
*@author  (admin)
*@date 17-04-2020 01:55:03
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				17-04-2020				 (admin)				CREACION	

*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.DescuentoLiquidacion=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.DescuentoLiquidacion.superclass.constructor.call(this,config);
		this.init();
		this.iniciarEventos();
		//this.load({params:{start:0, limit:this.tam_pag}})
	},
    iniciarEventos: function () {
        this.Cmp.id_concepto_ingas.on('select', function (rec, d) {
            console.log(d.json)
            this.Cmp.contabilizar.setValue(d.json.contabilizable);
            this.Cmp.tipo.setValue(d.json.tipo_descuento);
        }, this);
    },
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_descuento_liquidacion'
			},
			type:'Field',
			form:true 
		},
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
			config:{
				name: 'estado_reg',
				fieldLabel: 'Estado Reg.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'desliqui.estado_reg',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config: {
                name: 'id_concepto_ingas',
                msgTarget: 'title',
                fieldLabel: 'Concepto',
                allowBlank: false,
                emptyText: 'Elija una opción...',
                store: new Ext.data.JsonStore({
                    url: '../../sis_devoluciones/control/Liquidacion/listarConcepto',
                    id: 'id_concepto_ingas',
                    root: 'datos',
                    sortInfo: {
                        field: 'desc_ingas',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_concepto_ingas', 'desc_ingas','precio','contabilizable', 'tipo_descuento'],
                    remoteSort: true,
                    baseParams: {par_filtro: 'tci.desc_ingas',facturacion:'descu', emision:'DEVOLUCIONES'}
                }),
                valueField: 'id_concepto_ingas',
                displayField: 'desc_ingas',
                gdisplayField: 'desc_desc_ingas',
                hiddenName: 'id_concepto_ingas',
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
                    return String.format('{0}', record.data['desc_desc_ingas']);
                }
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'conig.desc_ingas',type: 'string'},
			grid: true,
			form: true
		},

        {
            config: {
                name: 'tipo',
                fieldLabel: 'tipo',
                allowBlank: true,
                emptyText: 'tipo...',
                typeAhead: true,
                triggerAction: 'all',
                lazyRender: true,
                mode: 'local',
                store: ['','FACTURABLE', 'NCD AGT', 'IMPUESTO', 'HAY NCD BOA', 'NO FACTURABLE'],
                width: 200
            },
            type: 'ComboBox',
            id_grupo: 4,
            form: true
        },
        {
            config: {
                name: 'contabilizar',
                fieldLabel: 'contabilizar',
                allowBlank: true,
                emptyText: 'contabilizar...',
                typeAhead: true,
                triggerAction: 'all',
                lazyRender: true,
                mode: 'local',
                store: ['si','no'],
                width: 200
            },
            type: 'ComboBox',
            filters:{pfiltro:'desliqui.contabilizar',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'importe',
                fieldLabel: 'importe',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:655362
            },
            type:'NumberField',
            filters:{pfiltro:'desliqui.importe',type:'numeric'},
            id_grupo:1,
            grid:true,
            form:true
        },

		{
			config:{
				name: 'sobre',
				fieldLabel: 'sobre',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'desliqui.sobre',type:'string'},
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
				filters:{pfiltro:'desliqui.fecha_reg',type:'date'},
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
				filters:{pfiltro:'desliqui.usuario_ai',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
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
				name: 'id_usuario_ai',
				fieldLabel: 'Creado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'desliqui.id_usuario_ai',type:'numeric'},
				id_grupo:1,
				grid:false,
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
				filters:{pfiltro:'desliqui.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
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
		}
	],
	tam_pag:50,	
	title:'Descuento Liquidacion',
	ActSave:'../../sis_devoluciones/control/DescuentoLiquidacion/insertarDescuentoLiquidacion',
	ActDel:'../../sis_devoluciones/control/DescuentoLiquidacion/eliminarDescuentoLiquidacion',
	ActList:'../../sis_devoluciones/control/DescuentoLiquidacion/listarDescuentoLiquidacion',
	id_store:'id_descuento_liquidacion',
	fields: [
		{name:'id_descuento_liquidacion', type: 'numeric'},
		{name:'contabilizar', type: 'string'},
		{name:'importe', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'id_concepto_ingas', type: 'numeric'},
		{name:'id_liquidacion', type: 'numeric'},
		{name:'sobre', type: 'string'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'desc_desc_ingas', type: 'string'},

	],
	sortInfo:{
		field: 'id_descuento_liquidacion',
		direction: 'ASC'
	},
	bdel:true,
	bsave:true,
    bedit: false,
    onReloadPage:function(m){
        this.maestro=m;
        this.store.baseParams={id_liquidacion:this.maestro.id_liquidacion};
        this.load({params: {start: 0, limit: 50}});
    },
    loadValoresIniciales: function () {
        this.Cmp.id_liquidacion.setValue(this.maestro.id_liquidacion);
        Phx.vista.DescuentoLiquidacion.superclass.loadValoresIniciales.call(this);
    }
	}
)
</script>
		
		