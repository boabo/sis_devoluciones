<?php
/*
**************************************************************************
 ISSUE  SIS       EMPRESA       FECHA       AUTOR                   DESCRIPCION
 #    DEVO       BOA           01/08/2019  FAVIO FIGUEROA        CREATE
***************************************************************************
*/
class RLiquidacionesPagadasXls
{
    private $objParam;
    public  $url_archivo;
    private $docexcel;
    private $dataSetMaster;
    private $dataSet;

    function __construct(CTParametro $objParam){
        $this->objParam = $objParam;
        $this->url_archivo = "../../../reportes_generados/".$this->objParam->getParametro('nombre_archivo');
        set_time_limit(400);
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize'  => '10MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $this->docexcel = new PHPExcel();
        $this->docexcel->getProperties()->setCreator("PXP")
            ->setLastModifiedBy("PXP")
            ->setTitle($this->objParam->getParametro('titulo_archivo'))
            ->setSubject($this->objParam->getParametro('titulo_archivo'))
            ->setDescription('Reporte "'.$this->objParam->getParametro('titulo_archivo').'", generado por el framework PXP')
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report File");

        $this->docexcel->setActiveSheetIndex(0);
        $this->docexcel->getActiveSheet()->setTitle($this->objParam->getParametro('titulo_archivo'));
        $this->initializeColumnWidth($this->docexcel->getActiveSheet());
        $this->printerConfiguration();
    }

    function setMaster($data) {
        $this->dataSetMaster = $data;

    }

    function setData($data) {
        $this->dataSet = $data;
    }

    function generarReporte() {
        $sheet=$this->docexcel->setActiveSheetIndex(0);
        $this->imprimeTitulo($sheet);
        $this->mainBox($sheet);
        //$this->detalleResumen($sheet);
        //$this->detalle($sheet);
        $this->firmas($sheet);
        //Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }

    function imprimeTitulo($sheet) {
        $sheet->setCellValueByColumnAndRow(0,1,$this->objParam->getParametro('titulo_rep'));

        //Título Principal
        $titulo1 = "REPORTE DE LIQUIDACIONES ";
        $this->cell($sheet,$titulo1,'A1',0,1,"center",true,16,'Arial'); //#55
        $sheet->mergeCells('A1:W1');

        //Título 1
        $titulo1 = "(DINAMICA)";
        $this->cell($sheet,$titulo1,'A2',0,2,"center",true,$this->tam_letra_titulo,'Arial'); //#55
        $sheet->mergeCells('A2:W2');

        //Título 2
        /*$fecha_hasta = date("d/m/Y",strtotime($this->objParam->getParametro('fecha_hasta')));
        $titulo2 = "Depto.: ";
        $this->cell($sheet,$titulo2.$this->paramDepto,'A3',0,3,"center",true,$this->tam_letra_subtitulo,'Arial'); //#55
        $sheet->mergeCells('A3:W3');*/

        //Título 3
        $titulo3="";
        $this->cell($sheet,$titulo3,'A4',0,4,"center",true,$this->tam_letra_subtitulo,'Arial'); //#55
        $sheet->mergeCells('A4:W4');

        //Logo
        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('Logo');
        $objDrawing->setDescription('Logo');
        $objDrawing->setPath(dirname(__FILE__).'/../../lib/imagenes/logos/logo.jpg');
        $objDrawing->setHeight(50);
        $objDrawing->setWorksheet($this->docexcel->setActiveSheetIndex(0));

        $this->cell($sheet,'Fecha Ini: ','A5',0,5,"right",true,$this->tam_letra_titulo,'Arial'); //#55
        $this->cell($sheet, date("d-m-Y", strtotime($this->objParam->getParametro('fecha_ini'))),'C5',2,5,"left",false,$this->tam_letra_titulo,'Arial'); //#55
        $sheet->mergeCells('A5:B5');
        $sheet->mergeCells('C5:H5');
        $this->cell($sheet,'Fecha Fin: ','A6',0,6,"right",true,$this->tam_letra_titulo,'Arial'); //#55
        $this->cell($sheet,date("d-m-Y", strtotime($this->objParam->getParametro('fecha_fin'))),'C6',2,6,"left",false,$this->tam_letra_titulo,'Arial'); //#55
        $sheet->mergeCells('A6:B6');
        $sheet->mergeCells('C6:H6');

        $this->fila = 11;
    }


    function cell($sheet,$texto,$cell,$x,$y,$align="left",$bold=true,$size=10,$name=Arial,$wrap=false,$border=false,$valign='center',$number=false){
        $sheet->getStyle($cell)->getFont()->applyFromArray(array('bold'=>$bold,'size'=>$size,'name'=>$name));
        //Alineación horizontal
        if($align=='left'){
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        } else if($align=='right'){
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        } else if($align=='center'){
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        //Alineación vertical
        if($valign=='center'){
            $sheet->getStyle($cell)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        } else if($valign=='top'){
            $sheet->getStyle($cell)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        } else if($valign=='bottom'){
            $sheet->getStyle($cell)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
        }
        //Rendereo del texto
        $sheet->setCellValueByColumnAndRow($x,$y,$texto);

        //Wrap texto
        if($wrap==true){
            $sheet->getStyle($cell)->getAlignment()->setWrapText(true);
        }

        //Border
        if($border==true){
            $styleArray = array(
                'borders' => array(
                    'outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN//PHPExcel_Style_Border::BORDER_THICK
                    ),
                ),
            );

            $sheet->getStyle($cell)->applyFromArray($styleArray);
        }

        if($number==true){
            $sheet->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
        }
    }

    function mainBox($sheet){
        //Cabecera caja
        $f = $this->fila;
        $this->cell($sheet,'LIQUI DOC.'						,"A$f" ,0, $f,"center",true,$this->tam_letra_detalle,'Arial',true,true); //#55
        $this->cell($sheet,'Cod AGT/OF.'						,"B$f" ,1, $f,"center",true,$this->tam_letra_detalle,'Arial',true,true); //#55
        $this->cell($sheet,'FECHA DE PAGO'						,"C$f" ,2, $f,"center",true,$this->tam_letra_detalle,'Arial',true,true); //#55
        $this->cell($sheet,'BOLETO O DOCUMENTO BOA'					,"D$f" ,3, $f,"center",true,$this->tam_letra_detalle,'Arial',true,true); //#55
        $this->cell($sheet,'NOMBRE PAX'				,"E$f" ,4, $f,"center",true,$this->tam_letra_detalle,'Arial',true,true); //#55
        $this->cell($sheet,'RUTA.'				,"F$f" ,5, $f,"center",true,$this->tam_letra_detalle,'Arial',true,true); //#55
        $this->cell($sheet,'NOMBRE DE CHEQUE'					,"G$f" ,6, $f,"center",true,$this->tam_letra_detalle,'Arial',true,true); //#55
        $this->cell($sheet,'IMPORTE LIQ'			,"H$f" ,7, $f,"center",true,$this->tam_letra_detalle,'Arial',true,true); //#55
        $this->cell($sheet,'NRO CHEQUE'			,"I$f" ,8, $f,"center",true,$this->tam_letra_detalle,'Arial',true,true); //#55
        $this->cell($sheet,'COD IATA'					,"J$f" ,9, $f,"center",true,$this->tam_letra_detalle,'Arial',true,true); //#55
        $this->cell($sheet,'OFICINA EMISORA TKT'						,"K$f" ,10, $f,"center",true,$this->tam_letra_detalle,'Arial',true,true); //#55


        $this->fila++;
        //////////////////
        //Detalle de datos
        //////////////////


        //Renderiza los datos
        $sheet->fromArray(
            $this->dataSet,  // The data to set
            NULL,        // Array values with this value will not be set
            'A12'         // Top left coordinate of the worksheet range where
        //    we want to set these values (default is A1)
        );

        //Definición del rango total de filas
        $range=count($this->dataSet)+11;

        //Coloreado de las columnas que se utilizan para la generación del comprobante contable
        $sheet->getStyle('W11:W'.$range)->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFF66')
                )
            )
        ); //Inc.x Actualiz.

        $sheet->getStyle('R11:R'.$range)->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '#ffff99')
                )
            )
        ); //Valor Actualiz.

        $sheet->getStyle('X11:X'.$range)->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '#ffff99')
                )
            )
        );//Inc. Dep.Acum.Actualiz.

        $sheet->getStyle('AB11:AB'.$range)->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '#ffff99')
                )
            )
        );//Depreciación Mensual

        $sheet->getStyle('AC11:AC'.$range)->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFF66')
                )
            )
        );//Depreciación Mensual

     /*   //TOTALES
        //Totales
        $f=count($this->dataSet)+11;
        $this->cell($sheet,'TOTALES',"A$f",0,$f,"center",true,$this->tam_letra_detalle,'Arial',false,false); //#55
        $this->cellBorder($sheet,"A$f:K$f");
        $sheet->mergeCells("A$f:K$f");*/



        //Estilos
        $count = count($this->dataSet) + 11;
        $sheet->getStyle("L11:R$count")
            ->getNumberFormat()
            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle("U11:AC$count")
            ->getNumberFormat()
            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        //Actualización variables
        $this->fila=$f+6;
    }

    function cellBorder($sheet,$range,$type='normal'){
        if($type=="normal"){
            $styleArray = array(
                'borders' => array(
                    'outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN //PHPExcel_Style_Border::BORDER_THICK,
                    ),
                ),
            );
        } else if($type=='vertical'){
            $styleArray = array(
                'borders' => array(
                    'vertical' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN //PHPExcel_Style_Border::BORDER_THICK,
                    ),
                ),
            );
        }

        $sheet->getStyle($range)->applyFromArray($styleArray);
    }

    function printerConfiguration(){
        $this->docexcel->setActiveSheetIndex(0)->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $this->docexcel->setActiveSheetIndex(0)->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);
        $this->docexcel->setActiveSheetIndex(0)->getPageSetup()->setFitToWidth(1);
        $this->docexcel->setActiveSheetIndex(0)->getPageSetup()->setFitToHeight(0);

    }

    function firmas($sheet){
        /*$f=$this->fila;
        $this->cell($sheet,'',"C$f",2,$f,"left",true,$this->tam_letra_cabecera,'Arial',false,false); //#55
        $f++;
        $this->cell($sheet,'',"C$f",2,$f,"left",true,$this->tam_letra_cabecera,'Arial',false,false);*/ //#55
    }

    function initializeColumnWidth($sheet){
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(30);
        $sheet->getColumnDimension('L')->setWidth(15);
        $sheet->getColumnDimension('M')->setWidth(15);
        $sheet->getColumnDimension('N')->setWidth(15);
        $sheet->getColumnDimension('O')->setWidth(15);//N:10
        $sheet->getColumnDimension('P')->setWidth(15);//O:10
        $sheet->getColumnDimension('Q')->setWidth(15);//P:15
        $sheet->getColumnDimension('R')->setWidth(15);//Q:15
        $sheet->getColumnDimension('S')->setWidth(15);
        $sheet->getColumnDimension('T')->setWidth(15);
        $sheet->getColumnDimension('U')->setWidth(15);
        $sheet->getColumnDimension('V')->setWidth(15);
        $sheet->getColumnDimension('W')->setWidth(15);
        $sheet->getColumnDimension('X')->setWidth(15);
        $sheet->getColumnDimension('Y')->setWidth(15);
        $sheet->getColumnDimension('Z')->setWidth(15);
        $sheet->getColumnDimension('AA')->setWidth(15);
        $sheet->getColumnDimension('AB')->setWidth(15);
        $sheet->getColumnDimension('AC')->setWidth(15);
        $sheet->getColumnDimension('AD')->setWidth(15);
        $sheet->getColumnDimension('AE')->setWidth(15);
        $sheet->getColumnDimension('AF')->setWidth(15);
        $sheet->getColumnDimension('AG')->setWidth(15);
        $sheet->getColumnDimension('AH')->setWidth(60);
        $sheet->getColumnDimension('AI')->setWidth(60);
        $sheet->getColumnDimension('AJ')->setWidth(60);
    }

}
?>