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

    	    v_consulta := '
            WITH t_venta_detalle AS
                     (
                         SELECT string_agg(tvd.id_venta_detalle::text, '','')::varchar as id_venta_detalle, tv.id_venta
                         from vef.tventa_detalle tvd
                                  inner join vef.tventa tv on tv.id_venta = tvd.id_venta
                                  INNER JOIN decr.tliquidacion tl on tl.id_venta = tv.id_venta
                         ';
            IF pxp.f_existe_parametro(p_tabla, 'id_liquidacion') THEN
                v_consulta := v_consulta || 'where liqui.id_id_liquidacion= '||v_parametros.id_liquidacion||' GROUP BY tv.id_venta ) ';

            ELSE
v_consulta := v_consulta || 'GROUP BY tv.id_venta ) ';

            END IF;
    		--Sentencia de la consulta
			v_consulta:= v_consulta || 'select
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
			pv.nombre as desc_punto_venta,
			nota.nro_nota,
			liqui.id_estado_wf,
			liqui.id_proceso_wf,
			liqui.num_tramite,
			tv.nro_factura,
			tv.nombre_factura,
			tb.id_boleto as id,
			1::integer as cantidad,
			liqui.id_venta,
			tmpp.name as desc_forma_pago,
			       tvd.id_venta_detalle,
			       liqui.exento,
			       liqui.id_medio_pago,
			       liqui.id_moneda

from decr.tliquidacion liqui
         inner join segu.tusuario usu1 on usu1.id_usuario = liqui.id_usuario_reg
         left join segu.tusuario usu2 on usu2.id_usuario = liqui.id_usuario_mod
inner join decr.ttipo_doc_liquidacion ttdl on ttdl.id_tipo_doc_liquidacion = liqui.id_tipo_doc_liquidacion
inner join decr.ttipo_liquidacion ttl on ttl.id_tipo_liquidacion = liqui.id_tipo_liquidacion
                  inner join obingresos.tmedio_pago_pw tmpp on tmpp.id_medio_pago_pw = liqui.id_medio_pago
LEFT JOIN obingresos.tboleto tb on tb.id_boleto = liqui.id_boleto
			            LEFT JOIN vef.tventa tv on tv.id_venta = liqui.id_venta
                         LEFT JOIN  t_venta_detalle tvd on tvd.id_venta = tv.id_venta
			            inner join vef.tpunto_venta pv on pv.id_punto_venta = liqui.id_punto_venta
			            left join decr.tnota nota on nota.id_liquidacion::integer = liqui.id_liquidacion
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
			v_consulta:='select count(liqui.id_liquidacion)
					    from decr.tliquidacion liqui
         inner join segu.tusuario usu1 on usu1.id_usuario = liqui.id_usuario_reg
         left join segu.tusuario usu2 on usu2.id_usuario = liqui.id_usuario_mod
inner join decr.ttipo_doc_liquidacion ttdl on ttdl.id_tipo_doc_liquidacion = liqui.id_tipo_doc_liquidacion
inner join decr.ttipo_liquidacion ttl on ttl.id_tipo_liquidacion = liqui.id_tipo_liquidacion
                  inner join obingresos.tmedio_pago_pw tmpp on tmpp.id_medio_pago_pw = liqui.id_medio_pago
LEFT JOIN obingresos.tboleto tb on tb.id_boleto = liqui.id_boleto
			            LEFT JOIN vef.tventa tv on tv.id_venta = liqui.id_venta
			            inner join vef.tpunto_venta pv on pv.id_punto_venta = liqui.id_punto_venta
			            left join decr.tnota nota on nota.id_liquidacion::integer = liqui.id_liquidacion
					    where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'DECR_LIQUIDET_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		Favio Figueroa
 	#FECHA:		17-04-2020 01:54:37
	***********************************/

    elsif(p_transaccion='DECR_LIQUIDET_SEL')then

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
			v_consulta:='SELECT
    tl.id_liquidacion,
    tl.estacion,
    tl.nro_liquidacion,
    tl.estado_reg,
    tl.tipo_de_cambio,
    tl.descripcion,
    tl.nombre_cheque,
    tl.fecha_liqui,
    tl.tramo_devolucion,
    tl.util,
    tl.fecha_pago,
    tl.id_tipo_doc_liquidacion,
    tl.pv_agt,
    tl.noiata,
    tl.id_tipo_liquidacion,
    tl.id_boleto,
    tl.tramo,
    tl.nombre,
    tl.moneda_liq,
    tl.estado,
    tl.cheque,
    tl.id_usuario_reg,
    tl.fecha_reg,
    tl.usuario_ai,
    tl.id_usuario_ai,
    tl.id_usuario_mod,
    tl.fecha_mod,
    usu1.cuenta as usr_reg,
    usu2.cuenta as usr_mod,
    ttdl.tipo_documento as desc_tipo_documento,
    ttl.tipo_liquidacion as desc_tipo_liquidacion,
    tv.nro_factura::varchar as desc_nro_boleto,
    tv.nit::varchar as nro_nit,
    tv.nombre_factura AS razon,
    tv.fecha::date as fecha_fac,
    tv.total_venta::numeric as total,
    td.nroaut as nro_aut,
    tv.nro_factura as nro_fac,
    tci.desc_ingas::varchar as concepto,
    ''FACTURA''::VARCHAR AS tipo,
    tvd.precio::numeric AS precio_unitario,
    tvd.precio::numeric AS importe_original,
    tl.punto_venta,
    tl.moneda_emision,
    tl.importe_neto,
    tl.tasas,
    tl.importe_total,
    '||v_sum_descuentos||' as sum_descuentos,
    --tl.importe_total as importe_devolver, -- solo funciona para generar nota y solo deberia funcinar para boleto
    tvd.precio::numeric as importe_devolver,
    tl.id_punto_venta,
    pv.nombre as desc_punto_venta,
    1::varchar as nro_nota,
    tl.id_estado_wf,
    tl.id_proceso_wf,
    tl.num_tramite,
    tv.nro_factura,
    tv.nombre_factura,
    tvd.id_venta_detalle as id,
    1::integer as cantidad
FROM vef.tventa_detalle tvd

         inner join segu.tusuario usu1 on usu1.id_usuario = tvd.id_usuario_reg
         left join segu.tusuario usu2 on usu2.id_usuario = tvd.id_usuario_mod
         inner JOIN decr.tliqui_venta_detalle lvd on lvd.id_venta_detalle = tvd.id_venta_detalle
         inner join param.tconcepto_ingas tci on tci.id_concepto_ingas = tvd.id_producto
         INNER JOIN decr.tliquidacion tl on tl.id_liquidacion = lvd.id_liquidacion
         inner join decr.ttipo_doc_liquidacion ttdl on ttdl.id_tipo_doc_liquidacion = tl.id_tipo_doc_liquidacion
         inner join decr.ttipo_liquidacion ttl on ttl.id_tipo_liquidacion = tl.id_tipo_liquidacion
         INNER JOIN vef.tventa tv on tv.id_venta = tl.id_venta
         inner join vef.tpunto_venta pv on pv.id_punto_venta = tl.id_punto_venta
inner join vef.tdosificacion td on td.id_dosificacion = tv.id_dosificacion
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'DECR_LIQUIDET_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		Favio Figueroa
 	#FECHA:		17-04-2020 01:54:37
	***********************************/

	elsif(p_transaccion='DECR_LIQUIDET_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(tvd.id_venta_detalle)
         FROM vef.tventa_detalle tvd

         inner join segu.tusuario usu1 on usu1.id_usuario = tvd.id_usuario_reg
         left join segu.tusuario usu2 on usu2.id_usuario = tvd.id_usuario_mod
         inner JOIN decr.tliqui_venta_detalle lvd on lvd.id_venta_detalle = tvd.id_venta_detalle
         inner join param.tconcepto_ingas tci on tci.id_concepto_ingas = tvd.id_producto
         INNER JOIN decr.tliquidacion tl on tl.id_liquidacion = lvd.id_liquidacion
         inner join decr.ttipo_doc_liquidacion ttdl on ttdl.id_tipo_doc_liquidacion = tl.id_tipo_doc_liquidacion
         inner join decr.ttipo_liquidacion ttl on ttl.id_tipo_liquidacion = tl.id_tipo_liquidacion
         INNER JOIN vef.tventa tv on tv.id_venta = tl.id_venta
         inner join vef.tpunto_venta pv on pv.id_punto_venta = tl.id_punto_venta
inner join vef.tdosificacion td on td.id_dosificacion = tv.id_dosificacion
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
				        where  nro_boleto = '''||v_parametros.nro_boleto||''' ';

            --Definicion de la respuesta
            --v_consulta:=v_consulta||v_parametros.filtro;
            --v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

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
				        where  nro_boleto = '''||v_parametros.nro_boleto||''' ';


            --Definicion de la respuesta
            --v_consulta:=v_consulta||v_parametros.filtro;

            --Devuelve la respuesta
            return v_consulta;

        end;
	/*********************************
 	#TRANSACCION:  'DECR_FACORI_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		Favio Figueroa
 	#FECHA:		17-04-2020 01:54:37
	***********************************/

	elsif(p_transaccion='DECR_FACORI_SEL')then

    	begin
            --RAISE EXCEPTION '%','llega';

            --Sentencia de la consulta
            v_consulta:='
            select tci.desc_ingas::varchar as concepto

            from vef.tventa tv
            inner join vef.tventa_detalle tvd on tvd.id_venta = tv.id_venta
            inner join param.tconcepto_ingas tci on tci.id_concepto_ingas = tvd.id_producto
            inner join vef.tdosificacion td on td.id_dosificacion = tv.id_dosificacion
            where ';
            --where tv.nro_factura = 6347 and td.nroaut = 402401000007295::varchar
            v_consulta:=v_consulta||v_parametros.filtro;

            --Devuelve la respuesta
            return v_consulta;

		end;


    /*********************************
     #TRANSACCION:  'DECR_FACORI_CONT'
     #DESCRIPCION:	Conteo de registros
     #AUTOR:		Favio Figueroa
     #FECHA:		17-04-2020 01:54:37
    ***********************************/

    elsif(p_transaccion='DECR_FACORI_CONT')then

        begin
            --Sentencia de la consulta de conteo de registros
            v_consulta:='select count(tvd.id_venta_detalle)
						from vef.tventa tv
            inner join vef.tventa_detalle tvd on tvd.id_venta = tv.id_venta
            inner join param.tconcepto_ingas tci on tci.id_concepto_ingas = tvd.id_producto
            inner join vef.tdosificacion td on td.id_dosificacion = tv.id_dosificacion
            where ';

            --Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;

            --Devuelve la respuesta
            return v_consulta;

        end;
	/*********************************
 	#TRANSACCION:  'DECR_DEPOS_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		Favio Figueroa
 	#FECHA:		17-04-2020 01:54:37
	***********************************/

	elsif(p_transaccion='DECR_DEPOS_SEL')then

    	begin
            --RAISE EXCEPTION '%','llega';

            --Sentencia de la consulta
            v_consulta:='
            SELECT td.id_deposito,
                    td.nro_deposito,
                    td.monto_deposito,
                    td.fecha,
                    td.saldo,
monto_total from obingresos.tdeposito td
            where ';
            v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;


            --Devuelve la respuesta
            return v_consulta;

		end;


    /*********************************
     #TRANSACCION:  'DECR_DEPOS_CONT'
     #DESCRIPCION:	Conteo de registros
     #AUTOR:		Favio Figueroa
     #FECHA:		17-04-2020 01:54:37
    ***********************************/

    elsif(p_transaccion='DECR_DEPOS_CONT')then

        begin
            --Sentencia de la consulta de conteo de registros
            v_consulta:='select count(td.id_deposito)
						from obingresos.tdeposito td
            where ';

            --Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;

            --Devuelve la respuesta
            return v_consulta;

        end;

    /*********************************
     #TRANSACCION:  'DECR_FACTUCOM_SEL'
     #DESCRIPCION:	Conteo de registros
     #AUTOR:		Favio Figueroa
     #FECHA:		28-02-2021 01:54:37
    ***********************************/

    elsif(p_transaccion='DECR_FACTUCOM_SEL')then

        begin
            --Sentencia de la consulta de conteo de registros
            v_consulta:='WITH t_factucom AS (
                            SELECT * FROM dblink(''dbname=dbendesis host=192.168.100.30 user=ende_pxp password=ende_pxp'',
                                                 ''SELECT id_factucom,nroaut,nrofac,monto,razon_cliente,fecha FROM informix.tif_factucom where nroaut = '||v_parametros.nro_aut||' and nrofac = '||v_parametros.nro_fac||' ''
                                              ) AS d (id_factucom integer, nroaut numeric, nrofac numeric, monto numeric, razon_cliente varchar, fecha date)
                        )select * from t_factucom';

            --Devuelve la respuesta
            return v_consulta;

        end;
	/*********************************
     #TRANSACCION:  'DECR_FACTUCOMCON_SEL'
     #DESCRIPCION:	Conteo de registros
     #AUTOR:		Favio Figueroa
     #FECHA:		28-02-2021 01:54:37
    ***********************************/

    elsif(p_transaccion='DECR_FACTUCOMCON_SEL')then

        begin
            --Sentencia de la consulta de conteo de registros
            v_consulta:='WITH t_factucomcon AS (
                            SELECT * FROM dblink(''dbname=dbendesis host=192.168.100.30 user=ende_pxp password=ende_pxp'',
                                                 ''SELECT id_factucomcon,id_factucom,cantidad,preciounit,importe,concepto FROM informix.tif_factucomcon where id_factucom = '||v_parametros.id_factucom||' ''
                                              ) AS d (id_factucomcon integer, id_factucom integer, cantidad numeric, preciounit numeric, importe numeric, concepto varchar)
                        )select * from t_factucomcon';

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
