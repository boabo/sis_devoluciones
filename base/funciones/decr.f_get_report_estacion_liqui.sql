CREATE OR REPLACE FUNCTION decr.f_get_report_estacion_liqui (
    p_params json
)
    RETURNS json AS
$body$
/**************************************************************************

 FUNCION:         decr.f_get_report_estacion_liqui
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
    v_json json;

    v_id_liquidacion integer DEFAULT p_params->>'id_liquidacion';
    v_estacion varchar DEFAULT p_params->>'estacion';
    v_estado varchar DEFAULT UPPER(p_params->>'estado');
    v_fecha_ini varchar DEFAULT UPPER(p_params->>'fecha_ini');
    v_fecha_fin varchar DEFAULT UPPER(p_params->>'fecha_fin');
    v_json_params_to_send json;

BEGIN



    v_nombre_funcion:='decr.f_get_report_estacion_liqui';





    with t_datos_por_tipo_doc_json as (SELECT ttdl.tipo_documento,
                                              (select coalesce(j ->> 'datos', '[]')
                                               FROM decr.f_get_liquidacion(json_strip_nulls(json_build_object(
                                                       'reporte', 'si',
                                                       'estacion', 'CBBWEB',
                                                       'estado', 'pagado',
                                                       'fecha_ini', '2023-02-01',
                                                       'fecha_fin', '2023-02-28',
                                                       'tipo_tab_liqui', ttdl.tipo_documento
                                                   ))) j) as data_json
                                       FROM decr.ttipo_doc_liquidacion ttdl)
       , t_data_json_det_por_tipo_doc as (select tipo_documento, json_array_elements(data_json::json) as json
                                          from t_datos_por_tipo_doc_json)
       , t_json_to_columns_bolemd as (select json ->> 'id_liquidacion'                  as id_liquidacion,
                                             json ->> 'importe_devolver'                as importe_devolver,
                                             json -> 'data_stage' ->> 'issueAgencyCode' as issueAgencyCode,
                                             json -> 'data_stage' ->> 'pointOfSale'     as pointOfSale,
                                             json -> 'liqui_forma_pago'                 as liqui_forma_pago
                                      from t_data_json_det_por_tipo_doc tdj
                                      where tipo_documento = 'BOLEMD')
       , t_json_to_columns_otros as (select json ->> 'id_liquidacion'   as id_liquidacion,
                                            json ->> 'importe_devolver' as importe_devolver,
                                            tipo_documento              as issueAgencyCode,
                                            tipo_documento              as pointOfSale,
                                            json -> 'liqui_forma_pago'  as liqui_forma_pago
                                     from t_data_json_det_por_tipo_doc tdj
                                     where tipo_documento != 'BOLEMD')
       , t_data_all as (select *
                        from t_json_to_columns_bolemd
                        union all
                        select *
                        from t_json_to_columns_otros)
       , t_all_liqui_forma_pago_json as (select json_array_elements(liqui_forma_pago) json
                                         from t_data_all)
       , t_all_liqui_forma_pago_columns as (select json ->> 'id_liquidacion'     as id_liquidacion,
                                                   json ->> 'id_medio_pago'      as id_medio_pago,
                                                   json ->> 'importe'            as importe,
                                                   json ->> 'desc_medio_pago_pw' as desc_medio_pago_pw,
                                                   json ->> 'desc_forma_pago_pw' as desc_forma_pago_pw,
                                                   json ->> 'administradora'     as administradora
                                            from t_all_liqui_forma_pago_json)
       , t_bolemd_forma_pago as (select cast(tjtcb.id_liquidacion::text as integer) as    id_liquidacion,
                                        json_array_elements(tjtcb.liqui_forma_pago::json) j
                                 from t_json_to_columns_bolemd tjtcb)
       , t_sum_bolemd_por_forma_pago as (select coalesce(sum(cast(j ->> 'importe' as numeric)), 0) as sum_total
                                         from t_bolemd_forma_pago j)
       , t_sum_bolemd_por_agencia as (select count(*)                               as cantidad,
                                             tdjdptd.issueAgencyCode                as codigo_punto_venta,
                                             tdjdptd.pointOfSale                    as nombre_punto_venta,
                                             sum(tdjdptd.importe_devolver::numeric) as importe_ml
                                      from t_json_to_columns_bolemd tdjdptd
                                      group by tdjdptd.issueAgencyCode, tdjdptd.pointOfSale)
       , t_otros_forma_pago as (select cast(tjtco.id_liquidacion::text as integer) as    id_liquidacion,
                                       json_array_elements(tjtco.liqui_forma_pago::json) j
                                from t_json_to_columns_otros tjtco)
       , t_sum_otros_por_forma_pago
        as (select coalesce(sum(cast(coalesce(j ->> 'importe', '0') as numeric)), 0) as sum_total
            from t_otros_forma_pago j)
       , t_sum_otros_por_agencia as (select count(*)                             as cantidad,
                                            tjtco.issueAgencyCode                as codigo_punto_venta,
                                            tjtco.pointOfSale                    as nombre_punto_venta,
                                            sum(tjtco.importe_devolver::numeric) as importe_ml
                                     from t_json_to_columns_otros tjtco
                                     group by tjtco.issueAgencyCode, tjtco.pointOfSale)
       , t_union_all as (select tsbpa.cantidad, tsbpa.codigo_punto_venta, tsbpa.nombre_punto_venta, tsbpa.importe_ml
                         from t_sum_bolemd_por_agencia tsbpa
                         union all
                         select tsoop.cantidad, tsoop.codigo_punto_venta, tsoop.nombre_punto_venta, tsoop.importe_ml
                         from t_sum_otros_por_agencia tsoop)
       , t_total as (select SUM(cantidad) as cantidad, sum(importe_ml) as total from t_union_all)
    SELECT TO_JSON(ROW_TO_JSON(jsonData) :: TEXT) #>> '{}' as json
    into v_json
    from (SELECT (SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(union_all)))
                  FROM (SELECT *
                        FROM t_union_all tll) union_all)                            as datos,
                 (select total from t_total)                                        as total,
                 (select cantidad from t_total)                                     as cantidad,
                 (select sum_total from t_sum_otros_por_forma_pago)                 as sum_otros_por_forma_pago,
                 (select sum_total from t_sum_bolemd_por_forma_pago)                as sum_bolemd_por_agencia,
                 ((select sum_total from t_sum_otros_por_forma_pago) +
                  (select sum_total from t_sum_bolemd_por_forma_pago))              as total_sum_forma_pago,
                 (SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(all_liqui_forma_pago_columns)))
                  FROM (SELECT desc_forma_pago_pw, count(*), sum(importe::numeric)
                        FROM t_all_liqui_forma_pago_columns
                        group by desc_forma_pago_pw) all_liqui_forma_pago_columns)  as forma_pago_por_forma_pago,
                 (select sum(importe::numeric) from t_all_liqui_forma_pago_columns) as total_forma_pago) jsonData;





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