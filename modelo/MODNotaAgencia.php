<?php
/**
*@package pXP
*@file gen-MODNotaAgencia.php
*@author  (admin)
*@date 26-04-2020 21:14:13
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODNotaAgencia extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarNotaAgencia(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='decr.ft_nota_agencia_sel';
		$this->transaccion='DECR_NOTAGE_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_nota_agencia','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('id_doc_compra_venta','int4');
		$this->captura('id_depto_conta','int4');
		$this->captura('id_moneda','int4');
		$this->captura('estado','varchar');
		$this->captura('nit','varchar');
		$this->captura('nro_nota','varchar');
		$this->captura('nro_aut_nota','int8');
		$this->captura('fecha','date');
		$this->captura('razon','varchar');
		$this->captura('tcambio','numeric');
		$this->captura('monto_total','numeric');
		$this->captura('excento','numeric');
		$this->captura('total_devuelto','numeric');
		$this->captura('credfis','numeric');
		$this->captura('billete','varchar');
		$this->captura('codigo_control','varchar');
		$this->captura('nrofac','int8');
		$this->captura('nroaut','int8');
		$this->captura('fecha_fac','date');
		$this->captura('codito_control_fac','varchar');
		$this->captura('monto_total_fac','numeric');
		$this->captura('iva','varchar');
		$this->captura('neto','numeric');
		$this->captura('obs','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('desc_moneda','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarNotaAgencia(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_nota_agencia_ime';
		$this->transaccion='DECR_NOTAGE_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_doc_compra_venta','id_doc_compra_venta','int4');
		$this->setParametro('id_depto_conta','id_depto_conta','int4');
		$this->setParametro('id_moneda','id_moneda','int4');
		$this->setParametro('estado','estado','varchar');
		$this->setParametro('nit','nit','varchar');
		$this->setParametro('nro_nota','nro_nota','varchar');
		$this->setParametro('nro_aut_nota','nro_aut_nota','int8');
		$this->setParametro('fecha','fecha','date');
		$this->setParametro('razon','razon','varchar');
		$this->setParametro('tcambio','tcambio','numeric');
		$this->setParametro('monto_total','monto_total','numeric');
		$this->setParametro('excento','excento','numeric');
		$this->setParametro('total_devuelto','total_devuelto','numeric');
		$this->setParametro('credfis','credfis','numeric');
		$this->setParametro('billete','billete','varchar');
		$this->setParametro('codigo_control','codigo_control','varchar');
		$this->setParametro('nrofac','nrofac','int8');
		$this->setParametro('nroaut','nroaut','int8');
		$this->setParametro('fecha_fac','fecha_fac','date');
		$this->setParametro('codito_control_fac','codito_control_fac','varchar');
		$this->setParametro('monto_total_fac','monto_total_fac','numeric');
		$this->setParametro('iva','iva','varchar');
		$this->setParametro('neto','neto','numeric');
		$this->setParametro('obs','obs','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarNotaAgencia(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_nota_agencia_ime';
		$this->transaccion='DECR_NOTAGE_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_nota_agencia','id_nota_agencia','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_doc_compra_venta','id_doc_compra_venta','int4');
		$this->setParametro('id_depto_conta','id_depto_conta','int4');
		$this->setParametro('id_moneda','id_moneda','int4');
		$this->setParametro('estado','estado','varchar');
		$this->setParametro('nit','nit','varchar');
		$this->setParametro('nro_nota','nro_nota','varchar');
		$this->setParametro('nro_aut_nota','nro_aut_nota','int8');
		$this->setParametro('fecha','fecha','date');
		$this->setParametro('razon','razon','varchar');
		$this->setParametro('tcambio','tcambio','numeric');
		$this->setParametro('monto_total','monto_total','numeric');
		$this->setParametro('excento','excento','numeric');
		$this->setParametro('total_devuelto','total_devuelto','numeric');
		$this->setParametro('credfis','credfis','numeric');
		$this->setParametro('billete','billete','varchar');
		$this->setParametro('codigo_control','codigo_control','varchar');
		$this->setParametro('nrofac','nrofac','int8');
		$this->setParametro('nroaut','nroaut','int8');
		$this->setParametro('fecha_fac','fecha_fac','date');
		$this->setParametro('codito_control_fac','codito_control_fac','varchar');
		$this->setParametro('monto_total_fac','monto_total_fac','numeric');
		$this->setParametro('iva','iva','varchar');
		$this->setParametro('neto','neto','numeric');
		$this->setParametro('obs','obs','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarNotaAgencia(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_nota_agencia_ime';
		$this->transaccion='DECR_NOTAGE_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_nota_agencia','id_nota_agencia','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>