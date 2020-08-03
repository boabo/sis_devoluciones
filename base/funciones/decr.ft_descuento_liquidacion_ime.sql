CREATE OR REPLACE FUNCTION "decr"."ft_descuento_liquidacion_ime" (	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		devoluciones
 FUNCION: 		decr.ft_descuento_liquidacion_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'decr.tdescuento_liquidacion'
 AUTOR: 		 (admin)
 FECHA:	        17-04-2020 01:55:03
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				17-04-2020 01:55:03								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'decr.tdescuento_liquidacion'	
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_descuento_liquidacion	integer;
			    
BEGIN

    v_nombre_funcion = 'decr.ft_descuento_liquidacion_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'DECR_DESLIQUI_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin	
 	#FECHA:		17-04-2020 01:55:03
	***********************************/

	if(p_transaccion='DECR_DESLIQUI_INS')then
					
        begin
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
			v_parametros.contabilizar,
			v_parametros.importe,
			'activo',
			v_parametros.id_concepto_ingas,
			v_parametros.id_liquidacion,
			v_parametros.sobre,
			now(),
			v_parametros._nombre_usuario_ai,
			p_id_usuario,
			v_parametros._id_usuario_ai,
			null,
			null
							
			
			
			)RETURNING id_descuento_liquidacion into v_id_descuento_liquidacion;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Descuento Liquidacion almacenado(a) con exito (id_descuento_liquidacion'||v_id_descuento_liquidacion||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_descuento_liquidacion',v_id_descuento_liquidacion::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'DECR_DESLIQUI_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin	
 	#FECHA:		17-04-2020 01:55:03
	***********************************/

	elsif(p_transaccion='DECR_DESLIQUI_MOD')then

		begin
			--Sentencia de la modificacion
			update decr.tdescuento_liquidacion set
			contabilizar = v_parametros.contabilizar,
			importe = v_parametros.importe,
			id_concepto_ingas = v_parametros.id_concepto_ingas,
			id_liquidacion = v_parametros.id_liquidacion,
			sobre = v_parametros.sobre,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_descuento_liquidacion=v_parametros.id_descuento_liquidacion;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Descuento Liquidacion modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_descuento_liquidacion',v_parametros.id_descuento_liquidacion::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'DECR_DESLIQUI_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin	
 	#FECHA:		17-04-2020 01:55:03
	***********************************/

	elsif(p_transaccion='DECR_DESLIQUI_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from decr.tdescuento_liquidacion
            where id_descuento_liquidacion=v_parametros.id_descuento_liquidacion;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Descuento Liquidacion eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_descuento_liquidacion',v_parametros.id_descuento_liquidacion::varchar);
              
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
ALTER FUNCTION "decr"."ft_descuento_liquidacion_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
