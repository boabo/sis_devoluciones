<?php
/**
*@package pXP
*@file gen-Devweb.php
*@author  (admin)
*@date 04-07-2016 15:19:06
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.Devweb=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.Devweb.superclass.constructor.call(this,config);
		this.init();
		this.load({params:{start:0, limit:this.tam_pag}})
	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_devweb'
			},
			type:'Field',
			form:true 
		},
		{
			config: {
				name: 'estado',
				fieldLabel: 'estado',
				allowBlank: false,
				emptyText: 'estado...',
				typeAhead: true,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'local',
				store: ['activo', 'inactivo'],
				width: 200
			},
			type: 'ComboBox',
			filters: {pfiltro: 'devweb.estado', type: 'string'},
			id_grupo: 1,
			form: true,
			grid: true
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
				filters:{pfiltro:'devweb.estado_reg',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name:'id_usuario',
				fieldLabel:'Usuario',
				allowBlank:false,
				emptyText:'Usuario...',
				store: new Ext.data.JsonStore({

					url: '../../sis_seguridad/control/Usuario/listarUsuario',
					id: 'id_persona',
					root: 'datos',
					sortInfo:{
						field: 'desc_person',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_usuario','desc_person','cuenta'],
					// turn on remote sorting
					remoteSort: true,
					baseParams:{par_filtro:'PERSON.nombre_completo2#cuenta'}
				}),
				valueField: 'id_usuario',
				displayField: 'desc_person',
				gdisplayField:'desc_persona',//dibuja el campo extra de la consulta al hacer un inner join con orra tabla
				tpl:'<tpl for="."><div class="x-combo-list-item"><p>{desc_person}</p><p>cuenta:{cuenta}</p> </div></tpl>',
				hiddenName: 'id_usuario',
				forceSelection:true,
				typeAhead: true,
				triggerAction: 'all',
				lazyRender:true,
				mode:'remote',
				pageSize:10,
				queryDelay:1000,
				width:250,
				gwidth:280,
				minChars:2,
				turl:'../../../sis_seguridad/vista/usuario/Usuario.php',
				ttitle:'Usuarios',
				// tconfig:{width:1800,height:500},
				tdata:{},
				tcls:'usuario',
				pid:this.idContenedor,

				renderer:function (value, p, record){return String.format('{0}', record.data['desc_persona']);}
			},
			type:'TrigguerCombo',
			//type:'ComboRec',
			id_grupo:0,
			filters:{
				pfiltro:'desc_person',
				type:'string'
			},

			grid:true,
			form:true
		},
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: '',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'devweb.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'devweb.usuario_ai',type:'string'},
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
				filters:{pfiltro:'devweb.fecha_reg',type:'date'},
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
				filters:{pfiltro:'devweb.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,	
	title:'devweb',
	ActSave:'../../sis_devoluciones/control/Devweb/insertarDevweb',
	ActDel:'../../sis_devoluciones/control/Devweb/eliminarDevweb',
	ActList:'../../sis_devoluciones/control/Devweb/listarDevweb',
	id_store:'id_devweb',
	fields: [
		{name:'id_devweb', type: 'numeric'},
		{name:'estado', type: 'string'},
		{name:'estado_reg', type: 'string'},
		{name:'id_usuario', type: 'numeric'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'desc_persona', type: 'string'},

	],
	sortInfo:{
		field: 'id_devweb',
		direction: 'ASC'
	},
	bdel:true,
	bsave:true
	}
)
</script>
		
		