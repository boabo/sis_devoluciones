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


select pxp.f_insert_tgui ('Generar Nota PXP2', 'Generar Nota PXP2', 'GN2', 'si', 1, 'sis_devoluciones/vista/nota/form_generar_nota.php', 2, '', 'FormNota', 'DECR');


INSERT INTO decr.ttipo_doc_liquidacion (id_usuario_reg, id_usuario_mod, fecha_reg, fecha_mod, estado_reg, id_usuario_ai, usuario_ai, tipo_documento, descripcion) VALUES (1, null, now(), null, 'activo', null, 'NULL', 'DEPOSITO', 'Por Deposito');


INSERT INTO decr.ttipo_doc_liquidacion (id_usuario_reg, id_usuario_mod, fecha_reg, fecha_mod, estado_reg, id_usuario_ai, usuario_ai, tipo_documento, descripcion) VALUES (1, null, now(), null, 'activo', null, 'NULL', 'BOLEMD', 'Boleto Emitido');
INSERT INTO decr.ttipo_doc_liquidacion (id_usuario_reg, id_usuario_mod, fecha_reg, fecha_mod, estado_reg, id_usuario_ai, usuario_ai, tipo_documento, descripcion) VALUES (1, null, now(), null, 'activo', null, 'NULL', 'FACCOM', 'Factura Computarizada');
INSERT INTO decr.ttipo_doc_liquidacion (id_usuario_reg, id_usuario_mod, fecha_reg, fecha_mod, estado_reg, id_usuario_ai, usuario_ai, tipo_documento, descripcion) VALUES (1, null, now(), null, 'activo', null, 'NULL', 'FAC-ANTIGUAS', 'Factura Antiguas');
INSERT INTO decr.ttipo_doc_liquidacion (id_usuario_reg, id_usuario_mod, fecha_reg, fecha_mod, estado_reg, id_usuario_ai, usuario_ai, tipo_documento, descripcion) VALUES (1, null, now(), null, 'activo', null, 'NULL', 'PORLIQUI', 'Liquidacion por Liquidacion');
INSERT INTO decr.ttipo_doc_liquidacion (id_usuario_reg, id_usuario_mod, fecha_reg, fecha_mod, estado_reg, id_usuario_ai, usuario_ai, tipo_documento, descripcion) VALUES (1, null, now(), null, 'activo', null, 'NULL', 'RO', 'Recibos');
INSERT INTO decr.ttipo_doc_liquidacion (id_usuario_reg, id_usuario_mod, fecha_reg, fecha_mod, estado_reg, id_usuario_ai, usuario_ai, tipo_documento, descripcion) VALUES (1, null, now(), null, 'activo', null, 'NULL', 'RO-ANTIGUAS', 'Recibos Antiguos');
INSERT INTO decr.ttipo_doc_liquidacion (id_usuario_reg, id_usuario_mod, fecha_reg, fecha_mod, estado_reg, id_usuario_ai, usuario_ai, tipo_documento, descripcion) VALUES (1, null, now(), null, 'activo', null, 'NULL', 'DEPOSITO', 'Por Deposito');
INSERT INTO decr.ttipo_doc_liquidacion (id_usuario_reg, id_usuario_mod, fecha_reg, fecha_mod, estado_reg, id_usuario_ai, usuario_ai, tipo_documento, descripcion) VALUES (1, null, now(), null, 'activo', null, 'NULL', 'BOLETO-NO-EXISTENTES', 'Boleto no existentes');


select pxp.f_insert_tgui ('Nota Agencia', 'Nota Agencia', 'RGA', 'si', 1, 'sis_devoluciones/vista/nota_agencia/NotaAgencia.php', 2, '', 'NotaAgencia', 'DECR');



/********************************************F-DAT-FFP-DECR-0-07/07/2020********************************************/
