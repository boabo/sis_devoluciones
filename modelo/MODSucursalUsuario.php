<?php
/**
*@package pXP
*@file gen-MODSucursalUsuario.php
*@author  (admin)
*@date 23-09-2015 19:15:16
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODSucursalUsuario extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarSucursalUsuario(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='decr.ft_sucursal_usuario_sel';
		$this->transaccion='VEN_SUCUS_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_sucursal_usuario','int4');
		$this->captura('tipo','varchar');
		$this->captura('id_usuario','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('id_sucursal','int4');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('desc_usuario','text');
		$this->captura('desc_sucursal','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarSucursalUsuario(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_sucursal_usuario_ime';
		$this->transaccion='VEN_SUCUS_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('tipo','tipo','varchar');
		$this->setParametro('id_usuario','id_usuario','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_sucursal','id_sucursal','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarSucursalUsuario(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_sucursal_usuario_ime';
		$this->transaccion='VEN_SUCUS_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_sucursal_usuario','id_sucursal_usuario','int4');
		$this->setParametro('tipo','tipo','varchar');
		$this->setParametro('id_usuario','id_usuario','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_sucursal','id_sucursal','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarSucursalUsuario(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_sucursal_usuario_ime';
		$this->transaccion='VEN_SUCUS_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_sucursal_usuario','id_sucursal_usuario','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>