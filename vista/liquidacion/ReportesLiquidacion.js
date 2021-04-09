var imprimirParaAdministradora = ({total, datos}) => {
    console.log(datos)
    //columnas para reporte
    //

    const header = `<tr>
    <th>NRO</th>
    <th> LUGAR</th>
    <th> NRO LIQUIDACION</th>
    <th> PASAJERO</th>
    <th> ESTABLEC.</th>
    <th>DENOMICACION E.</th>
    <th>NRO DE TARJETA</th>
    <th> FECHA PROCESO</th>
    <th> FECHA ORIGEN</th>
    <th> IMP.CRED BS</th>
    <th> LOTE</th>
    <th> CPTE</th>
    </tr>`
    const dataForRender = datos.reduce((valorAnterior, valorActual, indice, vector)=> {
        console.log('valorAnterior',valorAnterior)

        let tr = '';
        valorActual.liqui_forma_pago.forEach((formaPago)=> {
            tr = `${tr}
                <tr>
                    <td>${indice + 1}</td>   
                    <td>${valorActual.estacion}</td>   
                    <td>${valorActual.nro_liquidacion}</td>   
                    <td>${valorActual.nombre}</td>   
                    <td>${formaPago.cod_est}</td>   
                    <td>${valorActual.desc_punto_venta}</td>   
                    <td>${formaPago.nro_tarjeta}</td>   
                    <td></td>   
                    <td></td>   
                    <td>${formaPago.importe}</td>   
                    <td>${formaPago.lote}</td>   
                    <td>${formaPago.comprobante}</td>   
                </tr>
        `
        })
        return `${valorAnterior && valorAnterior}${tr}`

    },'');
    console.log(dataForRender)



    const table = `<table width="90%" style="font-size: 8pt; margin: auto;">${header} ${dataForRender}</table>`;

    console.log('table',table)
    const html = `<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;">
  <link rel="stylesheet" href="../../../sis_devoluciones/control/print.css" type="text/css" media="print" charset="utf-8">

</head>
<body  style="line-height: 18px; font-size: 10pt;">
<div style="width: 100%; border: 1px solid black; margin: auto; ">
<span><b>${datos[0].liqui_forma_pago[0].administradora}</b></span>
${table}
</div>
</body>
</html>`

    return html;
}