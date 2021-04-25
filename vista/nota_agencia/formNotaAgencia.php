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

            this.init();


            this.iniciarEventos();

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
                                    title: 'Datos Documento',
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
                                title: ' Datos Nota ',
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
                                title: 'Montos',
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
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_periodo'
                    },
                    type:'Field',
                    form:true
                },
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
                            return String.format('{0}', record.data['desc_depto']);
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
                        name: 'estado_reg',
                        fieldLabel: 'Estado Reg.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:10
                    },
                    type:'TextField',
                    filters:{pfiltro:'notage.estado_reg',type:'string'},
                    id_grupo:0,
                    grid:true,
                    form:false
                },



                {
                    config:{
                        name: 'nroaut',
                        fieldLabel: 'Nro Aut',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:100
                    },
                    type:'TextField',
                    filters:{pfiltro:'notage.nroaut',type:'string'},
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'nrofac',
                        fieldLabel: 'Nro Fac',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:100
                    },
                    type:'TextField',
                    filters:{pfiltro:'notage.nrofac',type:'string'},
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'id_doc_compra_venta',
                        fieldLabel: 'id doc cv',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:8,
                        disabled: true,
                    },
                    type:'TextField',
                    filters:{pfiltro:'notage.nroaut',type:'string'},
                    id_grupo:0,
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
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''},
                        disabled: true,
                    },
                    type:'DateField',
                    filters:{pfiltro:'notage.fecha_fac',type:'date'},
                    id_grupo:0,
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
                        maxLength:255,
                        disabled: true,
                    },
                    type:'TextField',
                    filters:{pfiltro:'notage.codito_control_fac',type:'string'},
                    id_grupo:0,
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
                        maxLength:655362,
                        disabled: true,
                    },
                    type:'NumberField',
                    filters:{pfiltro:'notage.monto_total_fac',type:'numeric'},
                    id_grupo:0,
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
                        maxLength:10,
                        disabled: true,
                    },
                    type:'TextField',
                    filters:{pfiltro:'notage.iva',type:'string'},
                    id_grupo:0,
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
                    id_grupo:0,
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
                    id_grupo:0,
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
                    id_grupo:0,
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
                    id_grupo:0,
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
                    id_grupo:0,
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
                    id_grupo:0,
                    grid:true,
                    form:false
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
                    id_grupo: 1,
                    grid: true,
                    form: true
                },

               /* {
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
                },*/




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
                        maxLength:100
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

                /*{
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
                    id_grupo:2,
                    grid:true,
                    form:true
                },*/

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
                    id_grupo:2,
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
                    id_grupo:2,
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
                    id_grupo:2,
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
                    id_grupo:2,
                    grid:true,
                    form:true
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
                        /*validator: function (v) {
                            return /^0|^([A-Fa-f0-9]{2,2}\-)*[A-Fa-f0-9]{2,2}$/i.test(v) ? true : 'Introducir texto de la forma xx-xx, donde x representa dígitos  hexadecimales  [0-9]ABCDEF.';
                        },*/
                        maskRe: /[0-9ABCDEF/-]+/i,
                        regex: /[0-9ABCDEF/-]+/i
                    },
                    type: 'TextField',
                    id_grupo: 2,
                    form: true
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
                    id_grupo:2,
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
                    id_grupo:2,
                    grid:true,
                    form:true
                },





            ];

            this.Atributos = this.Atributos.concat(me.extraAtributos);

        },
        title: 'Frm solicitud',



        iniciarEventos: function () {


            this.Cmp.nroaut.on('blur', function () {
                const nroFac = this.Cmp.nrofac.getValue();
                const nroAut = this.Cmp.nroaut.getValue();

                this.obtenerDatosFactucom({nroAut: nroAut, nroFac: nroFac});
            }, this);
            this.Cmp.nrofac.on('blur', function () {
                const nroFac = this.Cmp.nrofac.getValue();
                const nroAut = this.Cmp.nroaut.getValue();
                this.obtenerDatosFactucom({nroAut: nroAut, nroFac: nroFac});
            }, this);

            this.Cmp.monto_total.on('blur', function () {
                this.calcularTotales();
            }, this);
            this.Cmp.excento.on('blur', function () {
                this.calcularTotales();
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

            this.Cmp.nit.on('blur', function () {
                const nit = this.Cmp.nit.getValue();

                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    url: '../../sis_devoluciones/control/NotaAgencia/obtenerRazonSocialxNIT',
                    params: {'nit': nit},
                    success: function (resp) {
                        Phx.CP.loadingHide();
                        var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                        console.log(objRes)
                        var data = JSON.parse(objRes.ROOT.datos.mensaje);
                        console.log(data)

                        this.getComponente('razon').setValue(data.razon_social);
                        /*this.getComponente('id_moneda').setValue(1);
                        this.getComponente('id_moneda').setRawValue('Bolivianos');*/

                    },
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });


            }, this);




            /*this.Cmp.nro_autorizacion.on('select', function (cmb, rec, i) {

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
            }, this);*/



        },


        calcularTotales: function () {
            /*
            * monto_total
                excento
                total_devuelto
                credfis
            * */
            const montoTotal = parseFloat(this.Cmp.monto_total.getValue());
            const excento = parseFloat(this.Cmp.excento.getValue());
            const totalDevuelto = montoTotal-excento;
            this.Cmp.total_devuelto.setValue(totalDevuelto);
            this.Cmp.credfis.setValue(totalDevuelto * 0.13);


        },
        obtenerDatosFactucom: function ({nroAut, nroFac}) {

            if(nroAut !== '' && nroFac !== '') {
                Phx.CP.loadingShow();

                Ext.Ajax.request({
                    url: '../../sis_devoluciones/control/NotaAgencia/listarDocumentoJson',
                    params: {
                        'nro_aut': nroAut,
                        'nro_fac': nroFac,
                    },
                    success: function (resp) {

                        console.log(resp)

                        Phx.CP.loadingHide();

                        var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                        const dataJson = JSON.parse(reg.ROOT.datos.mensaje);
                        console.log('dataJson',dataJson)
                        //this.cmpIdBoleto.setValue(reg.datos[0].id_boleto);
                        if(typeof dataJson === 'object') {

                            this.Cmp.id_doc_compra_venta.setValue(dataJson.id_doc_compra_venta);
                            this.Cmp.id_doc_compra_venta.setDisabled(true);

                            this.Cmp.fecha_fac.setValue(dataJson.fecha);
                            this.Cmp.codito_control_fac.setValue(dataJson.codigo_control);
                            this.Cmp.monto_total_fac.setValue(dataJson.importe_doc);
                            this.Cmp.iva.setValue(dataJson.importe_iva);



                        } else {
                            this.Cmp.id_doc_compra_venta.setValue('');
                        }
                        console.log('reg',reg)
                    },
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                })
            }

        },



        onEdit: function () {
            this.Cmp.id_depto_conta.modificado = true;
            this.Cmp.id_doc_compra_venta.modificado = true;
            //this.Cmp.fecha.setReadOnly(false);

            console.log('this.data',this.data)
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

            this.Cmp.monto_total.setValue(0);
            this.Cmp.excento.setValue(0);
            this.Cmp.total_devuelto.setValue(0);
            this.Cmp.credfis.setValue(0);
            this.Cmp.id_moneda.setValue(1);
            this.Cmp.id_moneda.setRawValue('Bolivianos');
            this.Cmp.id_periodo.setValue(this.data.id_periodo);



        },

        onSubmit: function (o) {
            var me = this;


            Phx.vista.FormNotaAgencia.superclass.onSubmit.call(this, o, undefined, true);

        },


        successSave: function (resp) {
            Phx.CP.loadingHide();
            Phx.CP.getPagina(this.idContenedorPadre).reload();
            this.panel.close();
        },



    })
</script>
