<?php
/**
*@package pXP
*@file gen-ACTNota.php
*@author  (ada.torrico)
*@date 18-11-2014 19:30:03
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/
require_once(dirname(__FILE__).'/numLetra.php');
require_once(dirname(__FILE__).'/../../lib/tcpdf/tcpdf_barcodes_2d.php');
class ACTNota extends ACTbase{

	function listarNota(){
		$this->objParam->defecto('ordenacion','id_nota');

		$this->objParam->defecto('dir_ordenacion','asc');

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODNota','listarNota');
		} else{
			$this->objFunc=$this->create('MODNota');
			
			$this->res=$this->objFunc->listarNota($this->objParam);

		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarNota(){
		$this->objFunc=$this->create('MODNota');	
		if($this->objParam->insertar('id_nota')){
			$this->res=$this->objFunc->insertarNota($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarNota($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarNota(){
			$this->objFunc=$this->create('MODNota');	
		$this->res=$this->objFunc->eliminarNota($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function saveForm(){
		
		
		$this->objFunc=$this->create('MODNota');  
		
		
		$this->res=$this->objFunc->saveForm($this->objParam);
		
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
	
	
	function generarNota(){



		if($this->objParam->getParametro('reimpresion') != ''){
			$this->reImpresion();
		}


		if($this->objParam->getParametro('vista_previa') != ''){

		}

		

		$this->objParam->defecto('ordenacion','nro_nota');

		$this->objParam->defecto('dir_ordenacion','asc');
		
		
		$this->objFunc=$this->create('MODNota');
		
		$this->res=$this->objFunc->generarNota($this->objParam);
		
		
		


		/*if($this->res->getTipo()!='EXITO'){
			
			$this->res->imprimirRespuesta($this->res->generarJson());
			exit;
		}*/

		$notas = $this->res->getDatos();
		
		
		
		
		
		
		
		$html = "";
		$i = 0;
		
		$V = new EnLetras();



		
		
		foreach ($notas as $item) {
			
			
			/* Cadena para qr |nit emisor|razon social emisor|Número de Factura |Número de Autorización|
			|Fecha de emisión|Importe de la compra |Código de Contro|Fecha Límite de Emisión| NIT comprador | razon comprador| */
			//$cadena_qr = "|154422029|BOLIVIANA DE AVIACION|123|123|28/12/2014|67.10|73-65-52-A5-FE|29/12/2014"; 
			$cadena_qr = '|154422029|BOLIVIANA DE AVIACION|123|123|02/10/2014|'.$item['total_devuelto'].'|'.$item['codigo_control'].'| '.$item['nit'].' | '.trim($item['razon']).'|';
								

			$barcodeobj = new TCPDF2DBarcode($cadena_qr, 'QRCODE,H');
			
			
			//obtenemos conceptos originales de esta factura o boleto
			
			
			
			
			if($item['tipo'] == 'BOLETO'){ //esta autorizacion es de la nota ya sea de factura o boleto
				
				
				$original = $this->listarBoletosOriginales($item['factura']);
				
			}else if($item['tipo'] == 'FACTURA'){
				
				$original = $this->listarFacturaOriginales($item['factura'],$item['nroaut_anterior']);
				

			}else if($item['tipo'] == 'FACTURA MANUAL'){

				$original = $this->listarFacturaManualOriginal($item['id_nota']);

			}
			
			
			
			$dosificacion = $this->listarDosificacion($item['id_dosificacion']);
			//var_dump($dosificacion[0]['FECLIMEMI']);
			//var_dump($dosificacion[0]['GLOSA_IMPUESTOS']);

			

			$this->objParam->defecto('dir_ordenacion','asc');
			$this->objParam->parametros_consulta['ordenacion'] = 'id_nota_detalle';

			$this->objParam->parametros_consulta['filtro'] = ' 0 = 0 ';
			$this->objParam->addFiltro("deno.id_nota = ". $item['id_nota']);

			//listamos detalle de la nota
			$this->objFunc2=$this->create('MODNotaDetalle');	
			$this->res2=$this->objFunc2->listarNotaDetalle($this->objParam);
			
			if($this->res2->getTipo()!='EXITO'){
			
				$this->res2->imprimirRespuesta($this->res2->generarJson());
				exit;
			}
			
			$detalles = $this->res2->getDatos();
			
			
			
			
			setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
			



			if($dosificacion[0]['NRO_RESOLUCION'] =='RND 10-0016-07'){
				//antigua resolucion

				$desc = 'SUCURSAL '.trim($dosificacion[0]['SUCURSAL']).' -  '.trim($dosificacion[0]['TIPO_AUTOIMPRESOR']).' '.trim($dosificacion[0]['AUTOIMPRESOR']).'<br />';

				$desc2 = 'ORIGINAL';
				$glosa_consumidor = '';

			}else if($dosificacion[0]['NRO_RESOLUCION'] =='RND 10-0025-14' || $dosificacion[0]['NRO_RESOLUCION'] =='RND 10-0021-16' ){

				
				//nueva resolucion
				$desc = 'SUCURSAL '.trim($dosificacion[0]['SUCURSAL']).'<br />';
				$desc2 = '';
				$glosa_consumidor = '" '.$dosificacion[0]['GLOSA_CONSUMIDOR'].'"';


			}


			$html.='<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
					   "http://www.w3.org/TR/html4/strict.dtd">
					<html>
					<head>
						<meta http-equiv="Content-Type" content="text/html;">


					  <link rel="stylesheet" href="../../../sis_devoluciones/control/print.css" type="text/css" media="print" charset="utf-8">
					  
					</head>
					
					
					<body  style="line-height: 18px; font-size: 14pt;">';



			if($this->objParam->getParametro('vista_previa') == '' || $this->objParam->getParametro('vista_previa') == null) {


			}else{
				$html .= '<center>';
			}
					$html.='<table style="width: 395px;">
					<thead  >
						<tr   >
						<td colspan="2" style=" text-align: center;" align="center" >


						BOLIVIANA DE AVIACION BOA<br />
						'.$desc.'
						'.trim($dosificacion[0]['RAZON']).'<br />
						'.trim($dosificacion[0]['DIRECCION']).'<br />


						TELF : '.trim($dosificacion[0]['TELEFONOS']).'<br />
						'.trim($dosificacion[0]['ALCALDIA']).'<br />
<hr/>
						</td>
						</tr>


						<tr><td colspan="2" align="center" style="text-align: center;">NOTA DE CREDITO-DEBITO <br/> '.$desc2.'<hr/></td></tr>

						<tr>
						<td style="width: 200px;" colspan="1"  align="right">NIT:&nbsp;&nbsp;&nbsp;&nbsp;</td><td colspan="1" align="left">154422029</td>
						</tr>
						<tr>
						<td  colspan="1" align="right">N&#176; NOTA FISCAL:&nbsp;&nbsp;&nbsp;</td><td colspan="1" align="left">'.$item['nro_nota'].'</td>
						</tr>
						<tr>
						<td  colspan="1" align="right"> N&#176; AUTORIZACION:</td><td colspan="1" align="left">'.$item['autorizacion'].'</td>
						</tr>

						<tr>
						<td colspan="2"  align="center" style="text-align: center;">
					   <hr/>
					    '.$dosificacion[0]['NOMBRE_ACTIVIDAD'].'
						</td>
						</tr>';


			if($item['estado'] == 9) {
				$html .= '<tr><td colspan="2" align="center" style="text-align: center; font-size: 30pt;">
 N/C ANULADA<hr/>
</td></tr>';
			}

			$html.='<tr><td colspan="2">
 						 Fecha: '.strftime("%d/%m/%Y", strtotime($item['fecha'])).'<br/>
					    NIT/CI: '.$item['nit'].'<br/>
					     Senor(es): '.trim($item['razon']).'<hr/>
					</td></tr>';


			$html.='<tr><td colspan="2"  width="390px;" align="center" style="text-align: center;">DATOS DE LA TRANSACCION ORIGINAL<hr/></td></tr>';

			$html.='<tr><td colspan="2">
 						FACTURA: '.$item['factura'].' <br/>
					    AUTORIZACION : '.$item['nroaut_anterior'].'<br>
					    FECHA DE EMISION: '.$item['fecha_fac'].'
					</td></tr>';

					$html.='</thead>
					</table>';






					$html.='
					<table  style="width: 385px;">

					<thead>

						<tr><th>Cant<hr/></th><th style="width:60px;">Concepto<hr/></th><th align="center">PU<hr/></th><th align="right">SubTotal<hr/></th></tr>

					</thead>
					<tbody>';
					$total_original = 0;

					foreach ($original as $item_detalle) {

						$precio_unitario = ($item_detalle['precio_unitario']!=null)?$item_detalle['precio_unitario']:$item_detalle['importe_original'];
						$cantidad = ($item_detalle['cantidad']!=null)?$item_detalle['cantidad']:1;

					     $html .= '<tr>
							<td style="width: 11px;">'.$cantidad.'</td>
							<td style="width:60px;">'.str_replace( "/", " / ", $item_detalle['concepto'] ).'</td>
							<td align="center">'.number_format($precio_unitario, 2, '.', ',').'</td>
							<td align="right">'.number_format($item_detalle['importe_original'], 2, '.', ',').'</td>
							</tr>';
					    $total_original = $total_original + $item_detalle['importe_original'];

					}

			$html.='<tr><td colspan="4"><hr/></td></tr>';
					$html.='</tbody>
					    <tfoot>
					    <tr><td colspan="2" align="left">Total Bs. <hr/></td><td colspan="2" align="right"> ' .number_format($total_original, 2, '.', ',').'<hr/></td></tr>
					    </tfoot>
					</table>

					<p style="text-align: center; width: 385px;">
					    DETALLE DE LA DEVOLUCION O RESCISION DEL SERVICIO
					</p>
					<hr  />
					<br />
					<br />
					<table style="width: 385px;">
					    <thead>

					    <tr><th>Cant<hr/></th><th style="width:60px;">Concepto<hr/></th><th align="center"> PU<hr/></th><th align="right">SubTotal<hr/></th></tr>
					    </thead>
					    <tbody>';

					    $exento_total = 0;
					    $importe_total = 0;
					    foreach ($detalles as $item_detalle) {

					    $exento_total = $exento_total + $item_detalle['exento'];
					    $importe_total = $importe_total + $item_detalle['importe'];

					    $html .= '<tr>
							<td style="width: 11px;">'.$item_detalle['cantidad'].'</td>
							<td style="width:60px;">'.str_replace( "/", " / ", $item_detalle['concepto'] ).'</td>
							<td align="center">'.number_format($item_detalle['precio_unitario'], 2, '.', ',').'</td>
							<td align="right" >'.number_format($item_detalle['importe'], 2, '.', ',').'</td>
							</tr>';
					    }
					    $total_devolver = $importe_total - $exento_total;

			$html.='<tr><td colspan="4"><hr/></td></tr>

							<tr ><td colspan="3" align="left">Total Bs. <hr/></td><td  align="right" colspan="1">'.number_format($importe_total, 2, '.', ',').'<hr/></td></tr>

							<tr><td colspan="3" align="left">MENOS: Importes Exentos :<hr/></td><td colspan="1" align="right"> '.number_format($exento_total, 2, '.', ',').'<hr/></td></tr>
 							<tr><td colspan="3" align="left">Importe Total Devuelto: <hr/></td><td colspan="1" align="right">'.number_format($total_devolver, 2, '.', ',').'<hr/></td></tr>
							</head>
						</tbody></table><br/>';



			$html.='<table style="width: 300px;">
					<tbody>
					<tr><td style="text-align: left;">Son: '.$V->ValorEnLetras(number_format($total_devolver, 2, '.', ''),"").'<br/>
					    Monto efectivo del Crédito o Débito <br/>
					    (13% del Importe total Devuelto)  
					  
					</td></tr>
					</tbody>
					</table>

					<table style="width:380px;">
					<tbody>
					<tr>
					<td style="text-align: right;">
					'.number_format($item['credfis'], 2, '.', ',').'
					</td>
					</tr>
					</tbody>
					</table>


					<hr  />
					<br />
					<br />
					<table style="width: 350px;"><tbody><tr><td align="left" style="text-align:left;">
					 Codigo de Control: '.$item['codigo_control'].' <br/>
					    Fecha Limite de Emision: '.$dosificacion[0]['FECLIMEMI'].' <br/>
					    OBS: '.$item['nro_liquidacion'].' 
					</td></tr>



					<tr>
					<td style="text-align:center;">
					<!--<div align="center">
								    '.$barcodeobj->getBarcodeHTML(3, 3, 'black').'
								    </div>-->
					</td>
					</tr>



					</tbody>
					</table>

					<div style="width:270px; text-align: center;">
					   " '.$dosificacion[0]['GLOSA_IMPUESTOS'].'"
					</div>
					<div style="width:270px; text-align: center;">
					'.$glosa_consumidor.'
					</div>

<p>Usuario: '.$item['cuenta'].' Id:'.$item['id_nota'].'  Hora: '.strftime("%H:%M", strtotime($item['fecha_reg'])).' </p>

					<hr />
					<br />
					<br />


					<p>¡ '.$dosificacion[0]['GLOSA_BOA'].' !
					    <br/> www.boa.bo</p>';

			if($this->objParam->getParametro('vista_previa') == '' || $this->objParam->getParametro('vista_previa') == null){

				$html.= '
				<script type="text/javascript">
window.onload=function(){self.print();}
</script> 
				';
			}else{
				$html.='</center>';
			}


					
				$html.='</body>
					</html>';



		
			$temp[] = $html;
			$i++;
		}
		
		$this->res->setDatos($temp);
		$this->res->imprimirRespuesta($this->res->generarJson());		
		
	}

	function listarBoletosOriginales($billete){
		
		
	
		$this->objFunc=$this->create('MODLiquidevolu');	
		$ori =$this->objFunc->listarBoletosConTramos($billete);
		
		return $ori;
	}
	
	function listarFacturaOriginales($factura,$autorizacion){
		
		
		
		$this->objParam->addParametro('nrofac',$factura);
		$this->objParam->addParametro('nroaut',$autorizacion);
		
		
		$this->objFunc2=$this->create('MODLiquidevolu');	
		$ori =$this->objFunc2->listarFacturaConceptosOriginales($this->objParam);
		return $ori;
	}

	function listarFacturaManualOriginal($id_nota){

		$this->objParam->defecto('dir_ordenacion','asc');
		$this->objParam->parametros_consulta['filtro'] = ' 0 = 0 ';
		$this->objParam->parametros_consulta['ordenacion'] = 'id_concepto_original';


		$this->objParam->addFiltro("cono.id_nota = ".$id_nota);




		$this->objFunc2=$this->create('MODConceptoOriginal');
		$this->res = $this->objFunc2->listarConceptoOriginal($this->objParam);
		$datos = $this->res->getDatos();
		return $datos;



	}

	function listarDosificacion($id_dosificacion){
			
		$this->objFunc=$this->create('MODNota');	
		$dosi =$this->objFunc->listarDosificacion($id_dosificacion);
		
		return $dosi;
	}


	function reImpresion(){

		$this->objFunc2=$this->create('MODNota');

		$re =$this->objFunc2->reImpresion($this->objParam);
		return $re;

	}



	function anularNota(){

		$this->objFunc=$this->create('MODNota');

		$re =$this->objFunc->anularNota($this->objParam);
		//$this->generarNota();

	}

	function crearReporteNotas(){
		
		
		
	}
			
}

?>