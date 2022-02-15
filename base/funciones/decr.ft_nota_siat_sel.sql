CREATE OR REPLACE FUNCTION "decr"."ft_nota_siat_sel"(	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		devoluciones
 FUNCION: 		decr.ft_nota_siat_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'decr.tnota_siat'
 AUTOR: 		 (favio.figueroa)
 FECHA:	        15-02-2022 18:29:01
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				15-02-2022 18:29:01								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'decr.tnota_siat'	
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
			    
BEGIN

	v_nombre_funcion = 'decr.ft_nota_siat_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'DECR_TNS_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		favio.figueroa	
 	#FECHA:		15-02-2022 18:29:01
	***********************************/

	if(p_transaccion='DECR_TNS_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						tns.id_nota_siat,
						tns.estado_reg,
						tns.id_liquidacion,
						tns.nro_nota,
						tns.nro_aut,
						tns.id_usuario_reg,
						tns.fecha_reg,
						tns.id_usuario_ai,
						tns.usuario_ai,
						tns.id_usuario_mod,
						tns.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
						tl.nro_liquidacion as desc_liquidacion
						from decr.tnota_siat tns
						inner join segu.tusuario usu1 on usu1.id_usuario = tns.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = tns.id_usuario_mod
						inner join decr.tliquidacion tl on tl.id_liquidacion = tns.id_liquidacion
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'DECR_TNS_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		favio.figueroa	
 	#FECHA:		15-02-2022 18:29:01
	***********************************/

	elsif(p_transaccion='DECR_TNS_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_nota_siat)
					    from decr.tnota_siat tns
					    inner join segu.tusuario usu1 on usu1.id_usuario = tns.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = tns.id_usuario_mod
						inner join decr.tliquidacion tl on tl.id_liquidacion = tns.id_liquidacion
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
ALTER FUNCTION "decr"."ft_nota_siat_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
