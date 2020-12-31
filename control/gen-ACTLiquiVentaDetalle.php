<?php
/**
*@package pXP
*@file gen-ACTLiquiVentaDetalle.php
*@author  (admin)
*@date 29-12-2020 19:36:57
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTLiquiVentaDetalle extends ACTbase{    
			
	function listarLiquiVentaDetalle(){
		$this->objParam->defecto('ordenacion','id_liqui_venta_detalle');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODLiquiVentaDetalle','listarLiquiVentaDetalle');
		} else{
			$this->objFunc=$this->create('MODLiquiVentaDetalle');
			
			$this->res=$this->objFunc->listarLiquiVentaDetalle($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarLiquiVentaDetalle(){
		$this->objFunc=$this->create('MODLiquiVentaDetalle');	
		if($this->objParam->insertar('id_liqui_venta_detalle')){
			$this->res=$this->objFunc->insertarLiquiVentaDetalle($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarLiquiVentaDetalle($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarLiquiVentaDetalle(){
			$this->objFunc=$this->create('MODLiquiVentaDetalle');	
		$this->res=$this->objFunc->eliminarLiquiVentaDetalle($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>