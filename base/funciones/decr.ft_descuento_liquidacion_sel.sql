CREATE OR REPLACE FUNCTION "decr"."ft_descuento_liquidacion_sel"(	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		devoluciones
 FUNCION: 		decr.ft_descuento_liquidacion_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'decr.tdescuento_liquidacion'
 AUTOR: 		 (admin)
 FECHA:	        17-04-2020 01:55:03
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				17-04-2020 01:55:03								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'decr.tdescuento_liquidacion'	
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
			    
BEGIN

	v_nombre_funcion = 'decr.ft_descuento_liquidacion_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'DECR_DESLIQUI_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin	
 	#FECHA:		17-04-2020 01:55:03
	***********************************/

	if(p_transaccion='DECR_DESLIQUI_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						desliqui.id_descuento_liquidacion,
						desliqui.contabilizar,
						desliqui.importe,
						desliqui.estado_reg,
						desliqui.id_concepto_ingas,
						desliqui.id_liquidacion,
						desliqui.sobre,
						desliqui.fecha_reg,
						desliqui.usuario_ai,
						desliqui.id_usuario_reg,
						desliqui.id_usuario_ai,
						desliqui.fecha_mod,
						desliqui.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
						tci.desc_ingas as desc_desc_ingas
						from decr.tdescuento_liquidacion desliqui
						inner join segu.tusuario usu1 on usu1.id_usuario = desliqui.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = desliqui.id_usuario_mod
						INNER JOIN param.tconcepto_ingas tci on tci.id_concepto_ingas = desliqui.id_concepto_ingas
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'DECR_DESLIQUI_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin	
 	#FECHA:		17-04-2020 01:55:03
	***********************************/

	elsif(p_transaccion='DECR_DESLIQUI_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_descuento_liquidacion)
					    from decr.tdescuento_liquidacion desliqui
					    inner join segu.tusuario usu1 on usu1.id_usuario = desliqui.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = desliqui.id_usuario_mod
						INNER JOIN param.tconcepto_ingas tci on tci.id_concepto_ingas = desliqui.id_concepto_ingas
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
ALTER FUNCTION "decr"."ft_descuento_liquidacion_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
