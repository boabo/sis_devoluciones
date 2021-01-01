<?php
/**
 * @package pXP
 * @file gen-Nota.php
 * @author  (ada.torrico)
 * @date 18-11-2014 19:30:03
 * @description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */
/*favio ooo*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.Nota = Ext.extend(Phx.gridInterfaz, {

            constructor: function (config) {
                this.maestro = config.maestro;
                //llama al constructor de la clase padre
                Phx.vista.Nota.superclass.constructor.call(this, config);
                this.init();
                this.load({params: {start: 0, limit: this.tam_pag}});
                this.addButton('imprimir', {
                    argument: {imprimir: 'imprimir_nota'},
                    text: '<i class="fa fa-print fa-3x"></i>  Imprimir Nota', /*iconCls:'' ,*/
                    disabled: false,
                    handler: this.reimprimir
                });
                this.addButton('anular', {
                    argument: {imprimir: 'anular_nota'},
                    text: '<i class="fa fa-file-excel-o fa-3x"></i> Anular Nota', /*iconCls:'' ,*/
                    disabled: false,
                    handler: this.anular
                });

                this.addButton('ver', {
                    argument: {imprimir: 'ver'},
                    text: '<i class="fa fa-eye fa-3x"></i> ver', /*iconCls:'' ,*/
                    disabled: false,
                    handler: this.verImpresiones
                });


                //this.addButton('Ver Reimpresiones',{argument: {imprimir: 'verImpresiones'},text:'<i class="fa fa-files-o fa-3x"></i> ver ReImpresiones',/*iconCls:'' ,*/disabled:false,handler:this.verImpresiones});


            },

            Atributos: [
                {
                    //configuracion del componente
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_nota'
                    },
                    type: 'Field',
                    form: true
                },


                {
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_factura'
                    },
                    type: 'Field',
                    form: true
                },

                {
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_sucursal'
                    },
                    type: 'Field',
                    form: true
                },


                {
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_moneda'
                    },
                    type: 'Field',
                    form: true
                },

                {
                    config: {
                        name: 'nro_nota',
                        fieldLabel: 'nro_nota',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 50,
                        renderer: function (value, meta, record) {
                            console.log('meta',meta)
                            var resp;
                            resp = value;
                            var css;

                            var lista_negra = '';

                            if(record.json.estado == '9' || record.json.estado == 9){
                                css = "color:red; font-weight: bold; display:block;"
                                lista_negra = '<div>(anulado)</div>'
                            }else{
                                css = "";
                            }


                            var value = parseFloat(202020.1234).toFixed(2),
                                format = "0,000.00";

                            Number.prototype.formatDinero = function(c, d, t){
                                var n = this,
                                    c = isNaN(c = Math.abs(c)) ? 2 : c,
                                    d = d == undefined ? "." : d,
                                    t = t == undefined ? "," : t,
                                    s = n < 0 ? "-" : "",
                                    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
                                    j = (j = i.length) > 3 ? j % 3 : 0;
                                return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
                            };

                            console.log('value',(1123123.1234).formatDinero(2, ',', '.'))

                            Ext.util.Format.thousandSeparator = ".";
                            Ext.util.Format.decimalSeparator = ",";
                            console.log(Ext.util.Format.number(value, format)); // devuelve 202.020,12

                            return  String.format('<div style="vertical-align:middle;text-align:center;"><span style="{0}">{1}{2}</span></div>',css,resp,lista_negra);
                            //return resp;
                        }

                    },
                    type: 'TextField',
                    filters: {pfiltro: 'no.nro_nota', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true, bottom_filter:true

                },


                {
                    config: {
                        name: 'nro_liquidacion',
                        fieldLabel: 'nro_liquidacion',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 50,

                    },
                    type: 'TextField',
                    filters: {pfiltro: 'no.nro_liquidacion', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true,
                    bottom_filter:true,

                },

                {
                    config: {
                        name: 'total_devuelto',
                        fieldLabel: 'total_devuelto',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 1179654
                    },
                    type: 'NumberField',
                    filters: {pfiltro: 'no.total_devuelto', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },


                {
                    config: {
                        name: 'excento',
                        fieldLabel: 'excento',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 1179654
                    },
                    type: 'NumberField',
                    filters: {pfiltro: 'no.excento', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },


                {
                    config: {
                        name: 'estacion',
                        fieldLabel: 'estacion',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 20
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'no.estacion', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'fecha',
                        fieldLabel: 'fecha',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'no.fecha', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },


                {
                    config: {
                        name: 'tcambio',
                        fieldLabel: 'tcambio',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 1179654
                    },
                    type: 'NumberField',
                    filters: {pfiltro: 'no.tcambio', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },

                {
                    config: {
                        name: 'nit',
                        fieldLabel: 'nit',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 50
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'no.nit', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'razon',
                        fieldLabel: 'razon',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 50
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'no.razon', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true,
                    bottom_filter:true
                },
                {
                    config: {
                        name: 'estado',
                        fieldLabel: 'estado',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 50
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'no.estado', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'credfis',
                        fieldLabel: 'credfis',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 1179654
                    },
                    type: 'NumberField',
                    filters: {pfiltro: 'no.credfis', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },

                {
                    config: {
                        name: 'monto_total',
                        fieldLabel: 'monto_total',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 1179654
                    },
                    type: 'NumberField',
                    filters: {pfiltro: 'no.monto_total', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'estado_reg',
                        fieldLabel: 'Estado Reg.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 10
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'no.estado_reg', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },


                {
                    config: {
                        name: 'id_usuario_ai',
                        fieldLabel: '',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 4
                    },
                    type: 'Field',
                    filters: {pfiltro: 'no.id_usuario_ai', type: 'numeric'},
                    id_grupo: 1,
                    grid: false,
                    form: false
                },
                {
                    config: {
                        name: 'usuario_ai',
                        fieldLabel: 'Funcionaro AI',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 300
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'no.usuario_ai', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'fecha_reg',
                        fieldLabel: 'Fecha creación',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y H:i:s') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'no.fecha_reg', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'usr_reg',
                        fieldLabel: 'Creado por',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 4
                    },
                    type: 'Field',
                    filters: {pfiltro: 'usu1.cuenta', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'fecha_mod',
                        fieldLabel: 'Fecha Modif.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y H:i:s') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'no.fecha_mod', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'usr_mod',
                        fieldLabel: 'Modificado por',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 4
                    },
                    type: 'Field',
                    filters: {pfiltro: 'usu2.cuenta', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false

                }
            ],
            tam_pag: 50,
            title: 'Notas',
            ActSave: '../../sis_devoluciones/control/Nota/insertarNota',
            ActDel: '../../sis_devoluciones/control/Nota/eliminarNota',
            ActList: '../../sis_devoluciones/control/Nota/listarNota',
            id_store: 'id_nota',
            fields: [
                {name: 'id_nota', type: 'numeric'},
                {name: 'id_factura', type: 'numeric'},
                {name: 'id_sucursal', type: 'numeric'},
                {name: 'id_moneda', type: 'numeric'},
                {name: 'estacion', type: 'string'},
                {name: 'fecha', type: 'date', dateFormat: 'Y-m-d'},
                {name: 'excento', type: 'numeric'},
                {name: 'total_devuelto', type: 'numeric'},
                {name: 'tcambio', type: 'numeric'},
                {name: 'id_liquidacion', type: 'string'},
                {name: 'nit', type: 'string'},
                {name: 'estado', type: 'string'},
                {name: 'credfis', type: 'numeric'},
                {name: 'nro_liquidacion', type: 'string'},
                {name: 'monto_total', type: 'numeric'},
                {name: 'estado_reg', type: 'string'},
                {name: 'nro_nota', type: 'string'},
                {name: 'razon', type: 'string'},
                {name: 'id_usuario_ai', type: 'numeric'},
                {name: 'usuario_ai', type: 'string'},
                {name: 'fecha_reg', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'id_usuario_reg', type: 'numeric'},
                {name: 'fecha_mod', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'id_usuario_mod', type: 'numeric'},
                {name: 'usr_reg', type: 'string'},
                {name: 'usr_mod', type: 'string'},
                {name: 'billete', type: 'string'},
                {name: 'nroaut', type: 'numeric'},

            ],
            sortInfo: {
                field: 'id_nota',
                direction: 'DESC'
            },
            bdel: false,
            bsave: false,
            bedit: false,
            bnew: false,
            east: {
                url: '../../../sis_devoluciones/vista/nota_detalle/NotaDetalle.php',
                title: 'Columnas',
                width: 300,
                cls: 'NotaDetalle'
            },
            reimprimir: function () {

                var rec = this.sm.getSelected();

                Ext.Ajax.request({
                    url: '../../sis_devoluciones/control/Nota/verNota',
                    params: {'notas': rec.data['id_nota'], 'reimpresion': 'si'},
                    success: this.successExport,
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });


            },
            successExport: function (resp) {

                Phx.CP.loadingHide();

                var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));

                console.log(objRes);


                objetoDatos = (objRes.ROOT == undefined)?objRes.datos:objRes.ROOT.datos;
                var i = 0;

                objetoDatos.forEach(function (item) {

                    var texto = item;
                    ifrm = document.createElement("IFRAME");
                    ifrm.name = 'mifr' + i;
                    ifrm.id = 'mifr' + i;
                    document.body.appendChild(ifrm);
                    var doc = window.frames['mifr' + i].document;
                    doc.open();
                    doc.write(texto);
                    doc.close();
                    i++;

                });


            },

            anular: function () {

                var rec = this.sm.getSelected();
                //console.log(this.confirmacion());


                Ext.MessageBox.confirm('Confirmación', '¿Estas seguro de querer hacer esto?', function (btn) {
                    if (btn === 'yes') {

                        Ext.Ajax.request({
                            url: '../../sis_devoluciones/control/Nota/anularNota',
                            params: {'liquidevolu':rec.data['nro_liquidacion'],'nro_liquidacion':rec.data['nro_liquidacion'],'notas': rec.data['id_nota'], 'nota_informix': rec.data['nro_nota'],'nroaut':rec.data['nroaut']},
                            success: this.actualizarNotas,
                            failure: this.conexionFailure,
                            timeout: this.timeout,
                            scope: this

                        });
                    } else {
                        //si el usuario canceló
                        //alert('Decidiste Cancelar la Anulacion de la nota');
                    }
                }, this);


            },
            verImpresiones: function () {
                var rec = this.sm.getSelected();

                Ext.Ajax.request({
                    url: '../../sis_devoluciones/control/Nota/generarNota',
                    params: {'notas': rec.data['id_nota'], 'reimpresion': 'si','vista_previa':'si'},
                    success: this.successVistaPrevia,
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });



            },
            successVistaPrevia:function (resp) {
                var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));



                objetoDatos = (objRes.ROOT == undefined)?objRes.datos:objRes.ROOT.datos;
                console.log(objetoDatos[0])
               var myWindow = window.open("", "_blank");
                myWindow.document.write(objetoDatos[0]);



            },


            actualizarNotas:function(){
                this.load({params: {start: 0, limit: this.tam_pag}});
            }


        }
    )
</script>

