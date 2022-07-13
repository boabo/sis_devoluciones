CREATE OR REPLACE FUNCTION vef.f_insert_cliente_public(
    p_params json
)
    RETURNS json AS
$BODY$
DECLARE


    p_pre_registro               varchar DEFAULT 'no';
    p_nombres                    varchar DEFAULT upper(p_params ->> 'nombres');
    p_ap_paterno                 varchar DEFAULT upper(p_params ->> 'ap_paterno');
    p_ap_materno                 varchar DEFAULT upper(p_params ->> 'ap_materno');
    p_ci                         varchar DEFAULT p_params ->> 'ci';
    p_correo                     varchar DEFAULT p_params ->> 'correo';
    p_telefono_celular           varchar DEFAULT p_params ->> 'telefono_celular';
    p_telefono_fijo              varchar DEFAULT p_params ->> 'telefono_fijo';
    p_nombre_factura             varchar DEFAULT upper(p_params ->> 'nombre_factura');
    p_nit                        varchar DEFAULT p_params ->> 'nit';
    p_direccion                  varchar DEFAULT p_params ->> 'direccion';
    p_complemento_nit            varchar DEFAULT upper(p_params ->> 'complemento_nit');
    p_tipo_documento_identidad   integer DEFAULT p_params ->> 'tipo_documento_identidad';
    v_resp                       json;
    v_nombre_funcion             TEXT;
    v_id_cliente                 INTEGER;

BEGIN

    v_nombre_funcion = 'vef.f_insert_cliente_public';

    INSERT INTO vef.tcliente (id_usuario_reg, id_usuario_mod, fecha_reg, fecha_mod, estado_reg, id_usuario_ai,
                              usuario_ai, nombres, primer_apellido, segundo_apellido, telefono_celular,
                              telefono_fijo, otros_telefonos, correo, otros_correos, nombre_factura, nit, direccion,
                              observaciones, lugar, codigo, regimen_tributario, complemento_nit,
                              tipo_documento_identidad)
    VALUES (1,
            NULL,
            now(),
            NULL,
            'activo',
            NULL,
            'NULL',
            p_nombres,
            p_ap_paterno,
            p_ap_materno,
            p_telefono_celular,
            p_telefono_fijo,
            NULL,
            p_correo,
            NULL,
            p_nombre_factura,
            p_nit,
            p_direccion,
            NULL,
            NULL,
            NULL,
            NULL,
            p_complemento_nit,
            p_tipo_documento_identidad)
    RETURNING id_cliente into v_id_cliente;


    SELECT json_strip_nulls(json_build_object(
            'success', TRUE,
            'id_cliente', v_id_cliente
        ))
    INTO v_resp;
    RETURN v_resp;


EXCEPTION
    WHEN OTHERS THEN
        SELECT json_strip_nulls(json_build_object(
                'SQLERRM', SQLERRM,
                'SQLSTATE', SQLSTATE,
                'PROCEDURE', v_nombre_funcion
            ))
        INTO v_resp;
        RAISE EXCEPTION '%',v_resp;
        RETURN v_resp;


END ;
$BODY$
    LANGUAGE plpgsql VOLATILE;