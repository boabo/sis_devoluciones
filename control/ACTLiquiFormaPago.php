<?php
/**
*@package pXP
*@file gen-ACTLiquiFormaPago.php
*@author  (admin)
*@date 06-01-2021 03:55:40
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTLiquiFormaPago extends ACTbase{    
			
	function listarLiquiFormaPago(){
		$this->objParam->defecto('ordenacion','id_liqui_forma_pago');

		$this->objParam->defecto('dir_ordenacion','asc');

        if($this->objParam->getParametro('id_liquidacion') != '') {
            $this->objParam->addFiltro("tlp.id_liquidacion = ".$this->objParam->getParametro('id_liquidacion'));
        }
        
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODLiquiFormaPago','listarLiquiFormaPago');
		} else{
			$this->objFunc=$this->create('MODLiquiFormaPago');
			
			$this->res=$this->objFunc->listarLiquiFormaPago($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarLiquiFormaPago(){
		$this->objFunc=$this->create('MODLiquiFormaPago');
		if($this->objParam->insertar('id_liqui_forma_pago')){
			$this->res=$this->objFunc->insertarLiquiFormaPago($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarLiquiFormaPago($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarLiquiFormaPago(){
			$this->objFunc=$this->create('MODLiquiFormaPago');	
		$this->res=$this->objFunc->eliminarLiquiFormaPago($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>