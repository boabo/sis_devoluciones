CREATE OR REPLACE FUNCTION "decr"."ft_liquidacion_json" (
    p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
    RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:        devoluciones
 FUNCION:         decr.ft_liquidacion_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'decr.tliquidacion'
 AUTOR:          (admin)
 FECHA:            17-04-2020 01:54:37
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE                FECHA                AUTOR                DESCRIPCION
 #0                17-04-2020 01:54:37                                Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'decr.tliquidacion'
 #
 ***************************************************************************/

DECLARE

    v_nro_requerimiento        integer;
    v_parametros               record;
    v_id_requerimiento         integer;
    v_resp                    varchar;
    v_nombre_funcion        text;
    v_mensaje_error         text;
    v_id_liquidacion    integer;
    v_json    json;
    v_query varchar;
    v_count integer;
    v_liqui_json json;

    v_filtro_value varchar;
    v_query_value varchar;
    v_filter_by varchar;
    v_tipo_tab_liqui varchar;
    v_ids_liqui int[];
    v_ids_factucom varchar;
    v_administradora varchar;
    v_estado varchar;
    v_estacion varchar;
    v_id_medio_pago integer;
    v_fecha_ini date;
    v_fecha_fin date;
    v_id_liquidacion_array int[];

    v_params json;
        v_res_json json;
        v_liquidacion_json json;
        v_conceptos_originales json;

BEGIN

    v_nombre_funcion = 'decr.ft_liquidacion_ime';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************
     #TRANSACCION:  'DECR_LIQUI_JSON_SEL'
     #DESCRIPCION:    Insercion de registros
     #AUTOR:        favio figueroa
     #FECHA:        11-02-2021 19:36:57
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
            if(pxp.f_existe_parametro(p_tabla, 'filter_by' )) then
                if(v_parametros.filter_by != '') then
                    v_filter_by := v_parametros.filter_by;
                END IF;
            END IF;


            if(pxp.f_existe_parametro(p_tabla, 'id_liquidacion' )) then


                v_id_liquidacion := v_parametros.id_liquidacion;
                select ttdl.tipo_documento
                into v_tipo_tab_liqui
                from decr.ttipo_doc_liquidacion ttdl
                inner join decr.tliquidacion tl on tl.id_tipo_doc_liquidacion = ttdl.id_tipo_doc_liquidacion
                where tl.id_liquidacion = v_id_liquidacion;
            END IF;


            if(pxp.f_existe_parametro(p_tabla, 'administradora' )) then
                if(v_parametros.administradora != '') then
                    v_administradora := v_parametros.administradora;
                END IF;
            END IF;

            if(pxp.f_existe_parametro(p_tabla, 'estado' )) then
                if(v_parametros.estado != '') then
                    v_estado := v_parametros.estado;
                END IF;
            END IF;
            if(pxp.f_existe_parametro(p_tabla, 'estacion' )) then
                if(v_parametros.estacion != '') then
                    v_estacion := v_parametros.estacion;
                END IF;
            END IF;
            --RAISE EXCEPTION '%',v_parametros.id_medio_pago;
            if(pxp.f_existe_parametro(p_tabla, 'id_medio_pago' )) then
                v_id_medio_pago := v_parametros.id_medio_pago;
            END IF;



            if(pxp.f_existe_parametro(p_tabla, 'fecha_ini' )) then
                if(v_parametros.fecha_ini is not null) then
                    v_fecha_ini := v_parametros.fecha_ini;
                END IF;
            END IF;
            if(pxp.f_existe_parametro(p_tabla, 'fecha_fin' )) then
                if(v_parametros.fecha_fin is not null) then
                    v_fecha_fin := v_parametros.fecha_fin;
                END IF;
            END IF;




            select json_strip_nulls(json_build_object('id_liquidacion', v_id_liquidacion,
                                                      'cantidad', v_parametros.cantidad,
                                                      'puntero', v_parametros.puntero,
                                                      'filtro_value', v_filtro_value,
                                                      'query_value', v_query_value,
                                                      'filter_by', v_filter_by,
                                                      'tipo_tab_liqui', v_tipo_tab_liqui,
                                                      'ids_liqui', v_ids_liqui,
                                                      'ids_factucom', v_ids_factucom,
                                                      'administradora', v_administradora,
                                                      'estado', v_estado,
                                                      'estacion', v_estacion,
                                                      'id_medio_pago', v_id_medio_pago,
                                                      'fecha_ini', v_fecha_ini,
                                                      'fecha_fin', v_fecha_fin))
            into v_params;


            v_json := decr.f_get_liquidacion(v_params);



            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'json',v_json::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje',v_json::varchar);

            --Devuelve la respuesta
            return v_resp;

        end;

    /*********************************
     #TRANSACCION:  'LIQ_GETCONORI_SEL'
     #DESCRIPCION:    obtener solo concetos originales de la liquidacion del documento original
     #AUTOR:        favio figueroa
     #FECHA:        11-02-2021 19:36:57
    ***********************************/
    elsif(p_transaccion='LIQ_GETCONORI_SEL')then

        BEGIN

            v_params:= json_strip_nulls(json_build_object('id_liquidacion', v_parametros.id_liquidacion, 'cantidad', 1, 'puntero', 0));
            v_res_json := decr.f_get_liquidacion(v_params);
            v_liquidacion_json:= v_res_json#>'{datos,0}';
            v_conceptos_originales:= v_liquidacion_json->'_detalle_documento_original';


            --RAISE EXCEPTION '%', v_conceptos_originales;


            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'json',v_conceptos_originales::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje',v_conceptos_originales::varchar);
            --Devuelve la respuesta
            return v_resp;

        END;

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
