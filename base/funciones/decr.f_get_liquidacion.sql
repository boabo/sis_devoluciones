CREATE OR REPLACE FUNCTION decr.f_get_liquidacion (
    p_params json
)
    RETURNS json AS
$body$
/**************************************************************************

 FUNCION:         decr.f_get_liquidacion
 DESCRIPCION:     obtiene datos de la liquidacion
 AUTOR:         FAVIO FIGUEROA(FFP)
 FECHA:            19/07/2010
 COMENTARIOS:
***************************************************************************
 HISTORIA DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:

***************************************************************************/
DECLARE

    v_nombre_funcion    varchar;
    v_resp              varchar;
    v_liqui_json        json;
    v_id_liquidacion_array int[];
    v_json json;
    v_count integer;
    v_ids_factucom varchar;

    v_id_liquidacion integer DEFAULT p_params->>'id_liquidacion';
    v_tipo_tab_liqui varchar DEFAULT p_params->>'tipo_tab_liqui';
    v_filtro_value varchar DEFAULT UPPER(p_params->>'filtro_value');
    v_query_value varchar DEFAULT p_params->>'query_value';


BEGIN



    v_nombre_funcion:='decr.f_get_liquidacion';

    if(v_id_liquidacion is not null) then


        select ttdl.tipo_documento
        into v_tipo_tab_liqui
        from decr.ttipo_doc_liquidacion ttdl
                 inner join decr.tliquidacion tl on tl.id_tipo_doc_liquidacion = ttdl.id_tipo_doc_liquidacion
        where tl.id_liquidacion = v_id_liquidacion;
    END IF;


    if(p_params->>'administradora' is not null and p_params->>'fecha_ini' is not null and p_params->>'fecha_fin' is not null) then

        --raise EXCEPTION '%',p_params->'fecha_ini';
        --obtenemos todas las liquidaciones que tengan forma de pago con tarjeta de credito y ademas entre un
        -- determinado rango de fechas y con el tipo de administradora
        SELECT array_agg(tl.id_liquidacion)
        into v_id_liquidacion_array
        FROM decr.tliquidacion tl
                 inner join decr.tliqui_forma_pago tlfp on tlfp.id_liquidacion = tl.id_liquidacion
        where tlfp.administradora = p_params->>'administradora'::varchar
          AND tl.fecha_reg::date BETWEEN cast(p_params->>'fecha_ini' as date) and cast(p_params->>'fecha_fin' as date);

        --RAISE EXCEPTION '%',v_id_liquidacion_array;
        IF v_id_liquidacion_array is null then
            RAISE EXCEPTION '%', 'NO EXISTE DATOS PARA ESTOS PARAMETROS DE BUSQUEDA';
        END IF;

    END IF;
    if(p_params->>'estado' is not null and p_params->>'fecha_ini' is not null and p_params->>'fecha_fin' is not null) then

        --raise EXCEPTION '%',p_params->'fecha_ini';
        --obtenemos todas las liquidaciones que tengan forma de pago con tarjeta de credito y ademas entre un
        -- determinado rango de fechas y con el tipo de administradora
        SELECT array_agg(tl.id_liquidacion)
        into v_id_liquidacion_array
        FROM decr.tliquidacion tl
        where tl.estado = p_params->>'estado'::varchar
          AND tl.fecha_reg::date BETWEEN cast(p_params->>'fecha_ini' as date) and cast(p_params->>'fecha_fin' as date);

        --RAISE EXCEPTION '%',v_id_liquidacion_array;
        IF v_id_liquidacion_array is null then
            RAISE EXCEPTION '%', 'NO EXISTE DATOS PARA ESTOS PARAMETROS DE BUSQUEDA CON ESTADO';
        END IF;

    END IF;


    SELECT count(tl.id_liquidacion)
    INTO v_count
    FROM decr.tliquidacion tl
    INNER JOIN segu.tusuario usu1 ON usu1.id_usuario = tl.id_usuario_reg
    LEFT JOIN segu.tusuario usu2 ON usu2.id_usuario = tl.id_usuario_mod
    INNER JOIN decr.ttipo_doc_liquidacion ttdl ON ttdl.id_tipo_doc_liquidacion = tl.id_tipo_doc_liquidacion
    INNER JOIN decr.ttipo_liquidacion ttl ON ttl.id_tipo_liquidacion = tl.id_tipo_liquidacion
    INNER JOIN vef.tpunto_venta pv ON pv.id_punto_venta = tl.id_punto_venta
    INNER JOIN vef.tsucursal su on su.id_sucursal = pv.id_sucursal
         --LEFT JOIN decr.tnota nota ON nota.id_liquidacion::integer = tl.id_liquidacion
    WHERE (CASE WHEN v_id_liquidacion IS NOT NULL THEN tl.id_liquidacion = v_id_liquidacion ELSE 1 = 1 END)
    AND (CASE WHEN v_tipo_tab_liqui IS NOT NULL THEN ttdl.tipo_documento = v_tipo_tab_liqui ELSE 1 = 1 END)
    AND (CASE WHEN v_filtro_value IS NOT NULL THEN UPPER(tl.nro_liquidacion) LIKE '%' || v_filtro_value || '%'
                                                       or UPPER(tl.pagar_a_nombre) LIKE '%' || v_filtro_value || '%'
        ELSE 1 = 1 END)
    AND (CASE WHEN v_query_value IS NOT NULL THEN tl.nro_liquidacion LIKE '%' || v_query_value || '%' ELSE 1 = 1 END)
    AND (CASE WHEN v_id_liquidacion_array IS NOT NULL THEN tl.id_liquidacion = ANY (v_id_liquidacion_array) ELSE 1 = 1 END);



    with t_liqui
             AS (
            SELECT tl.id_liquidacion,
                   usu1.cuenta                          AS usr_reg,
                   usu2.cuenta                          AS usr_mod,
                   ttdl.tipo_documento                  AS desc_tipo_documento,
                   ttl.tipo_liquidacion                 AS desc_tipo_liquidacion,
                   pv.codigo                            AS codigo_punto_venta,
                   pv.nombre                            AS desc_punto_venta,
                   su.id_sucursal                       AS id_sucursal,
                   tl.id_proceso_wf_factura            AS id_proceso_wf_factura,
                   tl.id_factucom -- solo para el tipo fac-antigua
            FROM decr.tliquidacion tl
                     INNER JOIN segu.tusuario usu1 ON usu1.id_usuario = tl.id_usuario_reg
                     LEFT JOIN segu.tusuario usu2 ON usu2.id_usuario = tl.id_usuario_mod
                     INNER JOIN decr.ttipo_doc_liquidacion ttdl ON ttdl.id_tipo_doc_liquidacion = tl.id_tipo_doc_liquidacion
                     INNER JOIN decr.ttipo_liquidacion ttl ON ttl.id_tipo_liquidacion = tl.id_tipo_liquidacion
                     INNER JOIN vef.tpunto_venta pv ON pv.id_punto_venta = tl.id_punto_venta
                     INNER JOIN vef.tsucursal su on su.id_sucursal = pv.id_sucursal
                 --LEFT JOIN decr.tnota nota ON nota.id_liquidacion::integer = tl.id_liquidacion
            WHERE (case when v_id_liquidacion is not null then tl.id_liquidacion = v_id_liquidacion else 1=1 end)
              AND (case when v_tipo_tab_liqui is not null then ttdl.tipo_documento = v_tipo_tab_liqui else 1=1 end)
              AND (CASE WHEN v_filtro_value IS NOT NULL THEN tl.nro_liquidacion LIKE '%' || v_filtro_value || '%'
                                                        or upper(tl.pagar_a_nombre) LIKE '%' || upper(v_filtro_value) || '%'
                  ELSE 1 = 1 END)
              AND (CASE WHEN v_query_value is not null then tl.nro_liquidacion like '%' ||v_query_value|| '%' else 1=1 end)
              AND (CASE WHEN v_id_liquidacion_array is not null then tl.id_liquidacion = any (v_id_liquidacion_array) else 1=1 end)
            order by tl.id_liquidacion DESC
            LIMIT cast(p_params->>'cantidad' as integer) OFFSET cast(p_params->>'puntero' as integer)

        ),
         t_sum_descuentos as
             (
                 SELECT tl.id_liquidacion, tdl.tipo, sum(tdl.importe) as sum_total_por_tipo, tci.tipo_descuento
                 FROM decr.tdescuento_liquidacion tdl
                          inner JOIN param.tconcepto_ingas tci on tci.id_concepto_ingas = tdl.id_concepto_ingas
                     and (tci.tipo_descuento != 'HAY NOTA' or tci.tipo_descuento is null)
                          INNER JOIN t_liqui tl ON tl.id_liquidacion = tdl.id_liquidacion
                 GROUP BY tl.id_liquidacion, tdl.tipo, tci.tipo_descuento
             ),
         t_descuentos AS (
             SELECT tci.tipo_descuento, tdl.id_descuento_liquidacion, tci.codigo, tdl.id_liquidacion, tdl.id_concepto_ingas, tdl.importe, tci.desc_ingas, tdl.tipo, tci_padre.desc_ingas desc_ingas_fk, tci.id_concepto_ingas_fk
             FROM decr.tdescuento_liquidacion tdl
                      INNER JOIN param.tconcepto_ingas tci ON tci.id_concepto_ingas = tdl.id_concepto_ingas
                      inner join t_liqui tl on tl.id_liquidacion = tdl.id_liquidacion
                      left join param.tconcepto_ingas tci_padre on tci_padre.id_concepto_ingas = tci.id_concepto_ingas_fk
         ),
         t_liqui_forma_pago AS (
             SELECT tlfp.*, tmpw.name as desc_medio_pago_pw, tfpp.name as desc_forma_pago_pw
             FROM decr.tliqui_forma_pago tlfp
                      inner join t_liqui tl on tl.id_liquidacion = tlfp.id_liquidacion
                      inner join obingresos.tmedio_pago_pw tmpw on tmpw.id_medio_pago_pw = tlfp.id_medio_pago
                      inner join obingresos.tforma_pago_pw tfpp on tfpp.id_forma_pago_pw = tmpw.forma_pago_id
         ),
         t_nota AS (
             SELECT nota.*
             FROM decr.tnota nota
                      inner join t_liqui tl on tl.id_liquidacion::integer = nota.id_liquidacion::integer
         ),
         t_factura_pagada AS (
             SELECT tv.nro_factura, tl.id_proceso_wf_factura
             FROM vef.tventa tv
                      inner join t_liqui tl on tl.id_proceso_wf_factura::integer = tv.id_proceso_wf::integer
         )
    SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(liqui)))
    INTO v_liqui_json
    FROM
        (
            SELECT *,
                   (
                       select sum(importe)
                       from t_descuentos td2
                       where td2.id_liquidacion = tl.id_liquidacion
                         and (td2.tipo_descuento != 'HAY NOTA' or td2.tipo_descuento is null)
                    ) as sum_total_descuentos,
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
                   ) AS liqui_forma_pago,
                   (
                       SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(nota)))
                       FROM (
                                SELECT *
                                FROM t_nota tn WHERE tn.id_liquidacion::integer = tl.id_liquidacion::integer
                            ) nota
                   ) AS notas,
                   (
                       SELECT TO_JSON(factura_pagada) -- solo json por que devolvera un objeto
                       FROM (
                                SELECT *
                                FROM t_factura_pagada tf WHERE tf.id_proceso_wf_factura::integer = tl.id_proceso_wf_factura::integer
                            ) factura_pagada
                   ) AS factura_pagada
            FROM t_liqui tl
        ) liqui;


    --agregamos datos dependiendo el tipo de documento

    IF v_tipo_tab_liqui = 'BOLEMD' THEN


        WITH t_liqui AS
            (
                SELECT tl.*,
                       liqui_tabla.usr_reg,
                       liqui_tabla.usr_mod,
                       liqui_tabla.desc_tipo_documento,
                       liqui_tabla.desc_tipo_liquidacion,
                       liqui_tabla.desc_punto_venta,
                       liqui_tabla.codigo_punto_venta,
                       liqui_tabla.id_sucursal,
                       liqui_tabla.nro_nota,
                       liqui_tabla.sum_total_descuentos,
                       liqui_tabla.descuentos,
                       liqui_tabla.sum_descuentos,
                       liqui_tabla.liqui_forma_pago,
                       liqui_tabla.notas,
                       liqui_tabla.factura_pagada
                FROM decr.tliquidacion tl
                         INNER JOIN (SELECT * FROM json_populate_recordset(NULL::decr.json_type_liquidacion, v_liqui_json::json)
                ) liqui_tabla ON liqui_tabla.id_liquidacion = tl.id_liquidacion
            ), t_liqui_boleto_recursivo AS
            (
                SELECT tlbr.*
                FROM decr.tliqui_boleto_recursivo tlbr
                         inner join t_liqui tl on tl.id_liquidacion = tlbr.id_liquidacion
            )
           , t_liqui_boleto as
            (
                SELECT tl.*,
                       tl.importe_total - tl.importe_tramo_utilizado as importe_devolver_sin_descuentos,
                       tl.importe_total - tl.importe_tramo_utilizado - tl.sum_total_descuentos as importe_devolver,
                       tlb.data_stage->>'ticketNumber' as desc_nro_boleto,
                       tlb.data_stage->>'FOID'::varchar as nro_nit,
                       tlb.data_stage->>'FOID' AS razon,
                       tlb.data_stage->>'issueDate' as fecha_fac,
                       tlb.data_stage->>'totalAmount' as total,
                       1 as nro_aut,
                       tlb.data_stage->>'ticketNumber' as nro_fac,
                       concat(tlb.data_stage->>'ticketNumber','/',tl.tramo_devolucion):: varchar as concepto,
                       'BOLETO'::VARCHAR AS tipo,
                       tlb.data_stage->>'totalAmount' AS precio_unitario,
                       tlb.data_stage->>'totalAmount' AS importe_original,
                       tlb.data_stage->>'ticketNumber' as id, -- esto antes era el id_boleto
                       1::integer as cantidad,
                       --data para boleto tienen

                       (
                           SELECT TO_JSON(boleto) -- solo json por que devolvera un objeto
                           FROM (
                                    SELECT *
                                    FROM obingresos.tboleto tb2 where tb2.id_boleto = tl.id_boleto
                                ) boleto
                       ) AS data_boleto,
                       (
                           SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(liqui_boleto_recursivo)))
                           FROM
                               (
                                   select * from t_liqui_boleto_recursivo t_lbr
                                   where t_lbr.id_liquidacion = tl.id_liquidacion
                               ) liqui_boleto_recursivo
                       ) as boletos_recursivo,

                       tlb.data_stage->>'ticketNumber' as _desc_liqui,
                       tl.tramo_devolucion as _desc_liqui_det,
                       tl.tramo AS _detalle_documento_original,
                       tlb.data_stage->>'ticketNumber' as _liqui_importe_doc_original,
                       tlb.data_stage->>'issueDate' as _liqui_fecha_doc_original,
                       tlb.data_stage->>'ticketNumber' as _liqui_nro_doc_original,
                       1 as _liqui_nro_aut_doc_original,
                       tlb.data_stage->>'passengerName' as _liqui_nombre_doc_original,
                       tlb.data_stage->>'reservepointOfSale' as _liqui_oficina_emisora_original,
                       tlb.data_stage->>'issueAgencyCode' as _liqui_codigo_agencia_doc_original,
                       tlb.data_stage

                FROM t_liqui tl
                         INNER JOIN decr.tliqui_boleto tlb on tlb.id_liquidacion = tl.id_liquidacion

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
                       liqui_tabla.codigo_punto_venta,
                       liqui_tabla.id_sucursal,
                       liqui_tabla.nro_nota,
                       liqui_tabla.sum_total_descuentos,
                       liqui_tabla.descuentos,
                       liqui_tabla.sum_descuentos,
                       liqui_tabla.liqui_forma_pago,
                       liqui_tabla.notas,
                       liqui_tabla.factura_pagada
                FROM decr.tliquidacion tl
                         INNER JOIN (SELECT * FROM json_populate_recordset(NULL::decr.json_type_liquidacion, v_liqui_json::json)
                ) liqui_tabla ON liqui_tabla.id_liquidacion = tl.id_liquidacion
            ),t_venta_detalle_original AS
            (
                SELECT tvd.*, tci.codigo as desc_codigo, tci.desc_ingas, tci.desc_ingas as _concepto, 1 as _cantidad, tvd.precio as _importe, tvd.id_venta_detalle as _id
                from vef.tventa_detalle tvd
                         inner join vef.tventa tv on tv.id_venta = tvd.id_venta
                         inner join param.tconcepto_ingas tci on tci.id_concepto_ingas = tvd.id_producto
            ),t_venta_detalle AS
            (
                SELECT tvdo.*
                from t_venta_detalle_original tvdo
                INNER JOIN t_liqui tl on tl.id_venta = tvdo.id_venta
            ), sum_descuentos as
            (
                SELECT tl.id_liquidacion, sum(tdl.importe) as sum_descuentos
                FROM decr.tdescuento_liquidacion tdl
                         inner JOIN param.tconcepto_ingas tci on tci.id_concepto_ingas = tdl.id_concepto_ingas AND tci.tipo_descuento != 'HAY NOTA'
                         INNER JOIN t_liqui tl ON tl.id_liquidacion = tdl.id_liquidacion
                GROUP BY tl.id_liquidacion
            )
           , t_liqui_fac as
            (
                SELECT tl.*,
                       sd.sum_descuentos,
                       tv.total_venta - (coalesce(sd.sum_descuentos, 0)) as importe_devolver,
                       tv.nro_factura,
                       tv.nit,
                       tv.nombre_factura,
                       tv.fecha as fecha_doc_original, -- este campo deberia ser la fecha del documento vinculado a la liquidacion en este caso la venta (factura)
                       --tvd.id_venta_detalle,
                       (select  string_agg(tvd2.id_venta_detalle::text, ',')::varchar as id_venta_detalle from t_venta_detalle tvd2 where tvd2.id_venta = tl.id_venta  GROUP BY tvd2.id_venta ) as id_venta_detalle,
                       (select  sum(tvd3.precio) from t_venta_detalle tvd3 where tvd3.id_venta = tl.id_venta  GROUP BY tvd3.id_venta ) as sum_venta_seleccionados,
                       --data para boleto tienen
                       tv.nro_factura as _desc_liqui,
                       (
                           SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(venta_detalle)))
                           FROM (
                                    SELECT *
                                    FROM t_venta_detalle tvd3 where tvd3.id_venta = tl.id_venta
                                ) venta_detalle
                       ) AS _desc_liqui_det,
                       (
                           SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(venta_detalle_original)))
                           FROM (
                                    SELECT *
                                    FROM t_venta_detalle_original tvdo where tvdo.id_venta = tl.id_venta
                                ) venta_detalle_original
                       ) AS _detalle_documento_original,
                       tv.total_venta as _liqui_importe_doc_original,
                       tv.fecha as _liqui_fecha_doc_original,
                       tv.nro_factura as _liqui_nro_doc_original,
                       td.nroaut as _liqui_nro_aut_doc_original,
                       tv.nombre_factura as _liqui_nombre_doc_original,
                       concat(ts.nombre, '(', tpv.nombre, ')') as _liqui_oficina_emisora_original,
                       tpv.codigo as _liqui_codigo_agencia_doc_original

                       --tl.tramo_devolucion as _desc_liqui_det
                FROM t_liqui tl
                         INNER JOIN vef.tventa tv on tv.id_venta = tl.id_venta
                    INNER JOIN vef.tpunto_venta tpv on tpv.id_punto_venta = tv.id_punto_venta
                    INNER JOIN vef.tsucursal ts on ts.id_sucursal = tpv.id_sucursal
                         inner join vef.tdosificacion td on td.id_dosificacion = tv.id_dosificacion
                         INNER JOIN t_venta_detalle tvd on tvd.id_venta = tv.id_venta
                         LEFT JOIN sum_descuentos sd ON sd.id_liquidacion = tl.id_liquidacion
            )
        SELECT TO_JSON(ROW_TO_JSON(jsonData) :: TEXT) #>> '{}' as json
        into v_json
        from (
                 SELECT
                     (
                         SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(liqui_factura)))
                         FROM
                             (
                                 SELECT *
                                 FROM t_liqui_fac tlf
                             ) liqui_factura
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
                order by tl.id_liquidacion desc
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

    elsif v_tipo_tab_liqui = 'LIQUIMAN' THEN


        WITH t_liqui AS
                 (
                     SELECT tl.*,
                            liqui_tabla.usr_reg,
                            liqui_tabla.usr_mod,
                            liqui_tabla.desc_tipo_documento,
                            liqui_tabla.desc_tipo_liquidacion,
                            liqui_tabla.desc_punto_venta,
                            liqui_tabla.codigo_punto_venta,
                            liqui_tabla.id_sucursal,
                            liqui_tabla.nro_nota,
                            liqui_tabla.sum_total_descuentos,
                            liqui_tabla.descuentos,
                            liqui_tabla.sum_descuentos,
                            liqui_tabla.liqui_forma_pago,
                            liqui_tabla.notas,
                            liqui_tabla.factura_pagada
                     FROM decr.tliquidacion tl
                              INNER JOIN (SELECT * FROM json_populate_recordset(NULL::decr.json_type_liquidacion, v_liqui_json::json)
                     ) liqui_tabla ON liqui_tabla.id_liquidacion = tl.id_liquidacion
                 ),t_liquiman_detalle AS
                 (
                     SELECT tlm.tipo_manual, tlmd.*
                     from decr.tliqui_manual_detalle tlmd
                              inner join decr.tliqui_manual tlm on tlm.id_liqui_manual = tlmd.id_liqui_manual
                              INNER JOIN t_liqui tl on tl.id_liqui_manual = tlm.id_liqui_manual
                 ),
             t_sum_totales_manual as
                 (
                     SELECT tld.id_liqui_manual, sum(tld.importe_original) as importe_original, sum(tld.importe_devolver) as importe_devolver_sin_descuentos
                     FROM t_liquiman_detalle tld
                     GROUP BY tld.id_liqui_manual
                 )
                , t_liquiman as
                 (
                     SELECT  tl.*,
                             tstm.importe_devolver_sin_descuentos - tl.sum_total_descuentos as importe_devolver,
                             tstm.importe_original,
                             tstm.importe_devolver_sin_descuentos,
                             tlm.tipo_manual,
                             tlm.tipo_manual as _desc_liqui,
                             (
                                 SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(liquiman_detalle)))
                                 FROM (
                                          SELECT *
                                          FROM t_liquiman_detalle tld where tld.id_liqui_manual = tl.id_liqui_manual
                                      ) liquiman_detalle
                             ) AS _desc_liqui_det,
                             null as _detalle_documento_original,

                             null AS _detalle_documento_original,
                             null as _liqui_importe_doc_original,
                             null as _liqui_fecha_doc_original,
                             null as _liqui_nro_doc_original,
                             null as _liqui_nro_aut_doc_original,
                             tlm.razon_nombre_doc_org as _liqui_nombre_doc_original
                     FROM t_liqui tl
                              INNER JOIN decr.tliqui_manual tlm on tlm.id_liqui_manual = tl.id_liqui_manual
                              INNER JOIN t_sum_totales_manual tstm on tstm.id_liqui_manual = tlm.id_liqui_manual
                 )
        SELECT TO_JSON(ROW_TO_JSON(jsonData) :: TEXT) #>> '{}' as json
        into v_json
        from (
                 SELECT
                     (
                         SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(liqui_manual)))
                         FROM
                             (
                                 SELECT *
                                 FROM t_liquiman tl
                             ) liqui_manual
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
                            liqui_tabla.sum_descuentos,
                            liqui_tabla.liqui_forma_pago,
                            liqui_tabla.notas
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


    return v_json;


EXCEPTION

    WHEN OTHERS THEN

        v_resp='';
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
        v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
        v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
        raise exception '%',v_resp;

END;
$body$
    LANGUAGE 'plpgsql'
    VOLATILE
    CALLED ON NULL INPUT
    SECURITY INVOKER
    COST 100;