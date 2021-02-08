<?php
/**
*@package pXP
*@file gen-MODLiquiFormaPago.php
*@author  (admin)
*@date 06-01-2021 03:55:40
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODLiquiFormaPago extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarLiquiFormaPago(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='decr.ft_liqui_forma_pago_sel';
		$this->transaccion='DECR_TLP_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_liqui_forma_pago','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('id_liquidacion','int4');
		$this->captura('id_medio_pago','int4');
		$this->captura('pais','varchar');
		$this->captura('ciudad','varchar');
		$this->captura('fac_reporte','varchar');
		$this->captura('cod_est','varchar');
		$this->captura('lote','varchar');
		$this->captura('comprobante','varchar');
		$this->captura('fecha_tarjeta','date');
		$this->captura('nro_tarjeta','varchar');
		$this->captura('importe','numeric');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('desc_medio_pago','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarLiquiFormaPago(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_liqui_forma_pago_ime';
		$this->transaccion='DECR_TLP_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_liquidacion','id_liquidacion','int4');
		$this->setParametro('id_medio_pago','id_medio_pago','int4');
		$this->setParametro('pais','pais','varchar');
		$this->setParametro('ciudad','ciudad','varchar');
		$this->setParametro('fac_reporte','fac_reporte','varchar');
		$this->setParametro('cod_est','cod_est','varchar');
		$this->setParametro('lote','lote','varchar');
		$this->setParametro('comprobante','comprobante','varchar');
		$this->setParametro('fecha_tarjeta','fecha_tarjeta','date');
		$this->setParametro('nro_tarjeta','nro_tarjeta','varchar');
		$this->setParametro('importe','importe','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarLiquiFormaPago(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_liqui_forma_pago_ime';
		$this->transaccion='DECR_TLP_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_liqui_forma_pago','id_liqui_forma_pago','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_liquidacion','id_liquidacion','int4');
		$this->setParametro('id_medio_pago','id_medio_pago','int4');
		$this->setParametro('pais','pais','varchar');
		$this->setParametro('ciudad','ciudad','varchar');
		$this->setParametro('fac_reporte','fac_reporte','varchar');
		$this->setParametro('cod_est','cod_est','varchar');
		$this->setParametro('lote','lote','varchar');
		$this->setParametro('comprobante','comprobante','varchar');
		$this->setParametro('fecha_tarjeta','fecha_tarjeta','date');
		$this->setParametro('nro_tarjeta','nro_tarjeta','varchar');
		$this->setParametro('importe','importe','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarLiquiFormaPago(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_liqui_forma_pago_ime';
		$this->transaccion='DECR_TLP_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_liqui_forma_pago','id_liqui_forma_pago','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>