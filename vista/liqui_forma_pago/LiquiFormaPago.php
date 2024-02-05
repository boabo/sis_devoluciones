<?php
/**
*@package pXP
*@file gen-LiquiFormaPago.php
*@author  (admin)
*@date 06-01-2021 03:55:40
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.LiquiFormaPago=Ext.extend(Phx.gridInterfaz,{

    constructor:function(config){
        this.maestro=config.maestro;
        //llama al constructor de la clase padre
        Phx.vista.LiquiFormaPago.superclass.constructor.call(this,config);
        this.iniciarEventos();
        this.init();

        var dataPadre = Phx.CP.getPagina(this.idContenedorPadre).getSelectedData()
        if(dataPadre){
            this.onEnablePanel(this, dataPadre);
        }
        else
        {
            this.bloquearMenus();
        }

        //this.load({params:{start:0, limit:this.tam_pag}})
    },
    iniciarEventos: function () {



        this.Cmp.id_medio_pago.on('select', function (rec, d) {


            switch (d.json.nombre_fp) {
                case 'CREDIT CARD':
                    for (var i = 0; i < this.Atributos.length; i++) {
                        console.log(this.Componentes[i])

                        if (this.Componentes[i]) {
                            this.mostrarComponente(this.Componentes[i]);
                        }
                    }
                    break;
                case 'CHEQUE':
                    for (var i = 0; i < this.Atributos.length; i++) {
                        if (this.Componentes[i] && this.Componentes[i].name == 'comprobante') {
                            this.mostrarComponente(this.Componentes[i]);
                        }else {
                            this.ocultarComponente(this.Componentes[i]);
                        }
                    }
                    break;
                default:
                    console.log('default');
            };

        }, this);
    },
    onButtonNew:function() {
        var me = this;
        console.log('onButtonNew',this.Componentes)


        Phx.vista.LiquiFormaPago.superclass.onButtonNew.call(this);

        for (var i = 0; i < this.Atributos.length; i++) {
            if (this.Componentes[i] && this.Componentes[i].name == 'comprobante') {
                this.mostrarComponente(this.Componentes[i]);
            }else {
                this.ocultarComponente(this.Componentes[i]);
            }
        }
    },


        Atributos:[
        {
            //configuracion del componente
            config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_liqui_forma_pago'
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
                filters:{pfiltro:'tlp.estado_reg',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
        },

        {
            config: {
                name: 'id_medio_pago',
                fieldLabel: 'Medio de Pago',
                allowBlank: false,
                width:150,
                id: 'testeoColor',
                emptyText: 'Medio de pago...',
                store: new Ext.data.JsonStore({
                    url: '../../sis_obingresos/control/MedioPagoPw/listarMedioPagoPw',
                    id: 'id_medio_pago',
                    root: 'datos',
                    sortInfo: {
                        field: 'name',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_medio_pago_pw', 'name', 'fop_code'],
                    remoteSort: true,
                    baseParams: {par_filtro: 'mppw.name#fp.fop_code', emision:'dev', regional: 'BO'}
                }),
                valueField: 'id_medio_pago_pw',
                displayField: 'name',
                gdisplayField: 'desc_medio_pago',
                hiddenName: 'id_medio_pago_pw',
                tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Medio de Pago: <font color="Blue">{name}</font></b></p><b><p>Codigo: <font color="red">{fop_code}</font></b></p></div></tpl>',
                forceSelection: true,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender: true,
                mode: 'remote',
                pageSize: 15,
                queryDelay: 1000,
                // gwidth: 150,
                listWidth:250,
                resizable:true,
                minChars: 2,
                disabled:false
            },
            type: 'ComboBox',
            id_grupo: 2,
            grid: true,
            form: true
        },

            {
                config: {
                    name: 'id_auxiliar',
                    fieldLabel: 'Cuenta Corriente',
                    allowBlank: true,
                    emptyText: '',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_contabilidad/control/Auxiliar/listarAuxiliar',
                        id: 'id_auxiliar',
                        root: 'datos',
                        sortInfo: {
                            field: 'codigo_auxiliar',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_auxiliar', 'codigo_auxiliar','nombre_auxiliar'],
                        remoteSort: true,
                        baseParams: {par_filtro: 'auxcta.codigo_auxiliar#auxcta.nombre_auxiliar',corriente:'si', ro_activo:'no'}
                    }),
                    valueField: 'id_auxiliar',
                    displayField: 'nombre_auxiliar',
                    gdisplayField: 'desc_nombre_auxiliar',
                    hiddenName: 'id_auxiliar',
                    tpl:'<tpl for="."><div class="x-combo-list-item"><b><p style="color:red;">{nombre_auxiliar}</p><p>Codigo: <span style="color:green;">{codigo_auxiliar}</span></p></b></div></tpl>',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    gwidth: 150,
                    listWidth:350,
                    resizable:true,
                    minChars: 2,
                    renderer : function(value, p, record) {
                        return String.format('{0}', record.data['desc_nombre_auxiliar']);
                    }
                },
                type: 'ComboBox',
                id_grupo: 1,
                grid: true,
                form: true
            },


            {
                config: {
                    name: 'administradora',
                    fieldLabel: 'administradora',
                    allowBlank: true,
                    emptyText: 'administradora...',
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'local',
                    store: ['LINKSER', 'ATC', 'CYBER SOURCE', 'WORLDPAY', 'AMEX', 'KIOSKOS'],
                    width: 200
                },
                type: 'ComboBox',
                id_grupo: 4,
                form: true
            },

        {
            config:{
                name: 'pais',
                fieldLabel: 'pais',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:255
            },
                type:'TextField',
                filters:{pfiltro:'tlp.pais',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
        },
        {
            config:{
                name: 'ciudad',
                fieldLabel: 'ciudad',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:255
            },
                type:'TextField',
                filters:{pfiltro:'tlp.ciudad',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
        },
        {
            config:{
                name: 'fac_reporte',
                fieldLabel: 'fac_reporte',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:255
            },
                type:'TextField',
                filters:{pfiltro:'tlp.fac_reporte',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
        },
        {
            config:{
                name: 'cod_est',
                fieldLabel: 'cod_est',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:255
            },
                type:'TextField',
                filters:{pfiltro:'tlp.cod_est',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
        },
        {
            config:{
                name: 'lote',
                fieldLabel: 'lote',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:255
            },
                type:'TextField',
                filters:{pfiltro:'tlp.lote',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
        },
        {
            config:{
                name: 'comprobante',
                fieldLabel: 'comprobante',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:255
            },
                type:'TextField',
                filters:{pfiltro:'tlp.comprobante',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
        },
        {
            config:{
                name: 'autorizacion',
                fieldLabel: 'autorizacion',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:255
            },
                type:'TextField',
                filters:{pfiltro:'tlp.autorizacion',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
        },
        {
            config:{
                name: 'nro_terminal',
                fieldLabel: 'Nro Terminal o Comercio',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:255
            },
                type:'TextField',
                filters:{pfiltro:'tlp.nro_terminal',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
        },
        {
            config:{
                name: 'nombre_comercio',
                fieldLabel: 'Nombre Comercio',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:255
            },
                type:'TextField',
                filters:{pfiltro:'tlp.nombre_comercio',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
        },
        {
            config:{
                name: 'fecha_tarjeta',
                fieldLabel: 'fecha_tarjeta',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                            format: 'd/m/Y', 
                            renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
            },
                type:'DateField',
                filters:{pfiltro:'tlp.fecha_tarjeta',type:'date'},
                id_grupo:1,
                grid:true,
                form:true
        },
        {
            config:{
                name: 'fecha_cierre',
                fieldLabel: 'fecha_cierre',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                            format: 'd/m/Y',
                            renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
            },
                type:'DateField',
                filters:{pfiltro:'tlp.fecha_cierre',type:'date'},
                id_grupo:1,
                grid:true,
                form:true
        },
        {
            config:{
                name: 'nro_tarjeta',
                fieldLabel: 'nro_tarjeta',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:255
            },
                type:'TextField',
                filters:{pfiltro:'tlp.nro_tarjeta',type:'string'},
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
                filters:{pfiltro:'tlp.importe',type:'numeric'},
                id_grupo:1,
                grid:true,
                form:true
        },
        {
            config:{
                name: 'nro_documento_pago',
                fieldLabel: 'Nro Doc Pago(cheque u otro)',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:655362
            },
                type:'NumberField',
                filters:{pfiltro:'tlp.nro_documento_pago',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
        },
        {
            config:{
                name: 'nombre',
                fieldLabel: 'A Nombre de',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:655362
            },
                type:'TextField',
                filters:{pfiltro:'tlp.nombre',type:'string'},
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
                filters:{pfiltro:'tlp.fecha_reg',type:'date'},
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
                filters:{pfiltro:'tlp.id_usuario_ai',type:'numeric'},
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
                filters:{pfiltro:'tlp.usuario_ai',type:'string'},
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
                filters:{pfiltro:'tlp.fecha_mod',type:'date'},
                id_grupo:1,
                grid:true,
                form:false
        }
    ],
    tam_pag:50,    
    title:'liqui forma pago',
    ActSave:'../../sis_devoluciones/control/LiquiFormaPago/insertarLiquiFormaPago',
    ActDel:'../../sis_devoluciones/control/LiquiFormaPago/eliminarLiquiFormaPago',
    ActList:'../../sis_devoluciones/control/LiquiFormaPago/listarLiquiFormaPago',
    id_store:'id_liqui_forma_pago',
    fields: [
        {name:'id_liqui_forma_pago', type: 'numeric'},
        {name:'estado_reg', type: 'string'},
        {name:'id_liquidacion', type: 'numeric'},
        {name:'id_forma_pago', type: 'numeric'},
        {name:'id_medio_pago', type: 'numeric'},
        {name:'pais', type: 'string'},
        {name:'ciudad', type: 'string'},
        {name:'fac_reporte', type: 'string'},
        {name:'cod_est', type: 'string'},
        {name:'lote', type: 'string'},
        {name:'comprobante', type: 'string'},
        {name:'fecha_tarjeta', type: 'date',dateFormat:'Y-m-d'},
        {name:'nro_tarjeta', type: 'string'},
        {name:'importe', type: 'numeric'},
        {name:'id_usuario_reg', type: 'numeric'},
        {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
        {name:'id_usuario_ai', type: 'numeric'},
        {name:'usuario_ai', type: 'string'},
        {name:'id_usuario_mod', type: 'numeric'},
        {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
        {name:'usr_reg', type: 'string'},
        {name:'usr_mod', type: 'string'},
        {name:'desc_medio_pago', type: 'string'},
        {name:'nro_documento_pago', type: 'string'},
        {name:'nombre', type: 'string'},
        {name:'administradora', type: 'string'},
        {name:'desc_nombre_auxiliar', type: 'string'},
        {name:'autorizacion', type: 'string'},
        {name:'fecha_cierre', type: 'date',dateFormat:'Y-m-d'},
        'nro_terminal',
        'nombre_comercio'


    ],
    sortInfo:{
        field: 'id_liqui_forma_pago',
        direction: 'ASC'
    },
    bdel:true,
    bsave:true,

    onReloadPage:function(m){
        this.maestro=m;
        this.store.baseParams={id_liquidacion:this.maestro.id_liquidacion};
        this.load({params: {start: 0, limit: 50}});


    },
    loadValoresIniciales: function () {
        this.Cmp.id_liquidacion.setValue(this.maestro.id_liquidacion);
        Phx.vista.LiquiFormaPago.superclass.loadValoresIniciales.call(this);
    },
    successSave: function (resp) {
        console.log('onSuccessonSuccessonSuccess');
        Phx.vista.LiquiFormaPago.superclass.successSave.call(this,resp);
        Phx.CP.getPagina(this.idContenedorPadre).reload();
    },

    onSubmit:function(o) {

        this.refreshPadre = true;
console.log('0',o)



        const autorizacion = this.Cmp.autorizacion.getValue();
        const fecha_tarjeta = this.Cmp.fecha_tarjeta.getValue();
        const nro_tarjeta = this.Cmp.nro_tarjeta.getValue();
        const left = nro_tarjeta.substring(0,4);
        const right = nro_tarjeta.substr(nro_tarjeta.length -4);

        console.log('fecha_tarjeta',fecha_tarjeta)
        //Phx.CP.loadingShow();

        if(left && right && autorizacion && fecha_tarjeta) {
            Ext.Ajax.request({
                url: '../../sis_devoluciones/control/Liquidacion/getPaymentInformation',
                params: {
                    leftNumber: left,
                    rigthNumber: right,
                    authorizationNumber: autorizacion,
                    paymentDate: moment(fecha_tarjeta).format('YYYY-MM-DD')
                },
                success: (resp) => {
                    console.log('resp',resp)
                    const reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                    if(!reg.success) {
                        var seguro = confirm(`${reg.message} Deseas Continuar??? si presionas ok registrara`);
                        if(seguro){
                            Phx.vista.LiquiFormaPago.superclass.onSubmit.call(this, o, undefined, true);//habilita el boton y se abre
                        }

                    } else {
                        Phx.vista.LiquiFormaPago.superclass.onSubmit.call(this, o, undefined, true);//habilita el boton y se abre

                    }

                },
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
        } else {
            Phx.vista.LiquiFormaPago.superclass.onSubmit.call(this, o, undefined, true);//habilita el boton y se abre

        }




    }

    }
)
</script>
        
        