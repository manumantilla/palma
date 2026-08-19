--
-- PostgreSQL database dump
--

-- Dumped from database version 15.8 (Debian 15.8-1.pgdg110+1)
-- Dumped by pg_dump version 15.8 (Debian 15.8-1.pgdg110+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: tiger; Type: SCHEMA; Schema: -; Owner: sail
--

CREATE SCHEMA tiger;


ALTER SCHEMA tiger OWNER TO sail;

--
-- Name: tiger_data; Type: SCHEMA; Schema: -; Owner: sail
--

CREATE SCHEMA tiger_data;


ALTER SCHEMA tiger_data OWNER TO sail;

--
-- Name: topology; Type: SCHEMA; Schema: -; Owner: sail
--

CREATE SCHEMA topology;


ALTER SCHEMA topology OWNER TO sail;

--
-- Name: SCHEMA topology; Type: COMMENT; Schema: -; Owner: sail
--

COMMENT ON SCHEMA topology IS 'PostGIS Topology schema';


--
-- Name: fuzzystrmatch; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS fuzzystrmatch WITH SCHEMA public;


--
-- Name: EXTENSION fuzzystrmatch; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION fuzzystrmatch IS 'determine similarities and distance between strings';


--
-- Name: postgis; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS postgis WITH SCHEMA public;


--
-- Name: EXTENSION postgis; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION postgis IS 'PostGIS geometry and geography spatial types and functions';


--
-- Name: postgis_tiger_geocoder; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS postgis_tiger_geocoder WITH SCHEMA tiger;


--
-- Name: EXTENSION postgis_tiger_geocoder; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION postgis_tiger_geocoder IS 'PostGIS tiger geocoder and reverse geocoder';


--
-- Name: postgis_topology; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS postgis_topology WITH SCHEMA topology;


--
-- Name: EXTENSION postgis_topology; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION postgis_topology IS 'PostGIS topology spatial types and functions';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: arboles; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.arboles (
    id bigint NOT NULL,
    ciclo_productivo_id bigint NOT NULL,
    lote_id bigint NOT NULL,
    lote_zona_manejo_id bigint,
    codigo_unico character varying(255) NOT NULL,
    fila_indice integer NOT NULL,
    posicion_indice integer NOT NULL,
    altitud numeric(8,2),
    estado_vital character varying(255) DEFAULT 'excelente'::character varying NOT NULL,
    etapa_biologica character varying(255) DEFAULT 'establecimiento'::character varying NOT NULL,
    fecha_baja_muerte date,
    motivo_baja character varying(255),
    coordenada_precision public.geometry(Geometry,4326),
    altitud_ortometrica_msnm numeric(6,2),
    fecha_siembra date,
    fecha_primera_cosecha date,
    variedad character varying(255),
    fecha_muerte date,
    causa_muerte character varying(255),
    es_reemplazo boolean DEFAULT false NOT NULL,
    fecha_reemplazo date,
    produccion_acumulada_kg numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    ciclos_productivos_count integer DEFAULT 0 NOT NULL,
    observaciones text,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT arboles_estado_vital_check CHECK (((estado_vital)::text = ANY ((ARRAY['excelente'::character varying, 'con_estres'::character varying, 'enfermo_critico'::character varying, 'muerto'::character varying, 'erradicado'::character varying])::text[]))),
    CONSTRAINT arboles_etapa_biologica_check CHECK (((etapa_biologica)::text = ANY ((ARRAY['vivero'::character varying, 'establecimiento'::character varying, 'desarrollo_inmaduro'::character varying, 'produccion_madura'::character varying, 'senescencia'::character varying])::text[])))
);


ALTER TABLE public.arboles OWNER TO sail;

--
-- Name: arboles_historial_fitosanitario; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.arboles_historial_fitosanitario (
    id bigint NOT NULL,
    arbol_id bigint NOT NULL,
    fecha_hallazgo timestamp(0) without time zone NOT NULL,
    tipo_incidencia character varying(255) NOT NULL,
    agente_patogeno_nombre character varying(150) NOT NULL,
    severidad_afectacion character varying(255) NOT NULL,
    descripcion_sintomas text NOT NULL,
    evidencia_fotografica_url character varying(255),
    usuario_evaluador_id bigint NOT NULL,
    requiere_intervencion_quimica boolean DEFAULT false NOT NULL,
    caso_controlado boolean DEFAULT false NOT NULL,
    fecha_resolucion timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT arboles_historial_fitosanitario_severidad_afectacion_check CHECK (((severidad_afectacion)::text = ANY ((ARRAY['leve'::character varying, 'moderada'::character varying, 'critica_cuarentena'::character varying])::text[]))),
    CONSTRAINT arboles_historial_fitosanitario_tipo_incidencia_check CHECK (((tipo_incidencia)::text = ANY ((ARRAY['plaga'::character varying, 'enfermedad'::character varying, 'deficiencia_nutricional'::character varying, 'dano_mecanico'::character varying])::text[])))
);


ALTER TABLE public.arboles_historial_fitosanitario OWNER TO sail;

--
-- Name: arboles_historial_fitosanitario_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.arboles_historial_fitosanitario_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.arboles_historial_fitosanitario_id_seq OWNER TO sail;

--
-- Name: arboles_historial_fitosanitario_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.arboles_historial_fitosanitario_id_seq OWNED BY public.arboles_historial_fitosanitario.id;


--
-- Name: arboles_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.arboles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.arboles_id_seq OWNER TO sail;

--
-- Name: arboles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.arboles_id_seq OWNED BY public.arboles.id;


--
-- Name: arboles_metricas_historicas; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.arboles_metricas_historicas (
    id bigint NOT NULL,
    arbol_id bigint NOT NULL,
    fecha_medicion timestamp(0) without time zone NOT NULL,
    altura_metros numeric(4,2),
    diametro_tronco_cm numeric(5,2),
    diametro_copa_proyeccion_m numeric(4,2),
    volumen_copa_calculado_m3 numeric(6,2),
    indice_ndvi_medido numeric(4,3),
    indice_ndre_medido numeric(4,3),
    temperatura_canopia_celsius numeric(4,2),
    codigo_escala_bbch integer,
    origen_datos character varying(255) DEFAULT 'manual'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.arboles_metricas_historicas OWNER TO sail;

--
-- Name: arboles_metricas_historicas_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.arboles_metricas_historicas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.arboles_metricas_historicas_id_seq OWNER TO sail;

--
-- Name: arboles_metricas_historicas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.arboles_metricas_historicas_id_seq OWNED BY public.arboles_metricas_historicas.id;


--
-- Name: bitacoras; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.bitacoras (
    id bigint NOT NULL,
    bitacorable_type character varying(255) NOT NULL,
    bitacorable_id bigint NOT NULL,
    tipo character varying(255) NOT NULL,
    prioridad character varying(255) DEFAULT 'baja'::character varying NOT NULL,
    titulo character varying(255),
    contenido text NOT NULL,
    estado character varying(255) DEFAULT 'abierto'::character varying NOT NULL,
    archivo_adjunto character varying(255),
    user_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT bitacoras_estado_check CHECK (((estado)::text = ANY ((ARRAY['abierto'::character varying, 'en_proceso'::character varying, 'resuelto'::character varying])::text[]))),
    CONSTRAINT bitacoras_prioridad_check CHECK (((prioridad)::text = ANY ((ARRAY['baja'::character varying, 'media'::character varying, 'alta'::character varying, 'critica'::character varying])::text[]))),
    CONSTRAINT bitacoras_tipo_check CHECK (((tipo)::text = ANY ((ARRAY['observacion'::character varying, 'alerta'::character varying, 'incidente'::character varying, 'decision'::character varying, 'condicion_clima'::character varying, 'visita_tecnica'::character varying])::text[])))
);


ALTER TABLE public.bitacoras OWNER TO sail;

--
-- Name: bitacoras_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.bitacoras_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.bitacoras_id_seq OWNER TO sail;

--
-- Name: bitacoras_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.bitacoras_id_seq OWNED BY public.bitacoras.id;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration bigint NOT NULL
);


ALTER TABLE public.cache OWNER TO sail;

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration bigint NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO sail;

--
-- Name: carta_porte; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.carta_porte (
    id bigint NOT NULL,
    despacho_id bigint NOT NULL,
    numero_carta_porte character varying(255),
    tipo_transportador character varying(255) NOT NULL,
    transportador_id bigint,
    nombre_conductor character varying(255) NOT NULL,
    cedula_conductor character varying(255) NOT NULL,
    telefono_conductor character varying(255),
    placa_vehiculo character varying(255) NOT NULL,
    tipo_vehiculo character varying(255),
    placa_trailer character varying(255),
    municipio_origen character varying(255) NOT NULL,
    departamento_origen character varying(255) NOT NULL,
    municipio_destino character varying(255) NOT NULL,
    departamento_destino character varying(255) NOT NULL,
    ruta_descripcion character varying(255),
    valor_flete numeric(12,2),
    quien_paga_flete character varying(255),
    forma_pago_flete character varying(255),
    peso_declarado_kg numeric(12,2) NOT NULL,
    descripcion_carga character varying(255) NOT NULL,
    numero_sello character varying(255),
    fecha_salida timestamp(0) without time zone NOT NULL,
    fecha_llegada_real timestamp(0) without time zone,
    observaciones text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT carta_porte_forma_pago_flete_check CHECK (((forma_pago_flete)::text = ANY ((ARRAY['contado'::character varying, 'credito'::character varying, 'descuento_liquidacion'::character varying])::text[]))),
    CONSTRAINT carta_porte_quien_paga_flete_check CHECK (((quien_paga_flete)::text = ANY ((ARRAY['productor'::character varying, 'comprador'::character varying, 'comisionista'::character varying])::text[]))),
    CONSTRAINT carta_porte_tipo_transportador_check CHECK (((tipo_transportador)::text = ANY ((ARRAY['propio'::character varying, 'tercero'::character varying, 'fletero'::character varying])::text[])))
);


ALTER TABLE public.carta_porte OWNER TO sail;

--
-- Name: carta_porte_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.carta_porte_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.carta_porte_id_seq OWNER TO sail;

--
-- Name: carta_porte_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.carta_porte_id_seq OWNED BY public.carta_porte.id;


--
-- Name: categorias_insumo; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.categorias_insumo (
    id bigint NOT NULL,
    nombre character varying(255) NOT NULL,
    maneja_vencimiento boolean DEFAULT false NOT NULL,
    maneja_toxicidad boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.categorias_insumo OWNER TO sail;

--
-- Name: categorias_insumo_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.categorias_insumo_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.categorias_insumo_id_seq OWNER TO sail;

--
-- Name: categorias_insumo_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.categorias_insumo_id_seq OWNED BY public.categorias_insumo.id;


--
-- Name: ciclos_productivos; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.ciclos_productivos (
    id bigint NOT NULL,
    lote_id bigint NOT NULL,
    cultivo_id bigint NOT NULL,
    estado character varying(255) DEFAULT 'preparacion_suelo'::character varying NOT NULL,
    tipo character varying(255) DEFAULT 'transitorio'::character varying NOT NULL,
    nombre_campana character varying(255) NOT NULL,
    fecha_inicio date NOT NULL,
    fecha_estimada_cosecha date NOT NULL,
    fecha_real_inicio_cosecha date,
    fecha_estimada_fin_cosecha date NOT NULL,
    fecha_real_fin_cosecha date,
    fecha_finalizacion_ciclo date,
    modalidad_siembra character varying(255) NOT NULL,
    distancia_entre_hileras_metros numeric(5,2),
    distancia_entre_plantas_metros numeric(5,2),
    plantas_por_hectarea_real numeric(8,2) NOT NULL,
    proveedor_material_vegetal_id bigint,
    codigo_lote_vivero_origen character varying(100),
    registro_autorizacion_institucional character varying(100),
    es_organico_certificado boolean DEFAULT false NOT NULL,
    agronomo_responsable_id bigint,
    costo_acumulado_directo numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    costo_acumulado_indirecto numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT ciclos_productivos_estado_check CHECK (((estado)::text = ANY ((ARRAY['preparacion_suelo'::character varying, 'siembra_establecimiento'::character varying, 'desarrollo_vegetativo'::character varying, 'floracion_llenado'::character varying, 'cosecha_activa'::character varying, 'receso_invernal_poda'::character varying, 'concluido'::character varying, 'siniestrado_perdida'::character varying])::text[]))),
    CONSTRAINT ciclos_productivos_modalidad_siembra_check CHECK (((modalidad_siembra)::text = ANY ((ARRAY['semilla_directa'::character varying, 'plantula_vivero'::character varying, 'estaca_esqueje'::character varying, 'arbol_injertado'::character varying])::text[]))),
    CONSTRAINT ciclos_productivos_tipo_check CHECK (((tipo)::text = ANY ((ARRAY['perenne'::character varying, 'transitorio'::character varying])::text[])))
);


ALTER TABLE public.ciclos_productivos OWNER TO sail;

--
-- Name: ciclos_productivos_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.ciclos_productivos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.ciclos_productivos_id_seq OWNER TO sail;

--
-- Name: ciclos_productivos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.ciclos_productivos_id_seq OWNED BY public.ciclos_productivos.id;


--
-- Name: clientes; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.clientes (
    id bigint NOT NULL,
    razon_social character varying(255) NOT NULL,
    nombre_comercial character varying(255),
    tipo_persona character varying(255) DEFAULT 'natural'::character varying NOT NULL,
    nit character varying(255) NOT NULL,
    dv character varying(1),
    contacto_nombre character varying(255),
    contacto_telefono character varying(255),
    contacto_email character varying(255),
    direccion_fiscal text NOT NULL,
    municipio text NOT NULL,
    cupo_credito numeric(16,2) DEFAULT '0'::numeric NOT NULL,
    saldo_actual numeric(16,2) DEFAULT '0'::numeric NOT NULL,
    dias_credito integer DEFAULT 0 NOT NULL,
    estado_cuenta character varying(255) DEFAULT 'activo'::character varying NOT NULL,
    cultivos_interes json,
    categoria_cliente character varying(255) DEFAULT 'minorista'::character varying NOT NULL,
    autoriza_factura_electronica boolean DEFAULT true NOT NULL,
    email_recepcion_facturas character varying(255),
    codigo_postal character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    CONSTRAINT clientes_categoria_cliente_check CHECK (((categoria_cliente)::text = ANY ((ARRAY['mayorista'::character varying, 'minorista'::character varying, 'exportacion'::character varying, 'industrial'::character varying])::text[]))),
    CONSTRAINT clientes_estado_cuenta_check CHECK (((estado_cuenta)::text = ANY ((ARRAY['activo'::character varying, 'suspendido'::character varying, 'mora'::character varying, 'castigado'::character varying])::text[]))),
    CONSTRAINT clientes_tipo_persona_check CHECK (((tipo_persona)::text = ANY ((ARRAY['natural'::character varying, 'juridica'::character varying])::text[])))
);


ALTER TABLE public.clientes OWNER TO sail;

--
-- Name: clientes_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.clientes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.clientes_id_seq OWNER TO sail;

--
-- Name: clientes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.clientes_id_seq OWNED BY public.clientes.id;


--
-- Name: componentes_riego; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.componentes_riego (
    id bigint NOT NULL,
    sistema_riego_id bigint NOT NULL,
    tipo_componente character varying(255) NOT NULL,
    nombre_identificador character varying(100) NOT NULL,
    diametro_pulgadas character varying(20),
    presion_trabajo_psi numeric(6,2),
    caudal_estimado_litros_minuto numeric(8,2),
    activo boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT componentes_riego_tipo_componente_check CHECK (((tipo_componente)::text = ANY ((ARRAY['manguera'::character varying, 'bomba'::character varying, 'valvula_paso'::character varying, 'valvula_solenoide'::character varying, 'filtro'::character varying, 'manometro'::character varying])::text[])))
);


ALTER TABLE public.componentes_riego OWNER TO sail;

--
-- Name: componentes_riego_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.componentes_riego_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.componentes_riego_id_seq OWNER TO sail;

--
-- Name: componentes_riego_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.componentes_riego_id_seq OWNED BY public.componentes_riego.id;


--
-- Name: compra_items; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.compra_items (
    id bigint NOT NULL,
    compra_id bigint NOT NULL,
    insumo_id bigint NOT NULL,
    cantidad numeric(12,2) NOT NULL,
    precio_unitario numeric(12,2) NOT NULL,
    subtotal numeric(12,2) NOT NULL,
    fecha_vencimiento date NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.compra_items OWNER TO sail;

--
-- Name: compra_items_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.compra_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.compra_items_id_seq OWNER TO sail;

--
-- Name: compra_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.compra_items_id_seq OWNED BY public.compra_items.id;


--
-- Name: compras; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.compras (
    id bigint NOT NULL,
    proveedor_id bigint NOT NULL,
    fecha timestamp(0) without time zone NOT NULL,
    tipo character varying(255) DEFAULT 'insumos'::character varying NOT NULL,
    ciclo_productivo_id bigint,
    estado character varying(255) DEFAULT 'borrador'::character varying NOT NULL,
    factura_pdf_path character varying(255),
    ciudad character varying(255),
    departamento character varying(255),
    ubicacion public.geometry(Point,4326),
    plazo_pago_dias integer DEFAULT 0 NOT NULL,
    descuento_pronto_pago numeric(5,2),
    numero_factura character varying(255) NOT NULL,
    subtotal numeric(12,2),
    descuento_total numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    iva_total numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    total numeric(12,2) NOT NULL,
    porcentaje_iva_general numeric(5,2),
    fecha_pedido timestamp(0) without time zone,
    estado_pago character varying(255) DEFAULT 'pendiente'::character varying NOT NULL,
    observaciones character varying(255),
    user_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT compras_estado_check CHECK (((estado)::text = ANY ((ARRAY['borrador'::character varying, 'confirmada'::character varying, 'enviada'::character varying, 'recibida_parcial'::character varying, 'recibida_total'::character varying, 'cancelada'::character varying])::text[]))),
    CONSTRAINT compras_estado_pago_check CHECK (((estado_pago)::text = ANY ((ARRAY['pendiente'::character varying, 'pagado'::character varying])::text[]))),
    CONSTRAINT compras_tipo_check CHECK (((tipo)::text = ANY ((ARRAY['insumos'::character varying, 'maquinaria'::character varying, 'servicios_tecnicos'::character varying, 'transporte'::character varying, 'empaque'::character varying, 'laboratorio'::character varying, 'consultoria'::character varying, 'otros'::character varying])::text[])))
);


ALTER TABLE public.compras OWNER TO sail;

--
-- Name: compras_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.compras_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.compras_id_seq OWNER TO sail;

--
-- Name: compras_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.compras_id_seq OWNED BY public.compras.id;


--
-- Name: compras_pagos; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.compras_pagos (
    id bigint NOT NULL,
    compra_id bigint NOT NULL,
    monto numeric(12,2) NOT NULL,
    fecha_pago date NOT NULL,
    metodo character varying(255) DEFAULT 'transferencia'::character varying NOT NULL,
    referencia_transaccion character varying(255),
    user_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT compras_pagos_metodo_check CHECK (((metodo)::text = ANY ((ARRAY['efectivo'::character varying, 'transferencia'::character varying, 'cheque'::character varying, 'tarjeta'::character varying])::text[])))
);


ALTER TABLE public.compras_pagos OWNER TO sail;

--
-- Name: compras_pagos_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.compras_pagos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.compras_pagos_id_seq OWNER TO sail;

--
-- Name: compras_pagos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.compras_pagos_id_seq OWNED BY public.compras_pagos.id;


--
-- Name: contenedores; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.contenedores (
    id uuid NOT NULL,
    sesion_id uuid NOT NULL,
    cliente_id bigint,
    orden_pedido_id bigint,
    estado character varying(255) DEFAULT 'abierta'::character varying NOT NULL,
    nombre character varying(255) NOT NULL,
    tipo_destino character varying(255) NOT NULL,
    variedad character varying(255),
    calidad character varying(255),
    calibre_talla character varying(255),
    kilos_acumulados numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    peso_tara numeric(8,2) DEFAULT '0'::numeric NOT NULL,
    peso_total numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    client_updated_at timestamp(0) without time zone,
    synced_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT contenedores_calibre_talla_check CHECK (((calibre_talla)::text = ANY ((ARRAY['pequeño'::character varying, 'mediano'::character varying, 'grande'::character varying, 'jumbo'::character varying])::text[]))),
    CONSTRAINT contenedores_calidad_check CHECK (((calidad)::text = ANY ((ARRAY['extra'::character varying, 'primera'::character varying, 'segunda'::character varying, 'industria'::character varying, 'descarte'::character varying])::text[]))),
    CONSTRAINT contenedores_estado_check CHECK (((estado)::text = ANY ((ARRAY['abierta'::character varying, 'cerrada'::character varying, 'despachada'::character varying])::text[]))),
    CONSTRAINT contenedores_tipo_destino_check CHECK (((tipo_destino)::text = ANY ((ARRAY['exportacion'::character varying, 'mercado_local'::character varying, 'industria'::character varying, 'consumo_interno'::character varying, 'descarte'::character varying])::text[])))
);


ALTER TABLE public.contenedores OWNER TO sail;

--
-- Name: cultivos; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.cultivos (
    id bigint NOT NULL,
    tipo character varying(255) NOT NULL,
    nombre_cultivo character varying(255) NOT NULL,
    descripcion character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT cultivos_tipo_check CHECK (((tipo)::text = ANY ((ARRAY['perenne'::character varying, 'transitorio'::character varying])::text[])))
);


ALTER TABLE public.cultivos OWNER TO sail;

--
-- Name: cultivos_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.cultivos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.cultivos_id_seq OWNER TO sail;

--
-- Name: cultivos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.cultivos_id_seq OWNED BY public.cultivos.id;


--
-- Name: despacho_items; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.despacho_items (
    id bigint NOT NULL,
    despacho_id bigint NOT NULL,
    contenedor_id uuid,
    ciclo_productivo_id bigint,
    variedad character varying(255),
    calidad character varying(255),
    tipo_empaque character varying(255) NOT NULL,
    cantidad_unidades integer NOT NULL,
    peso_promedio_unidad numeric(8,2),
    peso_bruto_total numeric(12,2) NOT NULL,
    tara_total numeric(8,2) DEFAULT '0'::numeric NOT NULL,
    peso_neto_total numeric(12,2) GENERATED ALWAYS AS ((peso_bruto_total - tara_total)) STORED NOT NULL,
    precio_unitario_kg numeric(10,2),
    precio_liquidado_kg numeric(10,2),
    descuento_kg numeric(8,2) DEFAULT '0'::numeric NOT NULL,
    motivo_descuento text,
    descripcion text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT despacho_items_calidad_check CHECK (((calidad)::text = ANY ((ARRAY['extra'::character varying, 'primera'::character varying, 'segunda'::character varying, 'industria'::character varying, 'sin_clasificar'::character varying])::text[]))),
    CONSTRAINT despacho_items_tipo_empaque_check CHECK (((tipo_empaque)::text = ANY ((ARRAY['bulto'::character varying, 'costal'::character varying, 'canastilla'::character varying, 'caja'::character varying, 'paca'::character varying, 'granel'::character varying])::text[])))
);


ALTER TABLE public.despacho_items OWNER TO sail;

--
-- Name: despacho_items_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.despacho_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.despacho_items_id_seq OWNER TO sail;

--
-- Name: despacho_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.despacho_items_id_seq OWNED BY public.despacho_items.id;


--
-- Name: despachos; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.despachos (
    id bigint NOT NULL,
    numero_remision character varying(255) NOT NULL,
    tipo_destino character varying(255) NOT NULL,
    nombre_destino character varying(255) NOT NULL,
    ciudad_destino character varying(255),
    departamento_destino character varying(255),
    cliente_id bigint,
    comisionista_id bigint,
    fecha_despacho timestamp(0) without time zone NOT NULL,
    fecha_estimada_llegada timestamp(0) without time zone,
    fecha_liquidado timestamp(0) without time zone,
    estado character varying(255) DEFAULT 'preparando'::character varying NOT NULL,
    modalidad_precio character varying(255) DEFAULT 'precio_mercado'::character varying NOT NULL,
    precio_referencia_kg numeric(10,2),
    observaciones text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT despachos_estado_check CHECK (((estado)::text = ANY ((ARRAY['preparando'::character varying, 'en_transito'::character varying, 'recibido'::character varying, 'liquidado'::character varying, 'novedad'::character varying])::text[]))),
    CONSTRAINT despachos_modalidad_precio_check CHECK (((modalidad_precio)::text = ANY ((ARRAY['precio_fijo'::character varying, 'precio_mercado'::character varying, 'precio_minimo'::character varying])::text[]))),
    CONSTRAINT despachos_tipo_destino_check CHECK (((tipo_destino)::text = ANY ((ARRAY['central_mayorista'::character varying, 'mercado_local'::character varying, 'venta_detal'::character varying, 'exportacion'::character varying, 'consignacion'::character varying])::text[])))
);


ALTER TABLE public.despachos OWNER TO sail;

--
-- Name: despachos_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.despachos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.despachos_id_seq OWNER TO sail;

--
-- Name: despachos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.despachos_id_seq OWNED BY public.despachos.id;


--
-- Name: evento_arbol; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.evento_arbol (
    id bigint NOT NULL,
    evento_campo_id bigint NOT NULL,
    arbol_id bigint NOT NULL,
    novedad_arbol character varying(255) DEFAULT 'ninguna'::character varying NOT NULL,
    nota_individual text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT evento_arbol_novedad_arbol_check CHECK (((novedad_arbol)::text = ANY ((ARRAY['ninguna'::character varying, 'enfermo'::character varying, 'muerto'::character varying, 'no_aplicó'::character varying, 'reemplazo'::character varying])::text[])))
);


ALTER TABLE public.evento_arbol OWNER TO sail;

--
-- Name: evento_arbol_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.evento_arbol_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.evento_arbol_id_seq OWNER TO sail;

--
-- Name: evento_arbol_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.evento_arbol_id_seq OWNED BY public.evento_arbol.id;


--
-- Name: evento_insumo_lotes; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.evento_insumo_lotes (
    id bigint NOT NULL,
    evento_insumo_id bigint NOT NULL,
    lote_insumo_id bigint NOT NULL,
    cantidad numeric(12,2) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.evento_insumo_lotes OWNER TO sail;

--
-- Name: evento_insumo_lotes_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.evento_insumo_lotes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.evento_insumo_lotes_id_seq OWNER TO sail;

--
-- Name: evento_insumo_lotes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.evento_insumo_lotes_id_seq OWNED BY public.evento_insumo_lotes.id;


--
-- Name: evento_insumos; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.evento_insumos (
    id bigint NOT NULL,
    evento_campo_id bigint NOT NULL,
    insumo_id bigint NOT NULL,
    cantidad numeric(12,2) NOT NULL,
    area_aplicada numeric(10,2),
    metodo_aplicacion character varying(255) DEFAULT 'terrestre'::character varying NOT NULL,
    unidad_medida character varying(255) NOT NULL,
    costo_total numeric(12,2),
    observaciones text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT evento_insumos_metodo_aplicacion_check CHECK (((metodo_aplicacion)::text = ANY ((ARRAY['terrestre'::character varying, 'foliar'::character varying, 'dron'::character varying, 'fertirriego'::character varying, 'drench'::character varying])::text[]))),
    CONSTRAINT evento_insumos_unidad_medida_check CHECK (((unidad_medida)::text = ANY ((ARRAY['kg'::character varying, 'litros'::character varying, 'unidades'::character varying])::text[])))
);


ALTER TABLE public.evento_insumos OWNER TO sail;

--
-- Name: evento_insumos_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.evento_insumos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.evento_insumos_id_seq OWNER TO sail;

--
-- Name: evento_insumos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.evento_insumos_id_seq OWNED BY public.evento_insumos.id;


--
-- Name: evento_mano_obra; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.evento_mano_obra (
    id bigint NOT NULL,
    evento_campo_id bigint NOT NULL,
    sesion_id uuid,
    ciclo_id bigint,
    tipo_labor character varying(255) NOT NULL,
    trabajador_id bigint,
    nombre_trabajador character varying(255),
    cantidad numeric(6,1) NOT NULL,
    valor_unitario numeric(10,2) NOT NULL,
    observaciones text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT evento_mano_obra_tipo_labor_check CHECK (((tipo_labor)::text = ANY ((ARRAY['jornal_dia_completo'::character varying, 'jornal_medio_dia'::character varying, 'hora_extra'::character varying, 'destajo'::character varying])::text[])))
);


ALTER TABLE public.evento_mano_obra OWNER TO sail;

--
-- Name: evento_mano_obra_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.evento_mano_obra_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.evento_mano_obra_id_seq OWNER TO sail;

--
-- Name: evento_mano_obra_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.evento_mano_obra_id_seq OWNED BY public.evento_mano_obra.id;


--
-- Name: evento_maquinaria; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.evento_maquinaria (
    id bigint NOT NULL,
    evento_id bigint NOT NULL,
    maquina_id bigint NOT NULL,
    trabajador_id bigint NOT NULL,
    estado character varying(255) DEFAULT 'planificada'::character varying NOT NULL,
    horometro_inicial numeric(12,2),
    horometro_final numeric(12,2),
    horas_trabajadas numeric(12,2),
    tipo_combustible character varying(255) NOT NULL,
    litros_consumidos numeric(12,2),
    costo_total numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    observaciones text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT evento_maquinaria_estado_check CHECK (((estado)::text = ANY ((ARRAY['planificada'::character varying, 'en_ejecucion'::character varying, 'terminada'::character varying, 'cancelada'::character varying])::text[]))),
    CONSTRAINT evento_maquinaria_tipo_combustible_check CHECK (((tipo_combustible)::text = ANY ((ARRAY['acpm'::character varying, 'gasolina'::character varying, 'mecanico'::character varying])::text[])))
);


ALTER TABLE public.evento_maquinaria OWNER TO sail;

--
-- Name: evento_maquinaria_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.evento_maquinaria_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.evento_maquinaria_id_seq OWNER TO sail;

--
-- Name: evento_maquinaria_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.evento_maquinaria_id_seq OWNED BY public.evento_maquinaria.id;


--
-- Name: evento_riego_componentes; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.evento_riego_componentes (
    id bigint NOT NULL,
    evento_riego_id bigint NOT NULL,
    componente_id bigint NOT NULL,
    estaba_activo boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.evento_riego_componentes OWNER TO sail;

--
-- Name: evento_riego_componentes_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.evento_riego_componentes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.evento_riego_componentes_id_seq OWNER TO sail;

--
-- Name: evento_riego_componentes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.evento_riego_componentes_id_seq OWNED BY public.evento_riego_componentes.id;


--
-- Name: eventos_campo; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.eventos_campo (
    id bigint NOT NULL,
    ciclo_productivo_id bigint,
    lote_id bigint,
    zona_id bigint,
    hora_inicio time(0) without time zone,
    hora_fin time(0) without time zone,
    tipo_evento_id bigint NOT NULL,
    fecha_programada timestamp(0) without time zone NOT NULL,
    fecha_ejecucion timestamp(0) without time zone,
    latitud numeric(10,8),
    longitud numeric(11,8),
    estado character varying(255) DEFAULT 'Pendiente'::character varying NOT NULL,
    observaciones text,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT eventos_campo_estado_check CHECK (((estado)::text = ANY ((ARRAY['Pendiente'::character varying, 'En Proceso'::character varying, 'Completado'::character varying, 'Cancelado'::character varying])::text[])))
);


ALTER TABLE public.eventos_campo OWNER TO sail;

--
-- Name: eventos_campo_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.eventos_campo_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.eventos_campo_id_seq OWNER TO sail;

--
-- Name: eventos_campo_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.eventos_campo_id_seq OWNED BY public.eventos_campo.id;


--
-- Name: eventos_riego; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.eventos_riego (
    id bigint NOT NULL,
    sistema_riego_id bigint NOT NULL,
    responsable_id bigint NOT NULL,
    fecha_hora_inicio timestamp(0) without time zone NOT NULL,
    fecha_hora_fin timestamp(0) without time zone,
    duracion_total_minutos integer,
    presion_promedio_psi numeric(6,2),
    caudal_estimado_litros_minuto numeric(8,2) NOT NULL,
    volumen_estimado_litros numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    estado character varying(255) DEFAULT 'en_progreso'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT eventos_riego_estado_check CHECK (((estado)::text = ANY ((ARRAY['en_progreso'::character varying, 'finalizado'::character varying, 'cancelado'::character varying])::text[])))
);


ALTER TABLE public.eventos_riego OWNER TO sail;

--
-- Name: eventos_riego_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.eventos_riego_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.eventos_riego_id_seq OWNER TO sail;

--
-- Name: eventos_riego_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.eventos_riego_id_seq OWNED BY public.eventos_riego.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO sail;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.failed_jobs_id_seq OWNER TO sail;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: fenologia_etapas; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.fenologia_etapas (
    id bigint NOT NULL,
    cultivo_id bigint NOT NULL,
    nombre character varying(255) NOT NULL,
    orden integer NOT NULL,
    duracion_dias_desde_inicio integer,
    duracion_dias_estimada integer NOT NULL,
    descripcion text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.fenologia_etapas OWNER TO sail;

--
-- Name: fenologia_etapas_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.fenologia_etapas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.fenologia_etapas_id_seq OWNER TO sail;

--
-- Name: fenologia_etapas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.fenologia_etapas_id_seq OWNED BY public.fenologia_etapas.id;


--
-- Name: fincas; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.fincas (
    id bigint NOT NULL,
    nombre character varying(255) NOT NULL,
    ubicacion character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.fincas OWNER TO sail;

--
-- Name: fincas_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.fincas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.fincas_id_seq OWNER TO sail;

--
-- Name: fincas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.fincas_id_seq OWNED BY public.fincas.id;


--
-- Name: gastos; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.gastos (
    id bigint NOT NULL,
    gastable_type character varying(255) NOT NULL,
    gastable_id bigint NOT NULL,
    ciclo_productivo_id bigint,
    categoria character varying(255) DEFAULT 'otros'::character varying NOT NULL,
    naturaleza character varying(255) DEFAULT 'gasto_operacional'::character varying NOT NULL,
    numero_soporte character varying(255),
    comprobante_archivo character varying(255),
    metodo_pago character varying(255),
    concepto character varying(255) NOT NULL,
    monto numeric(14,2) NOT NULL,
    fecha date NOT NULL,
    descripcion text,
    user_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT gastos_categoria_check CHECK (((categoria)::text = ANY ((ARRAY['insumos'::character varying, 'mano_obra'::character varying, 'maquinaria'::character varying, 'transporte'::character varying, 'servicios_publicos'::character varying, 'arriendos'::character varying, 'mantenimiento'::character varying, 'administrativos'::character varying, 'impuestos'::character varying, 'seguros'::character varying, 'otros'::character varying])::text[]))),
    CONSTRAINT gastos_metodo_pago_check CHECK (((metodo_pago)::text = ANY ((ARRAY['efectivo'::character varying, 'transferencia'::character varying, 'tarjeta'::character varying])::text[]))),
    CONSTRAINT gastos_naturaleza_check CHECK (((naturaleza)::text = ANY ((ARRAY['costo_produccion'::character varying, 'gasto_operacional'::character varying, 'gasto_financiero'::character varying, 'inversion'::character varying])::text[])))
);


ALTER TABLE public.gastos OWNER TO sail;

--
-- Name: gastos_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.gastos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.gastos_id_seq OWNER TO sail;

--
-- Name: gastos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.gastos_id_seq OWNED BY public.gastos.id;


--
-- Name: insumo_componentes; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.insumo_componentes (
    id bigint NOT NULL,
    insumo_id bigint NOT NULL,
    tipo_componente character varying(255) DEFAULT 'nutriente'::character varying NOT NULL,
    componente character varying(255) NOT NULL,
    unidad character varying(255) NOT NULL,
    concentracion numeric(8,4) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT insumo_componentes_tipo_componente_check CHECK (((tipo_componente)::text = ANY ((ARRAY['nutriente'::character varying, 'activo'::character varying, 'coadyuvante'::character varying, 'carga'::character varying, 'otros'::character varying])::text[])))
);


ALTER TABLE public.insumo_componentes OWNER TO sail;

--
-- Name: insumo_componentes_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.insumo_componentes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.insumo_componentes_id_seq OWNER TO sail;

--
-- Name: insumo_componentes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.insumo_componentes_id_seq OWNED BY public.insumo_componentes.id;


--
-- Name: insumos; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.insumos (
    id bigint NOT NULL,
    categoria_id bigint NOT NULL,
    proveedor_id bigint,
    nombre character varying(255) NOT NULL,
    ingrediente_principal character varying(255),
    unidad_base character varying(255) DEFAULT 'unidad'::character varying NOT NULL,
    factor_conversion numeric(8,4) DEFAULT '1000'::numeric NOT NULL,
    nivel_toxicidad character varying(255),
    estado character varying(255) DEFAULT 'activo'::character varying NOT NULL,
    rei_horas integer,
    phi_dias integer,
    clasificacion_toxicologica character varying(20),
    equipo_proteccion character varying(255),
    franja_color character varying(20),
    almacenamiento_temp_min character varying(10),
    almacenamiento_temp_max character varying(10),
    almacenamiento_humedad character varying(20),
    requiere_refrigeracion boolean DEFAULT false NOT NULL,
    sensible_luz boolean DEFAULT false NOT NULL,
    stock_minimo numeric(12,2),
    dias_aviso_vencimiento integer DEFAULT 30 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT insumos_estado_check CHECK (((estado)::text = ANY ((ARRAY['activo'::character varying, 'inactivo'::character varying])::text[]))),
    CONSTRAINT insumos_nivel_toxicidad_check CHECK (((nivel_toxicidad)::text = ANY ((ARRAY['bajo'::character varying, 'medio'::character varying, 'alto'::character varying])::text[]))),
    CONSTRAINT insumos_unidad_base_check CHECK (((unidad_base)::text = ANY ((ARRAY['kg'::character varying, 'l'::character varying, 'unidad'::character varying])::text[])))
);


ALTER TABLE public.insumos OWNER TO sail;

--
-- Name: insumos_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.insumos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.insumos_id_seq OWNER TO sail;

--
-- Name: insumos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.insumos_id_seq OWNED BY public.insumos.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO sail;

--
-- Name: jobs; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO sail;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.jobs_id_seq OWNER TO sail;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: labores_plantilla; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.labores_plantilla (
    id bigint NOT NULL,
    cultivo_id bigint NOT NULL,
    nombre_labor character varying(255) NOT NULL,
    descripcion text,
    momento_tipo character varying(255) NOT NULL,
    dias_desde_siembra integer,
    fenologia_etapa_id bigint,
    periodicidad_dias integer,
    duracion_estimada_horas integer,
    tipo_evento_id bigint NOT NULL,
    requiere_insumos boolean DEFAULT false NOT NULL,
    requiere_mano_obra boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT labores_plantilla_momento_tipo_check CHECK (((momento_tipo)::text = ANY ((ARRAY['dias_desde_siembra'::character varying, 'etapa_fenologica'::character varying, 'fecha_fija_anual'::character varying])::text[])))
);


ALTER TABLE public.labores_plantilla OWNER TO sail;

--
-- Name: labores_plantilla_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.labores_plantilla_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.labores_plantilla_id_seq OWNER TO sail;

--
-- Name: labores_plantilla_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.labores_plantilla_id_seq OWNED BY public.labores_plantilla.id;


--
-- Name: lecturas_sensores_tanque; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.lecturas_sensores_tanque (
    id bigint NOT NULL,
    tanque_id bigint NOT NULL,
    lectura_distancia_cm numeric(6,2),
    porcentaje_volumen numeric(5,2) NOT NULL,
    calculo_litros_actuales numeric(12,2) NOT NULL,
    fecha_hora_lectura timestamp(0) without time zone NOT NULL,
    dispositivo_mac character varying(50),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.lecturas_sensores_tanque OWNER TO sail;

--
-- Name: lecturas_sensores_tanque_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.lecturas_sensores_tanque_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.lecturas_sensores_tanque_id_seq OWNER TO sail;

--
-- Name: lecturas_sensores_tanque_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.lecturas_sensores_tanque_id_seq OWNED BY public.lecturas_sensores_tanque.id;


--
-- Name: liquidaciones_despacho; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.liquidaciones_despacho (
    id bigint NOT NULL,
    despacho_id bigint NOT NULL,
    despacho_item_id bigint NOT NULL,
    fecha_liquidacion timestamp(0) without time zone NOT NULL,
    numero_identificacion character varying(255),
    precio_unitario_kg numeric(12,2) NOT NULL,
    valor_bruto_venta numeric(12,2) NOT NULL,
    comision_porcentaje numeric(5,2) DEFAULT '0'::numeric NOT NULL,
    valor_comision numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    valor_flete_descontado numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    otros_descuentos numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    detalle_otros_descuentos text,
    valor_neto_item numeric(15,2) NOT NULL,
    estado_pago character varying(255) DEFAULT 'pendiente'::character varying NOT NULL,
    valor_pagado numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    saldo_pendiente numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    fecha_pago timestamp(0) without time zone,
    medio_pago character varying(255),
    observaciones text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT liquidaciones_despacho_estado_pago_check CHECK (((estado_pago)::text = ANY ((ARRAY['pendiente'::character varying, 'parcial'::character varying, 'pagado'::character varying])::text[]))),
    CONSTRAINT liquidaciones_despacho_medio_pago_check CHECK (((medio_pago)::text = ANY ((ARRAY['efectivo'::character varying, 'transferencia'::character varying, 'cheque'::character varying, 'otro'::character varying])::text[])))
);


ALTER TABLE public.liquidaciones_despacho OWNER TO sail;

--
-- Name: liquidaciones_despacho_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.liquidaciones_despacho_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.liquidaciones_despacho_id_seq OWNER TO sail;

--
-- Name: liquidaciones_despacho_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.liquidaciones_despacho_id_seq OWNED BY public.liquidaciones_despacho.id;


--
-- Name: lotes; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.lotes (
    id bigint NOT NULL,
    finca_id bigint NOT NULL,
    nombre_lote character varying(255) NOT NULL,
    codigo_lote character varying(255) NOT NULL,
    area_hectareas_declaradas numeric(8,2) NOT NULL,
    area_hectareas_gis numeric(10,4),
    altitud_mediana_msnm numeric(7,2) NOT NULL,
    pendiente_promedio_porcentaje numeric(5,2) NOT NULL,
    pendiente_terreno character varying(255) DEFAULT 'ondulado'::character varying NOT NULL,
    tipo_suelo character varying(255) NOT NULL,
    ph_suelo numeric(5,2) NOT NULL,
    tiene_riego_instalado boolean DEFAULT false NOT NULL,
    fuente_agua character varying(255),
    tenencia character varying(255) DEFAULT 'propio'::character varying NOT NULL,
    registro_ica character varying(100),
    geometria_gps public.geometry(MultiPolygon,4326),
    activo boolean DEFAULT true NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT lotes_fuente_agua_check CHECK (((fuente_agua)::text = ANY ((ARRAY['acueducto'::character varying, 'pozo'::character varying, 'rio'::character varying, 'nacimiento'::character varying, 'lluvia'::character varying])::text[]))),
    CONSTRAINT lotes_pendiente_terreno_check CHECK (((pendiente_terreno)::text = ANY ((ARRAY['plano'::character varying, 'ondulado'::character varying, 'escarpado'::character varying, 'muy_escarpado'::character varying])::text[]))),
    CONSTRAINT lotes_tenencia_check CHECK (((tenencia)::text = ANY ((ARRAY['propio'::character varying, 'arrendado'::character varying, 'comodato'::character varying])::text[]))),
    CONSTRAINT lotes_tipo_suelo_check CHECK (((tipo_suelo)::text = ANY ((ARRAY['arenoso'::character varying, 'arcilloso'::character varying, 'limoso'::character varying, 'franco'::character varying, 'franco_arenoso'::character varying, 'franco_arcilloso'::character varying])::text[])))
);


ALTER TABLE public.lotes OWNER TO sail;

--
-- Name: lotes_analiticas_suelo; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.lotes_analiticas_suelo (
    id bigint NOT NULL,
    lote_id bigint NOT NULL,
    fecha_muestreo date NOT NULL,
    numero_laboratorio_ticket character varying(50),
    ph numeric(4,2) NOT NULL,
    conductividad_electrica_ds_m numeric(6,3),
    materia_organica_porcentaje numeric(5,2),
    capacidad_intercambio_cationico_meq numeric(6,2),
    textura_predominante character varying(255) NOT NULL,
    porcentaje_arena numeric(5,2),
    porcentaje_limo numeric(5,2),
    porcentaje_arcilla numeric(5,2),
    analista_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT lotes_analiticas_suelo_textura_predominante_check CHECK (((textura_predominante)::text = ANY ((ARRAY['arenoso'::character varying, 'arenoso_franco'::character varying, 'franco_arenoso'::character varying, 'franco'::character varying, 'limoso'::character varying, 'franco_limoso'::character varying, 'franco_arcilloso_arenoso'::character varying, 'franco_arcilloso_limoso'::character varying, 'franco_arcilloso'::character varying, 'arcilloso_arenoso'::character varying, 'arcilloso_limoso'::character varying, 'arcilloso'::character varying])::text[])))
);


ALTER TABLE public.lotes_analiticas_suelo OWNER TO sail;

--
-- Name: lotes_analiticas_suelo_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.lotes_analiticas_suelo_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.lotes_analiticas_suelo_id_seq OWNER TO sail;

--
-- Name: lotes_analiticas_suelo_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.lotes_analiticas_suelo_id_seq OWNED BY public.lotes_analiticas_suelo.id;


--
-- Name: lotes_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.lotes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.lotes_id_seq OWNER TO sail;

--
-- Name: lotes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.lotes_id_seq OWNED BY public.lotes.id;


--
-- Name: lotes_insumos; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.lotes_insumos (
    id bigint NOT NULL,
    insumo_id bigint NOT NULL,
    proveedor_id bigint,
    codigo_lote character varying(255) NOT NULL,
    ubicacion_bodega character varying(255),
    fecha_vencimiento date NOT NULL,
    fecha_ingreso date NOT NULL,
    cantidad_inicial numeric(12,2) NOT NULL,
    cantidad_actual numeric(12,2) NOT NULL,
    unidad character varying(20) NOT NULL,
    costo_unitario numeric(12,4) NOT NULL,
    estado character varying(255) DEFAULT 'activo'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT lotes_insumos_estado_check CHECK (((estado)::text = ANY ((ARRAY['activo'::character varying, 'agotado'::character varying, 'vencido'::character varying, 'cuarentena'::character varying])::text[])))
);


ALTER TABLE public.lotes_insumos OWNER TO sail;

--
-- Name: lotes_insumos_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.lotes_insumos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.lotes_insumos_id_seq OWNER TO sail;

--
-- Name: lotes_insumos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.lotes_insumos_id_seq OWNED BY public.lotes_insumos.id;


--
-- Name: lotes_sistemas_riego; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.lotes_sistemas_riego (
    id bigint NOT NULL,
    lote_id bigint NOT NULL,
    nombre_sistema character varying(100) NOT NULL,
    tipo_riego character varying(255) NOT NULL,
    fuente_agua character varying(255) NOT NULL,
    caudal_diseno_litros_segundo numeric(8,2) NOT NULL,
    presion_operacion_psi numeric(6,2),
    coeficiente_uniformidad numeric(5,2),
    espaciamiento_emisores_metros numeric(4,2),
    descarga_emisor_litros_hora numeric(5,2),
    activo boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT lotes_sistemas_riego_fuente_agua_check CHECK (((fuente_agua)::text = ANY ((ARRAY['acueducto_distrito'::character varying, 'pozo_profundo'::character varying, 'rio_directo'::character varying, 'embalse_almacenamiento'::character varying, 'nacimiento'::character varying])::text[]))),
    CONSTRAINT lotes_sistemas_riego_tipo_riego_check CHECK (((tipo_riego)::text = ANY ((ARRAY['goteo'::character varying, 'microaspersion'::character varying, 'aspersion'::character varying, 'pivot_central'::character varying, 'gravedad'::character varying, 'subterraneo'::character varying])::text[])))
);


ALTER TABLE public.lotes_sistemas_riego OWNER TO sail;

--
-- Name: lotes_sistemas_riego_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.lotes_sistemas_riego_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.lotes_sistemas_riego_id_seq OWNER TO sail;

--
-- Name: lotes_sistemas_riego_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.lotes_sistemas_riego_id_seq OWNED BY public.lotes_sistemas_riego.id;


--
-- Name: lotes_zonas_manejo; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.lotes_zonas_manejo (
    id bigint NOT NULL,
    lote_id bigint NOT NULL,
    nombre_zona character varying(100) NOT NULL,
    codigo_zona character varying(50) NOT NULL,
    area_hectareas numeric(8,4) NOT NULL,
    geometria_zona public.geometry(Polygon,4326),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.lotes_zonas_manejo OWNER TO sail;

--
-- Name: lotes_zonas_manejo_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.lotes_zonas_manejo_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.lotes_zonas_manejo_id_seq OWNER TO sail;

--
-- Name: lotes_zonas_manejo_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.lotes_zonas_manejo_id_seq OWNED BY public.lotes_zonas_manejo.id;


--
-- Name: mantenimientos_maquinaria; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.mantenimientos_maquinaria (
    id bigint NOT NULL,
    maquina_id bigint NOT NULL,
    tipo_mantenimiento character varying(255) NOT NULL,
    fecha date NOT NULL,
    descripcion_trabajo text NOT NULL,
    costo_mano_obra_mecanico numeric(12,2) NOT NULL,
    costo_materiales numeric(12,2) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT mantenimientos_maquinaria_tipo_mantenimiento_check CHECK (((tipo_mantenimiento)::text = ANY ((ARRAY['preventivo'::character varying, 'correctivo'::character varying, 'calibracion'::character varying])::text[])))
);


ALTER TABLE public.mantenimientos_maquinaria OWNER TO sail;

--
-- Name: mantenimientos_maquinaria_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.mantenimientos_maquinaria_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.mantenimientos_maquinaria_id_seq OWNER TO sail;

--
-- Name: mantenimientos_maquinaria_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.mantenimientos_maquinaria_id_seq OWNED BY public.mantenimientos_maquinaria.id;


--
-- Name: maquinaria; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.maquinaria (
    id bigint NOT NULL,
    codigo_interno character varying(255) NOT NULL,
    nombre character varying(255) NOT NULL,
    tipo_maquinaria_id bigint NOT NULL,
    marca character varying(255) NOT NULL,
    modelo character varying(255),
    placa character varying(255),
    serial character varying(255),
    fecha_compra date,
    valor_compra numeric(15,2),
    vida_util_anios integer,
    fuente_energia character varying(255) NOT NULL,
    capacidad_tanque numeric(8,2),
    consumo_hora numeric(8,2),
    horometro_actual integer DEFAULT 0 NOT NULL,
    kilometraje integer,
    estado character varying(255) DEFAULT 'activo'::character varying NOT NULL,
    responsable_id bigint,
    observaciones text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    CONSTRAINT maquinaria_estado_check CHECK (((estado)::text = ANY ((ARRAY['activo'::character varying, 'mantenimiento'::character varying, 'averiado'::character varying, 'alquilado'::character varying, 'vendido'::character varying, 'retirado'::character varying])::text[]))),
    CONSTRAINT maquinaria_fuente_energia_check CHECK (((fuente_energia)::text = ANY ((ARRAY['diesel'::character varying, 'gasolina'::character varying, 'electrica'::character varying, 'manual'::character varying, 'hibrida'::character varying])::text[])))
);


ALTER TABLE public.maquinaria OWNER TO sail;

--
-- Name: maquinaria_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.maquinaria_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.maquinaria_id_seq OWNER TO sail;

--
-- Name: maquinaria_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.maquinaria_id_seq OWNED BY public.maquinaria.id;


--
-- Name: mermas; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.mermas (
    id uuid NOT NULL,
    recepcion_campo_id uuid,
    contenedor_id uuid,
    fecha_registro date NOT NULL,
    kilos_merma numeric(10,2) NOT NULL,
    motivo character varying(255) NOT NULL,
    costo_estimado numeric(10,2),
    destino_final character varying(255),
    comentarios text,
    client_updated_at timestamp(0) without time zone,
    synced_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT mermas_motivo_check CHECK (((motivo)::text = ANY ((ARRAY['daño'::character varying, 'perdida'::character varying, 'robo'::character varying, 'deshidratacion'::character varying, 'consumo_interno'::character varying, 'error'::character varying, 'otro'::character varying])::text[])))
);


ALTER TABLE public.mermas OWNER TO sail;

--
-- Name: migrations; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO sail;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.migrations_id_seq OWNER TO sail;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: movimientos_clasificacion; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.movimientos_clasificacion (
    id uuid NOT NULL,
    recepcion_campo_id uuid NOT NULL,
    contenedor_id uuid NOT NULL,
    kilos_asignados numeric(10,2) NOT NULL,
    observaciones text,
    client_updated_at timestamp(0) without time zone,
    synced_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.movimientos_clasificacion OWNER TO sail;

--
-- Name: movimientos_stock; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.movimientos_stock (
    id bigint NOT NULL,
    lote_insumo_id bigint NOT NULL,
    tipo_movimiento character varying(255) NOT NULL,
    cantidad numeric(12,2) NOT NULL,
    movimientoable_type character varying(255) NOT NULL,
    movimientoable_id bigint NOT NULL,
    stock_resultante numeric(12,2) NOT NULL,
    observacion character varying(255),
    user_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT movimientos_stock_tipo_movimiento_check CHECK (((tipo_movimiento)::text = ANY ((ARRAY['entrada_compra'::character varying, 'entrada_devolucion'::character varying, 'ajuste_entrada'::character varying, 'salida_aplicacion'::character varying, 'salida_devolucion_prov'::character varying, 'ajuste_salida_merma'::character varying, 'ajuste_salida_vencido'::character varying, 'ajuste_salida_hurto'::character varying])::text[])))
);


ALTER TABLE public.movimientos_stock OWNER TO sail;

--
-- Name: movimientos_stock_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.movimientos_stock_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.movimientos_stock_id_seq OWNER TO sail;

--
-- Name: movimientos_stock_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.movimientos_stock_id_seq OWNED BY public.movimientos_stock.id;


--
-- Name: ordenes_cosecha; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.ordenes_cosecha (
    id bigint NOT NULL,
    cliente_id bigint NOT NULL,
    ciclo_productivo_id bigint NOT NULL,
    lote_cultivo_id bigint,
    lote_zona_id bigint,
    fecha_programada date NOT NULL,
    fecha_entrega date NOT NULL,
    responsable_id bigint,
    cantidad_solicitada_kg numeric(12,2),
    variedad_requerida character varying(255),
    cantidad_planificada_kg numeric(12,2),
    cantidad_recolectada_kg numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    fecha_inicio date,
    fecha_fin date,
    estado character varying(255) DEFAULT 'borrador'::character varying NOT NULL,
    notas text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT ordenes_cosecha_estado_check CHECK (((estado)::text = ANY ((ARRAY['borrador'::character varying, 'confirmada'::character varying, 'en_proceso'::character varying, 'completada'::character varying, 'cancelada'::character varying])::text[])))
);


ALTER TABLE public.ordenes_cosecha OWNER TO sail;

--
-- Name: ordenes_cosecha_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.ordenes_cosecha_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.ordenes_cosecha_id_seq OWNER TO sail;

--
-- Name: ordenes_cosecha_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.ordenes_cosecha_id_seq OWNED BY public.ordenes_cosecha.id;


--
-- Name: pagos_liquidacion; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.pagos_liquidacion (
    id bigint NOT NULL,
    liquidacion_id bigint,
    despacho_id bigint NOT NULL,
    valor_pagado numeric(15,2) NOT NULL,
    fecha_pago timestamp(0) without time zone NOT NULL,
    medio_pago character varying(255) NOT NULL,
    referencia_pago character varying(255),
    banco_origen character varying(255),
    registrado_por bigint,
    observaciones text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT pagos_liquidacion_medio_pago_check CHECK (((medio_pago)::text = ANY ((ARRAY['efectivo'::character varying, 'transferencia'::character varying, 'cheque'::character varying, 'otro'::character varying])::text[])))
);


ALTER TABLE public.pagos_liquidacion OWNER TO sail;

--
-- Name: pagos_liquidacion_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.pagos_liquidacion_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.pagos_liquidacion_id_seq OWNER TO sail;

--
-- Name: pagos_liquidacion_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.pagos_liquidacion_id_seq OWNED BY public.pagos_liquidacion.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO sail;

--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name text NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.personal_access_tokens OWNER TO sail;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.personal_access_tokens_id_seq OWNER TO sail;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: proveedores; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.proveedores (
    id bigint NOT NULL,
    nombre character varying(255) NOT NULL,
    nit character varying(255) NOT NULL,
    telefono character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    direccion character varying(255) NOT NULL,
    tiene_credito boolean DEFAULT false NOT NULL,
    cuente_banco_1 character varying(255),
    cuente_banco_2 character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.proveedores OWNER TO sail;

--
-- Name: proveedores_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.proveedores_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.proveedores_id_seq OWNER TO sail;

--
-- Name: proveedores_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.proveedores_id_seq OWNED BY public.proveedores.id;


--
-- Name: recepciones_campo; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.recepciones_campo (
    id uuid NOT NULL,
    sesion_cosecha_id uuid NOT NULL,
    lote_zona_id bigint,
    trabajador_id bigint NOT NULL,
    arbol_id bigint,
    peso_bruto numeric(10,2) NOT NULL,
    tara_costal numeric(8,2) DEFAULT '0'::numeric NOT NULL,
    peso_neto numeric(10,2) NOT NULL,
    hora_pesaje timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    foto_evidencia character varying(255),
    costal_codigo character varying(255),
    numero_corte integer,
    estado_clasificacion character varying(255) DEFAULT 'pendiente'::character varying NOT NULL,
    client_updated_at timestamp(0) without time zone,
    synced_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT recepciones_campo_estado_clasificacion_check CHECK (((estado_clasificacion)::text = ANY ((ARRAY['pendiente'::character varying, 'en_proceso'::character varying, 'clasificado'::character varying])::text[])))
);


ALTER TABLE public.recepciones_campo OWNER TO sail;

--
-- Name: recepciones_destino; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.recepciones_destino (
    id bigint NOT NULL,
    despacho_id bigint NOT NULL,
    fecha_recepcion timestamp(0) without time zone NOT NULL,
    recibido_por character varying(255),
    peso_recibido_kg numeric(12,2) NOT NULL,
    merma_transito_kg numeric(12,2),
    estado_carga character varying(255) DEFAULT 'buena'::character varying NOT NULL,
    novedad_descripcion text,
    kg_rechazados numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    motivo_rechazo character varying(255),
    observaciones text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT recepciones_destino_estado_carga_check CHECK (((estado_carga)::text = ANY ((ARRAY['buena'::character varying, 'con_novedad'::character varying, 'rechazo_parcial'::character varying, 'rechazo_total'::character varying])::text[])))
);


ALTER TABLE public.recepciones_destino OWNER TO sail;

--
-- Name: recepciones_destino_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.recepciones_destino_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.recepciones_destino_id_seq OWNER TO sail;

--
-- Name: recepciones_destino_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.recepciones_destino_id_seq OWNED BY public.recepciones_destino.id;


--
-- Name: sesiones_cosecha; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.sesiones_cosecha (
    id uuid NOT NULL,
    orden_cosecha_id bigint NOT NULL,
    evento_campo_id bigint NOT NULL,
    responsable_id bigint NOT NULL,
    fecha date NOT NULL,
    estado character varying(255) DEFAULT 'abierta'::character varying NOT NULL,
    meta_kg_dia numeric(10,2),
    numero_recolectores integer,
    hora_inicio time(0) without time zone,
    hora_fin time(0) without time zone,
    total_recolectado_kg numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    client_updated_at timestamp(0) without time zone,
    synced_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT sesiones_cosecha_estado_check CHECK (((estado)::text = ANY ((ARRAY['abierta'::character varying, 'cerrada'::character varying])::text[])))
);


ALTER TABLE public.sesiones_cosecha OWNER TO sail;

--
-- Name: sessions; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO sail;

--
-- Name: sistemas_riego; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.sistemas_riego (
    id bigint NOT NULL,
    tanque_id bigint NOT NULL,
    lote_id bigint,
    nombre_sistema character varying(100) NOT NULL,
    tipo_riego character varying(255) NOT NULL,
    activo boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT sistemas_riego_tipo_riego_check CHECK (((tipo_riego)::text = ANY ((ARRAY['goteo'::character varying, 'microaspersion'::character varying, 'aspersion'::character varying, 'pivot_central'::character varying, 'gravedad'::character varying, 'subterraneo'::character varying])::text[])))
);


ALTER TABLE public.sistemas_riego OWNER TO sail;

--
-- Name: sistemas_riego_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.sistemas_riego_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sistemas_riego_id_seq OWNER TO sail;

--
-- Name: sistemas_riego_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.sistemas_riego_id_seq OWNED BY public.sistemas_riego.id;


--
-- Name: stock_insumos; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.stock_insumos (
    id bigint NOT NULL,
    insumo_id bigint NOT NULL,
    cantidad_disponible numeric(12,2) NOT NULL,
    unidad_base character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.stock_insumos OWNER TO sail;

--
-- Name: stock_insumos_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.stock_insumos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.stock_insumos_id_seq OWNER TO sail;

--
-- Name: stock_insumos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.stock_insumos_id_seq OWNED BY public.stock_insumos.id;


--
-- Name: tanques; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.tanques (
    id bigint NOT NULL,
    nombre character varying(255) NOT NULL,
    capacidad_litros numeric(15,2) NOT NULL,
    lote_id bigint,
    nivel_actual_litros numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    tiene_sensor_iot boolean DEFAULT false NOT NULL,
    activo boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.tanques OWNER TO sail;

--
-- Name: tanques_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.tanques_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tanques_id_seq OWNER TO sail;

--
-- Name: tanques_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.tanques_id_seq OWNED BY public.tanques.id;


--
-- Name: team_invitations; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.team_invitations (
    id bigint NOT NULL,
    team_id bigint NOT NULL,
    email character varying(255) NOT NULL,
    role character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.team_invitations OWNER TO sail;

--
-- Name: team_invitations_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.team_invitations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.team_invitations_id_seq OWNER TO sail;

--
-- Name: team_invitations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.team_invitations_id_seq OWNED BY public.team_invitations.id;


--
-- Name: team_user; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.team_user (
    id bigint NOT NULL,
    team_id bigint NOT NULL,
    user_id bigint NOT NULL,
    role character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.team_user OWNER TO sail;

--
-- Name: team_user_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.team_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.team_user_id_seq OWNER TO sail;

--
-- Name: team_user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.team_user_id_seq OWNED BY public.team_user.id;


--
-- Name: teams; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.teams (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    personal_team boolean NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.teams OWNER TO sail;

--
-- Name: teams_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.teams_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.teams_id_seq OWNER TO sail;

--
-- Name: teams_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.teams_id_seq OWNED BY public.teams.id;


--
-- Name: tipo_maquinaria; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.tipo_maquinaria (
    id bigint NOT NULL,
    nombre character varying(255) NOT NULL,
    descripcion character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.tipo_maquinaria OWNER TO sail;

--
-- Name: tipo_maquinaria_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.tipo_maquinaria_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tipo_maquinaria_id_seq OWNER TO sail;

--
-- Name: tipo_maquinaria_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.tipo_maquinaria_id_seq OWNED BY public.tipo_maquinaria.id;


--
-- Name: tipos_evento; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.tipos_evento (
    id bigint NOT NULL,
    nombre character varying(255) NOT NULL,
    categoria character varying(255) NOT NULL,
    consume_insumos boolean DEFAULT false NOT NULL,
    consume_mano_obra boolean DEFAULT false NOT NULL,
    genera_ingreso boolean DEFAULT false NOT NULL,
    genera_movimiento_stock boolean DEFAULT false NOT NULL,
    requiere_area_ha boolean DEFAULT false NOT NULL,
    aplica_a_arbol boolean DEFAULT false NOT NULL,
    aplica_a_ciclo boolean DEFAULT true NOT NULL,
    periodo_reingreso_horas smallint,
    periodo_carencia_dias smallint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT tipos_evento_categoria_check CHECK (((categoria)::text = ANY ((ARRAY['Mantenimiento'::character varying, 'Fitosanitario'::character varying, 'Cosecha'::character varying, 'Fertilización'::character varying, 'Logística'::character varying])::text[])))
);


ALTER TABLE public.tipos_evento OWNER TO sail;

--
-- Name: tipos_evento_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.tipos_evento_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tipos_evento_id_seq OWNER TO sail;

--
-- Name: tipos_evento_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.tipos_evento_id_seq OWNED BY public.tipos_evento.id;


--
-- Name: trabajadores; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.trabajadores (
    id bigint NOT NULL,
    user_id bigint,
    tipo_documento character varying(255) DEFAULT 'CC'::character varying NOT NULL,
    numero_documento character varying(255) NOT NULL,
    nombres character varying(255) NOT NULL,
    apellidos character varying(255) NOT NULL,
    fecha_nacimiento date,
    genero character varying(255),
    cargo character varying(255) NOT NULL,
    fecha_ingreso date NOT NULL,
    fecha_retiro date,
    tipo_contrato character varying(255) NOT NULL,
    salario_base numeric(14,2),
    forma_pago character varying(255) DEFAULT 'jornal'::character varying NOT NULL,
    banco_numero_cuenta text,
    eps character varying(255),
    arl character varying(255),
    afp character varying(255),
    habilidades json,
    ubicacion_actual public.geography(Point,4326),
    ultima_ubicacion_at timestamp(0) without time zone,
    activo boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT trabajadores_forma_pago_check CHECK (((forma_pago)::text = ANY ((ARRAY['jornal'::character varying, 'destajo'::character varying, 'mixto'::character varying])::text[]))),
    CONSTRAINT trabajadores_genero_check CHECK (((genero)::text = ANY ((ARRAY['M'::character varying, 'F'::character varying, 'Otro'::character varying])::text[]))),
    CONSTRAINT trabajadores_tipo_contrato_check CHECK (((tipo_contrato)::text = ANY ((ARRAY['indefinido'::character varying, 'fijo'::character varying, 'por_labores'::character varying, 'aprendizaje'::character varying])::text[]))),
    CONSTRAINT trabajadores_tipo_documento_check CHECK (((tipo_documento)::text = ANY ((ARRAY['CC'::character varying, 'CE'::character varying, 'NIT'::character varying, 'PPT'::character varying])::text[])))
);


ALTER TABLE public.trabajadores OWNER TO sail;

--
-- Name: trabajadores_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.trabajadores_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.trabajadores_id_seq OWNER TO sail;

--
-- Name: trabajadores_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.trabajadores_id_seq OWNED BY public.trabajadores.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    current_team_id bigint,
    profile_photo_path character varying(2048),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    two_factor_secret text,
    two_factor_recovery_codes text,
    two_factor_confirmed_at timestamp(0) without time zone
);


ALTER TABLE public.users OWNER TO sail;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO sail;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: validaciones_riego; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.validaciones_riego (
    id bigint NOT NULL,
    evento_riego_id bigint NOT NULL,
    tanque_id bigint NOT NULL,
    nivel_tanque_antes_litros numeric(12,2) NOT NULL,
    nivel_tanque_despues_litros numeric(12,2) NOT NULL,
    volumen_real_consumido_litros numeric(12,2) NOT NULL,
    volumen_estimado_litros numeric(12,2) NOT NULL,
    diferencia_litros numeric(12,2) NOT NULL,
    porcentaje_error numeric(5,2) NOT NULL,
    observaciones_auditoria text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.validaciones_riego OWNER TO sail;

--
-- Name: validaciones_riego_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.validaciones_riego_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.validaciones_riego_id_seq OWNER TO sail;

--
-- Name: validaciones_riego_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.validaciones_riego_id_seq OWNED BY public.validaciones_riego.id;


--
-- Name: arboles id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.arboles ALTER COLUMN id SET DEFAULT nextval('public.arboles_id_seq'::regclass);


--
-- Name: arboles_historial_fitosanitario id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.arboles_historial_fitosanitario ALTER COLUMN id SET DEFAULT nextval('public.arboles_historial_fitosanitario_id_seq'::regclass);


--
-- Name: arboles_metricas_historicas id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.arboles_metricas_historicas ALTER COLUMN id SET DEFAULT nextval('public.arboles_metricas_historicas_id_seq'::regclass);


--
-- Name: bitacoras id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.bitacoras ALTER COLUMN id SET DEFAULT nextval('public.bitacoras_id_seq'::regclass);


--
-- Name: carta_porte id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.carta_porte ALTER COLUMN id SET DEFAULT nextval('public.carta_porte_id_seq'::regclass);


--
-- Name: categorias_insumo id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.categorias_insumo ALTER COLUMN id SET DEFAULT nextval('public.categorias_insumo_id_seq'::regclass);


--
-- Name: ciclos_productivos id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.ciclos_productivos ALTER COLUMN id SET DEFAULT nextval('public.ciclos_productivos_id_seq'::regclass);


--
-- Name: clientes id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.clientes ALTER COLUMN id SET DEFAULT nextval('public.clientes_id_seq'::regclass);


--
-- Name: componentes_riego id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.componentes_riego ALTER COLUMN id SET DEFAULT nextval('public.componentes_riego_id_seq'::regclass);


--
-- Name: compra_items id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.compra_items ALTER COLUMN id SET DEFAULT nextval('public.compra_items_id_seq'::regclass);


--
-- Name: compras id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.compras ALTER COLUMN id SET DEFAULT nextval('public.compras_id_seq'::regclass);


--
-- Name: compras_pagos id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.compras_pagos ALTER COLUMN id SET DEFAULT nextval('public.compras_pagos_id_seq'::regclass);


--
-- Name: cultivos id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.cultivos ALTER COLUMN id SET DEFAULT nextval('public.cultivos_id_seq'::regclass);


--
-- Name: despacho_items id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.despacho_items ALTER COLUMN id SET DEFAULT nextval('public.despacho_items_id_seq'::regclass);


--
-- Name: despachos id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.despachos ALTER COLUMN id SET DEFAULT nextval('public.despachos_id_seq'::regclass);


--
-- Name: evento_arbol id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_arbol ALTER COLUMN id SET DEFAULT nextval('public.evento_arbol_id_seq'::regclass);


--
-- Name: evento_insumo_lotes id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_insumo_lotes ALTER COLUMN id SET DEFAULT nextval('public.evento_insumo_lotes_id_seq'::regclass);


--
-- Name: evento_insumos id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_insumos ALTER COLUMN id SET DEFAULT nextval('public.evento_insumos_id_seq'::regclass);


--
-- Name: evento_mano_obra id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_mano_obra ALTER COLUMN id SET DEFAULT nextval('public.evento_mano_obra_id_seq'::regclass);


--
-- Name: evento_maquinaria id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_maquinaria ALTER COLUMN id SET DEFAULT nextval('public.evento_maquinaria_id_seq'::regclass);


--
-- Name: evento_riego_componentes id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_riego_componentes ALTER COLUMN id SET DEFAULT nextval('public.evento_riego_componentes_id_seq'::regclass);


--
-- Name: eventos_campo id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.eventos_campo ALTER COLUMN id SET DEFAULT nextval('public.eventos_campo_id_seq'::regclass);


--
-- Name: eventos_riego id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.eventos_riego ALTER COLUMN id SET DEFAULT nextval('public.eventos_riego_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: fenologia_etapas id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.fenologia_etapas ALTER COLUMN id SET DEFAULT nextval('public.fenologia_etapas_id_seq'::regclass);


--
-- Name: fincas id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.fincas ALTER COLUMN id SET DEFAULT nextval('public.fincas_id_seq'::regclass);


--
-- Name: gastos id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.gastos ALTER COLUMN id SET DEFAULT nextval('public.gastos_id_seq'::regclass);


--
-- Name: insumo_componentes id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.insumo_componentes ALTER COLUMN id SET DEFAULT nextval('public.insumo_componentes_id_seq'::regclass);


--
-- Name: insumos id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.insumos ALTER COLUMN id SET DEFAULT nextval('public.insumos_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: labores_plantilla id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.labores_plantilla ALTER COLUMN id SET DEFAULT nextval('public.labores_plantilla_id_seq'::regclass);


--
-- Name: lecturas_sensores_tanque id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.lecturas_sensores_tanque ALTER COLUMN id SET DEFAULT nextval('public.lecturas_sensores_tanque_id_seq'::regclass);


--
-- Name: liquidaciones_despacho id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.liquidaciones_despacho ALTER COLUMN id SET DEFAULT nextval('public.liquidaciones_despacho_id_seq'::regclass);


--
-- Name: lotes id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.lotes ALTER COLUMN id SET DEFAULT nextval('public.lotes_id_seq'::regclass);


--
-- Name: lotes_analiticas_suelo id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.lotes_analiticas_suelo ALTER COLUMN id SET DEFAULT nextval('public.lotes_analiticas_suelo_id_seq'::regclass);


--
-- Name: lotes_insumos id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.lotes_insumos ALTER COLUMN id SET DEFAULT nextval('public.lotes_insumos_id_seq'::regclass);


--
-- Name: lotes_sistemas_riego id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.lotes_sistemas_riego ALTER COLUMN id SET DEFAULT nextval('public.lotes_sistemas_riego_id_seq'::regclass);


--
-- Name: lotes_zonas_manejo id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.lotes_zonas_manejo ALTER COLUMN id SET DEFAULT nextval('public.lotes_zonas_manejo_id_seq'::regclass);


--
-- Name: mantenimientos_maquinaria id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.mantenimientos_maquinaria ALTER COLUMN id SET DEFAULT nextval('public.mantenimientos_maquinaria_id_seq'::regclass);


--
-- Name: maquinaria id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.maquinaria ALTER COLUMN id SET DEFAULT nextval('public.maquinaria_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: movimientos_stock id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.movimientos_stock ALTER COLUMN id SET DEFAULT nextval('public.movimientos_stock_id_seq'::regclass);


--
-- Name: ordenes_cosecha id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.ordenes_cosecha ALTER COLUMN id SET DEFAULT nextval('public.ordenes_cosecha_id_seq'::regclass);


--
-- Name: pagos_liquidacion id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.pagos_liquidacion ALTER COLUMN id SET DEFAULT nextval('public.pagos_liquidacion_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Name: proveedores id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.proveedores ALTER COLUMN id SET DEFAULT nextval('public.proveedores_id_seq'::regclass);


--
-- Name: recepciones_destino id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.recepciones_destino ALTER COLUMN id SET DEFAULT nextval('public.recepciones_destino_id_seq'::regclass);


--
-- Name: sistemas_riego id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.sistemas_riego ALTER COLUMN id SET DEFAULT nextval('public.sistemas_riego_id_seq'::regclass);


--
-- Name: stock_insumos id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.stock_insumos ALTER COLUMN id SET DEFAULT nextval('public.stock_insumos_id_seq'::regclass);


--
-- Name: tanques id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.tanques ALTER COLUMN id SET DEFAULT nextval('public.tanques_id_seq'::regclass);


--
-- Name: team_invitations id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.team_invitations ALTER COLUMN id SET DEFAULT nextval('public.team_invitations_id_seq'::regclass);


--
-- Name: team_user id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.team_user ALTER COLUMN id SET DEFAULT nextval('public.team_user_id_seq'::regclass);


--
-- Name: teams id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.teams ALTER COLUMN id SET DEFAULT nextval('public.teams_id_seq'::regclass);


--
-- Name: tipo_maquinaria id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.tipo_maquinaria ALTER COLUMN id SET DEFAULT nextval('public.tipo_maquinaria_id_seq'::regclass);


--
-- Name: tipos_evento id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.tipos_evento ALTER COLUMN id SET DEFAULT nextval('public.tipos_evento_id_seq'::regclass);


--
-- Name: trabajadores id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.trabajadores ALTER COLUMN id SET DEFAULT nextval('public.trabajadores_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: validaciones_riego id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.validaciones_riego ALTER COLUMN id SET DEFAULT nextval('public.validaciones_riego_id_seq'::regclass);


--
-- Name: arboles arboles_codigo_unico_unique; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.arboles
    ADD CONSTRAINT arboles_codigo_unico_unique UNIQUE (codigo_unico);


--
-- Name: arboles_historial_fitosanitario arboles_historial_fitosanitario_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.arboles_historial_fitosanitario
    ADD CONSTRAINT arboles_historial_fitosanitario_pkey PRIMARY KEY (id);


--
-- Name: arboles_metricas_historicas arboles_metricas_historicas_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.arboles_metricas_historicas
    ADD CONSTRAINT arboles_metricas_historicas_pkey PRIMARY KEY (id);


--
-- Name: arboles arboles_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.arboles
    ADD CONSTRAINT arboles_pkey PRIMARY KEY (id);


--
-- Name: bitacoras bitacoras_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.bitacoras
    ADD CONSTRAINT bitacoras_pkey PRIMARY KEY (id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: carta_porte carta_porte_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.carta_porte
    ADD CONSTRAINT carta_porte_pkey PRIMARY KEY (id);


--
-- Name: categorias_insumo categorias_insumo_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.categorias_insumo
    ADD CONSTRAINT categorias_insumo_pkey PRIMARY KEY (id);


--
-- Name: ciclos_productivos ciclos_productivos_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.ciclos_productivos
    ADD CONSTRAINT ciclos_productivos_pkey PRIMARY KEY (id);


--
-- Name: clientes clientes_nit_unique; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.clientes
    ADD CONSTRAINT clientes_nit_unique UNIQUE (nit);


--
-- Name: clientes clientes_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.clientes
    ADD CONSTRAINT clientes_pkey PRIMARY KEY (id);


--
-- Name: componentes_riego componentes_riego_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.componentes_riego
    ADD CONSTRAINT componentes_riego_pkey PRIMARY KEY (id);


--
-- Name: compra_items compra_items_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.compra_items
    ADD CONSTRAINT compra_items_pkey PRIMARY KEY (id);


--
-- Name: compras_pagos compras_pagos_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.compras_pagos
    ADD CONSTRAINT compras_pagos_pkey PRIMARY KEY (id);


--
-- Name: compras compras_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.compras
    ADD CONSTRAINT compras_pkey PRIMARY KEY (id);


--
-- Name: contenedores contenedores_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.contenedores
    ADD CONSTRAINT contenedores_pkey PRIMARY KEY (id);


--
-- Name: cultivos cultivos_nombre_cultivo_unique; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.cultivos
    ADD CONSTRAINT cultivos_nombre_cultivo_unique UNIQUE (nombre_cultivo);


--
-- Name: cultivos cultivos_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.cultivos
    ADD CONSTRAINT cultivos_pkey PRIMARY KEY (id);


--
-- Name: despacho_items despacho_items_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.despacho_items
    ADD CONSTRAINT despacho_items_pkey PRIMARY KEY (id);


--
-- Name: despachos despachos_numero_remision_unique; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.despachos
    ADD CONSTRAINT despachos_numero_remision_unique UNIQUE (numero_remision);


--
-- Name: despachos despachos_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.despachos
    ADD CONSTRAINT despachos_pkey PRIMARY KEY (id);


--
-- Name: evento_arbol evento_arbol_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_arbol
    ADD CONSTRAINT evento_arbol_pkey PRIMARY KEY (id);


--
-- Name: evento_insumo_lotes evento_insumo_lotes_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_insumo_lotes
    ADD CONSTRAINT evento_insumo_lotes_pkey PRIMARY KEY (id);


--
-- Name: evento_insumos evento_insumos_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_insumos
    ADD CONSTRAINT evento_insumos_pkey PRIMARY KEY (id);


--
-- Name: evento_mano_obra evento_mano_obra_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_mano_obra
    ADD CONSTRAINT evento_mano_obra_pkey PRIMARY KEY (id);


--
-- Name: evento_maquinaria evento_maquinaria_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_maquinaria
    ADD CONSTRAINT evento_maquinaria_pkey PRIMARY KEY (id);


--
-- Name: evento_riego_componentes evento_riego_componentes_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_riego_componentes
    ADD CONSTRAINT evento_riego_componentes_pkey PRIMARY KEY (id);


--
-- Name: eventos_campo eventos_campo_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.eventos_campo
    ADD CONSTRAINT eventos_campo_pkey PRIMARY KEY (id);


--
-- Name: eventos_riego eventos_riego_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.eventos_riego
    ADD CONSTRAINT eventos_riego_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: fenologia_etapas fenologia_etapas_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.fenologia_etapas
    ADD CONSTRAINT fenologia_etapas_pkey PRIMARY KEY (id);


--
-- Name: fincas fincas_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.fincas
    ADD CONSTRAINT fincas_pkey PRIMARY KEY (id);


--
-- Name: gastos gastos_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.gastos
    ADD CONSTRAINT gastos_pkey PRIMARY KEY (id);


--
-- Name: insumo_componentes insumo_componentes_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.insumo_componentes
    ADD CONSTRAINT insumo_componentes_pkey PRIMARY KEY (id);


--
-- Name: insumos insumos_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.insumos
    ADD CONSTRAINT insumos_pkey PRIMARY KEY (id);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: labores_plantilla labores_plantilla_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.labores_plantilla
    ADD CONSTRAINT labores_plantilla_pkey PRIMARY KEY (id);


--
-- Name: lecturas_sensores_tanque lecturas_sensores_tanque_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.lecturas_sensores_tanque
    ADD CONSTRAINT lecturas_sensores_tanque_pkey PRIMARY KEY (id);


--
-- Name: liquidaciones_despacho liquidaciones_despacho_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.liquidaciones_despacho
    ADD CONSTRAINT liquidaciones_despacho_pkey PRIMARY KEY (id);


--
-- Name: lotes_analiticas_suelo lotes_analiticas_suelo_lote_id_fecha_muestreo_unique; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.lotes_analiticas_suelo
    ADD CONSTRAINT lotes_analiticas_suelo_lote_id_fecha_muestreo_unique UNIQUE (lote_id, fecha_muestreo);


--
-- Name: lotes_analiticas_suelo lotes_analiticas_suelo_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.lotes_analiticas_suelo
    ADD CONSTRAINT lotes_analiticas_suelo_pkey PRIMARY KEY (id);


--
-- Name: lotes lotes_codigo_lote_unique; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.lotes
    ADD CONSTRAINT lotes_codigo_lote_unique UNIQUE (codigo_lote);


--
-- Name: lotes_insumos lotes_insumos_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.lotes_insumos
    ADD CONSTRAINT lotes_insumos_pkey PRIMARY KEY (id);


--
-- Name: lotes lotes_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.lotes
    ADD CONSTRAINT lotes_pkey PRIMARY KEY (id);


--
-- Name: lotes_sistemas_riego lotes_sistemas_riego_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.lotes_sistemas_riego
    ADD CONSTRAINT lotes_sistemas_riego_pkey PRIMARY KEY (id);


--
-- Name: lotes_zonas_manejo lotes_zonas_manejo_codigo_zona_unique; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.lotes_zonas_manejo
    ADD CONSTRAINT lotes_zonas_manejo_codigo_zona_unique UNIQUE (codigo_zona);


--
-- Name: lotes_zonas_manejo lotes_zonas_manejo_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.lotes_zonas_manejo
    ADD CONSTRAINT lotes_zonas_manejo_pkey PRIMARY KEY (id);


--
-- Name: mantenimientos_maquinaria mantenimientos_maquinaria_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.mantenimientos_maquinaria
    ADD CONSTRAINT mantenimientos_maquinaria_pkey PRIMARY KEY (id);


--
-- Name: maquinaria maquinaria_codigo_interno_unique; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.maquinaria
    ADD CONSTRAINT maquinaria_codigo_interno_unique UNIQUE (codigo_interno);


--
-- Name: maquinaria maquinaria_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.maquinaria
    ADD CONSTRAINT maquinaria_pkey PRIMARY KEY (id);


--
-- Name: maquinaria maquinaria_serial_unique; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.maquinaria
    ADD CONSTRAINT maquinaria_serial_unique UNIQUE (serial);


--
-- Name: mermas mermas_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.mermas
    ADD CONSTRAINT mermas_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: movimientos_clasificacion movimientos_clasificacion_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.movimientos_clasificacion
    ADD CONSTRAINT movimientos_clasificacion_pkey PRIMARY KEY (id);


--
-- Name: movimientos_stock movimientos_stock_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.movimientos_stock
    ADD CONSTRAINT movimientos_stock_pkey PRIMARY KEY (id);


--
-- Name: ordenes_cosecha ordenes_cosecha_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.ordenes_cosecha
    ADD CONSTRAINT ordenes_cosecha_pkey PRIMARY KEY (id);


--
-- Name: pagos_liquidacion pagos_liquidacion_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.pagos_liquidacion
    ADD CONSTRAINT pagos_liquidacion_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: proveedores proveedores_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.proveedores
    ADD CONSTRAINT proveedores_pkey PRIMARY KEY (id);


--
-- Name: recepciones_campo recepciones_campo_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.recepciones_campo
    ADD CONSTRAINT recepciones_campo_pkey PRIMARY KEY (id);


--
-- Name: recepciones_destino recepciones_destino_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.recepciones_destino
    ADD CONSTRAINT recepciones_destino_pkey PRIMARY KEY (id);


--
-- Name: sesiones_cosecha sesiones_cosecha_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.sesiones_cosecha
    ADD CONSTRAINT sesiones_cosecha_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: sistemas_riego sistemas_riego_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.sistemas_riego
    ADD CONSTRAINT sistemas_riego_pkey PRIMARY KEY (id);


--
-- Name: stock_insumos stock_insumos_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.stock_insumos
    ADD CONSTRAINT stock_insumos_pkey PRIMARY KEY (id);


--
-- Name: tanques tanques_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.tanques
    ADD CONSTRAINT tanques_pkey PRIMARY KEY (id);


--
-- Name: team_invitations team_invitations_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.team_invitations
    ADD CONSTRAINT team_invitations_pkey PRIMARY KEY (id);


--
-- Name: team_invitations team_invitations_team_id_email_unique; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.team_invitations
    ADD CONSTRAINT team_invitations_team_id_email_unique UNIQUE (team_id, email);


--
-- Name: team_user team_user_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.team_user
    ADD CONSTRAINT team_user_pkey PRIMARY KEY (id);


--
-- Name: team_user team_user_team_id_user_id_unique; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.team_user
    ADD CONSTRAINT team_user_team_id_user_id_unique UNIQUE (team_id, user_id);


--
-- Name: teams teams_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.teams
    ADD CONSTRAINT teams_pkey PRIMARY KEY (id);


--
-- Name: tipo_maquinaria tipo_maquinaria_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.tipo_maquinaria
    ADD CONSTRAINT tipo_maquinaria_pkey PRIMARY KEY (id);


--
-- Name: tipos_evento tipos_evento_nombre_unique; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.tipos_evento
    ADD CONSTRAINT tipos_evento_nombre_unique UNIQUE (nombre);


--
-- Name: tipos_evento tipos_evento_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.tipos_evento
    ADD CONSTRAINT tipos_evento_pkey PRIMARY KEY (id);


--
-- Name: trabajadores trabajadores_numero_documento_unique; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.trabajadores
    ADD CONSTRAINT trabajadores_numero_documento_unique UNIQUE (numero_documento);


--
-- Name: trabajadores trabajadores_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.trabajadores
    ADD CONSTRAINT trabajadores_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: validaciones_riego validaciones_riego_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.validaciones_riego
    ADD CONSTRAINT validaciones_riego_pkey PRIMARY KEY (id);


--
-- Name: arboles_coordenada_spatial_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX arboles_coordenada_spatial_index ON public.arboles USING gist (coordenada_precision);


--
-- Name: arboles_fila_indice_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX arboles_fila_indice_index ON public.arboles USING btree (fila_indice);


--
-- Name: arboles_historial_fitosanitario_arbol_id_tipo_incidencia_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX arboles_historial_fitosanitario_arbol_id_tipo_incidencia_index ON public.arboles_historial_fitosanitario USING btree (arbol_id, tipo_incidencia);


--
-- Name: arboles_metricas_historicas_arbol_id_fecha_medicion_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX arboles_metricas_historicas_arbol_id_fecha_medicion_index ON public.arboles_metricas_historicas USING btree (arbol_id, fecha_medicion);


--
-- Name: arboles_posicion_indice_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX arboles_posicion_indice_index ON public.arboles USING btree (posicion_indice);


--
-- Name: bitacoras_bitacorable_type_bitacorable_id_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX bitacoras_bitacorable_type_bitacorable_id_index ON public.bitacoras USING btree (bitacorable_type, bitacorable_id);


--
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);


--
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);


--
-- Name: componentes_riego_activo_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX componentes_riego_activo_index ON public.componentes_riego USING btree (activo);


--
-- Name: componentes_riego_tipo_componente_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX componentes_riego_tipo_componente_index ON public.componentes_riego USING btree (tipo_componente);


--
-- Name: evento_arbol_evento_campo_id_arbol_id_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX evento_arbol_evento_campo_id_arbol_id_index ON public.evento_arbol USING btree (evento_campo_id, arbol_id);


--
-- Name: eventos_riego_estado_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX eventos_riego_estado_index ON public.eventos_riego USING btree (estado);


--
-- Name: eventos_riego_fecha_hora_inicio_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX eventos_riego_fecha_hora_inicio_index ON public.eventos_riego USING btree (fecha_hora_inicio);


--
-- Name: gastos_gastable_type_gastable_id_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX gastos_gastable_type_gastable_id_index ON public.gastos USING btree (gastable_type, gastable_id);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: lecturas_sensores_tanque_fecha_hora_lectura_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX lecturas_sensores_tanque_fecha_hora_lectura_index ON public.lecturas_sensores_tanque USING btree (fecha_hora_lectura);


--
-- Name: lotes_codigo_lote_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX lotes_codigo_lote_index ON public.lotes USING btree (codigo_lote);


--
-- Name: lotes_finca_id_activo_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX lotes_finca_id_activo_index ON public.lotes USING btree (finca_id, activo);


--
-- Name: lotes_geometria_gps_spatial_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX lotes_geometria_gps_spatial_index ON public.lotes USING gist (geometria_gps);


--
-- Name: lotes_zonas_manejo_lote_id_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX lotes_zonas_manejo_lote_id_index ON public.lotes_zonas_manejo USING btree (lote_id);


--
-- Name: movimientos_stock_movimientoable_type_movimientoable_id_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX movimientos_stock_movimientoable_type_movimientoable_id_index ON public.movimientos_stock USING btree (movimientoable_type, movimientoable_id);


--
-- Name: movimientos_stock_tipo_movimiento_created_at_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX movimientos_stock_tipo_movimiento_created_at_index ON public.movimientos_stock USING btree (tipo_movimiento, created_at);


--
-- Name: personal_access_tokens_expires_at_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX personal_access_tokens_expires_at_index ON public.personal_access_tokens USING btree (expires_at);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: sistemas_riego_activo_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX sistemas_riego_activo_index ON public.sistemas_riego USING btree (activo);


--
-- Name: sistemas_riego_tipo_riego_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX sistemas_riego_tipo_riego_index ON public.sistemas_riego USING btree (tipo_riego);


--
-- Name: tanques_activo_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX tanques_activo_index ON public.tanques USING btree (activo);


--
-- Name: teams_user_id_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX teams_user_id_index ON public.teams USING btree (user_id);


--
-- Name: trabajadores_activo_cargo_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX trabajadores_activo_cargo_index ON public.trabajadores USING btree (activo, cargo);


--
-- Name: trabajadores_numero_documento_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX trabajadores_numero_documento_index ON public.trabajadores USING btree (numero_documento);


--
-- Name: arboles arboles_ciclo_productivo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.arboles
    ADD CONSTRAINT arboles_ciclo_productivo_id_foreign FOREIGN KEY (ciclo_productivo_id) REFERENCES public.ciclos_productivos(id) ON DELETE CASCADE;


--
-- Name: arboles_historial_fitosanitario arboles_historial_fitosanitario_arbol_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.arboles_historial_fitosanitario
    ADD CONSTRAINT arboles_historial_fitosanitario_arbol_id_foreign FOREIGN KEY (arbol_id) REFERENCES public.arboles(id) ON DELETE CASCADE;


--
-- Name: arboles_historial_fitosanitario arboles_historial_fitosanitario_usuario_evaluador_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.arboles_historial_fitosanitario
    ADD CONSTRAINT arboles_historial_fitosanitario_usuario_evaluador_id_foreign FOREIGN KEY (usuario_evaluador_id) REFERENCES public.users(id);


--
-- Name: arboles arboles_lote_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.arboles
    ADD CONSTRAINT arboles_lote_id_foreign FOREIGN KEY (lote_id) REFERENCES public.lotes(id) ON DELETE CASCADE;


--
-- Name: arboles arboles_lote_zona_manejo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.arboles
    ADD CONSTRAINT arboles_lote_zona_manejo_id_foreign FOREIGN KEY (lote_zona_manejo_id) REFERENCES public.lotes_zonas_manejo(id) ON DELETE SET NULL;


--
-- Name: arboles_metricas_historicas arboles_metricas_historicas_arbol_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.arboles_metricas_historicas
    ADD CONSTRAINT arboles_metricas_historicas_arbol_id_foreign FOREIGN KEY (arbol_id) REFERENCES public.arboles(id) ON DELETE CASCADE;


--
-- Name: bitacoras bitacoras_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.bitacoras
    ADD CONSTRAINT bitacoras_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: carta_porte carta_porte_despacho_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.carta_porte
    ADD CONSTRAINT carta_porte_despacho_id_foreign FOREIGN KEY (despacho_id) REFERENCES public.despachos(id) ON DELETE CASCADE;


--
-- Name: carta_porte carta_porte_transportador_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.carta_porte
    ADD CONSTRAINT carta_porte_transportador_id_foreign FOREIGN KEY (transportador_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: ciclos_productivos ciclos_productivos_agronomo_responsable_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.ciclos_productivos
    ADD CONSTRAINT ciclos_productivos_agronomo_responsable_id_foreign FOREIGN KEY (agronomo_responsable_id) REFERENCES public.users(id);


--
-- Name: ciclos_productivos ciclos_productivos_cultivo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.ciclos_productivos
    ADD CONSTRAINT ciclos_productivos_cultivo_id_foreign FOREIGN KEY (cultivo_id) REFERENCES public.cultivos(id) ON DELETE CASCADE;


--
-- Name: ciclos_productivos ciclos_productivos_lote_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.ciclos_productivos
    ADD CONSTRAINT ciclos_productivos_lote_id_foreign FOREIGN KEY (lote_id) REFERENCES public.lotes(id) ON DELETE CASCADE;


--
-- Name: ciclos_productivos ciclos_productivos_proveedor_material_vegetal_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.ciclos_productivos
    ADD CONSTRAINT ciclos_productivos_proveedor_material_vegetal_id_foreign FOREIGN KEY (proveedor_material_vegetal_id) REFERENCES public.proveedores(id);


--
-- Name: componentes_riego componentes_riego_sistema_riego_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.componentes_riego
    ADD CONSTRAINT componentes_riego_sistema_riego_id_foreign FOREIGN KEY (sistema_riego_id) REFERENCES public.sistemas_riego(id) ON DELETE CASCADE;


--
-- Name: compra_items compra_items_compra_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.compra_items
    ADD CONSTRAINT compra_items_compra_id_foreign FOREIGN KEY (compra_id) REFERENCES public.compras(id) ON DELETE CASCADE;


--
-- Name: compra_items compra_items_insumo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.compra_items
    ADD CONSTRAINT compra_items_insumo_id_foreign FOREIGN KEY (insumo_id) REFERENCES public.insumos(id) ON DELETE SET NULL;


--
-- Name: compras compras_ciclo_productivo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.compras
    ADD CONSTRAINT compras_ciclo_productivo_id_foreign FOREIGN KEY (ciclo_productivo_id) REFERENCES public.ciclos_productivos(id);


--
-- Name: compras_pagos compras_pagos_compra_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.compras_pagos
    ADD CONSTRAINT compras_pagos_compra_id_foreign FOREIGN KEY (compra_id) REFERENCES public.compras(id);


--
-- Name: compras_pagos compras_pagos_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.compras_pagos
    ADD CONSTRAINT compras_pagos_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- Name: compras compras_proveedor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.compras
    ADD CONSTRAINT compras_proveedor_id_foreign FOREIGN KEY (proveedor_id) REFERENCES public.proveedores(id) ON DELETE CASCADE;


--
-- Name: compras compras_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.compras
    ADD CONSTRAINT compras_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: contenedores contenedores_cliente_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.contenedores
    ADD CONSTRAINT contenedores_cliente_id_foreign FOREIGN KEY (cliente_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: contenedores contenedores_orden_pedido_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.contenedores
    ADD CONSTRAINT contenedores_orden_pedido_id_foreign FOREIGN KEY (orden_pedido_id) REFERENCES public.ordenes_cosecha(id) ON DELETE SET NULL;


--
-- Name: contenedores contenedores_sesion_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.contenedores
    ADD CONSTRAINT contenedores_sesion_id_foreign FOREIGN KEY (sesion_id) REFERENCES public.sesiones_cosecha(id) ON DELETE CASCADE;


--
-- Name: despacho_items despacho_items_ciclo_productivo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.despacho_items
    ADD CONSTRAINT despacho_items_ciclo_productivo_id_foreign FOREIGN KEY (ciclo_productivo_id) REFERENCES public.ciclos_productivos(id) ON DELETE SET NULL;


--
-- Name: despacho_items despacho_items_contenedor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.despacho_items
    ADD CONSTRAINT despacho_items_contenedor_id_foreign FOREIGN KEY (contenedor_id) REFERENCES public.contenedores(id) ON DELETE SET NULL;


--
-- Name: despacho_items despacho_items_despacho_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.despacho_items
    ADD CONSTRAINT despacho_items_despacho_id_foreign FOREIGN KEY (despacho_id) REFERENCES public.despachos(id) ON DELETE CASCADE;


--
-- Name: despachos despachos_cliente_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.despachos
    ADD CONSTRAINT despachos_cliente_id_foreign FOREIGN KEY (cliente_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: despachos despachos_comisionista_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.despachos
    ADD CONSTRAINT despachos_comisionista_id_foreign FOREIGN KEY (comisionista_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: evento_arbol evento_arbol_arbol_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_arbol
    ADD CONSTRAINT evento_arbol_arbol_id_foreign FOREIGN KEY (arbol_id) REFERENCES public.arboles(id) ON DELETE CASCADE;


--
-- Name: evento_arbol evento_arbol_evento_campo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_arbol
    ADD CONSTRAINT evento_arbol_evento_campo_id_foreign FOREIGN KEY (evento_campo_id) REFERENCES public.eventos_campo(id) ON DELETE CASCADE;


--
-- Name: evento_insumo_lotes evento_insumo_lotes_evento_insumo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_insumo_lotes
    ADD CONSTRAINT evento_insumo_lotes_evento_insumo_id_foreign FOREIGN KEY (evento_insumo_id) REFERENCES public.evento_insumos(id) ON DELETE CASCADE;


--
-- Name: evento_insumo_lotes evento_insumo_lotes_lote_insumo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_insumo_lotes
    ADD CONSTRAINT evento_insumo_lotes_lote_insumo_id_foreign FOREIGN KEY (lote_insumo_id) REFERENCES public.lotes_insumos(id) ON DELETE RESTRICT;


--
-- Name: evento_insumos evento_insumos_evento_campo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_insumos
    ADD CONSTRAINT evento_insumos_evento_campo_id_foreign FOREIGN KEY (evento_campo_id) REFERENCES public.eventos_campo(id) ON DELETE CASCADE;


--
-- Name: evento_insumos evento_insumos_insumo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_insumos
    ADD CONSTRAINT evento_insumos_insumo_id_foreign FOREIGN KEY (insumo_id) REFERENCES public.insumos(id) ON DELETE CASCADE;


--
-- Name: evento_mano_obra evento_mano_obra_ciclo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_mano_obra
    ADD CONSTRAINT evento_mano_obra_ciclo_id_foreign FOREIGN KEY (ciclo_id) REFERENCES public.ciclos_productivos(id) ON DELETE SET NULL;


--
-- Name: evento_mano_obra evento_mano_obra_evento_campo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_mano_obra
    ADD CONSTRAINT evento_mano_obra_evento_campo_id_foreign FOREIGN KEY (evento_campo_id) REFERENCES public.eventos_campo(id) ON DELETE CASCADE;


--
-- Name: evento_mano_obra evento_mano_obra_sesion_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_mano_obra
    ADD CONSTRAINT evento_mano_obra_sesion_id_foreign FOREIGN KEY (sesion_id) REFERENCES public.sesiones_cosecha(id) ON DELETE SET NULL;


--
-- Name: evento_mano_obra evento_mano_obra_trabajador_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_mano_obra
    ADD CONSTRAINT evento_mano_obra_trabajador_id_foreign FOREIGN KEY (trabajador_id) REFERENCES public.trabajadores(id) ON DELETE SET NULL;


--
-- Name: evento_maquinaria evento_maquinaria_evento_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_maquinaria
    ADD CONSTRAINT evento_maquinaria_evento_id_foreign FOREIGN KEY (evento_id) REFERENCES public.eventos_campo(id) ON DELETE CASCADE;


--
-- Name: evento_maquinaria evento_maquinaria_maquina_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_maquinaria
    ADD CONSTRAINT evento_maquinaria_maquina_id_foreign FOREIGN KEY (maquina_id) REFERENCES public.maquinaria(id) ON DELETE SET NULL;


--
-- Name: evento_maquinaria evento_maquinaria_trabajador_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_maquinaria
    ADD CONSTRAINT evento_maquinaria_trabajador_id_foreign FOREIGN KEY (trabajador_id) REFERENCES public.users(id);


--
-- Name: evento_riego_componentes evento_riego_componentes_componente_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_riego_componentes
    ADD CONSTRAINT evento_riego_componentes_componente_id_foreign FOREIGN KEY (componente_id) REFERENCES public.componentes_riego(id);


--
-- Name: evento_riego_componentes evento_riego_componentes_evento_riego_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.evento_riego_componentes
    ADD CONSTRAINT evento_riego_componentes_evento_riego_id_foreign FOREIGN KEY (evento_riego_id) REFERENCES public.eventos_riego(id) ON DELETE CASCADE;


--
-- Name: eventos_campo eventos_campo_ciclo_productivo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.eventos_campo
    ADD CONSTRAINT eventos_campo_ciclo_productivo_id_foreign FOREIGN KEY (ciclo_productivo_id) REFERENCES public.ciclos_productivos(id) ON DELETE SET NULL;


--
-- Name: eventos_campo eventos_campo_lote_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.eventos_campo
    ADD CONSTRAINT eventos_campo_lote_id_foreign FOREIGN KEY (lote_id) REFERENCES public.lotes(id);


--
-- Name: eventos_campo eventos_campo_tipo_evento_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.eventos_campo
    ADD CONSTRAINT eventos_campo_tipo_evento_id_foreign FOREIGN KEY (tipo_evento_id) REFERENCES public.tipos_evento(id);


--
-- Name: eventos_campo eventos_campo_zona_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.eventos_campo
    ADD CONSTRAINT eventos_campo_zona_id_foreign FOREIGN KEY (zona_id) REFERENCES public.lotes_zonas_manejo(id) ON DELETE SET NULL;


--
-- Name: eventos_riego eventos_riego_responsable_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.eventos_riego
    ADD CONSTRAINT eventos_riego_responsable_id_foreign FOREIGN KEY (responsable_id) REFERENCES public.users(id);


--
-- Name: eventos_riego eventos_riego_sistema_riego_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.eventos_riego
    ADD CONSTRAINT eventos_riego_sistema_riego_id_foreign FOREIGN KEY (sistema_riego_id) REFERENCES public.sistemas_riego(id);


--
-- Name: fenologia_etapas fenologia_etapas_cultivo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.fenologia_etapas
    ADD CONSTRAINT fenologia_etapas_cultivo_id_foreign FOREIGN KEY (cultivo_id) REFERENCES public.cultivos(id) ON DELETE CASCADE;


--
-- Name: gastos gastos_ciclo_productivo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.gastos
    ADD CONSTRAINT gastos_ciclo_productivo_id_foreign FOREIGN KEY (ciclo_productivo_id) REFERENCES public.ciclos_productivos(id) ON DELETE SET NULL;


--
-- Name: gastos gastos_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.gastos
    ADD CONSTRAINT gastos_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: insumo_componentes insumo_componentes_insumo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.insumo_componentes
    ADD CONSTRAINT insumo_componentes_insumo_id_foreign FOREIGN KEY (insumo_id) REFERENCES public.insumos(id) ON DELETE CASCADE;


--
-- Name: insumos insumos_categoria_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.insumos
    ADD CONSTRAINT insumos_categoria_id_foreign FOREIGN KEY (categoria_id) REFERENCES public.categorias_insumo(id) ON DELETE CASCADE;


--
-- Name: insumos insumos_proveedor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.insumos
    ADD CONSTRAINT insumos_proveedor_id_foreign FOREIGN KEY (proveedor_id) REFERENCES public.proveedores(id) ON DELETE SET NULL;


--
-- Name: labores_plantilla labores_plantilla_cultivo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.labores_plantilla
    ADD CONSTRAINT labores_plantilla_cultivo_id_foreign FOREIGN KEY (cultivo_id) REFERENCES public.cultivos(id) ON DELETE CASCADE;


--
-- Name: labores_plantilla labores_plantilla_fenologia_etapa_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.labores_plantilla
    ADD CONSTRAINT labores_plantilla_fenologia_etapa_id_foreign FOREIGN KEY (fenologia_etapa_id) REFERENCES public.fenologia_etapas(id);


--
-- Name: labores_plantilla labores_plantilla_tipo_evento_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.labores_plantilla
    ADD CONSTRAINT labores_plantilla_tipo_evento_id_foreign FOREIGN KEY (tipo_evento_id) REFERENCES public.tipos_evento(id);


--
-- Name: lecturas_sensores_tanque lecturas_sensores_tanque_tanque_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.lecturas_sensores_tanque
    ADD CONSTRAINT lecturas_sensores_tanque_tanque_id_foreign FOREIGN KEY (tanque_id) REFERENCES public.tanques(id) ON DELETE CASCADE;


--
-- Name: liquidaciones_despacho liquidaciones_despacho_despacho_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.liquidaciones_despacho
    ADD CONSTRAINT liquidaciones_despacho_despacho_id_foreign FOREIGN KEY (despacho_id) REFERENCES public.despachos(id) ON DELETE CASCADE;


--
-- Name: liquidaciones_despacho liquidaciones_despacho_despacho_item_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.liquidaciones_despacho
    ADD CONSTRAINT liquidaciones_despacho_despacho_item_id_foreign FOREIGN KEY (despacho_item_id) REFERENCES public.despacho_items(id) ON DELETE CASCADE;


--
-- Name: lotes_analiticas_suelo lotes_analiticas_suelo_analista_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.lotes_analiticas_suelo
    ADD CONSTRAINT lotes_analiticas_suelo_analista_user_id_foreign FOREIGN KEY (analista_user_id) REFERENCES public.users(id);


--
-- Name: lotes_analiticas_suelo lotes_analiticas_suelo_lote_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.lotes_analiticas_suelo
    ADD CONSTRAINT lotes_analiticas_suelo_lote_id_foreign FOREIGN KEY (lote_id) REFERENCES public.lotes(id) ON DELETE CASCADE;


--
-- Name: lotes lotes_finca_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.lotes
    ADD CONSTRAINT lotes_finca_id_foreign FOREIGN KEY (finca_id) REFERENCES public.fincas(id) ON DELETE CASCADE;


--
-- Name: lotes_insumos lotes_insumos_insumo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.lotes_insumos
    ADD CONSTRAINT lotes_insumos_insumo_id_foreign FOREIGN KEY (insumo_id) REFERENCES public.insumos(id);


--
-- Name: lotes_insumos lotes_insumos_proveedor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.lotes_insumos
    ADD CONSTRAINT lotes_insumos_proveedor_id_foreign FOREIGN KEY (proveedor_id) REFERENCES public.proveedores(id);


--
-- Name: lotes_sistemas_riego lotes_sistemas_riego_lote_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.lotes_sistemas_riego
    ADD CONSTRAINT lotes_sistemas_riego_lote_id_foreign FOREIGN KEY (lote_id) REFERENCES public.lotes(id) ON DELETE CASCADE;


--
-- Name: lotes_zonas_manejo lotes_zonas_manejo_lote_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.lotes_zonas_manejo
    ADD CONSTRAINT lotes_zonas_manejo_lote_id_foreign FOREIGN KEY (lote_id) REFERENCES public.lotes(id) ON DELETE CASCADE;


--
-- Name: mantenimientos_maquinaria mantenimientos_maquinaria_maquina_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.mantenimientos_maquinaria
    ADD CONSTRAINT mantenimientos_maquinaria_maquina_id_foreign FOREIGN KEY (maquina_id) REFERENCES public.maquinaria(id) ON DELETE SET NULL;


--
-- Name: maquinaria maquinaria_responsable_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.maquinaria
    ADD CONSTRAINT maquinaria_responsable_id_foreign FOREIGN KEY (responsable_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: maquinaria maquinaria_tipo_maquinaria_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.maquinaria
    ADD CONSTRAINT maquinaria_tipo_maquinaria_id_foreign FOREIGN KEY (tipo_maquinaria_id) REFERENCES public.tipo_maquinaria(id) ON DELETE RESTRICT;


--
-- Name: mermas mermas_contenedor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.mermas
    ADD CONSTRAINT mermas_contenedor_id_foreign FOREIGN KEY (contenedor_id) REFERENCES public.contenedores(id) ON DELETE SET NULL;


--
-- Name: mermas mermas_recepcion_campo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.mermas
    ADD CONSTRAINT mermas_recepcion_campo_id_foreign FOREIGN KEY (recepcion_campo_id) REFERENCES public.recepciones_campo(id) ON DELETE SET NULL;


--
-- Name: movimientos_clasificacion movimientos_clasificacion_contenedor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.movimientos_clasificacion
    ADD CONSTRAINT movimientos_clasificacion_contenedor_id_foreign FOREIGN KEY (contenedor_id) REFERENCES public.contenedores(id) ON DELETE CASCADE;


--
-- Name: movimientos_clasificacion movimientos_clasificacion_recepcion_campo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.movimientos_clasificacion
    ADD CONSTRAINT movimientos_clasificacion_recepcion_campo_id_foreign FOREIGN KEY (recepcion_campo_id) REFERENCES public.recepciones_campo(id) ON DELETE CASCADE;


--
-- Name: movimientos_stock movimientos_stock_lote_insumo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.movimientos_stock
    ADD CONSTRAINT movimientos_stock_lote_insumo_id_foreign FOREIGN KEY (lote_insumo_id) REFERENCES public.lotes_insumos(id) ON DELETE CASCADE;


--
-- Name: movimientos_stock movimientos_stock_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.movimientos_stock
    ADD CONSTRAINT movimientos_stock_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: ordenes_cosecha ordenes_cosecha_ciclo_productivo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.ordenes_cosecha
    ADD CONSTRAINT ordenes_cosecha_ciclo_productivo_id_foreign FOREIGN KEY (ciclo_productivo_id) REFERENCES public.ciclos_productivos(id);


--
-- Name: ordenes_cosecha ordenes_cosecha_cliente_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.ordenes_cosecha
    ADD CONSTRAINT ordenes_cosecha_cliente_id_foreign FOREIGN KEY (cliente_id) REFERENCES public.users(id) ON DELETE RESTRICT;


--
-- Name: ordenes_cosecha ordenes_cosecha_lote_cultivo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.ordenes_cosecha
    ADD CONSTRAINT ordenes_cosecha_lote_cultivo_id_foreign FOREIGN KEY (lote_cultivo_id) REFERENCES public.lotes(id) ON DELETE RESTRICT;


--
-- Name: ordenes_cosecha ordenes_cosecha_lote_zona_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.ordenes_cosecha
    ADD CONSTRAINT ordenes_cosecha_lote_zona_id_foreign FOREIGN KEY (lote_zona_id) REFERENCES public.lotes_zonas_manejo(id) ON DELETE SET NULL;


--
-- Name: ordenes_cosecha ordenes_cosecha_responsable_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.ordenes_cosecha
    ADD CONSTRAINT ordenes_cosecha_responsable_id_foreign FOREIGN KEY (responsable_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: pagos_liquidacion pagos_liquidacion_despacho_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.pagos_liquidacion
    ADD CONSTRAINT pagos_liquidacion_despacho_id_foreign FOREIGN KEY (despacho_id) REFERENCES public.despachos(id) ON DELETE RESTRICT;


--
-- Name: pagos_liquidacion pagos_liquidacion_liquidacion_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.pagos_liquidacion
    ADD CONSTRAINT pagos_liquidacion_liquidacion_id_foreign FOREIGN KEY (liquidacion_id) REFERENCES public.liquidaciones_despacho(id) ON DELETE RESTRICT;


--
-- Name: pagos_liquidacion pagos_liquidacion_registrado_por_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.pagos_liquidacion
    ADD CONSTRAINT pagos_liquidacion_registrado_por_foreign FOREIGN KEY (registrado_por) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: recepciones_campo recepciones_campo_arbol_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.recepciones_campo
    ADD CONSTRAINT recepciones_campo_arbol_id_foreign FOREIGN KEY (arbol_id) REFERENCES public.arboles(id) ON DELETE SET NULL;


--
-- Name: recepciones_campo recepciones_campo_lote_zona_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.recepciones_campo
    ADD CONSTRAINT recepciones_campo_lote_zona_id_foreign FOREIGN KEY (lote_zona_id) REFERENCES public.lotes_zonas_manejo(id) ON DELETE SET NULL;


--
-- Name: recepciones_campo recepciones_campo_sesion_cosecha_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.recepciones_campo
    ADD CONSTRAINT recepciones_campo_sesion_cosecha_id_foreign FOREIGN KEY (sesion_cosecha_id) REFERENCES public.sesiones_cosecha(id) ON DELETE CASCADE;


--
-- Name: recepciones_campo recepciones_campo_trabajador_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.recepciones_campo
    ADD CONSTRAINT recepciones_campo_trabajador_id_foreign FOREIGN KEY (trabajador_id) REFERENCES public.trabajadores(id) ON DELETE RESTRICT;


--
-- Name: recepciones_destino recepciones_destino_despacho_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.recepciones_destino
    ADD CONSTRAINT recepciones_destino_despacho_id_foreign FOREIGN KEY (despacho_id) REFERENCES public.despachos(id) ON DELETE CASCADE;


--
-- Name: sesiones_cosecha sesiones_cosecha_evento_campo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.sesiones_cosecha
    ADD CONSTRAINT sesiones_cosecha_evento_campo_id_foreign FOREIGN KEY (evento_campo_id) REFERENCES public.eventos_campo(id) ON DELETE CASCADE;


--
-- Name: sesiones_cosecha sesiones_cosecha_orden_cosecha_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.sesiones_cosecha
    ADD CONSTRAINT sesiones_cosecha_orden_cosecha_id_foreign FOREIGN KEY (orden_cosecha_id) REFERENCES public.ordenes_cosecha(id) ON DELETE CASCADE;


--
-- Name: sesiones_cosecha sesiones_cosecha_responsable_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.sesiones_cosecha
    ADD CONSTRAINT sesiones_cosecha_responsable_id_foreign FOREIGN KEY (responsable_id) REFERENCES public.users(id) ON DELETE RESTRICT;


--
-- Name: sistemas_riego sistemas_riego_lote_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.sistemas_riego
    ADD CONSTRAINT sistemas_riego_lote_id_foreign FOREIGN KEY (lote_id) REFERENCES public.lotes(id) ON DELETE SET NULL;


--
-- Name: sistemas_riego sistemas_riego_tanque_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.sistemas_riego
    ADD CONSTRAINT sistemas_riego_tanque_id_foreign FOREIGN KEY (tanque_id) REFERENCES public.tanques(id) ON DELETE CASCADE;


--
-- Name: stock_insumos stock_insumos_insumo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.stock_insumos
    ADD CONSTRAINT stock_insumos_insumo_id_foreign FOREIGN KEY (insumo_id) REFERENCES public.insumos(id) ON DELETE CASCADE;


--
-- Name: tanques tanques_lote_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.tanques
    ADD CONSTRAINT tanques_lote_id_foreign FOREIGN KEY (lote_id) REFERENCES public.lotes(id) ON DELETE SET NULL;


--
-- Name: team_invitations team_invitations_team_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.team_invitations
    ADD CONSTRAINT team_invitations_team_id_foreign FOREIGN KEY (team_id) REFERENCES public.teams(id) ON DELETE CASCADE;


--
-- Name: trabajadores trabajadores_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.trabajadores
    ADD CONSTRAINT trabajadores_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: validaciones_riego validaciones_riego_evento_riego_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.validaciones_riego
    ADD CONSTRAINT validaciones_riego_evento_riego_id_foreign FOREIGN KEY (evento_riego_id) REFERENCES public.eventos_riego(id) ON DELETE CASCADE;


--
-- Name: validaciones_riego validaciones_riego_tanque_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.validaciones_riego
    ADD CONSTRAINT validaciones_riego_tanque_id_foreign FOREIGN KEY (tanque_id) REFERENCES public.tanques(id);


--
-- PostgreSQL database dump complete
--

