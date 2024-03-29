<?php

/**
 * @package pXP
 * @file gen-MODNota.php
 * @author  (favio figueroa peñarrieta)
 * @date 18-11-2014 19:30:03
 * @description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */
class MODNota extends MODbase
{
    var $cone;
    var $link;
    var $informix;
    var $sucursal_global;
    var $punto_venta;
    var $pais;
    var $estacion;
    var $tabla_nota_informix; //tabla que contenera la tabla de prueba o de produccion

    function __construct(CTParametro $pParam)
    {
        parent::__construct($pParam);

        if($_SESSION['_ESTADO_SISTEMA'] === 'produccion') {
            $this->cone = new conexion();
            $this->informix = $this->cone->conectarPDOInformix();
            // conexion a informix
            $this->link = $this->cone->conectarpdo();
            //conexion a pxp(postgres)

            $this->tabla_nota_informix = $_SESSION['tabla_nota_informix'];
        }

    }

    function listarNota()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'decr.ft_nota_sel';
        $this->transaccion = 'FAC_NOT_SEL';
        $this->tipo_procedimiento = 'SEL';
        //tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_nota', 'int4');
        $this->captura('id_sucursal', 'int4');
        $this->captura('id_moneda', 'int4');
        $this->captura('estacion', 'varchar');
        $this->captura('fecha', 'date');
        $this->captura('excento', 'numeric');
        $this->captura('total_devuelto', 'numeric');
        $this->captura('tcambio', 'numeric');
        $this->captura('id_liquidacion', 'varchar');
        $this->captura('nit', 'varchar');
        $this->captura('estado', 'varchar');
        $this->captura('credfis', 'numeric');
        $this->captura('nro_liquidacion', 'varchar');
        $this->captura('monto_total', 'numeric');
        $this->captura('estado_reg', 'varchar');
        $this->captura('nro_nota', 'varchar');
        $this->captura('razon', 'varchar');
        $this->captura('id_usuario_ai', 'int4');
        $this->captura('usuario_ai', 'varchar');
        $this->captura('fecha_reg', 'timestamp');
        $this->captura('id_usuario_reg', 'int4');
        $this->captura('fecha_mod', 'timestamp');
        $this->captura('id_usuario_mod', 'int4');
        $this->captura('usr_reg', 'varchar');
        $this->captura('usr_mod', 'varchar');
        $this->captura('billete', 'varchar');
        $this->captura('nroaut', 'bigint');
        $this->captura('cuenta', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    private function montoTotal($items)
    {
        $res = 0;
        foreach ($items as $item) {
            $res += $item->importe;
        }
        if ($res == 0) {
            throw new Exception("El importe total no puede ser cero", 1);
        }
        return $res;
    }

    private function totalDevuelto($items)
    {
        $res = 0;
        foreach ($items as $item) {
            $res += $item->exento;
        }
        if ($res == 0) {
            throw new Exception("El importe total no puede ser cero", 1);
        }
        return $res;
    }

    function saveForm()
    {
        $items = json_decode($this->aParam->getParametro('newRecords'));


        //$liquidevolu = $this->aParam->getParametro('liquidevolu');
        try {

            $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->informix->beginTransaction();
            $this->link->beginTransaction();
            $i = 0;


            foreach ($items as $item) {
                if ($item->tipo == 'BOLETO') {

                    $temp[] = $this->guardarNotaBoleto($item);
                } elseif ($item->tipo == 'FACTURA' || $item->tipo == 'FACTURA MANUAL') {

                    $monto_total = 0;
                    $excento = 0;
                    if ($this->verSiExisteNota($item->nrofac, $item->nroaut) == 0) {

                        //se crea una nota para esta fila de datos por que no existe en la base de datos
                        $temp[] = $this->guardarNotaFactura($item);
                        $nota = $this->verDatosDeNota($item->nrofac, $item->nroaut);
                        $this->insertarNotaDetalle($item, $nota);

                        //mandamos la fila y el id_nota
                    } else {
                        //exote en la base de datos asi solo se guarda como detalle del que existe
                        $nota = $this->verDatosDeNota($item->nrofac, $item->nroaut);
                        $this->insertarNotaDetalle($item, $nota);
                        //mandamos la fila y el id_nota
                        //funcion para sumar a la nota si esque tiene mas de un detalle
                        $this->notaDetalleSuma($item, $nota);
                    }
                    //$nota_in = $this->insertarNotaInformix($temp[$i]);
                }
                $i++;
            }//fin foreach

            for ($h = 0; $h < count($temp); $h++) {
                $nota_in = $this->insertarNotaInformix($temp[$h]);
            }

            if($this->aParam->getParametro('conceptos_originales_facman')!= ''){


                $this->insertarJsonConceptosOriginales();
            }


            //TODO PONER SI ESTA LA NOTA CON UNA LIQUIDACION  ENTONCES ACTUALIZAR NOTABOA EN LIQUIDEVOLU
            if ($liquidevolu = $this->aParam->getParametro('liquidevolu') != '') {
                //es por una liquidacion la nota que se genera

                //funciona si esta en produccion
                if($this->tabla_nota_informix == 'notacrdb'){
                    $this->actualizarLiquidacion('S'); //se actualizara la liquidacion notaboa a S


                }

            } else {
                //no tiene una liquidacion relacionada

            }

            $this->link->commit();
            $this->informix->commit();
            $this->respuesta = new Mensaje();
            $this->respuesta->setMensaje('EXITO', $this->nombre_archivo, 'La consulta se ejecuto con exito de insercion de nota', 'La consulta se ejecuto con exito', 'base', 'no tiene', 'no tiene', 'SEL', '$this->consulta', 'no tiene');
            $this->respuesta->setTotal(1);
            $this->respuesta->setDatos($temp);
            return $this->respuesta;

        } catch (Exception $e) {

            $this->link->rollBack();
            $this->informix->rollBack();
            $this->respuesta = new Mensaje();
            if ($e->getCode() == 3) {//es un error de un procedimiento almacenado de pxp
                $this->respuesta->setMensaje($resp_procedimiento['tipo_respuesta'], $this->nombre_archivo, $resp_procedimiento['mensaje'], $resp_procedimiento['mensaje_tec'], 'base', $this->procedimiento, $this->transaccion, $this->tipo_procedimiento, $this->consulta);
            } else if ($e->getCode() == 2) {//es un error en bd de una consulta
                $this->respuesta->setMensaje('ERROR', $this->nombre_archivo, $e->getMessage(), $e->getMessage(), 'modelo', '', '', '', '');
            } else {//es un error lanzado con throw exception
                throw new Exception($e->getMessage(), 2);
            }
        } //fin catch

    }

    function guardarNotaBoleto($item)
    {

        $dosificacion = $this->generarDosificacion($item);
        if (count($dosificacion) > 0) {
            $nro_siguiente = $this->generarNroSiguiente($dosificacion);
            $codigo_control = $this->generarCodigoControl($item->nro_nit, $item->total_devuelto, $dosificacion, $nro_siguiente);
            $id_nota = $this->insertarNota($item, $codigo_control, $dosificacion, $nro_siguiente);
            $this->insertarNotaDetalle($item, $id_nota);
            //$nota_in = $this->insertarNotaInformix($id_nota, $dosificacion);
            return $id_nota;
        } else {
            throw new Exception('NO tienes una dosificacion para la sucursal seleccionada');
        }

    }

    function guardarNotaFactura($item)
    {

        $dosificacion = $this->generarDosificacion($item);

        if (count($dosificacion) > 0) {

            if ($item->total_devuelto != '') {
                $nro_siguiente = $this->generarNroSiguiente($dosificacion);
                $codigo_control = $this->generarCodigoControl($item->nit, $item->total_devuelto, $dosificacion, $nro_siguiente);
                $id_nota = $this->insertarNota($item, $codigo_control, $dosificacion, $nro_siguiente);
            } else {
                throw new Exception('uno de tus totales no estan o el exento no esta correccto');
            }


            return $id_nota;
        } else {
            throw new Exception('NO tienes una dosificacion para la sucursal seleccionada');
        }
    }

    function generarNroSiguiente($dosificacion)
    {

        $arra_json = json_encode($dosificacion[0]);
        $nro_si = $this->link->prepare("select decr.f_dosi_siguiente('" . $arra_json . "')");
        $nro_si->execute();
        $nro_si_res = $nro_si->fetchAll(PDO::FETCH_ASSOC);
        return $nro_si_res[0]["f_dosi_siguiente"];

    }

    function generarDosificacion($item)
    {

        $fecha_now = new DateTime("now");
        $fecha = $fecha_now->format('Ymd');

        $this->informix->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $usuario = $_SESSION['_LOGIN'];


        $usuario_sucursal = $this->link->prepare("select
						sucus.id_sucursal_usuario,
						sucus.tipo,
						sucus.id_usuario,
						sucus.estado_reg,
						sucus.id_sucursal,
						sucus.id_usuario_ai,
						sucus.id_usuario_reg,
						sucus.fecha_reg,
						sucus.usuario_ai,
						sucus.id_usuario_mod,
						sucus.fecha_mod,
						usudep.id_usuario,
						usudep.cuenta,
						person.nombre_completo1 as desc_usuario,
						su.estacion as desc_sucursal
						from decr.tsucursal_usuario sucus
						inner join segu.tusuario usudep on usudep.id_usuario=sucus.id_usuario
			            inner join segu.vpersona person on person.id_persona=usudep.id_persona
			            inner join decr.tsucursal su on su.id_sucursal = sucus.id_sucursal
			            where usudep.cuenta = '$usuario' and sucus.tipo = 'RESPONSABLE' limit 1");
        $usuario_sucursal->execute();
        $usuario_sucursal_result = $usuario_sucursal->fetchAll(PDO::FETCH_ASSOC);


        $sucursal = $usuario_sucursal_result[0]['desc_sucursal'];
        $this->estacion = $sucursal;
        $this->verPuntoVenta();


        $liquidacion = $this->aParam->getParametro('liquidevolu');

        if (count($usuario_sucursal_result) == 1) {

            if ($liquidacion != '') {
                //es por una devolucion liquidacion

                $suc_de_liquidacion = $this->str_osplit($liquidacion, 3); //obtenemos la sucursal de la devolucion
                if ($suc_de_liquidacion[0] == $usuario_sucursal_result[0]['desc_sucursal']) {
                    //misma sucursal y que el de la liquidacion y puede ser dosificado

                    $sql_in = $this->informix->prepare("select dos.feciniemi,dos.feclimemi, dos.glosa_impuestos,dos.llave,dos.nroaut,dos.id_dosificacion,dos.sucursal,dos.inicial,dos.final,dos.estacion
						from dosdoccom dos
						where dos.estacion = '$sucursal'
						and dos.nombre_sisfac = 'SISTEMA FACTURACION NCD'
						AND dos.estado = 'activo'
						and dos.feciniemi <= '" . $fecha_now->format('d-m-Y') . "'
						and dos.feclimemi >= '" . $fecha_now->format('d-m-Y') . "' ");



                } else {
                    throw new Exception('Solo puedes dosificar para ' . $sucursal . ' no para ' . $suc_de_liquidacion[0] . '  ');

                }

            } else {
                //es por una factura


                $sql_in = $this->informix->prepare("select dos.feciniemi,dos.feclimemi,dos.glosa_impuestos,dos.llave,dos.nroaut,dos.id_dosificacion,dos.sucursal,dos.inicial,dos.final,dos.estacion
						from dosdoccom dos
						where dos.estacion = '$sucursal'
						and dos.nombre_sisfac = 'SISTEMA FACTURACION NCD'
						AND dos.estado = 'activo'
						and dos.feciniemi <= '" . $fecha_now->format('d-m-Y') . "'
						and dos.feclimemi >= '" . $fecha_now->format('d-m-Y') . "' ");
            }


        } else {
            throw new Exception('NO tienes permiso para usar la dosificacion o puede que tengas mas de una sucursal habilitada para dosificar');
        }




        $sql_in->execute();
        $results = $sql_in->fetchAll(PDO::FETCH_ASSOC);

        //si agarro mas de una dosificacion solo deberia agarrar una
        if(count($results) > 1){
            throw new Exception('existe un problema hay dos dosificaciones tomadas consulte con sistemas');

        }



        return $results;
    }

    function generarCodigoControl($nit, $total_devuelto, $dosificacion, $nro_siguiente)
    {
        $fecha_now = new DateTime("now");
        $date = new DateTime('now');

        $id_dosi = $dosificacion[0]['id_dosificacion'];

        $func_cod_con = $this->link->prepare("select pxp.f_gen_cod_control(
										'" . $dosificacion[0]['llave'] . "',
										'" . $dosificacion[0]['nroaut'] . "','" . $nro_siguiente . "','" . $nit . "','" . $date->format('Ymd') . "',round('" . $total_devuelto . "',0))");

        $func_cod_con->execute();
        $codigo_control = $func_cod_con->fetchAll(PDO::FETCH_ASSOC);
        return $codigo_control;
    }

    function insertarNota($item, $codigo_control, $dosificacion, $nro_siguiente)
    {

        $razon = trim($item->razon);

        $total_para_devolver = $item->importe_devolver - $item->exento;
        $credfis = $total_para_devolver * 0.13;
        $id_dosi = $dosificacion[0]['id_dosificacion'];
        $stmt = $this->link->prepare("INSERT INTO decr.tnota
										(
										  id_usuario_reg,
										  id_usuario_mod,
										  fecha_reg,
										  fecha_mod,
										  estado_reg,
										  id_usuario_ai,
										  usuario_ai,

										  estacion,
										  id_sucursal,
										  estado,
										  nro_nota,
										  fecha,
										  razon,
										  tcambio,
										  nit,
										  id_liquidacion,
										  nro_liquidacion,
										  id_moneda,
										  monto_total,
										  excento,
										  total_devuelto,
										  credfis,
										  billete,
										  codigo_control,
										  id_dosificacion,
										  nrofac,
										  nroaut,
										  fecha_fac,
										  tipo,
										  nroaut_anterior,
										  fecha_limite
										)

										VALUES (

										  " . $_SESSION['ss_id_usuario'] . ",
										  null,
										  now(),
										  null,
										  'activo',
										  " . $_SESSION['ss_id_usuario'] . ",
										  null,

										  '" . $this->estacion . "',
										  '1',
										  '1',
										  '" . $nro_siguiente . "',
										   now(),
										  trim('" . $razon . "'),
										  '6.9',
										  '" . $item->nro_nit . "',
										  1,
										  '" . $item->nroliqui . "',
										  1,
										  " . $item->importe_devolver . ",
										   " . $item->exento . ",
										   '" . $total_para_devolver . "',
										  " . $credfis . ",
										  '" . $item->nro_billete . "',
										  '" . $codigo_control[0]['f_gen_cod_control'] . "',
										  '" . $dosificacion[0]['id_dosificacion'] . "',
										  '" . $item->nrofac . "',
										  '" . $dosificacion[0]['nroaut'] . "',
										  '" . $item->fecha_fac . "',
										  '" . $item->tipo . "',
										  '" . $item->nroaut . "',
										  '" . $dosificacion[0]['feclimemi'] . "'
										)RETURNING id_nota;");

        $dosi_up = $this->link->prepare("update decr.tdosi_correlativo set nro_siguiente = (cast(nro_siguiente as int) + 1) where id_dosificacion = '$id_dosi'");

        $dosi_up->execute();
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $results[0]['id_nota'];
    }

    function insertarNotaDetalle($item, $id_nota)
    {

        $stmt2 = $this->link->prepare("INSERT INTO
				  decr.tnota_detalle
				(
				  id_usuario_reg,
				  estado_reg,
				  id_nota,
				  importe,
				  cantidad,
				  concepto,
				  exento,
				  total_devuelto,
				  precio_unitario
				)
				VALUES (
				  " . $_SESSION['ss_id_usuario'] . ",
				  'activo',
				  '" . $id_nota . "',
				  '" . $item->importe_devolver . "',
				    " . $item->cantidad.",
				   '" . $item->concepto . "',
				   '" . $item->exento . "',
				   '" . $item->total_devuelto . "',
				   '" . $item->precio_unitario . "'
				);");


        $stmt2->execute();
    }

    function notaDetalleSuma($item, $nota)
    {
        $total = $item->monto_total + $item->exento;
        $stmt2 = $this->link->prepare("update decr.tnota
								set monto_total = monto_total + '$item->importe_devolver'
								, excento = excento + '$item->exento'
								, total_devuelto = total_devuelto + '$item->total_devuelto'
								 WHERE id_nota = '$nota'");
        $stmt2->execute();
    }

    function listarNotaCompleta($id_nota)
    {
        $stmt2 = $this->link->prepare("select * from decr.tnota where id_nota = '$id_nota'");
        $stmt2->execute();
        $results = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }


    function actualizarLiquidacion($digito)
    {

        $liquidevolu = $this->aParam->getParametro('liquidevolu');

        $sql = "update liquidevolu set notaboa = '$digito'
					where nroliqui = '$liquidevolu'";

        $informix_res = $this->informix->prepare($sql);
        $informix_res->execute();


    }

    function verPuntoVenta()
    {
        $sql = "SELECT * from agencias
					where estacion = '$this->estacion' AND ctoato = 'D'";

        $informix_res = $this->informix->prepare($sql);
        $informix_res->execute();
        $results = $informix_res->fetchAll(PDO::FETCH_ASSOC);

        $this->punto_venta = $results[0]['agt'];
        $this->pais = $results[0]['pais'];
        $this->sucursal_global = $results[0]['sucursal'];
        return;
    }

    function insertarNotaInformix($id_nota)
    {


        $totales = $this->detalleTotales($id_nota);
        $nota = $this->listarNotaCompleta($id_nota);

        $nroliqui = $this->objParam->getParametro('liquidevolu');
        $estacion = $this->objParam->getParametro('sucursal');

        $fecha_reg = $nota[0]['fecha_reg'];
        $date = new DateTime($fecha_reg);
        //var_dump($date->format('d-m-Y'));
        $fecha_fac = new DateTime($nota[0]['fecha_fac']);
        $fecha = new DateTime($nota[0]['fecha']);

        $nro_factura_anterior = '';
        $nro_autorizacion_anterior = '';

        $observaciones = '';
        $usuario = $_SESSION['_LOGIN'];
        if ($nota[0]['tipo'] == 'FACTURA' || $nota[0]['tipo'] == 'FACTURA MANUAL') {
            $nro_factura_anterior = $nota[0]['nrofac'];
            $nro_autorizacion_anterior = $nota[0]['nroaut_anterior'];
        } else {
            $observaciones = 'LIQUIDACION NRO: ' . $nota[0]['nro_liquidacion'];

        }

        $sql_in = "INSERT INTO ingresos:$this->tabla_nota_informix
						(pais, estacion, puntoven,
						 sucursal, estado, billete,
						  nrofac, nroaut, fechafac,
						  montofac, nronota, nroautnota,
						   fecha, tcambio, razon,
						   nit, nroliqui, moneda,
						   monto, exento, ptjiva,
						   neto, credfis, notamancom,
						    codcontrol, observa, usuario,
						     fechareg, horareg, devuelto,
						      saldo)
					VALUES
						('" . $this->pais . "', '" . $this->estacion . "', '" . $this->punto_venta . "',
						 '" . $this->sucursal_global . "', '1', '" . round($nota[0]['billete'], 0) . "',
						 '" . $nro_factura_anterior . "', '" . $nro_autorizacion_anterior . "', '" . $fecha_fac->format('d-m-Y') . "',
						 '" . $nota[0]['monto_total'] . "', '" . $nota[0]['nro_nota'] . "', '" . $nota[0]['nroaut'] . "',
						 '" . $fecha->format('d-m-Y') . "', '" . $nota[0]['tcambio'] . "', '" . $nota[0]['razon'] . "',
						 '" . $nota[0]['nit'] . "', '" . $nota[0]['nro_liquidacion'] . "', 'BOB',
						  '" . $nota[0]['monto_total'] . "', '" . $nota[0]['excento'] . "', '13.00',
						   '" . $nota[0]['total_devuelto'] . "', '" . $nota[0]['credfis'] . "', 'COMPUTARIZADA',
						   '" . $nota[0]['codigo_control'] . "', '" . $observaciones . "', '" . $usuario . "',
						   '" . $date->format('d-m-Y') . "' , '" . $date->format('H:i:s') . "' , '" . $nota[0]['total_devuelto'] . "' ,
						   '" . $nota[0]['monto_total'] . "')";

        $info_nota_ins = $this->informix->prepare($sql_in);
        $info_nota_ins->execute();
        $results = $info_nota_ins->fetchAll(PDO::FETCH_ASSOC);
        return true;
    }

    function detalleTotales($id_nota)
    {
        $stmt2 = $this->link->prepare("select sum(importe) as total_importe,sum(exento) as total_excento from decr.tnota_detalle where id_nota = '$id_nota' ");
        $stmt2->execute();

        $results = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        $importe_original = $results[0]["total_importe"];
        $excento_total = $results[0]["total_excento"];

        $total_devuelto = $importe_original - $excento_total;

        $credifis = ($importe_original - $excento_total) * 0.13;

        $nota = $this->listarNotaCompleta($id_nota);
        $dosificacion = $this->generarDosificacion($item);

        $codigo_control = $this->generarCodigoControl($nota[0]['nit'], $total_devuelto, $dosificacion, $nota[0]['nro_nota']);

        $cc = $codigo_control[0]['f_gen_cod_control'];

        $stmt3 = $this->link->prepare("update decr.tnota set monto_total = '$importe_original' , excento = '$excento_total' , credfis = '$credifis' , codigo_control = '$cc' where id_nota = '$id_nota'");

        $stmt3->execute();

        $results2 = $stmt3->fetchAll(PDO::FETCH_ASSOC);

        return $results2;
    }

    function verSiExisteNota($nrofac, $nroaut)
    {
        $stmt2 = $this->link->prepare("select count(*) as count
								 from decr.tnota where nrofac = '$nrofac' and nroaut_anterior = '$nroaut' AND estado = '1'");
        $stmt2->execute();
        $results = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        return $results[0]['count'];
    }

    function verDatosDeNota($nrofac, $nroaut)
    {

        $stmt2 = $this->link->prepare("select id_nota
								 from decr.tnota where nrofac = '$nrofac' and nroaut_anterior = '$nroaut' AND estado = '1'");

        $stmt2->execute();

        $results = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        return $results[0]['id_nota'];
    }

    function generarNota()
    {
        //esta version es la ultima version todo en el pxp
        if($this->aParam->getParametro('version') == '2') {

            //Definicion de variables para ejecucion del procedimiento
            $this->procedimiento='decr.ft_nota_sel';
            $this->transaccion='NOTA_GEN_SEL';
            $this->tipo_procedimiento='SEL';

            $this->setParametro('notas','notas','varchar');


            //Definicion de la lista del resultado del query

             $this->captura('id_usuario_reg','int4');
             $this->captura('id_usuario_mod','int4');
             $this->captura('fecha_reg','timestamp');
             $this->captura('fecha_mod','timestamp');
             $this->captura('estado_reg','varchar');
             $this->captura('id_usuario_ai','int4');
             $this->captura('usuario_ai','varchar');
             $this->captura('id_nota','int4');
             $this->captura('estacion','varchar');
             $this->captura('id_sucursal','int4');
             $this->captura('estado','varchar');
             $this->captura('nro_nota','varchar');
             $this->captura('fecha','varchar');
             $this->captura('razon','varchar');
             $this->captura('tcambio','numeric');
             $this->captura('nit','varchar');
             $this->captura('id_liquidacion','varchar');
             $this->captura('nro_liquidacion','varchar');
             $this->captura('id_moneda','int4');
             $this->captura('monto_total','numeric');
             $this->captura('excento','numeric');
             $this->captura('total_devuelto','numeric');
             $this->captura('credfis','numeric');
             $this->captura('billete','varchar');
             $this->captura('codigo_control','varchar');
             $this->captura('id_dosificacion','int4');
             $this->captura('factura','bigint');
             $this->captura('autorizacion','bigint');
             $this->captura('tipo','varchar');
             $this->captura('nroaut_anterior','bigint');
             $this->captura('fecha_fac','varchar');
             $this->captura('nroaut','bigint');
             $this->captura('fecha_limite','varchar');
             $this->captura('cuenta','varchar');

            $this->captura('id_actividad_economica','int4');
            $this->captura('nombre_actividad','varchar');
            $this->captura('glosa_impuestos','varchar');
            $this->captura('glosa_empresa','varchar');
            $this->captura('glosa_consumidor','varchar');
            $this->captura('nro_resolucion','varchar');
            $this->captura('feciniemi','varchar');
            $this->captura('feclimemi','varchar');
            $this->captura('direccion','varchar');
            $this->captura('telefonos','varchar');
            $this->captura('alcaldia','varchar');
            $this->captura('tipo_autoimpresor','varchar');
            $this->captura('autoimpresor','varchar');
            $this->captura('razon_sucursal','varchar');





            //Ejecuta la instruccion
            $this->armarConsulta();
            $this->ejecutarConsulta();

            //Devuelve la respuesta
            return $this->respuesta;

        } else {

            $items_notas = $this->aParam->getParametro('notas');
            //llega los id notas
            $cadena_aux = "";
            if (count($items_notas) == 1) {
                $cadena_aux .= "where nota.id_nota = '$items_notas'";
            } else {
                $coun = count($items_notas) - 1;
                $cadena_aux .= "where nota.id_nota in (";
                for ($i = 0; $i <= $coun; $i++) {
                    if ($i < $coun) {
                        $cadena_aux .= "'$items_notas[$i]',";
                    } else {
                        $cadena_aux .= "'$items_notas[$i]'";
                    }
                }
                $cadena_aux .= ")";
            }

            try {
                //obtener sucursal del usuario
                $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->link->beginTransaction();
                $stmt = $this->link->prepare("SELECT
										  nota.id_usuario_reg,
										  nota.id_usuario_mod,
										  nota.fecha_reg,
										  nota.fecha_mod,
										  nota.estado_reg,
										  nota.id_usuario_ai,
										  nota.usuario_ai,
										  nota.id_nota,
										  nota.estacion,
										  nota.id_sucursal,
										  nota.estado,
										  nota.nro_nota,
										  to_char(nota.fecha,'DD-MM-YYYY') AS fecha,
										  nota.razon,
										  nota.tcambio,
										  nota.nit,
										  nota.id_liquidacion,
										  nota.nro_liquidacion,
										  nota.id_moneda,
										  nota.monto_total,
										  nota.excento,
										  nota.total_devuelto,
										  nota.credfis,
										  nota.billete,
										  nota.codigo_control,
										  nota.id_dosificacion,
										  nota.nrofac as factura,
										  nota.nroaut as autorizacion,
										  nota.tipo,
										  nota.nroaut_anterior,
										  to_char(nota.fecha_fac,'DD-MM-YYYY') AS fecha_fac,

										  nota.nroaut,
										  to_char(nota.fecha_limite,'DD-MM-YYYY') AS fecha_limite,
										  usu1.cuenta
										FROM
										  decr.tnota nota
										  inner join segu.tusuario usu1 on usu1.id_usuario = nota.id_usuario_reg
										  $cadena_aux");

                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);


                $this->link->commit();
                $this->respuesta = new Mensaje();
                $this->respuesta->setMensaje($resp_procedimiento['tipo_respuesta'], $this->nombre_archivo, $resp_procedimiento['mensaje'], $resp_procedimiento['mensaje_tec'], 'base', $this->procedimiento, $this->transaccion, $this->tipo_procedimiento, $this->consulta);

                $this->respuesta->setDatos($results);

                $this->respuesta->getDatos();

            } catch (Exception $e) {
                $this->link->rollBack();
                $this->respuesta = new Mensaje();
                if ($e->getCode() == 3) {//es un error de un procedimiento almacenado de pxp
                    $this->respuesta->setMensaje($resp_procedimiento['tipo_respuesta'], $this->nombre_archivo, $resp_procedimiento['mensaje'], $resp_procedimiento['mensaje_tec'], 'base', $this->procedimiento, $this->transaccion, $this->tipo_procedimiento, $this->consulta);
                } else if ($e->getCode() == 2) {//es un error en bd de una consulta
                    $this->respuesta->setMensaje('ERROR', $this->nombre_archivo, $e->getMessage(), $e->getMessage(), 'modelo', '', '', '', '');
                } else {//es un error lanzado con throw exception
                    throw new Exception($e->getMessage(), 2);
                }
            }

            return $this->respuesta;

        }


    }


    function listarDosificacion($id_dosificacion)
    {

        try {
            $this->informix->beginTransaction();


            $sql_in = "select act.nombre_actividad,dos.glosa_impuestos,
                        dos.glosa_boa,dos.glosa_consumidor,dos.nro_resolucion,
						dos.feciniemi,TO_CHAR(dos.feclimemi, '%d-%m-%Y') as feclimemi,suc.direccion,suc.telefonos,suc.alcaldia,dos.sucursal,dos.tipo_autoimpresor,dos.autoimpresor,suc.razon
						from dosdoccom dos
						inner join sucursal suc on suc.sucursal = dos.sucursal
						inner join tif_actividad_economica act on act.id_actividad_economica = dos.id_actividad_economica
						where dos.id_dosificacion = '$id_dosificacion'";

            $info_nota_ins = $this->informix->prepare($sql_in);
            $info_nota_ins->execute();

            $results = $info_nota_ins->fetchAll(PDO::FETCH_ASSOC);
            $this->informix->commit();
            return $results;


        } catch (Exception $e) {
            $this->informix->rollBack();
        }

    }

    function reImpresion()
    {

        $id_nota = $this->aParam->getParametro('notas');
        $date = new DateTime('now');

        //inserto el arreglo de reimpresion
        $arreglo_impresion = '{{' . $_SESSION['ss_id_usuario'] . ', "' . $_SESSION['_NOM_USUARIO'] . '", ' . $date->format('Y-m-d H:i:s') . '}}';
        $reim = $this->link->prepare("update decr.tnota set reimpresion = reimpresion  || '$arreglo_impresion' WHERE id_nota ='$id_nota'");
        $reim->execute();

        //$dosi_result = $reim->fetchAll(PDO::FETCH_ASSOC);
    }

    function anularNota()
    {

        $this->verNotaInformix();


    }

    function verNotaInformix()
    {
        $nro_liquidacion = $this->aParam->getParametro('nro_liquidacion');
        $nota_informix = $this->aParam->getParametro('nota_informix');
        $nroaut = $this->aParam->getParametro('nroaut');



        if ($nro_liquidacion != '') { // esta nota tiene liquidacion ligada

            $sql = "select nota.nroliqui,liqui.estpago
				from $this->tabla_nota_informix nota
				inner join liquidevolu liqui on liqui.nroliqui = nota.nroliqui
				 where   nota.nronota = '$nota_informix' and nota.nroautnota = '$nroaut' ";

            $info_nota = $this->informix->prepare($sql);
            $info_nota->execute();
            $results = $info_nota->fetchAll(PDO::FETCH_ASSOC);


            if ($results[0]['ESTPAGO'] == 'P') {
                throw new Exception('NO SE PUEDE ANULAR, YA ESTA PAGADO');
            } else {
                $this->anularNotaLiquidacion();
                //TODO ANULAR UNA NOTA LIGADA A UNA LIQUIDACION
            }

        } else {//no tiene liquidacion
            //$this->anularNotaLiquidacion(); // esto solo cuando sucede casos excepcionales debe estar comentado
        }


        return true;
    }

    //funcion para anular una nota ligada a una liquidacion
    function anularNotaLiquidacion()
    {

        $nota_informix = $this->aParam->getParametro('nota_informix');
        $nroaut = $this->aParam->getParametro('nroaut');
        $nota = $this->aParam->getParametro('notas');
        $id_nota = $this->aParam->getParametro('id_nota');
        //id nota para comparar con informix

        try {
            $this->link->beginTransaction();
            $this->informix->beginTransaction();

            $sql = "UPDATE decr.tnota SET estado = 9, total_devuelto = 0
					,monto_total = 0, excento = 0, credfis = 0, id_usuario_mod = " . $_SESSION['ss_id_usuario'] . ", fecha_mod = now()   WHERE id_nota ='$nota'";

            $sql_conceptos = "update decr.tnota_detalle set importe = 0, exento =0,total_devuelto=0
								where id_nota ='$nota' ";
            //$sql_conceptos ="select * from fac.tnota_detalle where id_nota = '$id_nota'";

            $res = $this->link->prepare($sql);
            $res->execute();

            $res2 = $this->link->prepare($sql_conceptos);
            $res2->execute();

            $sql_in = "UPDATE $this->tabla_nota_informix SET razon='ANULADA',
                                                            nit=0,
                                                            monto=0,
                                                            exento=0,
                                                            neto=0,
                                                            credfis=0,
                                                            devuelto=0,
                                                            saldo=0,
                                                            estado = 9,
                                                            nroliqui = '',
                                                            ptjiva=0,
                                                            billete='',
                                                            codcontrol='',
                                                            montofac=0
                                                            WHERE nronota ='$nota_informix'
            and nroautnota = '$nroaut'";


            $info_nota_ins = $this->informix->prepare($sql_in);
            $info_nota_ins->execute();




            if ($liquidevolu = $this->aParam->getParametro('liquidevolu') != '') {
                //funciona si esta en produccion
                if($this->tabla_nota_informix == 'notacrdb'){
                    $this->actualizarLiquidacion('N');
                }
            }



            //$results = $res2->fetchAll(PDO::FETCH_ASSOC);

            $this->link->commit();
            $this->informix->commit();
            return true;

        } catch (Exception $e) {
            $this->link->rollBack();
        }

    }


    function str_osplit($string, $offset)
    {
        return isset($string[$offset]) ? array(substr($string, 0, $offset), substr($string, $offset)) : false;
    }


    function insertarJsonConceptosOriginales(){

        $conceptos_originales_manuales = json_decode($this->aParam->getParametro('conceptos_originales_facman'));



        foreach ($conceptos_originales_manuales as $item) {

            $stmt2 = $this->link->prepare("select id_nota
								 from decr.tnota where nrofac = '$item->nrofac' and nroaut_anterior = '$item->nroaut'");

            $stmt2->execute();

            $results = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            $id_nota = $results[0]['id_nota'];


            $res = $this->link->prepare("insert into decr.tconcepto_original(
                                  estado_reg,
                                  tipo,
                                  concepto,
                                  importe_original,
                                  id_nota,
                                  id_usuario_reg,
                                  fecha_reg,
                                  usuario_ai,
                                  id_usuario_ai,
                                  id_usuario_mod,
                                  fecha_mod,
                                  precio_unitario,
                                  cantidad
                                ) values(
                                  'activo',
                                 'manual',
                                 '$item->concepto',
                                  '$item->importe_original',
                                  '$id_nota',
                                  1,
                                  now(),
                                  'NULL',
                                  null,
                                  null,
                                  null,
                                  $item->precio_unitario,
                                  $item->cantidad)");


            $res->execute();




        }


    }


    function generarNotaPxp(){

        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'decr.ft_nota_ime';
        $this->transaccion = 'FAC_NOT_JSONIME';
        $this->tipo_procedimiento = 'IME';

        $valuesJson = json_encode($this->aParam->arreglo_parametros);


        //$this->aParam->addParametro('values_json', '{"tipo_id":"LIQUIDACION","liquidevolu":"10"}');
        //$this->arreglo['values_json'] = '{"tipo_id":"LIQUIDACION","liquidevolu":"10"}';

        $this->aParam->addParametro('values_json', $valuesJson);
        $this->arreglo['values_json'] = $valuesJson;
        $this->setParametro('values_json','values_json','text');
        $this->setParametro('tipo_id','tipo_id','varchar');

        $this->setParametro('detalle','newRecords','text');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function verNota(){

        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'decr.ft_nota_ime';
        $this->transaccion = 'FAC_GENNOTA_JSON';
        $this->tipo_procedimiento = 'IME';

        $this->setParametro('notas','notas','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }


    function listarBoletoParaUsarEnNota(){

        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'decr.ft_nota_sel';
        $this->transaccion = 'NOTA_BOLDEV_SEL';
        $this->tipo_procedimiento = 'SEL';
        //tipo de transaccion

        $this->setParametro('billete','billete','varchar');


        //Definicion de la lista del resultado del query
        $this->captura('billete', 'varchar');
        $this->captura('fecha', 'date');
        $this->captura('pasajero', 'varchar');
        $this->captura('moneda', 'varchar');
        $this->captura('importe', 'numeric');
        $this->captura('estado', 'varchar');
        $this->captura('nrofac', 'varchar');
        $this->captura('nroaut', 'int4');
        $this->captura('nit', 'varchar');
        $this->captura('nro_nit', 'varchar');
        $this->captura('razon', 'varchar');
        $this->captura('monto', 'numeric');
        $this->captura('exento', 'int4');
        $this->captura('fecha_fac', 'date');
        $this->captura('tipo', 'varchar');
        $this->captura('concepto_original', 'varchar');
        $this->captura('concepto', 'varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function generarNotaDesdeLiquidacion(){

        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'decr.ft_nota_ime';
        $this->transaccion = 'FAC_NOT_NOTLIQ';
        $this->tipo_procedimiento = 'IME';

        $this->setParametro('id_liquidacion','id_liquidacion','int4');
        $this->setParametro('desc_tipo_documento','desc_tipo_documento','varchar');
        $this->setParametro('razonSocialParaNota','razonSocialParaNota','varchar');
        $this->setParametro('importeParaNota','importeParaNota','numeric');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }


    function listarNotaJson(){

        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'decr.ft_nota_ime';
        $this->transaccion = 'FAC_REPNOTA_JSON';
        $this->tipo_procedimiento = 'IME';

        $this->setParametro('fecha_ini','fecha_ini','date');
        $this->setParametro('id_punto_venta','id_punto_venta','int4');
        $this->setParametro('fecha_fin','fecha_fin','date');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

}

?>