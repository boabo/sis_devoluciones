<?php
/**
 * @package pXP
 * @file    FormLiquidacion.php
 * @author  Favio Figueroa
 * @date    30-01-2014
 */
header("content-type: text/javascript; charset=UTF-8");
?>

<script>
    Phx.vista.FormLiquidacion = Ext.extend(Phx.frmInterfaz, {
        ActSave: '../../sis_devoluciones/control/Liquidacion/insertarLiquidacion',
        tam_pag: 10,
        //layoutType: 'wizard',
        layout: 'fit',
        autoScroll: false,
        breset: false,
        labelSubmit: '<i class="fa fa-check"></i> Siguiente',
        storeBoletosRecursivo : false,


        constructor: function (config) {

            //declaracion de eventos
            this.addEvents('beforesave');
            this.addEvents('successsave');

            this.buildComponentesDetalle();
            this.buildDetailGrid();
            this.buildGrupos();

            Phx.vista.FormLiquidacion.superclass.constructor.call(this, config);
            this.init();
            this.iniciarEventos();
            this.iniciarEventosDetalle();
            this.onNew();

        },
        buildComponentesDetalle: function () {

            this.detCmp = {
                'id_concepto_ingas': new Ext.form.ComboBox({
                    name: 'id_concepto_ingas',
                    msgTarget: 'title',
                    fieldLabel: 'id_concepto_ingas',
                    allowBlank: false,
                    emptyText: 'Elija una opción...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_ventas_facturacion/control/Servicios/listarServicios',
                        id: 'id_concepto_ingas',
                        root: 'datos',
                        sortInfo: {
                            field: 'desc_ingas',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_concepto_ingas', 'tipo','desc_moneda','id_moneda','desc_ingas','requiere_descripcion','precio','excento','contabilizable'],
                        remoteSort: true,
                        baseParams: {par_filtro: 'ingas.desc_ingas',facturacion:'dev', emision:'DEVOLUCIONES'}
                    }),
                    valueField: 'id_concepto_ingas',
                    displayField: 'desc_ingas',
                    gdisplayField: 'desc_ingas',
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
                        return String.format('{0}', record.data['desc_ingas']);
                    }
                }),

                'descripcion': new Ext.form.TextArea({
                    name: 'descripcion',
                    msgTarget: 'title',
                    fieldLabel: 'Descripcion',
                    allowBlank: false,
                    anchor: '80%',
                    maxLength: 5000
                }),
                'importe': new Ext.form.NumberField({
                    name: 'importe',
                    msgTarget: 'title',
                    fieldLabel: 'Importe ',
                    allowBlank: true,
                    allowDecimals: false,
                    minValue: 1,
                    maxLength: 10
                }),



            }


        },
        iniciarEventosDetalle: function () {


            /*this.ocultarComponente(this.detCmp.id_activo_fijo);

*/

        },

        onInitAdd: function () {


        },
        onCancelAdd: function (re, save) {
            if (this.sw_init_add) {
                this.mestore.remove(this.mestore.getAt(0));
            }

            this.sw_init_add = false;
            this.evaluaGrilla();
        },
        onUpdateRegister: function () {
            this.sw_init_add = false;
        },

        onAfterEdit: function (re, o, rec, num) {
            //set descriptins values ...  in combos boxs

            //todo borrar esto despues
            var cmb_rec = this.detCmp['id_concepto_ingas'].store.getById(rec.get('id_concepto_ingas'));
            if (cmb_rec) {
                rec.set('desc_concepto_ingas', cmb_rec.get('desc_ingas'));
            }

        },

        evaluaRequistos: function () {
            //valida que todos los requistosprevios esten completos y habilita la adicion en el grid
            var i = 0;
            sw = true
           /* while (i < this.Componentes.length) {

                if (!this.Componentes[i].isValid()) {
                    sw = false;
                    //i = this.Componentes.length;
                }
                i++;
            }*/


            return sw
        },

        bloqueaRequisitos: function (sw) {
            this.Cmp.id_depto.setDisabled(sw);
            this.Cmp.id_moneda.setDisabled(sw);

            this.Cmp.tipo_concepto.setDisabled(sw);
            this.Cmp.fecha_soli.setDisabled(sw);
            this.cargarDatosMaestro();

        },

        cargarDatosMaestro: function () {


         /*   this.detCmp.id_orden_trabajo.store.baseParams.fecha_solicitud = this.Cmp.fecha_soli.getValue().dateFormat('d/m/Y');
            this.detCmp.id_orden_trabajo.modificado = true;

            this.detCmp.id_centro_costo.store.baseParams.id_gestion = this.Cmp.id_gestion.getValue();
            this.detCmp.id_centro_costo.store.baseParams.codigo_subsistema = 'ADQ';
            this.detCmp.id_centro_costo.store.baseParams.id_depto = this.Cmp.id_depto.getValue();
            this.detCmp.id_centro_costo.modificado = true;*/


        },

        evaluaGrilla: function () {
            //al eliminar si no quedan registros en la grilla desbloquea los requisitos en el maestro
            var count = this.mestore.getCount();
            if (count == 0) {
                //this.bloqueaRequisitos(false);
            }
        },


        buildDetailGrid: function () {

            //cantidad,detalle,peso,totalo
            var Items = Ext.data.Record.create([ {
                name: 'id_concepto_ingas',
                type: 'int'
            }, {
                name: 'desc_ingas',
                type: 'string'
            },
            ]);

            this.mestore = new Ext.data.JsonStore({
                url: '../../sis_adquisiciones/control/SolicitudDet/listarSolicitudDet',
                id: 'id_concepto_ingas',
                root: 'datos',
                totalProperty: 'total',
                fields: ['id_solicitud_det', 'id_centro_costo', 'descripcion', 'precio_unitario',
                    'id_solicitud', 'id_orden_trabajo', 'id_concepto_ingas', 'precio_total', 'cantidad_sol',
                    'desc_centro_costo', 'desc_concepto_ingas', 'desc_orden_trabajo', 'id_activo_fijo',
                    'fecha_ini_act', 'fecha_fin_act', 'lista', 'desc_ingas'
                ], remoteSort: true,
                baseParams: {dir: 'ASC', sort: 'id_concepto_ingas', limit: '50', start: '0'}
            });

            this.editorDetail = new Ext.ux.grid.RowEditor({
                saveText: 'Aceptar',
                name: 'btn_editor'

            });

            this.summary = new Ext.ux.grid.GridSummary();
            // al iniciar la edicion
            this.editorDetail.on('beforeedit', this.onInitAdd, this);

            //al cancelar la edicion
            this.editorDetail.on('canceledit', this.onCancelAdd, this);

            //al cancelar la edicion
            this.editorDetail.on('validateedit', this.onUpdateRegister, this);

            this.editorDetail.on('afteredit', this.onAfterEdit, this);


            this.megrid = new Ext.grid.GridPanel({
                layout: 'fit',
                store: this.mestore,
                region: 'center',
                split: true,
                border: false,
                plain: true,
                //autoHeight: true,
                plugins: [this.editorDetail, this.summary],
                stripeRows: true,
                tbar: [{
                    /*iconCls: 'badd',*/
                    text: '<i class="fa fa-plus-circle fa-lg"></i> Agregar Concepto',
                    scope: this,
                    width: '100',
                    handler: function () {
                        if (this.evaluaRequistos() === true) {

                            var e = new Items({
                                id_concepto_ingas: undefined,
                                descripcion: '',
                            });
                            this.editorDetail.stopEditing();
                            this.mestore.insert(0, e);
                            this.megrid.getView().refresh();
                            this.megrid.getSelectionModel().selectRow(0);
                            this.editorDetail.startEditing(0);
                            this.sw_init_add = true;

                            //this.bloqueaRequisitos(true);
                        }
                        else {
                            //alert('Verifique los requisitos');
                        }

                    }
                }, {
                    ref: '../removeBtn',
                    text: '<i class="fa fa-trash fa-lg"></i> Eliminar',
                    scope: this,
                    handler: function () {
                        this.editorDetail.stopEditing();
                        var s = this.megrid.getSelectionModel().getSelections();
                        for (var i = 0, r; r = s[i]; i++) {
                            this.mestore.remove(r);
                        }
                        this.evaluaGrilla();
                    }
                }],

                columns: [
                    new Ext.grid.RowNumberer(),
                    {
                        header: 'Concepto',
                        dataIndex: 'id_concepto_ingas',
                        width: 200,
                        sortable: false,
                        renderer: function (value, p, record) {
                            console.log(`value ${value} p ${p} record ${record}`)
                            console.log(p)
                            console.log(record)
                            return String.format('{0}', record.data['desc_concepto_ingas']);
                        },
                        editor: this.detCmp.id_concepto_ingas
                    },
                    {
                        header: 'contabilizar',
                        dataIndex: 'contabilizar',
                        hidden: false,
                        hideable: false,
                        width: 100,
                        sortable: false,
                        editor: {
                            xtype: 'combo',
                            name: 'contabilizar',
                            fieldLabel: 'contabilizar',
                            allowBlank: true,
                            emptyText: 'contabilizar...',
                            typeAhead: true,
                            triggerAction: 'all',
                            lazyRender: true,
                            mode: 'local',
                            store: ['si','no'],
                            width: 200,
                            enableKeyEvents: true,
                            disabled:true,
                        }
                    },
                    {

                        header: 'Descripción',
                        dataIndex: 'descripcion',

                        align: 'center',
                        width: 200,
                        editor: this.detCmp.descripcion
                    },


                    {

                        header: 'importe',
                        dataIndex: 'importe',
                        align: 'center',
                        width: 50,
                        trueText: 'Yes',
                        falseText: 'No',
                        //minValue: 0.001,
                        minValue: 0,
                        summaryType: 'sum',
                        editor: this.detCmp.importe
                    },



                ]
            });
        },

        buildGrupos: function () {
            this.Grupos = [{
                layout: 'border',
                border: true,
                frame: true,
                //labelAlign: 'top',
                items: [
                    {
                        xtype: 'fieldset',
                        border: false,
                        split: true,
                        layout: 'column',
                        region: 'north',
                        autoScroll: true,
                        autoHeight: true,
                        collapseFirst: false,
                        collapsible: true,
                        width: '100%',
                        padding: '0 0 0 10',
                        items: [
                            {
                                bodyStyle: 'padding-right:5px;',

                                border: false,
                                autoHeight: true,
                                columnWidth: .32,
                                items: [{
                                    xtype: 'fieldset',
                                    //frame: true,
                                    layout: 'form',
                                    title: ' TIPO ',
                                    //width: '33%',

                                    //border: false,
                                    //margins: '0 0 0 5',
                                    padding: '0 0 0 10',
                                    bodyStyle: 'padding-left:5px;',
                                    id_grupo: 0,
                                    items: [],
                                }]
                            },
                            {
                                bodyStyle: 'padding-right:5px;',

                                autoHeight: true,
                                border: false,
                                columnWidth: .32,
                                items: [
                                    {
                                        xtype: 'fieldset',
                                        /*frame: true,
                                        border: false,*/
                                        layout: 'form',
                                        title: ' DATOS BÁSICOS ',
                                        //width: '33%',

                                        //margins: '0 0 0 5',
                                        padding: '0 0 0 10',
                                        bodyStyle: 'padding-left:5px;',
                                        id_grupo: 1,
                                        items: [{
                                            xtype:'button',

                                            text:'Datos Boletos',
                                            handler: this.onDatosBoleto,
                                            scope:this,
                                            //makes the button 24px high, there is also 'large' for this config
                                            scale: 'medium'
                                        }],
                                    },
                                    {
                                        xtype: 'fieldset',
                                        /*frame: true,
                                        border: false,*/
                                        layout: 'form',
                                        title: ' DATOS BÁSICOS FACTURA COM',
                                        //width: '33%',

                                        //margins: '0 0 0 5',
                                        padding: '0 0 0 10',
                                        bodyStyle: 'padding-left:5px;',
                                        id_grupo: 3,
                                        items: [],
                                    }]
                            },
                            {
                                bodyStyle: 'padding-right:2px;',

                                border: true,
                                autoHeight: true,
                                columnWidth: .32,
                                items: [{
                                    xtype: 'fieldset',
                                    //frame: true,
                                    layout: 'form',
                                    title: 'TIEMPO',
                                    //width: '33%',
                                    //border: false,
                                    //margins: '0 0 0 5',
                                    padding: '0 0 0 10',
                                    bodyStyle: 'padding-left:2px;',
                                    id_grupo: 2,
                                    items: [],
                                }]
                            }
                        ]
                    },
                    this.megrid
                ]
            }];


        },
        crearStoreBoletosRecursivo : function (billete) {
            Phx.CP.loadingShow();

            const tramoDevolucion = this.getComponente('tramo_devolucion');
            const tramoComponente = this.getComponente('tramo');
            const importeNeto = this.getComponente('importe_neto');
            const importeTotalComponente = this.getComponente('importe_total');
            const tasas = this.getComponente('tasas');
            const nombre = this.getComponente('nombre');
            const monedaEmision = this.getComponente('moneda_emision');
            const puntoVenta = this.getComponente('punto_venta');
            const estacion = this.getComponente('estacion');


            this.storeBoletosRecursivo = new Ext.data.JsonStore({
                url: '../../sis_devoluciones/control/Liquidacion/getTicketInformationRecursive',
                id: 'billete',
                root: 'datos',
                sortInfo: {
                    field: 'billete',
                    direction: 'ASC'
                },
                totalProperty: 'total',
                fields: [
                    {name: 'seleccionado',      type: 'string'},
                    {name: 'billete',      type: 'string'},
                    {name: 'monto',     type: 'numeric'},
                ],
            });
            this.storeBoletosRecursivo.baseParams.billete = billete;
            this.storeBoletosRecursivo.load({
                params: {start: 0, limit: 100},
                callback: function (e,d) {
                    let total = 0;
                    e.forEach((data)=> {
                        total = total + data.data.monto;
                    });
                    console.log(importeTotalComponente)
                    importeTotalComponente.setValue(total);
                    console.log('asda',e[0])
                    tramoComponente.setValue(e[0].json.itinerary);


                    importeNeto.setValue(e[0].json.netAmount);
                    tasas.setValue(parseFloat(total) - parseFloat(e[0].json.netAmount));
                    nombre.setValue(e[0].json.passengerName);
                    monedaEmision.setValue(e[0].json.currency);
                    puntoVenta.setValue(e[0].json.issueOfficeID);

                    Phx.CP.loadingHide();
                },
            });
        },
        onDatosBoleto : function () {
            if (!this.Cmp.id_boleto.getValue() && !this.Cmp.id_boleto.getValue()) {
                Ext.Msg.alert('ATENCION', 'Debe seleccionar un boleto');
            } else {

                 var wid = Ext.id();

                 /*if (!this.storeBoletosRecursivo) {
                     this.crearStoreBoletosRecursivo();
                 }*/

                 // create the Grid
                 var grid = new Ext.grid.EditorGridPanel({
                     store: this.storeBoletosRecursivo,
                     stateful: false,
                     margins: '3 3 3 0',
                     loadMask: true,
                     columns: [
                         {
                             header     : 'seleccionado',
                             width    : 125,
                             dataIndex: 'seleccionado',
                             editable : true,
                             editor: new Ext.form.ComboBox({
                                 name: 'seleccionado',
                                 fieldLabel: 'seleccionado',
                                 allowBlank: true,
                                 emptyText:'seleccionado...',
                                 triggerAction: 'all',
                                 lazyRender:true,
                                 mode: 'local',
                                 displayField: 'text',
                                 valueField: 'value',
                                 store:new Ext.data.SimpleStore({
                                     data : [['si', 'si'], ['no', 'no'],],
                                     id : 'value',
                                     fields : ['value', 'text']
                                 })
                             })
                         },
                         {
                             header     : 'Billete',
                             flex     : 1,
                             width    : 280,
                             dataIndex: 'billete'
                         },
                         {
                             header     : 'Monto',
                             flex     : 1,
                             width    : 280,
                             dataIndex: 'monto'
                         },
                     ],
                     region:  'center',
                 });

                 var win = new Ext.Window({
                     id: wid,
                     layout:'fit',
                     width:820,
                     height:350,
                     modal:true,
                     items: grid,
                     title: 'Historia Billetes',
                     buttons: [{
                         text:'Guardar',
                         disabled:false,
                         scope : this,
                         handler : function () {
                             this.storeBoletosRecursivo.commitChanges();
                             var validado = true;
                             console.log(this.storeBoletosRecursivo.getTotalCount())
                             let total = 0;
                             for(var i = 0; i < this.storeBoletosRecursivo.getTotalCount() ;i++) {
                                 var fp = this.storeBoletosRecursivo.getAt(i);
                                 if (fp.data.seleccionado == 'si') {
                                     total = total + fp.data.monto;
                                     console.log('siiii')
                                 } else {
                                     console.log('nooo')

                                 }
                             }
                             this.cmpImporte_total.setValue(total);
                             console.log(total)
                             win.close();

                             /*if (validado) {
                                 this.Cmp.id_forma_pago.setValue(0);
                                 this.Cmp.monto_forma_pago.setValue(0);
                                 this.Cmp.monto_forma_pago.setDisabled(true);
                                 this.Cmp.id_forma_pago.setRawValue('DIVIDIDO');
                                 this.Cmp.id_forma_pago.setDisabled(true);
                                 this.ocultarComponente(this.Cmp.numero_tarjeta);
                                 this.ocultarComponente(this.Cmp.codigo_tarjeta);
                                 this.ocultarComponente(this.Cmp.tipo_tarjeta);
                                 this.Cmp.numero_tarjeta.allowBlank = false;
                                 this.Cmp.codigo_tarjeta.allowBlank = false;
                                 this.Cmp.tipo_tarjeta.allowBlank = false;
                                 this.Cmp.numero_tarjeta.reset();
                                 this.Cmp.codigo_tarjeta.reset();
                                 this.Cmp.tipo_tarjeta.reset();
                                 win.close();
                             }*/

                         }
                     }]
                 });
                 win.show();
            }
        },

        loadValoresIniciales: function () {

            Phx.vista.FormLiquidacion.superclass.loadValoresIniciales.call(this);


        },

        successSave: function (resp) {
            Phx.CP.loadingHide();
            Phx.CP.getPagina(this.idContenedorPadre).reload();
            this.panel.close();
        },


        arrayStore: {
            'Bien': [
                ['bien', 'Bienes'],
                //['inmueble','Inmuebles'],
                //['vehiculo','Vehiculos']
            ],
            'Servicio': [
                ['servicio', 'Servicios'],
                ['consultoria_personal', 'Consultoria de Personas'],
                ['consultoria_empresa', 'Consultoria de Empresas'],
                //['alquiler_inmueble','Alquiler Inmuebles']
            ]
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
                    gdisplayField: 'desc_tipo_documento',
                    hiddenName: 'id_tipo_doc_liquidacion',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    gwidth: 150,
                    minChars: 2,
                    width:150,

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
                    gdisplayField: 'desc_tipo_liquidacion',
                    hiddenName: 'id_tipo_liquidacion',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    width:200,
                    gwidth: 150,
                    minChars: 2,
                },
                type: 'ComboBox',
                id_grupo: 0,
                filters: {pfiltro: 'movtip.nombre',type: 'string'},
                grid: true,
                form: true
            },


            {
                config: {
                    name: 'id_punto_venta',
                    id: 'id_punto_venta',
                    fieldLabel: 'Punto Venta',
                    allowBlank: true,
                    emptyText:'Punto de Venta...',
                    blankText: 'Año',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_ventas_facturacion/control/PuntoVenta/listarPuntoVenta',
                        id: 'id_punto_venta',
                        root: 'datos',
                        sortInfo: {
                            field: 'nombre',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_punto_venta', 'id_sucursal','nombre', 'codigo','habilitar_comisiones','formato_comprobante'],
                        remoteSort: true,
                        baseParams: {tipo_usuario: 'cajero',par_filtro: 'puve.nombre#puve.codigo', tipo_factura: this.tipo_factura}
                    }),
                    tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Codigo:</b> {codigo}</p><p><b>Nombre:</b> {nombre}</p></div></tpl>',
                    valueField: 'id_punto_venta',
                    triggerAction: 'all',
                    displayField: 'nombre',
                    hiddenName: 'id_punto_venta',
                    mode:'remote',
                    pageSize:50,
                    queryDelay:500,
                    listWidth:'300',
                    hidden:false,
                    gdisplayField: 'desc_punto_venta',
                    forceSelection: true,
                    typeAhead: false,
                    lazyRender: true,
                    gwidth: 150,
                    width:200,
                    minChars: 2,
                },
                type: 'ComboBox',
                id_grupo: 0,
                filters: {pfiltro: 'movtip.nombre',type: 'string'},
                grid: true,
                form: true
            },


            {
                config : {
                    name : 'estacion',
                    fieldLabel : 'Estacion',
                    tinit : false,
                    allowBlank : false,
                    origen : 'CATALOGO',
                    gdisplayField : 'estacion',

                    width:200,

                    gwidth : 200,
                    baseParams : {
                        cod_subsistema : 'DECR',
                        catalogo_tipo : 'tliquidacion_estacion'
                    },
                    renderer:function(value, p, record){return String.format('{0}', record.data['estacion']);}
                },
                type : 'ComboRec',
                id_grupo : 0,
                filters : {pfiltro : 'dos.nombre_sistema', type : 'string'},
                grid : true,
                form : true
            },


            {
                config:{
                    name: 'nro_liquidacion',
                    fieldLabel: 'Nro liquidacion',
                    allowBlank: false,
                    width:200,
                    gwidth: 100,
                    maxLength:255,
                    disabled: true,
                },
                type:'TextField',
                filters:{pfiltro:'liqui.nro_liquidacion',type:'string'},
                id_grupo:0,
                grid:true,
                form:true,
                bottom_filter : true

            },
            {
                config:{
                    name: 'fecha_liqui',
                    fieldLabel: 'Fecha Liqui',
                    allowBlank: true,
                    width:200,
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                filters:{pfiltro:'liqui.fecha_liqui',type:'date'},
                id_grupo:0,
                grid:true,
                form:true
            },
            {
                config: {
                    name: 'id_boleto',
                    fieldLabel: 'Boleto',
                    allowBlank: true,
                    emptyText: 'Elija una opción...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_devoluciones/control/Liquidacion/listarBoleto',
                        id: 'id_boleto',
                        root: 'datos',
                        sortInfo: {
                            field: 'id_boleto',
                            direction: 'desc'
                        },
                        totalProperty: 'total',
                        fields: ['id_boleto', 'nro_boleto'],
                        remoteSort: true,
                        baseParams: {par_filtro: 'bol.nro_boleto'}
                    }),
                    valueField: 'id_boleto',
                    displayField: 'nro_boleto',
                    gdisplayField: 'desc_nro_boleto',
                    hiddenName: 'id_boleto',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    gwidth: 150,
                    width: 200,
                    minChars: 2,
                    renderer : function(value, p, record) {
                        return String.format('{0}', record.data['desc_nro_boleto']);
                    }
                },
                type: 'ComboBox',
                id_grupo: 1,
                filters: {pfiltro: 'tb.nro_boleto',type: 'string'},
                grid: true,
                form: true,
                bottom_filter : true
            },





            {
                config:{
                    name: 'nombre',
                    fieldLabel: 'Nombre',
                    allowBlank: true,
                    gwidth: 100,
                    width: 200,

                    maxLength:255
                },
                type:'TextField',
                filters:{pfiltro:'liqui.nombre',type:'string'},
                id_grupo:1,
                grid:true,
                form:true,
                bottom_filter : true
            },


            {
                config:{
                    name: 'punto_venta',
                    fieldLabel: 'Punto de Venta',
                    allowBlank: true,
                    width: 200,
                    gwidth: 100,
                    maxLength:255,
                    disabled: true,
                },
                type:'TextField',
                filters:{pfiltro:'liqui.punto_venta',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'moneda_emision',
                    fieldLabel: 'Moneda Emision',
                    allowBlank: true,
                    width: 200,
                    gwidth: 100,
                    maxLength:255,
                    disabled: true,
                },
                type:'TextField',
                filters:{pfiltro:'liqui.importe_neto',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'importe_neto',
                    fieldLabel: 'Importe Neto',
                    allowBlank: true,
                    width: 200,
                    gwidth: 100,
                    maxLength:255,
                    //disabled: true,
                },
                type:'TextField',
                filters:{pfiltro:'liqui.importe_neto',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'tasas',
                    fieldLabel: 'tasas',
                    allowBlank: true,
                    width: 200,
                    gwidth: 100,
                    maxLength:255,
                    disabled: true,
                },
                type:'TextField',
                filters:{pfiltro:'liqui.tasas',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'importe_total',
                    fieldLabel: 'Importe Total',
                    allowBlank: true,
                    width: 200,
                    gwidth: 100,
                    maxLength:255,
                    //disabled: true,
                },
                type:'TextField',
                filters:{pfiltro:'liqui.importe_total',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },

            {
                config:{
                    name: 'tramo',
                    fieldLabel: 'Tramo',
                    allowBlank: true,
                    width: 200,
                    gwidth: 100,
                    maxLength:255,
                    disabled: true,
                },
                type:'TextField',
                filters:{pfiltro:'liqui.tramo',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name:'tramo_devolucion',
                    fieldLabel:'Tramos Devolucion',
                    allowBlank:true,
                    emptyText:'Tramos...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_devoluciones/control/Liquidacion/obtenerTramosSql',
                        id: 'id',
                        root: 'datos',
                        totalProperty: 'total',
                        fields: ['id','desc'],
                        // turn on remote sorting
                        remoteSort: true,
                        //baseParams:{par_filtro:'rol'}

                    }),

                    valueField: 'id',
                    displayField: 'desc',
                    forceSelection:true,
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode:'remote',
                    pageSize:10,
                    queryDelay:1000,
                    width:150,
                    minChars:2,
                    enableMultiSelect:true,
                },
                type:'AwesomeCombo',
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'util',
                    fieldLabel: 'Tramo Utilizado(facturable)',
                    allowBlank: true,
                    width: 200,
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
                    width: 200,
                    gwidth: 150,
                    minChars: 2,
                    renderer : function(value, p, record) {
                        return String.format('{0}', record.data['desc_']);
                    }
                },
                type: 'ComboBox',
                id_grupo: 2,
                filters: {pfiltro: 'movtip.nombre',type: 'string'},
                grid: true,
                form: true
            },

            {
                config:{
                    name: 'estado_reg',
                    fieldLabel: 'Estado Reg.',
                    allowBlank: true,
                    width: 200,
                    gwidth: 100,
                    maxLength:10
                },
                type:'TextField',
                filters:{pfiltro:'liqui.estado_reg',type:'string'},
                id_grupo:2,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'tipo_de_cambio',
                    fieldLabel: 'tipo de cambio',
                    allowBlank: true,
                    width: 200,
                    gwidth: 100,
                    maxLength:655362
                },
                type:'NumberField',
                filters:{pfiltro:'liqui.tipo_de_cambio',type:'numeric'},
                id_grupo:2,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'descripcion',
                    fieldLabel: 'Descripcion',
                    allowBlank: true,
                    width: 200,
                    gwidth: 100,
                    maxLength:255
                },
                type:'TextField',
                filters:{pfiltro:'liqui.descripcion',type:'string'},
                id_grupo:2,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'nombre_cheque',
                    fieldLabel: 'Nombre cheque',
                    allowBlank: true,
                    width: 200,
                    gwidth: 100,
                    maxLength:255
                },
                type:'TextField',
                filters:{pfiltro:'liqui.nombre_cheque',type:'string'},
                id_grupo:2,
                grid:true,
                form:true
            },

            /*{
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
            },*/

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
                id_grupo:2,
                grid:true,
                form:false
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
                id_grupo:2,
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
                id_grupo:2,
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
                id_grupo:2,
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
                id_grupo:2,
                grid:true,
                form:true
            },

            //items para el tipo de faccom

            {
                config:{
                    name: 'id_venta',
                    fieldLabel: 'Documento',
                    allowBlank: false,
                    emptyText:'Elija una plantilla...',
                    store:new Ext.data.JsonStore(
                        {
                            url: '../../sis_ventas_facturacion/control/Venta/listarVenta',
                            id: 'id_venta',
                            root:'datos',
                            sortInfo:{
                                field:'nro_factura',
                                direction:'ASC'
                            },
                            totalProperty:'total',
                            fields: [
                                'id_venta',
                                'id_cliente',
                                'id_sucursal',
                                'id_proceso_wf',
                                'id_estado_wf',
                                'estado_reg',
                                'correlativo_venta',
                                'a_cuenta',
                                'total_venta',
                                'fecha_estimada_entrega',
                                'usuario_ai',
                                'fecha_reg',
                                'id_usuario_reg',
                                'id_usuario_ai',
                                'id_usuario_mod',
                                'fecha_mod',
                                'usr_reg',
                                'usr_mod',
                                'estado',
                                'nombre_factura',
                                'nombre_sucursal',
                                'nit',
                                'id_punto_venta',
                                'nombre_punto_venta',
                                'id_forma_pago',
                                'forma_pago',
                                'monto_forma_pago',
                                'numero_tarjeta',
                                'codigo_tarjeta',
                                'tipo_tarjeta',
                                'porcentaje_descuento',
                                'id_vendedor_medico',
                                'comision',
                                'observaciones',
                                'fecha',
                                'nro_factura',
                                'excento',
                                'cod_control',
                                'id_moneda',
                                'total_venta_msuc',
                                'transporte_fob',
                                'seguros_fob',
                                'otros_fob',
                                'transporte_cif',
                                'seguros_cif',
                                'otros_cif',
                                'tipo_cambio_venta',
                                'desc_moneda',
                                'valor_bruto',
                                'descripcion_bulto',
                                'contabilizable',
                                'hora_estimada_entrega',
                                'forma_pedido',
                                'id_cliente_destino',
                                'cliente_destino',
                            ],
                            remoteSort: true,
                            baseParams:{par_filtro:'nro_factura', tipo_factura:'computarizada'}
                        }),
                    tpl:'<tpl for="."><div class="x-combo-list-item"><p>{nombre_factura},  NIT: {nit}</p><p>{desc_plantilla} </p><p>Doc: {nro_factura} de Fecha: {fecha}</p><p> {importe_doc} {desc_moneda}  </p></div></tpl>',
                    valueField: 'id_venta',
                    hiddenValue: 'id_venta',
                    displayField: 'nombre_factura',
                    gdisplayField:'nombre_factura',
                    listWidth:'280',
                    forceSelection:true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode:'remote',
                    pageSize:20,
                    queryDelay:500,
                    gwidth: 250,
                    minChars:2,

                },
                type:'ComboBox',
                id_grupo: 3,
                grid: false,
                bottom_filter: true,
                form: true
            },
            {
                config:{
                    name: 'id_venta_detalle',
                    fieldLabel: 'Detalle',
                    allowBlank: false,
                    emptyText:'Elija una plantilla...',
                    store:new Ext.data.JsonStore(
                        {
                            url: '../../sis_ventas_facturacion/control/VentaDetalleFacturacion/listarVentaDetalleFacturacion',
                            id: 'id_venta_detalle',
                            root:'datos',
                            sortInfo:{
                                field:'id_venta_detalle',
                                direction:'ASC'
                            },
                            totalProperty:'total',
                            fields: [
                                'id_venta_detalle',
                                'id_venta',
                                'id_producto',
                                'tipo',
                                'estado_reg',
                                'cantidad',
                                'precio_unitario',
                                'id_usuario_ai',
                                'usuario_ai',
                                'fecha_reg',
                                'id_usuario_reg',
                                'id_usuario_mod',
                                'fecha_mod',
                                'usr_reg',
                                'usr_mod',
                                'precio_total',
                                'nombre_producto',
                                'porcentaje_descuento',
                                'precio_total_sin_descuento',
                                'id_vendedor_medico',
                                'nombre_vendedor_medico',
                                'requiere_descripcion',
                                'descripcion',
                                'bruto',
                                'ley',
                                'kg_fino',
                                'id_unidad_medida',
                                'codigo_unidad_medida',
                                'ruta_foto',
                                'codigo_unidad_cig',
                            ],
                            remoteSort: true,
                            baseParams:{par_filtro:'mon.codigo'}
                        }),
                    tpl:'<tpl for="."><div class="x-combo-list-item"><p>{nombre_producto},  precio Total: {precio_total}</p></div></tpl>',
                    valueField: 'id_venta_detalle',
                    hiddenValue: 'id_venta_detalle',
                    displayField: 'nombre_producto',
                    gdisplayField:'nombre_producto',
                    listWidth:'280',
                    forceSelection:true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode:'remote',
                    pageSize:20,
                    queryDelay:500,
                    gwidth: 250,
                    minChars:2,
                    enableMultiSelect:true,

                },
                type:'AwesomeCombo',
                id_grupo: 3,
                grid: false,
                form: true
            },
            {
                config:{
                    name: 'nro_aut',
                    fieldLabel: 'Nro Autorizacion',
                    allowBlank: true,
                    width: 200,
                    gwidth: 100,
                    maxLength:255,
                    //disabled: true,
                },
                type:'TextField',
                filters:{pfiltro:'liqui.importe_total',type:'string'},
                id_grupo:3,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'nro_fac',
                    fieldLabel: 'Nro Factura',
                    allowBlank: true,
                    width: 200,
                    gwidth: 100,
                    maxLength:255,
                    //disabled: true,
                },
                type:'TextField',
                filters:{pfiltro:'liqui.importe_total',type:'string'},
                id_grupo:3,
                grid:true,
                form:true
            },


        ],
        title: 'Frm solicitud',

        iniciarEventos: function () {
            this.cmpIdTipoDocLiquidacion = this.getComponente('id_tipo_doc_liquidacion');
            this.cmpIdBoleto = this.getComponente('id_boleto');
            this.cmpIdVenta = this.getComponente('id_venta');
            this.cmpIdVentaDetalle = this.getComponente('id_venta_detalle');
            this.cmpTramo_devolucion = this.getComponente('tramo_devolucion');
            this.cmpTramo = this.getComponente('tramo');
            this.cmpImporte_neto = this.getComponente('importe_neto');
            this.cmpImporte_total = this.getComponente('importe_total');
            this.cmpTasas = this.getComponente('tasas');
            this.cmpNombre = this.getComponente('nombre');
            this.cmpMoneda_emision = this.getComponente('moneda_emision');
            this.cmpPunto_venta = this.getComponente('punto_venta');
            this.cmpEstacion = this.getComponente('estacion');
            this.cmp_nro_liquidacion = this.getComponente('nro_liquidacion');


            this.cmpTramo_devolucion.disable();



            this.cmpIdTipoDocLiquidacion.on('select', function (cmp, rec) {

                console.log(cmp)
                console.log(rec)

                switch (rec.json.tipo_documento) {
                    case 'FACCOM':
                        this.ocultarGrupo(1);
                        this.mostrarGrupo(3);
                        break;
                    case 'BOLEMD':
                        this.ocultarGrupo(3);
                        this.mostrarGrupo(1);

                        break;
                    default:
                        console.log('default');
                };

            }, this);

            this.cmpIdVenta.on('select', function (cmp, rec) {

                console.log(cmp)
                console.log(rec)

                this.cmpIdVentaDetalle.reset();
                this.cmpIdVentaDetalle.store.baseParams.id_venta = rec.json.id_venta;
                this.cmpIdVentaDetalle.modificado = true;


                this.cmpIdVentaDetalle.store.load({params:{start:0,limit:10},
                    callback:function(){
                    console.log('llega')

                    }, scope : this
                });



            }, this);



            this.Cmp.estacion.on('select', function (cmp, rec) {

                Ext.Ajax.request({
                    url: '../../sis_devoluciones/control/Liquidacion/obtenerLiquidacionCorrelativo',
                    params: {estacion: rec.json.codigo},
                    success: (resp) => {
                        const data = JSON.parse(resp.responseText);
                        const { f_obtener_correlativo } = data.datos[0];
                        this.cmp_nro_liquidacion.setValue(f_obtener_correlativo);
                    },
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope:   this
                });
            }, this);


          /*  this.cmpEstacion.on('select', function (rec, d) {




            }, this);*/


            this.cmpIdBoleto.on('select', function (rec, d) {

                console.log('llegggaaa')
                this.cmpTramo_devolucion.store.setBaseParam('billete', d.data.nro_boleto);


                this.crearStoreBoletosRecursivo(d.data.nro_boleto);


                this.cmpTramo_devolucion.enable();
                this.cmpTramo_devolucion.reset();
                this.cmpTramo_devolucion.store.baseParams.billete = d.data.nro_boleto;
                this.cmpTramo_devolucion.modificado = true;

                console.log(rec)
                console.log(d)
                /*Ext.Ajax.request({
                    url: '../../sis_devoluciones/control/Liquidacion/getTicketInformation',
                    params: {
                        billete: d.data.nro_boleto,
                    },
                    success: (resp) => {
                        const data = JSON.parse(resp.responseText);
                        console.log('getTicketInformation', data)

                        const boletoInfoJson = data[0];
                        console.log(boletoInfoJson);
                        this.cmpTramo.setValue(boletoInfoJson.itinerary);
                        //this.cmpImporte_neto.setValue(boletoInfoJson.netAmount);
                        //this.cmpImporte_total.setValue(boletoInfoJson.totalAmount);
                        this.cmpTasas.setValue(boletoInfoJson.totalAmount - boletoInfoJson.netAmount);
                        this.cmpNombre.setValue(boletoInfoJson.passengerName);
                        this.cmpMoneda_emision.setValue(boletoInfoJson.currency);
                        this.cmpPunto_venta.setValue(boletoInfoJson.issueOfficeID);


                        // nombre
                        //punto de venta --issueOfficeID
                        // moneda de emision --currency
                        //importe neto
                        //tasas
                        // total

                        // tramo total
                        // tramo utilizado
                        // tramo devolver
                        //

                        //descuento que no figure en la liquidacion

                        //issueOfficeID punto de venda
                        /!*const  = data.datos[0];
                        this.Cmp.nro_liquidacion.setValue(f_obtener_correlativo);*!/
                        /!* const data = JSON.parse(resp)
                         console.log(data)*!/
                    },
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });
                */
                console.log(rec)
                console.log(d)
            }, this);


            this.megrid.initialConfig.columns[1].editor.on('select', function () {
                const val = this.megrid.initialConfig.columns[1].editor.getValue();
                const dataJson = this.megrid.initialConfig.columns[1].editor.store.getById(val);
                console.log(dataJson)
                console.log(dataJson.json.contabilizable)
                this.megrid.initialConfig.columns[2].editor.setValue(dataJson.json.contabilizable);

            }, this);
        },

        obtenerGestion: function (x) {

            var fecha = x.getValue().dateFormat(x.format);
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                // form:this.form.getForm().getEl(),
                url: '../../sis_parametros/control/Gestion/obtenerGestionByFecha',
                params: {fecha: fecha},
                success: this.successGestion,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
        },
        successGestion: function (resp) {
            Phx.CP.loadingHide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            if (!reg.ROOT.error) {

                this.cmpIdGestion.setValue(reg.ROOT.datos.id_gestion);


            } else {

                alert('ocurrio al obtener la gestion')
            }
        },
        onEdit: function () {
            this.cmpFechaSoli.disable();
            this.cmpIdDepto.disable();
            this.Cmp.id_categoria_compra.disable();


            this.Cmp.tipo.disable();
            this.Cmp.tipo_concepto.disable();
            this.Cmp.id_moneda.disable();
            this.Cmp.id_funcionario.store.baseParams.fecha = this.cmpFechaSoli.getValue().dateFormat(this.cmpFechaSoli.format);
            //this.Cmp.fecha_soli.fireEvent('change');

            if (this.Cmp.tipo.getValue() == 'Bien' || this.Cmp.tipo.getValue() == 'Bien - Servicio') {
                this.ocultarComponente(this.Cmp.fecha_inicio);
                this.Cmp.dias_plazo_entrega.allowBlank = false;
            }
            else {
                this.mostrarComponente(this.Cmp.fecha_inicio);
                this.Cmp.dias_plazo_entrega.allowBlank = true;
            }
            this.mostrarComponente(this.Cmp.dias_plazo_entrega);
        },

        onNew: function () {

            this.form.getForm().reset();
            this.loadValoresIniciales();
            if (this.getValidComponente(0)) {
                this.getValidComponente(0).focus(false, 100);
            }


        },

        onSubmit: function (o) {
            //  validar formularios
            var arra = [], i, me = this;
            for (i = 0; i < me.megrid.store.getCount(); i++) {
                record = me.megrid.store.getAt(i);
                arra[i] = record.data;

            }


            me.argumentExtraSubmit = {
                'json_new_records': JSON.stringify(arra, function replacer(key, value) {
                    /*if (typeof value === 'string') {
                     return String(value).replace(/&/g, "%26")
                     }*/
                    return value;
                })
            };

            if (i > 0 && !this.editorDetail.isVisible()) {

                Phx.vista.FormLiquidacion.superclass.onSubmit.call(this, o, undefined, true);

            }
            else {
                alert('no tiene ningun concepto  para comprar')
            }
        },

        successSave: function (resp) {

            Phx.CP.loadingHide();
            var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            this.fireEvent('successsave', this, objRes);

        },

        loadCheckDocumentosSolWf: function (data) {
            //TODO Eventos para cuando ce cierre o destruye la interface de documentos
            Phx.CP.loadWindows('../../../sis_workflow/vista/documento_wf/DocumentoWf.php',
                'Documentos del Proceso',
                {
                    width: '90%',
                    height: 500
                },
                data,
                this.idContenedor,
                'DocumentoWf'
            );

        },



    })
</script>