<?php
/**
*@package pXP
*@file gen-MODLiquiManual.php
*@author  (favio.figueroa)
*@date 21-03-2021 22:59:57
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODLiquiManual extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarLiquiManual(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='decr.ft_liqui_manual_sel';
		$this->transaccion='DECR_LIQUIMA_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_liqui_manual','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('id_liquidacion','int4');
		$this->captura('tipo_manual','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
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
			
	function insertarLiquiManual(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_liqui_manual_ime';
		$this->transaccion='DECR_LIQUIMA_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_liquidacion','id_liquidacion','int4');
		$this->setParametro('tipo_manual','tipo_manual','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarLiquiManual(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_liqui_manual_ime';
		$this->transaccion='DECR_LIQUIMA_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_liqui_manual','id_liqui_manual','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_liquidacion','id_liquidacion','int4');
		$this->setParametro('tipo_manual','tipo_manual','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarLiquiManual(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_liqui_manual_ime';
		$this->transaccion='DECR_LIQUIMA_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_liqui_manual','id_liqui_manual','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>