<?php
/**
 * @package pxP
 * @file 	GridTicketsAtendidos.php
 * @author 	RCM
 * @date	10/07/2013
 * @description	Reporte Sistema de Colas
 */
header("content-type:text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.GridRepNotas = Ext.extend(Phx.gridInterfaz, {
        constructor : function(config) {
            this.maestro = config;
            this.sucursal = this.desc_sucursal;

            Phx.vista.GridRepNotas.superclass.constructor.call(this, config);
            this.init();
            this.load({
                params : {
                    start: 0,
                    limit: this.tam_pag,
                    fecha_ini:this.maestro.fecha_ini,
                    fecha_fin:this.maestro.fecha_fin,
                    id_punto_venta:this.maestro.id_punto_venta,
                }
            });
        },
        tam_pag:1000,
        Atributos : [

            {
                config: {
                    name: 'nro_liquidacion',
                    fieldLabel: 'Nro Liq.',
                    gwidth: 100
                },
                type: 'Field',
                grid: true

            },
            {
                config: {
                    name: 'estado',
                    fieldLabel: 'estado',
                    gwidth: 120
                },
                type: 'Field',
                grid: true

            },
            {
                config: {
                    name: 'fecha',
                    fieldLabel: 'fecha',
                    gwidth: 120
                },
                type: 'Field',
                grid: true

            },
            {
                config: {
                    name: 'nit',
                    fieldLabel: 'nit',
                    gwidth: 120
                },
                type: 'Field',
                grid: true

            },
            {
                config: {
                    name: 'razon',
                    fieldLabel: 'razon',
                    gwidth: 120
                },
                type: 'Field',
                grid: true

            },
            {
                config: {
                    name: 'nro_nota',
                    fieldLabel: 'nro_nota',
                    gwidth: 120
                },
                type: 'Field',
                grid: true

            },
            {
                config: {
                    name: 'nroaut_dosificacion',
                    fieldLabel: 'nroaut_dosificacion',
                    gwidth: 120
                },
                type: 'Field',
                grid: true

            },
            {
                config: {
                    name: 'importe_total',
                    fieldLabel: 'importe_total',
                    gwidth: 120
                },
                type: 'Field',
                grid: true

            },
            {
                config: {
                    name: 'exento_total',
                    fieldLabel: 'exento_total',
                    gwidth: 120
                },
                type: 'Field',
                grid: true

            },
            {
                config: {
                    name: 'total_devolver',
                    fieldLabel: 'total_devolver',
                    gwidth: 120
                },
                type: 'Field',
                grid: true

            },
            {
                config: {
                    name: 'codigo_control',
                    fieldLabel: 'codigo_control',
                    gwidth: 120
                },
                type: 'Field',
                grid: true

            },


        ],
        title : 'Notas',
        ActList : '../../sis_devoluciones/control/Nota/listarNotaJson',
        fields : [ {
            name : 'nro_liquidacion',
            type : 'string'
        },{
            name : 'estado',
            type : 'string'
        },{
            name : 'fecha',
            type : 'string'
        },'nit',
            'razon',
            'nro_nota',
            'nroaut_dosificacion',
            'importe_total',
            'exento_total',
            'total_devolver',
            'codigo_control'

        ],
        sortInfo : {
            field : 'nombre',
            direction : 'ASC'
        },
        bdel : false,
        bnew: false,
        bedit: false,
        fwidth : '90%',
        fheight : '80%'
    });
</script>
