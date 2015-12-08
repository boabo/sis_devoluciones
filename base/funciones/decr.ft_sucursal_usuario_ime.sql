CREATE OR REPLACE FUNCTION decr.ft_sucursal_usuario_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		ventas
 FUNCION: 		ven.ft_sucursal_usuario_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'ven.tsucursal_usuario'
 AUTOR: 		 (admin)
 FECHA:	        23-09-2015 19:15:16
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
	v_id_sucursal_usuario	integer;

BEGIN

    v_nombre_funcion = 'decr.ft_sucursal_usuario_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VEN_SUCUS_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin
 	#FECHA:		23-09-2015 19:15:16
	***********************************/

	if(p_transaccion='VEN_SUCUS_INS')then

        begin
        	--Sentencia de la insercion
        	insert into decr.tsucursal_usuario(
			tipo,
			id_usuario,
			estado_reg,
			id_sucursal,
			id_usuario_ai,
			id_usuario_reg,
			fecha_reg,
			usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			v_parametros.tipo,
			v_parametros.id_usuario,
			'activo',
			v_parametros.id_sucursal,
			v_parametros._id_usuario_ai,
			p_id_usuario,
			now(),
			v_parametros._nombre_usuario_ai,
			null,
			null



			)RETURNING id_sucursal_usuario into v_id_sucursal_usuario;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Sucursal Usuario almacenado(a) con exito (id_sucursal_usuario'||v_id_sucursal_usuario||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_sucursal_usuario',v_id_sucursal_usuario::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'VEN_SUCUS_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin
 	#FECHA:		23-09-2015 19:15:16
	***********************************/

	elsif(p_transaccion='VEN_SUCUS_MOD')then

		begin
			--Sentencia de la modificacion
			update decr.tsucursal_usuario set
			tipo = v_parametros.tipo,
			id_usuario = v_parametros.id_usuario,
			id_sucursal = v_parametros.id_sucursal,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_sucursal_usuario=v_parametros.id_sucursal_usuario;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Sucursal Usuario modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_sucursal_usuario',v_parametros.id_sucursal_usuario::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'VEN_SUCUS_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin
 	#FECHA:		23-09-2015 19:15:16
	***********************************/

	elsif(p_transaccion='VEN_SUCUS_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from decr.tsucursal_usuario
            where id_sucursal_usuario=v_parametros.id_sucursal_usuario;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Sucursal Usuario eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_sucursal_usuario',v_parametros.id_sucursal_usuario::varchar);

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