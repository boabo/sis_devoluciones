CREATE OR REPLACE FUNCTION decr.ft_nota_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar

)
RETURNS varchar AS
$body$
  /**************************************************************************
 SISTEMA:		Sistema de Factura 
 FUNCION: 		fac.ft_nota_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'fac.tnota'
 AUTOR: 		 (ada.torrico)
 FECHA:	        18-11-2014 19:30:03
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
	v_sucursales VARCHAR  ;
	v_suc VARCHAR;
BEGIN

	v_nombre_funcion = 'decr.ft_nota_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'FAC_NOT_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		favio figueroa
 	#FECHA:		11-02-2015 11:30:03
	***********************************/


	if(p_transaccion='FAC_NOT_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
                  no.id_nota,
                  no.id_sucursal,
                  no.id_moneda,
                  no.estacion,
                  no.fecha,
                  no.excento,
                  no.total_devuelto,
                  no.tcambio,
                  no.id_liquidacion,
                  no.nit,
                  no.estado,
                  no.credfis,
                  no.nro_liquidacion,
                  no.monto_total,
                  no.estado_reg,
                  no.nro_nota,
                  no.razon,
                  no.id_usuario_ai,
                  no.usuario_ai,
                  no.fecha_reg,
                  no.id_usuario_reg,
                  no.fecha_mod,
                  no.id_usuario_mod,
                  usu1.cuenta as usr_reg,
                  usu2.cuenta as usr_mod,
                  no.billete,
  								no.nroaut,
  								usu1.cuenta
                from decr.tnota no
                inner join segu.tusuario usu1 on usu1.id_usuario = no.id_usuario_reg
                left join segu.tusuario usu2 on usu2.id_usuario = no.id_usuario_mod
				        where  ';

				      --	RAISE EXCEPTION '%','asd';
        IF p_administrador !=1 THEN

					select pxp.list(distinct '''' ||suc.estacion|| '''')
					into v_sucursales
					from decr.tsucursal_usuario ussuc
					INNER JOIN decr.tsucursal suc on suc.id_sucursal = ussuc.id_sucursal
					where id_usuario = p_id_usuario;

					--v_suc = ' ''CBB'',''LPB'' ';


				--	RAISE EXCEPTION '%',v_sucursales;
          v_consulta:=v_consulta||'no.estacion  in (' || v_sucursales ||')  and ';

        END IF;

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'FAC_NOT_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ada.torrico	
 	#FECHA:		18-11-2014 19:30:03
	***********************************/

	elsif(p_transaccion='FAC_NOT_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_nota)
					    from decr.tnota no
					    inner join segu.tusuario usu1 on usu1.id_usuario = no.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = no.id_usuario_mod
					    where ';

			IF p_administrador !=1 THEN




				select pxp.list(distinct '''' ||suc.estacion|| '''')
				into v_sucursales
				from decr.tsucursal_usuario ussuc
					INNER JOIN decr.tsucursal suc on suc.id_sucursal = ussuc.id_sucursal
				where id_usuario = p_id_usuario;

				--v_suc = ' ''CBB'',''LPB'' ';
				--	RAISE EXCEPTION '%',v_sucursales;
				v_consulta:=v_consulta||'no.estacion  in (' || v_sucursales ||')  and ';
			END IF;

			--Definicion de la respuesta		    
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'NOTA_GEN_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		favio figueroa
 	#FECHA:		11-02-2020 11:30:03
	***********************************/

	elsif(p_transaccion='NOTA_GEN_SEL')then

    	begin

    		--Sentencia de la consulta
			v_consulta:='SELECT
                          nota.id_usuario_reg,
                          nota.id_usuario_mod,
                          nota.fecha_reg,
                          nota.fecha_mod,
                          nota.estado_reg,
                          nota.id_usuario_ai,
                          nota.usuario_ai,
                          nota.id_nota,
                          nota.estacion,
                          nota.id_sucursal,
                          nota.estado,
                          nota.nro_nota,
                          to_char(nota.fecha,''DD-MM-YYYY'')::varchar AS fecha,
                          nota.razon,
                          nota.tcambio,
                          nota.nit,
                          nota.id_liquidacion,
                          nota.nro_liquidacion,
                          nota.id_moneda,
                          nota.monto_total,
                          nota.excento,
                          nota.total_devuelto,
                          nota.credfis,
                          nota.billete,
                          nota.codigo_control,
                           nota.id_dosificacion,
                         nota.nrofac as factura,
                          nota.nroaut as autorizacion,
                          nota.tipo,
                          nota.nroaut_anterior,
                          to_char(nota.fecha_fac,''DD-MM-YYYY'')::varchar AS fecha_fac,

                          nota.nroaut,
                          to_char(nota.fecha_limite,''DD-MM-YYYY'')::varchar AS fecha_limite,
                          usu1.cuenta,
			                    ae.id_actividad_economica,
                        ae.nombre as nombre_actividad,
                           d.glosa_impuestos,
                           d.glosa_empresa as glosa_boa,
                           ''d.glosa_consumidor''::varchar as glosa_consumidor,
                           ''d.nro_resolucion''::varchar as nro_resolucion,
                        to_char(d.fecha_inicio_emi,''DD-MM-YYYY'')::varchar AS feciniemi,
                        to_char(d.fecha_limite,''DD-MM-YYYY'')::varchar AS feclimemi,
                           s.direccion,
                           s.telefono as telefonos,
                           ''s.alcaldia''::varchar as alcaldia,
                           ''d.tipo_autoimpresor''::varchar as tipo_autoimpresor,
                           ''d.autoimpresor''::varchar as autoimpresor,
                           ''s.razon''::varchar as razon_sucursal

                        FROM
                          decr.tnota nota
                          inner join segu.tusuario usu1 on usu1.id_usuario = nota.id_usuario_reg
                          inner join vef.tdosificacion d on d.id_dosificacion = nota.id_dosificacion
                            INNER JOIN vef.tactividad_economica ae on ae.id_actividad_economica =  ANY(d.id_activida_economica)
                            INNER JOIN vef.tsucursal s on s.id_sucursal = d.id_sucursal
				        where nota.id_nota in ('||v_parametros.notas||')  ';




			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'NOTA_GEN_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		favio figueroa
 	#FECHA:		18-11-2020 19:30:03
	***********************************/

	elsif(p_transaccion='NOTA_GEN_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_nota)
					    FROM
                          decr.tnota nota
                          inner join segu.tusuario usu1 on usu1.id_usuario = nota.id_usuario_reg
                          inner join vef.tdosificacion d on d.id_dosificacion = nota.id_dosificacion
                            INNER JOIN vef.tactividad_economica ae on ae.id_actividad_economica =  ANY(d.id_activida_economica)
                            INNER JOIN vef.tsucursal s on s.id_sucursal = d.id_sucursal
					    where nota.id_nota in ('||v_parametros.notas||')';

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