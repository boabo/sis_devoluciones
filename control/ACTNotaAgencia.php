<?php
/**
*@package pXP
*@file gen-ACTNotaAgencia.php
*@author  (admin)
*@date 26-04-2020 21:14:13
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTNotaAgencia extends ACTbase{    

	function listarNotaAgencia(){
		$this->objParam->defecto('ordenacion','id_nota_agencia');

		$this->objParam->defecto('dir_ordenacion','asc');

        if($this->objParam->getParametro('id_liquidacion') != ''){
            $this->objParam->addFiltro("notage.id_liquidacion = ".$this->objParam->getParametro('id_liquidacion'));
        }
        
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODNotaAgencia','listarNotaAgencia');
		} else{
			$this->objFunc=$this->create('MODNotaAgencia');
			
			$this->res=$this->objFunc->listarNotaAgencia($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarNotaAgencia(){
		$this->objFunc=$this->create('MODNotaAgencia');	
		if($this->objParam->insertar('id_nota_agencia')){
			$this->res=$this->objFunc->insertarNotaAgencia($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarNotaAgencia($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarNotaAgencia(){
			$this->objFunc=$this->create('MODNotaAgencia');	
		$this->res=$this->objFunc->eliminarNotaAgencia($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	function listarDocumentoJson(){
			$this->objFunc=$this->create('MODNotaAgencia');
		$this->res=$this->objFunc->listarDocumentoJson($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>