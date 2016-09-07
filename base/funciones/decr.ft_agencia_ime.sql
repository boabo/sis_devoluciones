CREATE OR REPLACE FUNCTION "decr"."ft_agencia_ime" (
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		Sistema de ventas
 FUNCION: 		ven.ft_agencia_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'ven.tagencia'
 AUTOR: 		 (ada.torrico)
 FECHA:	        18-11-2014 20:38:28
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
	v_id_agencia	integer;
			    
BEGIN

    v_nombre_funcion = 'ven.ft_agencia_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'VEN_AGEN_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ada.torrico	
 	#FECHA:		18-11-2014 20:38:28
	***********************************/

	if(p_transaccion='VEN_AGEN_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into ven.tagencia(
			id_sucursal,
			direccion,
			iata,
			id_lugar_pais,
			fax,
			estacion,
			responsable,
			codigo,
			fono,
			nombre,
			tipo_agencia,
			email,
			estado_reg,
			celular,
			id_usuario_ai,
			id_usuario_reg,
			fecha_reg,
			usuario_ai,
			fecha_mod,
			id_usuario_mod
          	) values(
			v_parametros.id_sucursal,
			v_parametros.direccion,
			v_parametros.iata,
			v_parametros.id_lugar_pais,
			v_parametros.fax,
			v_parametros.estacion,
			v_parametros.responsable,
			v_parametros.codigo,
			v_parametros.fono,
			v_parametros.nombre,
			v_parametros.tipo_agencia,
			v_parametros.email,
			'activo',
			v_parametros.celular,
			v_parametros._id_usuario_ai,
			p_id_usuario,
			now(),
			v_parametros._nombre_usuario_ai,
			null,
			null
							
			
			
			)RETURNING id_agencia into v_id_agencia;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Agencia almacenado(a) con exito (id_agencia'||v_id_agencia||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_agencia',v_id_agencia::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'VEN_AGEN_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		ada.torrico	
 	#FECHA:		18-11-2014 20:38:28
	***********************************/

	elsif(p_transaccion='VEN_AGEN_MOD')then

		begin
			--Sentencia de la modificacion
			update ven.tagencia set
			id_sucursal = v_parametros.id_sucursal,
			direccion = v_parametros.direccion,
			iata = v_parametros.iata,
			id_lugar_pais = v_parametros.id_lugar_pais,
			fax = v_parametros.fax,
			estacion = v_parametros.estacion,
			responsable = v_parametros.responsable,
			codigo = v_parametros.codigo,
			fono = v_parametros.fono,
			nombre = v_parametros.nombre,
			tipo_agencia = v_parametros.tipo_agencia,
			email = v_parametros.email,
			celular = v_parametros.celular,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_agencia=v_parametros.id_agencia;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Agencia modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_agencia',v_parametros.id_agencia::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'VEN_AGEN_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		ada.torrico	
 	#FECHA:		18-11-2014 20:38:28
	***********************************/

	elsif(p_transaccion='VEN_AGEN_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from ven.tagencia
            where id_agencia=v_parametros.id_agencia;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Agencia eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_agencia',v_parametros.id_agencia::varchar);
              
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
ALTER FUNCTION "ven"."ft_agencia_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
