<?php
/**
 *@package pXP
 *@file gen-Liquidacion.php
 *@author  (admin)
 *@date 17-04-2020 01:54:37
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
#0				17-04-2020				 (admin)				CREACION

 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.Liquidacion=Ext.extend(Phx.gridInterfaz,{

        tipoTabLiqui: 'BOLEMD',
        vista_transferencia:'mandar',
        gruposBarraTareas: [
            {
                name: 'BOLEMD',
                title: '<H1 align="center"><i class="fa fa-file"></i> Boleto</h1>',
                grupo: 0,
                height: 0
            },
            {
                name: 'FACCOM',
                title: '<H1 align="center"><i class="fa fa-file"></i> Factura Com</h1>',
                grupo: 0,
                height: 0
            },
            {
                name: 'FAC-ANTIGUAS',
                title: '<H1 align="center"><i class="fa fa-file"></i> Facturas Antigua</h1>',
                grupo: 0,
                height: 0
            },
            {
                name: 'PORLIQUI',
                title: '<H1 align="center"><i class="fa fa-file"></i> LIQUI X LIQUI</h1>',
                grupo: 0,
                height: 0
            },
            {
                name: 'DEPOSITO',
                title: '<H1 align="center"><i class="fa fa-file"></i> Deposito</h1>',
                grupo: 0,
                height: 0
            },
            {
                name: 'RO',
                title: '<H1 align="center"><i class="fa fa-file"></i> RO</h1>',
                grupo: 0,
                height: 0
            }

        ],


        beditGroups: [0, 1, 2],
        bactGroups: [0, 1, 2],
        btestGroups: [0,1, 2],
        bexcelGroups: [0, 1, 2],



            constructor:function(config){
                this.maestro=config.maestro;
                //llama al constructor de la clase padre
                Phx.vista.Liquidacion.superclass.constructor.call(this,config);



                this.init();
                this.iniciarEventos();

                this.load({params:{start:0, limit:this.tam_pag}})

                this.addButton('ant_estado',{grupo: [0,1,2,3,4,5,6],argument: {estado: 'anterior'},text:'Anterior',iconCls: 'batras',disabled:true,handler:this.antEstado,tooltip: '<b>Pasar al Anterior Estado</b>'});

                this.addButton('sig_estado',{text:'Siguiente',iconCls: 'badelante',disabled:true,handler:this.sigEstado,tooltip: '<b>Pasar al Siguiente Estado</b>',grupo: [0,1,2,3,4,5,6],});



                this.addButton('diagrama_gantt',{text:'Gant',iconCls: 'bgantt',disabled:true,handler:diagramGantt,tooltip: '<b>Diagrama Gantt del proceso</b>',grupo: [0,1,2,3,4,5,6],});
                this.addButton('btnChequeoDocumentosWf',
                    {
                        text: 'Doc. Movimiento',
                        iconCls: 'bchecklist',
                        disabled: true,
                        handler: this.loadCheckDocumentosPlanWf,
                        tooltip: '<b>Documentos de la Solicitud</b><br/>Subir los documetos requeridos en la solicitud seleccionada.',
                        grupo: [0,1,2,3,4,5,6],
                    }
                );

                this.addButton('verLiquidacion', {
                    argument: {imprimir: 'verLiquidacion'},
                    text: '<i class="fa fa-file-text-o fa-2x"></i> Ver Liquidación',/*iconCls:'' ,*/
                    disabled: false,
                    handler: this.verLiquidacion
                });
                this.addButton('Nota Agencia', {
                    argument: {imprimir: 'notaAgencia'},
                    text: '<i class="fa fa-file-text-o fa-2x"></i> Nota Agencia',/*iconCls:'' ,*/
                    disabled: false,
                    handler: this.notaAgencia
                });

                this.addButton('Pagar(Facturacion)', {
                    argument: {imprimir: 'pagarFacturacion'},
                    text: '<i class="fa fa-file-text-o fa-2x"></i> Pagar',/*iconCls:'' ,*/
                    disabled: false,
                    handler: this.pagar
                });

                function diagramGantt(){
                    var data=this.sm.getSelected().data.id_proceso_wf;
                    Phx.CP.loadingShow();
                    Ext.Ajax.request({
                        url:'../../sis_workflow/control/ProcesoWf/diagramaGanttTramite',
                        params:{'id_proceso_wf':data},
                        success:this.successExport,
                        failure: this.conexionFailure,
                        timeout:this.timeout,
                        scope:this
                    });
                }






            },

        getParametrosFiltro: function () {
            this.store.baseParams.tipo_tab_liqui = this.tipoTabLiqui;
        },

        actualizarSegunTab: function (name, indice) {
            console.log(name);

            this.tipoTabLiqui = name;
            this.getParametrosFiltro();
            this.load({params:{start:0, limit:this.tam_pag}});
            //Phx.vista.Liquidacion.superclass.onButtonAct.call(this);


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
                        name: 'datos_basicos_liquidacion',
                        fieldLabel: 'Datos Basicos',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 300,
                        maxLength:255,
                        renderer: function (value, p, record, rowIndex, colIndex){

                           console.log(value)
                           console.log(p)
                           console.log(record)
                            const { json } = record;

                            return  `<div style="vertical-align:middle;">
                            <span style="display: block;"><b>Estado:</b>${json.estado}</span>
                            <span style="display: block;"><b>Nro Liquidacion:</b>${json.nro_liquidacion}</span>
                            <span style="display: block;"><b>Punto de Venta:</b>${json.desc_punto_venta}</span>
                            <span style="display: block;"><b>Tipo Liqui Doc:</b>${json.desc_tipo_documento}</span>
                            <span style="display: block;"><b>Estacion:</b>${json.estacion}</span>
                            <span style="display: block;"><b>${json.nro_nota ? `<i class="fa fa-file"></i>Nro Nota:${json.nro_nota}`: 'No tiene Nota'} - ${json.id_proceso_wf_factura ? `<i class="fa fa-file"></i>Nro Nota:${json.id_proceso_wf_factura}`: 'No tiene Factura'} </span>


                            </div>`;

                        }
                    },
                    type:'TextField',
                    filters:{pfiltro:'liqui.estado',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'datos_por_tipo_liquidacion',
                        fieldLabel: 'Datos Por Tipo Doc',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 300,
                        maxLength:255,
                        renderer: function (value, p, record, rowIndex, colIndex){

                           console.log(value)
                           console.log(p)
                           console.log(record)
                            const { json } = record;
                           const liquiDet = (_desc_liqui_det) => {
                               const res = _desc_liqui_det.reduce((valorAnterior, valorActual, indice, vector)=> {
                                   return `${valorAnterior} <br> cant:${valorActual.cantidad }/${valorActual.desc_ingas }/${valorActual.precio || valorActual.importe }`;
                               },'');
                               return res;
                           }
                            return  `<div style="vertical-align:middle;">
                            <span style="display: block;"><b>Desc Liqui:</b>${json._desc_liqui}</span>
                            <span style="display: block;"><b>Desc Det:</b>${typeof json._desc_liqui_det === 'object' ? liquiDet(json._desc_liqui_det) : json._desc_liqui_det }</span>
                            <span style="display: block;"><b>A Nombre:</b>${json.nombre || json.nombre_factura }</span>
                            <span style="display: block;"><b>Importe Original:</b>${json.importe_original}</span>
                            <span style="display: block;"><b>Importe Total:</b>${json.importe_total}</span>
                            ${json.util > 0 ? `<span style="display: block;"><b>Tramos Utilizados:</b>${json.util}</span>` : ''}
                            ${json.importe_tramo_utilizado > 0 ? `<span style="display: block;"><b>Importe Tramos Utilizados:</b>${json.importe_tramo_utilizado}</span>` : ''}

                            </div>`;





                        }
                    },
                    type:'TextField',
                    filters:{pfiltro:'liqui.estado',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'datos_descuentos',
                        fieldLabel: 'Descuentos y a Devolver',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 300,
                        maxLength:255,
                        renderer: function (value, p, record, rowIndex, colIndex){

                           console.log(value);
                           console.log(p);
                           console.log(record);
                            const { json } = record;


                          /* const descuentos = (descuentos) => {
                               console.log('descuentosss',descuentos)
                               if(descuentos && typeof descuentos == 'object') {
                                   const output = descuentos.map((i) => {
                                       return `<tr><td>${i.tipo}</td><td>${i.desc_ingas}</td><td>${i.importe}</td></tr>`;
                                   });
                                   return output;
                               } else {
                                   return '';
                               }

                           }*/
                           let descuentosTemplate = '';
                            json.descuentos && typeof json.descuentos == 'object' && json.descuentos.forEach((i) => {
                               descuentosTemplate += `<tr><td>${i.tipo}</td><td>${i.desc_ingas}</td><td>${i.importe}</td></tr>`;
                           })


                            return `<div style="vertical-align:middle;"><table style="font-size: 11px;"><tr><th><b>Tipo</b></th><th><b>Desc.</b></th><th><b>Importe.</b></th></tr>${descuentosTemplate}
                            <tr><td colspan="2"><b>Total Descuetos:</b></td><td>-${json.sum_total_descuentos}</td></tr>
                            <tr><td colspan="2"><b>Importe Original:</b></td><td>+${json.importe_total}</td></tr>
                            ${json.importe_tramo_utilizado > 0 ? `<tr><td colspan="2"><b>Importe Tramos Utilizados:</b></td><td>-${json.importe_tramo_utilizado}</td></tr>` : ''}

                            <tr><td colspan="2"><b>Importe a Devolver:</b></td><td><b style="color: green">${json.importe_devolver ? json.importe_devolver :0}</b></td></tr>
                            </table>
                            </div>`;





                        }
                    },
                    type:'TextField',
                    filters:{pfiltro:'liqui.estado',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'estado',
                        fieldLabel: 'estado',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:255
                    },
                    type:'TextField',
                    filters:{pfiltro:'liqui.estado',type:'string'},
                    id_grupo:1,
                    grid:true,
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
                        anchor: '100%',
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
                        anchor: '100%',
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
                        width:300,
                        gdisplayField: 'desc_punto_venta',
                        forceSelection: true,
                        typeAhead: false,
                        lazyRender: true,
                        anchor: '100%',
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
                    config : {
                        name : 'estacion',
                        fieldLabel : 'Estacion',
                        anchor : '90%',
                        tinit : false,
                        allowBlank : false,
                        origen : 'CATALOGO',
                        gdisplayField : 'estacion',


                        gwidth : 200,
                        anchor : '100%',
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
                        anchor: '80%',
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
                    config:{
                        name: 'fecha_liqui',
                        fieldLabel: 'Fecha Liqui',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'liqui.fecha_liqui',type:'date'},
                    id_grupo:1,
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
                        anchor: '100%',
                        gwidth: 150,
                        minChars: 2,
                        renderer : function(value, p, record) {
                            return String.format('{0}', record.data['desc_nro_boleto']);
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 0,
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
                        anchor: '80%',
                        gwidth: 100,
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
                        anchor: '80%',
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
                        anchor: '80%',
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
                        anchor: '80%',
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
                        name: 'tasas',
                        fieldLabel: 'tasas',
                        allowBlank: true,
                        anchor: '80%',
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
                        anchor: '80%',
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
                        name: 'importe_total',
                        fieldLabel: 'Importe Total',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:255,
                        disabled: true,
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
                        anchor: '80%',
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
                        width:250,
                        minChars:2,
                        enableMultiSelect:true,
                    },
                    type:'AwesomeCombo',
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'util',
                        fieldLabel: 'Tramo Utilizado(facturable)',
                        allowBlank: true,
                        anchor: '80%',
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
                        name: 'nro_factura',
                        fieldLabel: 'Nro Factura',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:255
                    },
                    type:'TextField',
                    filters:{pfiltro:'tv.nro_factura',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'nombre_factura',
                        fieldLabel: 'Nombre Factura',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:255
                    },
                    type:'TextField',
                    filters:{pfiltro:'tv.nombre_factura',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },

                {
                    config: {
                        name: 'id_concepto_ingas',
                        fieldLabel: 'Descuentos',
                        allowBlank: true,
                        emptyText: 'Elija una opción...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_parametros/control/ConceptoIngas/listarConceptoIngasMasPartida',
                            id: 'id_concepto_ingas',
                            root: 'datos',
                            sortInfo: {
                                field: 'desc_ingas',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_concepto_ingas', 'desc_ingas'],
                            remoteSort: true,
                            baseParams: {par_filtro: 'conig.id_concepto_ingas#conig.desc_ingas'}
                        }),
                        valueField: 'id_concepto_ingas',
                        displayField: 'desc_ingas',
                        gdisplayField: 'desc_desc_ingas',
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
                            return String.format('{0}', record.data['desc_desc_ingas']);
                        },
                        enableMultiSelect:true,
                    },
                    type:'AwesomeCombo',
                    id_grupo: 0,
                    filters: {pfiltro: 'conig.desc_ingas',type: 'string'},
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
                    filters:{pfiltro:'liqui.estado_reg',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'tipo_de_cambio',
                        fieldLabel: 'tipo de cambio',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:655362
                    },
                    type:'NumberField',
                    filters:{pfiltro:'liqui.tipo_de_cambio',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'descripcion',
                        fieldLabel: 'Descripcion',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:255
                    },
                    type:'TextField',
                    filters:{pfiltro:'liqui.descripcion',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'nombre_cheque',
                        fieldLabel: 'Nombre cheque',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:255
                    },
                    type:'TextField',
                    filters:{pfiltro:'liqui.nombre_cheque',type:'string'},
                    id_grupo:1,
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
                    id_grupo:1,
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
                    id_grupo:1,
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
                    filters:{pfiltro:'liqui.fecha_reg',type:'date'},
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
                    filters:{pfiltro:'liqui.usuario_ai',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'id_usuario_ai',
                        fieldLabel: 'Funcionaro AI',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:4
                    },
                    type:'Field',
                    filters:{pfiltro:'liqui.id_usuario_ai',type:'numeric'},
                    id_grupo:1,
                    grid:false,
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
                    filters:{pfiltro:'liqui.fecha_mod',type:'date'},
                    id_grupo:1,
                    grid:true,
                    form:false
                }
            ],
            tam_pag:50,
            title:'Liquidacion',
            ActSave:'../../sis_devoluciones/control/Liquidacion/insertarLiquidacion',
            ActDel:'../../sis_devoluciones/control/Liquidacion/eliminarLiquidacion',
            ActList:'../../sis_devoluciones/control/Liquidacion/listarLiquidacionJson',
            id_store:'id_liquidacion',
            fields: [
                {name:'id_liquidacion', type: 'numeric'},
                {name:'estacion', type: 'string'},
                {name:'nro_liquidacion', type: 'string'},
                {name:'estado_reg', type: 'string'},
                {name:'tipo_de_cambio', type: 'numeric'},
                {name:'descripcion', type: 'string'},
                {name:'nombre_cheque', type: 'string'},
                {name:'fecha_liqui', type: 'date',dateFormat:'Y-m-d'},
                {name:'tramo_devolucion', type: 'string'},
                {name:'util', type: 'string'},
                {name:'fecha_pago', type: 'date',dateFormat:'Y-m-d'},
                {name:'id_tipo_doc_liquidacion', type: 'numeric'},
                {name:'pv_agt', type: 'string'},
                {name:'noiata', type: 'string'},
                {name:'id_tipo_liquidacion', type: 'numeric'},
              
                {name:'tramo', type: 'string'},
                {name:'nombre', type: 'string'},
                {name:'moneda_liq', type: 'string'},
                {name:'estado', type: 'string'},
                {name:'obs_dba', type: 'string'},
                {name:'cheque', type: 'string'},
                {name:'id_usuario_reg', type: 'numeric'},
                {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'usuario_ai', type: 'string'},
                {name:'id_usuario_ai', type: 'numeric'},
                {name:'id_usuario_mod', type: 'numeric'},
                {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'usr_reg', type: 'string'},
                {name:'usr_mod', type: 'string'},
                'desc_tipo_documento',
                'desc_tipo_liquidacion',
                'desc_nro_boleto',
                'punto_venta',
                'moneda_emision',
                'importe_neto',
                'tasas',
                'importe_total',
                'desc_punto_venta',
                'nro_factura',
                'nombre_factura',
                'id_boleto',
                'id_venta',
                'desc_forma_pago',
                'id_venta_detalle',
                'exento',

            ],
            sortInfo:{
                field: 'id_liquidacion',
                direction: 'ASC'
            },
            bdel:true,
            bsave:true,
            bedit:true,
            tabsouth:
                [{
                    url:'../../../sis_devoluciones/vista/descuento_liquidacion/DescuentoLiquidacion.php',
                    title:'Descuentos Liquidacion',
                    height:'50%',
                    cls:'DescuentoLiquidacion'
                },
                {
                    url:'../../../sis_devoluciones/vista/liqui_forma_pago/LiquiFormaPago.php',
                    title:'Forma de Pago',
                    height:'50%',
                    cls:'LiquiFormaPago'
                }],
            /*tabeast:[
                {
                    url:'../../../sis_devoluciones/vista/descuento_liquidacion/DescuentoLiquidacion.php',
                    title:'Descuentos Liquidacion',
                    width:400,
                    cls:'DescuentoLiquidacion'
                },

            ],*/
            loadCheckDocumentosPlanWf:function() {
                var rec=this.sm.getSelected();
                rec.data.nombreVista = this.nombreVista;
                console.log('RESPUESTA:',rec.data);
                Phx.CP.loadWindows('../../../sis_workflow/vista/documento_wf/DocumentoWf.php',
                    'Chequear documento del WF',
                    {
                        width:'90%',
                        height:500
                    },
                    rec.data,
                    this.idContenedor,
                    'DocumentoWf'
                )
            },
            iniciarEventos : function () {

                this.Cmp.tramo_devolucion.disable();

                this.Cmp.estacion.on('select', function (rec, d) {


                    Ext.Ajax.request({
                        url: '../../sis_devoluciones/control/Liquidacion/obtenerLiquidacionCorrelativo',
                        params: {estacion: d.json.codigo},
                        success: (resp) => {
                            const data = JSON.parse(resp.responseText);
                            const { f_obtener_correlativo } = data.datos[0];
                            this.Cmp.nro_liquidacion.setValue(f_obtener_correlativo);
                        },
                        failure: this.conexionFailure,
                        timeout: this.timeout,
                        scope:   this
                    });

                }, this);



                this.Cmp.id_boleto.on('select', function (rec, d) {

                    this.Cmp.tramo_devolucion.store.setBaseParam('billete', d.data.nro_boleto);


                    this.Cmp.tramo_devolucion.enable();
                    this.Cmp.tramo_devolucion.reset();
                    this.Cmp.tramo_devolucion.store.baseParams.billete = d.data.nro_boleto;
                    this.Cmp.tramo_devolucion.modificado = true;

                    console.log(rec)
                    console.log(d)
                    Ext.Ajax.request({
                        url: '../../sis_devoluciones/control/Liquidacion/getTicketInformation',
                        params: {
                            billete: d.data.nro_boleto,
                        },
                        success: (resp) => {
                            console.log(resp)
                            const data = JSON.parse(resp.responseText);
                            const boletoInfoJson = data[0];
                            console.log(boletoInfoJson);
                            this.Cmp.tramo.setValue(boletoInfoJson.itinerary);
                            this.Cmp.importe_neto.setValue(boletoInfoJson.netAmount);
                            this.Cmp.importe_total.setValue(boletoInfoJson.totalAmount);
                            this.Cmp.tasas.setValue(boletoInfoJson.totalAmount - boletoInfoJson.netAmount);
                            this.Cmp.exento.setValue(boletoInfoJson.exento);
                            this.Cmp.nombre.setValue(boletoInfoJson.passengerName);
                            this.Cmp.moneda_emision.setValue(boletoInfoJson.currency);
                            this.Cmp.punto_venta.setValue(boletoInfoJson.issueOfficeID);


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
                            /*const  = data.datos[0];
                            this.Cmp.nro_liquidacion.setValue(f_obtener_correlativo);*/
                            /* const data = JSON.parse(resp)
                             console.log(data)*/
                        },
                        failure: this.conexionFailure,
                        timeout: this.timeout,
                        scope: this
                    });

                    console.log(rec)
                    console.log(d)
                }, this);
            },

            abrirFormulario: function (tipo, record) {

                var me = this;
                console.log('me',me)
                console.log('record',record)
                me.objSolForm = Phx.CP.loadWindows('../../../sis_devoluciones/vista/liquidacion/FormLiquidacion.php',
                    'Formulario de Liquidacion Edit',
                    {
                        modal: true,
                        width: '90%',
                        height: (me.regitrarDetalle == 'si') ? '100%' : '60%',
                    }, {
                        data: {
                            objPadre: me,
                            datosOriginales: record,
                            tipo_form: 'edit'
                        },
                        regitrarDetalle: me.regitrarDetalle
                    },
                    this.idContenedor,
                    'FormLiquidacion',
                    {
                        config: [{
                            event: 'successsave',
                            delegate: this.onSaveForm,
                        }],
                        scope: this
                    });
            },

            onButtonNew:function(){
                /*this.accionFormulario = 'NEW';
                Phx.vista.Liquidacion.superclass.onButtonNew.call(this);//habilita el boton y se abre*/

                //abrir formulario de solicitud
                var me = this;
                me.objSolForm = Phx.CP.loadWindows('../../../sis_devoluciones/vista/liquidacion/FormLiquidacion.php',
                    'Formulario de Liquidacion',
                    {
                        modal:true,
                        width:'90%',
                        height:'90%'
                    }, {data:{objPadre: me,                     tipo_form: 'new'}
                    },
                    this.idContenedor,
                    'FormLiquidacion',
                    {
                        config:[{
                            event:'successsave',
                            delegate: this.onSaveForm,

                        }],

                        scope:this
                    });


            },
            onButtonEdit: function () {
                const dataSelected = this.sm.getSelected();
                const estado = dataSelected.json.estado;
                console.log('dataSelected',dataSelected)
                if(estado == 'borrador') {
                    this.abrirFormulario('edit', this.sm.getSelected())
                } else {
                    alert('No se puede editar una liquidacion con estado ' + estado)
                }
            },
            onSaveForm: function(form,  objRes){
                var me = this;

                form.panel.destroy()
                me.reload();

            },

            preparaMenu:function(n){
                var tb = Phx.vista.Liquidacion.superclass.preparaMenu.call(this);
                var data = this.getSelectedData();
                var tb = this.tbar;


                //Enable/disable WF buttons by status
                this.getBoton('ant_estado').enable();
                this.getBoton('sig_estado').enable();
                if(data.estado=='borrador'){
                    this.getBoton('ant_estado').disable();
                }

                if(data.estado=='emitido'){
                    this.getBoton('ant_estado').disable();
                    this.getBoton('sig_estado').disable();
                }


                return tb;
            },

            antEstado:function(){
                var rec=this.sm.getSelected();
                Phx.CP.loadWindows('../../../sis_workflow/vista/estado_wf/AntFormEstadoWf.php',
                    'Estado de Wf',
                    {
                        modal:true,
                        width:450,
                        height:250
                    }, {data:rec.data}, this.idContenedor,'AntFormEstadoWf',
                    {
                        config:[{
                            event:'beforesave',
                            delegate: this.onAntEstado,
                        }
                        ],
                        scope:this
                    })
            },
            onAntEstado:function(wizard,resp){
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    url:'../../sis_kactivos_fijos/control/Movimiento/anteriorEstadoMovimiento',
                    params:{
                        id_proceso_wf:resp.id_proceso_wf,
                        id_estado_wf:resp.id_estado_wf,
                        obs:resp.obs
                    },
                    argument:{wizard:wizard},
                    success:this.successWizard,
                    failure: this.conexionFailure,
                    timeout:this.timeout,
                    scope:this
                });
            },
            sigEstado:function(){
                var rec=this.sm.getSelected();
                console.log(rec)

                this.objWizard = Phx.CP.loadWindows('../../../sis_workflow/vista/estado_wf/FormEstadoWf.php',
                    'Estado de Wf',
                    {
                        modal:true,
                        width:700,
                        height:450
                    }, {data:{
                            id_estado_wf:rec.json.id_estado_wf,
                            id_proceso_wf:rec.json.id_proceso_wf,
                            fecha_ini:rec.json.fecha_liqui,
                        }}, this.idContenedor,'FormEstadoWf',
                    {
                        config:[{
                            event:'beforesave',
                            delegate: this.onSaveWizard,

                        }],
                        scope:this
                    });
            },
            onSaveWizard:function(wizard,resp){
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    url:'../../sis_devoluciones/control/Liquidacion/siguienteEstadoLiquidacion',
                    params:{
                        id_proceso_wf_act:  resp.id_proceso_wf_act,
                        id_estado_wf_act:   resp.id_estado_wf_act,
                        id_tipo_estado:     resp.id_tipo_estado,
                        id_funcionario_wf:  resp.id_funcionario_wf,
                        id_depto_wf:        resp.id_depto_wf,
                        obs:                resp.obs,
                        json_procesos:      Ext.util.JSON.encode(resp.procesos)
                    },
                    success:this.successWizard,
                    failure: this.conexionFailure,
                    argument:{wizard:wizard},
                    timeout:this.timeout,
                    scope:this
                });
            },
            successWizard:function(resp){
                Phx.CP.loadingHide();
                resp.argument.wizard.panel.destroy()
                this.reload();
            },

            notaAgencia: function () {
                var rec=this.sm.getSelected();
                Phx.CP.loadWindows('../../../sis_devoluciones/vista/nota_agencia/NotaAgencia.php',
                    'Nota Agencia con Liquidacion',
                    {
                        width:'90%',
                        height:500
                    },
                    rec.data,
                    this.idContenedor,
                    'NotaAgencia')
            },
            verLiquidacion : function () {

                var rec = this.sm.getSelected();

                Ext.Ajax.request({
                    url: '../../sis_devoluciones/control/Liquidacion/listarLiquidacionJson',
                    params: {'id_liquidacion': rec.data['id_liquidacion']},
                    success: this.successVistaPrevia,
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });
            },

            pagar : function () {


                var rec = this.sm.getSelected();
                console.log('recccc',rec)
                const find = rec.json.descuentos.find((resq) => resq.tipo === 'BO');
                console.log('find',find);
                if(find === undefined && (rec.json.id_nota != null || rec.json.id_nota != '')) {
                    Phx.CP.loadingShow();
                    Ext.Ajax.request({
                        url: '../../sis_devoluciones/control/Liquidacion/obtenerJsonPagar',
                        params: {'id_liquidacion': rec.data['id_liquidacion']},
                        success: this.successObtenerJsonPagar,
                        failure: this.conexionFailure,
                        timeout: this.timeout,
                        scope: this
                    });
                }


            },
            successObtenerJsonPagar : function (resp) {
                console.log(resp)
                var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                console.log(objRes.ROOT.datos.mensaje);
                let json_para_facturar = JSON.parse(objRes.ROOT.datos.mensaje);


                const json_para_emitir_factura = json_para_facturar.json_para_emitir_factura;

                console.log(json_para_facturar.json_para_emitir_factura);

                Ext.Ajax.request({
                    url: '../../sis_ventas_facturacion/control/FacturacionExterna/insertarVentaFactura',
                    params: {
                        ...json_para_emitir_factura,
                        json_venta_detalle: JSON.stringify(json_para_emitir_factura.json_venta_detalle)
                    },
                    success: this.successPagar,
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });


            },
            successPagar: function (resp) {
                console.log(resp.responseText)
                var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));

                console.log('objRes', objRes.ROOT.datos.id_proceso_wf)

                Ext.Ajax.request({
                    url: '../../sis_ventas_facturacion/control/ImprimirFacturaNotasDebCre/imprimirFacturaNotasDebCre',
                    params: {
                        id_proceso_wf :objRes.ROOT.datos.id_proceso_wf,
                    },
                    success: this.successExportHtmlFactura,
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });

                Phx.CP.loadingHide();
            },
        successExportHtmlFactura: function (resp) {
            var objRes = resp.responseText;
            var wnd = window.open("about:blank", "", "_blank");
            wnd.document.write(objRes);

        },


            successVistaPrevia: function (resp) {

                const objRes = JSON.parse(resp.responseText);
                const liquidacion = objRes.datos[0];
                const {descuentos, descuentos_impuestos_no_reembolsable, notas, liqui_venta_detalle_seleccionados, sum_venta_seleccionados, liqui_forma_pago, sum_total_descuentos} = liquidacion;

                console.log('liquidacion', liquidacion)
                const descuentosPorTipo = descuentos.reduce((valorAnterior, valorActual, indice, vector) => {
                    console.log('valorAnterior',valorAnterior)
                    console.log('valorActual.tipo', valorActual.tipo)
                    console.log(valorActual.tipo in valorAnterior)
                    return {
                        ...valorAnterior,
                        ...((valorActual.tipo in valorAnterior) ?
                            {[valorActual.tipo]: [...valorAnterior[valorActual.tipo], valorActual]}
                            : {[valorActual.tipo]: [valorActual]}),

                    }
                }, {});
                console.log('descuentosPorTipo', descuentosPorTipo)
                let sum_descuentos_impuestos_no_reembolsable = 0;
                let sum_descuentos = 0;


                const htmlPreview = `
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;">
  <link rel="stylesheet" href="../../../sis_devoluciones/control/print.css" type="text/css" media="print" charset="utf-8">

</head>
<body  style="line-height: 18px; font-size: 14pt;">
<table width="100%" style=" font-size: 12px; letter-spacing: 1px;">
    <tr>
        <td>
            <table width="100%" align="center" >
                <tr>
                    <td width="20%">
                        BOLIVIANA DE AVIACION

                        (BOA)
                        <br>
                        COCHABAMBA-BOLIVIA
                    </td>
                    <td align="center" width="60%" style="letter-spacing: 3px;">LIQUIDACION POR DEVOLUCION
                        <br>
                        ****** ${liquidacion.estado.toUpperCase()} ******
                    </td>
                    <td width="20%">
                        Nro: ${liquidacion.nro_liquidacion}
                        <br>
                        Fecha: ${liquidacion.fecha_reg}
                        <br>
                        Fecha-Aprob:
                        <br>
                        Fecha-Pago:
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td>
            <table width="100%">
                <tr>
                    <td width="20%">Nombre o Razon Social: </td>
                    <td>${liquidacion.nombre}</td>
                </tr>
                <tr align="left">
                    <td>P-Venta/Agencia: </td>
                    <td>${liquidacion.punto_venta} ${liquidacion.estacion}</td>
                </tr>
                <tr align="left">
                    <td >AGT-NO-IATA</td>
                    <td >${liquidacion.noiata}</td>
                </tr>
                <tr align="left">
                    <td colspan="2">${liquidacion.descripcion}</td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td>
            <table width="100%">
                <tr>
                    <td align="center">Punto Devolucion</td>
                    <td align="center">T/C</td>
                    <td align="center">Est-Pago</td>
                    <td align="center">Moneda</td>
                </tr>
                <tr>
                    <td>${liquidacion.punto_venta}</td>
                    <td align="center">6.9600</td>
                    <td align="center">${liquidacion.estacion}</td>
                    <td align="center">Bolivianos</td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td>
            <hr>
        </td>
    </tr>
    <tr>
        <td>
            <table width="100%">
                <tr>
                    <td align="center" width="80%">DETALLE</td>
                    <td width="10%">PARCIALES</td>
                    <td width="10%">TOTALES</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <hr>
        </td>
    </tr>
    <tr>
        <td>
            <table width="100%">
                <tr>
                    <td width="80%" colspan="2">
                        ${ liquidacion.desc_tipo_documento === 'BOLEMD' ? (`BOLETO: ${liquidacion.data_boleto.nro_boleto} ${liquidacion.data_boleto.fecha_emision}`) : `` }
                        ${ liquidacion.desc_tipo_documento === 'FACCOM' ? (`FACTURA COMPUTARIZADA: ${liquidacion.nombre_factura} / ${liquidacion.nro_factura} / ${liquidacion.fecha_factura}`) : ``}
                    </td>
                    <td width="10%"></td>
                    <td width="10%"></td>
                </tr>
                 ${ liquidacion.desc_tipo_documento === 'BOLEMD' ? (`
                <tr>
                    <td width="80%" colspan="2">P-VENTA/AGENCIA: ${liquidacion.noiata} ${''} ${liquidacion.punto_venta}
                    </td>
                    <td width="10%" >${ String.format('{0}', Ext.util.Format.number(liquidacion.importe_total, '0,000.00'))}</td>
                    <td width="10%" align="right"></td>
                </tr>
                <tr>
                    <td width="80%" colspan="2">Tramos Utilizados: ${liquidacion.util}</td>
                    <td width="10%">${ String.format('{0}', Ext.util.Format.number(liquidacion.importe_tramo_utilizado, '0,000.00'))}</td>
                    <td width="10%"></td>
                </tr>
                <tr>
                    <td width="60%"></td>
                    <td width="20%">TOTAL A DEVOLVER</td>
                    <td width="10%" ></td>
                    <td width="10%" align="right">${ String.format('{0}', Ext.util.Format.number(liquidacion.importe_devolver_sin_descuentos, '0,000.00'))}</td>
                </tr>
                <tr>
                    <td width="80%" colspan="2">Tramos a Devolver: ${liquidacion.tramo_devolucion}</td>
                    <td width="10%"></td>
                    <td width="10%" align="right"></td>
                </tr>`) : ``}

                 ${ liquidacion.tipo_documento === 'FACCOM' ? (`
                <tr>
                    <td width="80%" colspan="2">
                    </td>
                    <td width="10%"></td>
                    <td width="10%" align="right"></td>
                </tr>

                <tr>
                    <td width="60%">Conceptos a Devolver: </td>
                    <td width="20%"></td>
                    <td width="10%" >Monto</td>
                    <td width="10%" align="right"></td>
                </tr>
                    ${liqui_venta_detalle_seleccionados.map((detalleSeleccionado)=> (`
                        <tr>
                            <td width="80%" colspan="2" align="left">${detalleSeleccionado.desc_ingas}</td>
                            <td width="10%" >${ String.format('{0}', Ext.util.Format.number(detalleSeleccionado.precio, '0,000.00'))}</td>
                           <td width="10%" align="right"></td>
                        </tr>`))};

                <tr>
                    <td width="20%"></td>
                    <td width="60%" align="right" style="letter-spacing: 3px;">TOTAL CONCEPTOS A DEVOLVER BOB:</td>
                    <td width="10%"></td>
                    <td width="10%" align="right">${ String.format('{0}', Ext.util.Format.number(sum_venta_seleccionados, '0,000.00'))}</td>
                </tr>
               `) : ''}



            </table>
        </td>
    </tr>

    <tr>
        <td>
            <table width="100%">
                ${Object.entries(descuentosPorTipo).map(([nameKey, values], index) => {
                    return (
                        ` <tr>
                                <td width="20%">(MENOS)</td>
                                <td width="60%" style="letter-spacing: 3px;">${nameKey}</td>
                                <td width="10%"></td>
                                <td width="10%"></td>
                            </tr>
                            <tr>
                                <td width="20%"></td>
                                <td width="60%" style="letter-spacing: 3px;">-----------------</td>
                                <td width="10%"></td>
                                <td width="10%"></td>
                            </tr>
                            ${values.map((des)=> {
                                return '<tr>'
                                    +'<td width="20%">'+des.codigo+'</td>'
                                    +'<td width="60%">'+des.desc_ingas+'</td>'
                                    +'<td width="10%">'+String.format('{0}', Ext.util.Format.number(des.importe, '0,000.00'))+'</td>'
                                    +'<td width="10%"></td>'
                                    +'</tr>';
                            })}
<tr>
                                <td width="20%"></td>
                                <td width="60%" style="letter-spacing: 3px;">-----------------</td>
                                <td width="10%"></td>
                                <td width="10%"></td>
                            </tr>
                            `
                    )
                })}



                ${descuentos != null ? (`<tr>
                    <td width="20%"></td>
                    <td width="60%" style="letter-spacing: 3px;" align="right">TOTAL DECUENTOS:</td>
                    <td width="10%"></td>
                    <td width="10%" align="right">${ String.format('{0}', Ext.util.Format.number(sum_total_descuentos, '0,000.00')) || 0} </td>
                </tr>`) : ''}


                <tr>
                    <td width="20%"></td>
                    <td width="60%"></td>
                    <td width="10%"></td>
                    <td width="10%" align="right">================</td>
                </tr>
                <tr>
                    <td width="20%"></td>
                    <td width="60%" align="right" style="letter-spacing: 3px;">TOTAL REEMBOLSO BOB:</td>
                    <td width="10%"></td>
                    <td width="10%" align="right">*****${String.format('{0}', Ext.util.Format.number(liquidacion.importe_devolver, '0,000.00')) || String.format('{0}', Ext.util.Format.number(liquidacion.importe_devolver_liquidacion, '0,000.00'))}</td>
                </tr>

            </table>
        </td>
    </tr>
    <tr>
        <td>
            <hr>
        </td>
    </tr>
    <tr>
        <td>
           ---
        </td>
    </tr>

    <tr>
        <td>
           --
        </td>
    </tr>
${notas && notas.map(function (nota) {
                    console.log('nota',nota)
                    return '<tr>'
                        +'<td>Nro Nota : '+nota.nro_nota+'</td>'
                        +'</tr>';
                }).join("")}

    <tr>
<td>
 <table width="100%" style="width: 100%;">
                <tr><td align="center">Forma de Pago:</td></tr>
${liqui_forma_pago.map((forma_pago) => {
                return `<tr><td align="center">${forma_pago.desc_forma_pago_pw === 'CREDIT CARD' ? `${forma_pago.desc_medio_pago_pw} / ${forma_pago.nro_tarjeta} / ${forma_pago.pais}` : `${forma_pago.desc_medio_pago_pw}/${forma_pago.nro_documento_pago}` }</td></tr>`

                }).join("")}
            </table>
</td>
</tr>

     <tr>
        <td>
            <table width="100%" style="width: 100%;">
                <tr><td align="center">Creado por:</td><td align="center">Aprobado por:</td><td align="center">Pagado por:</b></td></tr>
                <tr><td align="center">${liquidacion.usr_reg}</td><td align="center">${liquidacion.aprobado_por || ''}</td><td align="center">${liquidacion.pagado_por || ''}</td></tr>
            </table>
        </td>
    </tr>


</table>
</body>
</html>
        `;

                var myWindow = window.open("", "_blank");
                myWindow.document.write(htmlPreview);


            },

        }
    )
</script>

		