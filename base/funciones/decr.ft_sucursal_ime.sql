CREATE OR REPLACE FUNCTION decr.ft_sucursal_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de ventas
 FUNCION: 		ven.ft_sucursal_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'ven.tsucursal'
 AUTOR: 		 (favio figueroa)
 FECHA:	        18-11-2014 20:00:02
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_sucursal	integer;
  v_registros_json			RECORD;

BEGIN

    v_nombre_funcion = 'decr.ft_sucursal_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VEN_SUCU_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ada.torrico
 	#FECHA:		18-11-2014 20:00:02
	***********************************/

	if(p_transaccion='VEN_SUCU_INS')then

        begin
        	--Sentencia de la insercion
        	insert into decr.tsucursal(
			alcaldia,
			estacion,
			telefono,
			estado_reg,
			direccion,
			razon,
			fecha_reg,
			usuario_ai,
			id_usuario_reg,
			id_usuario_ai,
			fecha_mod,
			id_usuario_mod
          	) values(
			v_parametros.alcaldia,
			v_parametros.estacion,
			v_parametros.telefono,
			'activo',
			v_parametros.direccion,
			v_parametros.razon,
			now(),
			v_parametros._nombre_usuario_ai,
			p_id_usuario,
			v_parametros._id_usuario_ai,
			null,
			null



			)RETURNING id_sucursal into v_id_sucursal;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Sucursal almacenado(a) con exito (id_sucursal'||v_id_sucursal||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_sucursal',v_id_sucursal::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'VEN_SUCU_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		ada.torrico
 	#FECHA:		18-11-2014 20:00:02
	***********************************/

	elsif(p_transaccion='VEN_SUCU_MOD')then

		begin
			--Sentencia de la modificacion
			update decr.tsucursal set
			alcaldia = v_parametros.alcaldia,
			estacion = v_parametros.estacion,
			telefono = v_parametros.telefono,
			direccion = v_parametros.direccion,
			razon = v_parametros.razon,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_sucursal=v_parametros.id_sucursal;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Sucursal modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_sucursal',v_parametros.id_sucursal::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'VEN_SUCU_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		ada.torrico
 	#FECHA:		18-11-2014 20:00:02
	***********************************/

	elsif(p_transaccion='VEN_SUCU_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from decr.tsucursal
            where id_sucursal=v_parametros.id_sucursal;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Sucursal eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_sucursal',v_parametros.id_sucursal::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

		/*********************************
	 #TRANSACCION:  'VEN_SUCU_INFX'
	 #DESCRIPCION:	Eliminacion de registros
	 #AUTOR:		FAVIO FIGUEROA
	 #FECHA:		18-11-2014 20:00:02
	***********************************/

  elsif(p_transaccion='VEN_SUCU_INFX')then

    begin

      --Sentencia de la eliminacion
      FOR v_registros_json
			in ( select * from json_populate_recordset(null::decr.sucursales_informix_importacion, v_parametros.arra_json::json))
			LOOP



        insert into decr.tsucursal(
          alcaldia,
          estacion,
          telefono,
          estado_reg,
          direccion,
          razon,
          fecha_reg,
          usuario_ai,
          id_usuario_reg,
          id_usuario_ai,
          fecha_mod,
          id_usuario_mod,
					sucursal
        ) values(
          v_registros_json.alcaldia,
          v_registros_json.estacion,
          v_registros_json.telefonos,
          'activo',
          v_registros_json.direccion,
          v_registros_json.razon,
          now(),
          v_parametros._nombre_usuario_ai,
          p_id_usuario,
          v_parametros._id_usuario_ai,
          null,
          null,
          v_registros_json.sucursal



        );


			END LOOP ;


        --Definicion de la respuesta
      v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Sucursales importados correctamente');
      --v_resp = pxp.f_agrega_clave(v_resp,'id_sucursal',v_parametros.id_sucursal::varchar);

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
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;