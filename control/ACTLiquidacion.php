<?php
/**
*@package pXP
*@file gen-ACTLiquidacion.php
*@author  (admin)
*@date 17-04-2020 01:54:37
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				17-04-2020 01:54:37								CREACION

*/

class ACTLiquidacion extends ACTbase{    
			
	function listarLiquidacion(){
		$this->objParam->defecto('ordenacion','id_liquidacion');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODLiquidacion','listarLiquidacion');
		} else{
			$this->objFunc=$this->create('MODLiquidacion');
			
			$this->res=$this->objFunc->listarLiquidacion($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarLiquidacion(){
		$this->objFunc=$this->create('MODLiquidacion');	
		if($this->objParam->insertar('id_liquidacion')){
			$this->res=$this->objFunc->insertarLiquidacion($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarLiquidacion($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarLiquidacion(){
			$this->objFunc=$this->create('MODLiquidacion');	
		$this->res=$this->objFunc->eliminarLiquidacion($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>