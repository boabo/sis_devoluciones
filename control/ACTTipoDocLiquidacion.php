<?php
/**
*@package pXP
*@file gen-ACTTipoDocLiquidacion.php
*@author  (admin)
*@date 17-04-2020 01:52:57
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				17-04-2020 01:52:57								CREACION

*/

class ACTTipoDocLiquidacion extends ACTbase{    
			
	function listarTipoDocLiquidacion(){
		$this->objParam->defecto('ordenacion','id_tipo_doc_liquidacion');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODTipoDocLiquidacion','listarTipoDocLiquidacion');
		} else{
			$this->objFunc=$this->create('MODTipoDocLiquidacion');
			
			$this->res=$this->objFunc->listarTipoDocLiquidacion($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarTipoDocLiquidacion(){
		$this->objFunc=$this->create('MODTipoDocLiquidacion');	
		if($this->objParam->insertar('id_tipo_doc_liquidacion')){
			$this->res=$this->objFunc->insertarTipoDocLiquidacion($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarTipoDocLiquidacion($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarTipoDocLiquidacion(){
			$this->objFunc=$this->create('MODTipoDocLiquidacion');	
		$this->res=$this->objFunc->eliminarTipoDocLiquidacion($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>