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

/*error_reporting(E_ALL);
ini_set('display_errors', 'On');*/

include_once(dirname(__FILE__).'/../../lib/lib_modelo/ConexionSqlServer.php');
require_once(dirname(__FILE__).'/../reporte/RLiquidacionesPagadasXls.php');
class ACTLiquidacion extends ACTbase{

    function listarLiquidacion(){
        $this->objParam->defecto('ordenacion','id_liquidacion');

        $this->objParam->defecto('dir_ordenacion','asc');


        if($this->objParam->getParametro('id_liquidacion') != ''){
            $this->objParam->addFiltro("liqui.id_liquidacion = ".$this->objParam->getParametro('id_liquidacion'));
        }
        if($this->objParam->getParametro('estado') != ''){
            $this->objParam->addFiltro("liqui.estado = ''".$this->objParam->getParametro('estado')."'' " );
        }

        if($this->objParam->getParametro('generar_nota') == 'si'){
            $this->objParam->addFiltro("nota.id_liquidacion is null " );
        }

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODLiquidacion','listarLiquidacion');
        } else{
            $this->objFunc=$this->create('MODLiquidacion');

            $this->res=$this->objFunc->listarLiquidacion($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }


    function listarLiquidacionJson(){
        $this->objFunc=$this->create('MODLiquidacion');
        $this->res=$this->objFunc->listarLiquidacionJson($this->objParam);


        if ($this->res->getTipo() != 'EXITO') {

            $this->res->imprimirRespuesta($this->res->generarJson());
            exit;
        }

        $data = $this->res->getDatos();

        $dataJson = json_decode($data["mensaje"]);
        $send = array(
            "total" => $dataJson->count,
            "datos"=> $dataJson->datos != null ? $dataJson->datos : []
        );
        $send = json_encode($send, true);
        echo $send;


    }

    function insertarLiquidacion(){
        $this->objFunc=$this->create('MODLiquidacion');
        if($this->objParam->insertar('id_liquidacion')){
            $this->res=$this->objFunc->insertarLiquidacion($this->objParam);

            if ($this->res->getTipo() != 'EXITO') {

                $this->res->imprimirRespuesta($this->res->generarJson());
                exit;
            }
            $data = $this->res->getDatos();
            if($data['tipo'] === 'BOLEMD') {
                //si es boleto necesitamos hacer un servicio
                $nroTicket = $this->objParam->getParametro('nro_boleto');

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $_SESSION['_PXP_ND_URL'].'/api/boa-stage-nd/Ticket/refundFactCoupons',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS =>'{
                "ticketNumber": '.$nroTicket.'
            }
            ',
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: ' . $_SESSION['_PXP_ND_TOKEN'],
                        'Content-Type: application/json'
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);

                $data_json = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response), true);

                $data['mensaje_stage_cupones'] = $data_json;
                $this->res->setDatos($data);

            }


            /*if($this->objParam->getParametro('tipo') != '') {
                $this->objParam->addFiltro("tp.id_marca= ".$this->objParam->getParametro('id_marca'));
            }*/


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
    function listarBoleto(){
        $this->objFunc=$this->create('MODLiquidacion');
        $this->res=$this->objFunc->listarBoleto($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    function obtenerTramos(){
        $this->objFunc=$this->create('MODLiquidacion');
        $this->res=$this->objFunc->obtenerTramos($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    function obtenerTramosSql(){

        $billete = $this->objParam->getParametro('billete');
        $param_conex = array();

        $conexion = new ConexionSqlServer('172.17.110.6', 'SPConnection', 'Passw0rd', 'DBStage');
        $conn = $conexion->conectarSQL();

        //EXECUTE [dbo].[spa_getRoutingTicket] @ticketNumber
        if($conn=='connect') {
            $error = 'connect';
            throw new Exception("connect: La conexión a la bd SQL Server ".$param_conex[1]." ha fallado.");
        }else if($conn=='select_db'){
            $error = 'select_db';
            throw new Exception("select_db: La seleccion de la bd SQL Server ".$param_conex[1]." ha fallado.");
        }else {

            $query_string = "exec DBStage.dbo.spa_getRoutingTicket @ticketNumber= $billete "; // boleto miami 9303852215072

            //$query_string = "select * from AuxBSPVersion";
            //$query_string = utf8_decode("select FlightItinerary from FactTicket where TicketNumber = '9302400056027'");

            $query = @mssql_query($query_string, $conn);
            //$query = @mssql_query(utf8_decode('select * from AuxBSPVersion'), $conn);

            $data = array();

            while ($row = mssql_fetch_array($query, MSSQL_ASSOC)){

                $row["id"] = $row["Origin"] .'-'.$row["Destination"];
                $row["desc"] = $row["Origin"] .'-'.$row["Destination"] . '(' .$row["CouponStatus"] . ')';

                $data[] = $row;
            }


            $send = array(
                "total" => count($row),
                "datos"=> $data
            );
            $send = json_encode($send, true);
            echo $send;
            mssql_free_result($query);
            $conexion->closeSQL();
        }
    }

    function verLiquidacion() {
        $this->objFunc=$this->create('MODLiquidacion');
        $this->res=$this->objFunc->verLiquidacion($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    function obtenerLiquidacionCorrelativo() {
        $this->objFunc=$this->create('MODLiquidacion');
        $this->res=$this->objFunc->obtenerLiquidacionCorrelativo($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }


    function getTicketInformationRecursive() {


        //solo por el momento
        $billete = $this->objParam->getParametro('billete');
        $array = array();
        

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $_SESSION['_PXP_ND_URL'].'/api/boa-stage-nd/Ticket/getTicketInformation',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                "ticketNumber": '.$billete.',
                "recursive": true
            }
            ',
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $_SESSION['_PXP_ND_TOKEN'],
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $data_json = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response), true);


        if($data_json != null) {


            /*var_dump($data_json);
        exit;*/
            //var_dump($data_json_string);
            //$data_json = json_decode($data_json_string);
            $data = $data_json[0];


            //todo  cambiar a moneda boliviana cualquier moneda con la que se haya pagado el boleto




            $netAmount = $data["netAmount"];
            $totalAmount = $data["totalAmount"];
            $ticketNumber = $data["ticketNumber"];
            $taxes = $data["taxes"];
            /*var_dump($data["taxes"]);
            exit;*/
            $exento = 0;
            $iva = 0;

            //var_dump($taxes);
            foreach ($taxes as $tax) {
                //var_dump($tax["taxCode"]);
                //var_dump($tax->taxCode);
                //var_dump($tax["taxCode"]);
                //exit;
                if(trim($tax["taxCode"]) !== 'BO' && trim($tax["taxCode"]) !== 'QM' && trim($tax["taxCode"]) !== 'CP') {
                    $exento = $exento + $tax["taxAmount"];
                }

                if(trim($tax["taxCode"]) === 'BO') {
                    $iva = $iva + $tax["taxAmount"]; // solo deberia ser uno pero por si acaso
                }
            }

            array_push($array, array('seleccionado' => 'si',
                'billete' => $ticketNumber,
                'monto' => $totalAmount,
                'itinerary' => $data["itinerary"],
                'passengerName' => $data["passengerName"],
                'currency' => $data["currency"],
                'issueOfficeID' => $data["issueOfficeID"],
                'issueAgencyCode' => $data["issueAgencyCode"], // este es el noiata
                'netAmount' => $netAmount,
                'exento' => $exento,
                'payment' => $data["payment"],
                'taxes' => $data["taxes"],
                'iva' => $iva,
                'iva_contabiliza_no_liquida' => $iva,
                'tiene_nota' => 'no',
                'concepto_para_nota'=> trim($ticketNumber).'/'.trim($data["itinerary"]),
                'foid'=> trim($data["FOID"]),
                'fecha_emision'=> trim($data["issueDate"]),
                'concilliation' => $data["concilliation"],
                'dataStage' => $data

            ));

            $OriginalTicket = $data["OriginalTicket"];
            //var_dump($OriginalTicket);
            while ($OriginalTicket != '') {

                $exento_hijo = 0;
                $iva_hijo = 0;
                foreach ($OriginalTicket["taxes"] as $tax) {
                    if($OriginalTicket["taxCode"] != 'BO' && $tax["taxCode"] != 'QM' && $tax["taxCode"] != 'CP') {
                        $exento_hijo = $exento_hijo + $tax["taxAmount"];
                    }
                    if(trim($tax["taxCode"]) === 'BO') {
                        $iva_hijo = $iva_hijo + $tax["taxAmount"]; // solo deberia ser uno pero por si acaso
                    }
                }
                array_push($array, array('seleccionado' => 'si',
                    'billete' => $OriginalTicket["ticketNumber"],
                    'monto' => $OriginalTicket["totalAmount"],
                    'itinerary' => $OriginalTicket["itinerary"],
                    'passengerName' => $data["passengerName"],
                    'currency' => $data["currency"],
                    'issueOfficeID' => $data["issueOfficeID"],
                    'issueAgencyCode' => $data["issueAgencyCode"],
                    'netAmount' => $data["netAmount"],
                    'exento' => $exento_hijo,
                    'payment' => $OriginalTicket["payment"],
                    'taxes' => $OriginalTicket["taxes"],
                    'iva' => $iva_hijo,
                    'iva_contabiliza_no_liquida' => $iva_hijo,
                    'tiene_nota' => 'no',
                    'concepto_para_nota'=> trim($OriginalTicket["ticketNumber"]).'/'.trim($OriginalTicket["itinerary"]),
                    'foid'=> trim($OriginalTicket["FOID"]),
                    'fecha_emision'=> trim($OriginalTicket["issueDate"]),
                    'concilliation' => $OriginalTicket["concilliation"],
                    'dataStage' => $data

                ));

                $OriginalTicket = $OriginalTicket["OriginalTicket"];
            }



            $send = array(
                "datos" =>  $array,
                "ticket_information" =>  $data,
                "total" => count($array),
            );

            echo json_encode($send);
        } else {
            $send = array(
                "error" => true,
                "mensaje" =>  "error en el servicio de orlando al querer decodificar el json",
            );
            echo json_encode($send);

        }



    }

    function getTicketInformation() {
        $billete = $this->objParam->getParametro('billete');
        $typeReturn = $this->objParam->getParametro('typeReturn');
        if($typeReturn == NULL) {

            /*$conexion = new ConexionSqlServer('172.17.110.6', 'SPConnection', 'Passw0rd', 'DBStage');
            $conn = $conexion->conectarSQL();
            //$query_string = "exec DBStage.dbo.fn_getTicketInformation @ticketNumber= 9303852215072 "; // boleto miami 9303852215072
            $query_string = "Select DBStage.dbo.fn_getTicketInformation('9303852215072') "; // boleto miami 9303852215072

            //$query_string = "select * from AuxBSPVersion";
            //$query_string = utf8_decode("select FlightItinerary from FactTicket where TicketNumber = '9302400056027'");
            @mssql_query('SET CONCAT_NULL_YIELDS_NULL ON');
            @mssql_query('SET ANSI_WARNINGS ON');
            @mssql_query('SET ANSI_PADDING ON');

            $query = @mssql_query($query_string, $conn);
            $row = mssql_fetch_array($query, MSSQL_ASSOC);
            $dataFromSql = json_decode($row['computed']);
            $data = $dataFromSql[0];
            var_dump($dataFromSql);*/

            echo '[{"ticketNumber":"9302401538940  ","pnrCode":"V978TF","transaction":"TKTT","passengerName":"ESPINOZAHERBAS\/DANIEL MARTIN","issueDate":"2020-06-18","issueAgencyCode":"56991314","issueOfficeID":"TJAOB04TR","issueAgent":"9998WS","reserveAgencyCode":"56991314","reserveOfficeID":"TJAOB04TR","ReserveAgent":"9998WS","currency":"BOB","origin":"SRZ","destination":"SRZ","netAmount":284.000000,"totalAmount":369.000000,"itinerary":"VVI - CBB - VVI","fareCalculation":"SRZ OB CBB142.00OB SRZ142.00BOB284.00END","coupon":[{"conjuntionTicketNumber":"9302401538940  ","couponNumber":1,"origin":"VVI","destination":"CBB","flightNumber":"0647 ","fareBasis":"OBOA           ","depatureDate":"28AUG","CouponStatus":"OPEN"},{"conjuntionTicketNumber":"9302401538940  ","couponNumber":2,"origin":"CBB","destination":"VVI","flightNumber":"0650 ","fareBasis":"OBOA           ","depatureDate":"07SEP","CouponStatus":"OPEN"}],"payment":[{"paymentCode":"EXT","paymentDescription":"EXTERNAL PAYMENT FORMS","paymentMethodCode":"BE","paymentMethodDescription":"Banca Electrónica","paymentInstanceCode":"02","paymentInstanceDescription":"Banco Unión","paymentAmount":369.000000,"reference":"EXTBE01\/02000000002050541384\/0620\/KGMEEK"},{"paymentCode":"CA","paymentDescription":"CASH","paymentAmount":0.000000}],"taxes":[{"taxCode":"A7      ","taxAmount":30.000000,"reference":""},{"taxCode":"BO      ","taxAmount":44.000000,"reference":""},{"taxCode":"QM      ","taxAmount":11.000000,"reference":""}]}]';

        } else {
            return '[{"ticketNumber":"9302401538940  ","pnrCode":"V978TF","transaction":"TKTT","passengerName":"ESPINOZAHERBAS\/DANIEL MARTIN","issueDate":"2020-06-18","issueAgencyCode":"56991314","issueOfficeID":"TJAOB04TR","issueAgent":"9998WS","reserveAgencyCode":"56991314","reserveOfficeID":"TJAOB04TR","ReserveAgent":"9998WS","currency":"BOB","origin":"SRZ","destination":"SRZ","netAmount":284.000000,"totalAmount":369.000000,"itinerary":"VVI - CBB - VVI","fareCalculation":"SRZ OB CBB142.00OB SRZ142.00BOB284.00END","coupon":[{"conjuntionTicketNumber":"9302401538940  ","couponNumber":1,"origin":"VVI","destination":"CBB","flightNumber":"0647 ","fareBasis":"OBOA           ","depatureDate":"28AUG","CouponStatus":"OPEN"},{"conjuntionTicketNumber":"9302401538940  ","couponNumber":2,"origin":"CBB","destination":"VVI","flightNumber":"0650 ","fareBasis":"OBOA           ","depatureDate":"07SEP","CouponStatus":"OPEN"}],"payment":[{"paymentCode":"EXT","paymentDescription":"EXTERNAL PAYMENT FORMS","paymentMethodCode":"BE","paymentMethodDescription":"Banca Electrónica","paymentInstanceCode":"02","paymentInstanceDescription":"Banco Unión","paymentAmount":369.000000,"reference":"EXTBE01\/02000000002050541384\/0620\/KGMEEK"},{"paymentCode":"CA","paymentDescription":"CASH","paymentAmount":0.000000}],"taxes":[{"taxCode":"A7      ","taxAmount":30.000000,"reference":""},{"taxCode":"BO      ","taxAmount":44.000000,"reference":""},{"taxCode":"QM      ","taxAmount":11.000000,"reference":""}]}]';

        }

        /*$conexion = new ConexionSqlServer('172.17.110.6', 'SPConnection', 'Passw0rd', 'DBStage');
        $conn = $conexion->conectarSQL();
        $query_string = "exec DBStage.dbo.fn_getTicketInformation @ticketNumber= 9303852215072 "; // boleto miami 9303852215072

        //$query_string = "select * from AuxBSPVersion";
        $query_string = utf8_decode("select FlightItinerary from FactTicket where TicketNumber = '9302400056027'");

        $query = @mssql_query($query_string, $conn);
        $row = mssql_fetch_array($query, MSSQL_ASSOC);*/

    }


    function siguienteEstadoLiquidacion() {
        $this->objFunc=$this->create('MODLiquidacion');
        $this->res=$this->objFunc->siguienteEstadoLiquidacion($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function obtenerJsonPagar() {
        $this->objFunc=$this->create('MODLiquidacion');
        $this->res=$this->objFunc->obtenerJsonPagar($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function listarLiquidacionDetalle() {


        $this->objParam->defecto('ordenacion','id_liquidacion');

        $this->objParam->defecto('dir_ordenacion','asc');




        if($this->objParam->getParametro('tipo') == 'FACCOM'){

            if($this->objParam->getParametro('id_liquidacion') != ''){
                $this->objParam->addFiltro("tl.id_liquidacion = ".$this->objParam->getParametro('id_liquidacion'));
            }
            if($this->objParam->getParametro('estado') != ''){
                $this->objParam->addFiltro("tl.estado = ''".$this->objParam->getParametro('estado')."'' " );
            }

            $this->objFunc=$this->create('MODLiquidacion');
            $this->res=$this->objFunc->listarLiquidacionDetalle($this->objParam);
            $this->res->imprimirRespuesta($this->res->generarJson());
        } else {

            if($this->objParam->getParametro('id_liquidacion') != ''){
                $this->objParam->addFiltro("liqui.id_liquidacion = ".$this->objParam->getParametro('id_liquidacion'));
            }
            if($this->objParam->getParametro('estado') != ''){
                $this->objParam->addFiltro("liqui.estado = ''".$this->objParam->getParametro('estado')."'' " );
            }

            $this->objFunc=$this->create('MODLiquidacion');
            $this->res=$this->objFunc->listarLiquidacion($this->objParam);
            $this->res->imprimirRespuesta($this->res->generarJson());
        }


    }


    function obtenerCambioOficiales() {
        $this->objFunc=$this->create('MODLiquidacion');
        $this->res=$this->objFunc->obtenerCambioOficiales($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function listarDeposito() {
        $this->objFunc=$this->create('MODLiquidacion');
        $this->res=$this->objFunc->listarDeposito($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    function listarFactuCom() {
        $this->objFunc=$this->create('MODLiquidacion');
        $this->res=$this->objFunc->listarFactuCom($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    function listarFactucomcon() {
        $this->objFunc=$this->create('MODLiquidacion');
        $this->res=$this->objFunc->listarFactucomcon($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    //esta factura es para la version nueva del erp2 DE LA TABLA VENTA
    function listarFactura() {
        $this->objFunc=$this->create('MODLiquidacion');
        $this->res=$this->objFunc->listarFactura($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function listarConcepto() {
        $this->objFunc=$this->create('MODLiquidacion');
        $this->res=$this->objFunc->listarConcepto($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function listarLiquidacionDocConceptosOriginales() {
        $this->objFunc=$this->create('MODLiquidacion');
        $this->res=$this->objFunc->listarLiquidacionDocConceptosOriginales($this->objParam);


        if ($this->res->getTipo() != 'EXITO') {

            $this->res->imprimirRespuesta($this->res->generarJson());
            exit;
        }

        $data = $this->res->getDatos();

        $dataJson = json_decode($data["mensaje"]);

        $send = array(
            "total" => count($dataJson),
            "datos"=>$dataJson != null ? $dataJson : []
        );
        $send = json_encode($send, true);
        echo $send;

    }

    function insertarNotaDesdeLiquidacion() {
        $this->objFunc=$this->create('MODLiquidacion');
        $this->res=$this->objFunc->insertarNotaDesdeLiquidacion($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    function anularLiquidacion() {
        $this->objFunc=$this->create('MODLiquidacion');
        $this->res=$this->objFunc->anularLiquidacion($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    function FechaPago() {
        $this->objFunc=$this->create('MODLiquidacion');
        $this->res=$this->objFunc->FechaPago($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function getLiquidacionDinamica () {


        $this->objParam->defecto('dir_ordenacion', 'ASC');
        $this->objParam->parametros_consulta['ordenacion'] = 'id_tipo_doc_liquidacion';
        $this->objParam->defecto('dir_ordenacion', 'ASC');
        $this->objParam->parametros_consulta['ordenacion'] = 'id_tipo_doc_liquidacion';
        $this->objParam->parametros_consulta['cantidad'] = '10000'; // traer todos los tipos doc
        $this->objParam->parametros_consulta['puntero'] = '0';
        $this->objParam->parametros_consulta['filtro'] = ' 0 = 0 ';

        $this->objFunc=$this->create('MODTipoDocLiquidacion');
        $this->res=$this->objFunc->listarTipoDocLiquidacion($this->objParam);

        if ($this->res->getTipo() != 'EXITO') {

            $this->res->imprimirRespuesta($this->res->generarJson());
            exit;
        }

        $data = $this->res->getDatos();

        $this->objParam->addParametro('estado', 'pagado');

        $dataDinamico = array();
        foreach ($data as $rowTipoDoc) {
            $this->objParam->addParametro('tipo_tab_liqui', $rowTipoDoc['tipo_documento']);
            $this->objFunc=$this->create('MODLiquidacion');
            $this->res=$this->objFunc->listarLiquidacionJson($this->objParam);
            if ($this->res->getTipo() != 'EXITO') {
                $this->res->imprimirRespuesta($this->res->generarJson());
                exit;
            }


            $data = $this->res->getDatos();

            $dataJson = json_decode($data["mensaje"]);
            $send = array(
                "total" => $dataJson->count,
                "datos"=> $dataJson->datos != null ? $dataJson->datos : []
            );


            foreach ($send["datos"] as $row) {

                $liquiDetData = '';
                if(is_array($row->_desc_liqui_det)) {
                    foreach ($row->_desc_liqui_det as $liquiDet) {
                        $liquiDetData .= $liquiDet->_concepto .', ';
                    }
                } else {
                    $liquiDetData = $row->_desc_liqui_det;
                }

                $nroCheque = '';
                $nombreCheque = '';
                if(is_array($row->liqui_forma_pago)) {
                    foreach ($row->liqui_forma_pago as $liqui_forma_pago) {
                        $nroCheque .= 'Nro: '. $liqui_forma_pago->nro_documento_pago. ' ,';
                        $nombreCheque .= $liqui_forma_pago->nombre. ' ,';
                    }
                }

                array_push($dataDinamico, array(
                    "desc_tipo_documento" => $row->desc_tipo_documento,
                    "codigo_punto_venta" => $row->codigo_punto_venta,
                    "fecha_pago" => $row->fecha_pago,
                    "_liqui_nro_doc_original" => $row->_liqui_nro_doc_original,
                    "_liqui_nombre_doc_original" => $row->_liqui_nombre_doc_original,
                    "_desc_liqui_det" => $liquiDetData,
                    "nombreCheque" => $nombreCheque,
                    "importe_devolver" => $row->importe_devolver,
                    "nroCheque" => $nroCheque,
                    "_liqui_codigo_agencia_doc_original" => $row->_liqui_codigo_agencia_doc_original,
                    "_liqui_oficina_emisora_original" => $row->_liqui_oficina_emisora_original,
                ));


            }


        }

        return $dataDinamico;



    }
    function generarReporteLiquidacionesPagadas() {
        $nombreArchivo = uniqid('liquidacionesPagadasXLS_'.md5(session_id())).'.xls';


        $dataForReport = $this->getLiquidacionDinamica();



        //Parametros básicos
        $tamano = 'LETTER';
        $orientacion = 'L';
        $titulo = 'Detalle Dep.';

        $this->objParam->addParametro('orientacion',$orientacion);
        $this->objParam->addParametro('tamano',$tamano);
        $this->objParam->addParametro('titulo_archivo',$titulo);
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);

        $reporte = new RLiquidacionesPagadasXls($this->objParam);
        $reporte->setMaster($dataForReport);
        $reporte->setData($dataForReport);
        $reporte->generarReporte();

        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }


}

?>