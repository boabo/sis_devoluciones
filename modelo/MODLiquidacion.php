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
        $this->captura('id_punto_venta','int4');

        $this->captura('desc_punto_venta','varchar');
        $this->captura('nro_nota','varchar');
        $this->captura('id_estado_wf','int4');
        $this->captura('id_proceso_wf','int4');
        $this->captura('num_tramite','varchar');
        $this->captura('nro_factura','int4');
        $this->captura('nombre_factura','varchar');
        $this->captura('id','int4');
        $this->captura('cantidad','int4');
        $this->captura('id_venta','int4');
        $this->captura('desc_forma_pago','varchar');
        $this->captura('id_venta_detalle','varchar');
        $this->captura('exento','numeric');
        $this->captura('id_medio_pago','int4');
        $this->captura('id_moneda','int4');

        $this->setParametro('tipo_tab_liqui','tipo_tab_liqui','varchar');



		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}

    function listarLiquidacionJson(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='decr.ft_liquidacion_json';
        $this->transaccion='DECR_LIQUI_JSON_SEL';
        $this->tipo_procedimiento='IME';

        $this->setParametro('tipo_tab_liqui','tipo_tab_liqui','varchar');
        $this->setParametro('id_liquidacion','id_liquidacion','int4');
        $this->setParametro('nro_liquidacion','nro_liquidacion','varchar');
        $this->setParametro('bottom_filtro_value','bottom_filtro_value','varchar');
        $this->setParametro('query','query','varchar');


        $this->setParametro('administradora','administradora','varchar');
        $this->setParametro('fecha_ini','fecha_ini','date');
        $this->setParametro('fecha_fin','fecha_fin','date');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }



    function listarLiquidacionDetalle(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='decr.ft_liquidacion_sel';
        $this->transaccion='DECR_LIQUIDET_SEL';
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
        $this->captura('nro_aut','varchar');
        $this->captura('nro_fac','integer');
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
        $this->captura('id_punto_venta','int4');

        $this->captura('desc_punto_venta','varchar');
        $this->captura('nro_nota','varchar');
        $this->captura('id_estado_wf','int4');
        $this->captura('id_proceso_wf','int4');
        $this->captura('num_tramite','varchar');
        $this->captura('nro_factura','int4');
        $this->captura('nombre_factura','varchar');
        $this->captura('id','int4');

        $this->captura('cantidad','int4');


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
		$this->setParametro('id_punto_venta','id_punto_venta','int4');
		$this->setParametro('id_venta','id_venta','int4');
		$this->setParametro('id_venta_detalle','id_venta_detalle','varchar');
		$this->setParametro('exento','exento','numeric');
		$this->setParametro('importe_tramo_utilizado','importe_tramo_utilizado','numeric');
        $this->setParametro('json','json_new_records','text');
        $this->setParametro('json_data_boletos_recursivo','json_data_boletos_recursivo','text');
        $this->setParametro('json_data_liqui_manual_det','json_data_liqui_manual_det','text');
        $this->setParametro('id_medio_pago','id_medio_pago','int4');
        $this->setParametro('id_moneda','id_moneda','int4');
        $this->setParametro('payment','payment','text'); // this is a json
        $this->setParametro('id_deposito','id_deposito','int4');
        $this->setParametro('importe_total_deposito','importe_total_deposito','numeric');
        $this->setParametro('id_descuento_liquidacion','id_descuento_liquidacion','varchar');
        $this->setParametro('id_liquidacion_fk','id_liquidacion_fk','int4');
        $this->setParametro('id_factucom','id_factucom','int4');
        $this->setParametro('id_factucomcon','id_factucomcon','varchar');
        $this->setParametro('tipo_manual','tipo_manual','varchar');


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
        $this->setParametro('id_concepto_ingas','id_concepto_ingas','varchar');
        $this->setParametro('id_punto_venta','id_punto_venta','int4');
        $this->setParametro('id_venta','id_venta','int4');
        $this->setParametro('exento','exento','numeric');
        $this->setParametro('importe_tramo_utilizado','importe_tramo_utilizado','numeric');

        $this->setParametro('id_venta_detalle','id_venta_detalle','varchar');

        $this->setParametro('id_medio_pago','id_medio_pago','int4');
        $this->setParametro('id_moneda','id_moneda','int4');

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

        		$this->setParametro('nro_boleto','nro_boleto','varchar');

        //Definicion de la lista del resultado del query
        $this->captura('id_boleto','int4');
        $this->captura('nro_boleto','varchar');
        $this->captura('fecha_emision','date');


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

    function siguienteEstadoLiquidacion(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='decr.ft_liquidacion_ime';
        $this->transaccion='DECR_LIQUI_SIGWF';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_proceso_wf_act','id_proceso_wf_act','int4');
        $this->setParametro('id_estado_wf_act','id_estado_wf_act','int4');
        $this->setParametro('id_funcionario_usu','id_funcionario_usu','int4');
        $this->setParametro('id_tipo_estado','id_tipo_estado','int4');
        $this->setParametro('id_funcionario_wf','id_funcionario_wf','int4');
        $this->setParametro('id_depto_wf','id_depto_wf','int4');
        $this->setParametro('obs','obs','text');
        $this->setParametro('json_procesos','json_procesos','text');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function obtenerJsonPagar(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='decr.ft_liquidacion_ime';
        $this->transaccion='DECR_LIQUI_JSONPAGAR';
        $this->tipo_procedimiento='IME';

        $this->setParametro('id_liquidacion','id_liquidacion','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }


    function listarVentaDetalleOriginal(){
        $this->procedimiento='decr.ft_liquidacion_sel';
        $this->transaccion='DECR_FACORI_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Define los parametros para la funcion

        $this->captura('concepto','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();

        //echo($this->consulta);exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }


    function obtenerCambioOficiales(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='decr.ft_liquidacion_ime';
        $this->transaccion='DECR_LIQUI_MON';
        $this->tipo_procedimiento='IME';

        $this->setParametro('fecha_emision','fecha_emision','date');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }


    function listarDeposito(){
        $this->procedimiento='decr.ft_liquidacion_sel';
        $this->transaccion='DECR_DEPOS_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Define los parametros para la funcion

        $this->captura('id_deposito','int4');
        $this->captura('nro_deposito','varchar');
        $this->captura('monto_deposito','numeric');
        $this->captura('fecha','date');
        $this->captura('saldo','numeric');
        $this->captura('monto_total','numeric');


        //Ejecuta la instruccion
        $this->armarConsulta();

        //echo($this->consulta);exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarFactuCom(){
        $this->procedimiento='decr.ft_liquidacion_sel';
        $this->transaccion='DECR_FACTUCOM_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->count = false;

        //Define los parametros para la funcion

        $this->captura('id_factucom','int4');
        $this->captura('nroaut','numeric');
        $this->captura('nrofac','numeric');
        $this->captura('monto','numeric');
        $this->captura('razon_cliente','varchar');
        $this->captura('fecha','date');


        $this->setParametro('nro_aut','nro_aut','numeric');
        $this->setParametro('nro_fac','nro_fac','numeric');


        //Ejecuta la instruccion
        $this->armarConsulta();

        //echo($this->consulta);exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarFactucomcon(){
        $this->procedimiento='decr.ft_liquidacion_sel';
        $this->transaccion='DECR_FACTUCOMCON_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->count = false;

        //Define los parametros para la funcion

        $this->captura('id_factucomcon','int4');
        $this->captura('id_factucom','int4');
        $this->captura('cantidad','numeric');
        $this->captura('preciounit','numeric');
        $this->captura('importe','numeric');
        $this->captura('concepto','varchar');


        $this->setParametro('id_factucom','id_factucom','integer');


        //Ejecuta la instruccion
        $this->armarConsulta();

        //echo($this->consulta);exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarDeposito(){
        $this->procedimiento='decr.ft_liquidacion_sel';
        $this->transaccion='DECR_DEPOS_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Define los parametros para la funcion

        $this->captura('id_deposito','int4');
        $this->captura('nro_deposito','varchar');
        $this->captura('monto_deposito','numeric');
        $this->captura('fecha','date');
        $this->captura('saldo','numeric');
        $this->captura('monto_total','numeric');


        //Ejecuta la instruccion
        $this->armarConsulta();

        //echo($this->consulta);exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }



}
?>