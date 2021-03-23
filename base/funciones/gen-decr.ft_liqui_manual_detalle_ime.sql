CREATE OR REPLACE FUNCTION "decr"."ft_liqui_manual_detalle_ime" (	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		devoluciones
 FUNCION: 		decr.ft_liqui_manual_detalle_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'decr.tliqui_manual_detalle'
 AUTOR: 		 (favio.figueroa)
 FECHA:	        22-03-2021 20:14:28
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				22-03-2021 20:14:28								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'decr.tliqui_manual_detalle'	
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_liqui_manual_detalle	integer;
			    
BEGIN

    v_nombre_funcion = 'decr.ft_liqui_manual_detalle_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'DECR_TLMD_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		favio.figueroa	
 	#FECHA:		22-03-2021 20:14:28
	***********************************/

	if(p_transaccion='DECR_TLMD_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into decr.tliqui_manual_detalle(
			estado_reg,
			id_liqui_manual,
			administradora,
			lote,
			comprobante,
			fecha,
			nro_tarjeta,
			concepto_original,
			concepto_devolver,
			importe_original,
			importe_devolver,
			descripcion,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			'activo',
			v_parametros.id_liqui_manual,
			v_parametros.administradora,
			v_parametros.lote,
			v_parametros.comprobante,
			v_parametros.fecha,
			v_parametros.nro_tarjeta,
			v_parametros.concepto_original,
			v_parametros.concepto_devolver,
			v_parametros.importe_original,
			v_parametros.importe_devolver,
			v_parametros.descripcion,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null
							
			
			
			)RETURNING id_liqui_manual_detalle into v_id_liqui_manual_detalle;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Liquidacion Manual Detalle almacenado(a) con exito (id_liqui_manual_detalle'||v_id_liqui_manual_detalle||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_liqui_manual_detalle',v_id_liqui_manual_detalle::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'DECR_TLMD_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		favio.figueroa	
 	#FECHA:		22-03-2021 20:14:28
	***********************************/

	elsif(p_transaccion='DECR_TLMD_MOD')then

		begin
			--Sentencia de la modificacion
			update decr.tliqui_manual_detalle set
			id_liqui_manual = v_parametros.id_liqui_manual,
			administradora = v_parametros.administradora,
			lote = v_parametros.lote,
			comprobante = v_parametros.comprobante,
			fecha = v_parametros.fecha,
			nro_tarjeta = v_parametros.nro_tarjeta,
			concepto_original = v_parametros.concepto_original,
			concepto_devolver = v_parametros.concepto_devolver,
			importe_original = v_parametros.importe_original,
			importe_devolver = v_parametros.importe_devolver,
			descripcion = v_parametros.descripcion,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_liqui_manual_detalle=v_parametros.id_liqui_manual_detalle;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Liquidacion Manual Detalle modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_liqui_manual_detalle',v_parametros.id_liqui_manual_detalle::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'DECR_TLMD_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		favio.figueroa	
 	#FECHA:		22-03-2021 20:14:28
	***********************************/

	elsif(p_transaccion='DECR_TLMD_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from decr.tliqui_manual_detalle
            where id_liqui_manual_detalle=v_parametros.id_liqui_manual_detalle;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Liquidacion Manual Detalle eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_liqui_manual_detalle',v_parametros.id_liqui_manual_detalle::varchar);
              
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
ALTER FUNCTION "decr"."ft_liqui_manual_detalle_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
