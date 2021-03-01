<?php
/**
*@package pXP
*@file gen-NotaAgencia.php
*@author  (admin)
*@date 26-04-2020 21:14:13
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.NotaAgencia=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.NotaAgencia.superclass.constructor.call(this,config);
		this.init();
        this.iniciarEventos();
		this.load({params:{start:0, limit:this.tam_pag, id_liquidacion:config.id_liquidacion}})
	},

	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_nota_agencia'
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
				filters:{pfiltro:'notage.estado_reg',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},

        {

            config:{
                name: 'id_doc_compra_venta',
                fieldLabel: 'Documento',
                allowBlank: false,
                emptyText:'Elija una plantilla...',
                store:new Ext.data.JsonStore(
                    {
                        url: '../../sis_contabilidad/control/DocCompraVenta/listarDocCompraVenta',
                        id: 'id_doc_compra_venta',
                        root:'datos',
                        sortInfo:{
                            field:'dcv.nro_documento',
                            direction:'asc'
                        },
                        totalProperty:'total',
                        fields: ['id_doc_compra_venta','revisado','nro_documento','nit',
                            'desc_plantilla', 'desc_moneda','importe_doc','nro_documento',
                            'tipo','razon_social','fecha'],
                        remoteSort: true,
                        baseParams:{par_filtro:'pla.desc_plantilla#dcv.razon_social#dcv.nro_documento#dcv.nit#dcv.importe_doc#dcv.codigo_control', filgestion: 'si'},
                    }),
                tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{razon_social}</b>,  NIT: {nit}</p><p>{desc_plantilla} </p><p ><span style="color: #F00000">Doc: {nro_documento}</span> de Fecha: {fecha}</p><p style="color: green;"> {importe_doc} {desc_moneda}  </p></div></tpl>',
                valueField: 'id_doc_compra_venta',
                hiddenValue: 'id_doc_compra_venta',
                displayField: 'desc_plantilla',
                gdisplayField:'nro_documento',
                listWidth:'401',
                forceSelection:true,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender:true,
                mode:'remote',
                pageSize:20,
                queryDelay:500,
                gwidth: 250,
                minChars:2,
                resizable: true,
                anchor: '100%'
            },
            type:'ComboBox',
            id_grupo: 0,
            grid: false,
            bottom_filter: true,
            form: true
        },

        {
            config:{
                name: 'nrofac',
                fieldLabel: 'Nro Fac',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:8
            },
            type:'TextField',
            filters:{pfiltro:'notage.nrofac',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'nroaut',
                fieldLabel: 'Nro Aut',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:8
            },
            type:'TextField',
            filters:{pfiltro:'notage.nroaut',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'fecha_fac',
                fieldLabel: 'Fecha Fac',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                format: 'd/m/Y',
                renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
            },
            type:'DateField',
            filters:{pfiltro:'notage.fecha_fac',type:'date'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'codito_control_fac',
                fieldLabel: 'Codito Control Fac',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:255
            },
            type:'TextField',
            filters:{pfiltro:'notage.codito_control_fac',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'monto_total_fac',
                fieldLabel: 'Monto Total Fac',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:655362
            },
            type:'NumberField',
            filters:{pfiltro:'notage.monto_total_fac',type:'numeric'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'iva',
                fieldLabel: 'Iva',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:10
            },
            type:'TextField',
            filters:{pfiltro:'notage.iva',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },

     /*   {
            config:{
                name: 'id_doc_compra_venta',
                fieldLabel: 'Documento',
                allowBlank: false,
                emptyText:'Elija una plantilla...',
                store:new Ext.data.JsonStore(
                    {
                        url: '../../sis_contabilidad/control/DocCompraVenta/listarDocCompraVenta',
                        id: 'id_doc_compra_venta',
                        root:'datos',
                        sortInfo:{
                            field:'dcv.nro_documento',
                            direction:'asc'
                        },
                        totalProperty:'total',
                        fields: ['id_doc_compra_venta','revisado','nro_documento','nit',
                            'desc_plantilla', 'desc_moneda','importe_doc','nro_documento',
                            'tipo','razon_social','fecha'],
                        remoteSort: true,
                        baseParams:{par_filtro:'pla.desc_plantilla#dcv.razon_social#dcv.nro_documento#dcv.nit#dcv.importe_doc#dcv.codigo_control'},
                    }),
                tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{razon_social}</b>,  NIT: {nit}</p><p>{desc_plantilla} </p><p ><span style="color: #F00000">Doc: {nro_documento}</span> de Fecha: {fecha}</p><p style="color: green;"> {importe_doc} {desc_moneda}  </p></div></tpl>',
                valueField: 'id_doc_compra_venta',
                hiddenValue: 'id_doc_compra_venta',
                displayField: 'desc_plantilla',
                gdisplayField:'nro_documento',
                listWidth:'401',
                forceSelection:true,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender:true,
                mode:'remote',
                pageSize:20,
                queryDelay:500,
                gwidth: 250,
                minChars:2,
                resizable: true,
                anchor: '56.5%'
            },
            type:'ComboBox',
            id_grupo: 0,
            grid: false,
            bottom_filter: true,
            form: true
        },
*/


        {
			config: {
				name: 'id_depto_conta',
				fieldLabel: 'Depto Conta',
				allowBlank: true,
				emptyText: 'Elija una opción...',
				 store: new Ext.data.JsonStore({
                    url: '../../sis_parametros/control/Depto/listarDeptoFiltradoDeptoUsuario',
                    id: 'id_depto',
					root: 'datos',
					sortInfo:{
						field: 'deppto.nombre',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_depto','nombre','codigo'],
					// turn on remote sorting
					remoteSort: true,
					baseParams: { par_filtro:'deppto.nombre#deppto.codigo', estado:'activo', codigo_subsistema: 'CONTA'}
                }),
				valueField: 'id_depto',
   				displayField: 'nombre',
   				hiddenName: 'id_depto',
                enableMultiSelect: true,
                triggerAction: 'all',
                lazyRender: true,
                mode: 'remote',
                pageSize: 20,
                queryDelay: 200,
                anchor: '80%',
                listWidth:'280',
                resizable:true,
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
                name: 'id_moneda',
                fieldLabel: 'Moneda',
                allowBlank: true,
                emptyText: 'Seleccione una Moneda...',
                store: new Ext.data.JsonStore({
                    url: '../../sis_parametros/control/Moneda/listarMoneda',
                    id: 'id_moneda',
                    root: 'datos',
                    sortInfo: {
                        field: 'codigo',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_moneda','codigo'],
                    remoteSort: true,
                    baseParams: {par_filtro:'codigo'}
                }),
                //hidden: true,
                valueField: 'id_moneda',
                displayField: 'codigo',
                gdisplayField: 'desc_moneda',
                forceSelection: false,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender: true,
                mode: 'remote',
                pageSize: 20,
                queryDelay: 500,
                anchor: '99%',
                gwidth: 70,
                minChars: 2,
                renderer: function (value, p, record) {
                    return String.format('{0}', value?record.data['desc_moneda']:'');
                }
            },
            type: 'ComboBox',
            filters: {
                pfiltro: 'mon.codigo',
                type: 'string'
            },
            id_grupo: 7,
            grid: true,
            form: true
        },

        {
            config:{
                name: 'tcambio',
                fieldLabel: 'Cambio',
                allowBlank: false,
                anchor: '80%',
                gwidth: 100,
                maxLength:1179654
            },
            type:'NumberField',
            filters:{pfiltro:'notage.tcambio',type:'numeric'},
            id_grupo:1,
            grid:true,
            form:true
        },

		{
			config:{
				name: 'estado',
				fieldLabel: 'Estado',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:50
			},
				type:'TextField',
				filters:{pfiltro:'notage.estado',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'nit',
				fieldLabel: 'Nit',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:50
			},
				type:'TextField',
				filters:{pfiltro:'notage.nit',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'nro_nota',
				fieldLabel: 'Nro Nota',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:50
			},
				type:'TextField',
				filters:{pfiltro:'notage.nro_nota',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'nro_aut_nota',
				fieldLabel: 'Nro Aut Nota',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:8
			},
				type:'TextField',
				filters:{pfiltro:'notage.nro_aut_nota',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'fecha',
				fieldLabel: 'Fecha',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'notage.fecha',type:'date'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'razon',
				fieldLabel: 'Razon',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:50
			},
				type:'TextField',
				filters:{pfiltro:'notage.razon',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},

		{
			config:{
				name: 'monto_total',
				fieldLabel: 'Monto Total',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179654
			},
				type:'NumberField',
				filters:{pfiltro:'notage.monto_total',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'excento',
				fieldLabel: 'Excento',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179654
			},
				type:'NumberField',
				filters:{pfiltro:'notage.excento',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'total_devuelto',
				fieldLabel: 'Total Devuelto',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179654
			},
				type:'NumberField',
				filters:{pfiltro:'notage.total_devuelto',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'credfis',
				fieldLabel: 'Credifis',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179654
			},
				type:'NumberField',
				filters:{pfiltro:'notage.credfis',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'billete',
				fieldLabel: 'Billete',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'notage.billete',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'codigo_control',
				fieldLabel: 'Codigo Control',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'notage.codigo_control',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},

		{
			config:{
				name: 'neto',
				fieldLabel: 'Neto',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:655362
			},
				type:'NumberField',
				filters:{pfiltro:'notage.neto',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'obs',
				fieldLabel: 'Obs',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'notage.obs',type:'string'},
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
				filters:{pfiltro:'notage.fecha_reg',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'notage.id_usuario_ai',type:'numeric'},
				id_grupo:1,
				grid:false,
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
				filters:{pfiltro:'notage.usuario_ai',type:'string'},
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
				filters:{pfiltro:'notage.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,
	title:'Nota Agencia',
	ActSave:'../../sis_devoluciones/control/NotaAgencia/insertarNotaAgencia',
	ActDel:'../../sis_devoluciones/control/NotaAgencia/eliminarNotaAgencia',
	ActList:'../../sis_devoluciones/control/NotaAgencia/listarNotaAgencia',
	id_store:'id_nota_agencia',
	fields: [
		{name:'id_nota_agencia', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'id_doc_compra_venta', type: 'numeric'},
		{name:'id_depto_conta', type: 'numeric'},
		{name:'id_moneda', type: 'numeric'},
		{name:'estado', type: 'string'},
		{name:'nit', type: 'string'},
		{name:'nro_nota', type: 'string'},
		{name:'nro_aut_nota', type: 'string'},
		{name:'fecha', type: 'date',dateFormat:'Y-m-d'},
		{name:'razon', type: 'string'},
		{name:'tcambio', type: 'numeric'},
		{name:'monto_total', type: 'numeric'},
		{name:'excento', type: 'numeric'},
		{name:'total_devuelto', type: 'numeric'},
		{name:'credfis', type: 'numeric'},
		{name:'billete', type: 'string'},
		{name:'codigo_control', type: 'string'},
		{name:'nrofac', type: 'string'},
		{name:'nroaut', type: 'string'},
		{name:'fecha_fac', type: 'date',dateFormat:'Y-m-d'},
		{name:'codito_control_fac', type: 'string'},
		{name:'monto_total_fac', type: 'numeric'},
		{name:'iva', type: 'string'},
		{name:'neto', type: 'numeric'},
		{name:'obs', type: 'string'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'desc_moneda', type: 'string'},

	],
	sortInfo:{
		field: 'id_nota_agencia',
		direction: 'ASC'
	},
	bdel:true,
	bsave:true,
    iniciarEventos : function () {
        this.Cmp.id_doc_compra_venta.on('select', function (rec, d) {

            console.log('llega')
            this.Cmp.nrofac.setValue(d.json.nro_documento);
            this.Cmp.nroaut.setValue(d.json.nro_autorizacion);
            this.Cmp.fecha_fac.setValue(d.json.fecha);
            this.Cmp.codito_control_fac.setValue();
            this.Cmp.monto_total_fac.setValue(d.json.importe_doc);

            console.log(d.json)

        }, this);
    },

    }
)
</script>
		
		