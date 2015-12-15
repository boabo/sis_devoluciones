<?php
/**
*@package pXP
*@file gen-MODSucursal.php
*@author  (ada.torrico)
*@date 18-11-2014 20:00:02
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODSucursal extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);


		$this->cone = new conexion();
		$this->informix = $this->cone->conectarPDOInformix(); // conexion a informix
		$this->link = $this->cone->conectarpdo(); //conexion a pxp(postgres)
	}
			
	function listarSucursal(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='decr.ft_sucursal_sel';
		$this->transaccion='VEN_SUCU_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_sucursal','int4');
		$this->captura('alcaldia','varchar');
		$this->captura('estacion','varchar');
		$this->captura('telefono','varchar');
		$this->captura('estado_reg','varchar');
		$this->captura('direccion','varchar');
		$this->captura('razon','varchar');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('id_usuario_ai','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('id_usuario_mod','int4');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('sucursal','int4');
		$this->captura('sucursal_descriptivo','text');
		//$this->captura('desc_person','text');

//asd




		
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarSucursal(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_sucursal_ime';
		$this->transaccion='VEN_SUCU_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('alcaldia','alcaldia','varchar');
		$this->setParametro('estacion','estacion','varchar');
		$this->setParametro('telefono','telefono','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('direccion','direccion','varchar');
		$this->setParametro('razon','razon','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarSucursal(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_sucursal_ime';
		$this->transaccion='VEN_SUCU_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_sucursal','id_sucursal','int4');
		$this->setParametro('alcaldia','alcaldia','varchar');
		$this->setParametro('estacion','estacion','varchar');
		$this->setParametro('telefono','telefono','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('direccion','direccion','varchar');
		$this->setParametro('razon','razon','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarSucursal(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='decr.ft_sucursal_ime';
		$this->transaccion='VEN_SUCU_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_sucursal','id_sucursal','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function obtenerSucursalesInformix(){

		$this->informix->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
		$res = $this->informix->prepare("select * from sucursal ");
		$res->execute();
		$result = $res->fetchAll(PDO::FETCH_ASSOC);




		$arra_json = json_encode($result);

		$this->aParam->addParametro('arra_json', $arra_json);
		$this->arreglo['arra_json'] = $arra_json;


		$this->procedimiento='decr.ft_sucursal_ime';
		$this->transaccion='VEN_SUCU_INFX';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('arra_json','arra_json','text');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;

	}


			
}
?>