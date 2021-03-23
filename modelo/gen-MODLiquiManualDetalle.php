<?php
/**
*@package pXP
*@file gen-MODLiquiManualDetalle.php
*@author  (favio.figueroa)
*@date 22-03-2021 20:14:28
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODLiquiManualDetalle extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarLiquiManualDetalle(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='decr.ft_liqui_manual_detalle_sel';
		$this->transaccion='DECR_TLMD_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_liqui_manual_detalle','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('id_liqui_manual','int4');
		$this->captura('administradora','varchar');
		$this->captura('lote','varchar');
		$this->captura('comprobante','varchar');
		$this->captura('fecha','varchar');
		$this->captura('nro_tarjeta','varchar');
		$this->captura('concepto_original','varchar');
		$this->captura('concepto_devolver','varchar');
		$this->captura('importe_original','numeric');
		$this->captura('importe_devolver','numeric');
		$this->captura('descripcion','varchar');
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
			
	function insertarLiquiManualDetalle(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_liqui_manual_detalle_ime';
		$this->transaccion='DECR_TLMD_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_liqui_manual','id_liqui_manual','int4');
		$this->setParametro('administradora','administradora','varchar');
		$this->setParametro('lote','lote','varchar');
		$this->setParametro('comprobante','comprobante','varchar');
		$this->setParametro('fecha','fecha','varchar');
		$this->setParametro('nro_tarjeta','nro_tarjeta','varchar');
		$this->setParametro('concepto_original','concepto_original','varchar');
		$this->setParametro('concepto_devolver','concepto_devolver','varchar');
		$this->setParametro('importe_original','importe_original','numeric');
		$this->setParametro('importe_devolver','importe_devolver','numeric');
		$this->setParametro('descripcion','descripcion','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarLiquiManualDetalle(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_liqui_manual_detalle_ime';
		$this->transaccion='DECR_TLMD_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_liqui_manual_detalle','id_liqui_manual_detalle','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_liqui_manual','id_liqui_manual','int4');
		$this->setParametro('administradora','administradora','varchar');
		$this->setParametro('lote','lote','varchar');
		$this->setParametro('comprobante','comprobante','varchar');
		$this->setParametro('fecha','fecha','varchar');
		$this->setParametro('nro_tarjeta','nro_tarjeta','varchar');
		$this->setParametro('concepto_original','concepto_original','varchar');
		$this->setParametro('concepto_devolver','concepto_devolver','varchar');
		$this->setParametro('importe_original','importe_original','numeric');
		$this->setParametro('importe_devolver','importe_devolver','numeric');
		$this->setParametro('descripcion','descripcion','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarLiquiManualDetalle(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_liqui_manual_detalle_ime';
		$this->transaccion='DECR_TLMD_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_liqui_manual_detalle','id_liqui_manual_detalle','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>