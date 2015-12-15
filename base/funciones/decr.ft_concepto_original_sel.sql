CREATE OR REPLACE FUNCTION "decr"."ft_concepto_original_sel"(	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		devoluciones
 FUNCION: 		decr.ft_concepto_original_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'decr.tconcepto_original'
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

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
			    
BEGIN

	v_nombre_funcion = 'decr.ft_concepto_original_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'DECR_CONO_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin	
 	#FECHA:		15-12-2015 19:08:12
	***********************************/

	if(p_transaccion='DECR_CONO_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						cono.id_concepto_original,
						cono.estado_reg,
						cono.tipo,
						cono.concepto,
						cono.importe_original,
						cono.id_nota,
						cono.id_usuario_reg,
						cono.fecha_reg,
						cono.usuario_ai,
						cono.id_usuario_ai,
						cono.id_usuario_mod,
						cono.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod	
						from decr.tconcepto_original cono
						inner join segu.tusuario usu1 on usu1.id_usuario = cono.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = cono.id_usuario_mod
				        where  ';



			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			--v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'DECR_CONO_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin	
 	#FECHA:		15-12-2015 19:08:12
	***********************************/

	elsif(p_transaccion='DECR_CONO_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_concepto_original)
					    from decr.tconcepto_original cono
					    inner join segu.tusuario usu1 on usu1.id_usuario = cono.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = cono.id_usuario_mod
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
ALTER FUNCTION "decr"."ft_concepto_original_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
