CREATE OR REPLACE FUNCTION ven.f_usuario_sucursal (
  p_id_usuario integer
)
RETURNS varchar AS
$body$
/**************************************************************************
 FUNCION: 		ven.f_usuario_sucursal
 DESCRIPCION:   devuelve las sucursales habilitadas para este usuario en las liquidaciones
 AUTOR: 	    favio figueroa (ffp)	
 FECHA:	        24/09/2015
 COMENTARIOS:	
***************************************************************************
 HISTORIA DE MODIFICACIONES:

 DESCRIPCION:	
 AUTOR:		
 FECHA:		
 ***************************************************************************/
 
 
 DECLARE
    v_nombre_funcion   	text;
    v_resp              varchar;
    v_respuesta         boolean;
    
    v_registros 		varchar;
     v_filadd			varchar;
 BEGIN
    v_nombre_funcion:='ven.f_usuario_sucursal';
    v_respuesta=false;
    v_filadd := 'li.estacion  in (';
    
    select pxp.list(''''||su.estacion||'''')
    into v_registros
    from ven.tsucursal_usuario suus
    inner join ven.tsucursal su on su.id_sucursal = suus.id_sucursal
    where id_usuario = p_id_usuario;
    
    v_filadd := v_filadd || v_registros ;
    v_filadd := v_filadd || ') and';       
 
    return v_filadd; 
    
    
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