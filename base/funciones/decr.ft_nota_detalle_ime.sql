CREATE OR REPLACE FUNCTION decr.ft_nota_detalle_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
  /**************************************************************************
 SISTEMA:		Sistema de Factura
 FUNCION: 		fac.ft_nota_detalle_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'fac.tnota_detalle'
 AUTOR: 		 (ada.torrico)
 FECHA:	        18-11-2014 19:32:09
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

	v_nombre_funcion = 'decr.ft_nota_detalle_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'FAC_DENO_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		favio figueroa
 	#FECHA:		18-11-2014 19:32:09
	***********************************/

	if(p_transaccion='FAC_DENO_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						deno.id_nota_detalle,
						deno.id_nota,
						deno.estado_reg,
						deno.importe,
						deno.id_usuario_reg,
						deno.usuario_ai,
						deno.fecha_reg,
						deno.id_usuario_ai,
						deno.id_usuario_mod,
						deno.fecha_mod,
                        deno.cantidad,
                        deno.concepto,
                        deno.exento,
                        deno.total_devuelto,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod
						from decr.tnota_detalle deno
						inner join segu.tusuario usu1 on usu1.id_usuario = deno.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = deno.id_usuario_mod
				        where  ';



			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;


			--v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			--raise exception '%',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'FAC_DENO_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ada.torrico
 	#FECHA:		18-11-2014 19:32:09
	***********************************/

	elsif(p_transaccion='FAC_DENO_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_nota_detalle)
					    from decr.tnota_detalle deno
					    inner join segu.tusuario usu1 on usu1.id_usuario = deno.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = deno.id_usuario_mod
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