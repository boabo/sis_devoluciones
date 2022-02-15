<?php
/**
*@package pXP
*@file gen-MODNotaSiat.php
*@author  (favio.figueroa)
*@date 15-02-2022 18:29:01
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODNotaSiat extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarNotaSiat(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='decr.ft_nota_siat_sel';
		$this->transaccion='DECR_TNS_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_nota_siat','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('id_liquidacion','int4');
		$this->captura('nro_nota','varchar');
		$this->captura('nro_aut','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('desc_liquidacion','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarNotaSiat(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_nota_siat_ime';
		$this->transaccion='DECR_TNS_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_liquidacion','id_liquidacion','int4');
		$this->setParametro('nro_nota','nro_nota','varchar');
		$this->setParametro('nro_aut','nro_aut','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarNotaSiat(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_nota_siat_ime';
		$this->transaccion='DECR_TNS_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_nota_siat','id_nota_siat','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_liquidacion','id_liquidacion','int4');
		$this->setParametro('nro_nota','nro_nota','varchar');
		$this->setParametro('nro_aut','nro_aut','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarNotaSiat(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_nota_siat_ime';
		$this->transaccion='DECR_TNS_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_nota_siat','id_nota_siat','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>