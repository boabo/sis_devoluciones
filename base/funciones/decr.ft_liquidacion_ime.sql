CREATE OR REPLACE FUNCTION "decr"."ft_liquidacion_ime" (    
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
    v_billete    text;
    v_json    varchar;

    v_id_concepto_ingas varchar[];
    v_i integer;
    v_id_notas	integer[];

    v_tamano integer;
    v_num_tramite          varchar;
    v_id_proceso_wf     integer;
    v_id_estado_wf         integer;
    v_codigo_estado     varchar;
    v_rec                         RECORD;
    v_codigo_estado_siguiente     varchar;
    v_acceso_directo              varchar;
    v_clase                       varchar;
    v_parametros_ad               varchar;
    v_tipo_noti                   varchar;
    v_titulo                      varchar;
    v_id_estado_actual            integer;
    v_conceptos_json            record;
    v_boletos_recursivo_json            record;
    v_liquiman_det_json            record;
    v_payments_json            record;
    v_detalle            record;
    v_detalle_descuento_liquidacion            record;
    v_importe_devolver numeric(10,2);
    v_sum_venta_seleccionados numeric(10,2);
    v_tipo_documento varchar;
    v_fecha_pago date;
    v_id_venta integer;
    v_id_medio_pago_pw integer;
    v_importe_tramo_utilizado numeric(10,2);
    v_fecha_emision date;
    v_id_liqui_manual integer;
    v_count_conceptos_hijos integer;
    v_conceptos_hijos record;
    v_conceptos_notas record;
    v_record record;
    v_params json;
    v_id_nota integer;
    v_estado varchar;
    v_liquidacion record;
    v_tabla_factura varchar;
    v_boleto_seleccionado record;
    v_nro_liquidacion_validado varchar;
    v_nro_liquidacion varchar;
BEGIN

    v_nombre_funcion = 'decr.ft_liquidacion_ime';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************    
     #TRANSACCION:  'DECR_LIQUI_INS'
     #DESCRIPCION:    Insercion de registros
     #AUTOR:        admin    
     #FECHA:        17-04-2020 01:54:37
    ***********************************/

    if(p_transaccion='DECR_LIQUI_INS')then
                    
        begin


            v_rec = param.f_get_periodo_gestion(to_char(now(), 'YYYY-mm-dd')::DATE);
            -- inciar el tramite en el sistema de WF
            SELECT
                ps_num_tramite ,
                ps_id_proceso_wf ,
                ps_id_estado_wf ,
                ps_codigo_estado
            INTO
                v_num_tramite,
                v_id_proceso_wf,
                v_id_estado_wf,
                v_codigo_estado

            FROM wf.f_inicia_tramite(
                    p_id_usuario,
                    NULL, --(p_hstore->'_id_usuario_ai')::integer,
                    NULL, --(p_hstore->'_nombre_usuario_ai')::varchar,
                    v_rec.po_id_gestion::INTEGER,
                    'LIQDEVOLU',
                    NULL::integer,
                    NULL,
                    NULL,
                    v_parametros.nro_liquidacion );



            --obtenemos el tipo de liquidacino
            select tipo_documento
            INTO v_tipo_documento
            from decr.ttipo_doc_liquidacion
            WHERE id_tipo_doc_liquidacion = v_parametros.id_tipo_doc_liquidacion;


            if(pxp.f_existe_parametro(p_tabla, 'importe_tramo_utilizado')) then

                if v_parametros.importe_tramo_utilizado is NULL then

                    v_importe_tramo_utilizado:= 0;
                END IF;
            END IF;


            --verificamos si existe payments data


            --Sentencia de la insercion
            insert into decr.tliquidacion(
            estacion,
            nro_liquidacion,
            estado_reg,
            tipo_de_cambio,
            descripcion,
            fecha_liqui,
            tramo_devolucion,
            util,
            --fecha_pago,
            id_tipo_doc_liquidacion,
            pv_agt,
            noiata,
            id_tipo_liquidacion,
            --id_forma_pago, se cambio por medio de pago
            tramo,
            nombre,
            moneda_liq,
            estado,
            id_usuario_reg,
            fecha_reg,
            usuario_ai,
            id_usuario_ai,
            id_usuario_mod,
            fecha_mod,
                                          id_boleto,
            punto_venta,
            moneda_emision,
            importe_neto,
            tasas,
            importe_total,
            id_punto_venta,
                                          id_estado_wf,
                                          id_proceso_wf,
                                          num_tramite,
                                          id_venta,
                                          exento,
                                          importe_tramo_utilizado,
                                          id_moneda,
                                          id_deposito,
                                          id_liquidacion_fk,
                                          id_factucom,
            pagar_a_nombre,
            billetes_seleccionados

              ) values(
            v_parametros.estacion,
            v_parametros.nro_liquidacion,
            'activo',
            v_parametros.tipo_de_cambio,
            v_parametros.descripcion,
            v_parametros.fecha_liqui,
            v_parametros.tramo_devolucion,
            v_parametros.util,
            --v_parametros.fecha_pago,
            v_parametros.id_tipo_doc_liquidacion,
            v_parametros.pv_agt,
            v_parametros.noiata,
            v_parametros.id_tipo_liquidacion,
            --v_parametros.id_forma_pago,
            v_parametros.tramo,
            v_parametros.nombre,
            v_parametros.moneda_liq,
            --v_parametros.estado,
            v_codigo_estado,
            p_id_usuario,
            now(),
            v_parametros._nombre_usuario_ai,
            v_parametros._id_usuario_ai,
            null,
            null,
                       v_parametros.id_boleto,
                       v_parametros.punto_venta,
                       v_parametros.moneda_emision,
                       v_parametros.importe_neto,
                       v_parametros.tasas,
                       CASE WHEN v_tipo_documento = 'DEPOSITO' THEN v_parametros.importe_total_deposito ELSE v_parametros.importe_total END,
                       v_parametros.id_punto_venta,

            v_id_estado_wf,
            v_id_proceso_wf,
            v_num_tramite,
                       v_parametros.id_venta,
                       v_parametros.exento,
                       v_parametros.importe_tramo_utilizado,
                       null,--v_parametros.id_moneda,
                       v_parametros.id_deposito,
                       v_parametros.id_liquidacion_fk,
                       v_parametros.id_factucom,
                       v_parametros.pagar_a_nombre,
                       v_parametros.billetes_seleccionados

            
            
            )RETURNING id_liquidacion into v_id_liquidacion;






            -- si el tipo de liquidacion es FACCOM entonces debemos sacar el importe total de la suma de los conceptos a devolver
            if(v_tipo_documento = 'FACCOM') THEN

                FOR v_detalle
                    IN (SELECT unnest(string_to_array(v_parametros.id_venta_detalle::varchar, ',')) as id_venta_detalle
                    )
                    loop

                        insert into decr.tliqui_venta_detalle(
                            estado_reg,
                            id_liquidacion,
                            id_venta_detalle,
                            id_usuario_reg,
                            fecha_reg,
                            id_usuario_ai,
                            usuario_ai,
                            id_usuario_mod,
                            fecha_mod,
                                                              tipo
                        ) values(
                                    'activo',
                                    v_id_liquidacion,
                                    v_detalle.id_venta_detalle::integer,
                                    p_id_usuario,
                                    now(),
                                    v_parametros._id_usuario_ai,
                                    v_parametros._nombre_usuario_ai,
                                    null,
                                    null,
                                 'FACCOM'
                                );

                    END LOOP;


                SELECT sum(tvd.precio)
                INTO v_sum_venta_seleccionados
                FROM vef.tventa_detalle tvd
                         inner JOIN decr.tliqui_venta_detalle lvd on lvd.id_venta_detalle = tvd.id_venta_detalle
                         inner join param.tconcepto_ingas tci on tci.id_concepto_ingas = tvd.id_producto
                where lvd.id_liquidacion = v_id_liquidacion;
                --RAISE EXCEPTION '%','llega' ||v_sum_venta_seleccionados::varchar;

                IF v_sum_venta_seleccionados is NULL then
                    RAISE EXCEPTION '%', 'por algun motivo es cero el importe total de la liquidacion en la faccom';
                END IF;

                UPDATE decr.tliquidacion SET importe_total = v_sum_venta_seleccionados where id_liquidacion = v_id_liquidacion ;

            ELSEIF (v_tipo_documento = 'FAC-ANTIGUAS') THEN


                FOR v_detalle
                    IN (SELECT unnest(string_to_array(v_parametros.id_factucomcon, ',')) as id_venta_detalle
                    )
                    loop
                        insert into decr.tliqui_venta_detalle(
                            estado_reg,
                            id_liquidacion,
                            id_venta_detalle,
                            id_usuario_reg,
                            fecha_reg,
                            id_usuario_ai,
                            usuario_ai,
                            id_usuario_mod,
                            fecha_mod,
                                                              tipo
                        ) values(
                                    'activo',
                                    v_id_liquidacion,
                                    v_detalle.id_venta_detalle::integer,
                                    p_id_usuario,
                                    now(),
                                    v_parametros._id_usuario_ai,
                                    v_parametros._nombre_usuario_ai,
                                    null,
                                    null,
                                 'FAC-ANTIGUAS'
                                );

                    END LOOP;


                WITH t_factucomcon AS (
                    SELECT * FROM dblink('dbname=dbendesis host=192.168.100.30 user=ende_pxp password=ende_pxp',
                                         'SELECT id_factucomcon,id_factucom,cantidad,preciounit,importe,concepto FROM informix.tif_factucomcon where id_factucom = '||v_parametros.id_factucom||' '
                                      ) AS d (id_factucomcon integer, id_factucom integer, cantidad numeric, preciounit numeric, importe numeric, concepto varchar)
                ) SELECT sum(tfcc.importe)
                INTO v_sum_venta_seleccionados
                from decr.tliqui_venta_detalle tlvd
                                   inner join t_factucomcon tfcc on tfcc.id_factucomcon = tlvd.id_venta_detalle
                where tlvd.id_liquidacion = v_id_liquidacion;


                UPDATE decr.tliquidacion SET importe_total = v_sum_venta_seleccionados where id_liquidacion = v_id_liquidacion ;




            ELSEIF (v_tipo_documento = 'PORLIQUI') THEN
                FOR v_detalle_descuento_liquidacion
                    IN (SELECT unnest(string_to_array(v_parametros.id_descuento_liquidacion::varchar, ',')) as id_descuento_liquidacion
                    )
                    loop

                        insert into decr.tliqui_decuento_detalle(
                            estado_reg,
                            id_liquidacion,
                            id_descuento_liquidacion,
                            id_usuario_reg,
                            fecha_reg,
                            id_usuario_ai,
                            usuario_ai,
                            id_usuario_mod,
                            fecha_mod
                        ) values(
                                    'activo',
                                    v_id_liquidacion,
                                    v_detalle_descuento_liquidacion.id_descuento_liquidacion::integer,
                                    p_id_usuario,
                                    now(),
                                    v_parametros._id_usuario_ai,
                                    v_parametros._nombre_usuario_ai,
                                    null,
                                    null
                                );

                    END LOOP;


            END IF;



            FOR v_conceptos_json
                IN (
                    SELECT *
                    FROM json_populate_recordset(NULL::record, v_parametros.json::json)
                             AS
                             (
                              id_concepto_ingas varchar,  tipo varchar,contabilizar varchar,  importe varchar
                                 )

                )
                LOOP
                -- necesitamos ver si el concepto es padre

                select count(*)
                into v_count_conceptos_hijos
                from param.tconcepto_ingas
                where id_concepto_ingas_fk = v_conceptos_json.id_concepto_ingas::integer;



                if(v_count_conceptos_hijos = 0) then

                    insert into decr.tdescuento_liquidacion(
                        contabilizar,
                        importe,
                        estado_reg,
                        id_concepto_ingas,
                        id_liquidacion,
                        sobre,
                        fecha_reg,
                        usuario_ai,
                        id_usuario_reg,
                        id_usuario_ai,
                        fecha_mod,
                        id_usuario_mod,
                        tipo
                    ) values(
                                v_conceptos_json.contabilizar,
                                v_conceptos_json.importe::numeric, --todo
                                'activo',
                                v_conceptos_json.id_concepto_ingas::integer,
                                v_id_liquidacion,
                                null,
                                now(),
                                v_parametros._nombre_usuario_ai,
                                p_id_usuario,
                                v_parametros._id_usuario_ai,
                                null,
                                null,
                                v_conceptos_json.tipo
                            );

                    else


                        --recorremos los conceptos hijos y miramos su configuracion de porcentaje sobre el monto enviado
                        FOR v_conceptos_hijos
                            IN (
                                select id_concepto_ingas,
                                       contabilizable as contabilizar,
                                       precio as porcentaje --el precio para estos conceptos usados en devolucion son el porcentaje
                                from param.tconcepto_ingas
                                where id_concepto_ingas_fk = v_conceptos_json.id_concepto_ingas::integer
                            )
                            loop


                            IF v_conceptos_hijos.porcentaje is null or v_conceptos_hijos.porcentaje = 0 then
                                RAISE EXCEPTION '%','ERROR ALGUN CONCEPTO HIJO DE LO SELECCIONADO NO TIENE CONFIGURADO EL PORCENTAJE(PRECIO)';
                            END IF;

                                insert into decr.tdescuento_liquidacion(
                                    contabilizar,
                                    importe,
                                    estado_reg,
                                    id_concepto_ingas,
                                    id_liquidacion,
                                    sobre,
                                    fecha_reg,
                                    usuario_ai,
                                    id_usuario_reg,
                                    id_usuario_ai,
                                    fecha_mod,
                                    id_usuario_mod,
                                    tipo
                                ) values(
                                            v_conceptos_hijos.contabilizar,
                                            v_conceptos_json.importe::numeric * v_conceptos_hijos.porcentaje::numeric, --todo
                                            'activo',
                                            v_conceptos_hijos.id_concepto_ingas::integer,
                                            v_id_liquidacion,
                                            null,
                                            now(),
                                            v_parametros._nombre_usuario_ai,
                                            p_id_usuario,
                                            v_parametros._id_usuario_ai,
                                            null,
                                            null,
                                            v_conceptos_json.tipo
                                        );



                            END LOOP;

                END IF;





            END LOOP;
            --RAISE EXCEPTION '%', v_parametros.payment;

            IF v_tipo_documento = 'BOLEMD' THEN

                -- INSERTAR BILLETES SELECCIONADOS A DEVOLVER
                FOR v_boleto_seleccionado
                    IN (SELECT unnest(string_to_array(v_parametros.billetes_seleccionados::varchar, ',')) as boleto
                    )
                    loop


                    -- validar si existe en alguna liquidacion este boleto
                    select tl.nro_liquidacion
                    into v_nro_liquidacion_validado
                    from decr.tliqui_boleto_seleccionado tbs
                    inner join decr.tliquidacion tl on tl.id_liquidacion = tbs.id_liquidacion
                    where tbs.boleto = v_boleto_seleccionado.boleto and tl.estado != 'anulado';

                    IF v_nro_liquidacion_validado IS NOT NULL THEN
                         RAISE EXCEPTION 'ERROR BOLETO YA SE ENCUENTRA EN UNA LIQUIDACION %', v_nro_liquidacion_validado;
                    END IF;


                        INSERT INTO decr.tliqui_boleto_seleccionado (id_usuario_reg, id_usuario_mod, fecha_reg, fecha_mod, estado_reg,
                                                                     id_usuario_ai, usuario_ai, id_liquidacion, boleto)
                        VALUES (p_id_usuario, null, now(), null, 'activo', v_parametros._id_usuario_ai, v_parametros._nombre_usuario_ai,
                                v_id_liquidacion, v_boleto_seleccionado.boleto);

                    END LOOP;
                -------


                INSERT INTO decr.tliqui_boleto (id_usuario_reg, id_usuario_mod, fecha_reg, fecha_mod, estado_reg,
                                                id_usuario_ai, usuario_ai, id_liquidacion, data_stage)
                VALUES (p_id_usuario, null, now(), null, 'activo', v_parametros._id_usuario_ai, v_parametros._nombre_usuario_ai,
                        v_id_liquidacion, v_parametros.json_data_boleto_stage::json);


                --guardar los boletos recursivos

                FOR v_boletos_recursivo_json
                    IN (
                        SELECT *
                        FROM json_populate_recordset(NULL::record, v_parametros.json_data_boletos_recursivo::json)
                                 AS
                                 (
                                  seleccionado varchar,  billete varchar, monto varchar,  tiene_nota varchar,   concepto_para_nota varchar,  foid varchar, fecha_emision varchar,  iva varchar,  iva_contabiliza_no_liquida varchar,  exento varchar, nit varchar, razon_social varchar
                                     )

                    )
                    LOOP

                        insert into decr.tliqui_boleto_recursivo(
                            seleccionado,
                            billete,
                            monto,
                            tiene_nota,
                            concepto_para_nota,
                            foid,
                             fecha_emision,
                            iva,
                            iva_contabiliza_no_liquida,
                            exento,
                            estado_reg,
                            id_liquidacion,
                            fecha_reg,
                            usuario_ai,
                            id_usuario_reg,
                            id_usuario_ai,
                            fecha_mod,
                            id_usuario_mod,
                                                                 nit,
                                                                 razon_social
                        ) values(
                                    v_boletos_recursivo_json.seleccionado,
                                    v_boletos_recursivo_json.billete::numeric, --todo
                                    v_boletos_recursivo_json.monto::numeric, --todo
                                    v_boletos_recursivo_json.tiene_nota, --todo
                                    v_boletos_recursivo_json.concepto_para_nota, --todo
                                    v_boletos_recursivo_json.foid, --todo
                                    v_boletos_recursivo_json.fecha_emision::date, --todo
                                    v_boletos_recursivo_json.iva::numeric, --todo
                                    v_boletos_recursivo_json.iva_contabiliza_no_liquida::numeric, --todo
                                    v_boletos_recursivo_json.exento::numeric, --todo
                                    'activo',
                                    v_id_liquidacion,
                                    now(),
                                    v_parametros._nombre_usuario_ai,
                                    p_id_usuario,
                                    v_parametros._id_usuario_ai,
                                    null,
                                    null,
                                    v_boletos_recursivo_json.nit,
                                    v_boletos_recursivo_json.razon_social
                                );

                    END LOOP;


                FOR v_payments_json
                    IN (
                        SELECT *
                        FROM json_populate_recordset(NULL::record, v_parametros.payment::json)
                                 AS
                                 (
                                  code varchar, description varchar, amount varchar, method_code varchar,
                                  reference varchar, administradora varchar, comprobante varchar, lote varchar,
                                  cod_est varchar, credit_card_number varchar
                                     )

                    )
                    LOOP

                        if v_payments_json.description = 'CREDIT CARD' THEN

                            SELECT id_medio_pago_pw into v_id_medio_pago_pw FROM obingresos.tmedio_pago_pw
                            WHERE mop_code = v_payments_json.method_code;
                            insert into decr.tliqui_forma_pago(
                                estado_reg,
                                id_liquidacion,
                                id_medio_pago,
                                pais,
                                ciudad,
                                fac_reporte,
                                cod_est,
                                lote,
                                comprobante,
                                fecha_tarjeta,
                                nro_tarjeta,
                                importe,
                                id_usuario_reg,
                                fecha_reg,
                                id_usuario_ai,
                                usuario_ai,
                                id_usuario_mod,
                                fecha_mod,
                                administradora
                            ) values(
                                        'activo',
                                        v_id_liquidacion,
                                        v_id_medio_pago_pw,
                                        v_parametros.punto_venta,
                                        v_parametros.punto_venta,
                                        null,
                                        v_payments_json.cod_est,
                                        v_payments_json.lote,
                                        v_payments_json.comprobante,
                                        RIGHT(v_payments_json.reference, 4),
                                        v_payments_json.credit_card_number,
                                        v_payments_json.amount::numeric,
                                        p_id_usuario,
                                        now(),
                                        v_parametros._id_usuario_ai,
                                        v_parametros._nombre_usuario_ai,
                                        null,
                                        null,
                                     v_payments_json.administradora



                                    );

                            /*ELSE
                            RAISE EXCEPTION '%','NO ES CREDIT CARD';*/
                        END IF;

                    END LOOP;

            ELSEIF v_tipo_documento = 'LIQUIMAN' THEN

                --guardamos el detalle de liquidacion manual y el tipo manual

                insert into decr.tliqui_manual(
                    estado_reg,
                    id_liquidacion,
                    tipo_manual,
                    id_usuario_reg,
                    fecha_reg,
                    id_usuario_ai,
                    usuario_ai,
                    id_usuario_mod,
                    fecha_mod,
                                               razon_nombre_doc_org
                ) values(
                            'activo',
                            v_id_liquidacion,
                            v_parametros.tipo_manual,
                            p_id_usuario,
                            now(),
                            v_parametros._id_usuario_ai,
                            v_parametros._nombre_usuario_ai,
                            null,
                            null,
                         v_parametros.razon_nombre_liquiman
                        )RETURNING id_liqui_manual into v_id_liqui_manual;

                --actualizamos la liquidacion para agregarle este id_liqui_manual
                update decr.tliquidacion set id_liqui_manual = v_id_liqui_manual
                where id_liquidacion = v_id_liquidacion;

                FOR v_liquiman_det_json
                    IN (
                        SELECT *
                        FROM json_populate_recordset(NULL::record, v_parametros.json_data_liqui_manual_det::json)
                                 AS
                                 (
                                        id_medio_pago varchar,
                                        id_cuenta_bancaria varchar,
                                        administradora varchar,
                                        lote varchar,
                                        comprobante varchar,
                                        nro_aut varchar,
                                        fecha varchar,
                                        nro_tarjeta varchar,
                                        concepto_original varchar,
                                        concepto_devolver varchar,
                                        descripcion varchar,
                                        importe_original varchar,
                                        importe_devolver varchar
                                     )

                    )
                    LOOP


                    --RAISE EXCEPTION '%', CASE WHEN v_liquiman_det_json.id_medio_pago is NOT null and v_liquiman_det_json.id_medio_pago != '' then v_liquiman_det_json.id_medio_pago else null END;
                        insert into decr.tliqui_manual_detalle(
                            estado_reg,
                            id_liqui_manual,
                            id_medio_pago,
                            id_cuenta_bancaria,
                            administradora,
                            lote,
                            comprobante,
                            nro_aut,
                            fecha,
                            nro_tarjeta,
                            concepto_original,
                            concepto_devolver,
                            importe_original,
                            importe_devolver,
                            descripcion,
                            id_usuario_reg,
                            fecha_reg,
                            id_usuario_ai,
                            usuario_ai,
                            id_usuario_mod,
                            fecha_mod
                        ) values(
                                    'activo',
                                    v_id_liqui_manual,
                                    CASE WHEN v_liquiman_det_json.id_medio_pago is NOT null and v_liquiman_det_json.id_medio_pago != '' then v_liquiman_det_json.id_medio_pago::integer else null END,
                                    CASE WHEN v_liquiman_det_json.id_cuenta_bancaria is NOT null and v_liquiman_det_json.id_cuenta_bancaria != '' then v_liquiman_det_json.id_cuenta_bancaria::integer else null END,
                                    CASE WHEN v_liquiman_det_json.administradora is NOT null and v_liquiman_det_json.administradora != '' then v_liquiman_det_json.administradora else null END,
                                    CASE WHEN v_liquiman_det_json.lote is NOT null and v_liquiman_det_json.lote != '' then v_liquiman_det_json.lote else null END,
                                    CASE WHEN v_liquiman_det_json.comprobante is NOT null and v_liquiman_det_json.comprobante != '' then v_liquiman_det_json.comprobante else null END,
                                    CASE WHEN v_liquiman_det_json.nro_aut is NOT null and v_liquiman_det_json.nro_aut != '' then v_liquiman_det_json.nro_aut else null END,
                                    CASE WHEN v_liquiman_det_json.fecha is NOT null and v_liquiman_det_json.fecha != '' then v_liquiman_det_json.fecha else null END,
                                    CASE WHEN v_liquiman_det_json.nro_tarjeta is NOT null and v_liquiman_det_json.nro_tarjeta != '' then v_liquiman_det_json.nro_tarjeta::varchar else null END,
                                    CASE WHEN v_liquiman_det_json.concepto_original is NOT null and v_liquiman_det_json.concepto_original != '' then v_liquiman_det_json.concepto_original else null END,
                                    CASE WHEN v_liquiman_det_json.concepto_devolver is NOT null and v_liquiman_det_json.concepto_devolver != '' then v_liquiman_det_json.concepto_devolver else null END,
                                    CASE WHEN v_liquiman_det_json.importe_original is NOT null and v_liquiman_det_json.importe_original != '' then v_liquiman_det_json.importe_original::numeric else null END,
                                    CASE WHEN v_liquiman_det_json.importe_devolver is NOT null and v_liquiman_det_json.importe_devolver != '' then v_liquiman_det_json.importe_devolver::numeric else null END,
                                    CASE WHEN v_liquiman_det_json.descripcion is NOT null and v_liquiman_det_json.descripcion != '' then v_liquiman_det_json.descripcion else null END,
                                    p_id_usuario,
                                    now(),
                                    v_parametros._id_usuario_ai,
                                    v_parametros._nombre_usuario_ai,
                                    null,
                                    null

                                );


                    if v_parametros.tipo_manual not in ('DEPOSITO MANUAL', 'RO MANUAL')
                    THEN
                        insert into decr.tliqui_forma_pago(
                            estado_reg,
                            id_liquidacion,
                            id_medio_pago,
                            pais,
                            ciudad,
                            fac_reporte,
                            cod_est,
                            lote,
                            comprobante,
                            fecha_tarjeta,
                            nro_tarjeta,
                            importe,
                            id_usuario_reg,
                            fecha_reg,
                            id_usuario_ai,
                            usuario_ai,
                            id_usuario_mod,
                            fecha_mod
                        ) values(
                                    'activo',
                                    v_id_liquidacion,
                                    v_liquiman_det_json.id_medio_pago::integer,
                                    v_parametros.punto_venta,
                                    v_parametros.punto_venta,
                                    null,
                                    null,
                                    v_liquiman_det_json.lote,
                                    v_liquiman_det_json.comprobante,
                                    v_liquiman_det_json.fecha,
                                    v_liquiman_det_json.nro_tarjeta,
                                    v_liquiman_det_json.importe_devolver::numeric,
                                    p_id_usuario,
                                    now(),
                                    v_parametros._id_usuario_ai,
                                    v_parametros._nombre_usuario_ai,
                                    null,
                                    null
                                );
                    END IF;





                    END LOOP;
            END IF;


            /*v_id_concepto_ingas = string_to_array(v_parametros.id_concepto_ingas,',');
            v_tamano = coalesce(array_length(v_id_concepto_ingas, 1),0);
            FOR v_i IN 1..v_tamano LOOP
                --insertamos  registro si no esta presente como activo

                --Sentencia de la insercion
                    insert into decr.tdescuento_liquidacion(
                        contabilizar,
                        importe,
                        estado_reg,
                        id_concepto_ingas,
                        id_liquidacion,
                        sobre,
                        fecha_reg,
                        usuario_ai,
                        id_usuario_reg,
                        id_usuario_ai,
                        fecha_mod,
                        id_usuario_mod
                    ) values(
                                null,
                                100, --todo
                                'activo',
                                v_id_concepto_ingas[v_i]::integer,
                                v_id_liquidacion,
                                null,
                                now(),
                                v_parametros._nombre_usuario_ai,
                                p_id_usuario,
                                v_parametros._id_usuario_ai,
                                null,
                                null
                            );
            END LOOP;*/


            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Liquidacion almacenado(a) con exito (id_liquidacion'||v_id_liquidacion||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_liquidacion',v_id_liquidacion::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'id_usuario',p_id_usuario::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'tipo',v_tipo_documento::varchar);

            --Devuelve la respuesta
            return v_resp;

        end;

    /*********************************    
     #TRANSACCION:  'DECR_LIQUI_MOD'
     #DESCRIPCION:    Modificacion de registros
     #AUTOR:        admin    
     #FECHA:        17-04-2020 01:54:37
    ***********************************/

    elsif(p_transaccion='DECR_LIQUI_MOD')then

        begin

        select tdl.tipo_documento
        INTO v_tipo_documento
        from decr.ttipo_doc_liquidacion tdl
        where tdl.id_tipo_doc_liquidacion = v_parametros.id_tipo_doc_liquidacion;



        IF(v_tipo_documento = 'FACCOM') THEN

            SELECT id_venta
            INTO v_id_venta
            from decr.tliquidacion
            where id_liquidacion = v_parametros.id_liquidacion;

            DELETE from decr.tliqui_venta_detalle where id_liquidacion = v_parametros.id_liquidacion;


        END IF;




            update decr.tliquidacion SET
         estacion  = v_parametros.estacion,
         nro_liquidacion = v_parametros.nro_liquidacion,
         tipo_de_cambio = v_parametros.tipo_de_cambio,
         descripcion = v_parametros.descripcion,
         nombre_cheque = v_parametros.nombre_cheque,
         fecha_liqui = v_parametros.fecha_liqui,
         tramo_devolucion = v_parametros.tramo_devolucion,
         util = v_parametros.util,
         noiata = v_parametros.noiata,
         --id_forma_pago = v_parametros.id_forma_pago,
         tramo = v_parametros.tramo,
         nombre = v_parametros.nombre,
         moneda_liq = v_parametros.moneda_liq,
         cheque = v_parametros.cheque,
         id_usuario_mod = p_id_usuario,
         fecha_mod = now(),
         id_boleto = v_parametros.id_boleto,
         punto_venta = v_parametros.punto_venta,
         moneda_emision = v_parametros.moneda_emision,
         importe_neto = v_parametros.importe_neto,
         tasas = v_parametros.tasas,
         importe_total = v_parametros.importe_total,
         id_venta = v_parametros.id_venta,
                                         exento = v_parametros.exento,
         importe_tramo_utilizado = v_parametros.importe_tramo_utilizado,
         id_medio_pago = v_parametros.id_medio_pago,
         id_moneda = v_parametros.id_moneda
            where id_liquidacion=v_parametros.id_liquidacion;





        if(v_tipo_documento = 'FACCOM') THEN

            FOR v_detalle
                IN (SELECT unnest(string_to_array(v_parametros.id_venta_detalle::varchar, ',')) as id_venta_detalle
                )
                loop

                    insert into decr.tliqui_venta_detalle(
                        estado_reg,
                        id_liquidacion,
                        id_venta_detalle,
                        id_usuario_reg,
                        fecha_reg,
                        id_usuario_ai,
                        usuario_ai,
                        id_usuario_mod,
                        fecha_mod
                    ) values(
                                'activo',
                                v_id_liquidacion,
                                v_detalle.id_venta_detalle::integer,
                                p_id_usuario,
                                now(),
                                v_parametros._id_usuario_ai,
                                v_parametros._nombre_usuario_ai,
                                null,
                                null
                            );

                END LOOP;

            SELECT sum(tvd.precio)
            INTO v_sum_venta_seleccionados
            FROM vef.tventa_detalle tvd
                     inner JOIN decr.tliqui_venta_detalle lvd on lvd.id_venta_detalle = tvd.id_venta_detalle
                     inner join param.tconcepto_ingas tci on tci.id_concepto_ingas = tvd.id_producto
            where lvd.id_liquidacion = v_id_liquidacion;
            --RAISE EXCEPTION '%','llega' ||v_sum_venta_seleccionados::varchar;

            UPDATE decr.tliquidacion SET importe_total = v_sum_venta_seleccionados where id_liquidacion = v_id_liquidacion ;


        END IF;


            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Liquidacion modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_liquidacion',v_parametros.id_liquidacion::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
        end;

    /*********************************    
     #TRANSACCION:  'DECR_LIQUI_ELI'
     #DESCRIPCION:    Eliminacion de registros
     #AUTOR:        admin    
     #FECHA:        17-04-2020 01:54:37
    ***********************************/

    elsif(p_transaccion='DECR_LIQUI_ELI')then

        begin
            --Sentencia de la eliminacion
            delete from decr.tliquidacion
            where id_liquidacion=v_parametros.id_liquidacion;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Liquidacion eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_liquidacion',v_parametros.id_liquidacion::varchar);
              
            --Devuelve la respuesta
            return v_resp;

        end;
    /*********************************
     #TRANSACCION:  'DECR_LIQUI_MON'
     #DESCRIPCION:    obtener datos de las monedas y cambios oficiales de bolivianos a las distintas monedas que se tiene para le fecha de ahora
     #AUTOR:        favio.figueroa
     #FECHA:        17-04-2020 01:54:37
    ***********************************/

    elsif(p_transaccion='DECR_LIQUI_MON')then

        begin

            IF (pxp.f_existe_parametro(p_tabla,'fecha_emision')) then
                v_fecha_emision:= v_parametros.fecha_emision::date;
                else
                    v_fecha_emision:= now();
            END IF;

            WITH t_cambio AS (
                SELECT tm.codigo_internacional || '->' || tl.codigo as from_to, tl.codigo, tm.codigo_internacional, tcp.oficial, tcp.fecha
                FROM conta.tmoneda_pais tmp
                         INNER JOIN param.tlugar tl ON tl.id_lugar = tmp.id_lugar
                         INNER JOIN param.tmoneda tm ON tm.id_moneda = tmp.id_moneda
                         INNER JOIN conta.ttipo_cambio_pais tcp ON tcp.id_moneda_pais = tmp.id_moneda_pais
                WHERE tcp.fecha::date = v_fecha_emision::date
            )
            SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(cambios_oficiales)))
            INTO v_json
            FROM (
                     SELECT *
                     FROM t_cambio
                     ORDER BY codigo DESC
                 ) cambios_oficiales;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'json',v_json);
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje',v_json);

            --Devuelve la respuesta
            return v_resp;

        end;
         
    /*********************************
     #TRANSACCION:  'DECR_LIQUI_VERJSON'
     #DESCRIPCION:    Eliminacion de registros
     #AUTOR:        admin
     #FECHA:        17-04-2020 01:54:37
    ***********************************/

    elsif(p_transaccion='DECR_LIQUI_VERJSON')then

        begin
            --Sentencia de la eliminacion

            WITH t_liqui AS
                     (
                         SELECT tl.*,
                                (tl.importe_total - tl.importe_tramo_utilizado) as importe_devolver_liquidacion,
                                tv.nro_factura,
                                tv.nombre_factura,
                                tv.fecha         AS fecha_factura,
                                tb.nro_boleto,
                                tb.fecha_emision,
                                ttdl.tipo_documento,
                                ttdl.descripcion AS desc_tipo_documento,
                                tu.cuenta as registrado_por
                         FROM decr.tliquidacion tl
                                  INNER JOIN segu.tusuario tu on tu.id_usuario = tl.id_usuario_reg
                                  INNER JOIN decr.ttipo_doc_liquidacion ttdl
                                             ON ttdl.id_tipo_doc_liquidacion = tl.id_tipo_doc_liquidacion
                                  LEFT JOIN obingresos.tboleto tb ON tb.id_boleto = tl.id_boleto
                                  LEFT JOIN vef.tventa tv ON tv.id_venta = tl.id_venta
                         WHERE tl.id_liquidacion = v_parametros.id_liquidacion
                     ),
                 t_descuentos AS (
                     SELECT tci.codigo, tdl.id_liquidacion, tdl.id_concepto_ingas, tdl.importe, tci.desc_ingas, tdl.tipo
                     FROM decr.tdescuento_liquidacion tdl
                              INNER JOIN param.tconcepto_ingas tci ON tci.id_concepto_ingas = tdl.id_concepto_ingas
                     WHERE tdl.id_liquidacion = v_parametros.id_liquidacion
                 ),
                 t_liqui_venta_detalle_seleccionados AS
                     (
                         SELECT tci.desc_ingas, tvd.precio, tvd.cantidad
                         FROM vef.tventa_detalle tvd
                                  INNER JOIN decr.tliqui_venta_detalle lvd ON lvd.id_venta_detalle = tvd.id_venta_detalle
                                  INNER JOIN param.tconcepto_ingas tci ON tci.id_concepto_ingas = tvd.id_producto
                         WHERE lvd.id_liquidacion = v_parametros.id_liquidacion
                     )
            SELECT TO_JSON(ROW_TO_JSON(jsonData) :: TEXT) #>> '{}' AS json
            INTO v_json
            FROM (
                     SELECT (
                                SELECT TO_JSON(liqui)
                                FROM (
                                         SELECT tl.*,
                                                (
                                                        tl.importe_devolver_liquidacion - (SELECT sum(importe)
                                                                            FROM t_descuentos td
                                                                            WHERE td.id_liquidacion = tl.id_liquidacion)
                                                    ) AS total_liquidacion
                                         FROM t_liqui tl
                                     ) liqui
                            ) AS liquidacion,
                            (
                                SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(descuentos)))
                                FROM (
                                         SELECT *
                                         FROM t_descuentos where tipo = 'DESCUENTO'
                                     ) descuentos
                            ) AS descuentos,
                            (
                                SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(descuentos)))
                                FROM (
                                         SELECT *
                                         FROM t_descuentos where tipo = 'IMPUESTO NO REEMBOLSABLE'
                                     ) descuentos
                            ) AS descuentos_impuestos_no_reembolsable,
                            (
                                SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(nota)))
                                FROM (
                                         SELECT *
                                         FROM decr.tnota
                                         WHERE id_liquidacion::integer =47
                                     ) nota
                            ) AS notas,
                            (
                                SELECT sum(importe)
                                FROM t_descuentos
                            ) AS sum_descuentos,

                            (
                                SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(t_liqui_venta_detalle_seleccionados)))
                                FROM t_liqui_venta_detalle_seleccionados
                            ) AS liqui_venta_detalle_seleccionados,
                            (
                                SELECT sum(precio)
                                FROM t_liqui_venta_detalle_seleccionados
                            ) AS sum_venta_seleccionados
                 ) jsonData;






            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'json',v_json);
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje',v_json);
            v_resp = pxp.f_agrega_clave(v_resp,'id_liquidacion',v_parametros.id_liquidacion::varchar);

            --Devuelve la respuesta
            return v_resp;

        end;

    /*********************************
     #TRANSACCION:  'DECR_LIQUI_SIGWF'
     #DESCRIPCION:    ACTUALIZAR SIGUIENTE ESTADO WORKFLOW
     #AUTOR:        admin
     #FECHA:        17-04-2020 01:54:37
    ***********************************/

    elsif(p_transaccion='DECR_LIQUI_SIGWF')then

        begin

            select
                tl.id_liquidacion, ttdl.tipo_documento, tl.fecha_pago
            into
                v_id_liquidacion, v_tipo_documento, v_fecha_pago
            from decr.tliquidacion tl
            inner join decr.ttipo_doc_liquidacion ttdl on ttdl.id_tipo_doc_liquidacion = tl.id_tipo_doc_liquidacion
            where tl.id_proceso_wf = v_parametros.id_proceso_wf_act;

            select
                codigo
            into
                v_codigo_estado_siguiente
            from wf.ttipo_estado tes
            where tes.id_tipo_estado =  v_parametros.id_tipo_estado;

            if v_codigo_estado_siguiente = 'pagado' then
                IF v_fecha_pago is null THEN
                    raise EXCEPTION '%', 'ERROR NO AGREGASTE FECHA DE PAGO';
                END IF;
            END IF;

            if v_codigo_estado_siguiente not in ('emitido') then
                v_acceso_directo = '../../../sis_devoluciones/vista/liquidacion/Liquidacion.php';
                v_clase = 'Liquidacion';
                v_parametros_ad = '{filtro_directo:{campo:"liqui.id_proceso_wf",valor:"'||v_parametros.id_proceso_wf_act::varchar||'"}}';
                v_tipo_noti = 'notificacion';
                v_titulo  = 'Notificacion';
            end if;

            --Obtención id del estaado actual
            v_id_estado_actual =  wf.f_registra_estado_wf(
                    v_parametros.id_tipo_estado,
                    v_parametros.id_funcionario_wf,
                    v_parametros.id_estado_wf_act,
                    v_parametros.id_proceso_wf_act,
                    p_id_usuario,
                    v_parametros._id_usuario_ai,
                    v_parametros._nombre_usuario_ai,
                    null,
                    v_parametros.obs,
                    v_acceso_directo ,
                    v_clase,
                    v_parametros_ad,
                    v_tipo_noti,
                    v_titulo
                );

            --Actualiza el estado actual del movimiento
            update decr.tliquidacion set
                                       id_estado_wf = v_id_estado_actual,
                                       estado = v_codigo_estado_siguiente,
                                         glosa_pagado = CASE WHEN v_codigo_estado_siguiente = 'pagado'  then v_parametros.obs else null END
            where id_liquidacion = v_id_liquidacion;




            -- si es boleto la logica debe obtener los boletos relacionados a esta liquidacion
            if v_tipo_documento = 'BOLEMD' then
                select tlb.data_stage->>'ticketNumber' as desc_nro_boleto
                into v_billete
                from decr.tliquidacion tl
                inner join decr.tliqui_boleto tlb on tlb.id_liquidacion = tl.id_liquidacion
                where tl.id_liquidacion = v_id_liquidacion;
            END IF;



            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','estado cambiado');
            v_resp = pxp.f_agrega_clave(v_resp,'id_liquidacion',v_id_liquidacion::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'estado_actual',v_codigo_estado_siguiente::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'tipo_documento',v_tipo_documento::varchar);
            v_resp = pxp.f_agrega_clave(v_resp, 'billete', v_billete::varchar);

            --Devuelve la respuesta
            return v_resp;

        end;

     /*********************************
     #TRANSACCION:  'DECR_LIQUI_JSONPAGAR'
     #DESCRIPCION:    Eliminacion de registros
     #AUTOR:        admin
     #FECHA:        17-04-2020 01:54:37
    ***********************************/

    elsif(p_transaccion='DECR_LIQUI_JSONPAGAR')then

        begin
            --Sentencia de la eliminacion


            WITH t_liqui AS
                     (
                         SELECT l.id_liquidacion,
                                pv.nombre AS punto_venta,
                                b.nit     AS nit_cliente,
                                b.razon   AS razon_social,
                                b.moneda AS moneda_boleto,
                                'BOB' AS moneda, --todas liquidaciones son emitidas en bolivianos si el boleto esta en dolar ya se hizo la conversion en la interfaz
                                l.tipo_de_cambio as tipo_cambio,
                                0 as exento,
                                '' as observaciones
                         FROM decr.tliquidacion l
                                  INNER JOIN vef.tpunto_venta pv ON pv.id_punto_venta = l.id_punto_venta
                                  INNER JOIN obingresos.tboleto b ON b.id_boleto = l.id_boleto
                         WHERE l.id_liquidacion = v_parametros.id_liquidacion
                     ),
                 t_conceptos AS
                     (
                         SELECT tl.id_liquidacion, ci.id_concepto_ingas AS id_concepto, ci.desc_ingas, 1 AS cantidad, dl.importe as precio_unitario
                         FROM decr.tdescuento_liquidacion dl
                                  INNER JOIN t_liqui tl ON tl.id_liquidacion = dl.id_liquidacion
                                  INNER JOIN param.tconcepto_ingas ci ON ci.id_concepto_ingas = dl.id_concepto_ingas
                         WHERE tl.id_liquidacion = v_parametros.id_liquidacion
                     )SELECT TO_JSON(ROW_TO_JSON(jsonData) :: TEXT) #>> '{}' as json
            into v_json
            FROM
                (
                    SELECT
                        (
                            SELECT TO_JSON(liqui)
                            FROM
                                (
                                    SELECT tl.*,
                                           (
                                               SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(concepto)))
                                               FROM
                                                   (
                                                       SELECT * FROM t_conceptos
                                                   ) concepto
                                           ) as json_venta_detalle
                                    FROM t_liqui tl
                                ) liqui
                        ) as json_para_emitir_factura

                ) jsonData;



            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'json',v_json);
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje',v_json);
            v_resp = pxp.f_agrega_clave(v_resp,'id_liquidacion',v_parametros.id_liquidacion::varchar);

            --Devuelve la respuesta
            return v_resp;

        end;


        /*********************************
     #TRANSACCION:  'DECR_FACTURA_JSON'
     #DESCRIPCION:    Eliminacion de registros
     #AUTOR:        admin
     #FECHA:        26-04-2020 21:14:13
    ***********************************/

    elsif(p_transaccion='DECR_FACTURA_JSON')then

        begin

            select tl.nro_liquidacion
            into v_nro_liquidacion
            from decr.tliquidacion tl
            inner join vef.tventa tv on tv.id_venta = tl.id_venta
            inner join vef.tdosificacion td on td.id_dosificacion = tv.id_dosificacion
            where td.nroaut = v_parametros.nro_aut::varchar
            and tv.nro_factura = v_parametros.nro_fac::integer
            and tl.estado != 'anulado'
            LIMIT 1;

            if v_nro_liquidacion is not null then
                raise EXCEPTION '%','FACTURA YA DEVUELTA EN LA LIQUIDACION ' || v_nro_liquidacion;
            END IF;

            SELECT TO_JSON(venta)::text
            into v_json
            from (
                     select tv.id_venta
                     FROM vef.tventa tv
                     inner join vef.tdosificacion td on td.id_dosificacion = tv.id_dosificacion
                     where td.nroaut = v_parametros.nro_aut::varchar
                       AND tv.nro_factura = v_parametros.nro_fac::integer
                     limit 1
                 ) venta;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'json',v_json);
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje',v_json);

            --Devuelve la respuesta
            return v_resp;

        end;

        /*********************************
     #TRANSACCION:  'DECR_RECIBO_JSON'
     #DESCRIPCION:    obteiene datos del recibo
     #AUTOR:        admin
     #FECHA:        26-04-2020 21:14:13
    ***********************************/

    elsif(p_transaccion='DECR_RECIBO_JSON')then

        begin

            select tl.nro_liquidacion
            into v_nro_liquidacion
            from decr.tliquidacion tl
                     inner join vef.tventa tv on tv.id_venta = tl.id_venta
            where tv.nro_factura = v_parametros.nro_recibo::integer
            and tv.fecha::date = v_parametros.fecha_recibo::date
            LIMIT 1;

            if v_nro_liquidacion is not null then
                raise EXCEPTION '%','RECIBO YA DEVUELTO EN LA LIQUIDACION ' || v_nro_liquidacion;
            END IF;


            SELECT TO_JSON(venta)::text
            into v_json
            from (
                     select tv.id_venta
                     FROM vef.tventa tv
                     where tv.fecha::date = v_parametros.fecha_recibo::date
                       AND tv.nro_factura = v_parametros.nro_recibo::integer
                     limit 1
                 ) venta;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'json',v_json);
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje',v_json);

            --Devuelve la respuesta
            return v_resp;

        end;
     /*********************************
     #TRANSACCION:  'LIQ_GENNOTA_INS'
     #DESCRIPCION:    INSERTAR NOTA DE CREDITO DESDE LA LIQUIDACION
     #AUTOR:        admin
     #FECHA:        26-04-2020 21:14:13
    ***********************************/

    elsif(p_transaccion='LIQ_GENNOTA_INS')then

        begin

            v_params := v_parametros.params::json;

            --guardamos la nota
            v_id_nota:= decr.f_insert_nota_crdb(v_params, p_id_usuario, v_parametros._id_usuario_ai);

            v_id_notas[1] := v_id_nota;


            --Definicion de la respuesta
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje', array_to_string(v_id_notas, ',')::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'id_nota',array_to_string(v_id_notas, ',')::varchar);


            --Devuelve la respuesta
            return v_resp;

        end;

/*********************************
     #TRANSACCION:  'LIQ_ANULAR_LI'
     #DESCRIPCION:    ANULAR LIQUIDACION
     #AUTOR:        admin
     #FECHA:        26-04-2021 21:14:13
    ***********************************/

    elsif(p_transaccion='LIQ_ANULAR_LI')then

        begin


            SELECT *
            INTO v_liquidacion
            FROM decr.tliquidacion
            WHERE id_liquidacion = v_parametros.id_liquidacion;

            IF v_liquidacion.estado = 'pagado' and p_id_usuario != 41 THEN
                RAISE EXCEPTION '%','esta liquidacion ya esta pagada no puedes anular';
            END IF;

            -- anulamos la liquidacion
            update decr.tliquidacion set estado = 'anulado', glosa_anulado = v_parametros.glosa
            where id_liquidacion = v_parametros.id_liquidacion;



            --verificamos si tiene nota
            IF EXISTS (SELECT 1 FROM decr.tnota n WHERE n.id_liquidacion::integer = v_parametros.id_liquidacion and fecha_reg >= '2021-05-15'::date) THEN
                -- si tiene necesitamos anular la nota primero

                FOR v_conceptos_notas
                    IN (
                        SELECT *
                        FROM decr.tnota n
                        WHERE n.id_liquidacion::integer = v_parametros.id_liquidacion and fecha_reg >= '2021-05-15'::date
                    )
                    LOOP
                        UPDATE decr.tnota SET estado = 9, total_devuelto = 0
                                ,monto_total = 0, excento = 0,
                                              credfis = 0, id_usuario_mod = id_usuario_mod, fecha_mod = now()
                        WHERE id_nota = v_conceptos_notas.id_nota;

                        UPDATE decr.tnota_detalle set importe = 0, exento =0,total_devuelto=0
                        where id_nota = v_conceptos_notas.id_nota;

                    END LOOP;
            END IF;

            --verificamos si tiene factura y debemos anular
            if EXISTS(select 1 from vef.tventa where id_proceso_wf = v_liquidacion.id_proceso_wf_factura) then
                v_tabla_factura =  pxp.f_crear_parametro(ARRAY[
                                                     'id_proceso_wf'
                                                     ],
                                                 ARRAY[

                                                     v_liquidacion.id_proceso_wf_factura::varchar
                                                     ],
                                                 ARRAY[
                                                     'int4'
                                                     ]
                    );
                --RAISE EXCEPTION '%', v_tabla_factura;


                v_resp = vef.ft_facturacion_externa_ime(p_id_usuario,p_id_usuario,v_tabla_factura,'VEF_ANU_FAC_LIQ_EXT');
            END IF;






            --Definicion de la respuesta
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'Liquidacion anulada', v_parametros.id_liquidacion::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'id_liquidacion',v_parametros.id_liquidacion::varchar);


            --Devuelve la respuesta
            return v_resp;

        end;


/*********************************
     #TRANSACCION:  'LIQ_FEPA_LI'
     #DESCRIPCION:    FECHA PAGO
     #AUTOR:        admin
     #FECHA:        26-04-2021 21:14:13
    ***********************************/

    elsif(p_transaccion='LIQ_FEPA_LI')then

        begin



            UPDATE decr.tliquidacion
            set fecha_pago = v_parametros.fecha_pago
            where id_liquidacion = v_parametros.id_liquidacion;


            --Definicion de la respuesta
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'Liquidacion Fecha pago', v_parametros.id_liquidacion::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'id_liquidacion',v_parametros.id_liquidacion::varchar);


            --Devuelve la respuesta
            return v_resp;

        end;


/*********************************
     #TRANSACCION:  'LIQ_VBD_LI'
     #DESCRIPCION:    VERIFICAR SI EL BOLETO YA SE DEVOLVIO
     #AUTOR:        admin
     #FECHA:        26-04-2021 21:14:13
    ***********************************/

    elsif(p_transaccion='LIQ_VBD_LI')then

        begin

            SELECT tl.nro_liquidacion
            into v_nro_liquidacion
            FROM decr.tliqui_boleto_recursivo tlbr
            inner join decr.tliquidacion tl on tl.id_liquidacion = tlbr.id_liquidacion
            where tl.estado != 'anulado' and billete = v_parametros.billete and seleccionado = 'si'
            limit 1;
            if v_nro_liquidacion is not NULL then
                RAISE EXCEPTION '%', 'EL BOLETO YA FUE DEVUELTO EN LA LIQUIDACION '||v_nro_liquidacion||', CUIDADO!!!!!';

            END IF;

            --Definicion de la respuesta
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'billete',v_parametros.billete::varchar);


            --Devuelve la respuesta
            return v_resp;

        end;


/*********************************
     #TRANSACCION:  'LIQ_SIAT_LI'
     #DESCRIPCION:    VERIFICAR SI EL BOLETO YA SE DEVOLVIO
     #AUTOR:        admin
     #FECHA:        26-04-2021 21:14:13
    ***********************************/

    elsif(p_transaccion='LIQ_SIAT_LI')then

        begin

           UPDATE decr.tliquidacion
               set nota_siat = v_parametros.nota::json
           where id_liquidacion = v_parametros.id_liquidacion;

            --Definicion de la respuesta
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'id_liquidacion',v_parametros.id_liquidacion::varchar);


            --Devuelve la respuesta
            return v_resp;

        end;


/*********************************
     #TRANSACCION:  'LIQ_SIAT_SEL'
     #DESCRIPCION:    mostrar nota siat
     #AUTOR:        admin
     #FECHA:        26-04-2021 21:14:13
    ***********************************/

    elsif(p_transaccion='LIQ_SIAT_SEL')then

        begin

            SELECT TO_JSON(nota_siat)::text
            into v_json
            from (
                     select nota_siat->>'nro_aut' as nro_aut, nota_siat->>'nro_nota' as nro_nota
                     from decr.tliquidacion
                     where id_liquidacion = v_parametros.id_liquidacion
                 ) nota_siat;

            --RAISE EXCEPTION '%',v_json;


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
ALTER FUNCTION "decr"."ft_liquidacion_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
