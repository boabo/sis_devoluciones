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
            --Sentencia de la eliminacion

            select count(tl.id_liquidacion)
            into v_count
            FROM decr.tliquidacion tl
                INNER JOIN segu.tusuario usu1 ON usu1.id_usuario = tl.id_usuario_reg
                LEFT JOIN segu.tusuario usu2 ON usu2.id_usuario = tl.id_usuario_mod
                INNER JOIN decr.ttipo_doc_liquidacion ttdl ON ttdl.id_tipo_doc_liquidacion = tl.id_tipo_doc_liquidacion
                INNER JOIN decr.ttipo_liquidacion ttl ON ttl.id_tipo_liquidacion = tl.id_tipo_liquidacion
                INNER JOIN vef.tpunto_venta pv ON pv.id_punto_venta = tl.id_punto_venta
                LEFT JOIN decr.tnota nota ON nota.id_liquidacion::integer = tl.id_liquidacion
            WHERE ttdl.tipo_documento = v_parametros.tipo_tab_liqui;


            IF v_parametros.tipo_tab_liqui = 'BOLEMD' THEN


                WITH t_liqui AS
                    (
                        SELECT tl.*,
                               usu1.cuenta                          AS usr_reg,
                               usu2.cuenta                          AS usr_mod,
                               ttdl.tipo_documento                  AS desc_tipo_documento,
                               ttl.tipo_liquidacion                 AS desc_tipo_liquidacion,
                               pv.nombre                            AS desc_punto_venta,
                               nota.nro_nota
                        FROM decr.tliquidacion tl
                                 INNER JOIN segu.tusuario usu1 ON usu1.id_usuario = tl.id_usuario_reg
                                 LEFT JOIN segu.tusuario usu2 ON usu2.id_usuario = tl.id_usuario_mod
                                 INNER JOIN decr.ttipo_doc_liquidacion ttdl ON ttdl.id_tipo_doc_liquidacion = tl.id_tipo_doc_liquidacion
                                 INNER JOIN decr.ttipo_liquidacion ttl ON ttl.id_tipo_liquidacion = tl.id_tipo_liquidacion
                                 INNER JOIN vef.tpunto_venta pv ON pv.id_punto_venta = tl.id_punto_venta
                                 LEFT JOIN decr.tnota nota ON nota.id_liquidacion::integer = tl.id_liquidacion
                        WHERE ttdl.tipo_documento = 'BOLEMD'
                        LIMIT 50 OFFSET 0
                    ), sum_descuentos as
                    (
                        SELECT tl.id_liquidacion, sum(tdl.importe) as sum_descuentos
                        FROM decr.tdescuento_liquidacion tdl
                                 inner JOIN param.tconcepto_ingas tci on tci.id_concepto_ingas = tdl.id_concepto_ingas
                                 INNER JOIN t_liqui tl ON tl.id_liquidacion = tdl.id_liquidacion
                        GROUP BY tl.id_liquidacion
                    )
                   , t_liqui_boleto as
                    (
                        SELECT tl.*,
                               sd.sum_descuentos,
                               tl.importe_total - sd.sum_descuentos as importe_devolver,
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
                               1::integer as cantidad
                        FROM t_liqui tl
                                 INNER JOIN obingresos.tboleto tb on tb.id_boleto = tl.id_boleto
                                 LEFT JOIN sum_descuentos sd ON sd.id_liquidacion = tl.id_liquidacion
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

            elsif v_parametros.tipo_tab_liqui = 'FACCOM' THEN



                WITH t_venta_detalle AS
                         (
                             SELECT string_agg(tvd.id_venta_detalle::text, ',')::varchar as id_venta_detalle, tv.id_venta
                             from vef.tventa_detalle tvd
                                      inner join vef.tventa tv on tv.id_venta = tvd.id_venta
                                      INNER JOIN decr.tliquidacion tl on tl.id_venta = tv.id_venta
                             GROUP BY tv.id_venta
                         ), sum_descuentos as
                         (
                             SELECT tl.id_liquidacion, sum(tdl.importe) as sum_descuentos
                             FROM decr.tdescuento_liquidacion tdl
                                      inner JOIN param.tconcepto_ingas tci on tci.id_concepto_ingas = tdl.id_concepto_ingas
                                      INNER JOIN decr.tliquidacion tl ON tl.id_liquidacion = tdl.id_liquidacion
                             GROUP BY tl.id_liquidacion
                         ), t_liqui_faccom as
                         (
                             SELECT tl.*,
                                    usu1.cuenta as usr_reg,
                                    usu2.cuenta as usr_mod,
                                    ttdl.tipo_documento as desc_tipo_documento,
                                    ttl.tipo_liquidacion as desc_tipo_liquidacion,
                                    pv.nombre as desc_punto_venta,
                                    nota.nro_nota,
                                    sd.sum_descuentos,
                                    tl.importe_total - sd.sum_descuentos as importe_devolver,
                                    tv.nro_factura,
                                    tv.nombre_factura,
                                    tvd.id_venta_detalle
                             FROM decr.tliquidacion tl
                                      inner join segu.tusuario usu1 on usu1.id_usuario = tl.id_usuario_reg
                                      left join segu.tusuario usu2 on usu2.id_usuario = tl.id_usuario_mod
                                      inner join decr.ttipo_doc_liquidacion ttdl on ttdl.id_tipo_doc_liquidacion = tl.id_tipo_doc_liquidacion
                                      inner join decr.ttipo_liquidacion ttl on ttl.id_tipo_liquidacion = tl.id_tipo_liquidacion
                                      inner join vef.tpunto_venta pv on pv.id_punto_venta = tl.id_punto_venta
                                      left JOIN sum_descuentos sd on sd.id_liquidacion = tl.id_liquidacion
                                      INNER JOIN vef.tventa tv on tv.id_venta = tl.id_venta
                                      INNER JOIN t_venta_detalle tvd on tvd.id_venta = tv.id_venta
                                      left join decr.tnota nota on nota.id_liquidacion::integer = tl.id_liquidacion
                             LIMIT 50 OFFSET 0
                         )
                SELECT TO_JSON(ROW_TO_JSON(jsonData) :: TEXT) #>> '{}' as json
                into v_json
                from (
                         SELECT
                             (
                                 SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(liqui_faccom)))
                                 FROM
                                     (
                                         SELECT *
                                         FROM t_liqui_faccom tl
                                     ) liqui_faccom
                             ) as datos,
                         v_count as count

                     ) jsonData;


            elsif v_parametros.tipo_tab_liqui = 'PORLIQUI' THEN


                WITH t_liqui AS
                    (
                        SELECT tl.*,
                               usu1.cuenta                          AS usr_reg,
                               usu2.cuenta                          AS usr_mod,
                               ttdl.tipo_documento                  AS desc_tipo_documento,
                               ttl.tipo_liquidacion                 AS desc_tipo_liquidacion,
                               pv.nombre                            AS desc_punto_venta,
                               nota.nro_nota
                        FROM decr.tliquidacion tl
                                 INNER JOIN segu.tusuario usu1 ON usu1.id_usuario = tl.id_usuario_reg
                                 LEFT JOIN segu.tusuario usu2 ON usu2.id_usuario = tl.id_usuario_mod
                                 INNER JOIN decr.ttipo_doc_liquidacion ttdl ON ttdl.id_tipo_doc_liquidacion = tl.id_tipo_doc_liquidacion
                                 INNER JOIN decr.ttipo_liquidacion ttl ON ttl.id_tipo_liquidacion = tl.id_tipo_liquidacion
                                 INNER JOIN vef.tpunto_venta pv ON pv.id_punto_venta = tl.id_punto_venta
                                 LEFT JOIN decr.tnota nota ON nota.id_liquidacion::integer = tl.id_liquidacion
                        WHERE ttdl.tipo_documento = 'PORLIQUI'
                        LIMIT 50 OFFSET 0
                    ), t_liqui_descuento_detalle AS
                    (
                        SELECT string_agg(tldd.id_liqui_descuento_detalle::text, ',')::varchar as id_liqui_descuento_detalle, tldd.id_liquidacion
                        from decr.tliqui_decuento_detalle tldd
                                 INNER JOIN decr.tdescuento_liquidacion tdl on tdl.id_descuento_liquidacion = tldd.id_descuento_liquidacion
                                 inner join param.tconcepto_ingas tci on tci.id_concepto_ingas = tdl.id_concepto_ingas
                                 INNER JOIN t_liqui tl on tl.id_liquidacion = tdl.id_liquidacion
                        GROUP BY tldd.id_liquidacion
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
                               tldd.id_liqui_descuento_detalle
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


            elsif v_parametros.tipo_tab_liqui = 'DEPOSITO' THEN

                WITH t_liqui AS
                    (
                        SELECT tl.*,
                               usu1.cuenta                          AS usr_reg,
                               usu2.cuenta                          AS usr_mod,
                               ttdl.tipo_documento                  AS desc_tipo_documento,
                               ttl.tipo_liquidacion                 AS desc_tipo_liquidacion,
                               pv.nombre                            AS desc_punto_venta,
                               nota.nro_nota
                        FROM decr.tliquidacion tl
                                 INNER JOIN segu.tusuario usu1 ON usu1.id_usuario = tl.id_usuario_reg
                                 LEFT JOIN segu.tusuario usu2 ON usu2.id_usuario = tl.id_usuario_mod
                                 INNER JOIN decr.ttipo_doc_liquidacion ttdl ON ttdl.id_tipo_doc_liquidacion = tl.id_tipo_doc_liquidacion
                                 INNER JOIN decr.ttipo_liquidacion ttl ON ttl.id_tipo_liquidacion = tl.id_tipo_liquidacion
                                 INNER JOIN vef.tpunto_venta pv ON pv.id_punto_venta = tl.id_punto_venta
                                 LEFT JOIN decr.tnota nota ON nota.id_liquidacion::integer = tl.id_liquidacion
                        WHERE ttdl.tipo_documento = 'DEPOSITO'
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
                               tl.importe_total AS importe_devolver
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
            elsif v_parametros.tipo_tab_liqui = 'RO' THEN

                WITH t_liqui AS
                    (
                        SELECT tl.*,
                               usu1.cuenta                          AS usr_reg,
                               usu2.cuenta                          AS usr_mod,
                               ttdl.tipo_documento                  AS desc_tipo_documento,
                               ttl.tipo_liquidacion                 AS desc_tipo_liquidacion,
                               pv.nombre                            AS desc_punto_venta,
                               nota.nro_nota
                        FROM decr.tliquidacion tl
                                 INNER JOIN segu.tusuario usu1 ON usu1.id_usuario = tl.id_usuario_reg
                                 LEFT JOIN segu.tusuario usu2 ON usu2.id_usuario = tl.id_usuario_mod
                                 INNER JOIN decr.ttipo_doc_liquidacion ttdl ON ttdl.id_tipo_doc_liquidacion = tl.id_tipo_doc_liquidacion
                                 INNER JOIN decr.ttipo_liquidacion ttl ON ttl.id_tipo_liquidacion = tl.id_tipo_liquidacion
                                 INNER JOIN vef.tpunto_venta pv ON pv.id_punto_venta = tl.id_punto_venta
                                 LEFT JOIN decr.tnota nota ON nota.id_liquidacion::integer = tl.id_liquidacion
                        WHERE ttdl.tipo_documento = 'RO'
                        LIMIT 50 OFFSET 0
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
