CREATE OR REPLACE FUNCTION decr.f_get_liquidacion_por_boleto (
    p_liqui_json json
)
    RETURNS json AS
$body$
/**************************************************************************

 FUNCION:         decr.f_get_liquidacion_por_boleto
 DESCRIPCION:     obtiene datos de la liquidacion por boleto apartir de los datos json ejecutados anteriormente
 AUTOR:         FAVIO FIGUEROA(FFP)
 FECHA:            26/11/2021
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
    v_query_value varchar DEFAULT UPPER(p_params->>'query_value');


BEGIN



    v_nombre_funcion:='decr.f_get_liquidacion_por_boleto';



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
                   tl.importe_total - tl.importe_tramo_utilizado - COALESCE(tl.sum_total_descuentos,0) as importe_devolver,
                   tlb.data_stage->>'ticketNumber' as desc_nro_boleto,
                   tlb.data_stage->>'nit'::varchar as nro_nit,
                   tlb.data_stage->>'bussinesName' AS razon,
                   tlb.data_stage->>'bussinesName' AS razon_social,
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
            WHERE (
                CASE WHEN v_filtro_value IS NOT NULL
                         THEN UPPER(tl.nro_liquidacion) LIKE '%' || v_filtro_value || '%'
                        or UPPER(tl.pagar_a_nombre) LIKE '%' || v_filtro_value || '%'
                        or upper(cast(tlb.data_stage->>'ticketNumber' as varchar)) like '%' || v_filtro_value || '%'
                        or upper(cast(tlb.data_stage->>'FOID' as varchar)) like '%' || v_filtro_value || '%'
                        or upper(cast(tlb.data_stage->>'bussinesName' as varchar)) like '%' || v_filtro_value || '%'
                        or upper(cast(tlb.data_stage->>'nit' as varchar)) like '%' || v_filtro_value || '%'
                     ELSE 1 = 1 END
                )
              AND (
                CASE WHEN v_query_value IS NOT NULL
                         THEN tl.nro_liquidacion LIKE '%' || v_query_value || '%'
                        or UPPER(tl.pagar_a_nombre) LIKE '%' || v_query_value || '%'
                        or upper(cast(tlb.data_stage->>'ticketNumber' as varchar)) like '%' || v_query_value || '%'
                        or upper(cast(tlb.data_stage->>'FOID' as varchar)) like '%' || v_query_value || '%'
                        or upper(cast(tlb.data_stage->>'bussinesName' as varchar)) like '%' || v_query_value || '%'
                        or upper(cast(tlb.data_stage->>'nit' as varchar)) like '%' || v_query_value || '%'
                     ELSE 1 = 1 END

                )

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