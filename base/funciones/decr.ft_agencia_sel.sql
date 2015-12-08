CREATE OR REPLACE FUNCTION "ven"."ft_agencia_sel"(	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		Sistema de ventas
 FUNCION: 		ven.ft_agencia_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'ven.tagencia'
 AUTOR: 		 (ada.torrico)
 FECHA:	        18-11-2014 20:38:28
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

	v_nombre_funcion = 'ven.ft_agencia_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'VEN_AGEN_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ada.torrico	
 	#FECHA:		18-11-2014 20:38:28
	***********************************/

	if(p_transaccion='VEN_AGEN_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						agen.id_agencia,
						agen.id_sucursal,
						agen.direccion,
						agen.iata,
						agen.id_lugar_pais,
						agen.fax,
						agen.estacion,
						agen.responsable,
						agen.codigo,
						agen.fono,
						agen.nombre,
						agen.tipo_agencia,
						agen.email,
						agen.estado_reg,
						agen.celular,
						agen.id_usuario_ai,
						agen.id_usuario_reg,
						agen.fecha_reg,
						agen.usuario_ai,
						agen.fecha_mod,
						agen.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod	
						from ven.tagencia agen
						inner join segu.tusuario usu1 on usu1.id_usuario = agen.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = agen.id_usuario_mod
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'VEN_AGEN_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ada.torrico	
 	#FECHA:		18-11-2014 20:38:28
	***********************************/

	elsif(p_transaccion='VEN_AGEN_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_agencia)
					    from ven.tagencia agen
					    inner join segu.tusuario usu1 on usu1.id_usuario = agen.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = agen.id_usuario_mod
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
ALTER FUNCTION "ven"."ft_agencia_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
