CREATE OR REPLACE FUNCTION "decr"."ft_nota_siat_ime" (	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		devoluciones
 FUNCION: 		decr.ft_nota_siat_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'decr.tnota_siat'
 AUTOR: 		 (favio.figueroa)
 FECHA:	        15-02-2022 18:29:01
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				15-02-2022 18:29:01								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'decr.tnota_siat'	
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_nota_siat	integer;
			    
BEGIN

    v_nombre_funcion = 'decr.ft_nota_siat_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'DECR_TNS_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		favio.figueroa	
 	#FECHA:		15-02-2022 18:29:01
	***********************************/

	if(p_transaccion='DECR_TNS_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into decr.tnota_siat(
			estado_reg,
			id_liquidacion,
			nro_nota,
			nro_aut,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			'activo',
			v_parametros.id_liquidacion,
			v_parametros.nro_nota,
			v_parametros.nro_aut,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null
							
			
			
			)RETURNING id_nota_siat into v_id_nota_siat;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','NOTASIAT almacenado(a) con exito (id_nota_siat'||v_id_nota_siat||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_nota_siat',v_id_nota_siat::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'DECR_TNS_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		favio.figueroa	
 	#FECHA:		15-02-2022 18:29:01
	***********************************/

	elsif(p_transaccion='DECR_TNS_MOD')then

		begin
			--Sentencia de la modificacion
			update decr.tnota_siat set
			id_liquidacion = v_parametros.id_liquidacion,
			nro_nota = v_parametros.nro_nota,
			nro_aut = v_parametros.nro_aut,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_nota_siat=v_parametros.id_nota_siat;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','NOTASIAT modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_nota_siat',v_parametros.id_nota_siat::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'DECR_TNS_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		favio.figueroa	
 	#FECHA:		15-02-2022 18:29:01
	***********************************/

	elsif(p_transaccion='DECR_TNS_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from decr.tnota_siat
            where id_nota_siat=v_parametros.id_nota_siat;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','NOTASIAT eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_nota_siat',v_parametros.id_nota_siat::varchar);
              
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
ALTER FUNCTION "decr"."ft_nota_siat_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
