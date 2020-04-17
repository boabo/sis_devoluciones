CREATE OR REPLACE FUNCTION "decr"."ft_liquidacion_ime" (	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		devoluciones
 FUNCION: 		decr.ft_liquidacion_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'decr.tliquidacion'
 AUTOR: 		 (admin)
 FECHA:	        17-04-2020 01:54:37
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				17-04-2020 01:54:37								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'decr.tliquidacion'	
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_liquidacion	integer;
			    
BEGIN

    v_nombre_funcion = 'decr.ft_liquidacion_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'DECR_LIQUI_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin	
 	#FECHA:		17-04-2020 01:54:37
	***********************************/

	if(p_transaccion='DECR_LIQUI_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into decr.tliquidacion(
			estacion,
			nro_liquidacion,
			estado_reg,
			tipo_de_cambio,
			descripcion,
			nombre_cheque,
			fecha_liqui,
			tramo_devolucion,
			util,
			fecha_pago,
			id_tipo_doc_liquidacion,
			pv_agt,
			noiata,
			id_tipo_liquidacion,
			id_forma_pago,
			tramo,
			nombre,
			moneda_liq,
			estado,
			obs_dba,
			cheque,
			id_usuario_reg,
			fecha_reg,
			usuario_ai,
			id_usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			v_parametros.estacion,
			v_parametros.nro_liquidacion,
			'activo',
			v_parametros.tipo_de_cambio,
			v_parametros.descripcion,
			v_parametros.nombre_cheque,
			v_parametros.fecha_liqui,
			v_parametros.tramo_devolucion,
			v_parametros.util,
			v_parametros.fecha_pago,
			v_parametros.id_tipo_doc_liquidacion,
			v_parametros.pv_agt,
			v_parametros.noiata,
			v_parametros.id_tipo_liquidacion,
			v_parametros.id_forma_pago,
			v_parametros.tramo,
			v_parametros.nombre,
			v_parametros.moneda_liq,
			v_parametros.estado,
			v_parametros.obs_dba,
			v_parametros.cheque,
			p_id_usuario,
			now(),
			v_parametros._nombre_usuario_ai,
			v_parametros._id_usuario_ai,
			null,
			null
							
			
			
			)RETURNING id_liquidacion into v_id_liquidacion;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Liquidacion almacenado(a) con exito (id_liquidacion'||v_id_liquidacion||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_liquidacion',v_id_liquidacion::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'DECR_LIQUI_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin	
 	#FECHA:		17-04-2020 01:54:37
	***********************************/

	elsif(p_transaccion='DECR_LIQUI_MOD')then

		begin
			--Sentencia de la modificacion
			update decr.tliquidacion set
			estacion = v_parametros.estacion,
			nro_liquidacion = v_parametros.nro_liquidacion,
			tipo_de_cambio = v_parametros.tipo_de_cambio,
			descripcion = v_parametros.descripcion,
			nombre_cheque = v_parametros.nombre_cheque,
			fecha_liqui = v_parametros.fecha_liqui,
			tramo_devolucion = v_parametros.tramo_devolucion,
			util = v_parametros.util,
			fecha_pago = v_parametros.fecha_pago,
			id_tipo_doc_liquidacion = v_parametros.id_tipo_doc_liquidacion,
			pv_agt = v_parametros.pv_agt,
			noiata = v_parametros.noiata,
			id_tipo_liquidacion = v_parametros.id_tipo_liquidacion,
			id_forma_pago = v_parametros.id_forma_pago,
			tramo = v_parametros.tramo,
			nombre = v_parametros.nombre,
			moneda_liq = v_parametros.moneda_liq,
			estado = v_parametros.estado,
			obs_dba = v_parametros.obs_dba,
			cheque = v_parametros.cheque,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_liquidacion=v_parametros.id_liquidacion;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Liquidacion modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_liquidacion',v_parametros.id_liquidacion::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'DECR_LIQUI_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin	
 	#FECHA:		17-04-2020 01:54:37
	***********************************/

	elsif(p_transaccion='DECR_LIQUI_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from decr.tliquidacion
            where id_liquidacion=v_parametros.id_liquidacion;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Liquidacion eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_liquidacion',v_parametros.id_liquidacion::varchar);
              
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
ALTER FUNCTION "decr"."ft_liquidacion_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
