CREATE OR REPLACE FUNCTION decr.ft_nota_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Factura 
 FUNCION: 		fac.ft_nota_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'fac.tnota'
 AUTOR: 		 (ada.torrico)
 FECHA:	        18-11-2014 19:30:03
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
	v_id_nota	integer;
    v_parametros_json record;
    v_registros_json record;
    v_record_liquidacion record;
    v_record_dosificacion record;
    v_id_liquidacion integer;
    v_nro_nota integer;
    v_nro_nit varchar;
    v_codigo_control text;

    v_total_para_devolver numeric;
    v_credfis numeric;

BEGIN

    v_nombre_funcion = 'decr.ft_nota_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'FAC_NOT_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ada.torrico	
 	#FECHA:		18-11-2014 19:30:03
	***********************************/

	if(p_transaccion='FAC_NOT_INS')then
			
        
           
        begin
        	--Sentencia de la insercion
        	insert into decr.tnota(
			id_factura,
			id_sucursal,
			id_moneda,
			estacion,
			fecha,
			excento,
			total_devuelto,
			tcambio,
			id_liquidacion,
			nit,
			estado,
			credfis,
			nro_liquidacion,
			monto_total,
			estado_reg,
			nro_nota,
			razon,
			id_usuario_ai,
			usuario_ai,
			fecha_reg,
			id_usuario_reg,
			fecha_mod,
			id_usuario_mod
          	) values(
			v_parametros.id_factura,
			v_parametros.id_sucursal,
			v_parametros.id_moneda,
			v_parametros.estacion,
			v_parametros.fecha,
			v_parametros.excento,
			v_parametros.total_devuelto,
			v_parametros.tcambio,
			v_parametros.id_liquidacion,
			v_parametros.nit,
			v_parametros.estado,
			v_parametros.credfis,
			v_parametros.nro_liquidacion,
			v_parametros.monto_total,
			'activo',
			v_parametros.nro_nota,
			v_parametros.razon,
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			now(),
			p_id_usuario,
			null,
			null
							
			
			
			)RETURNING id_nota into v_id_nota;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Notas almacenado(a) con exito (id_nota'||v_id_nota||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_nota',v_id_nota::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'FAC_NOT_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		ada.torrico	
 	#FECHA:		18-11-2014 19:30:03
	***********************************/

	elsif(p_transaccion='FAC_NOT_MOD')then

		begin
			--Sentencia de la modificacion
			update decr.tnota set
			id_factura = v_parametros.id_factura,
			id_sucursal = v_parametros.id_sucursal,
			id_moneda = v_parametros.id_moneda,
			estacion = v_parametros.estacion,
			fecha = v_parametros.fecha,
			excento = v_parametros.excento,
			total_devuelto = v_parametros.total_devuelto,
			tcambio = v_parametros.tcambio,
			id_liquidacion = v_parametros.id_liquidacion,
			nit = v_parametros.nit,
			estado = v_parametros.estado,
			credfis = v_parametros.credfis,
			nro_liquidacion = v_parametros.nro_liquidacion,
			monto_total = v_parametros.monto_total,
			nro_nota = v_parametros.nro_nota,
			razon = v_parametros.razon,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_nota=v_parametros.id_nota;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Notas modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_nota',v_parametros.id_nota::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'FAC_NOT_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		ada.torrico	
 	#FECHA:		18-11-2014 19:30:03
	***********************************/

	elsif(p_transaccion='FAC_NOT_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from decr.tnota
            where id_nota=v_parametros.id_nota;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Notas eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_nota',v_parametros.id_nota::varchar);
              
            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'FAC_NOT_JSONIME'
 	#DESCRIPCION:	REGISTRO DE NOTA PXP
 	#AUTOR:		FAVIO FIGUEROA
 	#FECHA:		18-08-2020 19:30:03
	***********************************/

	elsif(p_transaccion='FAC_NOT_JSONIME')then

		begin
            select *
            INTO v_parametros_json
            from json_to_record(v_parametros.values_json::json) as (tipo_id varchar, liquidevolu varchar, importe_porcentaje varchar,  newRecords text, importe_total_devolver numeric);


            --liquidevolu es id_liquidacion
            v_id_liquidacion := v_parametros_json.liquidevolu;
            --obtenemos los datos relacionado a la liquidacion
            SELECT liqui.*, su.id_sucursal
            INTO v_record_liquidacion
            FROM decr.tliquidacion liqui
                     INNER JOIN vef.tpunto_venta pv on pv.id_punto_venta = liqui.id_punto_venta
                     INNER JOIN vef.tsucursal su on su.id_sucursal = pv.id_sucursal
            WHERE liqui.id_liquidacion = v_id_liquidacion;



			--recorremos los datos del detalle
            FOR v_registros_json IN (SELECT *
                                     FROM json_to_recordset(v_parametros.detalle::JSON) AS (nroliqui varchar,
                                                                                              concepto varchar,
                                                                                              precio_unitario varchar,
                                                                                              importe_original varchar,
                                                                                              importe_devolver varchar,
                                                                                              exento varchar,
                                                                                              total_devuelto varchar,
                                                                                              nro_nit varchar,
                                                                                              razon varchar,
                                                                                              fecha_fac varchar,
                                                                                              nrofac varchar,
                                                                                              nroaut varchar,
                                                                                              tipo varchar,
                                                                                              cantidad varchar
                                         ))
            LOOP
                IF (v_registros_json.tipo = 'BOLETO') THEN

                    --obtenemos la dosificacion para generar la nota
                    SELECT d.*
                    INTO v_record_dosificacion
                    FROM vef.tdosificacion d
                    WHERE d.estado_reg = 'activo'
                      AND d.id_sucursal = v_record_liquidacion.id_sucursal
                      AND d.fecha_inicio_emi <= now()::date
                      AND d.fecha_limite >= now()::date
                      AND d.tipo = 'N'
                      AND d.tipo_generacion = 'computarizada'
                      --d.id_activida_economica @> v_id_actividad_economica todo preguntar sobre esto
                        FOR UPDATE;

                    IF (v_record_dosificacion IS NULL) THEN
                        RAISE EXCEPTION 'No existe una dosificacion activa para emitir la Nota';
                    END IF;

                    v_nro_nota = v_record_dosificacion.nro_siguiente;

                    --validar que el nro de factura no supere el maximo nro de nota de la dosificaiocn
                    IF (exists(SELECT 1
                               FROM decr.tnota nota
                               WHERE nota.nro_nota = v_nro_nota::varchar
                                 AND nota.id_dosificacion = v_record_dosificacion.id_dosificacion::integer)) THEN
                        RAISE EXCEPTION 'El numero de Nota ya existe para esta dosificacion. Por favor comuniquese con el administrador del sistema';
                    END IF;



                    -- el numero nit es el primero row del detalle del servicio de devolucion tomar en cuenta eso
                    v_nro_nit = v_parametros.detalle::json->0->>'nro_nit';

                    -- generar codigo de control para la nota
                    v_codigo_control:= pxp.f_gen_cod_control(v_record_dosificacion.llave,
                                                             v_record_dosificacion.nroaut,
                                                             v_nro_nota::varchar,
                                                             v_nro_nit::varchar,
                                                             to_char(now()::date,'YYYYMMDD')::varchar,
                                                             round(v_parametros_json.importe_total_devolver::numeric,0));



                    v_total_para_devolver = v_registros_json.importe_devolver::numeric - v_registros_json.exento::numeric;

                    v_credfis = v_total_para_devolver * 0.13;

                    --insertar la nota
                    INSERT INTO decr.tnota
                    (id_usuario_reg,
                     id_usuario_mod,
                     fecha_reg,
                     fecha_mod,
                     estado_reg,
                     id_usuario_ai,
                     usuario_ai,
                     estacion,
                     id_sucursal,
                     estado,
                     nro_nota,
                     fecha,
                     razon,
                     tcambio,
                     nit,
                     id_liquidacion,
                     nro_liquidacion,
                     id_moneda,
                     monto_total,
                     excento,
                     total_devuelto,
                     credfis,
                     billete,
                     codigo_control,
                     id_dosificacion,
                     nrofac,
                     nroaut,
                     fecha_fac,
                     tipo,
                     nroaut_anterior,
                     fecha_limite)

                    VALUES (p_id_usuario,
                            NULL,
                            now(),
                            NULL,
                            'activo',
                            v_parametros._id_usuario_ai,
                            NULL,
                            v_record_liquidacion.id_sucursal,
                            '1',
                            '1',
                            v_nro_nota,
                            now(),
                            v_registros_json.razon,
                            '6.9',
                            v_nro_nit,
                            v_id_liquidacion,
                            v_registros_json.nroliqui,
                            1,
                            v_parametros_json.importe_total_devolver,
                            v_registros_json.exento::numeric,
                            v_total_para_devolver,
                            v_credfis,
                            v_registros_json.nrofac::numeric, --  esto puede ser el numero de boleto
                            v_codigo_control,
                            v_record_dosificacion.id_dosificacion,
                            v_registros_json.nrofac::bigint,
                            v_record_dosificacion.nroaut::bigint,
                            v_registros_json.fecha_fac::date,
                            v_registros_json.tipo,
                            v_registros_json.nroaut::bigint,
                            v_record_dosificacion.fecha_limite)
                    RETURNING id_nota INTO v_id_nota;


                    INSERT INTO decr.tnota_detalle
                    (id_usuario_reg,
                     estado_reg,
                     id_nota,
                     importe,
                     cantidad,
                     concepto,
                     exento,
                     total_devuelto,
                     precio_unitario)
                    VALUES (p_id_usuario,
                            'activo',
                            v_id_nota,
                            v_registros_json.importe_devolver::numeric,
                            v_registros_json.cantidad::integer,
                            v_registros_json.concepto,
                            v_registros_json.exento::numeric,
                            v_registros_json.total_devuelto::numeric,
                            v_registros_json.precio_unitario::numeric);



                END IF;


            END LOOP;

            -- actualizamos la dosificacion para aumentar el numero siguiente
            UPDATE vef.tdosificacion
            SET nro_siguiente = nro_siguiente + 1
            WHERE id_dosificacion = v_record_dosificacion.id_dosificacion;

            --select v_parametros.detalle::json;

			--RAISE EXCEPTION '%', v_parametros.detalle::json->0->'nro_nit';
			--RAISE EXCEPTION '%', v_parametros.detalle;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Nota generada');
            v_resp = pxp.f_agrega_clave(v_resp,'id_nota',v_id_nota::varchar);

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
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;