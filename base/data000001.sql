/********************************************I-DAT-FFP-DECR-0-18/09/2015********************************************/


INSERT INTO segu.tsubsistema ( codigo, nombre, fecha_reg, prefijo, estado_reg, nombre_carpeta, id_subsis_orig)
VALUES ('DECR', 'devoluciones', '2015-09-18', 'DECR', 'activo', 'devoluciones', NULL);


select pxp.f_insert_tgui ('SISTEMA DE DEBITO CREDITO NCD', '', 'DECR', 'si', 1, '', 1, '', '', 'DECR');
select pxp.f_insert_tgui ('Devoluciones', 'Devoluciones', 'DEVO', 'si', 1, 'sis_devoluciones/vista/factura/formulario_notas.php', 2, '', 'FormNota', 'DECR');
select pxp.f_insert_tgui ('Ver Nota', 'notas y detalles de las devoluciones', 'NOTDE', 'si', 2, 'sis_devoluciones/vista/nota/Nota.php', 2, '', 'Nota', 'DECR');


/********************************************F-DAT-FFP-DECR-0-18/09/2015********************************************/
