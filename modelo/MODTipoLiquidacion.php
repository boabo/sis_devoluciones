<?php
/**
*@package pXP
*@file gen-MODTipoLiquidacion.php
*@author  (admin)
*@date 17-04-2020 01:50:31
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				17-04-2020 01:50:31								CREACION

*/

class MODTipoLiquidacion extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarTipoLiquidacion(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='decr.ft_tipo_liquidacion_sel';
		$this->transaccion='DECR_TIPOLIQU_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_tipo_liquidacion','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('tipo_liquidacion','varchar');
		$this->captura('obs_dba','varchar');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_reg','int4');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarTipoLiquidacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_tipo_liquidacion_ime';
		$this->transaccion='DECR_TIPOLIQU_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('tipo_liquidacion','tipo_liquidacion','varchar');
		$this->setParametro('obs_dba','obs_dba','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarTipoLiquidacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_tipo_liquidacion_ime';
		$this->transaccion='DECR_TIPOLIQU_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_tipo_liquidacion','id_tipo_liquidacion','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('tipo_liquidacion','tipo_liquidacion','varchar');
		$this->setParametro('obs_dba','obs_dba','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarTipoLiquidacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_tipo_liquidacion_ime';
		$this->transaccion='DECR_TIPOLIQU_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_tipo_liquidacion','id_tipo_liquidacion','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>