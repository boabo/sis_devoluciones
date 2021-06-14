<?php
/**
 * @package pXP
 * @file    FormGenerarNota.php
 * @author  Favio Figueroa
 * @date    03-06-2021
 */
header("content-type: text/javascript; charset=UTF-8");
?>

<script>

    Phx.vista.FormGenerarNota = Ext.extend(Phx.frmInterfaz, {
        ActSave: '../../sis_devoluciones/control/Liquidacion/insertarNotaDesdeLiquidacion',
        tam_pag: 10,
        //layoutType: 'wizard',
        layout: 'fit',
        autoScroll: false,
        breset: false,
        labelSubmit: '<i class="fa fa-check"></i> Siguiente',
        storeBoletosRecursivo : false,
        storeDatosIniciales: {},

        constructor: function (config) {

            console.log(config.id_liquidacion)

            this.id_liquidacion = config.id_liquidacion;
            this._liqui_nombre_doc_original = config._liqui_nombre_doc_original;
            this.nit = config.nit;
            this.buildComponentesDetalle();
            this.buildDetailGrid();
            this.buildGrupos();


            this.mestore.load()
            Phx.vista.FormGenerarNota.superclass.constructor.call(this, config);
            this.init();
            this.iniciarEventos();

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
            /*var cmb_rec = this.detCmp['id_concepto_ingas'].store.getById(rec.get('id_concepto_ingas'));
            if (cmb_rec) {
                rec.set('desc_concepto_ingas', cmb_rec.get('desc_ingas'));
            }*/

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

        evaluaGrilla: function () {
            //al eliminar si no quedan registros en la grilla desbloquea los requisitos en el maestro
            var count = this.mestore.getCount();
            if (count == 0) {
                //this.bloqueaRequisitos(false);
            }
        },

        buildComponentesDetalle: function () {

            this.detCmp = {
                'concepto': new Ext.form.TextField({
                    name: 'concepto',
                    msgTarget: 'title',
                    fieldLabel: 'concepto',
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
                    minValue: 0,
                    maxLength: 10
                }),
                'exento': new Ext.form.NumberField({
                    name: 'exento',
                    msgTarget: 'title',
                    fieldLabel: 'exento',
                    allowBlank: false,
                    allowDecimals: true,
                    minValue: 1,
                    maxLength: 10
                }),
                'importe_devolver': new Ext.form.NumberField({
                    name: 'importe_devolver',
                    msgTarget: 'title',
                    fieldLabel: 'Importe Devolver',
                    allowBlank: true,
                    allowDecimals: true,
                    minValue: 1,
                    maxLength: 10,
                    enableKeyEvents: true,
                    disabled: true,
                }),




            }


        },
        buildDetailGrid: function () {

            //cantidad,detalle,peso,totalo
            var Items = Ext.data.Record.create([ {
                name: '_id',
                type: 'int'
            }, {
                name: 'concepto',
                type: 'string'
            },
            ]);

            this.mestore = new Ext.data.JsonStore({
                url: '../../sis_devoluciones/control/Liquidacion/listarLiquidacionDocConceptosOriginales',
                id: '_id',
                root: 'datos',
                totalProperty: 'total',
                fields: ['_id', '_concepto', '_cantidad','_importe'], remoteSort: true,
                baseParams: {dir: 'ASC', sort: '_id', limit: '50', start: '0', id_liquidacion: this.id_liquidacion}
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
                        var e = new Items({
                            _id: undefined,
                            _importe: 0,
                            exento: 0,
                        });
                        this.editorDetail.stopEditing();
                        this.mestore.insert(0, e);
                        this.megrid.getView().refresh();
                        this.megrid.getSelectionModel().selectRow(0);
                        this.editorDetail.startEditing(0);
                        this.sw_init_add = true;

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

                        // id: 'cantidad',
                        header: 'Cant.',
                        dataIndex: '_cantidad',
                        width: 60,
                        sortable: true,
                        hidden: false,
                        hideable: false,
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: true,
                            enable: false,
                            enableKeyEvents: true,
                        },
                        summaryType: 'count',

                        summaryRenderer: function (v, params, data) {
                            return ((v === 0 || v > 1) ? '(' + v + ' items)' : '(1 item)');
                        },
                    },

                    {

                        header: '_concepto',
                        dataIndex: '_concepto',

                        align: 'center',
                        width: 200,
                        editor: this.detCmp.concepto
                    },


                    {

                        header: '_importe',
                        dataIndex: '_importe',
                        align: 'center',
                        width: 50,
                        trueText: 'Yes',
                        falseText: 'No',
                        //minValue: 0.001,
                        minValue: 0,
                        summaryType: 'sum',
                        editor: this.detCmp.importe_original
                    },


                    {

                        header: 'exento',
                        dataIndex: 'exento',
                        css: {
                            background: "#ccc",
                        },
                        format: '$0,0.00',
                        align: 'center',
                        width: 50,
                        summaryType: 'sum',
                        trueText: 'Yes',
                        falseText: 'No',
                        //minValue: 0.001,
                        minValue: 0,
                        summaryType: 'sum',
                        editor: this.detCmp.exento
                    },

                    {
                        xtype: 'numbercolumn',
                        header: 'Importe Devolver',
                        dataIndex: 'importe_devolver',

                        format: '$0,0.00',
                        width: 100,
                        sortable: false,
                        summaryType: 'sum',
                        editor: this.detCmp.importe_devolver
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
                        ]
                    },
                    this.megrid
                ]
            }];



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
                config:{
                    name: 'razon_social',
                    fieldLabel: 'Razon Social',
                    allowBlank: false,
                    width:200,
                    gwidth: 100,
                    maxLength:255,
                    disabled: false,
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
                    name: 'nit',
                    fieldLabel: 'Nit',
                    allowBlank: false,
                    width:200,
                    gwidth: 100,
                    maxLength:255,
                    disabled: false,
                },
                type:'TextField',
                filters:{pfiltro:'liqui.nro_liquidacion',type:'string'},
                id_grupo:0,
                grid:true,
                form:true,
                bottom_filter : true

            },
        ],
        title: 'Frm Gen Nota',
        iniciarEventos: function () {


            this.Cmp.id_liquidacion.setValue(this.id_liquidacion);
            this.Cmp.razon_social.setValue(this._liqui_nombre_doc_original);
            this.Cmp.nit.setValue(this.nit);

            this.detCmp.exento.on('blur', function () {
                console.log('llega el blur')
                const importeOriginal = this.detCmp.importe_original.getValue();
                const exento = this.detCmp.exento.getValue();
                const importeDevolver = parseFloat(importeOriginal) - parseFloat(exento);
                this.detCmp.importe_devolver.setValue(importeDevolver);
                console.log('importeDevolver',importeDevolver);

            }, this);
           /* this.megrid.initialConfig.columns[3].editor.on('blur', function () {

                console.log('llega el blur')
            }, this);*/

        },

        onSubmit: function (o) {

            
            if(this.form.getForm().isValid() ) {
                //  validar formularios
                var arra = [], i, me = this;
                for (i = 0; i < me.megrid.store.getCount(); i++) {
                    record = me.megrid.store.getAt(i);
                    arra[i] = record.data;

                }
                console.log('ne',me)
                // creamos los parametros para enviar al backend todo en un json
                const params = me.Componentes.reduce((previus, component) =>  { return {...previus, [component.name]: component.value}  } , { detail_json: arra });
                console.log('params',params)

                me.argumentExtraSubmit = {
                    /*'detail_json': JSON.stringify(arra, function replacer(key, value) {
                        /!*if (typeof value === 'string') {
                         return String(value).replace(/&/g, "%26")
                         }*!/
                        return value;
                    }),*/
                    'params': JSON.stringify(params),
                };


                Phx.vista.FormGenerarNota.superclass.onSubmit.call(this, o, undefined, true);

            }


        },

        successSave: function (resp) {

            Phx.CP.loadingHide();
            var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            console.log('obj desde form nota', objRes)
            this.fireEvent('successSave', this, objRes);

        },


    })
</script>