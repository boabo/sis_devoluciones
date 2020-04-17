<?php
/**
*@package pXP
*@file gen-MODTipoDocLiquidacion.php
*@author  (admin)
*@date 17-04-2020 01:52:57
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				17-04-2020 01:52:57								CREACION

*/

class MODTipoDocLiquidacion extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarTipoDocLiquidacion(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='decr.ft_tipo_doc_liquidacion_sel';
		$this->transaccion='DECR_TDOCLIQ_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_tipo_doc_liquidacion','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('tipo_documento','varchar');
		$this->captura('obs_dba','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
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
			
	function insertarTipoDocLiquidacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_tipo_doc_liquidacion_ime';
		$this->transaccion='DECR_TDOCLIQ_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('tipo_documento','tipo_documento','varchar');
		$this->setParametro('obs_dba','obs_dba','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarTipoDocLiquidacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_tipo_doc_liquidacion_ime';
		$this->transaccion='DECR_TDOCLIQ_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_tipo_doc_liquidacion','id_tipo_doc_liquidacion','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('tipo_documento','tipo_documento','varchar');
		$this->setParametro('obs_dba','obs_dba','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarTipoDocLiquidacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_tipo_doc_liquidacion_ime';
		$this->transaccion='DECR_TDOCLIQ_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_tipo_doc_liquidacion','id_tipo_doc_liquidacion','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>