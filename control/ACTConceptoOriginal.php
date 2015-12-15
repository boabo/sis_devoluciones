<?php
/**
*@package pXP
*@file gen-ACTConceptoOriginal.php
*@author  (admin)
*@date 15-12-2015 19:08:12
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTConceptoOriginal extends ACTbase{    
			
	function listarConceptoOriginal(){
		$this->objParam->defecto('ordenacion','id_concepto_original');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODConceptoOriginal','listarConceptoOriginal');
		} else{
			$this->objFunc=$this->create('MODConceptoOriginal');
			
			$this->res=$this->objFunc->listarConceptoOriginal($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarConceptoOriginal(){
		$this->objFunc=$this->create('MODConceptoOriginal');	
		if($this->objParam->insertar('id_concepto_original')){
			$this->res=$this->objFunc->insertarConceptoOriginal($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarConceptoOriginal($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarConceptoOriginal(){
			$this->objFunc=$this->create('MODConceptoOriginal');	
		$this->res=$this->objFunc->eliminarConceptoOriginal($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>