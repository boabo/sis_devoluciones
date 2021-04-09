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
        storeDatosIniciales: {},


       

        constructor: function (config) {

            //declaracion de eventos
            this.addEvents('beforesave');
            this.addEvents('successsave');

            Ext.apply(this, config);
            this.obtenerDatosIniciales(config);




            //this.onNew();

            //Ext.apply(this, config);




        },


        constructorEtapa2: function (config) {


            if (this.data.tipo_form == 'new') {
                this.buildComponentesDetalle();
                this.buildDetailGrid();
            }

            this.buildComponentesLiquiManDetalle();
            this.buildGrupos();


            Phx.vista.FormLiquidacion.superclass.constructor.call(this, config);
            this.init();
            this.iniciarEventos();
            this.data.tipo_form == 'new' && this.iniciarEventosDetalle();

            console.log(this.data)
            if (this.data.tipo_form == 'new') {
                this.onNew();
                this.ocultarGrupo(1);
                this.ocultarGrupo(3);
            }
            else {
                this.onEdit();
            }

        },

        obtenerTipoDeCambioConFecha: function (fecha_emision, callbackFin) {
            Ext.Ajax.request({
                url: '../../sis_devoluciones/control/Liquidacion/obtenerCambioOficiales',
                params: {
                    codigo: 'conta_partidas', fecha_emision:fecha_emision,
                },
                success: function (resp) {
                    //Phx.CP.loadingHide();
                    var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));

                    if (reg.ROOT.error) {
                        Ext.Msg.alert('Error', 'Error a recuperar la variable global')
                    } else {
                        const mensaje = reg.ROOT.datos.mensaje;
                        if (mensaje == "") {
                            alert('no se puede obtener ninguna moneda oficial para el dia de hoy')

                        } else {
                            const data = JSON.parse(mensaje);
                            this.from_to = data.reduce((valorAnterior, valorActual) => ({...valorAnterior, [valorActual.from_to] : valorActual}), {});
                            this.storeDatosIniciales = {
                                ...this.storeDatosIniciales,
                                monedas: data,
                                cambiosOficiales: this.cambiosOficiales,
                                from_to: this.from_to

                            };
                            callbackFin();
                        }
                    }

                },
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
        },
        obtenerDatosIniciales: function (config) {

            var me = this;
            //Verifica que la fecha y la moneda hayan sido elegidos
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_devoluciones/control/Liquidacion/obtenerCambioOficiales',
                params: {
                    codigo: 'conta_partidas'
                },
                success: function (resp) {
                    Phx.CP.loadingHide();
                    var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                    console.log('reg datos iniciales',reg)

                    if (reg.ROOT.error) {
                        Ext.Msg.alert('Error', 'Error a recuperar la variable global')
                    } else {
                        const mensaje = reg.ROOT.datos.mensaje;
                        if(mensaje == "") {
                            alert('no se puede obtener ninguna moneda oficial para el dia de hoy')
                        } else {
                            const data = JSON.parse(mensaje);
                            console.log('data',data)

                            me.cambiosOficiales = data.reduce((valorAnterior, valorActual) => ({...valorAnterior, [valorActual.codigo_internacional] : valorActual}), {});
                            me.from_to = data.reduce((valorAnterior, valorActual) => ({...valorAnterior, [valorActual.from_to] : valorActual}), {});
                            this.storeDatosIniciales = {
                                ...this.storeDatosIniciales,
                                monedas: data,
                                cambiosOficiales: me.cambiosOficiales,
                                from_to: me.from_to

                            };
                            console.log('this.storeDatosIniciales',this.storeDatosIniciales)

                        }
                        me.constructorEtapa2(config);


                    }
                },
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });


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

                'tipo': new Ext.form.ComboBox({
                    name: 'tipo',
                    fieldLabel: 'Tipo',
                    qtip: 'El tipo de descuento para aplicar a la liquidacion',
                    allowBlank: true,
                    anchor: '85%',
                    gwidth: 120,
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'local',
                    store: ['DESCUENTO', 'IMPUESTO NO REEMBOLSABLE', 'BO'],
                    allowBlank: false,


                }),
                'importe': new Ext.form.NumberField({
                    name: 'importe',
                    msgTarget: 'title',
                    fieldLabel: 'Importe ',
                    allowBlank: false,
                    allowDecimals: true,
                    minValue: 1,
                    maxLength: 10
                }),



            }


        },
        iniciarEventosDetalle: function () {

            this.megrid.initialConfig.columns[1].editor.on('select', function () {
                const val = this.megrid.initialConfig.columns[1].editor.getValue();
                const dataJson = this.megrid.initialConfig.columns[1].editor.store.getById(val);
                console.log(dataJson)
                console.log(dataJson.json.contabilizable)
                this.megrid.initialConfig.columns[2].editor.setValue(dataJson.json.contabilizable);

            }, this);



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
                    'fecha_ini_act', 'fecha_fin_act', 'lista', 'desc_ingas', 'exento', 'excento'
                ], remoteSort: true,
                baseParams: {dir: 'ASC', sort: 'id_concepto_ingas', limit: '50', start: '0'}
            });

            this.editorDetail = new Ext.ux.grid.RowEditor({
                saveText: 'Aceptar',
                name: 'btn_editor'

            });

            this.summary = new Ext.ux.grid.GridSummary();
            // al iniciar la edicion
            this.editorDetail.on('beforeedit', () => {
                console.log('beforeedit')
            }, this);

            //al cancelar la edicion
            this.editorDetail.on('canceledit', this.onCancelAdd, this);

            //al cancelar la edicion
            this.editorDetail.on('validateedit', () => {
                console.log('validateedit');
            }, this);

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
                                tipo: 'DESCUENTO'
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

                        header: 'Tipo',
                        dataIndex: 'tipo',

                        align: 'center',
                        width: 200,
                        editor: this.detCmp.tipo
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
            console.log('this.data.tipo_form',this.data.tipo_form)
            if(this.data.tipo_form == 'new') {
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
                                        },
                                        {
                                            xtype: 'fieldset',
                                            /*frame: true,
                                            border: false,*/
                                            layout: 'form',
                                            title: ' Factura Manual',
                                            //width: '33%',

                                            //margins: '0 0 0 5',
                                            padding: '0 0 0 10',
                                            bodyStyle: 'padding-left:5px;',
                                            id_grupo: 4,
                                            items: [{
                                                xtype:'button',

                                                text:'Agregar datos manuales',
                                                handler: this.onAgregarDatosManuales,
                                                scope:this,
                                                //makes the button 24px high, there is also 'large' for this config
                                                scale: 'medium'
                                            }],
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
                                        title: 'OTROS',
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
            } else {

                this.Grupos = [{
                    xtype: 'fieldset',
                    border: false,
                    split: true,
                    layout: 'column',
                    autoScroll: true,
                    autoHeight: true,
                    collapseFirst: false,
                    collapsible: true,
                    collapseMode: 'mini',
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
                                },
                                {
                                    xtype: 'fieldset',
                                    /*frame: true,
                                    border: false,*/
                                    layout: 'form',
                                    title: ' Factura Manual',
                                    //width: '33%',

                                    //margins: '0 0 0 5',
                                    padding: '0 0 0 10',
                                    bodyStyle: 'padding-left:5px;',
                                    id_grupo: 4,
                                    items: [{
                                        xtype:'button',

                                        text:'Agregar datos manuales',
                                        handler: this.onAgregarDatosManuales,
                                        scope:this,
                                        //makes the button 24px high, there is also 'large' for this config
                                        scale: 'medium'
                                    }],
                                }

                                ]
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
                                title: 'OTROS',
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
                }];

            }



        },
        crearStoreBoletosRecursivo : function (billete) {
            Phx.CP.loadingShow();

            const that = this;
            const tramoDevolucion = this.getComponente('tramo_devolucion');
            const tramoComponente = this.getComponente('tramo');
            const importeNeto = this.getComponente('importe_neto');
            const importeTotalComponente = this.getComponente('importe_total');
            const tasas = this.getComponente('tasas');
            const exento = this.getComponente('exento');
            const nombre = this.getComponente('nombre');
            const monedaEmision = this.getComponente('moneda_emision');
            const puntoVenta = this.getComponente('punto_venta');
            const estacion = this.getComponente('estacion');
            const pvAgt = this.getComponente('pv_agt');
            const noiata = this.getComponente('noiata');
            const payment = this.getComponente('payment');

            console.log('billete123123', billete)


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
                    {name: 'tiene_nota',     type: 'string'},
                    {name: 'iva',     type: 'numeric'},
                    {name: 'iva_contabiliza_no_liquida',     type: 'numeric'},
                    {name: 'exento',     type: 'numeric'},
                    {name: 'concepto_para_nota',     type: 'string'},
                    {name: 'foid',     type: 'string'},
                    {name: 'fecha_emision',     type: 'string'},
                ],
            });
            this.storeBoletosRecursivo.baseParams.billete = billete;
            this.storeBoletosRecursivo.load({
                params: {start: 0, limit: 100},
                callback: function (e,d) {
                    let total = 0;
                    console.log('e',e)
                    e.forEach((data)=> {
                        console.log(data)
                        total = total + data.data.monto;
                    });

                    const { currency } = e[0].json;

                    total = that.convertirImportePorMoneda(total, currency)
                    importeTotalComponente.setValue(total);
                    const netAmount = that.convertirImportePorMoneda(e[0].json.netAmount, currency);
                    const tasasConvertido = total - netAmount;
                    tramoComponente.setValue(e[0].json.itinerary);


                    //importeNeto.setValue(e[0].json.netAmount);
                    importeNeto.setValue(netAmount);

                    tasas.setValue(tasasConvertido);
                    exento.setValue(that.convertirImportePorMoneda(parseFloat(e[0].json.exento), currency));
                    nombre.setValue(e[0].json.passengerName);
                    monedaEmision.setValue(e[0].json.currency);
                    puntoVenta.setValue(e[0].json.issueOfficeID);
                    pvAgt.setValue(e[0].json.issueOfficeID);
                    noiata.setValue(e[0].json.issueAgencyCode);
                    //debemos recorrer todos payments por si hay de un exchange
                    const allPayments = e.reduce((valorAnterior, valorActual) => {
                        const { payment, concilliation } =  valorActual.json;
                        let payments = [...valorAnterior]
                        payment.forEach((p)=> {
                            payments = [...payments,
                                {
                                    code : p.paymentCode,
                                    description: p.paymentDescription,
                                    amount: that.convertirImportePorMoneda(p.paymentAmount, currency),
                                    method_code: p.paymentMethodCode,
                                    reference: p.reference,
                                    ...(p.paymentDescription === 'CREDIT CARD' && concilliation.lenght > 0 ? { administradora: concilliation[0].Formato, comprobante: concilliation[0].AuthorizationCode, lote: concilliation[0].LotNumber, cod_est: concilliation[0].EstablishmentCode } : { administradora:'',comprobante:'' ,lote: '', cod_est: '' } )
                                }
                            ]
                        })

                        return payments;
                    }, []);
                    console.log('allPayments', allPayments)

/*
                    const paymentData = e[0].json.payment;
                    const dataPaymentForInsert = paymentData.reduce((valorAnterior, valorActual) => {

                        const data = [...valorAnterior,
                            {
                                code : valorActual.paymentCode,
                                description: valorActual.paymentDescription,
                                amount: valorActual.paymentAmount,
                                method_code: valorActual.paymentMethodCode,
                                reference: valorActual.reference,
                            }
                        ]
                        return data;
                    }, []);
                    console.log('dataPaymentForInsert',dataPaymentForInsert)*/

                    payment.setValue(JSON.stringify(allPayments, function replacer(key, value) {
                        return value;
                    }));

                    Phx.CP.loadingHide();
                },
            });

        },

        buildComponentesLiquiManDetalle: function () {


            const itemsLiquiMan = Ext.data.Record.create([ {
                name: 'id',
                type: 'int'
            },{
                name: 'concepto_original',
                type: 'string'
            }, {
                name: 'concepto_devolver',
                type: 'string'
            },
            ]);

            this.editorLiquiManDetail = new Ext.ux.grid.RowEditor({
                saveText: 'Aceptar',
                name: 'btn_editor'

            });

            this.summaryLiquiManDet = new Ext.ux.grid.GridSummary();
            // al iniciar la edicion
            this.editorLiquiManDetail.on('beforeedit', this.onInitAdd, this);

            //al cancelar la edicion
            this.editorLiquiManDetail.on('canceledit', (re, save) => {
                if (this.liquimandet_init_add) {
                    this.mestoreLiquiManDetail.remove(this.mestoreLiquiManDetail.getAt(0));
                }

                this.liquimandet_init_add = false;
            }, this);

            //al cancelar la edicion
            this.editorLiquiManDetail.on('validateedit', () => {
                this.liquimandet_init_add = false;
            }, this);

            this.editorLiquiManDetail.on('afteredit', (re, o, rec, num) => {
                console.log('editorLiquiManDetail afteredit', rec );
            }, this);


            this.mestoreLiquiManDetail = new Ext.data.JsonStore({
                //los datos de abajo no son correctos se deberian cambiar si quisieramos traer datos
                url: '../../sis_adquisiciones/control/SolicitudDet/listarSolicitudDet',
                id: 'id',
                root: 'datos',
                totalProperty: 'total',
                fields: ['id_solicitud_det', 'id_centro_costo', 'descripcion', 'precio_unitario',
                    'id_solicitud', 'id_orden_trabajo', 'id_concepto_ingas', 'precio_total', 'cantidad_sol',
                    'desc_centro_costo', 'desc_concepto_ingas', 'desc_orden_trabajo', 'id_activo_fijo',
                    'fecha_ini_act', 'fecha_fin_act', 'lista', 'desc_ingas', 'exento', 'excento'
                ], remoteSort: true,
                baseParams: {dir: 'ASC', sort: 'id_concepto_ingas', limit: '50', start: '0'}
            });


            this.CmpLiquiManDet = {

                'id_medio_pago': new Ext.form.ComboBox({
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
                        baseParams: {par_filtro: 'mppw.name#fp.fop_code', emision:'dev', regional: 'BOL'}
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
                }),
                'administradora': new Ext.form.ComboBox({
                    name: 'administradora',
                    fieldLabel: 'administradora',
                    qtip: 'administradora',
                    allowBlank: true,
                    anchor: '85%',
                    gwidth: 120,
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'local',
                    store: ['LINKSER', 'ATC'],
                    allowBlank: false,


                }),
                'lote': new Ext.form.TextField({
                    name: 'lote',
                    msgTarget: 'title',
                    fieldLabel: 'lote',
                    allowBlank: true,
                    anchor: '80%',
                    maxLength: 1200,
                    disabled: false
                }),
                'comprobante': new Ext.form.TextField({
                    name: 'comprobante',
                    msgTarget: 'title',
                    fieldLabel: 'comprobante',
                    allowBlank: true,
                    anchor: '80%',
                    maxLength: 1200,
                    disabled: false
                }),
                /*'fecha': new Ext.form.TextField({
                    name: 'fecha',
                    msgTarget: 'title',
                    fieldLabel: 'fecha',
                    allowBlank: true,
                    anchor: '80%',
                    maxLength: 1200,
                    disabled: false
                }),*/
                'fecha': new Ext.form.DateField({
                    name: 'fecha',
                    fieldLabel: 'Fecha',
                    allowBlank: false,
                    disabled: false,
                    width: 105,
                    format: 'd/m/Y'

                }),
                'nro_tarjeta': new Ext.form.TextField({
                    name: 'nro_tarjeta',
                    msgTarget: 'title',
                    fieldLabel: 'nro_tarjeta',
                    allowBlank: true,
                    anchor: '80%',
                    maxLength: 1200,
                    disabled: false
                }),
                'concepto_original': new Ext.form.TextField({
                    name: 'concepto_original',
                    msgTarget: 'title',
                    fieldLabel: 'concepto_original',
                    allowBlank: true,
                    anchor: '80%',
                    maxLength: 1200,
                    disabled: false
                }),
                'concepto_devolver': new Ext.form.TextField({
                    name: 'concepto_devolver',
                    msgTarget: 'title',
                    fieldLabel: 'concepto_devolver',
                    allowBlank: true,
                    anchor: '80%',
                    maxLength: 1200,
                    disabled: false
                }),

                'descripcion': new Ext.form.TextField({
                    name: 'descripcion',
                    msgTarget: 'title',
                    fieldLabel: 'descripcion',
                    allowBlank: true,
                    anchor: '80%',
                    maxLength: 1200,
                    disabled: false
                }),

                'importe_original': new Ext.form.NumberField({
                    name: 'importe_original',
                    msgTarget: 'title',
                    fieldLabel: 'Importe Original',
                    allowBlank: false,
                    allowDecimals: true,
                    minValue: 1,
                    maxLength: 10
                }),
                'importe_devolver': new Ext.form.NumberField({
                    name: 'importe_devolver',
                    msgTarget: 'title',
                    fieldLabel: 'Importe Devolver',
                    allowBlank: false,
                    allowDecimals: true,
                    minValue: 1,
                    maxLength: 10
                }),

            }


            this.megridDatosManuales = new Ext.grid.GridPanel({
                layout: 'fit',
                store: this.mestoreLiquiManDetail,
                region: 'center',
                split: true,
                border: false,
                plain: true,
                //autoHeight: true,
                plugins: [this.editorLiquiManDetail, this.summaryLiquiManDet],
                stripeRows: true,
                tbar: [{
                    /*iconCls: 'badd',*/
                    text: '<i class="fa fa-plus-circle fa-lg"></i> Agregar Detalle Manual',
                    scope: this,
                    width: '100',
                    handler: function () {
                        console.log('asdasdsad asdasd ')
                        const id = Math.floor(Math.random() * (1000 - 1 + 1)) + 1;
                        console.log('idddd',id)
                        var e = new itemsLiquiMan({
                            id: id,
                            concepto_original: '',
                            concepto_devolver: ''
                        });
                        this.editorLiquiManDetail.stopEditing();
                        this.mestoreLiquiManDetail.insert(0, e);
                        this.megridDatosManuales.getView().refresh();
                        this.megridDatosManuales.getSelectionModel().selectRow(0);
                        this.editorLiquiManDetail.startEditing(0);
                        this.liquimandet_init_add = true;

                    }
                }, {
                    ref: '../removeBtn',
                    text: '<i class="fa fa-trash fa-lg"></i> Eliminar',
                    scope: this,
                    handler: function () {
                        this.editorLiquiManDetail.stopEditing();
                        var s = this.megridDatosManuales.getSelectionModel().getSelections();
                        for (var i = 0, r; r = s[i]; i++) {
                            this.mestoreLiquiManDetail.remove(r);
                        }
                    }
                }],

                columns: [
                    new Ext.grid.RowNumberer(),
                    {
                        header: 'Medio de Pago',
                        dataIndex: 'id_medio_pago',
                        width: 200,
                        sortable: false,
                        editor: this.CmpLiquiManDet.id_medio_pago
                    },
                    {
                        header: 'administradora',
                        dataIndex: 'administradora',
                        width: 100,
                        sortable: false,
                        editor: this.CmpLiquiManDet.administradora
                    },
                    {
                        header: 'lote',
                        dataIndex: 'lote',
                        width: 100,
                        sortable: false,
                        editor: this.CmpLiquiManDet.lote
                    },
                    {
                        header: 'comprobante',
                        dataIndex: 'comprobante',
                        width: 100,
                        sortable: false,
                        editor: this.CmpLiquiManDet.comprobante
                    },
                    {
                        header: 'fecha',
                        dataIndex: 'fecha',
                        width: 100,
                        sortable: false,
                        editor: this.CmpLiquiManDet.fecha
                    },
                    {
                        header: 'nro_tarjeta',
                        dataIndex: 'nro_tarjeta',
                        width: 100,
                        sortable: false,
                        editor: this.CmpLiquiManDet.nro_tarjeta
                    },
                    {
                        header: 'concepto_original',
                        dataIndex: 'concepto_original',
                        width: 100,
                        sortable: false,
                        editor: this.CmpLiquiManDet.concepto_original
                    },
                    {
                        header: 'concepto_devolver',
                        dataIndex: 'concepto_devolver',
                        width: 100,
                        sortable: false,
                        editor: this.CmpLiquiManDet.concepto_devolver
                    },
                    {
                        header: 'descripcion',
                        dataIndex: 'descripcion',
                        width: 100,
                        sortable: false,
                        editor: this.CmpLiquiManDet.descripcion
                    },

                    {

                        header: 'importe_original',
                        dataIndex: 'importe_original',
                        align: 'center',
                        width: 50,
                        trueText: 'Yes',
                        falseText: 'No',
                        //minValue: 0.001,
                        minValue: 0,
                        summaryType: 'sum',
                        editor: this.CmpLiquiManDet.importe_original
                    },
                    {

                        header: 'importe_devolver',
                        dataIndex: 'importe_devolver',
                        align: 'center',
                        width: 50,
                        trueText: 'Yes',
                        falseText: 'No',
                        //minValue: 0.001,
                        minValue: 0,
                        summaryType: 'sum',
                        editor: this.CmpLiquiManDet.importe_devolver
                    },



                ]
            });


            const that = this;

            const wid = Ext.id();

            this.winLiquiMan = new Ext.Window({
                id: wid,
                layout:'fit',
                width:820,
                height:350,
                modal:true,
                items: this.megridDatosManuales,
                title: 'Liquidacion Manual (LIQUIMAN)',
                buttons: [{
                    text:'Guardar',
                    disabled:false,
                    scope : this,
                    handler : function () {


                        this.mestoreLiquiManDetail.commitChanges();
                        that.megridDatosManuales.store.commitChanges();

                        let total = 0;
                        for(var i = 0; i < that.megridDatosManuales.store.getCount() ;i++) {
                            var fp = that.mestoreLiquiManDetail.getAt(i);
                            console.log('fp',fp)
                            total = total + fp.data.importe_devolver;
                        }
                        this.Cmp.importe_total_devolver_manual.setValue(total);
                        console.log(total)
                        this.winLiquiMan.close();

                    }
                }]
            });



        },
        onAgregarDatosManuales: function () {
            if (!this.Cmp.tipo_manual.getValue()) {
                Ext.Msg.alert('ATENCION', 'Debe seleccionar un tipo manual');
            } else {

                if(this.Cmp.tipo_manual.getValue() === 'ERRORES TARJETA') {
                    this.CmpLiquiManDet.id_medio_pago.setDisabled(false);
                    this.CmpLiquiManDet.administradora.setDisabled(false);
                    this.CmpLiquiManDet.lote.setDisabled(false);
                    this.CmpLiquiManDet.comprobante.setDisabled(false);
                    this.CmpLiquiManDet.fecha.setDisabled(false);
                    this.CmpLiquiManDet.nro_tarjeta.setDisabled(false);


                } else {
                    this.CmpLiquiManDet.id_medio_pago.setDisabled(true);
                    this.CmpLiquiManDet.administradora.setDisabled(true);
                    this.CmpLiquiManDet.lote.setDisabled(true);
                    this.CmpLiquiManDet.comprobante.setDisabled(true);
                    this.CmpLiquiManDet.fecha.setDisabled(true);
                    this.CmpLiquiManDet.nro_tarjeta.setDisabled(true);

                }
                this.winLiquiMan.show();

            }
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
                             width    : 60,
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
                             width    : 130,
                             dataIndex: 'billete',

                         },

                         {
                             header     : 'Monto',
                             flex     : 1,
                             width    : 100,
                             dataIndex: 'monto'
                         },
                         {
                             header     : 'tiene_nota',
                             width    : 100,
                             dataIndex: 'tiene_nota',
                             editable : true,
                             editor: new Ext.form.ComboBox({
                                 name: 'tiene_nota',
                                 fieldLabel: 'tiene_nota',
                                 allowBlank: true,
                                 emptyText:'tiene nota?...',
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
                             header     : 'Concepto Para Nota',
                             flex     : 1,
                             width    : 130,
                             dataIndex: 'concepto_para_nota',
                             editable : true,
                             editor: new Ext.form.TextField({
                                 name: 'concepto_para_nota',
                                 msgTarget: 'title',
                                 fieldLabel: 'concepto_para_nota',
                                 allowBlank: true,
                                 anchor: '80%',
                                 maxLength: 1200,
                                 disabled: false
                             }),

                         },
                         {
                             header     : 'Foid',
                             flex     : 1,
                             width    : 130,
                             dataIndex: 'foid',
                             editable : true,
                             editor: new Ext.form.TextField({
                                 name: 'foid',
                                 msgTarget: 'title',
                                 fieldLabel: 'foid',
                                 allowBlank: true,
                                 anchor: '80%',
                                 maxLength: 1200,
                                 disabled: false
                             }),

                         },
                         {
                             header     : 'fecha_emision',
                             flex     : 1,
                             width    : 130,
                             dataIndex: 'fecha_emision',
                             editable : true,
                             editor: new Ext.form.TextField({
                                 name: 'fecha_emision',
                                 msgTarget: 'title',
                                 fieldLabel: 'fecha_emision',
                                 allowBlank: true,
                                 anchor: '80%',
                                 maxLength: 1200,
                                 disabled: false
                             }),

                         },
                         {
                             header     : 'iva',
                             flex     : 1,
                             width    : 80,
                             dataIndex: 'iva'
                         },
                         {
                             header: 'exento',
                             dataIndex: 'exento',
                             align: 'center',
                             width: 80,
                             trueText: 'Yes',
                             falseText: 'No',
                             //minValue: 0.001,
                             minValue: 0,
                             editor: new Ext.form.NumberField({
                                 name: 'exento',
                                 msgTarget: 'title',
                                 fieldLabel: 'exento ',
                                 allowBlank: false,
                                 allowDecimals: true,
                                 minValue: 1,
                                 maxLength: 10
                             }),
                         },
                         {
                             header: 'Iva contabiliza no liquida',
                             dataIndex: 'iva_contabiliza_no_liquida',
                             align: 'center',
                             width: 180,
                             trueText: 'Yes',
                             falseText: 'No',
                             //minValue: 0.001,
                             minValue: 0,
                             editor: new Ext.form.NumberField({
                                 name: 'iva_contabiliza_no_liquida',
                                 msgTarget: 'title',
                                 fieldLabel: 'Iva contabiliza no liquida ',
                                 allowBlank: false,
                                 allowDecimals: true,
                                 minValue: 1,
                                 maxLength: 10
                             }),
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


                             let total = 0;
                             for(var i = 0; i < this.storeBoletosRecursivo.getTotalCount() ;i++) {
                                 var fp = this.storeBoletosRecursivo.getAt(i);
                                 if (fp.data.seleccionado == 'si') {
                                     console.log('fp.data.currency', fp.data.currency)
                                     total = total + fp.data.monto;
                                     console.log('siiii')
                                 } else {
                                     console.log('nooo')

                                 }
                             }
                             this.cmpImporte_total.setValue(total);
                             console.log(total)
                             win.close();

                         }
                     }]
                 });
                 win.show();
            }
        },

        loadValoresIniciales: function () {
            console.log('this.Cmp.fecha_liqui',this.Cmp.fecha_liqui)
            this.Cmp.fecha_liqui.setValue(new Date())
            this.Cmp.moneda_liq.setValue('BOB');
            this.Cmp.tipo_de_cambio.setValue(this.cambiosOficiales.USD.oficial);
            console.log('asdasdasdad12321',this.cambiosOficiales)

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
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''},
                    disabled: true,
                    //defaultValue: new Date(),
                },
                type:'DateField',
                filters:{pfiltro:'liqui.fecha_liqui',type:'date'},
                id_grupo:0,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'nro_boleto',
                    fieldLabel: 'Nro Boleto',
                    allowBlank: false,
                    width:200,
                    gwidth: 100,
                    maxLength:255,
                },
                type:'TextField',
                filters:{pfiltro:'liqui.nro_liquidacion',type:'string'},
                id_grupo:1,
                grid:true,
                form:true,
                bottom_filter : true

            },
            {
                config:{
                    name: 'id_boleto',
                    fieldLabel: 'Id Boleto',
                    allowBlank: false,
                    width:200,
                    gwidth: 100,
                    maxLength:255,
                    disabled: true,

                },
                type:'TextField',
                filters:{pfiltro:'liqui.nro_liquidacion',type:'string'},
                id_grupo:1,
                grid:true,
                form:true,
                bottom_filter : true

            },
            {
                config: {
                    name: 'id_deposito',
                    fieldLabel: 'Deposito',
                    allowBlank: true,
                    emptyText: 'Elija una opción...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_devoluciones/control/Liquidacion/listarDeposito',
                        id: 'id_deposito',
                        root: 'datos',
                        sortInfo: {
                            field: 'id_deposito',
                            direction: 'desc'
                        },
                        totalProperty: 'total',
                        fields: [
                            'id_deposito',
                                'nro_deposito',
                                'monto_deposito',
                                'fecha',
                                'saldo',
                                'monto_total'
                        ],
                        remoteSort: true,
                        baseParams: {par_filtro: 'td.nro_deposito'}
                    }),
                    valueField: 'id_deposito',
                    displayField: 'nro_deposito',
                    gdisplayField: 'desc_nro_deposito',
                    hiddenName: 'id_deposito',
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
                        return String.format('{0}', record.data['desc_nro_deposito']);
                    }
                },
                type: 'ComboBox',
                id_grupo: 3,
                filters: {pfiltro: 'tb.nro_boleto',type: 'string'},
                grid: true,
                form: true,
                bottom_filter : true
            },
            {
                config:{
                    name: 'importe_total_deposito',
                    fieldLabel: 'Importe Total Deposito',
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
                config: {
                    name: 'id_liquidacion_fk',
                    fieldLabel: 'Liquidacion',
                    allowBlank: true,
                    emptyText: 'Elija una opción...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_devoluciones/control/Liquidacion/listarLiquidacionJson',
                        id: 'id_liquidacion',
                        root: 'datos',
                        sortInfo: {
                            field: 'nro_liquidacion',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                            fields: ['id_liquidacion', 'nro_liquidacion', 'estacion'],
                            // turn on remote sorting
                        remoteSort: true,
                            baseParams: {par_filtro: 'liqui.nro_liquidacion',/* estado: 'pagado',*/ }
                    }),
                    valueField: 'id_liquidacion',
                    displayField: 'nro_liquidacion',
                        tpl: '<tpl for="."><div class="x-combo-list-item"><p><b>Nro Liqui:</b>{nro_liquidacion}</p><p><b><i class="fa fa-university"></i>Estacion:</b>{estacion}</p> </div></tpl>',
                        hiddenName: 'nro_liquidacion',
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
                        return String.format('{0}', record.data['nro_liquidacion']);
                    }
                },
                type: 'ComboBox',
                id_grupo: 3,
                filters: {pfiltro: 'tb.nro_boleto',type: 'string'},
                grid: true,
                form: true,
                bottom_filter : true
            },
            {
                config:{
                    name: 'id_descuento_liquidacion',
                    fieldLabel: 'Detalle Descuentos',
                    allowBlank: false,
                    emptyText:'Elija una plantilla...',
                    store:new Ext.data.JsonStore(
                        {
                            url: '../../sis_devoluciones/control/DescuentoLiquidacion/listarDescuentoLiquidacion',
                            id: 'id_descuento_liquidacion',
                            root:'datos',
                            sortInfo:{
                                field:'id_descuento_liquidacion',
                                direction:'ASC'
                            },
                            totalProperty:'total',
                            fields: [
                                'id_descuento_liquidacion',
                                'contabilizar',
                                'importe',
                                'estado_reg',
                                'id_concepto_ingas',
                                'id_liquidacion',
                                'sobre',
                                'fecha_reg',
                                'usuario_ai',
                                'id_usuario_reg',
                                'id_usuario_ai',
                                'fecha_mod',
                                'id_usuario_mod',
                                'usr_reg',
                                'usr_mod',
                                'desc_desc_ingas'
                            ],
                            remoteSort: true,
                            baseParams:{par_filtro:'tci.desc_ingas'}
                        }),
                    valueField: 'id_descuento_liquidacion',
                    hiddenValue: 'id_descuento_liquidacion',
                    displayField: 'desc_desc_ingas',
                    gdisplayField:'desc_desc_ingas',
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
                    name: 'exento',
                    fieldLabel: 'Exento',
                    allowBlank: true,
                    width: 200,
                    gwidth: 100,
                    maxLength:255,
                    disabled: false,
                },
                type:'TextField',
                filters:{pfiltro:'liqui.exento',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'payment',
                    fieldLabel: 'payment',
                    allowBlank: true,
                    width: 200,
                    gwidth: 100,
                    maxLength:10000,
                    disabled: true,
                },
                type:'TextField',
                filters:{pfiltro:'liqui.payment',type:'string'},
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
                config:{
                    name: 'importe_tramo_utilizado',
                    fieldLabel: 'Importe Tramo Utilizado',
                    allowBlank: true,
                    width: 200,
                    gwidth: 100,
                    maxLength:255,
                    //disabled: true,
                },
                type:'TextField',
                filters:{pfiltro:'liqui.importe_tramo_utilizado',type:'string'},
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
                config: {
                    name: 'id_moneda',
                    fieldLabel: 'Moneda',
                    allowBlank: false,
                    width:150,
                    listWidth:250,
                    resizable:true,
                    style: {
                        background: '#EFFFD6',
                        color: 'red',
                        fontWeight:'bold'
                    },
                    emptyText: 'Moneda a pagar...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_parametros/control/Moneda/listarMoneda',
                        id: 'id_moneda',
                        root: 'datos',
                        sortInfo: {
                            field: 'moneda',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_moneda', 'codigo', 'moneda', 'codigo_internacional'],
                        remoteSort: true,
                        baseParams: {par_filtro: 'moneda.codigo#moneda.codigo_internacional', filtrar: 'si'}
                    }),
                    valueField: 'id_moneda',
                    gdisplayField : 'codigo_internacional',
                    displayField: 'codigo_internacional',
                    hiddenName: 'id_moneda',
                    tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color:green;"><b style="color:black;">Moneda:</b> <b>{moneda}</b></p><p style="color:red;"><b style="color:black;">Código:</b> <b>{codigo_internacional}</b></p></div></tpl>',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    //disabled:true,
                    minChars: 2
                },
                type: 'ComboBox',
                id_grupo: 2,
                form: false
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
                    maxLength:655362,
                    disabled: true,
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
                    name: 'moneda_liq',
                    fieldLabel: 'moneda_liq',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:3,
                    disabled: true,
                },
                type:'TextField',
                filters:{pfiltro:'liqui.moneda_liq',type:'string'},
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
                            baseParams:{par_filtro:'nro_factura', tipo_factura:'computarizada',tipo_usuario:'cajero'}
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
                   // tpl:'<tpl for="."><div class="x-combo-list-item"><p>{nombre_producto},  precio Total: {precio_total}</p></div></tpl>',
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
                grid:false,
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
                grid:false,
                form:true
            },
            {
                config:{
                    name: 'id_factucom',
                    fieldLabel: 'id_factucom',
                    allowBlank: true,
                    width: 200,
                    gwidth: 100,
                    maxLength:255,
                    disabled: true,
                },
                type:'TextField',
                filters:{pfiltro:'liqui.importe_total',type:'string'},
                id_grupo:3,
                grid:false,
                form:true
            },

            {
                config:{
                    name: 'id_factucomcon',
                    fieldLabel: 'Detalle',
                    allowBlank: false,
                    emptyText:'Elija una plantilla...',
                    store:new Ext.data.JsonStore(
                        {
                            url: '../../sis_devoluciones/control/Liquidacion/listarFactucomcon',
                            id: 'id_factucomcon',
                            root:'datos',
                            sortInfo:{
                                field:'id_factucomcon',
                                direction:'ASC'
                            },
                            totalProperty:'total',
                            fields: [
                                'id_factucomcon',
                                'id_factucom',
                                'cantidad',
                                'importe',
                                'preciounit',
                                'concepto',
                            ],
                            remoteSort: true,
                            baseParams:{par_filtro:'mon.codigo'}
                        }),
                    // tpl:'<tpl for="."><div class="x-combo-list-item"><p>{nombre_producto},  precio Total: {precio_total}</p></div></tpl>',
                    valueField: 'id_factucomcon',
                    hiddenValue: 'id_factucomcon',
                    displayField: 'concepto',
                    gdisplayField:'concepto',
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
                config: {
                    name: 'tipo_manual',
                    fieldLabel: 'tipo_manual',
                    allowBlank: true,
                    emptyText: 'Tipo...',
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'local',
                    store: ['ERRORES TARJETA', 'BOLETOS INEXISTENTE', 'RO MANUAL'],
                    width: 200
                },
                type: 'ComboBox',
                id_grupo: 4,
                form: true
            },

            {
                config:{
                    name: 'importe_total_devolver_manual',
                    fieldLabel: 'Importe Total Devolver',
                    allowBlank: true,
                    width: 200,
                    gwidth: 100,
                    maxLength:255,
                    //disabled: true,
                },
                type:'TextField',
                filters:{pfiltro:'liqui.importe_tramo_utilizado',type:'string'},
                id_grupo:4,
                grid:true,
                form:true
            },



        ],
        title: 'Frm solicitud',

        convertirImportePorMoneda: function (importe, moneda) {
            console.log('moneda', moneda)
            console.log('importe', importe)
            let conver;
            switch (moneda) {
                case 'BOB':
                    return importe * 1;
                    break;
                case 'USD':
                    console.log('this.storeDatosIniciales.from_to[USD->BO].oficial',this.storeDatosIniciales.from_to['USD->BO'].oficial)
                    conver = importe * this.storeDatosIniciales.from_to['USD->BO'].oficial;
                    return conver;

                    break;
                case 'EUR':
                    conver = importe * this.storeDatosIniciales.from_to['USD->ES'].oficial;
                    conver = conver * this.storeDatosIniciales.from_to['USD->BO'].oficial;
                    return conver;

                    break;
                default:
                    alert('necesitamos configurar la triangulacion adecuada para esta moneda');
            };
        },
        liquidacionPorDeposito: function () {


            this.ocultarGrupo(1);
            this.ocultarGrupo(4);

            this.mostrarGrupo(3);
            //debemos ocultar los campos de factura que tambien se encuentran en el grupo 3
            this.ocultarComponente(this.Cmp.id_venta);
            this.ocultarComponente(this.Cmp.id_venta_detalle);
            this.ocultarComponente(this.Cmp.id_liquidacion_fk);
            this.ocultarComponente(this.Cmp.id_descuento_liquidacion);

            //ocultar los campos para factura antigua
            this.ocultarComponente(this.Cmp.nro_aut);
            this.ocultarComponente(this.Cmp.nro_fac);
            this.ocultarComponente(this.Cmp.id_factucom);
            this.ocultarComponente(this.Cmp.id_factucomcon);

            this.Cmp.id_venta.reset();
            this.Cmp.id_venta.store.baseParams.tipo_factura = 'recibo';
            this.Cmp.id_venta.modificado = true;

        },
        liquidacionPorFactura: function () {


            this.ocultarGrupo(1);
            this.mostrarGrupo(3);
            this.ocultarGrupo(4);

            //debemos ocultar los campos de factura que tambien se encuentran en el grupo 3
            this.ocultarComponente(this.Cmp.id_deposito);
            this.ocultarComponente(this.Cmp.importe_total_deposito);
            this.ocultarComponente(this.Cmp.id_liquidacion_fk);
            this.ocultarComponente(this.Cmp.id_descuento_liquidacion);

            //ocultar los campos para factura antigua
            this.ocultarComponente(this.Cmp.nro_aut);
            this.ocultarComponente(this.Cmp.nro_fac);
            this.ocultarComponente(this.Cmp.id_factucom);
            this.ocultarComponente(this.Cmp.id_factucomcon);

            this.Cmp.id_venta.reset();
            this.Cmp.id_venta.store.baseParams.tipo_factura = 'computarizada';
            this.Cmp.id_venta.modificado = true;
        },
        liquidacionPorRecibo: function () {
            this.ocultarGrupo(1);
            this.mostrarGrupo(3);
            this.ocultarGrupo(4);

            //debemos ocultar los campos de factura que tambien se encuentran en el grupo 3
            this.ocultarComponente(this.Cmp.id_deposito);
            this.ocultarComponente(this.Cmp.importe_total_deposito);
            this.ocultarComponente(this.Cmp.id_liquidacion_fk);
            this.ocultarComponente(this.Cmp.id_descuento_liquidacion);

            //ocultar los campos para factura antigua
            this.ocultarComponente(this.Cmp.nro_aut);
            this.ocultarComponente(this.Cmp.nro_fac);
            this.ocultarComponente(this.Cmp.id_factucom);
            this.ocultarComponente(this.Cmp.id_factucomcon);

            this.Cmp.id_venta.reset();
            this.Cmp.id_venta.store.baseParams.tipo_factura = 'recibo';
            this.Cmp.id_venta.modificado = true;
        },
        liquidacionPorLiquidacion: function () {

            this.ocultarGrupo(1);
            this.mostrarGrupo(3);
            this.ocultarGrupo(4);

            //debemos ocultar los campos de factura que tambien se encuentran en el grupo 3
            this.ocultarComponente(this.Cmp.id_deposito);
            this.ocultarComponente(this.Cmp.importe_total_deposito);
            this.ocultarComponente(this.Cmp.id_venta);
            this.ocultarComponente(this.Cmp.id_venta_detalle);

            //ocultar los campos para factura antigua
            this.ocultarComponente(this.Cmp.nro_aut);
            this.ocultarComponente(this.Cmp.nro_fac);
            this.ocultarComponente(this.Cmp.id_factucom);
            this.ocultarComponente(this.Cmp.id_factucomcon);


        },
        liquidacionPorFacturaAntigua: function () {

            this.ocultarGrupo(1);
            this.ocultarGrupo(4);
            this.mostrarGrupo(3);
            //debemos ocultar los campos de factura que tambien se encuentran en el grupo 3
            this.ocultarComponente(this.Cmp.id_deposito);
            this.ocultarComponente(this.Cmp.importe_total_deposito);
            this.ocultarComponente(this.Cmp.id_venta);
            this.ocultarComponente(this.Cmp.id_venta_detalle);
            this.ocultarComponente(this.Cmp.id_liquidacion_fk);
            this.ocultarComponente(this.Cmp.id_descuento_liquidacion);



        },
        liquidacionManual: function () {

            this.ocultarGrupo(1);
            this.ocultarGrupo(2);
            this.ocultarGrupo(3);
            this.mostrarGrupo(4);




        },

        obtenerDatosFactucom: function ({nroAut, nroFac}) {

            if(nroAut !== '' && nroFac !== '') {
                Ext.Ajax.request({
                    url: '../../sis_devoluciones/control/Liquidacion/listarFactucom',
                    params: {
                        'nro_aut': nroAut,
                        'nro_fac': nroFac,
                    },
                    success: function (resp) {

                        console.log(resp)

                        Phx.CP.loadingHide();

                        var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                        console.log('reg',reg)
                        //this.cmpIdBoleto.setValue(reg.datos[0].id_boleto);
                        if(reg.datos.length > 0) {

                            this.Cmp.id_factucom.setValue(reg.datos[0].id_factucom);
                            this.Cmp.id_factucomcon.setDisabled(false);

                            this.Cmp.id_factucomcon.reset();
                            this.Cmp.id_factucomcon.store.baseParams.id_factucom = reg.datos[0].id_factucom;
                            this.Cmp.id_factucomcon.modificado = true;


                        } else {
                            this.Cmp.id_factucom.setValue('');

                            this.Cmp.id_factucomcon.setDisabled(true);
                            this.Cmp.id_factucomcon.reset();
                            this.Cmp.id_factucomcon.store.baseParams.id_factucom = '';
                            this.Cmp.id_factucomcon.modificado = true;
                        }
                        console.log('reg',reg)
                    },
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                })
            }

        },

        inciarEventosParaTipoLiquidacion: function () {
            this.Cmp.id_liquidacion_fk.on('select', function (cmp, rec) {

                console.log(cmp)
                console.log(rec)

                this.Cmp.id_descuento_liquidacion.reset();
                this.Cmp.id_descuento_liquidacion.store.baseParams.id_liquidacion = rec.json.id_liquidacion;
                this.Cmp.id_descuento_liquidacion.modificado = true;


                /*this.Cmp.id_descuento_liquidacion.store.load({params:{start:0,limit:10},
                    callback:function(){
                        console.log('llega')

                    }, scope : this
                });*/



            }, this);
        },
        iniciarEventos: function () {
            this.cmpIdTipoDocLiquidacion = this.getComponente('id_tipo_doc_liquidacion');
            this.cmpNroBoleto = this.getComponente('nro_boleto');
            this.cmpIdBoleto = this.getComponente('id_boleto');
            this.cmpIdVenta = this.getComponente('id_venta');
            this.cmpIdVentaDetalle = this.getComponente('id_venta_detalle');
            this.cmpTramo_devolucion = this.getComponente('tramo_devolucion');
            this.cmpTramo = this.getComponente('tramo');
            this.cmpImporte_neto = this.getComponente('importe_neto');
            this.cmpImporte_total = this.getComponente('importe_total');
            this.cmpTasas = this.getComponente('tasas');
            this.cmpExento = this.getComponente('exento');
            this.cmpNombre = this.getComponente('nombre');
            this.cmpMoneda_emision = this.getComponente('moneda_emision');
            this.cmpPunto_venta = this.getComponente('punto_venta');
            this.cmpEstacion = this.getComponente('estacion');
            this.cmp_nro_liquidacion = this.getComponente('nro_liquidacion');
            this.cmpFechaLiqui = this.getComponente('fecha_liqui');


            this.cmpTramo_devolucion.disable();

            this.inciarEventosParaTipoLiquidacion();

            this.cmpNroBoleto.on('blur', function () {


                const nro_boleto = this.cmpNroBoleto.getValue();
                console.log(this.cmpNroBoleto.getValue());
                Phx.CP.loadingShow();

                Ext.Ajax.request({
                    url: '../../sis_devoluciones/control/Liquidacion/listarBoleto',
                    params: {
                        'nro_boleto': this.cmpNroBoleto.getValue(),
                    },
                    success: function (resp) {

                        console.log(resp)


                       // Phx.CP.loadingHide();
                        Phx.CP.loadingShow();

                        var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                        this.cmpIdBoleto.setValue(reg.datos[0].id_boleto);
                        if(reg.datos.length > 0) {
                            
                            //obtenemos datos de tipo de cambio para la fecha de emision del boleto
                            const that = this;
                            this.obtenerTipoDeCambioConFecha(reg.datos[0].fecha_emision, () => {
                                that.crearStoreBoletosRecursivo(nro_boleto);

                            });

                            this.cmpTramo_devolucion.store.setBaseParam('billete', nro_boleto);




                            this.cmpTramo_devolucion.enable();
                            this.cmpTramo_devolucion.reset();
                            this.cmpTramo_devolucion.store.baseParams.billete = nro_boleto;
                            this.cmpTramo_devolucion.modificado = true;
                        }
                        console.log('reg',reg)
                    },
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                })




            }, this);

            this.cmpIdTipoDocLiquidacion.on('select', function (cmp, rec) {

                console.log(cmp)
                console.log(rec)

                //grupo 2 es boleto
                //grupo 3 es factura computarizada nuevas
                //grupo 4 es deposito
                switch (rec.json.tipo_documento) {
                    case 'FACCOM':
                        this.liquidacionPorFactura();

                        break;
                    case 'RO': // ES LO MISMO QUE FACTURA SOLO QUE AGREGARA AL DOCUMENTO UNA BANDERA
                        this.liquidacionPorRecibo();

                        break;
                    case 'DEPOSITO':
                        this.liquidacionPorDeposito();


                        break;
                    case 'PORLIQUI':
                        this.liquidacionPorLiquidacion();



                        break;
                    case 'BOLEMD':
                        this.ocultarGrupo(3);
                        this.mostrarGrupo(1);


                        break;
                    case 'FAC-ANTIGUAS':
                        this.liquidacionPorFacturaAntigua();

                        break;
                    case 'LIQUIMAN':
                        this.liquidacionManual();

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

            this.Cmp.nro_aut.on('blur', function () {
                const nroFac = this.Cmp.nro_fac.getValue();
                const nroAut = this.Cmp.nro_aut.getValue();

                this.obtenerDatosFactucom({nroAut: nroAut, nroFac: nroFac});
            }, this);
            this.Cmp.nro_fac.on('blur', function () {
                const nroFac = this.Cmp.nro_fac.getValue();
                const nroAut = this.Cmp.nro_aut.getValue();
                this.obtenerDatosFactucom({nroAut: nroAut, nroFac: nroFac});
            }, this);

          /*  this.cmpEstacion.on('select', function (rec, d) {




            }, this);*/


         /*   this.cmpIdBoleto.on('select', function (rec, d) {

                console.log('llegggaaa')
                this.cmpTramo_devolucion.store.setBaseParam('billete', d.data.nro_boleto);


                this.crearStoreBoletosRecursivo(d.data.nro_boleto);


                this.cmpTramo_devolucion.enable();
                this.cmpTramo_devolucion.reset();
                this.cmpTramo_devolucion.store.baseParams.billete = d.data.nro_boleto;
                this.cmpTramo_devolucion.modificado = true;

                console.log(rec)
                console.log(d)
                /!*Ext.Ajax.request({
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
                *!/
                console.log(rec)
                console.log(d)
            }, this);
*/


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
            console.log('this',this)
            //id_tipo_doc_liquidacion

            if (this.data.datosOriginales) {
                this.loadForm(this.data.datosOriginales);
            }
            console.log(this.data.datosOriginales)

            //necesitamos bloquear algunos componentes
            this.Cmp.id_tipo_doc_liquidacion.setDisabled(true);
            this.Cmp.id_tipo_liquidacion.setDisabled(true);
            this.Cmp.id_punto_venta.setDisabled(true);
            this.Cmp.estacion.setDisabled(true);
            this.Cmp.fecha_liqui.setDisabled(true);



            switch (this.data.datosOriginales.json.desc_tipo_documento) {
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

            this.cmpIdVentaDetalle = this.getComponente('id_venta_detalle');

            this.cmpIdVentaDetalle.reset();
            this.cmpIdVentaDetalle.store.baseParams.id_venta = this.data.datosOriginales.json.id_venta;
            this.cmpIdVentaDetalle.modificado = true;


            this.cmpIdVentaDetalle.store.load({params:{start:0,limit:10},
                callback:function(){
                    this.cmpIdVentaDetalle.setValue(this.data.datosOriginales.json.id_venta_detalle);

                }, scope : this
            });





            /* this.Cmp.id_tipo_doc_liquidacion.disable()
             this.Cmp.id_tipo_doc_liquidacion.setValue(this.data.datosOriginales.id_tipo_doc_liquidacion);

             console.log('this.Cmp.id_tipo_doc_liquidacion',this.Cmp.id_tipo_doc_liquidacion)*/
            /*this.cmpFechaSoli.disable();
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
            this.mostrarComponente(this.Cmp.dias_plazo_entrega);*/
        },

        onNew: function () {

            this.form.getForm().reset();
            this.loadValoresIniciales();
            if (this.getValidComponente(0)) {
                this.getValidComponente(0).focus(false, 100);
            }


        },

        onSubmit: function (o) {

            if(this.data.tipo_form == 'new') {

                //  validar formularios
                var arra = [], i, me = this;
                for (i = 0; i < me.megrid.store.getCount(); i++) {
                    record = me.megrid.store.getAt(i);
                    arra[i] = record.data;

                }


                var arraParaNota = [], i2;
                console.log('this.storeBoletosRecursivo',this.storeBoletosRecursivo)
                if(this.storeBoletosRecursivo) {
                    for (i2 = 0; i2 < this.storeBoletosRecursivo.getCount(); i2++) {
                        record = this.storeBoletosRecursivo.getAt(i2);
                        arraParaNota[i2] = record.data;

                    }
                }

                var arraParaLiquiManual = [], i3;
                if(this.mestoreLiquiManDetail) {
                    for (i3 = 0; i3 < this.mestoreLiquiManDetail.getCount(); i3++) {
                        record = this.mestoreLiquiManDetail.getAt(i3);
                        arraParaLiquiManual[i3] = record.data;

                    }
                }

                console.log('arraParaLiquiManual',arraParaLiquiManual);
                console.log('arraParaNota',arraParaNota);
                me.argumentExtraSubmit = {
                    'json_new_records': JSON.stringify(arra, function replacer(key, value) {
                        /*if (typeof value === 'string') {
                         return String(value).replace(/&/g, "%26")
                         }*/
                        return value;
                    }),
                    'json_data_boletos_recursivo': JSON.stringify(arraParaNota, function replacer(key, value) {
                        /*if (typeof value === 'string') {
                         return String(value).replace(/&/g, "%26")
                         }*/
                        return value;
                    }),
                    'json_data_liqui_manual_det': JSON.stringify(arraParaLiquiManual, function replacer(key, value) {
                        /*if (typeof value === 'string') {
                         return String(value).replace(/&/g, "%26")
                         }*/
                        return value;
                    })
                };

                /*if (i > 0 && !this.editorDetail.isVisible()) {

                    Phx.vista.FormLiquidacion.superclass.onSubmit.call(this, o, undefined, true);

                }
                else {
                    alert('no tiene ningun concepto  para comprar')
                }*/

                Phx.vista.FormLiquidacion.superclass.onSubmit.call(this, o, undefined, true);
                

            } else {
                this.argumentExtraSubmit = {'tipo_form': 'edit'};
                Phx.vista.FormLiquidacion.superclass.onSubmit.call(this, o, undefined, true);
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