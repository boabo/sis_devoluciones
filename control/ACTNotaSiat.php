<?php
/**
*@package pXP
*@file gen-ACTNotaSiat.php
*@author  (favio.figueroa)
*@date 15-02-2022 18:29:02
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTNotaSiat extends ACTbase{    
			
	function listarNotaSiat(){
		$this->objParam->defecto('ordenacion','id_nota_siat');

		$this->objParam->defecto('dir_ordenacion','asc');

        if($this->objParam->getParametro('id_liquidacion')!=''){
            $this->objParam->addFiltro("tns.id_liquidacion = ''".$this->objParam->getParametro('id_liquidacion')."''");
        }
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODNotaSiat','listarNotaSiat');
		} else{
			$this->objFunc=$this->create('MODNotaSiat');
			
			$this->res=$this->objFunc->listarNotaSiat($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarNotaSiat(){
		$this->objFunc=$this->create('MODNotaSiat');	
		if($this->objParam->insertar('id_nota_siat')){
			$this->res=$this->objFunc->insertarNotaSiat($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarNotaSiat($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarNotaSiat(){
			$this->objFunc=$this->create('MODNotaSiat');	
		$this->res=$this->objFunc->eliminarNotaSiat($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>