<?php
/**
*@package pXP
*@file gen-NotaSiat.php
*@author  (favio.figueroa)
*@date 15-02-2022 18:29:02
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.NotaSiat=Ext.extend(Phx.gridInterfaz,{
    id_liquidacion_:'',
	constructor:function(config){
		this.maestro=config.maestro;

        var id_liquidacion = config.id_liquidacion;
        this.id_liquidacion_ = id_liquidacion;

    	//llama al constructor de la clase padre
		Phx.vista.NotaSiat.superclass.constructor.call(this,config);
		this.init();
		this.store.baseParams.id_liquidacion = config.id_liquidacion;
		this.load({params:{start:0, limit:this.tam_pag}})
	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_nota_siat'
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
				filters:{pfiltro:'tns.estado_reg',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config: {
				name: 'id_liquidacion',
				fieldLabel: 'id_liquidacion',
				allowBlank: true,
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({
					url: '../../sis_/control/Clase/Metodo',
					id: 'id_',
					root: 'datos',
					sortInfo: {
						field: 'nombre',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_', 'nombre', 'codigo'],
					remoteSort: true,
					baseParams: {par_filtro: 'movtip.nombre#movtip.codigo'}
				}),
				valueField: 'id_',
				displayField: 'nombre',
				gdisplayField: 'desc_liquidacion',
				hiddenName: 'id_liquidacion',
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
					return String.format('{0}', record.data['desc_liquidacion']);
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
				name: 'nro_nota',
				fieldLabel: 'nro_nota',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:10000
			},
				type:'TextField',
				filters:{pfiltro:'tns.nro_nota',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'nro_aut',
				fieldLabel: 'nro_aut',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:10000
			},
				type:'TextField',
				filters:{pfiltro:'tns.nro_aut',type:'string'},
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
				filters:{pfiltro:'tns.fecha_reg',type:'date'},
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
				filters:{pfiltro:'tns.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'tns.usuario_ai',type:'string'},
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
				filters:{pfiltro:'tns.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,	
	title:'NOTASIAT',
	ActSave:'../../sis_devoluciones/control/NotaSiat/insertarNotaSiat',
	ActDel:'../../sis_devoluciones/control/NotaSiat/eliminarNotaSiat',
	ActList:'../../sis_devoluciones/control/NotaSiat/listarNotaSiat',
	id_store:'id_nota_siat',
	fields: [
		{name:'id_nota_siat', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'id_liquidacion', type: 'numeric'},
		{name:'nro_nota', type: 'string'},
		{name:'nro_aut', type: 'string'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'desc_liquidacion', type: 'string'},

	],
	sortInfo:{
		field: 'id_nota_siat',
		direction: 'ASC'
	},
	bdel:true,
	bsave:true,

    onButtonNew : function() {
        Phx.vista.NotaSiat.superclass.onButtonNew.call(this);

        this.Cmp.id_liquidacion.hide();
        this.Cmp.id_liquidacion.setValue(this.id_liquidacion_);

        this.argumentExtraSubmit={'id_liquidacion':this.id_liquidacion_};

    },
    onButtonEdit: function() {
        Phx.vista.NotaSiat.superclass.onButtonEdit.call(this);

        this.Cmp.id_liquidacion.show();

    },

	}
)
</script>
		
		