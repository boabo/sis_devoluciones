/***********************************I-SCP-FFP-DECR-3-12/02/2015****************************************/


CREATE SCHEMA informix;

CREATE EXTENSION informix_fdw;

CREATE SERVER sai1
FOREIGN DATA WRAPPER informix_fdw
OPTIONS (informixserver 'sai1');

CREATE USER MAPPING FOR CURRENT_USER
SERVER sai1
OPTIONS (username 'conexinf', password 'conexinf123');

CREATE FOREIGN TABLE informix.liquidevolu (
  pais varchar(3),
  estacion varchar(3),
  docmnt varchar(6),
  nroliqui varchar(20),
  fecha date,
  estpago varchar(1),
  estado varchar(1),
  notaboa varchar(1)

) SERVER sai1

OPTIONS ( query 'SELECT pais,estacion,docmnt,nroliqui,fecha,estpago,estado,notaboa FROM liquidevolu where estado = ''1'' and estpago = ''N'' AND notaboa=''N'' ',
database 'ingresos',
  informixdir '/opt/informix',
  client_locale 'en_US.utf8',
  informixserver 'sai1');


CREATE FOREIGN TABLE informix.liquidevolu_devweb (
  pais varchar(3),
  estacion varchar(3),
  docmnt varchar(6),
  nroliqui varchar(20),
  fecha date,
  estpago varchar(1),
  estado varchar(1),
  notaboa varchar(1)

) SERVER sai1

OPTIONS ( query 'SELECT pais,estacion,docmnt,nroliqui,fecha,estpago,estado,notaboa FROM liquidevolu where estado = ''1'' and estpago = ''P'' AND notaboa=''N'' and docmnt = ''DEVWEB'' ',
database 'ingresos',
  informixdir '/opt/informix',
  client_locale 'en_US.utf8',
  informixserver 'sai1');


  CREATE FOREIGN TABLE informix.liquitra (

 	pais varchar(3),
	estacion varchar(3),
	docmnt varchar(6),
	nroliqui varchar(20),
	renglon integer,
	idtramo varchar(1),
	billcupon decimal(13),
	cupon integer,
	origen varchar(3),
	destino varchar(3),
	estado varchar(1)

) SERVER sai1

OPTIONS ( query 'SELECT
                pais,
                estacion,
                docmnt,
                nroliqui,
                renglon,
                idtramo,
                billcupon,
                cupon,
                origen,
                destino,
                estado
            FROM
                liquitra',
          database 'ingresos',
          informixdir '/opt/informix',
          client_locale 'en_US.utf8',
          informixserver 'sai1');



CREATE TABLE decr.tdosificacion (
  id_dosificacion SERIAL,
  id_lugar_pais INTEGER NOT NULL,
  estacion VARCHAR(50) NOT NULL,
  tipo VARCHAR(50) NOT NULL,
  id_sucursal INTEGER NOT NULL,
  tipo_autoimpresor VARCHAR(150) NOT NULL,
  autoimpresor VARCHAR(150) NOT NULL,
  nroaut VARCHAR(150) NOT NULL,
  inicial VARCHAR(150) NOT NULL,
  final VARCHAR(150) NOT NULL,
  llave VARCHAR(150) NOT NULL,
  fecha_dosificacion DATE NOT NULL,
  nro_tramite VARCHAR(150) NOT NULL,
  nombre_sisfac VARCHAR(150) NOT NULL,
  fecha_inicio_emi DATE NOT NULL,
  notificado VARCHAR(150) NOT NULL,
  id_activida_economica INTEGER NOT NULL,
  glosa_impuestos VARCHAR(150) NOT NULL,
  glosa_consumidor VARCHAR(150) NOT NULL,
  glosa_empresa VARCHAR(150) NOT NULL,
  nro_resolucion VARCHAR(150) NOT NULL,
  fecha_limite DATE,
  nro_siguiente INTEGER,
  CONSTRAINT pk_tdosificacion__id_dosificacion PRIMARY KEY(id_dosificacion)
) INHERITS (pxp.tbase)
WITHOUT OIDS;



CREATE TABLE decr.tnota (
  id_nota SERIAL,
  estacion VARCHAR(20),
  id_sucursal INTEGER,
  estado VARCHAR(50) NOT NULL,
  nro_nota VARCHAR(50) NOT NULL,
  fecha DATE DEFAULT now() NOT NULL,
  razon VARCHAR(50) NOT NULL,
  tcambio NUMERIC(18,6) NOT NULL,
  nit VARCHAR(50) NOT NULL,
  id_liquidacion VARCHAR(50) NOT NULL,
  nro_liquidacion VARCHAR(50) NOT NULL,
  id_moneda INTEGER NOT NULL,
  monto_total NUMERIC(18,6) NOT NULL,
  excento NUMERIC(18,6) NOT NULL,
  total_devuelto NUMERIC(18,6) NOT NULL,
  credfis NUMERIC(18,6) NOT NULL,
  billete VARCHAR(255),
  codigo_control VARCHAR(255),
  id_dosificacion INTEGER,
  nrofac BIGINT,
  nroaut BIGINT,
  reimpresion VARCHAR(100)[],
  CONSTRAINT pk_tnota__id_nota PRIMARY KEY(id_nota)
) INHERITS (pxp.tbase)
WITHOUT OIDS;


ALTER TABLE decr.tnota
  ADD COLUMN fecha_limite DATE;
  

CREATE TABLE decr.tnota_detalle (
  id_nota_detalle SERIAL,
  id_nota INTEGER,
  importe NUMERIC(18,6),
  cantidad INTEGER,
  concepto VARCHAR(255),
  exento NUMERIC(18,6),
  total_devuelto NUMERIC(18,6),
  CONSTRAINT pk_tnota_detalle__id_nota_detalle PRIMARY KEY(id_nota_detalle)
) INHERITS (pxp.tbase)
WITHOUT OIDS;


/***********************************F-SCP-FFP-DECR-3-12/02/2015****************************************/




/***********************************I-SCP-FFP-DECR-4-20/02/2015****************************************/

ALTER TABLE decr.tnota ADD fecha_fac DATE NULL;

/***********************************F-SCP-FFP-DECR-4-20/02/2015****************************************/


/***********************************I-SCP-FFP-DECR-1-02/09/2015****************************************/
ALTER TABLE decr.tdosificacion ALTER COLUMN glosa_impuestos DROP NOT NULL;
ALTER TABLE decr.tdosificacion ALTER COLUMN glosa_consumidor DROP NOT NULL;

ALTER TABLE decr.tnota
  DROP COLUMN reimpresion;
  
ALTER TABLE decr.tnota
  ADD COLUMN reimpresion VARCHAR(255)[][];
  
  
  CREATE TABLE decr.tcasosprueba (
  id_caso_prueba SERIAL,
  autorizacion NUMERIC,
  factura NUMERIC,
  nit NUMERIC,
  fecha VARCHAR(255),
  anio VARCHAR(255),
  mes VARCHAR(255),
  dia VARCHAR(255),
  monto NUMERIC,
  llave VARCHAR(255),
  codigo_de_control_impuestos VARCHAR(255),
  codigo_control_pxp VARCHAR(255),
  validacion VARCHAR(255)
);

/***********************************F-SCP-FFP-DECR-1-02/09/2015****************************************/



/***********************************I-SCP-FFP-DECR-1-10/11/2015****************************************/

CREATE TABLE decr.tdosi_correlativo (
  id_dosi_correlativo SERIAL,
  id_dosificacion INTEGER NOT NULL,
  nro_actual INTEGER NOT NULL,
  nro_siguiente VARCHAR(150) NOT NULL,
  CONSTRAINT pk_tdosi_correlativo__id_dosi_correlativo PRIMARY KEY(id_dosi_correlativo)
) INHERITS (pxp.tbase)
WITHOUT OIDS;


ALTER TABLE decr.tnota
  ADD COLUMN tipo VARCHAR(255);
  
  ALTER TABLE decr.tnota
  ADD COLUMN nroaut_anterior BIGINT;
  


/***********************************F-SCP-FFP-DECR-1-10/11/2015****************************************/





/***********************************I-SCP-FFP-DECR-1-08/12/2015****************************************/



CREATE TABLE decr.tsucursal (
  id_sucursal SERIAL,
  estacion VARCHAR(50) NOT NULL,
  razon VARCHAR(50) NOT NULL,
  direccion VARCHAR(150) NOT NULL,
  id_persona_resp INTEGER NOT NULL,
  telefono VARCHAR(150) NOT NULL,
  alcaldia VARCHAR(150) NOT NULL,
  sucursal INTEGER,
  CONSTRAINT pk_tsucursal__id_sucursal PRIMARY KEY(id_sucursal)
) INHERITS (pxp.tbase)
WITHOUT OIDS;


CREATE TABLE decr.tsucursal_usuario (
  id_sucursal_usuario SERIAL,
  id_sucursal integer,
  id_usuario integer,
  tipo VARCHAR(50) NOT NULL,
  CONSTRAINT pk_tsucursal_usuario__id_sucursal PRIMARY KEY(id_sucursal_usuario)
) INHERITS (pxp.tbase)
WITHOUT OIDS;


ALTER TABLE decr.tsucursal DROP id_persona_resp;

ALTER TABLE decr.tsucursal ADD sucursal INTEGER  NULL;
ALTER TABLE decr.tsucursal ALTER COLUMN telefono DROP NOT NULL;
ALTER TABLE decr.tsucursal ALTER COLUMN alcaldia DROP NOT NULL;

/*
CREATE TYPE ven.sucursales_informix_importacion AS (
  sucursal INTEGER,
  estacion VARCHAR(255),
  razon VARCHAR(255),
  direccion VARCHAR(255),
  telefonos VARCHAR(255),
  alcaldia VARCHAR(255)
);*/

/***********************************F-SCP-FFP-DECR-1-08/12/2015****************************************/



/***********************************I-SCP-FFP-DECR-1-15/12/2015****************************************/


CREATE TABLE decr.tconcepto_original (
  id_concepto_original SERIAL,
  id_nota integer,
  concepto VARCHAR(255),
  tipo VARCHAR(50),
  importe_original NUMERIC(10,2),
  CONSTRAINT pk_tconcepto_original__id_concepto_original PRIMARY KEY(id_concepto_original)
) INHERITS (pxp.tbase)
WITHOUT OIDS;

CREATE TYPE decr.json_conceptos_originales AS (
  concepto VARCHAR(255),
  importe_original NUMERIC(10,2),
  nroaut NUMERIC,
  nrofac NUMERIC
);


ALTER TABLE decr.tconcepto_original ADD precio_unitario NUMERIC(10,2) NULL;

ALTER TABLE decr.tnota_detalle ADD precio_unitario NUMERIC(10,2) NULL;

ALTER TABLE decr.tconcepto_original ADD cantidad INT NULL;


ALTER TABLE decr.tnota
ALTER COLUMN razon TYPE VARCHAR(150) COLLATE pg_catalog."default";



CREATE TABLE decr.tdevweb (
  id_devweb SERIAL,
  id_usuario INTEGER,
  estado VARCHAR(255),

  CONSTRAINT pk_tdevweb__id_devweb PRIMARY KEY(id_devweb)
) INHERITS (pxp.tbase)
WITHOUT OIDS;


/***********************************F-SCP-FFP-DECR-1-15/12/2015****************************************/



/***********************************I-SCP-FFP-DECR-1-15/04/2020****************************************/

-- LIQUIDACION
--create tables

--tipo document de la liquidacion
CREATE TABLE decr.ttipo_doc_liquidacion (
  id_tipo_doc_liquidacion SERIAL,
  tipo_documento varchar(255),
  CONSTRAINT pk_ttipo_doc_liquidacion__id_tipo_doc_liquidacion PRIMARY KEY(id_tipo_doc_liquidacion)
) INHERITS (pxp.tbase)
WITHOUT OIDS;


CREATE TABLE decr.ttipo_liquidacion (
  id_tipo_liquidacion SERIAL,
  tipo_liquidacion varchar(255),
  CONSTRAINT pk_ttipo_liquidacion__id_tipo_liquidacion PRIMARY KEY(id_tipo_liquidacion)
) INHERITS (pxp.tbase)
WITHOUT OIDS;


CREATE TABLE decr.tliquidacion (
  id_liquidacion SERIAL,
  id_forma_pago INTEGER, -- relationship with obingresos.tforma_pago
  id_tipo_doc_liquidacion INTEGER,
  id_tipo_liquidacion INTEGER,
  id_boleto INTEGER,
  nro_liquidacion varchar(255),
  estado VARCHAR(255),
  estacion varchar(255),
  tramo VARCHAR(255),
  pv_agt VARCHAR(255),
  noiata VARCHAR(255),
  descripcion VARCHAR(255),
  fecha_liqui date,
  tipo_de_cambio NUMERIC(10,2),
  moneda_liq VARCHAR(3),
  nombre VARCHAR(255),
  nombre_cheque VARCHAR(255),
  util VARCHAR(255),
  tramo_devolucion VARCHAR(255),
  fecha_pago date,
  cheque VARCHAR(255),

  CONSTRAINT pk_tliquidacion__id_liquidacion PRIMARY KEY(id_liquidacion)
) INHERITS (pxp.tbase)
WITHOUT OIDS;



CREATE TABLE decr.tdescuento_liquidacion (
  id_descuento_liquidacion SERIAL,
  id_liquidacion INTEGER,
  id_concepto_ingas INTEGER, -- relationship with param.tconcepto_ingas
  sobre varchar(10),
  importe NUMERIC(10,2),
  contabilizar VARCHAR(2),-- SI O NO
  CONSTRAINT pk_tdescuento_liquidacion__id_descuento_liquidacion PRIMARY KEY(id_descuento_liquidacion)
) INHERITS (pxp.tbase)
WITHOUT OIDS;



--nota agenca
-- preguntar si los datos de factura  se deben jalar desde el libre de ventas
-- que pasa si no existe esa factura en el libro de ventas
CREATE TABLE decr.tnota_agencia (
  id_nota_agencia SERIAL,
  id_doc_compra_venta INTEGER,
  id_depto_conta INTEGER,
  id_moneda INTEGER NOT NULL,
  estado VARCHAR(50) NOT NULL,
  nit VARCHAR(50) NOT NULL,
  nro_nota VARCHAR(50) NOT NULL,
  nro_aut_nota BIGINT,
  fecha DATE DEFAULT now() NOT NULL,
  razon VARCHAR(50) NOT NULL,
  tcambio NUMERIC(18,6) NOT NULL,
  monto_total NUMERIC(18,6) NOT NULL,
  excento NUMERIC(18,6) NOT NULL,
  total_devuelto NUMERIC(18,6) NOT NULL,
  credfis NUMERIC(18,6) NOT NULL,
  billete VARCHAR(255),
  codigo_control VARCHAR(255),
  nrofac BIGINT,
  nroaut BIGINT,
  fecha_fac date,
  codito_control_fac VARCHAR(255),
  monto_total_fac NUMERIC(10,2),
  iva VARCHAR(10),
  neto NUMERIC(10,2),
  obs varchar(255),
  CONSTRAINT pk_tnota_agencia__id_nota_agencia PRIMARY KEY(id_nota_agencia)
) INHERITS (pxp.tbase)
WITHOUT OIDS;


alter table decr.tnota_agencia
    add id_periodo integer;


alter table decr.ttipo_doc_liquidacion
    add descripcion varchar(255);



alter table decr.tliquidacion
    add punto_venta varchar(255);
alter table decr.tliquidacion
    add moneda_emision varchar(255);
alter table decr.tliquidacion
    add importe_neto numeric(10,2);
alter table decr.tliquidacion
    add tasas numeric(10,2);
alter table decr.tliquidacion
    add importe_total numeric(10,2);



alter table decr.tnota_agencia
    add id_liquidacion integer;


alter table decr.tliquidacion
    add id_punto_venta integer;

alter table decr.tliquidacion
    add id_estado_wf INTEGER;

alter table decr.tliquidacion
    add id_proceso_wf INTEGER;


alter table decr.tliquidacion
    add num_tramite VARCHAR(200);

alter table decr.tliquidacion
    add id_venta integer;



CREATE TABLE decr.tliqui_venta_detalle (
                                             id_liqui_venta_detalle SERIAL,
                                             id_liquidacion INTEGER,
                                             id_venta_detalle INTEGER, -- relationship with param.tconcepto_ingas
                                             CONSTRAINT pk_tliqui_venta_detalle__id_liqui_venta_detalle PRIMARY KEY(id_liqui_venta_detalle)
) INHERITS (pxp.tbase)
  WITHOUT OIDS;



alter table decr.tliquidacion
    add exento numeric;

alter table decr.tliquidacion
    add importe_tramo_utilizado numeric default 0;



alter table decr.tdescuento_liquidacion
    add tipo varchar(255);



CREATE TABLE decr.tliqui_forma_pago (
                                           id_liqui_forma_pago SERIAL,
                                           id_liquidacion INTEGER,
                                           id_forma_pago INTEGER, -- relationship with param.tconcepto_ingas
                                           pais varchar(255),
                                           ciudad varchar(255),
                                           fac_reporte varchar(255),
                                           cod_est varchar(255),
                                           lote varchar(255),
                                           comprobante varchar(255),
                                           fecha_tarjeta date,
                                           nro_tarjeta varchar(255),
                                           importe numeric(10,2),
                                           CONSTRAINT pk_tliqui_forma_pago__id_liqui_forma_pago PRIMARY KEY(id_liqui_forma_pago)
) INHERITS (pxp.tbase)
  WITHOUT OIDS;



alter table decr.tliquidacion
    add id_proceso_wf_factura integer;

alter table decr.tliquidacion
    add id_medio_pago INTEGER;

alter table decr.tliquidacion
    add id_moneda INTEGER;



alter table decr.tliqui_forma_pago
    add id_medio_pago integer;

ALTER TABLE decr.tliqui_forma_pago
    DROP COLUMN id_forma_pago;


alter table decr.tliqui_forma_pago alter column fecha_tarjeta type varchar(255) using fecha_tarjeta::varchar(255);


alter table decr.tliquidacion
    add id_deposito integer;

alter table decr.tliquidacion
    add id_liquidacion_fk integer;



--esta tabla es para relacionar el descuento con una liquidacion para liquidaciones por liquidaciones
CREATE TABLE decr.tliqui_decuento_detalle (
                                           id_liqui_descuento_detalle SERIAL,
                                           id_liquidacion INTEGER,
                                           id_descuento_liquidacion INTEGER,
                                           CONSTRAINT pk_tliqui_decuento_detalle__id_liqui_descuento_detalle PRIMARY KEY(id_liqui_descuento_detalle)
) INHERITS (pxp.tbase)
  WITHOUT OIDS;


drop type decr.json_type_liquidacion;
create type decr.json_type_liquidacion as (
                                         id_liquidacion integer,
                                         usr_reg varchar,
                                         usr_mod varchar,
                                         desc_tipo_documento varchar,
                                         desc_tipo_liquidacion varchar,
                                         desc_punto_venta varchar,
                                         codigo_punto_venta varchar,
                                         id_sucursal integer,
                                         nro_nota varchar,
                                         sum_total_descuentos numeric,
                                         descuentos json,
                                         sum_descuentos json,
                                         liqui_forma_pago json,
                                         notas json,
                                         factura_pagada json,
                                         notas_siat json,
                                         notas_agencia json
                                     );


alter table decr.tliquidacion
    add id_factucom integer;


alter table decr.tliqui_venta_detalle
    add tipo varchar(255);




alter table decr.tliqui_forma_pago
    add nro_documento_pago varchar;




CREATE TABLE decr.tliqui_boleto_recursivo (
                                           id_liqui_boleto_recursivo SERIAL,
                                           id_liquidacion INTEGER,
                                           seleccionado varchar(10),
                                               billete varchar(255),
                                               monto numeric(10,2),
                                               tiene_nota varchar(255),
                                               iva numeric(10,2),
                                               iva_contabiliza_no_liquida numeric(10,2),
                                               exento numeric(10,2),
                                           CONSTRAINT pk_tliqui_boleto_recursivo__id_liqui_boleto_recursivo PRIMARY KEY(id_liqui_boleto_recursivo)
) INHERITS (pxp.tbase)
  WITHOUT OIDS;

alter table decr.tliqui_boleto_recursivo
    add concepto_para_nota varchar(255);

alter table decr.tliqui_boleto_recursivo
    add fecha_emision date;




CREATE TABLE decr.tliqui_manual (
                                              id_liqui_manual SERIAL,
                                              id_liquidacion INTEGER,
                                              tipo_manual varchar(255),
                                              CONSTRAINT pk_tliqui_manual__id_liqui_manual PRIMARY KEY(id_liqui_manual)
) INHERITS (pxp.tbase)
  WITHOUT OIDS;


CREATE TABLE decr.tliqui_manual_detalle (
                                              id_liqui_manual_detalle SERIAL,
                                              id_liqui_manual INTEGER,
                                              administradora varchar(255),
                                              lote varchar(255),
                                              comprobante varchar(255),
                                              fecha varchar(255),
                                              nro_tarjeta varchar(255),
                                              concepto_original varchar(255),
                                              concepto_devolver varchar(255),
                                              importe_original numeric(10,2),
                                              importe_devolver numeric(10,2),
                                              descripcion varchar(255),
                                              CONSTRAINT pk_tliqui_manual_detalle__id_liqui_manual_detalle PRIMARY KEY(id_liqui_manual_detalle)
) INHERITS (pxp.tbase)
  WITHOUT OIDS;


alter table decr.tliquidacion
    add id_liqui_manual integer;

alter table decr.tliqui_manual_detalle
    add id_medio_pago integer;

alter table decr.tliqui_forma_pago
    add administradora varchar(255);


alter table decr.tnota_agencia alter column estado drop not null;


alter table param.tconcepto_ingas
    add tipo_descuento varchar(255);


alter table decr.tliqui_forma_pago
    add nombre varchar(255);




alter table decr.tliquidacion
    add pagar_a_nombre varchar(255);

comment on column decr.tliquidacion.pagar_a_nombre is 'esta columna es  para ayudar a los registros de forma de pago y otros';






CREATE TABLE decr.tliqui_boleto (
                                            id_liqui_boleto SERIAL,
                                            id_liquidacion INTEGER,
                                            data_stage json,
                                            CONSTRAINT pk_tliqui_boleto__id_liqui_boleto PRIMARY KEY(id_liqui_boleto)
) INHERITS (pxp.tbase)
  WITHOUT OIDS;



alter table decr.tliqui_manual_detalle
    add id_cuenta_bancaria integer;


alter table decr.tliqui_manual
    add razon_nombre_doc_org varchar(255);


alter table decr.tliqui_forma_pago
    add autorizacion varchar(255);

alter table decr.tliqui_forma_pago
    add fecha_cierre date;

alter table decr.tliquidacion
    add billetes_seleccionados varchar(255);


alter table decr.tliqui_forma_pago
    add nro_terminal varchar(255);

alter table decr.tliqui_forma_pago
    add nombre_comercio varchar(255);

CREATE TABLE decr.tliqui_boleto_seleccionado (
                                    id_liqui_boleto_seleccionado SERIAL,
                                    id_liquidacion INTEGER,
                                    boleto varchar,
                                    CONSTRAINT pk_tliqui_boleto_seleccionado__id_liqui_boleto_seleccionado PRIMARY KEY(id_liqui_boleto_seleccionado)
) INHERITS (pxp.tbase)
  WITHOUT OIDS;


alter table decr.tliqui_manual_detalle
    add nro_aut varchar(255);



alter table decr.tliqui_boleto_recursivo
    add nit varchar(50);

alter table decr.tliqui_boleto_recursivo
    add razon_social varchar(100);

alter table decr.tliquidacion
    add glosa_pagado text;

alter table decr.tliquidacion
    add nota_siat json;

alter table decr.tliquidacion
add glosa_anulado text;


CREATE TABLE decr.tnota_siat (
                                                 id_nota_siat SERIAL,
                                                 id_liquidacion INTEGER,
                                                 nro_nota varchar,
                                                 nro_aut varchar,
                                                 CONSTRAINT pk_tnota_siat__id_nota_siat PRIMARY KEY(id_nota_siat)
) INHERITS (pxp.tbase)
  WITHOUT OIDS;



alter table decr.tnota_agencia alter column nro_aut_nota type varchar(255) using nro_aut_nota::varchar(255);


alter table decr.tliqui_manual_detalle
    add payment_key varchar(255);

comment on column decr.tliqui_manual_detalle.payment_key is 'esta columna hace referencia a la base de datos stage';


alter table decr.tnota_agencia alter column nroaut type varchar(255) using nroaut::varchar(255);

alter table decr.tnota_agencia alter column nrofac type varchar(255) using nrofac::varchar(255);

/***********************************F-SCP-FFP-DECR-1-15/04/2020****************************************/




