CREATE OR REPLACE FUNCTION "decr"."ft_liqui_manual_detalle_sel"(	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		devoluciones
 FUNCION: 		decr.ft_liqui_manual_detalle_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'decr.tliqui_manual_detalle'
 AUTOR: 		 (favio.figueroa)
 FECHA:	        22-03-2021 20:14:28
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				22-03-2021 20:14:28								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'decr.tliqui_manual_detalle'	
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
			    
BEGIN

	v_nombre_funcion = 'decr.ft_liqui_manual_detalle_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'DECR_TLMD_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		favio.figueroa	
 	#FECHA:		22-03-2021 20:14:28
	***********************************/

	if(p_transaccion='DECR_TLMD_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						tlmd.id_liqui_manual_detalle,
						tlmd.estado_reg,
						tlmd.id_liqui_manual,
						tlmd.administradora,
						tlmd.lote,
						tlmd.comprobante,
						tlmd.fecha,
						tlmd.nro_tarjeta,
						tlmd.concepto_original,
						tlmd.concepto_devolver,
						tlmd.importe_original,
						tlmd.importe_devolver,
						tlmd.descripcion,
						tlmd.id_usuario_reg,
						tlmd.fecha_reg,
						tlmd.id_usuario_ai,
						tlmd.usuario_ai,
						tlmd.id_usuario_mod,
						tlmd.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod	
						from decr.tliqui_manual_detalle tlmd
						inner join segu.tusuario usu1 on usu1.id_usuario = tlmd.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = tlmd.id_usuario_mod
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'DECR_TLMD_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		favio.figueroa	
 	#FECHA:		22-03-2021 20:14:28
	***********************************/

	elsif(p_transaccion='DECR_TLMD_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_liqui_manual_detalle)
					    from decr.tliqui_manual_detalle tlmd
					    inner join segu.tusuario usu1 on usu1.id_usuario = tlmd.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = tlmd.id_usuario_mod
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
ALTER FUNCTION "decr"."ft_liqui_manual_detalle_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
