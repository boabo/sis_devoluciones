<?php
/**
 *@package pXP
 *@file    KardexItem.php
 *@author  RCM
 *@date    06/07/2013
 *@description Archivo con la interfaz para generación de reporte
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.FormRepNotas = Ext.extend(Phx.frmInterfaz, {

        constructor: function(config) {
            Ext.apply(this,config);
            this.Atributos = [

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
                            baseParams: {tipo_usuario: 'finanzas',par_filtro: 'puve.nombre#puve.codigo', tipo_factura: this.tipo_factura}
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
                    form: false
                },
                {
                    config : {
                        name : 'fecha_ini',
                        id:'fecha_ini'+this.idContenedor,
                        fieldLabel : 'Fecha Desde',
                        allowBlank : false,
                        format : 'd/m/Y',
                        renderer : function(value, p, record) {
                            return value ? value.dateFormat('d/m/Y h:i:s') : ''
                        },
                        vtype: 'daterange',
                        endDateField: 'fecha_fin'+this.idContenedor
                    },
                    type : 'DateField',
                    id_grupo : 0,
                    grid : true,
                    form : true
                },
                {
                    config : {
                        name : 'fecha_fin',
                        id:'fecha_fin'+this.idContenedor,
                        fieldLabel: 'Fecha Hasta',
                        allowBlank: false,
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer: function(value, p, record) {
                            return value ? value.dateFormat('d/m/Y h:i:s') : ''
                        },
                        vtype: 'daterange',
                        startDateField: 'fecha_ini'+this.idContenedor
                    },
                    type : 'DateField',
                    id_grupo : 0,
                    grid : true,
                    form : true
                },
             ];

            Phx.vista.FormRepNotas.superclass.constructor.call(this, config);
            this.init();
            this.iniciarEventos();

        },
        title : 'Reporte de Notas de Credito Debito ligadas a una liquidacion',
        topBar : true,
        botones : false,
        remoteServer : '',
        labelSubmit : 'Generar',
        tooltipSubmit : '<b>Reporte de Notas de Credito Debito ligadas a una liquidacion</b>',
        tipo : 'reporte',
        clsSubmit : 'bprint',
        Grupos : [{
            layout : 'column',
            items : [{
                xtype : 'fieldset',
                layout : 'form',
                border : true,
                title : 'Generar Reporte',
                bodyStyle : 'padding:0 10px 0;',
                columnWidth : '300px',
                items : [],
                id_grupo : 0,
                collapsible : true
            }]
        }],

        iniciarEventos : function() {

        },

        onSubmit: function(){
            if (this.form.getForm().isValid()) {
                var data={};
                data.fecha_ini=this.getComponente('fecha_ini').getValue().dateFormat('d/m/Y');
                //data.id_punto_venta=this.getComponente('id_punto_venta').getValue();
                data.fecha_fin=this.getComponente('fecha_fin').getValue().dateFormat('d/m/Y');

                Phx.CP.loadWindows('../../../sis_devoluciones/vista/reportes/GridRepNotas.php', 'Notas ', {
                    width : '90%',
                    height : '80%'
                }, data	, this.idContenedor, 'GridRepNotas')



            }
        },
        desc_item:''

    })
</script>
