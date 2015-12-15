<?php
/**
*@package pXP
*@file gen-MODConceptoOriginal.php
*@author  (admin)
*@date 15-12-2015 19:08:12
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODConceptoOriginal extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarConceptoOriginal(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='decr.ft_concepto_original_sel';
		$this->transaccion='DECR_CONO_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_concepto_original','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('tipo','varchar');
		$this->captura('concepto','varchar');
		$this->captura('importe_original','numeric');
		$this->captura('id_nota','int4');
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
			
	function insertarConceptoOriginal(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_concepto_original_ime';
		$this->transaccion='DECR_CONO_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('tipo','tipo','varchar');
		$this->setParametro('concepto','concepto','varchar');
		$this->setParametro('importe_original','importe_original','numeric');
		$this->setParametro('id_nota','id_nota','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarConceptoOriginal(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_concepto_original_ime';
		$this->transaccion='DECR_CONO_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_concepto_original','id_concepto_original','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('tipo','tipo','varchar');
		$this->setParametro('concepto','concepto','varchar');
		$this->setParametro('importe_original','importe_original','numeric');
		$this->setParametro('id_nota','id_nota','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarConceptoOriginal(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_concepto_original_ime';
		$this->transaccion='DECR_CONO_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_concepto_original','id_concepto_original','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}



			
}
?>