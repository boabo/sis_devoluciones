<?php
/**
*@package pXP
*@file gen-Sucursal.php
*@author  (ada.torrico)
*@date 18-11-2014 20:00:02
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.Sucursal=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.Sucursal.superclass.constructor.call(this,config);
		this.init();
		this.addButton('importar',{argument: {imprimir: 'importar'},text:'<i class="fa fa-file-text-o fa-2x"></i> importar Sucursales',/*iconCls:'' ,*/disabled:false,handler:this.importarSucursal});

		this.load({params:{start:0, limit:this.tam_pag}})
	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_sucursal'
			},
			type:'Field',
			form:true 
		},

		{
			config:{
				name: 'sucursal',
				fieldLabel: 'sucursal',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:150,
				style: 'background-color: #ccc;'
			},
			type:'TextField',
			filters:{pfiltro:'sucu.sucursal',type:'string'},
			id_grupo:1,
			grid:true,
			form:true
		},

		
		{
			config:{
				name: 'alcaldia',
				fieldLabel: 'Alcaldia',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:150,
				style: 'background-color: #ccc;'
			},
				type:'TextField',
				filters:{pfiltro:'sucu.alcaldia',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'estacion',
				fieldLabel: 'Estacion',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:50
			},
				type:'TextField',
				filters:{pfiltro:'sucu.estacion',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'telefono',
				fieldLabel: 'Telefono',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:150
			},
				type:'TextField',
				filters:{pfiltro:'sucu.telefono',type:'string'},
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
				filters:{pfiltro:'sucu.estado_reg',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'direccion',
				fieldLabel: 'Direccion',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:150
			},
				type:'TextField',
				filters:{pfiltro:'sucu.direccion',type:'string'},
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
				filters:{pfiltro:'sucu.razon',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
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
				filters:{pfiltro:'sucu.fecha_reg',type:'date'},
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
				filters:{pfiltro:'sucu.usuario_ai',type:'string'},
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
				filters:{pfiltro:'sucu.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'sucu.fecha_mod',type:'date'},
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
	title:'Sucursal',
	ActSave:'../../sis_devoluciones/control/Sucursal/insertarSucursal',
	ActDel:'../../sis_devoluciones/control/Sucursal/eliminarSucursal',
	ActList:'../../sis_devoluciones/control/Sucursal/listarSucursal',
	id_store:'id_sucursal',
	fields: [
		{name:'id_sucursal', type: 'numeric'},
		{name:'alcaldia', type: 'string'},
		{name:'estacion', type: 'string'},
		{name:'telefono', type: 'string'},
		{name:'estado_reg', type: 'string'},
		{name:'direccion', type: 'string'},
		{name:'razon', type: 'string'},
		{name:'id_persona_resp', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'desc_person', type: 'string'},
		{name:'sucursal', type: 'string'},

	],
	sortInfo:{
		field: 'id_sucursal',
		direction: 'ASC'
	},
	bdel:true,
	bsave:true,

		importarSucursal:function(){

			Ext.Ajax.request({
				url: '../../sis_devoluciones/control/Sucursal/obtenerSucursalesInformix',
				params: {"tipo":"hola"},
				success: this.verAutorizacion,
				failure: this.conexionFailure,
				timeout: this.timeout,
				scope: this
			});


		}
	}
)
</script>
		
		