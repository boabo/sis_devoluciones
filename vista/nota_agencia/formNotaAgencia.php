<?php
/**
 * @package pXP
 * @file    FormCompraVenta.php
 * @author  Favio Figueroa
 * @date    30-01-2021
 * @description
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.FormNotaAgencia = Ext.extend(Phx.frmInterfaz, {
        ActSave: '../../sis_devoluciones/control/NotaAgencia/insertarNotaAgencia',
        tam_pag: 10,
        tabEnter: true,
        codigoSistema: 'ADQ',
        mostrarFormaPago: true,
        mostrarPartidas: false,
        regitrarDetalle: 'si',
        id_moneda_defecto: 0,  // 0 quiere decir todas las monedas
        //layoutType: 'wizard',
        layout: 'fit',
        autoScroll: false,
        breset: false,
        heightHeader: 290,
        conceptos_eliminados: [],
        parFilConcepto: 'desc_ingas#par.codigo',
        tipo_pres_gasto: 'gasto',
        tipo_pres_recurso: 'recurso',
        aux: '',
        constructor: function (config) {
            this.addEvents('beforesave');
            this.addEvents('successsave');

            Ext.apply(this, config);
            this.generarAtributos();
            this.constructorEtapa2(config);

        },

        constructorEtapa2: function (config) {

            this.buildGrupos();


            Phx.vista.FormNotaAgencia.superclass.constructor.call(this, config);
            alert('llega')

            this.init();


            //this.iniciarEventos();

            if (this.data.tipo_form == 'new') {
                this.onNew();
            }
            else {
                this.onEdit();
            }

        },



        onInitAdd: function () {
            if (this.data.readOnly === true) {
                return false
            }

        },


        onAfterEdit: function (re, o, rec, num) {
            //set descriptins values ...  in combos boxs
            console.log('edit ' + rec);
            var cmb_rec = this.detCmp['id_concepto_ingas'].store.getById(rec.get('id_concepto_ingas'));
            if (cmb_rec) {
                rec.set('desc_concepto_ingas', cmb_rec.get('desc_ingas'));
            }

            var cmb_rec = this.detCmp['id_orden_trabajo'].store.getById(rec.get('id_orden_trabajo'));
            if (cmb_rec) {
                rec.set('desc_orden_trabajo', cmb_rec.get('desc_orden'));
            }

            var cmb_rec = this.detCmp['id_centro_costo'].store.getById(rec.get('id_centro_costo'));
            if (cmb_rec) {
                rec.set('desc_centro_costo', cmb_rec.get('codigo_cc'));
            }

        },





        buildGrupos: function () {
            var me = this;

                me.Grupos = [{
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
                            width: '33%',
                            autoHeight: true,
                            border: true,
                            items: [
                                {
                                    xtype: 'fieldset',
                                    frame: true,
                                    border: false,
                                    layout: 'form',
                                    title: 'Tipo',
                                    width: '100%',

                                    //margins: '0 0 0 5',
                                    padding: '0 0 0 10',
                                    bodyStyle: 'padding-left:5px;',
                                    id_grupo: 0,
                                    items: [],
                                }]
                        },
                        {
                            bodyStyle: 'padding-right:5px;',
                            width: '33%',
                            border: true,
                            autoHeight: true,
                            items: [{
                                xtype: 'fieldset',
                                frame: true,
                                layout: 'form',
                                title: ' Datos básicos ',
                                width: '100%',
                                border: false,
                                //margins: '0 0 0 5',
                                padding: '0 0 0 10',
                                bodyStyle: 'padding-left:5px;',
                                id_grupo: 1,
                                items: [],
                            }]
                        },
                        {
                            bodyStyle: 'padding-right:2px;',
                            width: '33%',
                            border: true,
                            autoHeight: true,
                            items: [{
                                xtype: 'fieldset',
                                frame: true,
                                layout: 'form',
                                title: 'Detalle de pago',
                                width: '100%',
                                border: false,
                                //margins: '0 0 0 5',
                                padding: '0 0 0 10',
                                bodyStyle: 'padding-left:2px;',
                                id_grupo: 2,
                                items: [],
                            }]
                        }
                    ]
                }];


        },

        loadValoresIniciales: function () {

            Phx.vista.FormNotaAgencia.superclass.loadValoresIniciales.call(this);


        },


        extraAtributos: [],
        generarAtributos: function () {
            var me = this;
            this.Atributos = [
                {
                    //configuracion del componente
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_nota_agencia'
                    },
                    type: 'Field',
                    form: true
                },
                {
                    config: {
                        name: 'id_plantilla',
                        fieldLabel: 'Tipo Documento',
                        allowBlank: false,
                        anchor: '85%',
                        emptyText: 'Elija una plantilla...',
                        store: new Ext.data.JsonStore(
                            {
                                url: '../../sis_parametros/control/Plantilla/listarPlantillaFil',
                                id: 'id_plantilla',
                                root: 'datos',
                                sortInfo: {
                                    field: 'desc_plantilla',
                                    direction: 'ASC'
                                },
                                totalProperty: 'total',
                                fields: ['id_plantilla', 'nro_linea', 'desc_plantilla', 'tipo',
                                    'sw_tesoro', 'sw_compro', 'sw_monto_excento', 'sw_descuento',
                                    'sw_autorizacion', 'sw_codigo_control', 'tipo_plantilla', 'sw_nro_dui', 'sw_ic', 'tipo_excento', 'valor_excento', 'sw_qr', 'sw_nit', 'plantilla_qr',
                                    'sw_estacion', 'sw_punto_venta', 'sw_codigo_no_iata'],
                                remoteSort: true,
                                baseParams: {par_filtro: 'plt.desc_plantilla', sw_compro: 'si', sw_tesoro: 'si'}
                            }),
                        tpl: '<tpl for="."><div class="x-combo-list-item"><p>{desc_plantilla}</p></div></tpl>',
                        valueField: 'id_plantilla',
                        hiddenValue: 'id_plantilla',
                        displayField: 'desc_plantilla',
                        gdisplayField: 'desc_plantilla',
                        listWidth: '280',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 20,
                        queryDelay: 500,
                        minChars: 2
                    },
                    type: 'ComboBox',
                    id_grupo: 0,
                    form: true
                },

                {
                    config: {
                        name: 'id_moneda',
                        origen: 'MONEDA',
                        allowBlank: false,
                        //02-09-2019, se comenta poque se tiene que ver las demas monedas para los pagos
                        //baseParams: {id_moneda_defecto: me.id_moneda_defecto},
                        fieldLabel: 'Moneda',
                        gdisplayField: 'desc_moneda',
                        gwidth: 100,
                        anchor: '85%',
                        width: 180
                    },
                    type: 'ComboRec',
                    id_grupo: 0,
                    form: true
                },
                {
                    config: {
                        name: 'nro_autorizacion',
                        fieldLabel: 'Autorización',
                        allowBlank: false,
                        anchor: '85%',
                        emptyText: 'autorización ...',
                        store: new Ext.data.JsonStore(
                            {
                                url: '../../sis_contabilidad/control/DocCompraVenta/listarNroAutorizacion',
                                id: 'nro_autorizacion',
                                root: 'datos',
                                sortInfo: {
                                    field: 'nro_autorizacion',
                                    direction: 'ASC'
                                },
                                totalProperty: 'total',
                                fields: ['nro_autorizacion', 'nit', 'razon_social'],
                                remoteSort: true
                            }),
                        valueField: 'nro_autorizacion',
                        hiddenValue: 'nro_autorizacion',
                        displayField: 'nro_autorizacion',
                        queryParam: 'nro_autorizacion',
                        listWidth: '280',
                        forceSelection: false,
                        autoSelect: false,
                        hideTrigger: true,
                        typeAhead: false,
                        typeAheadDelay: 75,
                        lazyRender: false,
                        mode: 'remote',
                        pageSize: 20,
                        width: 180,
                        boxMinWidth: 200,
                        queryDelay: 500,
                        minChars: 1,
                        maskRe: /[0-9/-]+/i,
                        regex: /[0-9/-]+/i
                    },
                    type: 'ComboBox',
                    id_grupo: 0,
                    form: true
                },

                {
                    config: {
                        name: 'nit',
                        fieldLabel: 'NIT',
                        qtip: 'Número de indentificación del proveedor',
                        allowBlank: false,
                        emptyText: 'nit ...',
                        store: new Ext.data.JsonStore(
                            {
                                url: '../../sis_contabilidad/control/DocCompraVenta/listarNroNit',
                                id: 'nit',
                                root: 'datos',
                                sortInfo: {
                                    field: 'nit',
                                    direction: 'ASC'
                                },
                                totalProperty: 'total',
                                fields: ['nit', 'razon_social'],
                                remoteSort: true
                            }),
                        valueField: 'nit',
                        hiddenValue: 'nit',
                        displayField: 'nit',
                        gdisplayField: 'nit',
                        queryParam: 'nit',
                        listWidth: '280',
                        forceSelection: false,
                        autoSelect: false,
                        typeAhead: false,
                        typeAheadDelay: 75,
                        hideTrigger: true,
                        triggerAction: 'query',
                        lazyRender: false,
                        mode: 'remote',
                        pageSize: 20,
                        queryDelay: 500,
                        anchor: '85%',
                        minChars: 1
                    },
                    type: 'ComboBox',
                    id_grupo: 0,
                    form: true
                },
                {
                    config: {
                        name: 'id_proveedor',
                        fieldLabel: 'Proveedor',
                        anchor: '85%',
                        tinit: false,
                        allowBlank: true,
                        origen: 'PROVEEDOR',
                        listWidth: '280',
                        resizable: true
                    },
                    type: 'ComboRec',
                    id_grupo: 0,
                    form: true
                },

                {
                    config: {
                        name: 'razon_social',
                        fieldLabel: 'Razón Social (Impuestos)',
                        allowBlank: false,
                        // maskRe: /[A-Za-z0-9 &-. ñ Ñ]/,
                        // fieldStyle: 'text-transform:uppercase',
                        style: 'text-transform:uppercase;',
                        // listeners:{
                        //     'change': function(field, newValue, oldValue){
                        //
                        //         field.suspendEvents(true);
                        //         field.setValue(newValue.toUpperCase());
                        //         field.resumeEvents(true);
                        //     }
                        // },
                        anchor: '85%',
                        maxLength: 180
                    },
                    type: 'TextField',
                    id_grupo: 0,
                    form: true
                },
                {
                    config: {
                        name: 'nro_documento',
                        fieldLabel: 'Nro Factura / Doc',
                        allowBlank: false,
                        anchor: '85%',
                        allowDecimals: false,
                        maxLength: 100
                        // maskRe: /[0-9/-]+/i,
                        // regex: /[0-9/-]+/i


                    },
                    // type:'NumberField',
                    type: 'TextField',
                    id_grupo: 1,
                    form: true
                },
                {
                    config: {
                        name: 'dia',
                        fieldLabel: 'Día',
                        allowBlank: true,
                        allowNEgative: false,
                        allowDecimal: false,
                        anchor: '85%',
                        maxValue: 31,
                        minValue: 1,
                        width: 40
                    },
                    type: 'NumberField',
                    id_grupo: 1,
                    form: true
                },
                {
                    config: {
                        name: 'fecha',
                        fieldLabel: 'Fecha',
                        allowBlank: false,
                        anchor: '85%',
                        format: 'd/m/Y',
                        readOnly: true,
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y') : ''
                        }
                    },
                    type: 'DateField',
                    id_grupo: 1,
                    form: true
                },
                {
                    config: {
                        name: 'fecha_vencimiento',
                        fieldLabel: 'Fecha de Vencimiento de la Deuda',
                        allowBlank: true,
                        anchor: '85%',
                        format: 'd/m/Y',
                        readOnly: true,
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y') : ''
                        }
                    },
                    type: 'DateField',
                    id_grupo: 1,
                    form: true
                },
                {
                    config: {
                        name: 'nro_dui',
                        fieldLabel: 'DUI',
                        allowBlank: true,
                        anchor: '85%',
                        gwidth: 100,
                        maxLength: 16,
                        minLength: 9,
                        listeners: {
                            'change': function (field, newValue, oldValue) {

                                field.suspendEvents(true);
                                field.setValue(newValue.toUpperCase());
                                field.resumeEvents(true);
                            }
                        },
                    },
                    type: 'TextField',
                    id_grupo: 1,
                    form: true
                },
                {
                    config: {
                        name: 'codigo_control',
                        fieldLabel: 'Código de Control',
                        allowBlank: true,
                        anchor: '85%',
                        gwidth: 100,
                        enableKeyEvents: true,
                        fieldStyle: 'text-transform: uppercase',
                        maxLength: 200,
                        validator: function (v) {
                            return /^0|^([A-Fa-f0-9]{2,2}\-)*[A-Fa-f0-9]{2,2}$/i.test(v) ? true : 'Introducir texto de la forma xx-xx, donde x representa dígitos  hexadecimales  [0-9]ABCDEF.';
                        },
                        maskRe: /[0-9ABCDEF/-]+/i,
                        regex: /[0-9ABCDEF/-]+/i
                    },
                    type: 'TextField',
                    id_grupo: 1,
                    form: true
                },
                {
                    config: {
                        name: 'estacion',
                        fieldLabel: 'Estacion',
                        qtip: 'Estacion donde se encentra el punto de venta y la agencia',
                        allowBlank: true,
                        anchor: '85%',
                        gwidth: 120,
                        typeAhead: true,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'local',
                        store: ['CBB', 'LPB', 'SRZ', 'CIJ', 'TJA', 'POI', 'ORU', 'TDD', 'SRE', 'UYU', 'CCA', 'RIB', 'RBQ', 'GYA', 'BYC']
                    },
                    type: 'ComboBox',
                    id_grupo: 1,
                    filters: {
                        type: 'list',
                        options: ['CBB', 'LPB', 'SRZ', 'CIJ', 'TJA', 'POI', 'ORU', 'TDD', 'SRE', 'UYU', 'CCA', 'RIB', 'RBQ', 'GYA', 'BYC']
                    },
                    grid: true,
                    egrid: true,
                    form: true
                },
                {
                    config: {
                        name: 'id_punto_venta',
                        fieldLabel: 'Punto de Venta/Agencia IATA',
                        allowBlank: true,
                        anchor: '85%',
                        emptyText: 'Elija un punto de venta...',
                        store: new Ext.data.JsonStore(
                            {
                                url: '../../sis_ventas_facturacion/control/PuntoVenta/listarPuntoVenta',
                                id: 'id_punto_venta',
                                root: 'datos',
                                sortInfo: {
                                    field: 'codigo',
                                    direction: 'ASC'
                                },
                                totalProperty: 'total',
                                fields: ['id_punto_venta', 'nombre', 'codigo'],
                                remoteSort: true,
                                baseParams: {par_filtro: 'puve.nombre#puve.codigo'}
                            }),
                        tpl: '<tpl for="."><div class="x-combo-list-item"><p>{nombre}</p><p>{codigo}</p></div></tpl>',
                        valueField: 'id_punto_venta',
                        hiddenValue: 'id_punto_venta',
                        displayField: 'nombre',
                        gdisplayField: 'nombre',
                        listWidth: '280',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 20,
                        queryDelay: 500,
                        minChars: 2
                    },
                    type: 'ComboBox',
                    id_grupo: 1,
                    form: true
                },
                {
                    config: {
                        name: 'id_agencia',
                        fieldLabel: 'Agencia IATA/Agencia No IATA',
                        anchor: '85%',
                        allowBlank: true,
                        emptyText: 'Elija una agencia...',
                        store: new Ext.data.JsonStore(
                            {
                                url: '../../sis_obingresos/control/Agencia/listarAgencia',
                                id: 'id_agencia',
                                root: 'datos',
                                sortInfo: {
                                    field: 'codigo_noiata',
                                    direction: 'ASC'
                                },
                                totalProperty: 'total',
                                fields: ['id_agencia', 'nombre', 'codigo_noiata', 'codigo', 'tipo_agencia', 'codigo_int'],
                                remoteSort: true,
                                baseParams: {
                                    par_filtro: 'age.nombre#age.codigo_noiata#age.codigo#age.tipo_agencia#codigo_int',
                                    tipo_agencia: ''
                                }
                            }),
                        tpl: '<tpl for="."><div class="x-combo-list-item"><p>{nombre}</p><p>Codigo IATA: {codigo}</p><p>Codigo NO IATA: {codigo_noiata}</p><p>OficceId: {codigo_int}</p></div></tpl>',
                        valueField: 'id_agencia',
                        hiddenValue: 'id_agencia',
                        displayField: 'nombre',//codigo_noiata
                        gdisplayField: 'nombre',//codigo_noiata
                        listWidth: '280',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 20,
                        queryDelay: 500,
                        minChars: 2
                    },
                    type: 'ComboBox',
                    id_grupo: 1,
                    form: true
                },
                {
                    config: {
                        name: 'obs',
                        fieldLabel: 'Obs',
                        allowBlank: true,
                        anchor: '85%',
                        gwidth: 100,
                        maxLength: 400
                    },
                    type: 'TextArea',
                    id_grupo: 1,
                    bottom_filter: true,
                    form: true
                },
                {
                    config: {
                        name: 'importe_doc',
                        fieldLabel: 'Monto',
                        allowBlank: false,
                        allowNegative: false,

                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 1179650
                    },
                    type: 'NumberField',
                    id_grupo: 2,
                    form: true
                },
                {
                    config: {
                        name: 'cambio',
                        fieldLabel: 'Tipo de Cambio',
                        allowBlank: false,
                        anchor: '80%',
                        maxLength: 100,
                        allowDecimals: true,
                        decimalPrecision: 15
                    },
                    type: 'NumberField',
                    valorInicial: 1,
                    id_grupo: 2,
                    form: true
                },
                {
                    config: {
                        name: 'importe_descuento',
                        fieldLabel: 'Descuento',
                        allowBlank: true,
                        allowNegative: false,
                        anchor: '80%',
                        gwidth: 100
                    },
                    type: 'NumberField',
                    id_grupo: 2,
                    form: true
                },
                {
                    config: {
                        name: 'importe_neto',
                        qtip: 'Importe del documento menos descuentos, sobre este monto se calcula el iva',
                        fieldLabel: 'Monto Neto',
                        allowBlank: false,
                        allowNegative: false,
                        readOnly: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 1179650
                    },
                    type: 'NumberField',
                    id_grupo: 2,
                    form: true
                },
                {
                    config: {
                        name: 'importe_excento',
                        qtip: 'sobre el importe ento, ¿que monto es exento de impuestos?',
                        fieldLabel: 'Exento',
                        allowNegative: false,
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100
                    },
                    type: 'NumberField',
                    id_grupo: 2,
                    form: true
                },
                {
                    config: {
                        name: 'importe_pendiente',
                        fieldLabel: (me.data.tipoDoc == 'compra') ? 'Cuentas por  Pagar' : 'Cuentas por Cobrar',
                        qtip: 'Usualmente una cuenta pendiente de  cobrar o  pagar, si la cuenta se aplica posterior a la emisión del documento',
                        allowBlank: true,
                        allowNegative: false,
                        anchor: '80%',
                        gwidth: 100
                    },
                    type: 'NumberField',
                    filters: {pfiltro: 'dcv.importe_pendiente', type: 'numeric'},
                    id_grupo: 2,
                    form: true
                },
                {
                    config: {
                        name: 'importe_anticipo',
                        fieldLabel: 'Anticipo',
                        qtip: 'Importe pagado por anticipado al documento',
                        allowBlank: true,
                        allowNegative: false,
                        anchor: '80%'
                    },
                    type: 'NumberField',
                    filters: {pfiltro: 'dcv.importe_anticipo', type: 'numeric'},
                    id_grupo: 2,
                    form: true
                },
                {
                    config: {
                        name: 'importe_retgar',
                        fieldLabel: 'Ret. Garantia',
                        qtip: 'Importe retenido por garantia',
                        allowBlank: true,
                        allowNegative: false,
                        anchor: '80%'
                    },
                    type: 'NumberField',
                    filters: {pfiltro: 'dcv.importe_retgar', type: 'numeric'},
                    id_grupo: 2,
                    form: true
                },
                {
                    config: {
                        sysorigen: 'sis_contabilidad',
                        name: 'id_auxiliar',
                        origen: 'AUXILIAR',
                        readOnly: true,
                        allowBlank: true,
                        fieldLabel: 'Cuenta Corriente',
                        baseParams: {corriente: 'si'},
                        gdisplayField: 'codigo_auxiliar',//mapea al store del grid
                        anchor: '85%',
                        listWidth: 350
                    },
                    type: 'ComboRec',
                    id_grupo: 2,
                    form: true
                },
                {
                    config: {
                        name: 'importe_descuento_ley',
                        fieldLabel: 'Descuentos de Ley',
                        allowBlank: true,
                        readOnly: true,
                        anchor: '80%',
                        allowNegative: false,
                        gwidth: 100
                    },
                    type: 'NumberField',
                    id_grupo: 2,
                    form: true
                },
                {
                    config: {
                        name: 'importe_ice',
                        fieldLabel: 'ICE',
                        allowBlank: true,
                        allowNegative: false,
                        anchor: '80%',
                        gwidth: 100
                    },
                    type: 'NumberField',
                    id_grupo: 2,
                    form: true
                },
                {
                    config: {
                        name: 'importe_iva',
                        fieldLabel: 'IVA',
                        allowBlank: true,
                        readOnly: true,
                        allowNegative: false,
                        anchor: '80%',
                        gwidth: 100
                    },
                    type: 'NumberField',
                    id_grupo: 2,
                    form: true
                },
                {
                    config: {
                        name: 'importe_it',
                        fieldLabel: 'IT',
                        allowBlank: true,
                        allowNegative: false,
                        anchor: '80%',
                        readOnly: true,
                        gwidth: 100
                    },
                    type: 'NumberField',
                    id_grupo: 2,
                    form: true
                },
                {
                    config: {
                        name: 'importe_pago_liquido',
                        fieldLabel: 'Líquido Pagado',
                        allowBlank: true,
                        allowNegative: false,
                        readOnly: true,
                        anchor: '80%',
                        gwidth: 100
                    },
                    type: 'NumberField',
                    id_grupo: 2,
                    form: true
                },
                {
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'new_relation_editable'
                    },
                    type: 'Field',
                    form: true
                },
                {
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'boton_rendicion'
                    },
                    type: 'Field',
                    form: true
                },
                {
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'mod_rev'
                    },
                    type: 'Field',
                    form: true
                }

            ];

            this.Atributos = this.Atributos.concat(me.extraAtributos);

        },
        title: 'Frm solicitud',
        iniciarEventos: function () {


            this.Cmp.nro_autorizacion.on('select', function (cmb, rec, i) {

                if (this.data.tipoDoc == 'compra') {
                    this.Cmp.nit.setValue(rec.data.nit);
                    this.Cmp.razon_social.setValue(rec.data.razon_social);///
                }

            }, this);


            this.Cmp.nro_autorizacion.on('change', function (cmb, newval, oldval) {
                var rec = cmb.getStore().getById(newval)
                if (!rec) {
                    //si el combo no tiene resultado
                    if (cmb.lastQuery) {
                        //y se tiene una consulta anterior( cuando editemos no abra cnsulta anterior)
                        this.Cmp.nit.reset();
                        this.Cmp.razon_social.reset();
                    }
                }
            }, this);


            this.Cmp.nit.on('select', function (cmb, rec, i) {
                this.Cmp.razon_social.setValue(rec.data.razon_social);
            }, this);
            //aparece en razon social segun el proveedor del combo elegido
            this.Cmp.id_proveedor.on('select', function (cmb, rec, i) {
                // this.Cmp.razon_social.setValue(rec.data.desc_proveedor);
                this.Cmp.razon_social.setValue(rec.data.rotulo_comercial);
            }, this);
            //
            this.Cmp.nit.on('change', function (cmb, newval, oldval) {
                var rec = cmb.getStore().getById(newval);
                if (!rec) {
                    //si el combo no tiene resultado
                    if (cmb.lastQuery) {
                        //y se tiene una consulta anterior( cuando editemos no abra cnsulta anterior)
                        this.Cmp.razon_social.reset();
                    }
                }

            }, this);




            this.Cmp.nro_autorizacion.on('change', function (fild, newValue, oldValue) {
                if (newValue[3] == '4' || newValue[3] == '8' || newValue[3] == '6') {
                    this.mostrarComponente(this.Cmp.codigo_control);
                    this.Cmp.codigo_control.allowBlank = false;
                }
                else {
                    this.Cmp.codigo_control.allowBlank = true;
                    this.Cmp.codigo_control.setValue('0');
                    this.ocultarComponente(this.Cmp.codigo_control);

                }
                ;
                this.disableComponentes();
            }, this);

            this.Cmp.codigo_control.on('keyup', function (cmp, e) {
                //inserta guiones en codigo de contorl
                var value = cmp.getValue(), tmp = '', tmp2 = '', sw = 0;
                tmp = value.replace(/-/g, '');
                for (var i = 0; i < tmp.length; i++) {
                    tmp2 = tmp2 + tmp[i];
                    if ((i + 1) % 2 == 0 && i != tmp.length - 1) {
                        tmp2 = tmp2 + '-';
                    }
                }
                cmp.setValue(tmp2.toUpperCase());
            }, this);


            // eventos ffp
            //(may) tipo de cambio solo muestre para la moneda en dolares
            this.Cmp.id_moneda.on('select', function (cmb, rec, i) {
                if (rec.data.id_moneda == 2) {
                    this.mostrarComponente(this.Cmp.tipo_cambio);
                }
                else {
                    this.ocultarComponente(this.Cmp.tipo_cambio);
                    this.Cmp.tipo_cambio.reset();
                    //this.Cmp.tipo_cambio.reset();
                }
            }, this);



        },


        onEdit: function () {
            this.Cmp.nit.modificado = true;
            this.Cmp.nro_autorizacion.modificado = true;
            this.Cmp.fecha.setReadOnly(false);
            this.accionFormulario = 'EDIT';
            if (this.data.datosOriginales) {
                this.loadForm(this.data.datosOriginales);
            }








        },

        onNew: function () {

            this.accionFormulario = 'NEW';
           /* this.Cmp.nit.modificado = true;
            this.Cmp.nro_autorizacion.modificado = true;


            this.Cmp.id_depto_conta.setValue(this.data.id_depto);
            this.Cmp.id_gestion.setValue(this.data.id_gestion);
            this.Cmp.tipo.setValue(this.data.tipoDoc);*/


        },

        onSubmit: function (o) {
            var me = this;
            if (me.regitrarDetalle == 'si') {
                //  validar formularios
                var arra = [], total_det = 0.0, i;
                for (i = 0; i < me.megrid.store.getCount(); i++) {
                    record = me.megrid.store.getAt(i);
                    arra[i] = record.data;
                    total_det = total_det + (record.data.precio_total) * 1

                }

                //si tiene conceptos eliminados es necesari oincluirlos ...


                me.argumentExtraSubmit = {
                    'regitrarDetalle': me.regitrarDetalle,
                    'id_doc_conceto_elis': this.conceptos_eliminados.join(),
                    'json_new_records': JSON.stringify(arra, function replacer(key, value) {
                        if (typeof value === 'string') {
                            return String(value).replace(/&/g, "%26")
                        }
                        return value;
                    })
                };

                if (i > 0 && !this.editorDetail.isVisible()) {

                    if (this.aux != 'Póliza de Importación - DUI') {
                        // importe_pago_liquido
                        if ((total_det.toFixed(2) * 1) == this.Cmp.importe_doc.getValue()) {
                            Phx.vista.FormNotaAgencia.superclass.onSubmit.call(this, o, undefined, true);
                        }
                        else {
                            alert('El total del detalle no cuadra con el total del documento');
                        }

                    } else {


                        if ((total_det.toFixed(2) * 1) == this.Cmp.importe_pago_liquido.getValue()) {
                            Phx.vista.FormNotaAgencia.superclass.onSubmit.call(this, o, undefined, true);
                        }
                        else {
                            alert('El total del detalle no cuadra con el Liquido Pagado');
                        }

                    }
                }
                else {
                    alert('no tiene ningun concepto  en el documento')
                }
            }
            else {
                me.argumentExtraSubmit = {'regitrarDetalle': me.regitrarDetalle};
                Phx.vista.FormNotaAgencia.superclass.onSubmit.call(this, o, undefined, true);
            }
        },


        successSave: function (resp) {
            Phx.CP.loadingHide();
            Phx.CP.getPagina(this.idContenedorPadre).reload();
            this.panel.close();
        },

        checkRelacionConcepto: function (cfg) {
            var me = this;
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_contabilidad/control/DocConcepto/verificarRelacionConcepto',
                params: {
                    id_centro_costo: cfg.id_centro_costo,
                    id_gestion: cfg.id_gestion,
                    id_concepto_ingas: cfg.id_concepto_ingas,
                    relacion: me.data.tipoDoc
                },
                success: function (resp) {
                    Phx.CP.loadingHide();
                    var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));

                },
                failure: function (resp) {

                    this.conexionFailure(resp);
                    Phx.CP.loadingHide();
                },
                timeout: this.timeout,
                scope: this
            });

        },
        getPlantilla: function (id_plantilla) {
            Phx.CP.loadingShow();

            Ext.Ajax.request({
                // form:this.form.getForm().getEl(),
                url: '../../sis_parametros/control/Plantilla/listarPlantilla',
                params: {id_plantilla: id_plantilla, start: 0, limit: 1},
                success: this.successPlantilla,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });

        },
        successPlantilla: function (resp) {
            Phx.CP.loadingHide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            if (reg.total == 1) {

                this.Cmp.id_plantilla.fireEvent('select', this.Cmp.id_plantilla, {data: reg.datos[0]}, 0);
                this.Cmp.nro_autorizacion.fireEvent('change', this.Cmp.nro_autorizacion, this.data.datosOriginales.data.nro_autorizacion)


            } else {
                alert('error al recuperar la plantilla para editar, actualice su navegador');
            }
        },


        cargarRazonSocial: function (nit) {
            //Busca en la base de datos la razon social en función del NIT digitado. Si Razon social no esta vacío, entonces no hace nada
            if (this.getComponente('razon_social').getValue() == '') {
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    url: '../../sis_contabilidad/control/DocCompraVenta/obtenerRazonSocialxNIT',
                    params: {'nit': this.Cmp.nit.getValue()},
                    success: function (resp) {
                        Phx.CP.loadingHide();
                        var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                        var razonSocial = objRes.ROOT.datos.razon_social;
                        this.getComponente('razon_social').setValue(razonSocial);
                        this.getComponente('id_moneda').setValue(1);
                        this.getComponente('id_moneda').setRawValue('Bolivianos');

                    },
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });
            }

        },
        mensaje_: function (titulo, mensaje) {

            var tipo = 'ext-mb-warning';
            Ext.MessageBox.show({
                title: titulo,
                msg: mensaje,
                buttons: Ext.MessageBox.OK,
                icon: tipo
            })

        },
        controlMiles: function (value) {
            return value.replace(',', "")
        }


    })
</script>
