CREATE OR REPLACE FUNCTION "decr"."ft_liqui_forma_pago_ime" (    
                p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:        devoluciones
 FUNCION:         decr.ft_liqui_forma_pago_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'decr.tliqui_forma_pago'
 AUTOR:          (admin)
 FECHA:            06-01-2021 03:55:40
 COMENTARIOS:    
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE                FECHA                AUTOR                DESCRIPCION
 #0                06-01-2021 03:55:40                                Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'decr.tliqui_forma_pago'    
 #
 ***************************************************************************/

DECLARE

    v_nro_requerimiento        integer;
    v_parametros               record;
    v_id_requerimiento         integer;
    v_resp                    varchar;
    v_nombre_funcion        text;
    v_mensaje_error         text;
    v_id_liqui_forma_pago    integer;
    v_left_credit_card    varchar;
    v_right_credit_card    varchar;
    v_nro_liquidacion    varchar;

BEGIN

    v_nombre_funcion = 'decr.ft_liqui_forma_pago_ime';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************    
     #TRANSACCION:  'DECR_TLP_INS'
     #DESCRIPCION:    Insercion de registros
     #AUTOR:        admin    
     #FECHA:        06-01-2021 03:55:40
    ***********************************/

    if(p_transaccion='DECR_TLP_INS')then
                    
        begin


            -- si es por tarjeta necesitamos validar que no se este pagando doble el mismo comprobante
            IF v_parametros.administradora != '' then
                v_left_credit_card := left(v_parametros.nro_tarjeta, 4);
                v_right_credit_card := right(v_parametros.nro_tarjeta, 4);

                SELECT string_agg(tl.nro_liquidacion::varchar, ',')
                into v_nro_liquidacion
                FROM decr.tliqui_forma_pago tlfp
                inner join decr.tliquidacion tl on tl.id_liquidacion = tlfp.id_liquidacion and tl.estado != 'anulado'
                where left(nro_tarjeta,4) = v_left_credit_card
                  and right(nro_tarjeta, 4) = v_right_credit_card
                  and trim(autorizacion) = trim(v_parametros.autorizacion);

                IF v_nro_liquidacion is not null THEN
                    RAISE EXCEPTION '%','ESTA AUTORIZACION Y TARJETA YA SE DEVOLVIO EN LIQ' || v_nro_liquidacion ;
                END IF;


            END IF;

            --Sentencia de la insercion
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
                                               nro_documento_pago,
            nombre,
                                               administradora,
            id_auxiliar,
            autorizacion,
                fecha_cierre,
            nro_terminal,
                nombre_comercio
              ) values(
            'activo',
            v_parametros.id_liquidacion,
            v_parametros.id_medio_pago,
            v_parametros.pais,
            v_parametros.ciudad,
            v_parametros.fac_reporte,
            v_parametros.cod_est,
            v_parametros.lote,
            v_parametros.comprobante,
            v_parametros.fecha_tarjeta,
            v_parametros.nro_tarjeta,
            v_parametros.importe,
            p_id_usuario,
            now(),
            v_parametros._id_usuario_ai,
            v_parametros._nombre_usuario_ai,
            null,
            null,
                       v_parametros.nro_documento_pago,
                       v_parametros.nombre,
                       v_parametros.administradora,
                       v_parametros.id_auxiliar,
                       v_parametros.autorizacion,
                       v_parametros.fecha_cierre,
            v_parametros.nro_terminal,
            v_parametros.nombre_comercio



            )RETURNING id_liqui_forma_pago into v_id_liqui_forma_pago;
            
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','liqui forma pago almacenado(a) con exito (id_liqui_forma_pago'||v_id_liqui_forma_pago||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_liqui_forma_pago',v_id_liqui_forma_pago::varchar);

            --Devuelve la respuesta
            return v_resp;

        end;

    /*********************************    
     #TRANSACCION:  'DECR_TLP_MOD'
     #DESCRIPCION:    Modificacion de registros
     #AUTOR:        admin    
     #FECHA:        06-01-2021 03:55:40
    ***********************************/

    elsif(p_transaccion='DECR_TLP_MOD')then

        begin


            -- si es por tarjeta necesitamos validar que no se este pagando doble el mismo comprobante
            IF v_parametros.administradora != '' then
                v_left_credit_card := left(v_parametros.nro_tarjeta, 4);
                v_right_credit_card := right(v_parametros.nro_tarjeta, 4);

                SELECT string_agg(tl.nro_liquidacion::varchar,',')
                into v_nro_liquidacion
                FROM decr.tliqui_forma_pago tlfp
                         inner join decr.tliquidacion tl on tl.id_liquidacion = tlfp.id_liquidacion and tl.id_liquidacion != v_parametros.id_liquidacion and tl.estado != 'anulado'
                where left(nro_tarjeta,4) = v_left_credit_card
                  and right(nro_tarjeta, 4) = v_right_credit_card
                  and trim(autorizacion) = trim(v_parametros.autorizacion);

                IF v_nro_liquidacion is not null THEN
                    RAISE EXCEPTION '%','ESTA AUTORIZACION Y TARJETA YA SE DEVOLVIO EN LIQ' || v_nro_liquidacion ;
                END IF;


            END IF;

            --Sentencia de la modificacion
            update decr.tliqui_forma_pago set
            id_liquidacion = v_parametros.id_liquidacion,
            id_medio_pago = v_parametros.id_medio_pago,
            pais = v_parametros.pais,
            ciudad = v_parametros.ciudad,
            fac_reporte = v_parametros.fac_reporte,
            cod_est = v_parametros.cod_est,
            lote = v_parametros.lote,
            comprobante = v_parametros.comprobante,
            fecha_tarjeta = v_parametros.fecha_tarjeta,
            nro_tarjeta = v_parametros.nro_tarjeta,
            importe = v_parametros.importe,
            id_usuario_mod = p_id_usuario,
            fecha_mod = now(),
            id_usuario_ai = v_parametros._id_usuario_ai,
            usuario_ai = v_parametros._nombre_usuario_ai,
            nro_documento_pago = v_parametros.nro_documento_pago,
            nombre = v_parametros.nombre,
                                              administradora = v_parametros.administradora,
            id_auxiliar = v_parametros.id_auxiliar,
            autorizacion = v_parametros.autorizacion,
            fecha_cierre = v_parametros.fecha_cierre,
            nro_terminal = v_parametros.nro_terminal,
            nombre_comercio = v_parametros.nombre_comercio
            where id_liqui_forma_pago=v_parametros.id_liqui_forma_pago;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','liqui forma pago modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_liqui_forma_pago',v_parametros.id_liqui_forma_pago::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
        end;

    /*********************************    
     #TRANSACCION:  'DECR_TLP_ELI'
     #DESCRIPCION:    Eliminacion de registros
     #AUTOR:        admin    
     #FECHA:        06-01-2021 03:55:40
    ***********************************/

    elsif(p_transaccion='DECR_TLP_ELI')then

        begin
            --Sentencia de la eliminacion
            delete from decr.tliqui_forma_pago
            where id_liqui_forma_pago=v_parametros.id_liqui_forma_pago;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','liqui forma pago eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_liqui_forma_pago',v_parametros.id_liqui_forma_pago::varchar);
              
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
ALTER FUNCTION "decr"."ft_liqui_forma_pago_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
