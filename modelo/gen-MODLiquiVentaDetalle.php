<?php
/**
*@package pXP
*@file gen-MODLiquiVentaDetalle.php
*@author  (admin)
*@date 29-12-2020 19:36:57
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODLiquiVentaDetalle extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarLiquiVentaDetalle(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='decr.ft_liqui_venta_detalle_sel';
		$this->transaccion='DECR_LVD_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_liqui_venta_detalle','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('id_liquidacion','int4');
		$this->captura('id_venta_detalle','int4');
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
			
	function insertarLiquiVentaDetalle(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_liqui_venta_detalle_ime';
		$this->transaccion='DECR_LVD_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_liquidacion','id_liquidacion','int4');
		$this->setParametro('id_venta_detalle','id_venta_detalle','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarLiquiVentaDetalle(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_liqui_venta_detalle_ime';
		$this->transaccion='DECR_LVD_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_liqui_venta_detalle','id_liqui_venta_detalle','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_liquidacion','id_liquidacion','int4');
		$this->setParametro('id_venta_detalle','id_venta_detalle','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarLiquiVentaDetalle(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_liqui_venta_detalle_ime';
		$this->transaccion='DECR_LVD_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_liqui_venta_detalle','id_liqui_venta_detalle','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>