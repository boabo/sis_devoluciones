CREATE OR REPLACE FUNCTION decr.f_insert_nota_crdb (
    p_params json, p_id_usuario integer, p_id_usuario_ai integer
)
    RETURNS json AS
$body$
/**************************************************************************

 FUNCION:         decr.f_insert_nota_crdb
 DESCRIPCION:     inserta una nota desde una liquidacion
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
    v_params_to_liq json;
    v_json json;
    v_res_json json;
    v_liquidacion_json json;
    v_count integer;
    v_record record;
    v_record_dosificacion record;
    v_nro_nota integer;
    v_importe_total_devolver numeric DEFAULT 0;
    v_total_devuelto_por_concepto numeric DEFAULT 0;
    v_exento_total numeric DEFAULT 0;
    v_codigo_control text;
    v_credfis numeric;
    v_id_nota integer;


BEGIN


    RAISE EXCEPTION '%', p_params;

    v_nombre_funcion:='decr.f_get_liquidacion';

    -- obtenemos todos los datos de la liquidacion
    v_params_to_liq:= json_strip_nulls(json_build_object('id_liquidacion', p_params->>'id_liquidacion', 'cantidad', 1, 'puntero', 0));
    v_res_json:= decr.f_get_liquidacion(v_params_to_liq);
    v_liquidacion_json:= v_res_json#>'{datos,0}';

    --obtenemos la dosificacion para generar la nota
    SELECT d.*
    INTO v_record_dosificacion
    FROM vef.tdosificacion d
    WHERE d.estado_reg = 'activo'
      AND d.id_sucursal = v_liquidacion_json->>'id_sucursal'
      AND d.fecha_inicio_emi <= now()::date
      AND d.fecha_limite >= now()::date
      AND d.tipo = 'N'
      AND d.tipo_generacion = 'computarizada'
      AND d.nombre_sistema = 'SISTEMA FACTURACION NCD'
      --d.id_activida_economica @> v_id_actividad_economica todo preguntar sobre esto
        FOR UPDATE;


    IF (v_record_dosificacion IS NULL) THEN
        RAISE EXCEPTION 'No existe una dosificacion activa para emitir la Nota';
    END IF;

    v_nro_nota = v_record_dosificacion.nro_siguiente;


    IF EXISTS (
            select 1 from decr.tnota WHERE  nro_nota = v_nro_nota::varchar and id_dosificacion = v_record_dosificacion.id_dosificacion) THEN
        RAISE EXCEPTION 'El numero de Nota ya existe para esta dosificacion. Por favor comuniquese con el administrador del sistema, ....';
        -- do something
    END IF;


    FOR v_record IN (SELECT json_array_elements(p_params->'detail_json') obj)
        LOOP
        --v_record.obj ->> '_id';
        --v_record.obj ->> '_concepto';
        --v_record.obj ->> '_importe';
        --v_record.obj ->> 'exento';
        --v_record.obj ->> 'importe_devolver';
            v_importe_total_devolver:= v_importe_total_devolver + cast(v_record.obj ->> 'importe_devolver' as numeric);
            v_exento_total:= v_exento_total + cast(v_record.obj ->> 'exento' as numeric);
        END LOOP;

    -- generar codigo de control para la nota
    v_codigo_control:= pxp.f_gen_cod_control(v_record_dosificacion.llave,
                                             v_record_dosificacion.nroaut,
                                             v_nro_nota::varchar,
                                             cast(v_record.obj ->> 'nit' as varchar),
                                             to_char(now()::date,'YYYYMMDD')::varchar,
                                             round(v_importe_total_devolver::numeric,0));

    v_credfis = v_importe_total_devolver * 0.13;

    --insertar la nota
    INSERT INTO decr.tnota
    (id_usuario_reg,
     id_usuario_mod,
     fecha_reg,
     fecha_mod,
     estado_reg,
     id_usuario_ai,
     usuario_ai,
     estacion,
     id_sucursal,
     estado,
     nro_nota,
     fecha,
     razon,
     tcambio,
     nit,
     id_liquidacion,
     nro_liquidacion,
     id_moneda,
     monto_total,
     excento,
     total_devuelto,
     credfis,
     billete,
     codigo_control,
     id_dosificacion,
     nrofac,
     nroaut,
     fecha_fac,
     tipo,
     nroaut_anterior,
     fecha_limite)

    VALUES (p_id_usuario,
            NULL,
            now(),
            NULL,
            'activo',
            p_id_usuario_ai, -- este era el v_parametros._id_usuario_ai
            NULL,
            v_liquidacion_json->>'id_sucursal',
            '1',
            '1',
            v_nro_nota,
            now(),
            p_params->>'razon_social',
            '6.9',
            p_params->>'nit',
            cast(v_liquidacion_json->>'id_liquidacion' as integer),
            cast(v_liquidacion_json->>'nro_liquidacion' as varchar),
            1,
            v_importe_total_devolver,
            v_exento_total,
            v_importe_total_devolver, -- aclarar con shirley
            v_credfis,
            NULL, --  esto puede ser el numero de boleto
            v_codigo_control,
            v_record_dosificacion.id_dosificacion,
            cast(v_liquidacion_json->>'_liqui_nro_doc_original' as bigint),
            v_record_dosificacion.nroaut,
            cast(v_liquidacion_json->>'_liqui_fecha_doc_original' as date),
            'BOLETO',
            cast(v_liquidacion_json->>'_liqui_nro_aut_doc_original' as bigint),
            v_record_dosificacion.fecha_limite)
    RETURNING id_nota INTO v_id_nota;


    -- necesitamos agregar el detalle de la nota
    FOR v_record IN (SELECT json_array_elements(p_params->'detail_json') obj)
        LOOP
        --v_record.obj ->> '_id';
        --v_record.obj ->> '_concepto';
        --v_record.obj ->> '_importe';
        --v_record.obj ->> 'exento';
        --v_record.obj ->> 'importe_devolver';
            v_total_devuelto_por_concepto:= cast(v_record.obj ->> 'importe_devolver' as numeric) - cast(v_record.obj ->> 'exento' as numeric);
            v_exento_total:= v_exento_total + cast(v_record.obj ->> 'exento' as numeric);

            INSERT INTO decr.tnota_detalle
            (id_usuario_reg,
             estado_reg,
             id_nota,
             importe,
             cantidad,
             concepto,
             exento,
             total_devuelto,
             precio_unitario)
            VALUES (p_id_usuario,
                    'activo',
                    v_id_nota,
                    cast(v_record.obj ->> 'importe_devolver' AS numeric),
                    1::integer,
                    cast(v_record.obj ->> 'concepto' AS varchar),
                    cast(v_record.obj ->> 'exento' AS numeric),
                    v_total_devuelto_por_concepto::numeric,
                    cast(v_record.obj ->> 'importe' AS numeric)
                    );

    END LOOP;

    -- actualizamos la dosificacion para aumentar el numero siguiente
    UPDATE vef.tdosificacion
    SET nro_siguiente = nro_siguiente + 1
    WHERE id_dosificacion = v_record_dosificacion.id_dosificacion;



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