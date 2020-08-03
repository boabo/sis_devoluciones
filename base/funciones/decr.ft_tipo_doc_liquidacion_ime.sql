CREATE OR REPLACE FUNCTION "decr"."ft_tipo_doc_liquidacion_ime" (	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		devoluciones
 FUNCION: 		decr.ft_tipo_doc_liquidacion_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'decr.ttipo_doc_liquidacion'
 AUTOR: 		 (admin)
 FECHA:	        17-04-2020 01:52:57
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				17-04-2020 01:52:57								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'decr.ttipo_doc_liquidacion'	
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_tipo_doc_liquidacion	integer;
			    
BEGIN

    v_nombre_funcion = 'decr.ft_tipo_doc_liquidacion_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'DECR_TDOCLIQ_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin	
 	#FECHA:		17-04-2020 01:52:57
	***********************************/

	if(p_transaccion='DECR_TDOCLIQ_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into decr.ttipo_doc_liquidacion(
			estado_reg,
			tipo_documento,
			id_usuario_reg,
			fecha_reg,
			usuario_ai,
			id_usuario_ai,
			id_usuario_mod,
			fecha_mod,
        	                                       descripcion
          	) values(
			'activo',
			v_parametros.tipo_documento,
			p_id_usuario,
			now(),
			v_parametros._nombre_usuario_ai,
			v_parametros._id_usuario_ai,
			null,
			null,
          	         v_parametros.descripcion
							
			
			
			)RETURNING id_tipo_doc_liquidacion into v_id_tipo_doc_liquidacion;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Tipo Doc Liquidacion almacenado(a) con exito (id_tipo_doc_liquidacion'||v_id_tipo_doc_liquidacion||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_tipo_doc_liquidacion',v_id_tipo_doc_liquidacion::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'DECR_TDOCLIQ_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin	
 	#FECHA:		17-04-2020 01:52:57
	***********************************/

	elsif(p_transaccion='DECR_TDOCLIQ_MOD')then

		begin
			--Sentencia de la modificacion
			update decr.ttipo_doc_liquidacion set
			tipo_documento = v_parametros.tipo_documento,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai,
			                                      descripcion = v_parametros.descripcion
			where id_tipo_doc_liquidacion=v_parametros.id_tipo_doc_liquidacion;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Tipo Doc Liquidacion modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_tipo_doc_liquidacion',v_parametros.id_tipo_doc_liquidacion::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'DECR_TDOCLIQ_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin	
 	#FECHA:		17-04-2020 01:52:57
	***********************************/

	elsif(p_transaccion='DECR_TDOCLIQ_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from decr.ttipo_doc_liquidacion
            where id_tipo_doc_liquidacion=v_parametros.id_tipo_doc_liquidacion;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Tipo Doc Liquidacion eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_tipo_doc_liquidacion',v_parametros.id_tipo_doc_liquidacion::varchar);
              
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
ALTER FUNCTION "decr"."ft_tipo_doc_liquidacion_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
