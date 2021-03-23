<?php
/**
*@package pXP
*@file gen-ACTLiquiManualDetalle.php
*@author  (favio.figueroa)
*@date 22-03-2021 20:14:28
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTLiquiManualDetalle extends ACTbase{    
			
	function listarLiquiManualDetalle(){
		$this->objParam->defecto('ordenacion','id_liqui_manual_detalle');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODLiquiManualDetalle','listarLiquiManualDetalle');
		} else{
			$this->objFunc=$this->create('MODLiquiManualDetalle');
			
			$this->res=$this->objFunc->listarLiquiManualDetalle($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarLiquiManualDetalle(){
		$this->objFunc=$this->create('MODLiquiManualDetalle');	
		if($this->objParam->insertar('id_liqui_manual_detalle')){
			$this->res=$this->objFunc->insertarLiquiManualDetalle($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarLiquiManualDetalle($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarLiquiManualDetalle(){
			$this->objFunc=$this->create('MODLiquiManualDetalle');	
		$this->res=$this->objFunc->eliminarLiquiManualDetalle($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>