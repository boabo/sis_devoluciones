CREATE OR REPLACE FUNCTION "decr"."ft_liqui_manual_ime" (	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		devoluciones
 FUNCION: 		decr.ft_liqui_manual_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'decr.tliqui_manual'
 AUTOR: 		 (favio.figueroa)
 FECHA:	        21-03-2021 22:59:57
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				21-03-2021 22:59:57								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'decr.tliqui_manual'	
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_liqui_manual	integer;
			    
BEGIN

    v_nombre_funcion = 'decr.ft_liqui_manual_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'DECR_LIQUIMA_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		favio.figueroa	
 	#FECHA:		21-03-2021 22:59:57
	***********************************/

	if(p_transaccion='DECR_LIQUIMA_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into decr.tliqui_manual(
			estado_reg,
			id_liquidacion,
			tipo_manual,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			'activo',
			v_parametros.id_liquidacion,
			v_parametros.tipo_manual,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null
							
			
			
			)RETURNING id_liqui_manual into v_id_liqui_manual;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Liquidacion Manual almacenado(a) con exito (id_liqui_manual'||v_id_liqui_manual||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_liqui_manual',v_id_liqui_manual::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'DECR_LIQUIMA_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		favio.figueroa	
 	#FECHA:		21-03-2021 22:59:57
	***********************************/

	elsif(p_transaccion='DECR_LIQUIMA_MOD')then

		begin
			--Sentencia de la modificacion
			update decr.tliqui_manual set
			id_liquidacion = v_parametros.id_liquidacion,
			tipo_manual = v_parametros.tipo_manual,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_liqui_manual=v_parametros.id_liqui_manual;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Liquidacion Manual modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_liqui_manual',v_parametros.id_liqui_manual::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'DECR_LIQUIMA_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		favio.figueroa	
 	#FECHA:		21-03-2021 22:59:57
	***********************************/

	elsif(p_transaccion='DECR_LIQUIMA_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from decr.tliqui_manual
            where id_liqui_manual=v_parametros.id_liqui_manual;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Liquidacion Manual eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_liqui_manual',v_parametros.id_liqui_manual::varchar);
              
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
ALTER FUNCTION "decr"."ft_liqui_manual_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
