<?php
/**
*@package pXP
*@file gen-MODDescuentoLiquidacion.php
*@author  (admin)
*@date 17-04-2020 01:55:03
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				17-04-2020 01:55:03								CREACION

*/

class MODDescuentoLiquidacion extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarDescuentoLiquidacion(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='decr.ft_descuento_liquidacion_sel';
		$this->transaccion='DECR_DESLIQUI_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_descuento_liquidacion','int4');
		$this->captura('contabilizar','varchar');
		$this->captura('obs_dba','varchar');
		$this->captura('importe','numeric');
		$this->captura('estado_reg','varchar');
		$this->captura('id_concepto_ingas','int4');
		$this->captura('id_liquidacion','int4');
		$this->captura('sobre','varchar');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('id_usuario_ai','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('id_usuario_mod','int4');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarDescuentoLiquidacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_descuento_liquidacion_ime';
		$this->transaccion='DECR_DESLIQUI_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('contabilizar','contabilizar','varchar');
		$this->setParametro('obs_dba','obs_dba','varchar');
		$this->setParametro('importe','importe','numeric');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_concepto_ingas','id_concepto_ingas','int4');
		$this->setParametro('id_liquidacion','id_liquidacion','int4');
		$this->setParametro('sobre','sobre','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarDescuentoLiquidacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_descuento_liquidacion_ime';
		$this->transaccion='DECR_DESLIQUI_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_descuento_liquidacion','id_descuento_liquidacion','int4');
		$this->setParametro('contabilizar','contabilizar','varchar');
		$this->setParametro('obs_dba','obs_dba','varchar');
		$this->setParametro('importe','importe','numeric');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_concepto_ingas','id_concepto_ingas','int4');
		$this->setParametro('id_liquidacion','id_liquidacion','int4');
		$this->setParametro('sobre','sobre','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarDescuentoLiquidacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_descuento_liquidacion_ime';
		$this->transaccion='DECR_DESLIQUI_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_descuento_liquidacion','id_descuento_liquidacion','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>