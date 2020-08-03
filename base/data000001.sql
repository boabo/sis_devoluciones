/********************************************I-DAT-FFP-DECR-0-18/09/2015********************************************/


INSERT INTO segu.tsubsistema ( codigo, nombre, fecha_reg, prefijo, estado_reg, nombre_carpeta, id_subsis_orig)
VALUES ('DECR', 'devoluciones', '2015-09-18', 'DECR', 'activo', 'devoluciones', NULL);


select pxp.f_insert_tgui ('SISTEMA DE DEBITO CREDITO NCD', '', 'DECR', 'si', 1, '', 1, '', '', 'DECR');
select pxp.f_insert_tgui ('Devoluciones', 'Devoluciones', 'DEVO', 'si', 1, 'sis_devoluciones/vista/factura/formulario_notas.php', 2, '', 'FormNota', 'DECR');
select pxp.f_insert_tgui ('Ver Nota', 'notas y detalles de las devoluciones', 'NOTDE', 'si', 2, 'sis_devoluciones/vista/nota/Nota.php', 2, '', 'Nota', 'DECR');

select pxp.f_insert_tgui ('conf', 'conf', 'CONFIDE', 'si', 1, '', 2, '', '', 'DECR');
select pxp.f_insert_tgui ('sucursal', 'sucursal', 'SUCD', 'si', 1, 'sis_devoluciones/vista/sucursal/Sucursal.php', 3, '', 'Sucursal', 'DECR');
select pxp.f_insert_tgui ('usuario_suc', 'usuario sucursal', 'USUSUC', 'si', 2, 'sis_devoluciones/vista/sucursal_usuario/SucursalUsuario.php', 3, '', 'SucursalUsuario', 'DECR');




select pxp.f_insert_tgui ('devweb', 'devweb', 'DEWE', 'si', 3, 'sis_devoluciones/vista/devweb/Devweb.php', 3, '', 'Devweb', 'DECR');

/********************************************F-DAT-FFP-DECR-0-18/09/2015********************************************/

/********************************************I-DAT-FFP-DECR-0-07/07/2020********************************************/


select pxp.f_insert_tgui ('Liquidacion', 'Liquidacion', 'LIQFILE', 'si', 1, '', 2, '', '', 'DECR');
select pxp.f_insert_tgui ('Tipo Liquidacion', 'Tipo Liquidacion', 'TYPELIQ', 'si', 1, 'sis_devoluciones/vista/tipo_liquidacion/TipoLiquidacion.php', 3, '', 'TipoLiquidacion', 'DECR');
select pxp.f_insert_tgui ('Tipo Doc Liquidacion', 'Tipo Doc Liquidacion', 'TDOCLIQ', 'si', 2, 'sis_devoluciones/vista/tipo_doc_liquidacion/TipoDocLiquidacion.php', 3, '', 'TipoDocLiquidacion', 'DECR');
select pxp.f_insert_tgui ('Liquidacion', 'Liquidacion', 'SCREENLIQ', 'si', 3, 'sis_devoluciones/vista/liquidacion/Liquidacion.php', 3, '', 'Liquidacion', 'DECR');
select pxp.f_insert_tgui ('Descuento Liquidacion', 'Descuento Liquidacion', 'SCREENDESL', 'si', 4, 'sis_devoluciones/vista/descuento_liquidacion/DescuentoLiquidacion.php', 3, '', 'DescuentoLiquidacion', 'DECR');


--configuracion para el documento para los autocorrelativos
SELECT param.f_import_tdocumento('insert', 'LIQCBBDEV', 'LIQCBBDEV', 'DECR', 'tabla', 'gestion', '',
                                 'CBB-DEVCBB-gestioncorrelativo');


select param.f_import_tcatalogo_tipo ('insert','tliquidacion_estacion','DECR','tliquidacion');
select param.f_import_tcatalogo ('insert','DECR','CBB','CBB','tliquidacion_estacion');
select param.f_import_tcatalogo ('insert','DECR','LPB','LPB','tliquidacion_estacion');


/********************************************F-DAT-FFP-DECR-0-07/07/2015********************************************/
