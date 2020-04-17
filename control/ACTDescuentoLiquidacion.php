<?php
/**
*@package pXP
*@file gen-ACTDescuentoLiquidacion.php
*@author  (admin)
*@date 17-04-2020 01:55:03
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				17-04-2020 01:55:03								CREACION

*/

class ACTDescuentoLiquidacion extends ACTbase{    
			
	function listarDescuentoLiquidacion(){
		$this->objParam->defecto('ordenacion','id_descuento_liquidacion');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODDescuentoLiquidacion','listarDescuentoLiquidacion');
		} else{
			$this->objFunc=$this->create('MODDescuentoLiquidacion');
			
			$this->res=$this->objFunc->listarDescuentoLiquidacion($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarDescuentoLiquidacion(){
		$this->objFunc=$this->create('MODDescuentoLiquidacion');	
		if($this->objParam->insertar('id_descuento_liquidacion')){
			$this->res=$this->objFunc->insertarDescuentoLiquidacion($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarDescuentoLiquidacion($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarDescuentoLiquidacion(){
			$this->objFunc=$this->create('MODDescuentoLiquidacion');	
		$this->res=$this->objFunc->eliminarDescuentoLiquidacion($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>