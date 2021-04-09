CREATE OR REPLACE FUNCTION "decr"."ft_nota_agencia_sel"(	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		devoluciones
 FUNCION: 		decr.ft_nota_agencia_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'decr.tnota_agencia'
 AUTOR: 		 (admin)
 FECHA:	        26-04-2020 21:14:13
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				26-04-2020 21:14:13								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'decr.tnota_agencia'	
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
			    
BEGIN

	v_nombre_funcion = 'decr.ft_nota_agencia_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'DECR_NOTAGE_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin	
 	#FECHA:		26-04-2020 21:14:13
	***********************************/

	if(p_transaccion='DECR_NOTAGE_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						notage.id_nota_agencia,
						notage.estado_reg,
						notage.id_doc_compra_venta,
						notage.id_depto_conta,
						notage.id_moneda,
						notage.estado,
						notage.nit,
						notage.nro_nota,
						notage.nro_aut_nota,
						notage.fecha,
						notage.razon,
						notage.tcambio,
						notage.monto_total,
						notage.excento,
						notage.total_devuelto,
						notage.credfis,
						notage.billete,
						notage.codigo_control,
						notage.nrofac,
						notage.nroaut,
						notage.fecha_fac,
						notage.codito_control_fac,
						notage.monto_total_fac,
						notage.iva,
						notage.neto,
						notage.obs,
						notage.id_usuario_reg,
						notage.fecha_reg,
						notage.id_usuario_ai,
						notage.usuario_ai,
						notage.id_usuario_mod,
						notage.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
						mon.moneda as desc_moneda,
						notage.id_liquidacion,
						td.nombre as desc_depto
						from decr.tnota_agencia notage
						inner join param.tmoneda mon on mon.id_moneda = notage.id_moneda
						inner join param.tdepto td on td.id_depto = notage.id_depto_conta
						inner join segu.tusuario usu1 on usu1.id_usuario = notage.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = notage.id_usuario_mod
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'DECR_NOTAGE_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin	
 	#FECHA:		26-04-2020 21:14:13
	***********************************/

	elsif(p_transaccion='DECR_NOTAGE_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_nota_agencia)
					    from decr.tnota_agencia notage
					    inner join param.tmoneda mon on mon.id_moneda = notage.id_moneda
					    inner join param.tdepto td on td.id_depto = notage.id_depto_conta
					    inner join segu.tusuario usu1 on usu1.id_usuario = notage.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = notage.id_usuario_mod
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
ALTER FUNCTION "decr"."ft_nota_agencia_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
