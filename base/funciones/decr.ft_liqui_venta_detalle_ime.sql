CREATE OR REPLACE FUNCTION "decr"."ft_liqui_venta_detalle_ime" (	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		devoluciones
 FUNCION: 		decr.ft_liqui_venta_detalle_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'decr.tliqui_venta_detalle'
 AUTOR: 		 (admin)
 FECHA:	        29-12-2020 19:36:57
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				29-12-2020 19:36:57								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'decr.tliqui_venta_detalle'	
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_liqui_venta_detalle	integer;
			    
BEGIN

    v_nombre_funcion = 'decr.ft_liqui_venta_detalle_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'DECR_LVD_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin	
 	#FECHA:		29-12-2020 19:36:57
	***********************************/

	if(p_transaccion='DECR_LVD_INS')then
					
        begin
        	--Sentencia de la insercion
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
			v_parametros.id_liquidacion,
			v_parametros.id_venta_detalle,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null
							
			
			
			)RETURNING id_liqui_venta_detalle into v_id_liqui_venta_detalle;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','liqui venta detalle almacenado(a) con exito (id_liqui_venta_detalle'||v_id_liqui_venta_detalle||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_liqui_venta_detalle',v_id_liqui_venta_detalle::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'DECR_LVD_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin	
 	#FECHA:		29-12-2020 19:36:57
	***********************************/

	elsif(p_transaccion='DECR_LVD_MOD')then

		begin
			--Sentencia de la modificacion
			update decr.tliqui_venta_detalle set
			id_liquidacion = v_parametros.id_liquidacion,
			id_venta_detalle = v_parametros.id_venta_detalle,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_liqui_venta_detalle=v_parametros.id_liqui_venta_detalle;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','liqui venta detalle modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_liqui_venta_detalle',v_parametros.id_liqui_venta_detalle::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'DECR_LVD_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin	
 	#FECHA:		29-12-2020 19:36:57
	***********************************/

	elsif(p_transaccion='DECR_LVD_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from decr.tliqui_venta_detalle
            where id_liqui_venta_detalle=v_parametros.id_liqui_venta_detalle;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','liqui venta detalle eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_liqui_venta_detalle',v_parametros.id_liqui_venta_detalle::varchar);
              
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
ALTER FUNCTION "decr"."ft_liqui_venta_detalle_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
