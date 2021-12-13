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
    Phx.vista.FormNotaSiat = Ext.extend(Phx.frmInterfaz, {
        ActSave: '../../sis_devoluciones/control/Liquidacion/insertarNotaSiat',

        constructor: function(config) {
            Ext.apply(this,config);
            this.Atributos = [
                {
                    config:{
                        name: 'nro_aut',
                        id: 'nro_aut'+this.idContenedor,
                        fieldLabel: 'Nro Autorizacion',
                        allowBlank: true,
                        width: 200,
                        gwidth: 100,
                        maxLength:255,
                        //disabled: true,
                    },
                    type:'TextField',
                    filters:{pfiltro:'liqui.nro_aut',type:'string'},
                    id_grupo:3,
                    grid:false,
                    form:true
                },
                {
                    config:{
                        name: 'nro_nota',
                        id: 'nro_nota'+this.idContenedor,
                        fieldLabel: 'Nro Nota',
                        allowBlank: true,
                        width: 200,
                        gwidth: 100,
                        maxLength:255,
                        //disabled: true,
                    },
                    type:'TextField',
                    filters:{pfiltro:'liqui.nro_nota',type:'string'},
                    id_grupo:3,
                    grid:false,
                    form:true
                },
            ];

            Phx.vista.FormNotaSiat.superclass.constructor.call(this, config);
            this.init();
            this.iniciarEventos();
            this.obtenerDatosIniciales(config);

        },
        title : 'Nota siat',
        topBar : true,
        botones : false,
        remoteServer : '',
        labelSubmit : 'Guardar',
        tooltipSubmit : '<b>Guardar nota siat</b>',
        //tipo : 'reporte',
        clsSubmit : 'bsave',
        Grupos : [{
            layout : 'column',
            items : [{
                xtype : 'fieldset',
                layout : 'form',
                border : true,
                title : 'Datos Nota Siat',
                bodyStyle : 'padding:0 10px 0;',
                columnWidth : '300px',
                items : [],
                id_grupo : 0,
                collapsible : true
            }]
        }],
        breset: false,
        obtenerDatosIniciales: function (config) {
            var me = this;
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_devoluciones/control/Liquidacion/obtenerNotaSiat',
                params: {
                    id_liquidacion: me.id_liquidacion
                },
                success: function (resp) {
                    Phx.CP.loadingHide();

                    var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));

                    if (reg.ROOT.error) {
                        Ext.Msg.alert('Error', 'Error a recuperar la variable global')
                    } else {
                        const mensaje = reg.ROOT.datos.mensaje;
                        console.log('mensaje', mensaje);

                        if (mensaje == "") {
                            alert('no se puede obtener ninguna moneda oficial para el dia de hoy')

                        } else {
                            const data = JSON.parse(mensaje);
                            console.log('data', data);
                            this.getComponente('nro_aut').setValue(data.nro_aut);
                            this.getComponente('nro_nota').setValue(data.nro_nota);
                        }
                    }

                },
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
        },


        iniciarEventos : function() {

        },

        onSubmit: function(o){
            if (this.form.getForm().isValid()) {
                var data = {};
                data.nro_aut = this.getComponente('nro_aut').getValue();
                data.nro_nota = this.getComponente('nro_nota').getValue();
                this.argumentExtraSubmit = {id_liquidacion: this.id_liquidacion, 'nota': JSON.stringify(data)};

                console.log(this)
                Phx.vista.FormNotaSiat.superclass.onSubmit.call(this, o, undefined, true);

            }

        },
        desc_item:''

    })
</script>
