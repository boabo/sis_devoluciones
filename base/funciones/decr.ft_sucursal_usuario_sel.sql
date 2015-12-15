CREATE OR REPLACE FUNCTION decr.ft_sucursal_usuario_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		ventas
 FUNCION: 		ven.ft_sucursal_usuario_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'ven.tsucursal_usuario'
 AUTOR: 		 (favio figueroa penarrieta)
 FECHA:	        23-09-2015 19:15:16
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

	v_nombre_funcion = 'decr.ft_sucursal_usuario_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'VEN_SUCUS_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin
 	#FECHA:		23-09-2015 19:15:16
	***********************************/

	if(p_transaccion='VEN_SUCUS_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						sucus.id_sucursal_usuario,
						sucus.tipo,
						sucus.id_usuario,
						sucus.estado_reg,
						sucus.id_sucursal,
						sucus.id_usuario_ai,
						sucus.id_usuario_reg,
						sucus.fecha_reg,
						sucus.usuario_ai,
						sucus.id_usuario_mod,
						sucus.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
						person.nombre_completo1 as desc_usuario,
						su.estacion as desc_sucursal
						from decr.tsucursal_usuario sucus
						inner join segu.tusuario usu1 on usu1.id_usuario = sucus.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = sucus.id_usuario_mod

						inner join segu.tusuario usudep on usudep.id_usuario=sucus.id_usuario
            inner join segu.vpersona person on person.id_persona=usudep.id_persona

            inner join decr.tsucursal su on su.id_sucursal = sucus.id_sucursal
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'VEN_SUCUS_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		23-09-2015 19:15:16
	***********************************/

	elsif(p_transaccion='VEN_SUCUS_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_sucursal_usuario)
					    from decr.tsucursal_usuario sucus
					    inner join segu.tusuario usu1 on usu1.id_usuario = sucus.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = sucus.id_usuario_mod
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