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

OPTIONS ( query 'SELECT pais,estacion,docmnt,nroliqui,fecha,estpago,estado,notaboa FROM liquidevolu',
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
ALTER TABLE decr.tdosificacion ALTER COLUMN glosa_impuestos SET NULL;
ALTER TABLE decr.tdosificacion ALTER COLUMN glosa_consumidor SET NULL;
ALTER TABLE decr.tdosificacion ALTER COLUMN glosa_empresa SET NULL;

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
) 

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

