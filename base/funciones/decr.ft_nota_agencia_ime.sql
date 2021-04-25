CREATE OR REPLACE FUNCTION "decr"."ft_nota_agencia_ime" (	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		devoluciones
 FUNCION: 		decr.ft_nota_agencia_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'decr.tnota_agencia'
 AUTOR: 		 (admin)
 FECHA:	        26-04-2020 21:14:13
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				26-04-2020 21:14:13								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'decr.tnota_agencia'	
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_nota_agencia	integer;
	v_id_liquidacion	integer;
    v_json	varchar;

    v_estado_periodo		varchar;
    v_fecha_ini				varchar;
    v_fecha_fin 			varchar;
    v_record_nota_agencia 			record;


BEGIN

    v_nombre_funcion = 'decr.ft_nota_agencia_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'DECR_NOTAGE_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin	
 	#FECHA:		26-04-2020 21:14:13
	***********************************/

	if(p_transaccion='DECR_NOTAGE_INS')then
					
        begin

            IF (pxp.f_existe_parametro(p_tabla, 'id_liquidacion')) then
                v_id_liquidacion:= v_parametros.id_liquidacion;
            else
                v_id_liquidacion:= NULL;
            END IF;

            /*Aqui control para no Anular Facturas cuando el periodo este cerrado*/
            select
                per.fecha_ini,
                per.fecha_fin,
                cp.estado
            into
                v_fecha_ini,
                v_fecha_fin,
                v_estado_periodo
            from param.tgestion ges
                     inner join param.tperiodo per on per.id_gestion = ges.id_gestion
                     inner join conta.tperiodo_compra_venta cp on cp.id_periodo = per.id_periodo
            where v_parametros.fecha between per.fecha_ini and per.fecha_fin
              and cp.id_depto = v_parametros.id_depto_conta;
            /*********************************************************************/

            if (v_estado_periodo = 'cerrado') then
                raise exception 'No se puede registrar la nota debido a que el periodo %, %, se encuentra cerrado',v_fecha_ini,v_fecha_fin;
            end if;


            -- select estado
            -- from conta.tperiodo_compra_venta tpcv
            -- where tpcv.id_periodo = 119 and tpcv.id_depto = 36;

            --Sentencia de la insercion
        	insert into decr.tnota_agencia(
			estado_reg,
			id_doc_compra_venta,
			id_depto_conta,
			id_moneda,
			nit,
			nro_nota,
			nro_aut_nota,
			fecha,
			razon,
			--tcambio,
			monto_total,
			excento,
			total_devuelto,
			credfis,
			--billete,
			codigo_control,
			nrofac,
			nroaut,
			fecha_fac,
			codito_control_fac,
			monto_total_fac,
			iva,
			neto,
			obs,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod,
        	                               id_liquidacion,
        	                               id_periodo
          	) values(
			'activo',
			v_parametros.id_doc_compra_venta,
			v_parametros.id_depto_conta,
			v_parametros.id_moneda,
			v_parametros.nit,
			v_parametros.nro_nota,
			v_parametros.nro_aut_nota,
			v_parametros.fecha,
			v_parametros.razon,
			--v_parametros.tcambio,
			v_parametros.monto_total,
			v_parametros.excento,
			v_parametros.total_devuelto,
			v_parametros.credfis,
			--v_parametros.billete,
			v_parametros.codigo_control,
			v_parametros.nrofac,
			v_parametros.nroaut,
			v_parametros.fecha_fac,
			v_parametros.codito_control_fac,
			v_parametros.monto_total_fac,
			v_parametros.iva,
			v_parametros.neto,
			v_parametros.obs,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null,
            v_id_liquidacion,
          	         v_parametros.id_periodo
							
			
			
			)RETURNING id_nota_agencia into v_id_nota_agencia;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Nota Agencia almacenado(a) con exito (id_nota_agencia'||v_id_nota_agencia||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_nota_agencia',v_id_nota_agencia::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'DECR_NOTAGE_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin	
 	#FECHA:		26-04-2020 21:14:13
	***********************************/

	elsif(p_transaccion='DECR_NOTAGE_MOD')then

		begin

		    select *
		    into v_record_nota_agencia
		    FROM decr.tnota_agencia
            where id_nota_agencia = v_parametros.id_nota_agencia;

            /*Aqui control para no Anular Facturas cuando el periodo este cerrado*/
            select
                per.fecha_ini,
                per.fecha_fin,
                cp.estado
            into
                v_fecha_ini,
                v_fecha_fin,
                v_estado_periodo
            from param.tgestion ges
                     inner join param.tperiodo per on per.id_gestion = ges.id_gestion
                     inner join conta.tperiodo_compra_venta cp on cp.id_periodo = per.id_periodo
            where v_record_nota_agencia.fecha between per.fecha_ini and per.fecha_fin
              and cp.id_depto = v_record_nota_agencia.id_depto_conta;
            /*********************************************************************/

            if (v_estado_periodo = 'cerrado') then
                raise exception 'No se puede registrar la nota debido a que el periodo %, %, se encuentra cerrado',v_fecha_ini,v_fecha_fin;
            end if;

            /*Aqui control para no Anular Facturas cuando el periodo este cerrado*/
            select
                per.fecha_ini,
                per.fecha_fin,
                cp.estado
            into
                v_fecha_ini,
                v_fecha_fin,
                v_estado_periodo
            from param.tgestion ges
                     inner join param.tperiodo per on per.id_gestion = ges.id_gestion
                     inner join conta.tperiodo_compra_venta cp on cp.id_periodo = per.id_periodo
            where v_parametros.fecha_fac between per.fecha_ini and per.fecha_fin
              and cp.id_depto = v_parametros.id_depto_conta;
            /*********************************************************************/

            if (v_estado_periodo = 'cerrado') then
                raise exception 'No se puede registrar la nota debido a que el periodo %, %, se encuentra cerrado',v_fecha_ini,v_fecha_fin;
            end if;


			--Sentencia de la modificacion
			update decr.tnota_agencia set
			id_doc_compra_venta = v_parametros.id_doc_compra_venta,
			id_depto_conta = v_parametros.id_depto_conta,
			id_moneda = v_parametros.id_moneda,
			estado = v_parametros.estado,
			nit = v_parametros.nit,
			nro_nota = v_parametros.nro_nota,
			nro_aut_nota = v_parametros.nro_aut_nota,
			fecha = v_parametros.fecha,
			razon = v_parametros.razon,
			tcambio = v_parametros.tcambio,
			monto_total = v_parametros.monto_total,
			excento = v_parametros.excento,
			total_devuelto = v_parametros.total_devuelto,
			credfis = v_parametros.credfis,
			billete = v_parametros.billete,
			codigo_control = v_parametros.codigo_control,
			nrofac = v_parametros.nrofac,
			nroaut = v_parametros.nroaut,
			fecha_fac = v_parametros.fecha_fac,
			codito_control_fac = v_parametros.codito_control_fac,
			monto_total_fac = v_parametros.monto_total_fac,
			iva = v_parametros.iva,
			neto = v_parametros.neto,
			obs = v_parametros.obs,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai,
            id_liquidacion = v_parametros.id_liquidacion
			where id_nota_agencia=v_parametros.id_nota_agencia;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Nota Agencia modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_nota_agencia',v_parametros.id_nota_agencia::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'DECR_NOTAGE_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin	
 	#FECHA:		26-04-2020 21:14:13
	***********************************/

	elsif(p_transaccion='DECR_NOTAGE_ELI')then

		begin


            select *
            into v_record_nota_agencia
            FROM decr.tnota_agencia
            where id_nota_agencia = v_parametros.id_nota_agencia;

            /*Aqui control para no Anular Facturas cuando el periodo este cerrado*/
            select
                per.fecha_ini,
                per.fecha_fin,
                cp.estado
            into
                v_fecha_ini,
                v_fecha_fin,
                v_estado_periodo
            from param.tgestion ges
                     inner join param.tperiodo per on per.id_gestion = ges.id_gestion
                     inner join conta.tperiodo_compra_venta cp on cp.id_periodo = per.id_periodo
            where v_record_nota_agencia.fecha between per.fecha_ini and per.fecha_fin
              and cp.id_depto = v_record_nota_agencia.id_depto_conta;
            /*********************************************************************/

            if (v_estado_periodo = 'cerrado') then
                raise exception 'No se puede registrar la nota debido a que el periodo %, %, se encuentra cerrado',v_fecha_ini,v_fecha_fin;
            end if;

			--Sentencia de la eliminacion
			delete from decr.tnota_agencia
            where id_nota_agencia=v_parametros.id_nota_agencia;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Nota Agencia eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_nota_agencia',v_parametros.id_nota_agencia::varchar);
              
            --Devuelve la respuesta
            return v_resp;

		end;
	/*********************************
 	#TRANSACCION:  'DECR_DOC_JSON'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin
 	#FECHA:		26-04-2020 21:14:13
	***********************************/

	elsif(p_transaccion='DECR_DOC_JSON')then

		begin


            SELECT TO_JSON(doc)::text
            into v_json
            from (
                select *
		        FROM conta.tdoc_compra_venta
                where nro_autorizacion = v_parametros.nro_aut
                  and nro_documento = v_parametros.nro_fac
                limit 1
		          ) doc;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'json',v_json);
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje',v_json);

            --Devuelve la respuesta
            return v_resp;

		end;
	/*********************************
 	#TRANSACCION:  'DECR_NIT_JSON'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin
 	#FECHA:		26-04-2020 21:14:13
	***********************************/

	elsif(p_transaccion='DECR_NIT_JSON')then

		begin


            SELECT TO_JSON(doc)::text
            into v_json
            from (
                select razon_social, nit
		        FROM conta.tdoc_compra_venta
                where nit = v_parametros.nit
                order by fecha_reg desc
                limit 1
		          ) doc;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'json',v_json);
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje',v_json);

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
ALTER FUNCTION "decr"."ft_nota_agencia_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
