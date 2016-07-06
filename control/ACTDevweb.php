<?php
/**
*@package pXP
*@file gen-ACTDevweb.php
*@author  (admin)
*@date 04-07-2016 15:19:06
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTDevweb extends ACTbase{    
			
	function listarDevweb(){
		$this->objParam->defecto('ordenacion','id_devweb');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODDevweb','listarDevweb');
		} else{
			$this->objFunc=$this->create('MODDevweb');
			
			$this->res=$this->objFunc->listarDevweb($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarDevweb(){
		$this->objFunc=$this->create('MODDevweb');	
		if($this->objParam->insertar('id_devweb')){
			$this->res=$this->objFunc->insertarDevweb($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarDevweb($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarDevweb(){
			$this->objFunc=$this->create('MODDevweb');	
		$this->res=$this->objFunc->eliminarDevweb($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>