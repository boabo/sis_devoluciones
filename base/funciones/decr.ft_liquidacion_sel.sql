CREATE OR REPLACE FUNCTION "decr"."ft_liquidacion_sel"(	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		devoluciones
 FUNCION: 		decr.ft_liquidacion_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'decr.tliquidacion'
 AUTOR: 		 (admin)
 FECHA:	        17-04-2020 01:54:37
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				17-04-2020 01:54:37								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'decr.tliquidacion'	
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
    v_sum_descuentos  numeric(10,2) DEFAULT 0;
    v_importe_devolver  numeric(10,2) DEFAULT 0;

BEGIN

	v_nombre_funcion = 'decr.ft_liquidacion_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'DECR_LIQUI_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		Favio Figueroa
 	#FECHA:		17-04-2020 01:54:37
	***********************************/

	if(p_transaccion='DECR_LIQUI_SEL')then

    	begin

    	    --obtener descuentos si existe un filtro de id_liquidacion
    	    IF pxp.f_existe_parametro(p_tabla, 'id_liquidacion') THEN
                SELECT sum(tci.precio) as sum_descuentos
                INTO v_sum_descuentos
    	        FROM decr.tdescuento_liquidacion tdl
                inner JOIN param.tconcepto_ingas tci on tci.id_concepto_ingas = tdl.id_concepto_ingas
    	        WHERE tdl.id_liquidacion = v_parametros.id_liquidacion;

            END IF;

    		--Sentencia de la consulta
			v_consulta:='select
						liqui.id_liquidacion,
						liqui.estacion,
						liqui.nro_liquidacion,
						liqui.estado_reg,
						liqui.tipo_de_cambio,
						liqui.descripcion,
						liqui.nombre_cheque,
						liqui.fecha_liqui,
						liqui.tramo_devolucion,
						liqui.util,
						liqui.fecha_pago,
						liqui.id_tipo_doc_liquidacion,
						liqui.pv_agt,
						liqui.noiata,
						liqui.id_tipo_liquidacion,
						liqui.id_forma_pago,
						liqui.id_boleto,
						liqui.tramo,
						liqui.nombre,
						liqui.moneda_liq,
						liqui.estado,
						liqui.cheque,
						liqui.id_usuario_reg,
						liqui.fecha_reg,
						liqui.usuario_ai,
						liqui.id_usuario_ai,
						liqui.id_usuario_mod,
						liqui.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
						ttdl.tipo_documento as desc_tipo_documento,
       ttl.tipo_liquidacion as desc_tipo_liquidacion,
       tb.nro_boleto as desc_nro_boleto,
			            tb.nit::varchar as nro_nit,
       tb.razon,
       tb.fecha_emision as fecha_fac,
       tb.total,
       1 as nro_aut,
       tb.nro_boleto as nro_fac,
			       concat(tb.nro_boleto,''/'',liqui.tramo_devolucion):: varchar as concepto,
			''BOLETO''::VARCHAR AS tipo,
			tb.total AS precio_unitario,
			tb.total AS importe_original,
			liqui.punto_venta,
			liqui.moneda_emision,
			liqui.importe_neto,
			liqui.tasas,
			liqui.importe_total,
			'||v_sum_descuentos||' as sum_descuentos,
			liqui.importe_total - '||v_sum_descuentos||' as importe_devolver, -- solo funciona para generar nota
			liqui.id_punto_venta,
			pv.nombre as desc_punto_venta
from decr.tliquidacion liqui
         inner join segu.tusuario usu1 on usu1.id_usuario = liqui.id_usuario_reg
         left join segu.tusuario usu2 on usu2.id_usuario = liqui.id_usuario_mod
inner join decr.ttipo_doc_liquidacion ttdl on ttdl.id_tipo_doc_liquidacion = liqui.id_tipo_doc_liquidacion
inner join decr.ttipo_liquidacion ttl on ttl.id_tipo_liquidacion = liqui.id_tipo_liquidacion
INNER JOIN obingresos.tboleto tb on tb.id_boleto = liqui.id_boleto
			            inner join vef.tpunto_venta pv on pv.id_punto_venta = liqui.id_punto_venta
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'DECR_LIQUI_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		Favio Figueroa
 	#FECHA:		17-04-2020 01:54:37
	***********************************/

	elsif(p_transaccion='DECR_LIQUI_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_liquidacion)
					    from decr.tliquidacion liqui
         inner join segu.tusuario usu1 on usu1.id_usuario = liqui.id_usuario_reg
         left join segu.tusuario usu2 on usu2.id_usuario = liqui.id_usuario_mod
inner join decr.ttipo_doc_liquidacion ttdl on ttdl.id_tipo_doc_liquidacion = liqui.id_tipo_doc_liquidacion
inner join decr.ttipo_liquidacion ttl on ttl.id_tipo_liquidacion = liqui.id_tipo_liquidacion
INNER JOIN obingresos.tboleto tb on tb.id_boleto = liqui.id_boleto
			            inner join vef.tpunto_venta pv on pv.id_punto_venta = liqui.id_punto_venta
					    where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'DECR_TRAMO_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		Favio Figueroa
 	#FECHA:		17-04-2020 01:54:37
	***********************************/

	elsif(p_transaccion='DECR_TRAMO_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='WITH billete as
                        (
                            select bc.billete, bc.origen, bc.destino
                            from informix.boletos_cupon2 bc
                            where bc.i = '||v_parametros.billete||'
                            order by bc.cupon asc
                        )
                        SELECT string_agg(concat_ws(''/'', b.origen, b.destino), ''/'') AS list_tramo
                        FROM billete b
                        GROUP BY 1';

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'DECR_NUMLIQ_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		Favio Figueroa
 	#FECHA:		17-04-2020 01:54:37
	***********************************/

	elsif(p_transaccion='DECR_NUMLIQ_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='SELECT * FROM param.f_obtener_correlativo(
                            ''LIQ'||v_parametros.estacion||'DEV'', --codigo documento
                            NULL,-- par_id,
                            NULL, --id_uo
                            NULL, --depto
                            1, --usuario
                            ''DECR'',
                            NULL,--formato
                            1,
                        4)';

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'DECR_BOL_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		Favio Figueroa
 	#FECHA:		17-04-2020 01:54:37
	***********************************/

	elsif(p_transaccion='DECR_BOL_SEL')then

    	begin
    		--Sentencia de la consulta
            v_consulta:='select
						bol.id_boleto, bol.nro_boleto
						from obingresos.tboleto bol
				        where  ';

            --Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

            --Devuelve la respuesta
            return v_consulta;

		end;


    /*********************************
     #TRANSACCION:  'DECR_BOL_CONT'
     #DESCRIPCION:	Conteo de registros
     #AUTOR:		Favio Figueroa
     #FECHA:		17-04-2020 01:54:37
    ***********************************/

    elsif(p_transaccion='DECR_BOL_CONT')then

        begin
            --Sentencia de la consulta de conteo de registros
            v_consulta:='select
						count(bol.id_boleto)
						from obingresos.tboleto bol
				        where  ';


            --Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;

            --Devuelve la respuesta
            return v_consulta;

        end;
	else

		raise exception 'Transaccion inexistente';

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
ALTER FUNCTION "decr"."ft_liquidacion_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
