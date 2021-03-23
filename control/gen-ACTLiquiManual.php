<?php
/**
*@package pXP
*@file gen-ACTLiquiManual.php
*@author  (favio.figueroa)
*@date 21-03-2021 22:59:57
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTLiquiManual extends ACTbase{    
			
	function listarLiquiManual(){
		$this->objParam->defecto('ordenacion','id_liqui_manual');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODLiquiManual','listarLiquiManual');
		} else{
			$this->objFunc=$this->create('MODLiquiManual');
			
			$this->res=$this->objFunc->listarLiquiManual($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarLiquiManual(){
		$this->objFunc=$this->create('MODLiquiManual');	
		if($this->objParam->insertar('id_liqui_manual')){
			$this->res=$this->objFunc->insertarLiquiManual($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarLiquiManual($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarLiquiManual(){
			$this->objFunc=$this->create('MODLiquiManual');	
		$this->res=$this->objFunc->eliminarLiquiManual($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>