<?php
/**
*@package pXP
*@file gen-ACTLiquiDecuentoDetalle.php
*@author  (favio.figueroa)
*@date 21-03-2021 23:00:28
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTLiquiDecuentoDetalle extends ACTbase{    
			
	function listarLiquiDecuentoDetalle(){
		$this->objParam->defecto('ordenacion','id_liqui_descuento_detalle');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODLiquiDecuentoDetalle','listarLiquiDecuentoDetalle');
		} else{
			$this->objFunc=$this->create('MODLiquiDecuentoDetalle');
			
			$this->res=$this->objFunc->listarLiquiDecuentoDetalle($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarLiquiDecuentoDetalle(){
		$this->objFunc=$this->create('MODLiquiDecuentoDetalle');	
		if($this->objParam->insertar('id_liqui_descuento_detalle')){
			$this->res=$this->objFunc->insertarLiquiDecuentoDetalle($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarLiquiDecuentoDetalle($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarLiquiDecuentoDetalle(){
			$this->objFunc=$this->create('MODLiquiDecuentoDetalle');	
		$this->res=$this->objFunc->eliminarLiquiDecuentoDetalle($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>