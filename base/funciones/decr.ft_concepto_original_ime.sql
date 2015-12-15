CREATE OR REPLACE FUNCTION "decr"."ft_concepto_original_ime" (	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		devoluciones
 FUNCION: 		decr.ft_concepto_original_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'decr.tconcepto_original'
 AUTOR: 		 (admin)
 FECHA:	        15-12-2015 19:08:12
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
	v_id_concepto_original	integer;
  v_registros_json				RECORD;
	v_nro_fac								NUMERIC;
	v_nro_autorizacion_anterior NUMERIC;
	v_id_nota								INTEGER;
			    
BEGIN

    v_nombre_funcion = 'decr.ft_concepto_original_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'DECR_CONO_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin	
 	#FECHA:		15-12-2015 19:08:12
	***********************************/

	if(p_transaccion='DECR_CONO_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into decr.tconcepto_original(
			estado_reg,
			tipo,
			concepto,
			importe_original,
			id_nota,
			id_usuario_reg,
			fecha_reg,
			usuario_ai,
			id_usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			'activo',
			v_parametros.tipo,
			v_parametros.concepto,
			v_parametros.importe_original,
			v_parametros.id_nota,
			p_id_usuario,
			now(),
			v_parametros._nombre_usuario_ai,
			v_parametros._id_usuario_ai,
			null,
			null
							
			
			
			)RETURNING id_concepto_original into v_id_concepto_original;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Concepto Original almacenado(a) con exito (id_concepto_original'||v_id_concepto_original||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_concepto_original',v_id_concepto_original::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'DECR_CONO_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin	
 	#FECHA:		15-12-2015 19:08:12
	***********************************/

	elsif(p_transaccion='DECR_CONO_MOD')then

		begin
			--Sentencia de la modificacion
			update decr.tconcepto_original set
			tipo = v_parametros.tipo,
			concepto = v_parametros.concepto,
			importe_original = v_parametros.importe_original,
			id_nota = v_parametros.id_nota,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_concepto_original=v_parametros.id_concepto_original;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Concepto Original modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_concepto_original',v_parametros.id_concepto_original::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;


	/*********************************
 	#TRANSACCION:  'DECR_CONO_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin	
 	#FECHA:		15-12-2015 19:08:12
	***********************************/

	elsif(p_transaccion='DECR_CONO_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from decr.tconcepto_original
            where id_concepto_original=v_parametros.id_concepto_original;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Concepto Original eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_concepto_original',v_parametros.id_concepto_original::varchar);
              
            --Devuelve la respuesta
            return v_resp;

		end;


  /*********************************
   #TRANSACCION:  'DECR_CONO_JSIN'
   #DESCRIPCION:	insercion de datos con json
   #AUTOR:		favio figueroa
   #FECHA:		15-12-2015 19:08:12
  ***********************************/

  elsif(p_transaccion='DECR_CONO_JSIN')then


    begin
      --Sentencia de la eliminacion


      FOR v_registros_json
      IN (SELECT *
          FROM json_populate_recordset(NULL :: decr.json_conceptos_originales, v_parametros.arra_json :: JSON))
      LOOP

				SELECT id_nota
				INTO v_id_nota
					FROM decr.tnota
						WHERE nroaut_anterior = v_registros_json.nroaut::BIGINT and nrofac = v_registros_json.nrofac::BIGINT;


        insert into decr.tconcepto_original(
          estado_reg,
          tipo,
          concepto,
          importe_original,
          id_nota,
          id_usuario_reg,
          fecha_reg,
          usuario_ai,
          id_usuario_ai,
          id_usuario_mod,
          fecha_mod
        ) values(
          'activo',
         'manual',
          v_registros_json.concepto,
          v_registros_json.importe_original,
          v_id_nota,
          p_id_usuario,
          now(),
          v_parametros._nombre_usuario_ai,
          v_parametros._id_usuario_ai,
          null,
          null
        )RETURNING id_concepto_original into v_id_concepto_original;


			END LOOP ;



      --Definicion de la respuesta
      v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Concepto Original eliminado(a)');
      v_resp = pxp.f_agrega_clave(v_resp,'id_concepto_original',v_id_concepto_original::varchar);

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
ALTER FUNCTION "decr"."ft_concepto_original_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
