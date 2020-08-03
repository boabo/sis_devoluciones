CREATE OR REPLACE FUNCTION "decr"."ft_tipo_doc_liquidacion_sel"(	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		devoluciones
 FUNCION: 		decr.ft_tipo_doc_liquidacion_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'decr.ttipo_doc_liquidacion'
 AUTOR: 		 (admin)
 FECHA:	        17-04-2020 01:52:57
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				17-04-2020 01:52:57								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'decr.ttipo_doc_liquidacion'	
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
			    
BEGIN

	v_nombre_funcion = 'decr.ft_tipo_doc_liquidacion_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'DECR_TDOCLIQ_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin	
 	#FECHA:		17-04-2020 01:52:57
	***********************************/

	if(p_transaccion='DECR_TDOCLIQ_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						tdocliq.id_tipo_doc_liquidacion,
						tdocliq.estado_reg,
						tdocliq.tipo_documento,
						tdocliq.id_usuario_reg,
						tdocliq.fecha_reg,
						tdocliq.usuario_ai,
						tdocliq.id_usuario_ai,
						tdocliq.id_usuario_mod,
						tdocliq.fecha_mod,
						tdocliq.descripcion,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod	
						from decr.ttipo_doc_liquidacion tdocliq
						inner join segu.tusuario usu1 on usu1.id_usuario = tdocliq.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = tdocliq.id_usuario_mod
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'DECR_TDOCLIQ_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin	
 	#FECHA:		17-04-2020 01:52:57
	***********************************/

	elsif(p_transaccion='DECR_TDOCLIQ_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_tipo_doc_liquidacion)
					    from decr.ttipo_doc_liquidacion tdocliq
					    inner join segu.tusuario usu1 on usu1.id_usuario = tdocliq.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = tdocliq.id_usuario_mod
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
ALTER FUNCTION "decr"."ft_tipo_doc_liquidacion_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
