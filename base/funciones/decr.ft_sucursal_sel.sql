CREATE OR REPLACE FUNCTION decr.ft_sucursal_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de ventas
 FUNCION: 		ven.ft_sucursal_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'ven.tsucursal'
 AUTOR: 		 (ada.torrico)
 FECHA:	        18-11-2014 20:00:02
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

	v_nombre_funcion = 'decr.ft_sucursal_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VEN_SUCU_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ada.torrico
 	#FECHA:		18-11-2014 20:00:02
	***********************************/

	if(p_transaccion='VEN_SUCU_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						sucu.id_sucursal,
						sucu.alcaldia,
						sucu.estacion,
						sucu.telefono,
						sucu.estado_reg,
						sucu.direccion,
						sucu.razon,
						sucu.fecha_reg,
						sucu.usuario_ai,
						sucu.id_usuario_reg,
						sucu.id_usuario_ai,
						sucu.fecha_mod,
						sucu.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
							sucu.sucursal,
							sucu.sucursal || '' '' || sucu.estacion || '' '' || sucu.direccion as sucursal_descriptivo
						from decr.tsucursal sucu
						inner join segu.tusuario usu1 on usu1.id_usuario = sucu.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = sucu.id_usuario_mod
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VEN_SUCU_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ada.torrico
 	#FECHA:		18-11-2014 20:00:02
	***********************************/

	elsif(p_transaccion='VEN_SUCU_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_sucursal)
					    from decr.tsucursal sucu
					    inner join segu.tusuario usu1 on usu1.id_usuario = sucu.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = sucu.id_usuario_mod
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
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;