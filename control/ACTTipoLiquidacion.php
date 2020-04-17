<?php
/**
*@package pXP
*@file gen-ACTTipoLiquidacion.php
*@author  (admin)
*@date 17-04-2020 01:50:31
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				17-04-2020 01:50:31								CREACION

*/

class ACTTipoLiquidacion extends ACTbase{    
			
	function listarTipoLiquidacion(){
		$this->objParam->defecto('ordenacion','id_tipo_liquidacion');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODTipoLiquidacion','listarTipoLiquidacion');
		} else{
			$this->objFunc=$this->create('MODTipoLiquidacion');
			
			$this->res=$this->objFunc->listarTipoLiquidacion($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarTipoLiquidacion(){
		$this->objFunc=$this->create('MODTipoLiquidacion');	
		if($this->objParam->insertar('id_tipo_liquidacion')){
			$this->res=$this->objFunc->insertarTipoLiquidacion($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarTipoLiquidacion($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarTipoLiquidacion(){
			$this->objFunc=$this->create('MODTipoLiquidacion');	
		$this->res=$this->objFunc->eliminarTipoLiquidacion($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>