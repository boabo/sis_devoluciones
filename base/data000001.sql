/********************************************I-DAT-FFP-DECR-0-18/09/2015********************************************/


INSERT INTO segu.tsubsistema ( codigo, nombre, fecha_reg, prefijo, estado_reg, nombre_carpeta, id_subsis_orig)
VALUES ('DECR', 'devoluciones', '2015-09-18', 'DECR', 'activo', 'devoluciones', NULL);


select pxp.f_insert_tgui ('SISTEMA DE DEBITO CREDITO NCD', '', 'DECR', 'si', 1, '', 1, '', '', 'DECR');
select pxp.f_insert_tgui ('Devoluciones', 'Devoluciones', 'DEVO', 'si', 1, 'sis_devoluciones/vista/factura/formulario_notas.php', 2, '', 'FormNota', 'DECR');
select pxp.f_insert_tgui ('Ver Nota', 'notas y detalles de las devoluciones', 'NOTDE', 'si', 2, 'sis_devoluciones/vista/nota/Nota.php', 2, '', 'Nota', 'DECR');

select pxp.f_insert_tgui ('conf', 'conf', 'CONFIDE', 'si', 1, '', 2, '', '', 'DECR');
select pxp.f_insert_tgui ('sucursal', 'sucursal', 'SUCD', 'si', 1, 'sis_devoluciones/vista/sucursal/Sucursal.php', 3, '', 'Sucursal', 'DECR');
select pxp.f_insert_tgui ('usuario_suc', 'usuario sucursal', 'USUSUC', 'si', 2, 'sis_devoluciones/vista/sucursal_usuario/SucursalUsuario.php', 3, '', 'SucursalUsuario', 'DECR');


/********************************************F-DAT-FFP-DECR-0-18/09/2015********************************************/
