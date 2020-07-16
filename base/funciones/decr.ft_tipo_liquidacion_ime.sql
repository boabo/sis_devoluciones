CREATE OR REPLACE FUNCTION "decr"."ft_tipo_liquidacion_ime" (	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		devoluciones
 FUNCION: 		decr.ft_tipo_liquidacion_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'decr.ttipo_liquidacion'
 AUTOR: 		 (admin)
 FECHA:	        17-04-2020 01:50:31
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				17-04-2020 01:50:31								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'decr.ttipo_liquidacion'	
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_tipo_liquidacion	integer;
			    
BEGIN

    v_nombre_funcion = 'decr.ft_tipo_liquidacion_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'DECR_TIPOLIQU_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin	
 	#FECHA:		17-04-2020 01:50:31
	***********************************/

	if(p_transaccion='DECR_TIPOLIQU_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into decr.ttipo_liquidacion(
			estado_reg,
			tipo_liquidacion,
			usuario_ai,
			fecha_reg,
			id_usuario_reg,
			id_usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			'activo',
			v_parametros.tipo_liquidacion,
			v_parametros.obs_dba,
			v_parametros._nombre_usuario_ai,
			now(),
			p_id_usuario,
			v_parametros._id_usuario_ai,
			null,
			null
							
			
			
			)RETURNING id_tipo_liquidacion into v_id_tipo_liquidacion;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Tipo Liquidacion almacenado(a) con exito (id_tipo_liquidacion'||v_id_tipo_liquidacion||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_tipo_liquidacion',v_id_tipo_liquidacion::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'DECR_TIPOLIQU_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin	
 	#FECHA:		17-04-2020 01:50:31
	***********************************/

	elsif(p_transaccion='DECR_TIPOLIQU_MOD')then

		begin
			--Sentencia de la modificacion
			update decr.ttipo_liquidacion set
			tipo_liquidacion = v_parametros.tipo_liquidacion,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_tipo_liquidacion=v_parametros.id_tipo_liquidacion;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Tipo Liquidacion modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_tipo_liquidacion',v_parametros.id_tipo_liquidacion::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'DECR_TIPOLIQU_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin	
 	#FECHA:		17-04-2020 01:50:31
	***********************************/

	elsif(p_transaccion='DECR_TIPOLIQU_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from decr.ttipo_liquidacion
            where id_tipo_liquidacion=v_parametros.id_tipo_liquidacion;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Tipo Liquidacion eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_tipo_liquidacion',v_parametros.id_tipo_liquidacion::varchar);
              
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
ALTER FUNCTION "decr"."ft_tipo_liquidacion_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
