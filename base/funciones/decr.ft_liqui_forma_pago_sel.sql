CREATE OR REPLACE FUNCTION "decr"."ft_liqui_forma_pago_sel"(	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		devoluciones
 FUNCION: 		decr.ft_liqui_forma_pago_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'decr.tliqui_forma_pago'
 AUTOR: 		 (admin)
 FECHA:	        06-01-2021 03:55:40
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				06-01-2021 03:55:40								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'decr.tliqui_forma_pago'	
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
			    
BEGIN

	v_nombre_funcion = 'decr.ft_liqui_forma_pago_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'DECR_TLP_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin	
 	#FECHA:		06-01-2021 03:55:40
	***********************************/

	if(p_transaccion='DECR_TLP_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
                            tlp.id_liqui_forma_pago,
                            tlp.estado_reg,
                            tlp.id_liquidacion,
                            tlp.id_medio_pago,
                            tlp.pais,
                            tlp.ciudad,
                            tlp.fac_reporte,
                            tlp.cod_est,
                            tlp.lote,
                            tlp.comprobante,
                            tlp.fecha_tarjeta,
                            tlp.nro_tarjeta,
                            tlp.importe,
                            tlp.id_usuario_reg,
                            tlp.fecha_reg,
                            tlp.id_usuario_ai,
                            tlp.usuario_ai,
                            tlp.id_usuario_mod,
                            tlp.fecha_mod,
                            usu1.cuenta as usr_reg,
                            usu2.cuenta as usr_mod,
                            tmpp.name as desc_medio_pago
						from decr.tliqui_forma_pago tlp
						inner join segu.tusuario usu1 on usu1.id_usuario = tlp.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = tlp.id_usuario_mod
						inner join obingresos.tmedio_pago_pw tmpp on tmpp.id_medio_pago_pw = tlp.id_medio_pago
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'DECR_TLP_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin	
 	#FECHA:		06-01-2021 03:55:40
	***********************************/

	elsif(p_transaccion='DECR_TLP_CONT')then


		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_liqui_forma_pago)
					    from decr.tliqui_forma_pago tlp
					    inner join segu.tusuario usu1 on usu1.id_usuario = tlp.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = tlp.id_usuario_mod
						inner join obingresos.tmedio_pago_pw tmpp on tmpp.id_medio_pago_pw = tlp.id_medio_pago
					    where ';
			
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
ALTER FUNCTION "decr"."ft_liqui_forma_pago_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
