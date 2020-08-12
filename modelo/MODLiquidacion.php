<?php
/**
*@package pXP
*@file gen-MODLiquidacion.php
*@author  (admin)
*@date 17-04-2020 01:54:37
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				17-04-2020 01:54:37								CREACION

*/

class MODLiquidacion extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarLiquidacion(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='decr.ft_liquidacion_sel';
		$this->transaccion='DECR_LIQUI_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_liquidacion','int4');
		$this->captura('estacion','varchar');
		$this->captura('nro_liquidacion','varchar');
		$this->captura('estado_reg','varchar');
		$this->captura('tipo_de_cambio','numeric');
		$this->captura('descripcion','varchar');
		$this->captura('nombre_cheque','varchar');
		$this->captura('fecha_liqui','date');
		$this->captura('tramo_devolucion','varchar');
		$this->captura('util','varchar');
		$this->captura('fecha_pago','date');
		$this->captura('id_tipo_doc_liquidacion','int4');
		$this->captura('pv_agt','varchar');
		$this->captura('noiata','varchar');
		$this->captura('id_tipo_liquidacion','int4');
		$this->captura('id_forma_pago','int4');
		$this->captura('id_boleto','int4');
		$this->captura('tramo','varchar');
		$this->captura('nombre','varchar');
		$this->captura('moneda_liq','varchar');
		$this->captura('estado','varchar');
		$this->captura('cheque','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('desc_tipo_documento','varchar');
		$this->captura('desc_tipo_liquidacion','varchar');
		$this->captura('desc_nro_boleto','varchar');
		$this->captura('nro_nit','varchar');
		$this->captura('razon','varchar');
		$this->captura('fecha_fac','date');
		$this->captura('total','numeric');
        $this->captura('nro_aut','int4');
        $this->captura('nro_fac','varchar');
        $this->captura('concepto','varchar');
        $this->captura('tipo','varchar');
        $this->captura('precio_unitario','numeric');
        $this->captura('importe_original','numeric');

        $this->captura('punto_venta','varchar');
        $this->captura('moneda_emision','varchar');
        $this->captura('importe_neto','numeric');
        $this->captura('tasas','numeric');
        $this->captura('importe_total','numeric');
        $this->captura('sum_descuentos','numeric');
        $this->captura('importe_devolver','numeric');



		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarLiquidacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_liquidacion_ime';
		$this->transaccion='DECR_LIQUI_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estacion','estacion','varchar');
		$this->setParametro('nro_liquidacion','nro_liquidacion','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('tipo_de_cambio','tipo_de_cambio','numeric');
		$this->setParametro('descripcion','descripcion','varchar');
		$this->setParametro('nombre_cheque','nombre_cheque','varchar');
		$this->setParametro('fecha_liqui','fecha_liqui','date');
		$this->setParametro('tramo_devolucion','tramo_devolucion','varchar');
		$this->setParametro('util','util','varchar');
		$this->setParametro('fecha_pago','fecha_pago','date');
		$this->setParametro('id_tipo_doc_liquidacion','id_tipo_doc_liquidacion','int4');
		$this->setParametro('pv_agt','pv_agt','varchar');
		$this->setParametro('noiata','noiata','varchar');
		$this->setParametro('id_tipo_liquidacion','id_tipo_liquidacion','int4');
		$this->setParametro('id_forma_pago','id_forma_pago','int4');
		$this->setParametro('tramo','tramo','varchar');
		$this->setParametro('nombre','nombre','varchar');
		$this->setParametro('moneda_liq','moneda_liq','varchar');
		$this->setParametro('estado','estado','varchar');
		$this->setParametro('cheque','cheque','varchar');
		$this->setParametro('id_boleto','id_boleto','int4');
		$this->setParametro('punto_venta','punto_venta','varchar');
		$this->setParametro('moneda_emision','moneda_emision','varchar');
		$this->setParametro('importe_neto','importe_neto','numeric');
		$this->setParametro('tasas','tasas','numeric');
		$this->setParametro('importe_total','importe_total','numeric');
		$this->setParametro('id_concepto_ingas','id_concepto_ingas','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarLiquidacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_liquidacion_ime';
		$this->transaccion='DECR_LIQUI_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_liquidacion','id_liquidacion','int4');
		$this->setParametro('estacion','estacion','varchar');
		$this->setParametro('nro_liquidacion','nro_liquidacion','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('tipo_de_cambio','tipo_de_cambio','numeric');
		$this->setParametro('descripcion','descripcion','varchar');
		$this->setParametro('nombre_cheque','nombre_cheque','varchar');
		$this->setParametro('fecha_liqui','fecha_liqui','date');
		$this->setParametro('tramo_devolucion','tramo_devolucion','varchar');
		$this->setParametro('util','util','varchar');
		$this->setParametro('fecha_pago','fecha_pago','date');
		$this->setParametro('id_tipo_doc_liquidacion','id_tipo_doc_liquidacion','int4');
		$this->setParametro('pv_agt','pv_agt','varchar');
		$this->setParametro('noiata','noiata','varchar');
		$this->setParametro('id_tipo_liquidacion','id_tipo_liquidacion','int4');
		$this->setParametro('id_forma_pago','id_forma_pago','int4');
		$this->setParametro('tramo','tramo','varchar');
		$this->setParametro('nombre','nombre','varchar');
		$this->setParametro('moneda_liq','moneda_liq','varchar');
		$this->setParametro('estado','estado','varchar');
		$this->setParametro('cheque','cheque','varchar');
        $this->setParametro('id_boleto','id_boleto','int4');


        $this->setParametro('punto_venta','punto_venta','varchar');
        $this->setParametro('moneda_emision','moneda_emision','varchar');
        $this->setParametro('importe_neto','importe_neto','numeric');
        $this->setParametro('tasas','tasas','numeric');
        $this->setParametro('importe_total','importe_total','numeric');



        //Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarLiquidacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_liquidacion_ime';
		$this->transaccion='DECR_LIQUI_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_liquidacion','id_liquidacion','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarBoleto(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='decr.ft_liquidacion_sel';
        $this->transaccion='DECR_BOL_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_boleto','int4');
        $this->captura('nro_boleto','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
	}
	function obtenerTramos(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='decr.ft_liquidacion_sel';
        $this->transaccion='DECR_TRAMO_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->count = false;

        //Definicion de la lista del resultado del query
        $this->setParametro('billete','billete','varchar');

        $this->captura('billete','numeric');
        $this->captura('list_tramo','text');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
	}
	function verLiquidacion(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='decr.ft_liquidacion_ime';
        $this->transaccion='DECR_LIQUI_VERJSON';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_liquidacion','id_liquidacion','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
	}
	function obtenerLiquidacionCorrelativo(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='decr.ft_liquidacion_sel';
        $this->transaccion='DECR_NUMLIQ_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->count = false;

        //Define los parametros para la funcion
        $this->setParametro('estacion','estacion','varchar');
        $this->captura('f_obtener_correlativo','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
	}
			
}
?>