CREATE OR REPLACE FUNCTION "decr"."ft_liqui_manual_sel"(	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		devoluciones
 FUNCION: 		decr.ft_liqui_manual_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'decr.tliqui_manual'
 AUTOR: 		 (favio.figueroa)
 FECHA:	        21-03-2021 22:59:57
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				21-03-2021 22:59:57								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'decr.tliqui_manual'	
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
			    
BEGIN

	v_nombre_funcion = 'decr.ft_liqui_manual_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'DECR_LIQUIMA_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		favio.figueroa	
 	#FECHA:		21-03-2021 22:59:57
	***********************************/

	if(p_transaccion='DECR_LIQUIMA_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						liquima.id_liqui_manual,
						liquima.estado_reg,
						liquima.id_liquidacion,
						liquima.tipo_manual,
						liquima.id_usuario_reg,
						liquima.fecha_reg,
						liquima.id_usuario_ai,
						liquima.usuario_ai,
						liquima.id_usuario_mod,
						liquima.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod	
						from decr.tliqui_manual liquima
						inner join segu.tusuario usu1 on usu1.id_usuario = liquima.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = liquima.id_usuario_mod
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'DECR_LIQUIMA_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		favio.figueroa	
 	#FECHA:		21-03-2021 22:59:57
	***********************************/

	elsif(p_transaccion='DECR_LIQUIMA_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_liqui_manual)
					    from decr.tliqui_manual liquima
					    inner join segu.tusuario usu1 on usu1.id_usuario = liquima.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = liquima.id_usuario_mod
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
ALTER FUNCTION "decr"."ft_liqui_manual_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
