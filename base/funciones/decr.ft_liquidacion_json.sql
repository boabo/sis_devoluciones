CREATE OR REPLACE FUNCTION "decr"."ft_liquidacion_json" (
    p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
    RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		devoluciones
 FUNCION: 		decr.ft_liquidacion_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'decr.tliquidacion'
 AUTOR: 		 (admin)
 FECHA:	        17-04-2020 01:54:37
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				17-04-2020 01:54:37								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'decr.tliquidacion'
 #
 ***************************************************************************/

DECLARE

    v_nro_requerimiento    	integer;
    v_parametros           	record;
    v_id_requerimiento     	integer;
    v_resp		            varchar;
    v_nombre_funcion        text;
    v_mensaje_error         text;
    v_id_liquidacion	integer;
    v_json	varchar;
    v_query varchar;
    v_count integer;
    v_liqui_json json;

    v_filtro_value varchar;
    v_query_value varchar;
    v_tipo_tab_liqui varchar;
    v_ids_liqui int[];
    v_ids_factucom varchar;


BEGIN

    v_nombre_funcion = 'decr.ft_liquidacion_ime';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************
 	#TRANSACCION:  'DECR_LIQUI_JSON_SEL'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		favio figueroa
 	#FECHA:		11-02-2021 19:36:57
	***********************************/

    if(p_transaccion='DECR_LIQUI_JSON_SEL')then

        begin

            IF(pxp.f_existe_parametro(p_tabla, 'tipo_tab_liqui' )) THEN
                if(v_parametros.tipo_tab_liqui != '') then
                    v_tipo_tab_liqui := v_parametros.tipo_tab_liqui;
                END IF;
            END IF;

            if(pxp.f_existe_parametro(p_tabla, 'bottom_filtro_value' )) then
                if(v_parametros.bottom_filtro_value != '') then
                    v_filtro_value := v_parametros.bottom_filtro_value;
                END IF;
            END IF;

            if(pxp.f_existe_parametro(p_tabla, 'query' )) then
                if(v_parametros.query != '') then
                    v_query_value := v_parametros.query;
                END IF;
            END IF;

            if(pxp.f_existe_parametro(p_tabla, 'id_liquidacion' )) then
                if(v_parametros.id_liquidacion != '') then
                    v_id_liquidacion := v_parametros.id_liquidacion;
                END IF;
            END IF;


            select count(tl.id_liquidacion)
            into v_count
            FROM decr.tliquidacion tl
                INNER JOIN segu.tusuario usu1 ON usu1.id_usuario = tl.id_usuario_reg
                LEFT JOIN segu.tusuario usu2 ON usu2.id_usuario = tl.id_usuario_mod
                INNER JOIN decr.ttipo_doc_liquidacion ttdl ON ttdl.id_tipo_doc_liquidacion = tl.id_tipo_doc_liquidacion
                INNER JOIN decr.ttipo_liquidacion ttl ON ttl.id_tipo_liquidacion = tl.id_tipo_liquidacion
                INNER JOIN vef.tpunto_venta pv ON pv.id_punto_venta = tl.id_punto_venta
                LEFT JOIN decr.tnota nota ON nota.id_liquidacion::integer = tl.id_liquidacion
            WHERE (case when v_id_liquidacion is not null then tl.id_liquidacion = v_id_liquidacion else 1=1 end)
            AND (case when v_tipo_tab_liqui is not null then ttdl.tipo_documento = v_tipo_tab_liqui else 1=1 end)
                    AND (CASE WHEN v_filtro_value is not null then tl.nro_liquidacion like '%' ||v_filtro_value|| '%' else 1=1 end)
                    AND (CASE WHEN v_query_value is not null then tl.nro_liquidacion like '%' ||v_query_value|| '%' else 1=1 end);



            -- creamos una consulta unica para usar con todos los tipos

            -- como en postgres no existe un bull collectino entonces guardamos los datos en un json para luego convertirlo en tabla

            with t_liqui
                     AS (
                    SELECT tl.id_liquidacion,
                           usu1.cuenta                          AS usr_reg,
                           usu2.cuenta                          AS usr_mod,
                           ttdl.tipo_documento                  AS desc_tipo_documento,
                           ttl.tipo_liquidacion                 AS desc_tipo_liquidacion,
                           pv.nombre                            AS desc_punto_venta,
                           nota.nro_nota,
                           tl.id_factucom -- solo para el tipo fac-antigua
                    FROM decr.tliquidacion tl
                             INNER JOIN segu.tusuario usu1 ON usu1.id_usuario = tl.id_usuario_reg
                             LEFT JOIN segu.tusuario usu2 ON usu2.id_usuario = tl.id_usuario_mod
                             INNER JOIN decr.ttipo_doc_liquidacion ttdl ON ttdl.id_tipo_doc_liquidacion = tl.id_tipo_doc_liquidacion
                             INNER JOIN decr.ttipo_liquidacion ttl ON ttl.id_tipo_liquidacion = tl.id_tipo_liquidacion
                             INNER JOIN vef.tpunto_venta pv ON pv.id_punto_venta = tl.id_punto_venta
                             LEFT JOIN decr.tnota nota ON nota.id_liquidacion::integer = tl.id_liquidacion
                    WHERE (case when v_id_liquidacion is not null then tl.id_liquidacion = v_id_liquidacion else 1=1 end)
                    AND (case when v_tipo_tab_liqui is not null then ttdl.tipo_documento = v_tipo_tab_liqui else 1=1 end)
                    AND (CASE WHEN v_filtro_value is not null then tl.nro_liquidacion like '%' ||v_filtro_value|| '%' else 1=1 end)
                    AND (CASE WHEN v_query_value is not null then tl.nro_liquidacion like '%' ||v_query_value|| '%' else 1=1 end)
                    order by tl.id_liquidacion DESC
                    LIMIT v_parametros.cantidad OFFSET v_parametros.puntero

                ),
                 t_sum_descuentos as
                     (
                         SELECT tl.id_liquidacion, tdl.tipo, sum(tdl.importe) as sum_total_por_tipo
                         FROM decr.tdescuento_liquidacion tdl
                                  inner JOIN param.tconcepto_ingas tci on tci.id_concepto_ingas = tdl.id_concepto_ingas
                                  INNER JOIN t_liqui tl ON tl.id_liquidacion = tdl.id_liquidacion
                         GROUP BY tl.id_liquidacion, tdl.tipo
                     ),
                 t_descuentos AS (
                     SELECT tci.codigo, tdl.id_liquidacion, tdl.id_concepto_ingas, tdl.importe, tci.desc_ingas, tdl.tipo
                     FROM decr.tdescuento_liquidacion tdl
                              INNER JOIN param.tconcepto_ingas tci ON tci.id_concepto_ingas = tdl.id_concepto_ingas
                              inner join t_liqui tl on tl.id_liquidacion = tdl.id_liquidacion
                 ),
                 t_liqui_forma_pago AS (
                     SELECT tlfp.id_liquidacion, tlfp.id_medio_pago, tmpw.name
                     FROM decr.tliqui_forma_pago tlfp
                     inner join t_liqui tl on tl.id_liquidacion = tlfp.id_liquidacion
                     inner join obingresos.tmedio_pago_pw tmpw on tmpw.id_medio_pago_pw = tlfp.id_medio_pago
                 )
            SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(liqui)))
            INTO v_liqui_json
            FROM
                (
                    SELECT *,
                           (select sum(importe) from t_descuentos td2  where td2.id_liquidacion = tl.id_liquidacion) as sum_total_descuentos,
                           (
                               SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(descuentos)))
                               FROM (
                                        SELECT *
                                        FROM t_descuentos td where td.id_liquidacion = tl.id_liquidacion
                                    ) descuentos
                           ) AS descuentos,
                           (
                               SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(sum_descuentos)))
                               FROM (
                                        SELECT *
                                        FROM t_sum_descuentos tsm WHERE tsm.id_liquidacion = tl.id_liquidacion
                                    ) sum_descuentos
                           ) AS sum_descuentos,
                           (
                               SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(liqui_forma_pago)))
                               FROM (
                                        SELECT *
                                        FROM t_liqui_forma_pago tlfp WHERE tlfp.id_liquidacion = tl.id_liquidacion
                                    ) liqui_forma_pago
                           ) AS liqui_forma_pago
                    FROM t_liqui tl
                ) liqui;





            IF v_tipo_tab_liqui = 'BOLEMD' THEN


                WITH t_liqui AS
                    (
                        SELECT tl.*,
                               liqui_tabla.usr_reg,
                               liqui_tabla.usr_mod,
                               liqui_tabla.desc_tipo_documento,
                               liqui_tabla.desc_tipo_liquidacion,
                               liqui_tabla.desc_punto_venta,
                               liqui_tabla.nro_nota,
                               liqui_tabla.sum_total_descuentos,
                               liqui_tabla.descuentos,
                               liqui_tabla.sum_descuentos,
                               liqui_tabla.liqui_forma_pago
                        FROM decr.tliquidacion tl
                                 INNER JOIN (SELECT * FROM json_populate_recordset(NULL::decr.json_type_liquidacion, v_liqui_json::json)
                        ) liqui_tabla ON liqui_tabla.id_liquidacion = tl.id_liquidacion
                    ), t_liqui_boleto as
                    (
                        SELECT tl.*,
                               tl.importe_total - tl.sum_total_descuentos as importe_devolver,
                               tb.nro_boleto as desc_nro_boleto,
                               tb.nit::varchar as nro_nit,
                               tb.razon,
                               tb.fecha_emision as fecha_fac,
                               tb.total,
                               1 as nro_aut,
                               tb.nro_boleto as nro_fac,
                               concat(tb.nro_boleto,'/',tl.tramo_devolucion):: varchar as concepto,
                               'BOLETO'::VARCHAR AS tipo,
                               tb.total AS precio_unitario,
                               tb.total AS importe_original,
                               tb.id_boleto as id,
                               1::integer as cantidad,
                               --data para boleto tienen
                               tb.nro_boleto as _desc_liqui,
                               tl.tramo_devolucion as _desc_liqui_det
                        FROM t_liqui tl
                                 INNER JOIN obingresos.tboleto tb on tb.id_boleto = tl.id_boleto
                    )
                SELECT TO_JSON(ROW_TO_JSON(jsonData) :: TEXT) #>> '{}' as json
                into v_json
                from (
                         SELECT
                             (
                                 SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(liqui_boleto)))
                                 FROM
                                     (
                                         SELECT *
                                         FROM t_liqui_boleto tlb
                                     ) liqui_boleto
                             ) as datos,
                         v_count as count

                     ) jsonData;

            elsif v_tipo_tab_liqui = 'FACCOM' THEN


                WITH t_liqui AS
                    (
                        SELECT tl.*,
                               liqui_tabla.usr_reg,
                               liqui_tabla.usr_mod,
                               liqui_tabla.desc_tipo_documento,
                               liqui_tabla.desc_tipo_liquidacion,
                               liqui_tabla.desc_punto_venta,
                               liqui_tabla.nro_nota,
                               liqui_tabla.sum_total_descuentos,
                               liqui_tabla.descuentos,
                               liqui_tabla.sum_descuentos
                        FROM decr.tliquidacion tl
                                 INNER JOIN (SELECT * FROM json_populate_recordset(NULL::decr.json_type_liquidacion, v_liqui_json::json)
                        ) liqui_tabla ON liqui_tabla.id_liquidacion = tl.id_liquidacion
                    ),t_venta_detalle AS
                    (
                        SELECT tvd.*, tci.codigo as desc_codigo, tci.desc_ingas
                        from vef.tventa_detalle tvd
                                 inner join vef.tventa tv on tv.id_venta = tvd.id_venta
                                 INNER JOIN t_liqui tl on tl.id_venta = tv.id_venta
                                 inner join param.tconcepto_ingas tci on tci.id_concepto_ingas = tvd.id_producto
                    ), sum_descuentos as
                    (
                        SELECT tl.id_liquidacion, sum(tdl.importe) as sum_descuentos
                        FROM decr.tdescuento_liquidacion tdl
                                 inner JOIN param.tconcepto_ingas tci on tci.id_concepto_ingas = tdl.id_concepto_ingas
                                 INNER JOIN t_liqui tl ON tl.id_liquidacion = tdl.id_liquidacion
                        GROUP BY tl.id_liquidacion
                    )
                   , t_liqui_recibo as
                    (
                        SELECT tl.*,
                               sd.sum_descuentos,
                               tl.importe_total - sd.sum_descuentos as importe_devolver,
                               tv.nro_factura,
                               tv.nombre_factura,
                               --tvd.id_venta_detalle,
                               (select  string_agg(tvd2.id_venta_detalle::text, ',')::varchar as id_venta_detalle from t_venta_detalle tvd2 where tvd2.id_venta = tl.id_venta  GROUP BY tvd2.id_venta ) as id_venta_detalle,
                               --data para boleto tienen
                               tv.nro_factura as _desc_liqui,
                               (
                                   SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(venta_detalle)))
                                   FROM (
                                            SELECT *
                                            FROM t_venta_detalle tvd3 where tvd3.id_venta = tl.id_venta
                                        ) venta_detalle
                               ) AS _desc_liqui_det
                               --tl.tramo_devolucion as _desc_liqui_det
                        FROM t_liqui tl
                                 INNER JOIN vef.tventa tv on tv.id_venta = tl.id_venta
                                 INNER JOIN t_venta_detalle tvd on tvd.id_venta = tv.id_venta
                                 LEFT JOIN sum_descuentos sd ON sd.id_liquidacion = tl.id_liquidacion
                    )
                SELECT TO_JSON(ROW_TO_JSON(jsonData) :: TEXT) #>> '{}' as json
                into v_json
                from (
                         SELECT
                             (
                                 SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(liqui_recibo)))
                                 FROM
                                     (
                                         SELECT *
                                         FROM t_liqui_recibo tlr
                                     ) liqui_recibo
                             ) as datos,
                             v_count as count

                     ) jsonData;

            elsif v_tipo_tab_liqui = 'FAC-ANTIGUAS' THEN




                SELECT string_agg(j->>'id_factucom', ',')::varchar
                INTO v_ids_factucom
                FROM json_array_elements(v_liqui_json) j;

                WITH t_liqui AS
                    (
                        SELECT tl.*,
                               liqui_tabla.usr_reg,
                               liqui_tabla.usr_mod,
                               liqui_tabla.desc_tipo_documento,
                               liqui_tabla.desc_tipo_liquidacion,
                               liqui_tabla.desc_punto_venta,
                               liqui_tabla.nro_nota,
                               liqui_tabla.sum_total_descuentos,
                               liqui_tabla.descuentos,
                               liqui_tabla.sum_descuentos
                        FROM decr.tliquidacion tl
                                 INNER JOIN (SELECT * FROM json_populate_recordset(NULL::decr.json_type_liquidacion, v_liqui_json::json)
                        ) liqui_tabla ON liqui_tabla.id_liquidacion = tl.id_liquidacion
                    ), t_factucom AS (
                            SELECT * FROM dblink('dbname=dbendesis host=192.168.100.30 user=ende_pxp password=ende_pxp',
                                         'SELECT id_factucom,nroaut,nrofac,monto,razon_cliente,fecha FROM informix.tif_factucom where id_factucom in ('||v_ids_factucom||') '
                                      ) AS d (id_factucom integer, nroaut numeric, nrofac numeric, monto numeric, razon_cliente varchar, fecha date)
                    ), t_factucomcon AS (
                            SELECT * FROM dblink('dbname=dbendesis host=192.168.100.30 user=ende_pxp password=ende_pxp',
                                                 'SELECT id_factucomcon,id_factucom,cantidad,preciounit,importe,concepto FROM informix.tif_factucomcon where id_factucom in ('||v_ids_factucom||') '
                                              ) AS d (id_factucomcon integer, id_factucom integer, cantidad numeric, preciounit numeric, importe numeric, concepto varchar)
                    )
                     ,t_venta_detalle AS
                    (
                        SELECT tfcc.*, tfcc.concepto as desc_ingas
                        from t_factucomcon tfcc
                        INNER JOIN t_liqui tl on tl.id_factucom = tfcc.id_factucom
                    ), sum_descuentos as
                    (
                        SELECT tl.id_liquidacion, sum(tdl.importe) as sum_descuentos
                        FROM decr.tdescuento_liquidacion tdl
                                 inner JOIN param.tconcepto_ingas tci on tci.id_concepto_ingas = tdl.id_concepto_ingas
                                 INNER JOIN t_liqui tl ON tl.id_liquidacion = tdl.id_liquidacion
                        GROUP BY tl.id_liquidacion
                    )
                   , t_liqui_recibo as
                    (
                        SELECT tl.*,
                               sd.sum_descuentos,
                               tl.importe_total - sd.sum_descuentos as importe_devolver,
                               tfc.nrofac as nro_factura,
                               tfc.razon_cliente as nombre_factura,
                               --tvd.id_venta_detalle,
                               (select  string_agg(tvd2.id_factucomcon::text, ',')::varchar as id_venta_detalle from t_venta_detalle tvd2 where tvd2.id_factucom = tl.id_factucom  GROUP BY tvd2.id_factucom ) as id_factucomcon,
                               --data para boleto tienen
                               tfc.nrofac as _desc_liqui,
                               (
                                   SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(venta_detalle)))
                                   FROM (
                                            SELECT *
                                            FROM t_venta_detalle tvd3 where tvd3.id_factucom = tl.id_factucom
                                        ) venta_detalle
                               ) AS _desc_liqui_det
                               --tl.tramo_devolucion as _desc_liqui_det
                        FROM t_liqui tl
                                 INNER JOIN t_factucom tfc on tfc.id_factucom = tl.id_factucom
                                 LEFT JOIN sum_descuentos sd ON sd.id_liquidacion = tl.id_liquidacion
                    )
                SELECT TO_JSON(ROW_TO_JSON(jsonData) :: TEXT) #>> '{}' as json
                into v_json
                from (
                         SELECT
                             (
                                 SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(liqui_recibo)))
                                 FROM
                                     (
                                         SELECT *
                                         FROM t_liqui_recibo tlr
                                     ) liqui_recibo
                             ) as datos,
                             v_count as count

                     ) jsonData;


            elsif v_tipo_tab_liqui = 'PORLIQUI' THEN


                WITH t_liqui AS
                    (
                        SELECT tl.*,
                               liqui_tabla.usr_reg,
                               liqui_tabla.usr_mod,
                               liqui_tabla.desc_tipo_documento,
                               liqui_tabla.desc_tipo_liquidacion,
                               liqui_tabla.desc_punto_venta,
                               liqui_tabla.nro_nota,
                               liqui_tabla.sum_total_descuentos,
                               liqui_tabla.descuentos,
                               liqui_tabla.sum_descuentos
                        FROM decr.tliquidacion tl
                                 INNER JOIN (SELECT * FROM json_populate_recordset(NULL::decr.json_type_liquidacion, v_liqui_json::json)
                        ) liqui_tabla ON liqui_tabla.id_liquidacion = tl.id_liquidacion
                    ), t_liqui_descuento_detalle AS
                    (
                        SELECT tldd.*,tci.codigo, tci.desc_ingas, tdl.importe, 1 as cantidad
                        from decr.tliqui_decuento_detalle tldd
                                 INNER JOIN decr.tdescuento_liquidacion tdl on tdl.id_descuento_liquidacion = tldd.id_descuento_liquidacion
                                 inner join param.tconcepto_ingas tci on tci.id_concepto_ingas = tdl.id_concepto_ingas
                                 INNER JOIN t_liqui tl on tl.id_liquidacion = tldd.id_liquidacion
                    ), sum_descuentos as
                    (
                        SELECT tl.id_liquidacion, sum(tdl.importe) as sum_descuentos
                        FROM decr.tdescuento_liquidacion tdl
                                 inner JOIN param.tconcepto_ingas tci on tci.id_concepto_ingas = tdl.id_concepto_ingas
                                 INNER JOIN t_liqui tl ON tl.id_liquidacion = tdl.id_liquidacion
                        GROUP BY tl.id_liquidacion
                    )
                   , t_liqui_liqui as
                    (
                        SELECT tl.*,
                               sd.sum_descuentos,
                               tl.importe_total AS importe_devolver,
                               tl.importe_total - sd.sum_descuentos AS importe_devolver,
                               tldd.id_liqui_descuento_detalle,
                               (select  string_agg(tldd2.id_liqui_descuento_detalle::text, ',')::varchar as id_liqui_descuento_detalle from t_liqui_descuento_detalle tldd2 where tldd2.id_liquidacion = tl.id_liquidacion  GROUP BY tldd2.id_liquidacion ) as id_liqui_descuento_detalle,
                               tl2.nro_liquidacion as _desc_liqui,
                               (
                                   SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(liqui_descuento_detalle)))
                                   FROM (
                                            SELECT *
                                            FROM t_liqui_descuento_detalle tldd3 where tldd3.id_liquidacion = tl.id_liquidacion
                                        ) liqui_descuento_detalle
                               ) AS _desc_liqui_det
                        FROM t_liqui tl
                                 INNER JOIN decr.tliquidacion tl2 ON tl2.id_liquidacion = tl.id_liquidacion_fk
                                 INNER JOIN t_liqui_descuento_detalle tldd ON tldd.id_liquidacion = tl.id_liquidacion
                                 LEFT JOIN sum_descuentos sd ON sd.id_liquidacion = tl.id_liquidacion
                    )
                SELECT TO_JSON(ROW_TO_JSON(jsonData) :: TEXT) #>> '{}' as json
                into v_json
                from (
                         SELECT
                             (
                                 SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(liqui_liqui)))
                                 FROM
                                     (
                                         SELECT *
                                         FROM t_liqui_liqui tll
                                     ) liqui_liqui
                             ) as datos,
                         v_count as count

                     ) jsonData;


            elsif v_tipo_tab_liqui = 'DEPOSITO' THEN

                WITH t_liqui AS
                    (
                        SELECT tl.*,
                               liqui_tabla.usr_reg,
                               liqui_tabla.usr_mod,
                               liqui_tabla.desc_tipo_documento,
                               liqui_tabla.desc_tipo_liquidacion,
                               liqui_tabla.desc_punto_venta,
                               liqui_tabla.nro_nota,
                               liqui_tabla.sum_total_descuentos,
                               liqui_tabla.descuentos,
                               liqui_tabla.sum_descuentos
                        FROM decr.tliquidacion tl
                                 INNER JOIN (SELECT * FROM json_populate_recordset(NULL::decr.json_type_liquidacion, v_liqui_json::json)
                        ) liqui_tabla ON liqui_tabla.id_liquidacion = tl.id_liquidacion
                        LIMIT 50 OFFSET 0
                    ), sum_descuentos as
                    (
                        SELECT tl.id_liquidacion, sum(tdl.importe) as sum_descuentos
                        FROM decr.tdescuento_liquidacion tdl
                                 inner JOIN param.tconcepto_ingas tci on tci.id_concepto_ingas = tdl.id_concepto_ingas
                                 INNER JOIN t_liqui tl ON tl.id_liquidacion = tdl.id_liquidacion
                        GROUP BY tl.id_liquidacion
                    )
                   , t_liqui_deposito as
                    (
                        SELECT tl.*,
                               sd.sum_descuentos,
                               tl.importe_total AS importe_devolver,
                               td.nro_deposito,
                               td.nro_deposito as _desc_liqui,
                               'monto depositado:' || td.monto_deposito::varchar as _desc_liqui_det
                        FROM t_liqui tl
                                 INNER JOIN obingresos.tdeposito td on td.id_deposito = tl.id_deposito
                                 LEFT JOIN sum_descuentos sd ON sd.id_liquidacion = tl.id_liquidacion
                    )
                SELECT TO_JSON(ROW_TO_JSON(jsonData) :: TEXT) #>> '{}' as json
                into v_json
                from (
                         SELECT
                             (
                                 SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(liqui_deposito)))
                                 FROM
                                     (
                                         SELECT *
                                         FROM t_liqui_deposito td
                                     ) liqui_deposito
                             ) as datos,
                         v_count as count

                     ) jsonData;
            elsif v_tipo_tab_liqui = 'RO' THEN

                WITH t_liqui AS
                    (
                        SELECT tl.*,
                               liqui_tabla.usr_reg,
                               liqui_tabla.usr_mod,
                               liqui_tabla.desc_tipo_documento,
                               liqui_tabla.desc_tipo_liquidacion,
                               liqui_tabla.desc_punto_venta,
                               liqui_tabla.nro_nota,
                               liqui_tabla.sum_total_descuentos,
                               liqui_tabla.descuentos,
                               liqui_tabla.sum_descuentos

                        FROM decr.tliquidacion tl
                                 INNER JOIN (SELECT * FROM json_populate_recordset(NULL::decr.json_type_liquidacion, v_liqui_json::json)
                        ) liqui_tabla ON liqui_tabla.id_liquidacion = tl.id_liquidacion
                    ),t_venta_detalle AS
                    (
                        SELECT string_agg(tvd.id_venta_detalle::text, ',')::varchar as id_venta_detalle, tv.id_venta
                        from vef.tventa_detalle tvd
                                 inner join vef.tventa tv on tv.id_venta = tvd.id_venta
                                 INNER JOIN t_liqui tl on tl.id_venta = tv.id_venta
                        GROUP BY tv.id_venta
                    ), sum_descuentos as
                    (
                        SELECT tl.id_liquidacion, sum(tdl.importe) as sum_descuentos
                        FROM decr.tdescuento_liquidacion tdl
                                 inner JOIN param.tconcepto_ingas tci on tci.id_concepto_ingas = tdl.id_concepto_ingas
                                 INNER JOIN t_liqui tl ON tl.id_liquidacion = tdl.id_liquidacion
                        GROUP BY tl.id_liquidacion
                    )
                   , t_liqui_recibo as
                    (
                        SELECT tl.*,
                               sd.sum_descuentos,
                               tl.importe_total - sd.sum_descuentos as importe_devolver,
                               tv.nro_factura,
                               tv.nombre_factura,
                               tvd.id_venta_detalle
                        FROM t_liqui tl
                                 INNER JOIN vef.tventa tv on tv.id_venta = tl.id_venta
                                 INNER JOIN t_venta_detalle tvd on tvd.id_venta = tv.id_venta
                                 LEFT JOIN sum_descuentos sd ON sd.id_liquidacion = tl.id_liquidacion
                    )
                SELECT TO_JSON(ROW_TO_JSON(jsonData) :: TEXT) #>> '{}' as json
                into v_json
                from (
                         SELECT
                             (
                                 SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(liqui_recibo)))
                                 FROM
                                     (
                                         SELECT *
                                         FROM t_liqui_recibo tlr
                                     ) liqui_recibo
                             ) as datos,
                         v_count as count

                     ) jsonData;

                ELSE -- EJEMPLO MUESTRA TODAS LAS LIQUIDACIONES SIN IMPORTAR SU TIPO DOC


                    WITH t_liqui AS
                             (
                                 SELECT tl.*,
                                        liqui_tabla.usr_reg,
                                        liqui_tabla.usr_mod,
                                        liqui_tabla.desc_tipo_documento,
                                        liqui_tabla.desc_tipo_liquidacion,
                                        liqui_tabla.desc_punto_venta,
                                        liqui_tabla.nro_nota,
                                        liqui_tabla.sum_total_descuentos,
                                        liqui_tabla.descuentos,
                                        liqui_tabla.sum_descuentos

                                 FROM decr.tliquidacion tl
                                          INNER JOIN (SELECT *
                                                      FROM json_populate_recordset(NULL::decr.json_type_liquidacion,
                                                                                   v_liqui_json::json)
                                 ) liqui_tabla ON liqui_tabla.id_liquidacion = tl.id_liquidacion
                             )SELECT TO_JSON(ROW_TO_JSON(jsonData) :: TEXT) #>> '{}' as json
                    into v_json
                    from (
                             SELECT
                                 (
                                     SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(liqui)))
                                     FROM
                                         (
                                             SELECT *
                                             FROM t_liqui tl
                                         ) liqui
                                 ) as datos,
                                 v_count as count

                         ) jsonData;

            END IF;






            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'json',v_json);
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje',v_json);

            --Devuelve la respuesta
            return v_resp;

        end;


    else

        raise exception 'Transaccion inexistente: %',p_transaccion;

    end if;

EXCEPTION

    WHEN OTHERS THEN
        v_resp='';
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
        v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
        v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
        raise exception '%',v_resp;

END;
$BODY$
    LANGUAGE 'plpgsql' VOLATILE
                       COST 100;
ALTER FUNCTION "decr"."ft_liquidacion_json"(integer, integer, character varying, character varying) OWNER TO postgres;
