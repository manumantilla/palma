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
-- Name: arboles_red_vecindad; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.arboles_red_vecindad (
    id bigint NOT NULL,
    arbol_origen_id bigint NOT NULL,
    arbol_destino_id bigint NOT NULL,
    distancia_metros numeric(6,2) NOT NULL,
    probabilidad_contagio_base numeric(5,4) DEFAULT 0.05 NOT NULL,
    tipo_contacto character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT arboles_red_vecindad_tipo_contacto_check CHECK (((tipo_contacto)::text = ANY ((ARRAY['misma_fila'::character varying, 'fila_contigua'::character varying, 'viento_predominante'::character varying, 'mecanico_herramienta'::character varying])::text[])))
);


ALTER TABLE public.arboles_red_vecindad OWNER TO sail;

--
-- Name: arboles_red_vecindad_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.arboles_red_vecindad_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.arboles_red_vecindad_id_seq OWNER TO sail;

--
-- Name: arboles_red_vecindad_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.arboles_red_vecindad_id_seq OWNED BY public.arboles_red_vecindad.id;


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
-- Name: ciclo_productivo_zona_manejo; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.ciclo_productivo_zona_manejo (
    id bigint NOT NULL,
    ciclo_productivo_id bigint NOT NULL,
    zona_id bigint NOT NULL,
    toneladas_producidas numeric(8,2) DEFAULT '0'::numeric NOT NULL,
    area_hectareas_momento numeric(8,4),
    geometria_zona_momento public.geometry(Polygon,4326),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.ciclo_productivo_zona_manejo OWNER TO sail;

--
-- Name: ciclo_productivo_zona_manejo_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.ciclo_productivo_zona_manejo_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.ciclo_productivo_zona_manejo_id_seq OWNER TO sail;

--
-- Name: ciclo_productivo_zona_manejo_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.ciclo_productivo_zona_manejo_id_seq OWNED BY public.ciclo_productivo_zona_manejo.id;


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
    variedad character varying(255),
    calidad character varying(255),
    tipo_destino character varying(255) DEFAULT 'mercado_local'::character varying NOT NULL,
    calibre_talla character varying(255),
    kilos_acumulados numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    peso_tara numeric(8,2) DEFAULT '0'::numeric NOT NULL,
    peso_total numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    kilos_merma_acumulada numeric(12,2) DEFAULT '0'::numeric NOT NULL,
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
    precio numeric(14,2) NOT NULL,
    area_aplicada numeric(10,2),
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
    evento_campo_id bigint,
    sesion_id uuid,
    ciclo_id bigint,
    tipo_labor character varying(255) NOT NULL,
    trabajador_id bigint,
    nombre_trabajador character varying(255),
    cedula character varying(255) NOT NULL,
    cantidad numeric(12,1) NOT NULL,
    unidad_destajo character varying(30),
    valor_unitario numeric(10,2) NOT NULL,
    costo_total numeric(15,2) NOT NULL,
    estado_pago character varying(255) DEFAULT 'pendiente'::character varying NOT NULL,
    fecha_pago timestamp(0) without time zone,
    observaciones text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT evento_mano_obra_estado_pago_check CHECK (((estado_pago)::text = ANY ((ARRAY['pendiente'::character varying, 'pagado'::character varying, 'anulado'::character varying])::text[]))),
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
    coordenada_gps public.geometry(Geometry,4326),
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
    nombre character varying(255) NOT NULL,
    registro_ica character varying(50),
    ingrediente_principal character varying(255),
    unidad_base_id bigint NOT NULL,
    unidad_uso_id bigint,
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
    metadata jsonb,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT insumos_estado_check CHECK (((estado)::text = ANY ((ARRAY['activo'::character varying, 'inactivo'::character varying])::text[]))),
    CONSTRAINT insumos_nivel_toxicidad_check CHECK (((nivel_toxicidad)::text = ANY ((ARRAY['bajo'::character varying, 'medio'::character varying, 'alto'::character varying])::text[])))
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
    id uuid NOT NULL,
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
    compra_id bigint NOT NULL,
    codigo_lote character varying(255) NOT NULL,
    fecha_vencimiento date NOT NULL,
    fecha_ingreso date NOT NULL,
    cantidad_inicial numeric(12,2) NOT NULL,
    cantidad_actual numeric(12,2) NOT NULL,
    unidad_id bigint NOT NULL,
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
    CONSTRAINT mermas_motivo_check CHECK (((motivo)::text = ANY ((ARRAY['daño_mecanico'::character varying, 'daño_fitosanitario'::character varying, 'descarte_calidad'::character varying, 'deshidratacion'::character varying, 'perdida'::character varying, 'robo'::character varying, 'consumo_interno'::character varying, 'error_bascula'::character varying, 'otro'::character varying])::text[])))
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
-- Name: recepcion_arboles; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.recepcion_arboles (
    id uuid NOT NULL,
    recepcion_campo_id uuid NOT NULL,
    arbol_id bigint NOT NULL,
    peso_estimado_kg numeric(10,2),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.recepcion_arboles OWNER TO sail;

--
-- Name: recepciones_campo; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.recepciones_campo (
    id uuid NOT NULL,
    sesion_cosecha_id uuid NOT NULL,
    lote_zona_id bigint,
    trabajador_id bigint NOT NULL,
    peso_bruto numeric(10,2) NOT NULL,
    tara_costal numeric(8,2) DEFAULT '0'::numeric NOT NULL,
    peso_neto numeric(10,2) NOT NULL,
    hora_pesaje timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    foto_evidencia character varying(255),
    metodo_pesaje character varying(255) DEFAULT 'balanza_electronica'::character varying NOT NULL,
    costal_codigo character varying(255),
    numero_corte integer,
    estado_clasificacion character varying(255) DEFAULT 'pendiente'::character varying NOT NULL,
    client_updated_at timestamp(0) without time zone,
    synced_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT recepciones_campo_estado_clasificacion_check CHECK (((estado_clasificacion)::text = ANY ((ARRAY['pendiente'::character varying, 'en_proceso'::character varying, 'clasificado'::character varying])::text[]))),
    CONSTRAINT recepciones_campo_metodo_pesaje_check CHECK (((metodo_pesaje)::text = ANY ((ARRAY['balanza_electronica'::character varying, 'balanza_mecanica'::character varying, 'estimado'::character varying])::text[])))
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
    altura_maxima_cm numeric(9,2) NOT NULL,
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
-- Name: unidades_medida; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.unidades_medida (
    id bigint NOT NULL,
    nombre character varying(50) NOT NULL,
    abreviatura character varying(10) NOT NULL,
    tipo character varying(255) DEFAULT 'unidad'::character varying NOT NULL,
    factor_conversion numeric(8,4) DEFAULT '1'::numeric NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT unidades_medida_tipo_check CHECK (((tipo)::text = ANY ((ARRAY['masa'::character varying, 'volumen'::character varying, 'unidad'::character varying])::text[])))
);


ALTER TABLE public.unidades_medida OWNER TO sail;

--
-- Name: unidades_medida_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.unidades_medida_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.unidades_medida_id_seq OWNER TO sail;

--
-- Name: unidades_medida_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.unidades_medida_id_seq OWNED BY public.unidades_medida.id;


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
-- Name: arboles_red_vecindad id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.arboles_red_vecindad ALTER COLUMN id SET DEFAULT nextval('public.arboles_red_vecindad_id_seq'::regclass);


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
-- Name: ciclo_productivo_zona_manejo id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.ciclo_productivo_zona_manejo ALTER COLUMN id SET DEFAULT nextval('public.ciclo_productivo_zona_manejo_id_seq'::regclass);


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
-- Name: unidades_medida id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.unidades_medida ALTER COLUMN id SET DEFAULT nextval('public.unidades_medida_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: validaciones_riego id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.validaciones_riego ALTER COLUMN id SET DEFAULT nextval('public.validaciones_riego_id_seq'::regclass);


--
-- Data for Name: arboles; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.arboles (id, ciclo_productivo_id, lote_id, lote_zona_manejo_id, codigo_unico, fila_indice, posicion_indice, altitud, estado_vital, etapa_biologica, fecha_baja_muerte, motivo_baja, coordenada_precision, altitud_ortometrica_msnm, fecha_siembra, fecha_primera_cosecha, variedad, fecha_muerte, causa_muerte, es_reemplazo, fecha_reemplazo, produccion_acumulada_kg, ciclos_productivos_count, observaciones, deleted_at, created_at, updated_at) FROM stdin;
1	1	1	\N	A-001-001	1	1	1886.54	muerto	vivero	2026-03-25	\N	0101000020E610000000000000004053C000000000000028C0	1287.52	2024-08-02	2025-07-25	Bacon	\N	\N	f	\N	251.04	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
2	1	1	\N	A-001-002	1	2	1786.63	excelente	senescencia	\N	\N	0101000020E61000001CE9708CFF3F53C000000000000028C0	817.64	2024-04-11	\N	Hass	\N	\N	f	\N	131.00	8	Ab in accusantium aut rerum in eos molestias.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
3	1	1	\N	A-001-003	1	3	1952.95	excelente	establecimiento	\N	\N	0101000020E6100000F2D1E118FF3F53C000000000000028C0	2116.39	2023-03-15	\N	Bacon	\N	\N	f	\N	394.23	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
4	1	1	\N	A-001-004	1	4	1761.30	excelente	senescencia	\N	\N	0101000020E61000000EBB52A5FE3F53C000000000000028C0	2409.83	2026-05-29	2026-07-20	Ettinger	\N	\N	f	\N	479.50	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
5	1	1	\N	A-001-005	1	5	2448.47	con_estres	senescencia	\N	\N	0101000020E6100000E3A3C331FE3F53C000000000000028C0	1578.02	2024-10-23	2025-07-20	Hass	\N	\N	f	\N	474.81	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
6	1	1	\N	A-001-006	1	6	2195.39	excelente	produccion_madura	\N	\N	0101000020E6100000FF8C34BEFD3F53C000000000000028C0	2392.17	2025-06-04	2026-06-11	Hass	\N	\N	t	\N	416.60	4	Harum officiis temporibus molestiae consectetur atque ipsam et.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
7	1	1	\N	A-001-007	1	7	1632.83	muerto	senescencia	2025-10-30	\N	0101000020E6100000D575A54AFD3F53C000000000000028C0	1911.47	2025-09-18	2025-10-09	Bacon	\N	\N	f	\N	183.16	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
8	1	1	\N	A-001-008	1	8	959.07	excelente	senescencia	\N	\N	0101000020E6100000F15E16D7FC3F53C000000000000028C0	1666.87	2024-02-29	\N	Zutano	\N	\N	f	\N	131.91	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
9	1	1	\N	A-001-009	1	9	1143.25	enfermo_critico	establecimiento	\N	\N	0101000020E61000000D488763FC3F53C000000000000028C0	2270.64	2025-07-23	\N	Zutano	\N	\N	f	\N	462.27	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
10	1	1	\N	A-001-010	1	10	2172.32	con_estres	produccion_madura	\N	\N	0101000020E6100000E330F8EFFB3F53C000000000000028C0	1074.13	2026-01-17	2026-04-25	Ettinger	\N	\N	f	\N	304.16	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
11	1	1	\N	A-001-011	1	11	1171.55	excelente	senescencia	\N	\N	0101000020E6100000FF19697CFB3F53C000000000000028C0	\N	2024-01-07	2025-01-12	Fuerte	\N	\N	f	\N	376.37	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
12	1	1	\N	A-001-012	1	12	1576.58	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000D502DA08FB3F53C000000000000028C0	1140.92	2025-07-13	\N	Zutano	\N	\N	f	\N	177.56	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
13	1	1	\N	A-001-013	1	13	2155.77	muerto	vivero	2025-11-16	\N	0101000020E6100000F1EB4A95FA3F53C000000000000028C0	1712.57	2023-07-29	2026-02-19	Ettinger	\N	\N	f	\N	394.88	3	Provident odio illo deleniti veritatis occaecati et sequi et.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
14	1	1	\N	A-001-014	1	14	1653.39	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000C6D4BB21FA3F53C000000000000028C0	\N	2025-08-10	2025-08-21	Bacon	\N	\N	f	\N	413.84	7	Alias officiis quos sit sint corporis natus.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
15	1	1	\N	A-001-015	1	15	1123.71	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000E2BD2CAEF93F53C000000000000028C0	1712.02	2025-12-23	\N	Hass	\N	\N	f	\N	413.54	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
16	1	1	\N	A-001-016	1	16	1732.36	muerto	senescencia	2026-03-01	\N	0101000020E6100000FEA69D3AF93F53C000000000000028C0	964.85	2024-01-25	2024-05-10	Fuerte	\N	\N	f	\N	289.08	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
17	1	1	\N	A-001-017	1	17	1121.62	enfermo_critico	senescencia	\N	\N	0101000020E6100000D48F0EC7F83F53C000000000000028C0	\N	2025-01-29	2025-10-26	Hass	\N	\N	f	\N	79.51	4	Voluptatum corrupti corrupti quia exercitationem ipsa repudiandae quo.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
18	1	1	\N	A-001-018	1	18	2472.44	erradicado	establecimiento	2026-03-19	\N	0101000020E6100000F0787F53F83F53C000000000000028C0	\N	2025-11-21	2025-12-14	Bacon	\N	\N	f	\N	64.09	9	Minima omnis delectus cupiditate harum enim alias laudantium.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
19	1	1	\N	A-001-019	1	19	2190.54	muerto	produccion_madura	2026-01-13	\N	0101000020E6100000C661F0DFF73F53C000000000000028C0	1758.72	2025-10-13	2025-12-31	Fuerte	\N	\N	f	\N	92.23	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
20	1	1	\N	A-001-020	1	20	1424.06	muerto	vivero	2025-12-21	\N	0101000020E6100000E24A616CF73F53C000000000000028C0	2007.15	2025-08-03	\N	Hass	\N	\N	f	\N	319.93	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
21	1	1	\N	A-001-021	1	21	1322.93	con_estres	produccion_madura	\N	\N	0101000020E6100000B733D2F8F63F53C000000000000028C0	1279.09	2024-09-17	2026-01-18	Zutano	\N	\N	f	\N	77.96	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
22	1	1	\N	A-001-022	1	22	1053.01	enfermo_critico	establecimiento	\N	\N	0101000020E6100000D31C4385F63F53C000000000000028C0	\N	2025-02-13	2025-06-07	Hass	\N	\N	f	\N	225.37	9	Repellendus velit quo libero vel tempora.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
23	1	1	\N	A-001-023	1	23	1421.58	muerto	vivero	2025-09-28	\N	0101000020E6100000EF05B411F63F53C000000000000028C0	2334.00	2025-03-21	2026-07-17	Ettinger	\N	\N	f	\N	13.46	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
24	1	1	\N	A-001-024	1	24	1583.64	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000C5EE249EF53F53C000000000000028C0	\N	2021-11-08	2026-06-29	Hass	\N	\N	f	\N	188.46	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
25	1	1	\N	A-001-025	1	25	1237.08	erradicado	produccion_madura	2025-12-16	\N	0101000020E6100000E1D7952AF53F53C000000000000028C0	1459.66	2024-03-18	2026-07-21	Zutano	\N	\N	f	\N	320.93	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
26	1	1	\N	A-001-026	1	26	969.94	muerto	produccion_madura	2025-09-24	\N	0101000020E6100000B7C006B7F43F53C000000000000028C0	\N	2022-01-06	2023-10-27	Ettinger	\N	\N	f	\N	462.11	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
27	1	1	\N	A-001-027	1	27	1477.24	enfermo_critico	establecimiento	\N	\N	0101000020E6100000D3A97743F43F53C000000000000028C0	902.35	2022-01-03	\N	Ettinger	\N	\N	f	\N	297.59	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
28	1	1	\N	A-001-028	1	28	1312.37	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000A892E8CFF33F53C000000000000028C0	2280.88	2023-04-13	2025-07-05	Ettinger	\N	\N	f	\N	454.40	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
29	1	1	\N	A-001-029	1	29	1792.99	muerto	produccion_madura	2026-01-27	\N	0101000020E6100000C47B595CF33F53C000000000000028C0	1522.98	2022-03-22	\N	Hass	\N	\N	f	\N	488.64	4	Ab saepe quo excepturi.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
30	1	1	\N	A-001-030	1	30	899.68	excelente	vivero	\N	\N	0101000020E6100000E164CAE8F23F53C000000000000028C0	\N	2025-12-16	2026-05-12	Ettinger	\N	\N	f	\N	249.73	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
31	1	1	\N	A-001-031	1	31	1326.98	muerto	senescencia	2026-07-16	\N	0101000020E6100000B64D3B75F23F53C000000000000028C0	1155.04	2026-03-08	2026-04-21	Ettinger	\N	\N	f	\N	29.73	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
32	1	1	\N	A-001-032	1	32	2435.18	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000D236AC01F23F53C000000000000028C0	1802.62	2023-02-03	2024-07-25	Ettinger	\N	\N	f	\N	310.74	7	Totam aut maxime aut.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
33	1	1	\N	A-001-033	1	33	1984.57	con_estres	produccion_madura	\N	\N	0101000020E6100000A81F1D8EF13F53C000000000000028C0	\N	2023-07-12	2025-11-14	Hass	\N	\N	f	\N	486.62	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
34	1	1	\N	A-001-034	1	34	2233.55	con_estres	senescencia	\N	\N	0101000020E6100000C4088E1AF13F53C000000000000028C0	\N	2022-08-01	\N	Fuerte	\N	\N	f	\N	312.96	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
35	1	1	\N	A-001-035	1	35	873.35	muerto	senescencia	2026-05-14	\N	0101000020E61000009AF1FEA6F03F53C000000000000028C0	838.95	2024-01-25	2024-02-11	Hass	\N	\N	f	\N	390.88	2	Enim eaque sit qui blanditiis.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
36	1	1	\N	A-001-036	1	36	850.54	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000B6DA6F33F03F53C000000000000028C0	1144.86	2023-05-03	2025-08-16	Hass	\N	\N	f	\N	374.19	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
37	1	1	\N	A-001-037	1	37	1787.37	erradicado	produccion_madura	2025-09-28	\N	0101000020E6100000D2C3E0BFEF3F53C000000000000028C0	\N	2022-07-28	2023-11-18	Ettinger	\N	\N	f	\N	340.01	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
38	1	1	\N	A-001-038	1	38	1010.50	enfermo_critico	senescencia	\N	\N	0101000020E6100000A7AC514CEF3F53C000000000000028C0	1551.05	2023-07-05	\N	Ettinger	\N	\N	f	\N	119.96	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
39	1	1	\N	A-001-039	1	39	1592.42	excelente	vivero	\N	\N	0101000020E6100000C395C2D8EE3F53C000000000000028C0	1864.64	2024-09-30	2025-05-26	Bacon	\N	\N	f	\N	5.88	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
40	1	1	\N	A-001-040	1	40	1521.60	enfermo_critico	senescencia	\N	\N	0101000020E6100000997E3365EE3F53C000000000000028C0	1099.34	2024-09-03	\N	Bacon	\N	\N	f	\N	33.16	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
41	1	1	\N	A-002-001	2	1	2192.10	erradicado	produccion_madura	2025-09-10	\N	0101000020E610000000000000004053C09DF9BA77FCFF27C0	1484.19	2024-03-02	2025-11-28	Bacon	\N	\N	f	\N	399.22	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
42	1	1	\N	A-002-002	2	2	1489.79	erradicado	vivero	2026-03-09	\N	0101000020E61000001CE9708CFF3F53C09DF9BA77FCFF27C0	2084.45	2024-06-02	2025-10-15	Ettinger	\N	\N	t	\N	50.53	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
43	1	1	\N	A-002-003	2	3	1435.21	excelente	senescencia	\N	\N	0101000020E6100000F2D1E118FF3F53C09DF9BA77FCFF27C0	2213.15	2026-07-19	2026-07-21	Bacon	\N	\N	f	\N	240.82	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
44	1	1	\N	A-002-004	2	4	2090.25	con_estres	vivero	\N	\N	0101000020E61000000EBB52A5FE3F53C09DF9BA77FCFF27C0	\N	2022-07-23	2025-04-22	Zutano	\N	\N	f	\N	214.42	2	Dolorum perspiciatis laborum quibusdam.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
45	1	1	\N	A-002-005	2	5	900.80	excelente	produccion_madura	\N	\N	0101000020E6100000E3A3C331FE3F53C09DF9BA77FCFF27C0	2234.64	2022-04-01	\N	Zutano	\N	\N	f	\N	249.72	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
46	1	1	\N	A-002-006	2	6	1582.31	muerto	desarrollo_inmaduro	2025-11-02	\N	0101000020E6100000FF8C34BEFD3F53C09DF9BA77FCFF27C0	\N	2021-11-09	2022-11-28	Hass	\N	\N	t	\N	209.60	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
47	1	1	\N	A-002-007	2	7	1053.56	muerto	establecimiento	2026-06-22	\N	0101000020E6100000D575A54AFD3F53C09DF9BA77FCFF27C0	1564.84	2022-07-11	\N	Zutano	\N	\N	f	\N	338.30	3	Qui aut voluptates alias qui adipisci incidunt repudiandae in.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
48	1	1	\N	A-002-008	2	8	1171.33	con_estres	vivero	\N	\N	0101000020E6100000F15E16D7FC3F53C09DF9BA77FCFF27C0	1731.14	2023-05-05	\N	Hass	\N	\N	t	\N	149.97	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
49	1	1	\N	A-002-009	2	9	1959.81	erradicado	senescencia	2026-01-24	\N	0101000020E61000000D488763FC3F53C09DF9BA77FCFF27C0	2094.80	2024-06-05	2024-07-21	Ettinger	\N	\N	f	\N	208.83	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
50	1	1	\N	A-002-010	2	10	2412.53	con_estres	vivero	\N	\N	0101000020E6100000E330F8EFFB3F53C09DF9BA77FCFF27C0	1098.78	2025-07-07	2026-05-01	Hass	\N	\N	f	\N	143.36	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
51	1	1	\N	A-002-011	2	11	1174.98	excelente	vivero	\N	\N	0101000020E6100000FF19697CFB3F53C09DF9BA77FCFF27C0	921.21	2023-10-11	2024-04-02	Bacon	\N	\N	f	\N	160.21	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
52	1	1	\N	A-002-012	2	12	2319.02	erradicado	establecimiento	2026-08-07	\N	0101000020E6100000D502DA08FB3F53C09DF9BA77FCFF27C0	\N	2026-01-10	2026-06-27	Ettinger	\N	\N	f	\N	48.87	10	Consequatur consequuntur et voluptatem aliquid.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
53	1	1	\N	A-002-013	2	13	2143.86	muerto	desarrollo_inmaduro	2026-04-18	\N	0101000020E6100000F1EB4A95FA3F53C09DF9BA77FCFF27C0	1016.21	2026-03-19	2026-06-19	Bacon	\N	\N	f	\N	427.65	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
54	1	1	\N	A-002-014	2	14	2347.99	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000C6D4BB21FA3F53C09DF9BA77FCFF27C0	1515.20	2023-05-10	2023-08-31	Bacon	\N	\N	f	\N	397.76	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
55	1	1	\N	A-002-015	2	15	2132.18	muerto	desarrollo_inmaduro	2026-03-27	\N	0101000020E6100000E2BD2CAEF93F53C09DF9BA77FCFF27C0	1749.32	2022-12-07	2024-08-05	Ettinger	\N	\N	f	\N	250.61	9	Id maiores sit adipisci nostrum vel eos et.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
56	1	1	\N	A-002-016	2	16	2105.56	excelente	produccion_madura	\N	\N	0101000020E6100000FEA69D3AF93F53C09DF9BA77FCFF27C0	2255.32	2024-07-31	\N	Hass	\N	\N	f	\N	77.56	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
57	1	1	\N	A-002-017	2	17	2212.77	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000D48F0EC7F83F53C09DF9BA77FCFF27C0	1071.42	2026-02-20	2026-04-22	Zutano	\N	\N	f	\N	238.38	6	Nihil sed maxime ut voluptatem consectetur corrupti.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
58	1	1	\N	A-002-018	2	18	956.84	muerto	vivero	2025-09-09	\N	0101000020E6100000F0787F53F83F53C09DF9BA77FCFF27C0	1619.34	2025-06-29	\N	Hass	\N	\N	f	\N	475.22	4	Necessitatibus debitis qui officia eum voluptas dolorem minima.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
59	1	1	\N	A-002-019	2	19	1280.36	excelente	produccion_madura	\N	\N	0101000020E6100000C661F0DFF73F53C09DF9BA77FCFF27C0	1096.72	2023-04-13	2026-08-16	Ettinger	\N	\N	f	\N	202.89	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
60	1	1	\N	A-002-020	2	20	2302.71	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000E24A616CF73F53C09DF9BA77FCFF27C0	2186.56	2024-02-16	\N	Hass	\N	\N	f	\N	481.63	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
61	1	1	\N	A-002-021	2	21	1260.85	con_estres	senescencia	\N	\N	0101000020E6100000B733D2F8F63F53C09DF9BA77FCFF27C0	805.33	2024-09-13	2025-08-26	Bacon	\N	\N	t	\N	365.09	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
62	1	1	\N	A-002-022	2	22	1503.76	erradicado	desarrollo_inmaduro	2026-04-15	\N	0101000020E6100000D31C4385F63F53C09DF9BA77FCFF27C0	1153.68	2023-08-10	2026-01-28	Ettinger	\N	\N	f	\N	199.98	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
63	1	1	\N	A-002-023	2	23	805.47	muerto	vivero	2026-07-23	\N	0101000020E6100000EF05B411F63F53C09DF9BA77FCFF27C0	2127.76	2024-10-03	2026-07-29	Fuerte	\N	\N	f	\N	230.38	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
64	1	1	\N	A-002-024	2	24	1052.45	erradicado	desarrollo_inmaduro	2025-10-20	\N	0101000020E6100000C5EE249EF53F53C09DF9BA77FCFF27C0	984.43	2023-01-06	2025-12-05	Zutano	\N	\N	f	\N	432.48	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
65	1	1	\N	A-002-025	2	25	1211.60	excelente	produccion_madura	\N	\N	0101000020E6100000E1D7952AF53F53C09DF9BA77FCFF27C0	2145.11	2022-04-14	2026-02-11	Bacon	\N	\N	f	\N	346.57	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
66	1	1	\N	A-002-026	2	26	980.14	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000B7C006B7F43F53C09DF9BA77FCFF27C0	2425.80	2023-12-14	2025-12-04	Bacon	\N	\N	f	\N	1.00	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
67	1	1	\N	A-002-027	2	27	2482.42	excelente	establecimiento	\N	\N	0101000020E6100000D3A97743F43F53C09DF9BA77FCFF27C0	2363.78	2025-10-07	2026-07-12	Ettinger	\N	\N	f	\N	105.00	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
68	1	1	\N	A-002-028	2	28	2339.50	erradicado	establecimiento	2026-03-22	\N	0101000020E6100000A892E8CFF33F53C09DF9BA77FCFF27C0	1984.75	2023-11-16	2024-04-28	Zutano	\N	\N	f	\N	376.66	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
69	1	1	\N	A-002-029	2	29	1594.14	erradicado	senescencia	2026-01-22	\N	0101000020E6100000C47B595CF33F53C09DF9BA77FCFF27C0	957.58	2023-07-29	2024-04-27	Bacon	\N	\N	f	\N	303.22	3	Est ipsa aut nam quas.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
70	1	1	\N	A-002-030	2	30	2297.59	excelente	establecimiento	\N	\N	0101000020E6100000E164CAE8F23F53C09DF9BA77FCFF27C0	2361.39	2023-06-09	\N	Fuerte	\N	\N	f	\N	32.60	9	Occaecati autem inventore fuga qui beatae sit libero et.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
71	1	1	\N	A-002-031	2	31	1299.63	excelente	establecimiento	\N	\N	0101000020E6100000B64D3B75F23F53C09DF9BA77FCFF27C0	1958.36	2022-04-28	2026-08-06	Zutano	\N	\N	f	\N	57.86	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
72	1	1	\N	A-002-032	2	32	864.76	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000D236AC01F23F53C09DF9BA77FCFF27C0	1116.24	2026-01-28	\N	Hass	\N	\N	f	\N	418.59	9	At quidem beatae labore eum.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
73	1	1	\N	A-002-033	2	33	1946.89	muerto	establecimiento	2025-11-21	\N	0101000020E6100000A81F1D8EF13F53C09DF9BA77FCFF27C0	1529.77	2024-10-31	2026-08-17	Fuerte	\N	\N	f	\N	278.00	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
74	1	1	\N	A-002-034	2	34	2468.96	erradicado	produccion_madura	2026-02-07	\N	0101000020E6100000C4088E1AF13F53C09DF9BA77FCFF27C0	1035.07	2023-11-08	2024-04-21	Hass	\N	\N	f	\N	233.06	9	Exercitationem consequatur vel dolorem.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
75	1	1	\N	A-002-035	2	35	1428.54	con_estres	desarrollo_inmaduro	\N	\N	0101000020E61000009AF1FEA6F03F53C09DF9BA77FCFF27C0	2284.06	2023-05-30	\N	Ettinger	\N	\N	f	\N	97.30	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
76	1	1	\N	A-002-036	2	36	2371.58	muerto	establecimiento	2026-02-03	\N	0101000020E6100000B6DA6F33F03F53C09DF9BA77FCFF27C0	915.87	2024-01-28	2026-03-14	Hass	\N	\N	f	\N	259.36	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
77	1	1	\N	A-002-037	2	37	2270.32	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000D2C3E0BFEF3F53C09DF9BA77FCFF27C0	937.13	2026-05-09	2026-07-30	Fuerte	\N	\N	f	\N	327.64	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
78	1	1	\N	A-002-038	2	38	2446.86	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000A7AC514CEF3F53C09DF9BA77FCFF27C0	\N	2024-12-06	2026-07-04	Zutano	\N	\N	f	\N	442.70	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
79	1	1	\N	A-002-039	2	39	2436.97	con_estres	vivero	\N	\N	0101000020E6100000C395C2D8EE3F53C09DF9BA77FCFF27C0	2404.44	2022-08-04	2023-06-08	Fuerte	\N	\N	t	\N	479.01	6	Ad mollitia quo asperiores.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
80	1	1	\N	A-002-040	2	40	1919.24	con_estres	establecimiento	\N	\N	0101000020E6100000997E3365EE3F53C09DF9BA77FCFF27C0	1671.21	2024-11-07	2025-07-09	Fuerte	\N	\N	f	\N	178.12	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
81	1	1	\N	A-003-001	3	1	1244.19	erradicado	vivero	2025-09-20	\N	0101000020E610000000000000004053C03AF375EFF8FF27C0	820.47	2025-03-19	\N	Ettinger	\N	\N	f	\N	208.85	1	Consequatur veniam ipsum et sed vitae minus.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
82	1	1	\N	A-003-002	3	2	837.61	con_estres	desarrollo_inmaduro	\N	\N	0101000020E61000001CE9708CFF3F53C03AF375EFF8FF27C0	961.50	2022-05-07	\N	Bacon	\N	\N	f	\N	33.34	0	Nesciunt illum nobis dolor et unde dolore et.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
83	1	1	\N	A-003-003	3	3	1472.99	erradicado	vivero	2026-01-29	\N	0101000020E6100000F2D1E118FF3F53C03AF375EFF8FF27C0	974.23	2023-12-01	2026-06-08	Bacon	\N	\N	f	\N	480.07	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
84	1	1	\N	A-003-004	3	4	1239.64	con_estres	senescencia	\N	\N	0101000020E61000000EBB52A5FE3F53C03AF375EFF8FF27C0	1282.35	2024-11-22	2026-01-27	Zutano	\N	\N	f	\N	110.34	6	Quos deserunt sunt omnis.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
85	1	1	\N	A-003-005	3	5	931.87	erradicado	senescencia	2026-08-22	\N	0101000020E6100000E3A3C331FE3F53C03AF375EFF8FF27C0	941.42	2022-03-21	\N	Fuerte	\N	\N	t	\N	432.89	8	Doloribus sunt commodi quam culpa.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
86	1	1	\N	A-003-006	3	6	2350.20	muerto	desarrollo_inmaduro	2025-09-22	\N	0101000020E6100000FF8C34BEFD3F53C03AF375EFF8FF27C0	840.29	2025-10-21	2026-05-03	Bacon	\N	\N	f	\N	426.63	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
87	1	1	\N	A-003-007	3	7	1696.61	con_estres	vivero	\N	\N	0101000020E6100000D575A54AFD3F53C03AF375EFF8FF27C0	1457.87	2026-04-04	\N	Zutano	\N	\N	f	\N	444.36	1	Aut est consequatur magnam rerum.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
88	1	1	\N	A-003-008	3	8	1194.04	erradicado	produccion_madura	2026-01-03	\N	0101000020E6100000F15E16D7FC3F53C03AF375EFF8FF27C0	2492.45	2021-11-04	2025-09-18	Fuerte	\N	\N	f	\N	339.77	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
89	1	1	\N	A-003-009	3	9	974.06	erradicado	senescencia	2025-10-25	\N	0101000020E61000000D488763FC3F53C03AF375EFF8FF27C0	\N	2024-06-20	2025-02-14	Fuerte	\N	\N	t	\N	293.94	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
90	1	1	\N	A-003-010	3	10	2499.02	erradicado	senescencia	2026-01-04	\N	0101000020E6100000E330F8EFFB3F53C03AF375EFF8FF27C0	1863.85	2024-09-23	2025-11-02	Fuerte	\N	\N	f	\N	158.94	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
91	1	1	\N	A-003-011	3	11	2015.90	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000FF19697CFB3F53C03AF375EFF8FF27C0	\N	2025-08-19	2026-08-25	Fuerte	\N	\N	f	\N	58.25	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
92	1	1	\N	A-003-012	3	12	1625.82	erradicado	establecimiento	2025-10-22	\N	0101000020E6100000D502DA08FB3F53C03AF375EFF8FF27C0	1430.72	2026-05-05	2026-07-12	Bacon	\N	\N	f	\N	272.41	1	Perferendis nihil laborum maiores ut accusantium.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
93	1	1	\N	A-003-013	3	13	1983.97	muerto	establecimiento	2025-10-21	\N	0101000020E6100000F1EB4A95FA3F53C03AF375EFF8FF27C0	\N	2021-11-04	\N	Zutano	\N	\N	f	\N	388.03	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
94	1	1	\N	A-003-014	3	14	1780.13	con_estres	vivero	\N	\N	0101000020E6100000C6D4BB21FA3F53C03AF375EFF8FF27C0	\N	2024-04-28	\N	Bacon	\N	\N	f	\N	284.08	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
95	1	1	\N	A-003-015	3	15	1355.09	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000E2BD2CAEF93F53C03AF375EFF8FF27C0	924.24	2024-06-10	2025-07-18	Fuerte	\N	\N	f	\N	310.04	4	Quos veniam doloremque omnis.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
96	1	1	\N	A-003-016	3	16	1007.66	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000FEA69D3AF93F53C03AF375EFF8FF27C0	1794.45	2024-10-12	2025-09-21	Ettinger	\N	\N	f	\N	370.62	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
97	1	1	\N	A-003-017	3	17	1264.26	excelente	produccion_madura	\N	\N	0101000020E6100000D48F0EC7F83F53C03AF375EFF8FF27C0	2321.35	2022-04-10	2025-10-04	Ettinger	\N	\N	f	\N	215.15	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
98	1	1	\N	A-003-018	3	18	2013.92	erradicado	vivero	2025-11-04	\N	0101000020E6100000F0787F53F83F53C03AF375EFF8FF27C0	\N	2023-06-24	2023-12-02	Zutano	\N	\N	f	\N	118.42	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
99	1	1	\N	A-003-019	3	19	2427.61	erradicado	desarrollo_inmaduro	2026-04-06	\N	0101000020E6100000C661F0DFF73F53C03AF375EFF8FF27C0	2259.79	2023-10-14	2026-05-23	Fuerte	\N	\N	f	\N	344.08	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
100	1	1	\N	A-003-020	3	20	1367.87	con_estres	vivero	\N	\N	0101000020E6100000E24A616CF73F53C03AF375EFF8FF27C0	1059.43	2024-11-14	2025-03-01	Ettinger	\N	\N	f	\N	379.66	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
101	1	1	\N	A-003-021	3	21	950.41	muerto	senescencia	2026-05-09	\N	0101000020E6100000B733D2F8F63F53C03AF375EFF8FF27C0	1817.81	2025-02-07	\N	Ettinger	\N	\N	f	\N	296.04	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
102	1	1	\N	A-003-022	3	22	1162.68	excelente	produccion_madura	\N	\N	0101000020E6100000D31C4385F63F53C03AF375EFF8FF27C0	1667.46	2026-05-18	2026-07-19	Ettinger	\N	\N	f	\N	426.38	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
103	1	1	\N	A-003-023	3	23	1160.23	con_estres	produccion_madura	\N	\N	0101000020E6100000EF05B411F63F53C03AF375EFF8FF27C0	2484.73	2022-12-14	2023-04-07	Hass	\N	\N	f	\N	359.95	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
104	1	1	\N	A-003-024	3	24	1707.33	con_estres	produccion_madura	\N	\N	0101000020E6100000C5EE249EF53F53C03AF375EFF8FF27C0	1742.29	2025-02-12	2026-01-12	Bacon	\N	\N	f	\N	335.67	0	Saepe qui odio voluptatem quibusdam.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
105	1	1	\N	A-003-025	3	25	1378.70	excelente	vivero	\N	\N	0101000020E6100000E1D7952AF53F53C03AF375EFF8FF27C0	904.48	2022-07-07	2025-12-05	Hass	\N	\N	f	\N	208.73	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
106	1	1	\N	A-003-026	3	26	1226.88	excelente	establecimiento	\N	\N	0101000020E6100000B7C006B7F43F53C03AF375EFF8FF27C0	2274.46	2024-01-08	2026-06-15	Hass	\N	\N	f	\N	243.79	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
107	1	1	\N	A-003-027	3	27	1283.91	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000D3A97743F43F53C03AF375EFF8FF27C0	935.56	2026-01-30	\N	Ettinger	\N	\N	f	\N	306.56	0	Et placeat voluptate possimus corporis.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
108	1	1	\N	A-003-028	3	28	981.12	enfermo_critico	establecimiento	\N	\N	0101000020E6100000A892E8CFF33F53C03AF375EFF8FF27C0	2165.03	2024-02-19	\N	Hass	\N	\N	f	\N	406.80	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
109	1	1	\N	A-003-029	3	29	1292.66	excelente	senescencia	\N	\N	0101000020E6100000C47B595CF33F53C03AF375EFF8FF27C0	2468.83	2025-07-06	2026-06-30	Zutano	\N	\N	t	\N	189.28	10	Sit in a beatae laboriosam praesentium ut.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
110	1	1	\N	A-003-030	3	30	2325.53	con_estres	produccion_madura	\N	\N	0101000020E6100000E164CAE8F23F53C03AF375EFF8FF27C0	\N	2023-01-14	2023-10-19	Fuerte	\N	\N	f	\N	239.51	0	Corporis magni et quia aut voluptas iste sint.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
111	1	1	\N	A-003-031	3	31	2461.98	muerto	vivero	2025-12-14	\N	0101000020E6100000B64D3B75F23F53C03AF375EFF8FF27C0	\N	2026-05-19	2026-06-04	Fuerte	\N	\N	f	\N	276.71	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
112	1	1	\N	A-003-032	3	32	919.85	con_estres	establecimiento	\N	\N	0101000020E6100000D236AC01F23F53C03AF375EFF8FF27C0	1972.40	2024-04-12	2025-02-15	Fuerte	\N	\N	f	\N	266.83	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
113	1	1	\N	A-003-033	3	33	1109.96	enfermo_critico	senescencia	\N	\N	0101000020E6100000A81F1D8EF13F53C03AF375EFF8FF27C0	1922.90	2024-03-31	2025-05-25	Hass	\N	\N	f	\N	141.28	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
114	1	1	\N	A-003-034	3	34	1128.80	erradicado	desarrollo_inmaduro	2025-09-01	\N	0101000020E6100000C4088E1AF13F53C03AF375EFF8FF27C0	1842.77	2024-01-10	2025-09-28	Zutano	\N	\N	f	\N	32.61	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
115	1	1	\N	A-003-035	3	35	1946.76	enfermo_critico	vivero	\N	\N	0101000020E61000009AF1FEA6F03F53C03AF375EFF8FF27C0	1943.56	2024-09-05	\N	Fuerte	\N	\N	f	\N	140.90	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
116	1	1	\N	A-003-036	3	36	1036.17	con_estres	establecimiento	\N	\N	0101000020E6100000B6DA6F33F03F53C03AF375EFF8FF27C0	\N	2023-07-02	2024-02-24	Hass	\N	\N	t	\N	181.39	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
117	1	1	\N	A-003-037	3	37	2048.42	con_estres	produccion_madura	\N	\N	0101000020E6100000D2C3E0BFEF3F53C03AF375EFF8FF27C0	2165.49	2024-01-12	\N	Hass	\N	\N	f	\N	153.48	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
118	1	1	\N	A-003-038	3	38	1928.36	muerto	produccion_madura	2025-12-19	\N	0101000020E6100000A7AC514CEF3F53C03AF375EFF8FF27C0	2231.51	2024-05-02	2024-06-19	Fuerte	\N	\N	f	\N	321.31	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
119	1	1	\N	A-003-039	3	39	2246.54	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000C395C2D8EE3F53C03AF375EFF8FF27C0	1623.66	2023-11-07	2025-02-15	Ettinger	\N	\N	f	\N	206.44	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
120	1	1	\N	A-003-040	3	40	2486.60	excelente	establecimiento	\N	\N	0101000020E6100000997E3365EE3F53C03AF375EFF8FF27C0	\N	2025-09-20	2025-10-01	Fuerte	\N	\N	f	\N	227.23	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
121	1	1	\N	A-004-001	4	1	1922.97	excelente	vivero	\N	\N	0101000020E610000000000000004053C0A4EA3067F5FF27C0	1235.02	2022-06-05	2024-02-29	Hass	\N	\N	f	\N	156.47	10	Blanditiis itaque mollitia laboriosam iste aut consectetur.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
122	1	1	\N	A-004-002	4	2	1535.60	excelente	establecimiento	\N	\N	0101000020E61000001CE9708CFF3F53C0A4EA3067F5FF27C0	1426.06	2021-12-01	2024-08-19	Bacon	\N	\N	f	\N	498.35	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
123	1	1	\N	A-004-003	4	3	2351.21	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000F2D1E118FF3F53C0A4EA3067F5FF27C0	2331.73	2022-06-07	2025-10-12	Hass	\N	\N	f	\N	344.43	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
124	1	1	\N	A-004-004	4	4	1026.45	muerto	desarrollo_inmaduro	2025-12-19	\N	0101000020E61000000EBB52A5FE3F53C0A4EA3067F5FF27C0	\N	2025-11-08	\N	Bacon	\N	\N	f	\N	235.10	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
125	1	1	\N	A-004-005	4	5	931.46	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000E3A3C331FE3F53C0A4EA3067F5FF27C0	\N	2023-05-04	\N	Zutano	\N	\N	f	\N	9.30	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
126	1	1	\N	A-004-006	4	6	2429.10	excelente	vivero	\N	\N	0101000020E6100000FF8C34BEFD3F53C0A4EA3067F5FF27C0	1347.46	2024-12-04	2025-01-12	Ettinger	\N	\N	f	\N	433.82	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
127	1	1	\N	A-004-007	4	7	1753.40	enfermo_critico	senescencia	\N	\N	0101000020E6100000D575A54AFD3F53C0A4EA3067F5FF27C0	\N	2026-07-06	2026-08-12	Hass	\N	\N	f	\N	157.20	7	Libero accusantium eaque est voluptatem vitae ipsam.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
128	1	1	\N	A-004-008	4	8	875.26	erradicado	produccion_madura	2025-08-29	\N	0101000020E6100000F15E16D7FC3F53C0A4EA3067F5FF27C0	1850.74	2024-06-27	2024-12-23	Ettinger	\N	\N	f	\N	431.85	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
129	1	1	\N	A-004-009	4	9	2179.96	muerto	desarrollo_inmaduro	2026-06-05	\N	0101000020E61000000D488763FC3F53C0A4EA3067F5FF27C0	1868.00	2021-12-03	\N	Zutano	\N	\N	f	\N	470.04	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
130	1	1	\N	A-004-010	4	10	1835.77	enfermo_critico	establecimiento	\N	\N	0101000020E6100000E330F8EFFB3F53C0A4EA3067F5FF27C0	2166.71	2023-09-12	2023-12-12	Ettinger	\N	\N	f	\N	453.06	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
131	1	1	\N	A-004-011	4	11	1747.97	con_estres	senescencia	\N	\N	0101000020E6100000FF19697CFB3F53C0A4EA3067F5FF27C0	1868.03	2026-02-20	2026-03-13	Fuerte	\N	\N	f	\N	61.85	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
132	1	1	\N	A-004-012	4	12	1424.78	enfermo_critico	vivero	\N	\N	0101000020E6100000D502DA08FB3F53C0A4EA3067F5FF27C0	1161.36	2026-02-18	2026-08-14	Hass	\N	\N	f	\N	358.86	7	Hic iusto delectus repellendus temporibus.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
133	1	1	\N	A-004-013	4	13	1140.68	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000F1EB4A95FA3F53C0A4EA3067F5FF27C0	1481.61	2023-03-23	2023-08-19	Zutano	\N	\N	f	\N	364.19	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
134	1	1	\N	A-004-014	4	14	912.34	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000C6D4BB21FA3F53C0A4EA3067F5FF27C0	1282.89	2024-10-18	2025-03-10	Hass	\N	\N	f	\N	48.48	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
135	1	1	\N	A-004-015	4	15	2013.09	enfermo_critico	vivero	\N	\N	0101000020E6100000E2BD2CAEF93F53C0A4EA3067F5FF27C0	2057.12	2022-09-03	2023-03-04	Hass	\N	\N	f	\N	87.74	8	Occaecati pariatur tempora earum illum quia ut tenetur.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
136	1	1	\N	A-004-016	4	16	1542.05	con_estres	vivero	\N	\N	0101000020E6100000FEA69D3AF93F53C0A4EA3067F5FF27C0	2183.45	2022-10-22	2024-09-01	Hass	\N	\N	f	\N	204.47	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
137	1	1	\N	A-004-017	4	17	2308.65	muerto	senescencia	2026-01-25	\N	0101000020E6100000D48F0EC7F83F53C0A4EA3067F5FF27C0	1988.32	2023-10-01	\N	Ettinger	\N	\N	f	\N	80.89	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
138	1	1	\N	A-004-018	4	18	1882.48	erradicado	produccion_madura	2026-06-25	\N	0101000020E6100000F0787F53F83F53C0A4EA3067F5FF27C0	1247.07	2023-06-04	2023-11-24	Fuerte	\N	\N	f	\N	447.83	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
139	1	1	\N	A-004-019	4	19	2265.17	excelente	produccion_madura	\N	\N	0101000020E6100000C661F0DFF73F53C0A4EA3067F5FF27C0	1296.64	2024-08-20	2025-12-06	Zutano	\N	\N	f	\N	255.10	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
140	1	1	\N	A-004-020	4	20	1332.24	enfermo_critico	vivero	\N	\N	0101000020E6100000E24A616CF73F53C0A4EA3067F5FF27C0	2474.09	2026-03-20	2026-05-24	Fuerte	\N	\N	f	\N	469.77	9	Et ut ea laborum veritatis ab ut.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
141	1	1	\N	A-004-021	4	21	2122.54	con_estres	vivero	\N	\N	0101000020E6100000B733D2F8F63F53C0A4EA3067F5FF27C0	2418.90	2024-04-17	2025-01-12	Hass	\N	\N	f	\N	355.86	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
142	1	1	\N	A-004-022	4	22	1974.67	enfermo_critico	establecimiento	\N	\N	0101000020E6100000D31C4385F63F53C0A4EA3067F5FF27C0	\N	2022-11-12	\N	Hass	\N	\N	f	\N	488.50	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
143	1	1	\N	A-004-023	4	23	880.00	erradicado	desarrollo_inmaduro	2026-07-31	\N	0101000020E6100000EF05B411F63F53C0A4EA3067F5FF27C0	1179.38	2024-10-09	2025-08-10	Bacon	\N	\N	f	\N	304.98	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
144	1	1	\N	A-004-024	4	24	2263.21	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000C5EE249EF53F53C0A4EA3067F5FF27C0	1029.13	2023-11-14	\N	Ettinger	\N	\N	f	\N	223.12	8	Cumque fugiat veniam aperiam voluptas porro dolores quibusdam qui.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
145	1	1	\N	A-004-025	4	25	972.93	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000E1D7952AF53F53C0A4EA3067F5FF27C0	\N	2021-11-25	2023-08-20	Fuerte	\N	\N	f	\N	474.88	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
146	1	1	\N	A-004-026	4	26	1979.66	muerto	produccion_madura	2026-05-26	\N	0101000020E6100000B7C006B7F43F53C0A4EA3067F5FF27C0	\N	2023-02-20	2025-03-14	Zutano	\N	\N	f	\N	393.28	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
147	1	1	\N	A-004-027	4	27	1984.30	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000D3A97743F43F53C0A4EA3067F5FF27C0	2331.24	2024-06-17	\N	Ettinger	\N	\N	f	\N	103.89	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
148	1	1	\N	A-004-028	4	28	853.36	erradicado	establecimiento	2026-04-09	\N	0101000020E6100000A892E8CFF33F53C0A4EA3067F5FF27C0	1422.26	2026-06-01	2026-08-01	Bacon	\N	\N	f	\N	151.09	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
149	1	1	\N	A-004-029	4	29	968.71	enfermo_critico	establecimiento	\N	\N	0101000020E6100000C47B595CF33F53C0A4EA3067F5FF27C0	2249.52	2026-02-04	2026-08-18	Bacon	\N	\N	f	\N	339.15	0	Autem beatae nulla quis sit.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
150	1	1	\N	A-004-030	4	30	1947.89	excelente	vivero	\N	\N	0101000020E6100000E164CAE8F23F53C0A4EA3067F5FF27C0	1945.75	2024-06-09	2025-10-18	Bacon	\N	\N	f	\N	480.28	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
151	1	1	\N	A-004-031	4	31	2378.90	excelente	establecimiento	\N	\N	0101000020E6100000B64D3B75F23F53C0A4EA3067F5FF27C0	1784.64	2026-03-20	2026-03-26	Hass	\N	\N	f	\N	158.79	5	Aut ad sit consectetur omnis.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
152	1	1	\N	A-004-032	4	32	1589.83	erradicado	desarrollo_inmaduro	2025-08-28	\N	0101000020E6100000D236AC01F23F53C0A4EA3067F5FF27C0	1577.00	2023-04-11	2025-05-03	Hass	\N	\N	f	\N	137.73	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
153	1	1	\N	A-004-033	4	33	2404.75	muerto	desarrollo_inmaduro	2026-06-12	\N	0101000020E6100000A81F1D8EF13F53C0A4EA3067F5FF27C0	1890.33	2024-11-27	2026-05-28	Bacon	\N	\N	f	\N	233.11	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
154	1	1	\N	A-004-034	4	34	1847.68	muerto	produccion_madura	2026-03-30	\N	0101000020E6100000C4088E1AF13F53C0A4EA3067F5FF27C0	953.50	2021-10-05	2024-06-05	Fuerte	\N	\N	f	\N	65.55	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
155	1	1	\N	A-004-035	4	35	872.01	erradicado	vivero	2026-08-04	\N	0101000020E61000009AF1FEA6F03F53C0A4EA3067F5FF27C0	\N	2024-06-10	\N	Fuerte	\N	\N	f	\N	40.73	1	Non repellendus voluptas sint quia voluptatum quidem pariatur.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
156	1	1	\N	A-004-036	4	36	2061.32	muerto	senescencia	2026-03-17	\N	0101000020E6100000B6DA6F33F03F53C0A4EA3067F5FF27C0	1722.75	2026-04-30	\N	Hass	\N	\N	f	\N	361.48	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
157	1	1	\N	A-004-037	4	37	1935.97	con_estres	senescencia	\N	\N	0101000020E6100000D2C3E0BFEF3F53C0A4EA3067F5FF27C0	1867.40	2026-07-02	\N	Ettinger	\N	\N	f	\N	463.39	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
158	1	1	\N	A-004-038	4	38	1285.45	erradicado	senescencia	2025-12-29	\N	0101000020E6100000A7AC514CEF3F53C0A4EA3067F5FF27C0	1350.95	2026-04-02	2026-04-17	Zutano	\N	\N	f	\N	53.63	9	Non sit odio consequuntur deserunt perferendis.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
159	1	1	\N	A-004-039	4	39	2232.87	muerto	produccion_madura	2025-11-14	\N	0101000020E6100000C395C2D8EE3F53C0A4EA3067F5FF27C0	2365.02	2022-02-02	2023-01-13	Ettinger	\N	\N	f	\N	201.13	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
160	1	1	\N	A-004-040	4	40	1057.90	muerto	establecimiento	2025-09-11	\N	0101000020E6100000997E3365EE3F53C0A4EA3067F5FF27C0	\N	2025-03-24	2026-01-20	Hass	\N	\N	f	\N	399.50	10	Voluptatibus eligendi provident omnis incidunt est.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
161	1	1	\N	A-005-001	5	1	2229.08	excelente	produccion_madura	\N	\N	0101000020E610000000000000004053C041E4EBDEF1FF27C0	\N	2024-12-23	2026-05-10	Zutano	\N	\N	f	\N	292.39	0	In ea et adipisci repellat unde minus.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
162	1	1	\N	A-005-002	5	2	1226.29	muerto	vivero	2026-01-18	\N	0101000020E61000001CE9708CFF3F53C041E4EBDEF1FF27C0	\N	2026-02-15	2026-04-25	Zutano	\N	\N	f	\N	182.52	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
163	1	1	\N	A-005-003	5	3	2499.73	excelente	vivero	\N	\N	0101000020E6100000F2D1E118FF3F53C041E4EBDEF1FF27C0	\N	2023-06-24	\N	Zutano	\N	\N	f	\N	484.12	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
164	1	1	\N	A-005-004	5	4	1164.93	excelente	produccion_madura	\N	\N	0101000020E61000000EBB52A5FE3F53C041E4EBDEF1FF27C0	2111.24	2025-08-05	\N	Hass	\N	\N	f	\N	49.87	10	Voluptatem et quos enim ab qui aut.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
165	1	1	\N	A-005-005	5	5	1149.32	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000E3A3C331FE3F53C041E4EBDEF1FF27C0	881.12	2023-01-13	2024-05-23	Hass	\N	\N	f	\N	52.96	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
166	1	1	\N	A-005-006	5	6	1481.19	muerto	establecimiento	2026-03-18	\N	0101000020E6100000FF8C34BEFD3F53C041E4EBDEF1FF27C0	\N	2023-10-10	2023-12-15	Zutano	\N	\N	f	\N	192.93	9	Soluta non repellendus autem molestias.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
167	1	1	\N	A-005-007	5	7	2493.70	muerto	produccion_madura	2026-02-28	\N	0101000020E6100000D575A54AFD3F53C041E4EBDEF1FF27C0	879.66	2025-10-23	\N	Hass	\N	\N	f	\N	208.30	7	Saepe qui velit rerum sunt vitae nulla voluptatum ad.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
168	1	1	\N	A-005-008	5	8	2311.00	muerto	produccion_madura	2026-04-06	\N	0101000020E6100000F15E16D7FC3F53C041E4EBDEF1FF27C0	1419.66	2024-08-31	2025-08-01	Bacon	\N	\N	f	\N	72.26	7	Aliquid exercitationem optio quia hic quis quia.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
169	1	1	\N	A-005-009	5	9	1612.48	enfermo_critico	establecimiento	\N	\N	0101000020E61000000D488763FC3F53C041E4EBDEF1FF27C0	\N	2026-01-04	2026-07-23	Hass	\N	\N	f	\N	105.70	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
170	1	1	\N	A-005-010	5	10	2147.22	con_estres	produccion_madura	\N	\N	0101000020E6100000E330F8EFFB3F53C041E4EBDEF1FF27C0	2339.24	2024-02-26	2025-01-21	Zutano	\N	\N	f	\N	424.96	8	Molestiae natus consequuntur rem non id.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
171	1	1	\N	A-005-011	5	11	2106.63	erradicado	produccion_madura	2025-11-10	\N	0101000020E6100000FF19697CFB3F53C041E4EBDEF1FF27C0	1229.87	2022-03-02	2026-02-14	Hass	\N	\N	f	\N	344.31	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
172	1	1	\N	A-005-012	5	12	1186.28	con_estres	senescencia	\N	\N	0101000020E6100000D502DA08FB3F53C041E4EBDEF1FF27C0	865.29	2022-05-07	2024-05-29	Bacon	\N	\N	f	\N	105.44	6	Nihil dicta ipsa quam quis.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
173	1	1	\N	A-005-013	5	13	2328.27	muerto	establecimiento	2026-08-07	\N	0101000020E6100000F1EB4A95FA3F53C041E4EBDEF1FF27C0	2381.06	2026-05-14	2026-06-21	Ettinger	\N	\N	f	\N	2.96	5	Et in reiciendis neque ut culpa dolor.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
174	1	1	\N	A-005-014	5	14	830.10	erradicado	desarrollo_inmaduro	2026-08-23	\N	0101000020E6100000C6D4BB21FA3F53C041E4EBDEF1FF27C0	2083.54	2021-10-03	2026-05-13	Ettinger	\N	\N	f	\N	24.19	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
175	1	1	\N	A-005-015	5	15	2213.04	erradicado	produccion_madura	2025-09-24	\N	0101000020E6100000E2BD2CAEF93F53C041E4EBDEF1FF27C0	1393.86	2023-10-17	2025-12-30	Fuerte	\N	\N	f	\N	58.93	5	At delectus deserunt ducimus nisi numquam aut.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
176	1	1	\N	A-005-016	5	16	1641.77	muerto	produccion_madura	2025-10-11	\N	0101000020E6100000FEA69D3AF93F53C041E4EBDEF1FF27C0	1382.63	2022-10-24	2026-06-22	Hass	\N	\N	t	\N	322.28	8	Sequi aliquam rem aut id et.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
177	1	1	\N	A-005-017	5	17	1957.09	muerto	establecimiento	2025-09-22	\N	0101000020E6100000D48F0EC7F83F53C041E4EBDEF1FF27C0	1549.64	2024-12-04	2024-12-06	Bacon	\N	\N	f	\N	296.71	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
178	1	1	\N	A-005-018	5	18	915.04	erradicado	produccion_madura	2025-09-06	\N	0101000020E6100000F0787F53F83F53C041E4EBDEF1FF27C0	1179.17	2024-06-24	2026-08-20	Bacon	\N	\N	f	\N	210.77	9	Quam cupiditate sit perspiciatis quidem tempore voluptatibus.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
179	1	1	\N	A-005-019	5	19	1536.18	erradicado	desarrollo_inmaduro	2026-05-25	\N	0101000020E6100000C661F0DFF73F53C041E4EBDEF1FF27C0	1639.89	2023-07-06	\N	Zutano	\N	\N	f	\N	440.43	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
180	1	1	\N	A-005-020	5	20	1127.40	excelente	produccion_madura	\N	\N	0101000020E6100000E24A616CF73F53C041E4EBDEF1FF27C0	2460.57	2022-11-27	2026-01-18	Ettinger	\N	\N	f	\N	327.22	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
181	1	1	\N	A-005-021	5	21	1445.10	excelente	produccion_madura	\N	\N	0101000020E6100000B733D2F8F63F53C041E4EBDEF1FF27C0	\N	2025-07-05	2026-01-07	Bacon	\N	\N	f	\N	46.78	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
182	1	1	\N	A-005-022	5	22	1796.45	excelente	produccion_madura	\N	\N	0101000020E6100000D31C4385F63F53C041E4EBDEF1FF27C0	1294.35	2024-07-31	\N	Ettinger	\N	\N	f	\N	299.91	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
183	1	1	\N	A-005-023	5	23	1517.21	excelente	establecimiento	\N	\N	0101000020E6100000EF05B411F63F53C041E4EBDEF1FF27C0	1089.05	2021-11-06	2025-07-07	Hass	\N	\N	f	\N	101.14	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
184	1	1	\N	A-005-024	5	24	1244.96	muerto	desarrollo_inmaduro	2025-10-20	\N	0101000020E6100000C5EE249EF53F53C041E4EBDEF1FF27C0	\N	2024-12-27	2025-06-15	Fuerte	\N	\N	f	\N	276.98	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
185	1	1	\N	A-005-025	5	25	1422.65	erradicado	establecimiento	2026-01-03	\N	0101000020E6100000E1D7952AF53F53C041E4EBDEF1FF27C0	1804.51	2022-06-07	\N	Zutano	\N	\N	t	\N	365.22	3	Inventore quos consequuntur esse neque enim eos.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
186	1	1	\N	A-005-026	5	26	1032.48	excelente	produccion_madura	\N	\N	0101000020E6100000B7C006B7F43F53C041E4EBDEF1FF27C0	1911.83	2023-12-02	\N	Hass	\N	\N	f	\N	369.90	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
187	1	1	\N	A-005-027	5	27	1912.11	excelente	senescencia	\N	\N	0101000020E6100000D3A97743F43F53C041E4EBDEF1FF27C0	2372.22	2023-05-20	2024-03-05	Fuerte	\N	\N	f	\N	242.95	4	Necessitatibus atque exercitationem consequatur totam nisi facere mollitia.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
188	1	1	\N	A-005-028	5	28	1353.93	excelente	establecimiento	\N	\N	0101000020E6100000A892E8CFF33F53C041E4EBDEF1FF27C0	1028.07	2025-03-05	2025-03-20	Zutano	\N	\N	f	\N	397.57	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
189	1	1	\N	A-005-029	5	29	1108.88	erradicado	desarrollo_inmaduro	2025-10-07	\N	0101000020E6100000C47B595CF33F53C041E4EBDEF1FF27C0	1553.88	2024-06-28	\N	Bacon	\N	\N	f	\N	404.94	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
190	1	1	\N	A-005-030	5	30	817.76	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000E164CAE8F23F53C041E4EBDEF1FF27C0	\N	2025-09-03	\N	Fuerte	\N	\N	f	\N	17.79	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
191	1	1	\N	A-005-031	5	31	1687.35	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000B64D3B75F23F53C041E4EBDEF1FF27C0	1445.82	2025-01-09	\N	Fuerte	\N	\N	f	\N	204.34	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
192	1	1	\N	A-005-032	5	32	1868.11	erradicado	establecimiento	2026-06-11	\N	0101000020E6100000D236AC01F23F53C041E4EBDEF1FF27C0	1530.50	2022-09-13	\N	Zutano	\N	\N	f	\N	464.74	0	Sit in voluptatem facilis corporis.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
193	1	1	\N	A-005-033	5	33	1059.79	muerto	senescencia	2025-10-27	\N	0101000020E6100000A81F1D8EF13F53C041E4EBDEF1FF27C0	1489.01	2022-02-17	2022-12-14	Fuerte	\N	\N	t	\N	386.02	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
194	1	1	\N	A-005-034	5	34	1763.89	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000C4088E1AF13F53C041E4EBDEF1FF27C0	1842.82	2025-08-02	2026-04-01	Bacon	\N	\N	f	\N	389.03	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
195	1	1	\N	A-005-035	5	35	1455.67	muerto	senescencia	2026-05-27	\N	0101000020E61000009AF1FEA6F03F53C041E4EBDEF1FF27C0	880.43	2022-06-11	\N	Fuerte	\N	\N	f	\N	495.81	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
196	1	1	\N	A-005-036	5	36	1887.21	con_estres	vivero	\N	\N	0101000020E6100000B6DA6F33F03F53C041E4EBDEF1FF27C0	903.95	2023-11-08	\N	Fuerte	\N	\N	f	\N	238.73	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
197	1	1	\N	A-005-037	5	37	1238.78	erradicado	produccion_madura	2026-04-19	\N	0101000020E6100000D2C3E0BFEF3F53C041E4EBDEF1FF27C0	1803.21	2025-12-19	2026-05-15	Fuerte	\N	\N	f	\N	219.66	3	Earum excepturi est pariatur atque pariatur.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
198	1	1	\N	A-005-038	5	38	1828.39	enfermo_critico	establecimiento	\N	\N	0101000020E6100000A7AC514CEF3F53C041E4EBDEF1FF27C0	2224.04	2021-09-12	2025-10-24	Ettinger	\N	\N	f	\N	458.29	6	Nihil quis quia non quia possimus quia dicta.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
199	1	1	\N	A-005-039	5	39	1273.16	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000C395C2D8EE3F53C041E4EBDEF1FF27C0	1201.19	2022-01-09	2022-06-11	Fuerte	\N	\N	f	\N	111.36	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
200	1	1	\N	A-005-040	5	40	2143.80	excelente	produccion_madura	\N	\N	0101000020E6100000997E3365EE3F53C041E4EBDEF1FF27C0	1484.24	2025-12-09	\N	Ettinger	\N	\N	f	\N	87.28	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
201	1	1	\N	A-006-001	6	1	2474.36	con_estres	desarrollo_inmaduro	\N	\N	0101000020E610000000000000004053C0DEDDA656EEFF27C0	1407.90	2022-10-17	\N	Fuerte	\N	\N	f	\N	5.55	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
202	1	1	\N	A-006-002	6	2	2256.03	excelente	vivero	\N	\N	0101000020E61000001CE9708CFF3F53C0DEDDA656EEFF27C0	812.05	2025-09-23	2026-04-26	Bacon	\N	\N	f	\N	392.38	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
203	1	1	\N	A-006-003	6	3	1554.29	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000F2D1E118FF3F53C0DEDDA656EEFF27C0	1850.15	2022-07-14	2026-03-13	Bacon	\N	\N	f	\N	151.32	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
204	1	1	\N	A-006-004	6	4	1420.65	con_estres	vivero	\N	\N	0101000020E61000000EBB52A5FE3F53C0DEDDA656EEFF27C0	1997.36	2023-09-16	\N	Hass	\N	\N	f	\N	37.86	1	Maiores itaque dolor sed dolorem voluptatem.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
205	1	1	\N	A-006-005	6	5	1425.51	erradicado	establecimiento	2026-04-04	\N	0101000020E6100000E3A3C331FE3F53C0DEDDA656EEFF27C0	1860.35	2024-07-02	\N	Bacon	\N	\N	f	\N	366.77	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
206	1	1	\N	A-006-006	6	6	859.75	enfermo_critico	establecimiento	\N	\N	0101000020E6100000FF8C34BEFD3F53C0DEDDA656EEFF27C0	\N	2023-06-09	2023-09-27	Fuerte	\N	\N	f	\N	69.69	9	Deserunt nihil reprehenderit qui aut.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
207	1	1	\N	A-006-007	6	7	1872.23	con_estres	establecimiento	\N	\N	0101000020E6100000D575A54AFD3F53C0DEDDA656EEFF27C0	2084.43	2024-10-18	2024-12-24	Fuerte	\N	\N	f	\N	27.58	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
208	1	1	\N	A-006-008	6	8	1381.51	enfermo_critico	establecimiento	\N	\N	0101000020E6100000F15E16D7FC3F53C0DEDDA656EEFF27C0	2362.56	2022-02-05	2024-06-24	Zutano	\N	\N	f	\N	205.17	7	Eius mollitia magni temporibus pariatur nam iure.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
209	1	1	\N	A-006-009	6	9	1157.42	muerto	vivero	2026-02-03	\N	0101000020E61000000D488763FC3F53C0DEDDA656EEFF27C0	886.68	2021-12-17	\N	Zutano	\N	\N	f	\N	437.78	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
210	1	1	\N	A-006-010	6	10	2279.79	con_estres	vivero	\N	\N	0101000020E6100000E330F8EFFB3F53C0DEDDA656EEFF27C0	1276.08	2022-01-26	\N	Ettinger	\N	\N	f	\N	345.28	9	Voluptatem aperiam illo aliquam quia ut.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
211	1	1	\N	A-006-011	6	11	2136.37	con_estres	produccion_madura	\N	\N	0101000020E6100000FF19697CFB3F53C0DEDDA656EEFF27C0	2199.83	2026-07-16	2026-07-29	Bacon	\N	\N	f	\N	21.54	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
212	1	1	\N	A-006-012	6	12	1123.89	con_estres	vivero	\N	\N	0101000020E6100000D502DA08FB3F53C0DEDDA656EEFF27C0	1408.54	2022-05-28	2026-04-30	Zutano	\N	\N	f	\N	35.75	0	Ea iste asperiores sapiente non.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
213	1	1	\N	A-006-013	6	13	1693.95	con_estres	produccion_madura	\N	\N	0101000020E6100000F1EB4A95FA3F53C0DEDDA656EEFF27C0	1425.77	2023-08-25	2024-12-16	Hass	\N	\N	f	\N	128.50	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
214	1	1	\N	A-006-014	6	14	2245.65	erradicado	senescencia	2026-04-05	\N	0101000020E6100000C6D4BB21FA3F53C0DEDDA656EEFF27C0	1525.54	2021-09-20	2021-11-20	Ettinger	\N	\N	f	\N	223.17	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
215	1	1	\N	A-006-015	6	15	1829.05	excelente	senescencia	\N	\N	0101000020E6100000E2BD2CAEF93F53C0DEDDA656EEFF27C0	811.53	2024-03-09	2026-01-28	Bacon	\N	\N	f	\N	169.17	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
216	1	1	\N	A-006-016	6	16	905.80	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000FEA69D3AF93F53C0DEDDA656EEFF27C0	1968.85	2024-07-07	2025-07-08	Fuerte	\N	\N	f	\N	192.96	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
217	1	1	\N	A-006-017	6	17	1153.00	con_estres	vivero	\N	\N	0101000020E6100000D48F0EC7F83F53C0DEDDA656EEFF27C0	1492.50	2026-03-21	\N	Hass	\N	\N	f	\N	421.39	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
218	1	1	\N	A-006-018	6	18	1732.30	con_estres	produccion_madura	\N	\N	0101000020E6100000F0787F53F83F53C0DEDDA656EEFF27C0	2450.30	2025-09-06	2026-03-14	Hass	\N	\N	f	\N	253.41	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
219	1	1	\N	A-006-019	6	19	1658.93	muerto	produccion_madura	2026-07-10	\N	0101000020E6100000C661F0DFF73F53C0DEDDA656EEFF27C0	2481.93	2021-08-27	2025-09-10	Hass	\N	\N	f	\N	250.19	7	Laborum quasi vel quia nulla ipsa voluptatibus.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
220	1	1	\N	A-006-020	6	20	864.31	erradicado	produccion_madura	2026-05-04	\N	0101000020E6100000E24A616CF73F53C0DEDDA656EEFF27C0	812.32	2025-05-08	2026-06-06	Zutano	\N	\N	f	\N	418.74	4	Voluptas autem sed sed rerum nobis quo.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
221	1	1	\N	A-006-021	6	21	2138.25	muerto	establecimiento	2026-06-22	\N	0101000020E6100000B733D2F8F63F53C0DEDDA656EEFF27C0	1605.52	2022-12-21	2024-01-03	Ettinger	\N	\N	f	\N	435.20	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
222	1	1	\N	A-006-022	6	22	1472.20	con_estres	vivero	\N	\N	0101000020E6100000D31C4385F63F53C0DEDDA656EEFF27C0	2402.28	2023-12-03	2025-09-13	Fuerte	\N	\N	f	\N	51.27	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
223	1	1	\N	A-006-023	6	23	1598.11	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000EF05B411F63F53C0DEDDA656EEFF27C0	2478.04	2024-09-10	2025-09-08	Ettinger	\N	\N	f	\N	485.42	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
224	1	1	\N	A-006-024	6	24	1638.18	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000C5EE249EF53F53C0DEDDA656EEFF27C0	1365.45	2025-09-16	\N	Bacon	\N	\N	f	\N	223.31	9	Nihil nostrum similique voluptatem corrupti natus quae qui.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
225	1	1	\N	A-006-025	6	25	983.32	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000E1D7952AF53F53C0DEDDA656EEFF27C0	1044.14	2023-03-25	2024-11-22	Zutano	\N	\N	f	\N	28.74	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
226	1	1	\N	A-006-026	6	26	2385.57	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000B7C006B7F43F53C0DEDDA656EEFF27C0	1133.52	2021-08-31	\N	Fuerte	\N	\N	f	\N	243.54	8	Magni qui impedit sed ducimus alias ex dolor sunt.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
227	1	1	\N	A-006-027	6	27	997.92	con_estres	establecimiento	\N	\N	0101000020E6100000D3A97743F43F53C0DEDDA656EEFF27C0	\N	2022-07-30	2026-05-16	Fuerte	\N	\N	f	\N	469.17	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
228	1	1	\N	A-006-028	6	28	1627.48	excelente	establecimiento	\N	\N	0101000020E6100000A892E8CFF33F53C0DEDDA656EEFF27C0	932.79	2025-01-12	\N	Zutano	\N	\N	f	\N	473.40	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
229	1	1	\N	A-006-029	6	29	1463.70	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000C47B595CF33F53C0DEDDA656EEFF27C0	1988.49	2023-12-29	2025-04-13	Hass	\N	\N	f	\N	132.37	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
230	1	1	\N	A-006-030	6	30	2209.86	excelente	produccion_madura	\N	\N	0101000020E6100000E164CAE8F23F53C0DEDDA656EEFF27C0	\N	2022-11-21	2025-12-27	Ettinger	\N	\N	f	\N	14.22	2	Qui doloribus accusamus in autem a esse.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
231	1	1	\N	A-006-031	6	31	828.11	enfermo_critico	senescencia	\N	\N	0101000020E6100000B64D3B75F23F53C0DEDDA656EEFF27C0	897.53	2026-04-12	2026-05-26	Zutano	\N	\N	f	\N	430.94	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
232	1	1	\N	A-006-032	6	32	2197.57	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000D236AC01F23F53C0DEDDA656EEFF27C0	2414.02	2024-09-10	2025-07-19	Hass	\N	\N	f	\N	430.58	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
233	1	1	\N	A-006-033	6	33	1207.61	enfermo_critico	establecimiento	\N	\N	0101000020E6100000A81F1D8EF13F53C0DEDDA656EEFF27C0	1088.86	2025-08-17	2026-03-06	Ettinger	\N	\N	t	\N	348.09	0	Est autem eaque suscipit inventore illum voluptate ab.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
234	1	1	\N	A-006-034	6	34	1042.72	erradicado	senescencia	2025-11-02	\N	0101000020E6100000C4088E1AF13F53C0DEDDA656EEFF27C0	2223.25	2021-09-12	\N	Zutano	\N	\N	f	\N	145.92	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
235	1	1	\N	A-006-035	6	35	942.46	excelente	establecimiento	\N	\N	0101000020E61000009AF1FEA6F03F53C0DEDDA656EEFF27C0	1019.88	2022-08-04	2024-02-03	Hass	\N	\N	f	\N	233.14	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
236	1	1	\N	A-006-036	6	36	1022.90	enfermo_critico	establecimiento	\N	\N	0101000020E6100000B6DA6F33F03F53C0DEDDA656EEFF27C0	1031.22	2022-06-14	\N	Hass	\N	\N	f	\N	160.55	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
237	1	1	\N	A-006-037	6	37	2171.70	enfermo_critico	establecimiento	\N	\N	0101000020E6100000D2C3E0BFEF3F53C0DEDDA656EEFF27C0	1366.56	2022-09-24	\N	Fuerte	\N	\N	f	\N	388.52	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
238	1	1	\N	A-006-038	6	38	2270.56	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000A7AC514CEF3F53C0DEDDA656EEFF27C0	1453.23	2022-02-23	2025-03-02	Zutano	\N	\N	f	\N	11.30	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
239	1	1	\N	A-006-039	6	39	1009.57	erradicado	desarrollo_inmaduro	2026-06-26	\N	0101000020E6100000C395C2D8EE3F53C0DEDDA656EEFF27C0	\N	2024-07-14	2024-09-12	Hass	\N	\N	f	\N	91.67	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
240	1	1	\N	A-006-040	6	40	1128.14	muerto	senescencia	2026-01-03	\N	0101000020E6100000997E3365EE3F53C0DEDDA656EEFF27C0	1462.74	2023-12-12	2024-11-03	Ettinger	\N	\N	f	\N	220.93	9	Dolorum commodi veniam laudantium veniam veniam natus voluptas.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
241	1	1	\N	A-007-001	7	1	923.64	enfermo_critico	produccion_madura	\N	\N	0101000020E610000000000000004053C07BD761CEEAFF27C0	\N	2024-09-28	2026-07-07	Hass	\N	\N	f	\N	249.55	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
242	1	1	\N	A-007-002	7	2	1052.94	muerto	establecimiento	2026-06-26	\N	0101000020E61000001CE9708CFF3F53C07BD761CEEAFF27C0	\N	2024-07-09	2025-05-31	Hass	\N	\N	f	\N	65.96	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
243	1	1	\N	A-007-003	7	3	1770.01	muerto	produccion_madura	2026-06-19	\N	0101000020E6100000F2D1E118FF3F53C07BD761CEEAFF27C0	2025.93	2024-11-25	\N	Zutano	\N	\N	f	\N	193.56	7	Est est voluptates ipsam enim consequatur odio exercitationem.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
244	1	1	\N	A-007-004	7	4	1502.27	enfermo_critico	senescencia	\N	\N	0101000020E61000000EBB52A5FE3F53C07BD761CEEAFF27C0	1842.60	2023-01-31	\N	Bacon	\N	\N	f	\N	148.83	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
245	1	1	\N	A-007-005	7	5	2406.29	muerto	vivero	2025-10-27	\N	0101000020E6100000E3A3C331FE3F53C07BD761CEEAFF27C0	1699.72	2022-06-23	\N	Fuerte	\N	\N	f	\N	178.89	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
246	1	1	\N	A-007-006	7	6	964.25	muerto	produccion_madura	2026-07-06	\N	0101000020E6100000FF8C34BEFD3F53C07BD761CEEAFF27C0	2016.84	2025-04-04	2026-07-05	Ettinger	\N	\N	f	\N	446.07	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
247	1	1	\N	A-007-007	7	7	2229.11	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000D575A54AFD3F53C07BD761CEEAFF27C0	1284.74	2023-10-22	2024-01-02	Fuerte	\N	\N	f	\N	481.39	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
248	1	1	\N	A-007-008	7	8	2367.82	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000F15E16D7FC3F53C07BD761CEEAFF27C0	1446.25	2024-08-19	\N	Fuerte	\N	\N	f	\N	231.90	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
249	1	1	\N	A-007-009	7	9	2069.62	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E61000000D488763FC3F53C07BD761CEEAFF27C0	2290.58	2023-04-21	2026-04-17	Hass	\N	\N	f	\N	308.35	4	Quo ut in aut autem consequatur.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
250	1	1	\N	A-007-010	7	10	2496.58	excelente	produccion_madura	\N	\N	0101000020E6100000E330F8EFFB3F53C07BD761CEEAFF27C0	1023.75	2024-07-19	2025-01-24	Hass	\N	\N	f	\N	436.05	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
251	1	1	\N	A-007-011	7	11	1701.30	con_estres	produccion_madura	\N	\N	0101000020E6100000FF19697CFB3F53C07BD761CEEAFF27C0	1855.63	2022-01-30	2024-04-03	Bacon	\N	\N	f	\N	248.06	6	Reprehenderit aliquid doloribus dolorem architecto aut nemo ea.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
252	1	1	\N	A-007-012	7	12	2336.24	excelente	establecimiento	\N	\N	0101000020E6100000D502DA08FB3F53C07BD761CEEAFF27C0	1779.89	2022-10-09	2024-01-31	Bacon	\N	\N	f	\N	280.51	6	Ut hic qui quo voluptate a labore omnis.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
253	1	1	\N	A-007-013	7	13	1793.77	muerto	establecimiento	2026-07-26	\N	0101000020E6100000F1EB4A95FA3F53C07BD761CEEAFF27C0	2249.65	2025-08-07	2026-03-28	Bacon	\N	\N	f	\N	447.48	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
254	1	1	\N	A-007-014	7	14	978.99	muerto	produccion_madura	2025-11-30	\N	0101000020E6100000C6D4BB21FA3F53C07BD761CEEAFF27C0	1117.90	2023-01-01	2023-06-08	Fuerte	\N	\N	f	\N	18.71	8	Cumque porro labore voluptatem numquam qui.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
255	1	1	\N	A-007-015	7	15	1619.27	con_estres	senescencia	\N	\N	0101000020E6100000E2BD2CAEF93F53C07BD761CEEAFF27C0	1076.69	2023-05-14	2025-07-07	Hass	\N	\N	f	\N	372.35	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
256	1	1	\N	A-007-016	7	16	2220.65	con_estres	vivero	\N	\N	0101000020E6100000FEA69D3AF93F53C07BD761CEEAFF27C0	1950.55	2023-02-03	2023-09-29	Bacon	\N	\N	f	\N	456.80	10	Ut numquam quisquam est qui.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
257	1	1	\N	A-007-017	7	17	1638.03	enfermo_critico	establecimiento	\N	\N	0101000020E6100000D48F0EC7F83F53C07BD761CEEAFF27C0	\N	2025-06-14	2026-01-16	Bacon	\N	\N	f	\N	486.81	4	Et ex magnam earum commodi.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
258	1	1	\N	A-007-018	7	18	2290.60	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000F0787F53F83F53C07BD761CEEAFF27C0	1674.73	2023-07-02	\N	Hass	\N	\N	f	\N	171.31	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
259	1	1	\N	A-007-019	7	19	2167.20	muerto	vivero	2026-02-17	\N	0101000020E6100000C661F0DFF73F53C07BD761CEEAFF27C0	2049.76	2026-04-16	2026-04-30	Zutano	\N	\N	f	\N	419.82	3	Autem provident hic fuga vero ut asperiores.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
260	1	1	\N	A-007-020	7	20	1025.24	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000E24A616CF73F53C07BD761CEEAFF27C0	1528.05	2024-06-09	2025-09-22	Ettinger	\N	\N	f	\N	300.35	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
261	1	1	\N	A-007-021	7	21	1531.93	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000B733D2F8F63F53C07BD761CEEAFF27C0	1515.95	2021-11-05	\N	Hass	\N	\N	f	\N	432.05	6	Omnis odio in accusamus magni soluta reiciendis possimus.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
262	1	1	\N	A-007-022	7	22	2402.36	muerto	desarrollo_inmaduro	2026-05-24	\N	0101000020E6100000D31C4385F63F53C07BD761CEEAFF27C0	2212.18	2023-05-29	2026-06-25	Fuerte	\N	\N	f	\N	368.02	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
263	1	1	\N	A-007-023	7	23	1808.77	enfermo_critico	vivero	\N	\N	0101000020E6100000EF05B411F63F53C07BD761CEEAFF27C0	865.58	2024-12-03	2025-12-03	Ettinger	\N	\N	f	\N	479.21	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
264	1	1	\N	A-007-024	7	24	821.31	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000C5EE249EF53F53C07BD761CEEAFF27C0	2284.58	2025-08-09	2026-02-06	Ettinger	\N	\N	f	\N	338.03	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
265	1	1	\N	A-007-025	7	25	2143.18	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000E1D7952AF53F53C07BD761CEEAFF27C0	904.85	2023-07-23	\N	Bacon	\N	\N	f	\N	47.93	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
266	1	1	\N	A-007-026	7	26	968.04	muerto	produccion_madura	2026-04-02	\N	0101000020E6100000B7C006B7F43F53C07BD761CEEAFF27C0	836.88	2022-03-09	2023-12-15	Bacon	\N	\N	f	\N	76.02	3	Vel odit cupiditate cupiditate hic explicabo praesentium deserunt eos.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
267	1	1	\N	A-007-027	7	27	1364.33	enfermo_critico	vivero	\N	\N	0101000020E6100000D3A97743F43F53C07BD761CEEAFF27C0	2241.80	2025-06-29	2026-04-30	Zutano	\N	\N	f	\N	64.22	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
268	1	1	\N	A-007-028	7	28	850.67	erradicado	produccion_madura	2025-09-08	\N	0101000020E6100000A892E8CFF33F53C07BD761CEEAFF27C0	2352.47	2022-05-05	2023-01-31	Fuerte	\N	\N	f	\N	436.48	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
269	1	1	\N	A-007-029	7	29	2012.39	con_estres	vivero	\N	\N	0101000020E6100000C47B595CF33F53C07BD761CEEAFF27C0	1834.92	2024-01-28	\N	Hass	\N	\N	f	\N	489.50	2	Fugiat fuga dolorem saepe.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
270	1	1	\N	A-007-030	7	30	2134.59	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000E164CAE8F23F53C07BD761CEEAFF27C0	1198.28	2022-08-21	2024-08-24	Ettinger	\N	\N	f	\N	76.78	7	Laudantium quasi facilis vero.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
271	1	1	\N	A-007-031	7	31	1142.10	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000B64D3B75F23F53C07BD761CEEAFF27C0	2331.79	2023-09-21	2024-06-25	Fuerte	\N	\N	f	\N	460.09	2	Molestiae et ab est sequi ab modi sit.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
272	1	1	\N	A-007-032	7	32	1959.55	erradicado	senescencia	2025-10-24	\N	0101000020E6100000D236AC01F23F53C07BD761CEEAFF27C0	2497.88	2022-07-01	2026-07-14	Zutano	\N	\N	f	\N	329.36	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
273	1	1	\N	A-007-033	7	33	1722.74	muerto	vivero	2026-03-17	\N	0101000020E6100000A81F1D8EF13F53C07BD761CEEAFF27C0	2210.04	2023-03-03	2024-07-04	Fuerte	\N	\N	f	\N	375.69	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
274	1	1	\N	A-007-034	7	34	2424.14	enfermo_critico	vivero	\N	\N	0101000020E6100000C4088E1AF13F53C07BD761CEEAFF27C0	1246.46	2026-04-17	\N	Fuerte	\N	\N	f	\N	448.68	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
275	1	1	\N	A-007-035	7	35	1270.26	muerto	senescencia	2026-07-13	\N	0101000020E61000009AF1FEA6F03F53C07BD761CEEAFF27C0	2216.82	2024-05-12	2026-01-03	Hass	\N	\N	f	\N	422.31	1	Et voluptas consequatur velit aspernatur.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
276	1	1	\N	A-007-036	7	36	1029.45	muerto	produccion_madura	2026-04-26	\N	0101000020E6100000B6DA6F33F03F53C07BD761CEEAFF27C0	1608.08	2025-08-12	\N	Zutano	\N	\N	f	\N	113.32	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
277	1	1	\N	A-007-037	7	37	1037.52	con_estres	produccion_madura	\N	\N	0101000020E6100000D2C3E0BFEF3F53C07BD761CEEAFF27C0	1196.33	2024-02-18	\N	Fuerte	\N	\N	f	\N	322.13	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
278	1	1	\N	A-007-038	7	38	1617.69	erradicado	senescencia	2026-04-12	\N	0101000020E6100000A7AC514CEF3F53C07BD761CEEAFF27C0	2437.62	2024-09-24	2026-04-06	Bacon	\N	\N	f	\N	251.84	1	Natus totam dolorum adipisci et quis non soluta.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
279	1	1	\N	A-007-039	7	39	802.60	muerto	produccion_madura	2026-01-16	\N	0101000020E6100000C395C2D8EE3F53C07BD761CEEAFF27C0	2417.96	2024-01-19	2024-01-31	Zutano	\N	\N	f	\N	159.96	8	Sit occaecati iusto quo facilis et dolorem quia dolores.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
280	1	1	\N	A-007-040	7	40	1981.00	excelente	senescencia	\N	\N	0101000020E6100000997E3365EE3F53C07BD761CEEAFF27C0	2105.59	2021-10-08	2025-09-23	Ettinger	\N	\N	f	\N	176.13	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
281	1	1	\N	A-008-001	8	1	1064.05	muerto	vivero	2026-06-22	\N	0101000020E610000000000000004053C0E5CE1C46E7FF27C0	808.29	2022-04-30	2026-05-20	Ettinger	\N	\N	f	\N	463.71	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
282	1	1	\N	A-008-002	8	2	2170.19	muerto	establecimiento	2026-05-20	\N	0101000020E61000001CE9708CFF3F53C0E5CE1C46E7FF27C0	2373.66	2024-03-04	2026-08-04	Hass	\N	\N	f	\N	371.11	2	Eveniet et commodi voluptas veniam rerum illum quae odit.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
283	1	1	\N	A-008-003	8	3	1945.08	excelente	establecimiento	\N	\N	0101000020E6100000F2D1E118FF3F53C0E5CE1C46E7FF27C0	1172.28	2022-10-27	\N	Bacon	\N	\N	f	\N	150.55	5	Cumque sunt aliquid qui et magni nobis.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
284	1	1	\N	A-008-004	8	4	868.21	con_estres	vivero	\N	\N	0101000020E61000000EBB52A5FE3F53C0E5CE1C46E7FF27C0	2330.06	2024-06-03	2024-12-06	Fuerte	\N	\N	f	\N	7.54	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
285	1	1	\N	A-008-005	8	5	2446.42	excelente	produccion_madura	\N	\N	0101000020E6100000E3A3C331FE3F53C0E5CE1C46E7FF27C0	1792.65	2026-07-16	\N	Zutano	\N	\N	f	\N	190.48	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
286	1	1	\N	A-008-006	8	6	2486.09	enfermo_critico	vivero	\N	\N	0101000020E6100000FF8C34BEFD3F53C0E5CE1C46E7FF27C0	1118.04	2022-08-20	2025-07-26	Ettinger	\N	\N	f	\N	368.97	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
287	1	1	\N	A-008-007	8	7	939.84	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000D575A54AFD3F53C0E5CE1C46E7FF27C0	1365.89	2026-01-06	2026-01-25	Zutano	\N	\N	f	\N	472.63	5	Non quaerat quam consequatur delectus explicabo est.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
288	1	1	\N	A-008-008	8	8	1334.12	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000F15E16D7FC3F53C0E5CE1C46E7FF27C0	\N	2022-10-13	2026-05-03	Ettinger	\N	\N	f	\N	320.69	0	Ea officiis amet voluptatibus fuga iure iure.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
289	1	1	\N	A-008-009	8	9	1664.47	muerto	produccion_madura	2025-10-14	\N	0101000020E61000000D488763FC3F53C0E5CE1C46E7FF27C0	1683.18	2023-04-13	\N	Zutano	\N	\N	f	\N	271.24	8	Ipsum nostrum quas vel dolores.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
290	1	1	\N	A-008-010	8	10	1666.04	erradicado	establecimiento	2025-10-13	\N	0101000020E6100000E330F8EFFB3F53C0E5CE1C46E7FF27C0	1520.14	2024-04-26	\N	Fuerte	\N	\N	f	\N	479.91	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
291	1	1	\N	A-008-011	8	11	2393.51	excelente	establecimiento	\N	\N	0101000020E6100000FF19697CFB3F53C0E5CE1C46E7FF27C0	1985.25	2021-11-03	2026-06-07	Bacon	\N	\N	f	\N	278.26	10	Laudantium quo dignissimos expedita dolorum rerum quibusdam illo.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
292	1	1	\N	A-008-012	8	12	1770.93	con_estres	senescencia	\N	\N	0101000020E6100000D502DA08FB3F53C0E5CE1C46E7FF27C0	1087.68	2022-08-12	2023-06-17	Bacon	\N	\N	f	\N	376.14	4	Maiores sit eos ut possimus natus.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
293	1	1	\N	A-008-013	8	13	1439.62	erradicado	vivero	2026-04-17	\N	0101000020E6100000F1EB4A95FA3F53C0E5CE1C46E7FF27C0	\N	2022-03-12	2024-03-20	Ettinger	\N	\N	f	\N	423.07	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
294	1	1	\N	A-008-014	8	14	1633.32	erradicado	senescencia	2026-01-30	\N	0101000020E6100000C6D4BB21FA3F53C0E5CE1C46E7FF27C0	1379.50	2022-02-05	2023-11-24	Zutano	\N	\N	f	\N	444.09	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
295	1	1	\N	A-008-015	8	15	2072.82	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000E2BD2CAEF93F53C0E5CE1C46E7FF27C0	\N	2023-01-15	2025-01-31	Fuerte	\N	\N	f	\N	74.51	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
296	1	1	\N	A-008-016	8	16	1159.09	enfermo_critico	senescencia	\N	\N	0101000020E6100000FEA69D3AF93F53C0E5CE1C46E7FF27C0	1358.14	2021-12-13	\N	Bacon	\N	\N	f	\N	342.15	1	Non aut est voluptatum sed quo dicta.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
297	1	1	\N	A-008-017	8	17	2169.73	erradicado	produccion_madura	2026-05-29	\N	0101000020E6100000D48F0EC7F83F53C0E5CE1C46E7FF27C0	\N	2026-03-24	2026-06-30	Bacon	\N	\N	f	\N	195.39	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
298	1	1	\N	A-008-018	8	18	1164.21	muerto	desarrollo_inmaduro	2025-10-16	\N	0101000020E6100000F0787F53F83F53C0E5CE1C46E7FF27C0	1573.81	2025-06-21	\N	Zutano	\N	\N	f	\N	139.52	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
299	1	1	\N	A-008-019	8	19	1236.95	excelente	senescencia	\N	\N	0101000020E6100000C661F0DFF73F53C0E5CE1C46E7FF27C0	1191.47	2023-10-29	\N	Ettinger	\N	\N	f	\N	182.36	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
300	1	1	\N	A-008-020	8	20	1647.02	enfermo_critico	establecimiento	\N	\N	0101000020E6100000E24A616CF73F53C0E5CE1C46E7FF27C0	1099.59	2026-06-08	2026-08-19	Hass	\N	\N	f	\N	128.49	1	Sed sit sapiente et reprehenderit neque molestias.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
301	1	1	\N	A-008-021	8	21	2286.24	con_estres	establecimiento	\N	\N	0101000020E6100000B733D2F8F63F53C0E5CE1C46E7FF27C0	2488.23	2025-07-02	\N	Zutano	\N	\N	f	\N	321.04	3	Et itaque deserunt debitis quia vero incidunt.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
302	1	1	\N	A-008-022	8	22	1570.61	excelente	produccion_madura	\N	\N	0101000020E6100000D31C4385F63F53C0E5CE1C46E7FF27C0	2282.50	2022-08-16	2025-01-23	Ettinger	\N	\N	f	\N	123.10	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
303	1	1	\N	A-008-023	8	23	1423.95	muerto	vivero	2025-09-01	\N	0101000020E6100000EF05B411F63F53C0E5CE1C46E7FF27C0	815.63	2024-07-06	2026-07-27	Bacon	\N	\N	f	\N	341.42	3	Cumque nemo rerum pariatur repellat officiis temporibus eos ad.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
304	1	1	\N	A-008-024	8	24	2309.60	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000C5EE249EF53F53C0E5CE1C46E7FF27C0	2444.92	2026-04-09	\N	Zutano	\N	\N	f	\N	455.74	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
305	1	1	\N	A-008-025	8	25	808.98	muerto	vivero	2026-07-04	\N	0101000020E6100000E1D7952AF53F53C0E5CE1C46E7FF27C0	1623.27	2022-03-01	\N	Fuerte	\N	\N	f	\N	229.42	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
306	1	1	\N	A-008-026	8	26	1471.73	erradicado	produccion_madura	2026-03-18	\N	0101000020E6100000B7C006B7F43F53C0E5CE1C46E7FF27C0	1314.31	2026-03-05	2026-06-12	Ettinger	\N	\N	f	\N	210.56	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
307	1	1	\N	A-008-027	8	27	1271.79	con_estres	produccion_madura	\N	\N	0101000020E6100000D3A97743F43F53C0E5CE1C46E7FF27C0	\N	2021-11-06	2024-08-23	Bacon	\N	\N	f	\N	92.97	4	Facere hic culpa excepturi nam voluptatem quo sunt.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
308	1	1	\N	A-008-028	8	28	1972.33	enfermo_critico	senescencia	\N	\N	0101000020E6100000A892E8CFF33F53C0E5CE1C46E7FF27C0	\N	2022-02-26	2026-02-11	Zutano	\N	\N	f	\N	342.91	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
309	1	1	\N	A-008-029	8	29	1563.99	enfermo_critico	establecimiento	\N	\N	0101000020E6100000C47B595CF33F53C0E5CE1C46E7FF27C0	2247.37	2022-01-15	2025-06-06	Bacon	\N	\N	f	\N	8.42	5	Voluptates exercitationem voluptatem fuga architecto laboriosam qui.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
310	1	1	\N	A-008-030	8	30	2328.47	muerto	desarrollo_inmaduro	2025-10-23	\N	0101000020E6100000E164CAE8F23F53C0E5CE1C46E7FF27C0	997.17	2024-09-08	2025-08-16	Fuerte	\N	\N	t	\N	340.14	10	Quae quibusdam enim non ullam labore earum.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
311	1	1	\N	A-008-031	8	31	2016.42	muerto	senescencia	2026-05-11	\N	0101000020E6100000B64D3B75F23F53C0E5CE1C46E7FF27C0	2125.08	2021-12-25	\N	Bacon	\N	\N	f	\N	444.77	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
312	1	1	\N	A-008-032	8	32	1348.51	muerto	senescencia	2026-04-23	\N	0101000020E6100000D236AC01F23F53C0E5CE1C46E7FF27C0	1189.80	2026-01-20	2026-05-03	Bacon	\N	\N	f	\N	209.66	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
313	1	1	\N	A-008-033	8	33	2048.60	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000A81F1D8EF13F53C0E5CE1C46E7FF27C0	1186.34	2026-07-17	\N	Hass	\N	\N	f	\N	340.73	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
314	1	1	\N	A-008-034	8	34	2020.14	muerto	establecimiento	2026-05-07	\N	0101000020E6100000C4088E1AF13F53C0E5CE1C46E7FF27C0	1717.27	2025-12-24	2026-01-07	Hass	\N	\N	f	\N	489.57	10	Dolor in atque voluptatem magni dolores distinctio quibusdam necessitatibus.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
315	1	1	\N	A-008-035	8	35	2156.64	muerto	establecimiento	2026-07-25	\N	0101000020E61000009AF1FEA6F03F53C0E5CE1C46E7FF27C0	1335.69	2026-04-02	2026-06-10	Bacon	\N	\N	f	\N	24.75	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
316	1	1	\N	A-008-036	8	36	2308.72	erradicado	produccion_madura	2026-06-22	\N	0101000020E6100000B6DA6F33F03F53C0E5CE1C46E7FF27C0	1182.08	2021-10-16	\N	Ettinger	\N	\N	f	\N	498.35	1	Et dicta labore et.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
317	1	1	\N	A-008-037	8	37	1725.89	erradicado	establecimiento	2025-10-14	\N	0101000020E6100000D2C3E0BFEF3F53C0E5CE1C46E7FF27C0	978.13	2025-09-19	2026-07-27	Bacon	\N	\N	f	\N	292.06	4	Aut deserunt repellat molestiae libero.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
318	1	1	\N	A-008-038	8	38	1073.52	erradicado	senescencia	2025-10-09	\N	0101000020E6100000A7AC514CEF3F53C0E5CE1C46E7FF27C0	1440.17	2026-07-13	2026-07-20	Bacon	\N	\N	f	\N	383.67	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
319	1	1	\N	A-008-039	8	39	945.24	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000C395C2D8EE3F53C0E5CE1C46E7FF27C0	1697.23	2022-02-26	2025-01-06	Zutano	\N	\N	f	\N	230.37	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
320	1	1	\N	A-008-040	8	40	2427.69	con_estres	establecimiento	\N	\N	0101000020E6100000997E3365EE3F53C0E5CE1C46E7FF27C0	1184.55	2025-09-10	2026-01-15	Ettinger	\N	\N	f	\N	221.76	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
321	1	1	\N	A-009-001	9	1	924.96	excelente	vivero	\N	\N	0101000020E610000000000000004053C082C8D7BDE3FF27C0	1728.76	2025-02-03	2025-05-18	Bacon	\N	\N	f	\N	106.23	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
322	1	1	\N	A-009-002	9	2	873.34	muerto	establecimiento	2026-04-03	\N	0101000020E61000001CE9708CFF3F53C082C8D7BDE3FF27C0	1810.58	2024-06-13	2025-03-09	Fuerte	\N	\N	f	\N	71.13	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
323	1	1	\N	A-009-003	9	3	1894.39	erradicado	establecimiento	2025-12-20	\N	0101000020E6100000F2D1E118FF3F53C082C8D7BDE3FF27C0	1889.57	2026-07-21	2026-08-21	Ettinger	\N	\N	f	\N	253.41	3	Omnis quis repellat provident rem in alias tenetur.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
324	1	1	\N	A-009-004	9	4	1954.49	con_estres	desarrollo_inmaduro	\N	\N	0101000020E61000000EBB52A5FE3F53C082C8D7BDE3FF27C0	1562.91	2024-08-02	2024-10-27	Bacon	\N	\N	f	\N	433.39	1	Et omnis fuga vel soluta a laudantium.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
325	1	1	\N	A-009-005	9	5	1557.97	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000E3A3C331FE3F53C082C8D7BDE3FF27C0	1877.10	2023-10-22	\N	Ettinger	\N	\N	f	\N	18.30	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
326	1	1	\N	A-009-006	9	6	1387.80	erradicado	produccion_madura	2026-08-16	\N	0101000020E6100000FF8C34BEFD3F53C082C8D7BDE3FF27C0	927.36	2026-01-26	\N	Bacon	\N	\N	f	\N	277.52	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
327	1	1	\N	A-009-007	9	7	2413.43	con_estres	senescencia	\N	\N	0101000020E6100000D575A54AFD3F53C082C8D7BDE3FF27C0	\N	2025-07-22	2025-11-06	Zutano	\N	\N	f	\N	371.17	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
328	1	1	\N	A-009-008	9	8	2161.12	excelente	vivero	\N	\N	0101000020E6100000F15E16D7FC3F53C082C8D7BDE3FF27C0	1840.43	2022-11-26	2025-03-17	Ettinger	\N	\N	f	\N	384.44	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
329	1	1	\N	A-009-009	9	9	1659.01	con_estres	establecimiento	\N	\N	0101000020E61000000D488763FC3F53C082C8D7BDE3FF27C0	2072.95	2026-01-25	2026-06-19	Bacon	\N	\N	f	\N	210.40	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
330	1	1	\N	A-009-010	9	10	2282.38	erradicado	senescencia	2026-08-17	\N	0101000020E6100000E330F8EFFB3F53C082C8D7BDE3FF27C0	2146.60	2024-10-11	\N	Fuerte	\N	\N	f	\N	440.37	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
331	1	1	\N	A-009-011	9	11	2195.69	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000FF19697CFB3F53C082C8D7BDE3FF27C0	2317.44	2022-04-07	2023-06-18	Bacon	\N	\N	f	\N	95.46	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
332	1	1	\N	A-009-012	9	12	1878.75	erradicado	establecimiento	2026-06-26	\N	0101000020E6100000D502DA08FB3F53C082C8D7BDE3FF27C0	\N	2023-11-14	2026-03-27	Zutano	\N	\N	f	\N	178.34	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
333	1	1	\N	A-009-013	9	13	1800.98	enfermo_critico	establecimiento	\N	\N	0101000020E6100000F1EB4A95FA3F53C082C8D7BDE3FF27C0	859.73	2024-01-20	2024-12-23	Ettinger	\N	\N	f	\N	133.99	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
334	1	1	\N	A-009-014	9	14	832.28	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000C6D4BB21FA3F53C082C8D7BDE3FF27C0	2169.28	2024-09-09	2024-11-13	Ettinger	\N	\N	f	\N	29.12	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
335	1	1	\N	A-009-015	9	15	2362.29	muerto	produccion_madura	2025-12-05	\N	0101000020E6100000E2BD2CAEF93F53C082C8D7BDE3FF27C0	1862.38	2022-03-19	2023-08-21	Hass	\N	\N	f	\N	306.24	6	Dolores vitae quidem velit eaque molestiae.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
336	1	1	\N	A-009-016	9	16	2294.25	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000FEA69D3AF93F53C082C8D7BDE3FF27C0	2139.42	2024-02-29	2024-08-05	Bacon	\N	\N	t	\N	457.28	0	Accusamus et similique ratione nemo voluptate et accusamus.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
337	1	1	\N	A-009-017	9	17	2146.80	excelente	vivero	\N	\N	0101000020E6100000D48F0EC7F83F53C082C8D7BDE3FF27C0	1440.84	2021-12-15	2024-12-28	Hass	\N	\N	f	\N	263.60	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
338	1	1	\N	A-009-018	9	18	1634.98	muerto	establecimiento	2026-05-07	\N	0101000020E6100000F0787F53F83F53C082C8D7BDE3FF27C0	\N	2022-04-30	\N	Hass	\N	\N	f	\N	6.66	4	Consequatur consectetur velit et reprehenderit ipsa.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
339	1	1	\N	A-009-019	9	19	1994.61	erradicado	vivero	2026-04-06	\N	0101000020E6100000C661F0DFF73F53C082C8D7BDE3FF27C0	2263.39	2024-10-01	\N	Bacon	\N	\N	f	\N	184.40	2	Quo et odio natus consequatur ipsam sit ut.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
340	1	1	\N	A-009-020	9	20	1678.76	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000E24A616CF73F53C082C8D7BDE3FF27C0	1310.56	2023-01-16	2023-11-01	Zutano	\N	\N	f	\N	452.35	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
341	1	1	\N	A-009-021	9	21	2006.47	con_estres	senescencia	\N	\N	0101000020E6100000B733D2F8F63F53C082C8D7BDE3FF27C0	\N	2025-05-22	2026-06-05	Zutano	\N	\N	f	\N	274.20	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
342	1	1	\N	A-009-022	9	22	2354.81	enfermo_critico	establecimiento	\N	\N	0101000020E6100000D31C4385F63F53C082C8D7BDE3FF27C0	1539.31	2023-10-31	2025-07-06	Hass	\N	\N	f	\N	206.95	6	Qui ut ratione voluptatum aut soluta officia corrupti.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
343	1	1	\N	A-009-023	9	23	1403.21	enfermo_critico	senescencia	\N	\N	0101000020E6100000EF05B411F63F53C082C8D7BDE3FF27C0	2228.09	2026-06-06	2026-06-06	Bacon	\N	\N	f	\N	462.30	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
344	1	1	\N	A-009-024	9	24	1929.51	muerto	senescencia	2026-03-25	\N	0101000020E6100000C5EE249EF53F53C082C8D7BDE3FF27C0	1774.75	2024-01-18	2026-03-10	Fuerte	\N	\N	f	\N	470.65	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
345	1	1	\N	A-009-025	9	25	1434.86	erradicado	produccion_madura	2025-12-15	\N	0101000020E6100000E1D7952AF53F53C082C8D7BDE3FF27C0	1251.41	2026-03-31	\N	Fuerte	\N	\N	f	\N	70.44	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
346	1	1	\N	A-009-026	9	26	1093.25	muerto	vivero	2026-07-14	\N	0101000020E6100000B7C006B7F43F53C082C8D7BDE3FF27C0	2348.10	2025-11-10	\N	Ettinger	\N	\N	f	\N	178.32	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
347	1	1	\N	A-009-027	9	27	2228.57	muerto	desarrollo_inmaduro	2026-03-17	\N	0101000020E6100000D3A97743F43F53C082C8D7BDE3FF27C0	\N	2025-09-08	\N	Zutano	\N	\N	f	\N	253.86	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
348	1	1	\N	A-009-028	9	28	1641.33	excelente	senescencia	\N	\N	0101000020E6100000A892E8CFF33F53C082C8D7BDE3FF27C0	\N	2022-05-08	2024-07-02	Fuerte	\N	\N	f	\N	352.76	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
349	1	1	\N	A-009-029	9	29	1304.05	erradicado	vivero	2026-06-23	\N	0101000020E6100000C47B595CF33F53C082C8D7BDE3FF27C0	\N	2025-07-20	\N	Bacon	\N	\N	f	\N	28.03	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
350	1	1	\N	A-009-030	9	30	1303.13	con_estres	vivero	\N	\N	0101000020E6100000E164CAE8F23F53C082C8D7BDE3FF27C0	2453.53	2025-04-20	2025-07-21	Bacon	\N	\N	f	\N	413.48	5	Ipsum possimus et consequatur voluptatem corrupti voluptatibus.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
351	1	1	\N	A-009-031	9	31	1652.38	con_estres	senescencia	\N	\N	0101000020E6100000B64D3B75F23F53C082C8D7BDE3FF27C0	991.16	2024-04-14	\N	Ettinger	\N	\N	f	\N	360.54	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
352	1	1	\N	A-009-032	9	32	2301.44	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000D236AC01F23F53C082C8D7BDE3FF27C0	1439.96	2023-01-07	2024-12-17	Hass	\N	\N	f	\N	192.35	5	Sed modi sed sed nihil.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
353	1	1	\N	A-009-033	9	33	2467.56	muerto	establecimiento	2026-08-03	\N	0101000020E6100000A81F1D8EF13F53C082C8D7BDE3FF27C0	967.08	2022-03-24	2023-07-01	Zutano	\N	\N	f	\N	60.75	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
354	1	1	\N	A-009-034	9	34	1612.54	muerto	establecimiento	2026-07-24	\N	0101000020E6100000C4088E1AF13F53C082C8D7BDE3FF27C0	1441.54	2023-02-25	2025-03-16	Ettinger	\N	\N	f	\N	158.66	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
355	1	1	\N	A-009-035	9	35	1781.74	excelente	establecimiento	\N	\N	0101000020E61000009AF1FEA6F03F53C082C8D7BDE3FF27C0	983.95	2025-01-18	2025-08-15	Zutano	\N	\N	f	\N	106.89	10	Soluta officiis dolorum enim id.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
356	1	1	\N	A-009-036	9	36	2476.97	erradicado	produccion_madura	2026-04-09	\N	0101000020E6100000B6DA6F33F03F53C082C8D7BDE3FF27C0	1093.13	2022-08-20	\N	Ettinger	\N	\N	f	\N	124.59	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
357	1	1	\N	A-009-037	9	37	1024.39	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000D2C3E0BFEF3F53C082C8D7BDE3FF27C0	1189.21	2022-06-12	2023-05-10	Fuerte	\N	\N	f	\N	359.51	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
358	1	1	\N	A-009-038	9	38	1454.66	erradicado	senescencia	2026-07-05	\N	0101000020E6100000A7AC514CEF3F53C082C8D7BDE3FF27C0	1426.32	2023-10-02	\N	Hass	\N	\N	f	\N	489.04	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
359	1	1	\N	A-009-039	9	39	1525.90	muerto	establecimiento	2026-06-03	\N	0101000020E6100000C395C2D8EE3F53C082C8D7BDE3FF27C0	2263.82	2024-05-16	2026-07-08	Bacon	\N	\N	f	\N	407.56	8	Facere excepturi aut aut.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
360	1	1	\N	A-009-040	9	40	2011.86	enfermo_critico	senescencia	\N	\N	0101000020E6100000997E3365EE3F53C082C8D7BDE3FF27C0	1309.33	2022-11-10	\N	Bacon	\N	\N	f	\N	120.67	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
361	1	1	\N	A-010-001	10	1	2268.48	erradicado	vivero	2026-02-04	\N	0101000020E610000000000000004053C01FC29235E0FF27C0	869.78	2024-09-13	2026-06-03	Bacon	\N	\N	f	\N	11.53	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
362	1	1	\N	A-010-002	10	2	2053.98	enfermo_critico	vivero	\N	\N	0101000020E61000001CE9708CFF3F53C01FC29235E0FF27C0	\N	2023-05-04	2024-06-24	Ettinger	\N	\N	f	\N	431.27	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
363	1	1	\N	A-010-003	10	3	1533.22	erradicado	senescencia	2026-02-02	\N	0101000020E6100000F2D1E118FF3F53C01FC29235E0FF27C0	1945.23	2024-04-27	2025-11-15	Ettinger	\N	\N	f	\N	22.31	5	Autem et delectus quas beatae voluptas.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
364	1	1	\N	A-010-004	10	4	2352.43	enfermo_critico	establecimiento	\N	\N	0101000020E61000000EBB52A5FE3F53C01FC29235E0FF27C0	1483.02	2024-06-13	\N	Bacon	\N	\N	f	\N	172.34	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
365	1	1	\N	A-010-005	10	5	964.37	con_estres	establecimiento	\N	\N	0101000020E6100000E3A3C331FE3F53C01FC29235E0FF27C0	1245.39	2025-05-13	2026-08-14	Hass	\N	\N	f	\N	475.17	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
366	1	1	\N	A-010-006	10	6	1389.18	muerto	senescencia	2025-09-20	\N	0101000020E6100000FF8C34BEFD3F53C01FC29235E0FF27C0	\N	2024-03-06	\N	Ettinger	\N	\N	f	\N	351.86	5	Quaerat quos consequuntur et eaque repudiandae esse.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
367	1	1	\N	A-010-007	10	7	880.99	con_estres	establecimiento	\N	\N	0101000020E6100000D575A54AFD3F53C01FC29235E0FF27C0	1561.25	2023-07-24	2025-05-01	Bacon	\N	\N	t	\N	181.19	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
368	1	1	\N	A-010-008	10	8	2145.49	enfermo_critico	senescencia	\N	\N	0101000020E6100000F15E16D7FC3F53C01FC29235E0FF27C0	\N	2024-04-07	2025-09-04	Hass	\N	\N	f	\N	281.45	9	Et vero quod odio adipisci.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
369	1	1	\N	A-010-009	10	9	2099.40	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E61000000D488763FC3F53C01FC29235E0FF27C0	\N	2021-12-16	\N	Fuerte	\N	\N	f	\N	50.95	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
370	1	1	\N	A-010-010	10	10	1737.34	erradicado	desarrollo_inmaduro	2026-08-25	\N	0101000020E6100000E330F8EFFB3F53C01FC29235E0FF27C0	1698.83	2024-11-29	2025-06-17	Zutano	\N	\N	f	\N	17.90	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
371	1	1	\N	A-010-011	10	11	1768.62	erradicado	desarrollo_inmaduro	2025-11-07	\N	0101000020E6100000FF19697CFB3F53C01FC29235E0FF27C0	\N	2022-06-09	\N	Ettinger	\N	\N	f	\N	99.09	7	Illum aut tempore omnis amet.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
372	1	1	\N	A-010-012	10	12	2104.15	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000D502DA08FB3F53C01FC29235E0FF27C0	927.97	2022-04-17	2023-05-31	Hass	\N	\N	f	\N	352.77	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
373	1	1	\N	A-010-013	10	13	1610.41	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000F1EB4A95FA3F53C01FC29235E0FF27C0	1597.28	2022-01-16	2024-06-14	Bacon	\N	\N	f	\N	194.29	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
374	1	1	\N	A-010-014	10	14	2416.10	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000C6D4BB21FA3F53C01FC29235E0FF27C0	1991.56	2025-07-14	2026-08-02	Hass	\N	\N	f	\N	152.58	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
375	1	1	\N	A-010-015	10	15	1006.63	con_estres	establecimiento	\N	\N	0101000020E6100000E2BD2CAEF93F53C01FC29235E0FF27C0	2010.65	2026-04-16	\N	Hass	\N	\N	f	\N	152.42	7	Non recusandae nemo in totam.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
376	1	1	\N	A-010-016	10	16	2145.29	erradicado	vivero	2026-05-22	\N	0101000020E6100000FEA69D3AF93F53C01FC29235E0FF27C0	1222.93	2022-09-18	\N	Zutano	\N	\N	f	\N	69.66	0	Atque necessitatibus incidunt necessitatibus eveniet quas magni reprehenderit.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
377	1	1	\N	A-010-017	10	17	1003.24	enfermo_critico	vivero	\N	\N	0101000020E6100000D48F0EC7F83F53C01FC29235E0FF27C0	934.31	2026-06-29	2026-08-24	Zutano	\N	\N	f	\N	171.09	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
378	1	1	\N	A-010-018	10	18	2148.08	excelente	establecimiento	\N	\N	0101000020E6100000F0787F53F83F53C01FC29235E0FF27C0	2088.95	2024-01-06	2024-09-28	Fuerte	\N	\N	f	\N	379.10	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
379	1	1	\N	A-010-019	10	19	2240.09	erradicado	desarrollo_inmaduro	2025-11-05	\N	0101000020E6100000C661F0DFF73F53C01FC29235E0FF27C0	1117.06	2022-05-19	2025-12-22	Hass	\N	\N	f	\N	70.91	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
380	1	1	\N	A-010-020	10	20	1204.60	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000E24A616CF73F53C01FC29235E0FF27C0	876.82	2025-09-25	2025-12-29	Bacon	\N	\N	f	\N	228.04	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
381	1	1	\N	A-010-021	10	21	2110.33	con_estres	senescencia	\N	\N	0101000020E6100000B733D2F8F63F53C01FC29235E0FF27C0	1532.94	2023-10-10	\N	Hass	\N	\N	t	\N	422.53	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
382	1	1	\N	A-010-022	10	22	1281.44	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000D31C4385F63F53C01FC29235E0FF27C0	\N	2025-04-09	2025-11-13	Ettinger	\N	\N	f	\N	465.31	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
383	1	1	\N	A-010-023	10	23	1082.41	erradicado	produccion_madura	2026-03-10	\N	0101000020E6100000EF05B411F63F53C01FC29235E0FF27C0	2254.22	2024-01-13	2025-11-27	Zutano	\N	\N	f	\N	11.81	3	Maxime velit at et cumque aliquam.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
384	1	1	\N	A-010-024	10	24	1363.61	con_estres	senescencia	\N	\N	0101000020E6100000C5EE249EF53F53C01FC29235E0FF27C0	1948.01	2022-05-17	2024-07-14	Ettinger	\N	\N	f	\N	302.17	10	Non rerum saepe id optio.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
385	1	1	\N	A-010-025	10	25	1131.39	con_estres	establecimiento	\N	\N	0101000020E6100000E1D7952AF53F53C01FC29235E0FF27C0	895.79	2023-09-27	2025-02-05	Ettinger	\N	\N	f	\N	383.04	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
386	1	1	\N	A-010-026	10	26	861.84	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000B7C006B7F43F53C01FC29235E0FF27C0	\N	2023-05-30	2024-03-24	Zutano	\N	\N	f	\N	451.76	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
387	1	1	\N	A-010-027	10	27	1328.19	enfermo_critico	establecimiento	\N	\N	0101000020E6100000D3A97743F43F53C01FC29235E0FF27C0	\N	2022-08-06	2024-07-16	Bacon	\N	\N	f	\N	255.58	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
388	1	1	\N	A-010-028	10	28	861.71	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000A892E8CFF33F53C01FC29235E0FF27C0	1639.99	2026-05-23	2026-06-03	Bacon	\N	\N	f	\N	145.92	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
389	1	1	\N	A-010-029	10	29	1551.96	erradicado	establecimiento	2026-02-12	\N	0101000020E6100000C47B595CF33F53C01FC29235E0FF27C0	\N	2023-03-14	\N	Hass	\N	\N	f	\N	261.94	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
390	1	1	\N	A-010-030	10	30	1463.65	muerto	produccion_madura	2026-07-13	\N	0101000020E6100000E164CAE8F23F53C01FC29235E0FF27C0	1149.56	2023-01-17	2023-12-27	Bacon	\N	\N	f	\N	260.66	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
391	1	1	\N	A-010-031	10	31	1581.05	muerto	produccion_madura	2026-05-11	\N	0101000020E6100000B64D3B75F23F53C01FC29235E0FF27C0	1217.64	2024-11-15	\N	Zutano	\N	\N	f	\N	47.25	7	Aspernatur voluptatem soluta hic quas nobis est quibusdam.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
392	1	1	\N	A-010-032	10	32	1247.33	muerto	establecimiento	2025-11-29	\N	0101000020E6100000D236AC01F23F53C01FC29235E0FF27C0	\N	2025-12-15	\N	Bacon	\N	\N	f	\N	452.78	8	Necessitatibus modi cupiditate sunt molestiae.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
393	1	1	\N	A-010-033	10	33	2265.23	muerto	desarrollo_inmaduro	2026-05-18	\N	0101000020E6100000A81F1D8EF13F53C01FC29235E0FF27C0	1493.10	2021-12-22	2026-01-29	Fuerte	\N	\N	f	\N	260.23	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
394	1	1	\N	A-010-034	10	34	1617.13	erradicado	establecimiento	2025-12-17	\N	0101000020E6100000C4088E1AF13F53C01FC29235E0FF27C0	\N	2021-11-21	\N	Zutano	\N	\N	f	\N	475.07	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
395	1	1	\N	A-010-035	10	35	1546.77	enfermo_critico	establecimiento	\N	\N	0101000020E61000009AF1FEA6F03F53C01FC29235E0FF27C0	1101.26	2022-10-08	\N	Zutano	\N	\N	f	\N	28.47	10	Nemo molestiae officiis quis minima iste tempore nihil.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
396	1	1	\N	A-010-036	10	36	1351.28	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000B6DA6F33F03F53C01FC29235E0FF27C0	1471.25	2022-09-26	2023-07-07	Hass	\N	\N	f	\N	308.98	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
397	1	1	\N	A-010-037	10	37	1641.10	muerto	produccion_madura	2025-11-06	\N	0101000020E6100000D2C3E0BFEF3F53C01FC29235E0FF27C0	2057.34	2022-07-25	2024-12-31	Ettinger	\N	\N	f	\N	399.43	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
398	1	1	\N	A-010-038	10	38	885.37	muerto	establecimiento	2026-03-13	\N	0101000020E6100000A7AC514CEF3F53C01FC29235E0FF27C0	1989.26	2024-04-20	2024-11-20	Ettinger	\N	\N	f	\N	163.50	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
399	1	1	\N	A-010-039	10	39	2366.50	erradicado	desarrollo_inmaduro	2025-12-22	\N	0101000020E6100000C395C2D8EE3F53C01FC29235E0FF27C0	1956.43	2026-07-08	2026-07-08	Ettinger	\N	\N	f	\N	57.03	5	Voluptas voluptatibus beatae in consequatur suscipit.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
400	1	1	\N	A-010-040	10	40	884.64	erradicado	produccion_madura	2026-06-13	\N	0101000020E6100000997E3365EE3F53C01FC29235E0FF27C0	866.37	2026-06-07	2026-08-20	Zutano	\N	\N	f	\N	378.23	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
401	1	1	\N	A-011-001	11	1	1521.43	muerto	vivero	2025-12-21	\N	0101000020E610000000000000004053C0BCBB4DADDCFF27C0	1047.25	2023-12-20	2024-06-26	Ettinger	\N	\N	f	\N	332.80	7	Incidunt reprehenderit neque nam qui et.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
402	1	1	\N	A-011-002	11	2	1032.94	excelente	establecimiento	\N	\N	0101000020E61000001CE9708CFF3F53C0BCBB4DADDCFF27C0	1734.30	2024-02-04	2025-07-19	Hass	\N	\N	f	\N	453.60	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
403	1	1	\N	A-011-003	11	3	1347.95	con_estres	vivero	\N	\N	0101000020E6100000F2D1E118FF3F53C0BCBB4DADDCFF27C0	2451.19	2023-07-17	\N	Bacon	\N	\N	f	\N	263.72	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
404	1	1	\N	A-011-004	11	4	1014.66	excelente	desarrollo_inmaduro	\N	\N	0101000020E61000000EBB52A5FE3F53C0BCBB4DADDCFF27C0	1084.28	2024-09-03	2024-09-13	Ettinger	\N	\N	f	\N	230.02	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
405	1	1	\N	A-011-005	11	5	1611.09	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000E3A3C331FE3F53C0BCBB4DADDCFF27C0	1467.65	2026-03-11	\N	Hass	\N	\N	f	\N	353.06	6	Repudiandae repellat culpa debitis exercitationem aliquid eaque.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
406	1	1	\N	A-011-006	11	6	1231.77	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000FF8C34BEFD3F53C0BCBB4DADDCFF27C0	1606.47	2024-05-01	\N	Fuerte	\N	\N	f	\N	224.94	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
407	1	1	\N	A-011-007	11	7	2155.99	muerto	establecimiento	2026-05-29	\N	0101000020E6100000D575A54AFD3F53C0BCBB4DADDCFF27C0	2477.66	2025-10-15	2026-02-17	Hass	\N	\N	f	\N	42.61	7	Rem maiores velit consequatur.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
408	1	1	\N	A-011-008	11	8	1006.87	muerto	vivero	2026-05-17	\N	0101000020E6100000F15E16D7FC3F53C0BCBB4DADDCFF27C0	1473.45	2026-07-24	2026-08-05	Zutano	\N	\N	f	\N	318.09	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
409	1	1	\N	A-011-009	11	9	1069.73	con_estres	produccion_madura	\N	\N	0101000020E61000000D488763FC3F53C0BCBB4DADDCFF27C0	1406.71	2022-09-24	\N	Zutano	\N	\N	f	\N	166.52	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
410	1	1	\N	A-011-010	11	10	2364.30	muerto	establecimiento	2026-03-20	\N	0101000020E6100000E330F8EFFB3F53C0BCBB4DADDCFF27C0	2058.06	2024-11-18	2025-06-30	Hass	\N	\N	f	\N	477.73	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
411	1	1	\N	A-011-011	11	11	1399.99	excelente	vivero	\N	\N	0101000020E6100000FF19697CFB3F53C0BCBB4DADDCFF27C0	896.26	2024-07-24	2025-05-01	Ettinger	\N	\N	f	\N	334.91	0	Consequatur quas mollitia qui sit.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
412	1	1	\N	A-011-012	11	12	1314.16	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000D502DA08FB3F53C0BCBB4DADDCFF27C0	\N	2025-09-06	2026-06-03	Ettinger	\N	\N	t	\N	205.92	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
413	1	1	\N	A-011-013	11	13	1159.90	excelente	produccion_madura	\N	\N	0101000020E6100000F1EB4A95FA3F53C0BCBB4DADDCFF27C0	1747.38	2026-07-11	\N	Hass	\N	\N	f	\N	31.93	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
414	1	1	\N	A-011-014	11	14	1472.95	excelente	produccion_madura	\N	\N	0101000020E6100000C6D4BB21FA3F53C0BCBB4DADDCFF27C0	\N	2023-04-23	2025-06-30	Zutano	\N	\N	f	\N	471.41	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
415	1	1	\N	A-011-015	11	15	2491.85	excelente	produccion_madura	\N	\N	0101000020E6100000E2BD2CAEF93F53C0BCBB4DADDCFF27C0	2149.60	2025-08-11	2025-11-17	Zutano	\N	\N	f	\N	171.43	6	Eaque et ea impedit quia et saepe doloremque.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
416	1	1	\N	A-011-016	11	16	2490.11	erradicado	produccion_madura	2026-07-08	\N	0101000020E6100000FEA69D3AF93F53C0BCBB4DADDCFF27C0	1159.58	2023-06-27	2023-10-13	Ettinger	\N	\N	f	\N	74.54	4	Id nemo sit est quisquam ratione nihil aut.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
417	1	1	\N	A-011-017	11	17	1200.39	muerto	vivero	2026-04-19	\N	0101000020E6100000D48F0EC7F83F53C0BCBB4DADDCFF27C0	2183.53	2025-03-03	2026-08-24	Zutano	\N	\N	f	\N	17.17	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
418	1	1	\N	A-011-018	11	18	1252.41	excelente	produccion_madura	\N	\N	0101000020E6100000F0787F53F83F53C0BCBB4DADDCFF27C0	1778.73	2025-01-16	\N	Ettinger	\N	\N	t	\N	403.00	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
419	1	1	\N	A-011-019	11	19	1516.06	erradicado	vivero	2025-09-03	\N	0101000020E6100000C661F0DFF73F53C0BCBB4DADDCFF27C0	917.25	2025-08-03	2026-02-16	Bacon	\N	\N	f	\N	126.56	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
420	1	1	\N	A-011-020	11	20	1402.22	excelente	produccion_madura	\N	\N	0101000020E6100000E24A616CF73F53C0BCBB4DADDCFF27C0	981.95	2025-10-17	2026-04-05	Fuerte	\N	\N	f	\N	253.95	7	Sit consequatur quos id nihil quisquam.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
421	1	1	\N	A-011-021	11	21	1463.77	muerto	establecimiento	2025-10-27	\N	0101000020E6100000B733D2F8F63F53C0BCBB4DADDCFF27C0	1411.53	2025-04-08	\N	Hass	\N	\N	f	\N	208.79	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
422	1	1	\N	A-011-022	11	22	2368.44	erradicado	produccion_madura	2025-09-04	\N	0101000020E6100000D31C4385F63F53C0BCBB4DADDCFF27C0	1080.01	2023-03-21	\N	Bacon	\N	\N	f	\N	262.17	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
423	1	1	\N	A-011-023	11	23	989.10	erradicado	produccion_madura	2025-10-08	\N	0101000020E6100000EF05B411F63F53C0BCBB4DADDCFF27C0	1974.66	2026-05-12	2026-05-28	Fuerte	\N	\N	f	\N	267.71	9	Aspernatur quo aut consequatur impedit nulla facilis.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
424	1	1	\N	A-011-024	11	24	1520.19	con_estres	produccion_madura	\N	\N	0101000020E6100000C5EE249EF53F53C0BCBB4DADDCFF27C0	2213.83	2026-02-21	2026-03-04	Bacon	\N	\N	f	\N	417.10	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
425	1	1	\N	A-011-025	11	25	875.53	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000E1D7952AF53F53C0BCBB4DADDCFF27C0	1655.61	2026-02-08	\N	Bacon	\N	\N	f	\N	181.65	0	Doloribus dolores dolorem iure vitae commodi nesciunt.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
426	1	1	\N	A-011-026	11	26	1830.64	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000B7C006B7F43F53C0BCBB4DADDCFF27C0	2048.35	2022-02-12	2024-11-11	Zutano	\N	\N	f	\N	496.59	9	Et ipsam debitis voluptates perspiciatis ut molestiae veritatis incidunt.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
427	1	1	\N	A-011-027	11	27	2242.31	muerto	vivero	2026-05-10	\N	0101000020E6100000D3A97743F43F53C0BCBB4DADDCFF27C0	1260.94	2021-11-25	2022-11-17	Ettinger	\N	\N	f	\N	8.31	7	Vitae nesciunt possimus odio et corrupti dicta repellat.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
428	1	1	\N	A-011-028	11	28	1061.70	excelente	senescencia	\N	\N	0101000020E6100000A892E8CFF33F53C0BCBB4DADDCFF27C0	1736.32	2024-02-21	2025-05-20	Ettinger	\N	\N	f	\N	282.44	9	Numquam sit dignissimos maxime velit aut.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
429	1	1	\N	A-011-029	11	29	1608.35	muerto	produccion_madura	2026-07-23	\N	0101000020E6100000C47B595CF33F53C0BCBB4DADDCFF27C0	2496.37	2026-04-23	2026-07-29	Hass	\N	\N	t	\N	390.05	9	Eos illo tempora beatae earum.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
430	1	1	\N	A-011-030	11	30	1625.97	excelente	vivero	\N	\N	0101000020E6100000E164CAE8F23F53C0BCBB4DADDCFF27C0	2150.82	2024-05-30	2025-04-01	Bacon	\N	\N	f	\N	358.62	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
431	1	1	\N	A-011-031	11	31	2042.22	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000B64D3B75F23F53C0BCBB4DADDCFF27C0	873.50	2021-12-20	\N	Zutano	\N	\N	t	\N	10.54	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
432	1	1	\N	A-011-032	11	32	2171.68	excelente	vivero	\N	\N	0101000020E6100000D236AC01F23F53C0BCBB4DADDCFF27C0	1131.76	2023-05-06	2025-01-26	Bacon	\N	\N	f	\N	146.59	8	Laboriosam et dolor quidem nostrum est numquam ut.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
433	1	1	\N	A-011-033	11	33	917.87	erradicado	establecimiento	2026-04-21	\N	0101000020E6100000A81F1D8EF13F53C0BCBB4DADDCFF27C0	\N	2024-08-31	2024-11-09	Bacon	\N	\N	f	\N	208.81	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
434	1	1	\N	A-011-034	11	34	1480.93	muerto	desarrollo_inmaduro	2026-04-07	\N	0101000020E6100000C4088E1AF13F53C0BCBB4DADDCFF27C0	1061.23	2021-11-06	2022-05-07	Bacon	\N	\N	f	\N	9.68	5	Vel molestias earum necessitatibus nemo necessitatibus.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
435	1	1	\N	A-011-035	11	35	864.63	con_estres	produccion_madura	\N	\N	0101000020E61000009AF1FEA6F03F53C0BCBB4DADDCFF27C0	\N	2024-02-08	2024-12-14	Zutano	\N	\N	f	\N	222.47	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
436	1	1	\N	A-011-036	11	36	2287.89	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000B6DA6F33F03F53C0BCBB4DADDCFF27C0	2182.30	2024-12-13	2025-12-05	Fuerte	\N	\N	f	\N	225.40	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
437	1	1	\N	A-011-037	11	37	870.90	muerto	desarrollo_inmaduro	2025-10-28	\N	0101000020E6100000D2C3E0BFEF3F53C0BCBB4DADDCFF27C0	\N	2026-04-09	2026-06-25	Fuerte	\N	\N	f	\N	65.79	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
438	1	1	\N	A-011-038	11	38	1563.93	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000A7AC514CEF3F53C0BCBB4DADDCFF27C0	\N	2022-11-08	\N	Fuerte	\N	\N	f	\N	307.83	1	Ut aut ut illum magni necessitatibus veniam necessitatibus.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
439	1	1	\N	A-011-039	11	39	2392.93	muerto	establecimiento	2026-06-22	\N	0101000020E6100000C395C2D8EE3F53C0BCBB4DADDCFF27C0	\N	2024-06-14	\N	Ettinger	\N	\N	f	\N	348.89	5	Suscipit sapiente omnis et esse eum debitis perferendis id.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
440	1	1	\N	A-011-040	11	40	1305.69	muerto	senescencia	2026-03-22	\N	0101000020E6100000997E3365EE3F53C0BCBB4DADDCFF27C0	2071.82	2021-10-23	2025-12-04	Hass	\N	\N	f	\N	280.36	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
441	1	1	\N	A-012-001	12	1	1920.55	muerto	vivero	2025-08-31	\N	0101000020E610000000000000004053C026B30825D9FF27C0	1929.11	2022-12-15	\N	Hass	\N	\N	f	\N	294.73	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
442	1	1	\N	A-012-002	12	2	1811.43	enfermo_critico	produccion_madura	\N	\N	0101000020E61000001CE9708CFF3F53C026B30825D9FF27C0	\N	2023-07-04	\N	Fuerte	\N	\N	f	\N	381.77	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
443	1	1	\N	A-012-003	12	3	2202.15	erradicado	senescencia	2026-01-04	\N	0101000020E6100000F2D1E118FF3F53C026B30825D9FF27C0	\N	2022-04-06	2025-07-09	Bacon	\N	\N	f	\N	232.54	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
444	1	1	\N	A-012-004	12	4	1763.60	muerto	vivero	2025-09-30	\N	0101000020E61000000EBB52A5FE3F53C026B30825D9FF27C0	1240.23	2025-07-30	2026-03-04	Zutano	\N	\N	f	\N	289.43	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
445	1	1	\N	A-012-005	12	5	1590.88	enfermo_critico	vivero	\N	\N	0101000020E6100000E3A3C331FE3F53C026B30825D9FF27C0	1770.06	2025-09-26	2026-06-09	Fuerte	\N	\N	f	\N	30.29	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
446	1	1	\N	A-012-006	12	6	2478.78	excelente	establecimiento	\N	\N	0101000020E6100000FF8C34BEFD3F53C026B30825D9FF27C0	1548.51	2023-09-07	2026-06-21	Fuerte	\N	\N	f	\N	125.24	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
447	1	1	\N	A-012-007	12	7	1521.32	erradicado	establecimiento	2026-04-01	\N	0101000020E6100000D575A54AFD3F53C026B30825D9FF27C0	1526.24	2022-03-28	2025-12-17	Zutano	\N	\N	f	\N	132.56	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
448	1	1	\N	A-012-008	12	8	920.26	excelente	vivero	\N	\N	0101000020E6100000F15E16D7FC3F53C026B30825D9FF27C0	1217.88	2022-03-10	\N	Fuerte	\N	\N	f	\N	158.09	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
449	1	1	\N	A-012-009	12	9	2388.14	excelente	senescencia	\N	\N	0101000020E61000000D488763FC3F53C026B30825D9FF27C0	1587.65	2026-06-29	2026-06-29	Zutano	\N	\N	f	\N	435.85	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
450	1	1	\N	A-012-010	12	10	864.50	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000E330F8EFFB3F53C026B30825D9FF27C0	1826.52	2025-04-04	\N	Ettinger	\N	\N	f	\N	310.04	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
451	1	1	\N	A-012-011	12	11	1356.93	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000FF19697CFB3F53C026B30825D9FF27C0	\N	2024-04-01	2024-08-15	Bacon	\N	\N	f	\N	125.93	0	Voluptatem minus animi optio ea.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
452	1	1	\N	A-012-012	12	12	1083.54	erradicado	produccion_madura	2026-06-24	\N	0101000020E6100000D502DA08FB3F53C026B30825D9FF27C0	993.70	2026-02-03	2026-05-28	Bacon	\N	\N	f	\N	142.75	5	Sapiente quod repudiandae et eum.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
453	1	1	\N	A-012-013	12	13	1571.68	erradicado	produccion_madura	2026-06-05	\N	0101000020E6100000F1EB4A95FA3F53C026B30825D9FF27C0	1534.51	2023-12-05	\N	Fuerte	\N	\N	f	\N	159.69	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
454	1	1	\N	A-012-014	12	14	1091.01	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000C6D4BB21FA3F53C026B30825D9FF27C0	1854.44	2023-02-23	2024-07-17	Zutano	\N	\N	f	\N	99.16	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
455	1	1	\N	A-012-015	12	15	1024.89	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000E2BD2CAEF93F53C026B30825D9FF27C0	\N	2022-03-02	2024-09-26	Bacon	\N	\N	f	\N	499.57	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
456	1	1	\N	A-012-016	12	16	1337.77	muerto	produccion_madura	2025-12-15	\N	0101000020E6100000FEA69D3AF93F53C026B30825D9FF27C0	1932.41	2024-07-17	2025-07-20	Hass	\N	\N	f	\N	105.22	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
457	1	1	\N	A-012-017	12	17	1686.04	enfermo_critico	establecimiento	\N	\N	0101000020E6100000D48F0EC7F83F53C026B30825D9FF27C0	1354.50	2023-12-28	2025-06-25	Bacon	\N	\N	f	\N	276.95	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
458	1	1	\N	A-012-018	12	18	1770.75	muerto	produccion_madura	2026-04-07	\N	0101000020E6100000F0787F53F83F53C026B30825D9FF27C0	966.88	2021-10-15	\N	Zutano	\N	\N	f	\N	485.24	1	Voluptatem laborum est voluptas beatae molestiae.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
459	1	1	\N	A-012-019	12	19	1010.90	con_estres	vivero	\N	\N	0101000020E6100000C661F0DFF73F53C026B30825D9FF27C0	802.02	2026-03-15	2026-03-18	Zutano	\N	\N	f	\N	193.06	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
460	1	1	\N	A-012-020	12	20	1047.90	con_estres	establecimiento	\N	\N	0101000020E6100000E24A616CF73F53C026B30825D9FF27C0	\N	2022-05-12	2024-01-18	Fuerte	\N	\N	f	\N	342.71	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
461	1	1	\N	A-012-021	12	21	808.07	excelente	produccion_madura	\N	\N	0101000020E6100000B733D2F8F63F53C026B30825D9FF27C0	1968.90	2025-08-14	2026-02-21	Ettinger	\N	\N	f	\N	35.06	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
462	1	1	\N	A-012-022	12	22	1288.19	muerto	vivero	2025-11-12	\N	0101000020E6100000D31C4385F63F53C026B30825D9FF27C0	2000.50	2022-12-01	\N	Fuerte	\N	\N	f	\N	139.25	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
463	1	1	\N	A-012-023	12	23	1921.82	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000EF05B411F63F53C026B30825D9FF27C0	923.86	2022-07-14	2026-07-12	Hass	\N	\N	f	\N	484.55	0	Tenetur nulla harum blanditiis eum iste id.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
464	1	1	\N	A-012-024	12	24	1801.70	erradicado	vivero	2026-05-04	\N	0101000020E6100000C5EE249EF53F53C026B30825D9FF27C0	1439.72	2024-11-14	2025-02-22	Zutano	\N	\N	f	\N	18.78	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
465	1	1	\N	A-012-025	12	25	930.49	con_estres	vivero	\N	\N	0101000020E6100000E1D7952AF53F53C026B30825D9FF27C0	\N	2021-10-26	2023-11-04	Bacon	\N	\N	f	\N	14.74	2	Ea possimus recusandae omnis eum consequatur at molestiae.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
466	1	1	\N	A-012-026	12	26	1899.18	enfermo_critico	senescencia	\N	\N	0101000020E6100000B7C006B7F43F53C026B30825D9FF27C0	1385.34	2026-02-14	2026-07-23	Zutano	\N	\N	f	\N	180.72	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
467	1	1	\N	A-012-027	12	27	1902.14	muerto	produccion_madura	2026-02-21	\N	0101000020E6100000D3A97743F43F53C026B30825D9FF27C0	2040.19	2022-11-07	2024-10-22	Hass	\N	\N	f	\N	405.33	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
468	1	1	\N	A-012-028	12	28	1450.14	muerto	produccion_madura	2026-06-22	\N	0101000020E6100000A892E8CFF33F53C026B30825D9FF27C0	1670.96	2026-03-05	2026-07-19	Zutano	\N	\N	f	\N	394.16	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
469	1	1	\N	A-012-029	12	29	1779.53	muerto	produccion_madura	2026-08-26	\N	0101000020E6100000C47B595CF33F53C026B30825D9FF27C0	2446.09	2023-08-04	2024-03-31	Zutano	\N	\N	f	\N	440.59	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
470	1	1	\N	A-012-030	12	30	1036.72	enfermo_critico	senescencia	\N	\N	0101000020E6100000E164CAE8F23F53C026B30825D9FF27C0	2445.06	2024-12-28	2025-10-20	Ettinger	\N	\N	f	\N	326.75	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
471	1	1	\N	A-012-031	12	31	1841.26	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000B64D3B75F23F53C026B30825D9FF27C0	1371.57	2025-06-28	2025-08-12	Bacon	\N	\N	f	\N	341.72	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
472	1	1	\N	A-012-032	12	32	947.18	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000D236AC01F23F53C026B30825D9FF27C0	\N	2021-09-20	2024-02-15	Bacon	\N	\N	f	\N	149.33	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
473	1	1	\N	A-012-033	12	33	1959.01	enfermo_critico	establecimiento	\N	\N	0101000020E6100000A81F1D8EF13F53C026B30825D9FF27C0	1649.99	2021-12-27	2022-06-23	Bacon	\N	\N	f	\N	412.50	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
474	1	1	\N	A-012-034	12	34	2162.18	erradicado	establecimiento	2025-12-13	\N	0101000020E6100000C4088E1AF13F53C026B30825D9FF27C0	1457.95	2025-08-04	\N	Fuerte	\N	\N	f	\N	478.43	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
475	1	1	\N	A-012-035	12	35	1983.23	erradicado	senescencia	2026-05-10	\N	0101000020E61000009AF1FEA6F03F53C026B30825D9FF27C0	1970.10	2025-11-16	2026-03-16	Bacon	\N	\N	f	\N	464.15	9	Optio officiis aperiam atque veritatis.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
476	1	1	\N	A-012-036	12	36	1271.39	muerto	desarrollo_inmaduro	2026-02-18	\N	0101000020E6100000B6DA6F33F03F53C026B30825D9FF27C0	2327.60	2026-05-09	2026-08-01	Ettinger	\N	\N	f	\N	336.89	9	Temporibus officiis ea nobis voluptas voluptate.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
477	1	1	\N	A-012-037	12	37	880.60	muerto	desarrollo_inmaduro	2026-02-10	\N	0101000020E6100000D2C3E0BFEF3F53C026B30825D9FF27C0	877.07	2025-01-25	\N	Hass	\N	\N	f	\N	76.86	8	Impedit officiis impedit excepturi non est ut itaque.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
478	1	1	\N	A-012-038	12	38	2242.77	excelente	senescencia	\N	\N	0101000020E6100000A7AC514CEF3F53C026B30825D9FF27C0	1405.65	2022-04-11	2025-07-31	Hass	\N	\N	f	\N	308.91	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
479	1	1	\N	A-012-039	12	39	1247.60	erradicado	establecimiento	2026-01-18	\N	0101000020E6100000C395C2D8EE3F53C026B30825D9FF27C0	1749.63	2025-02-19	\N	Bacon	\N	\N	f	\N	33.18	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
480	1	1	\N	A-012-040	12	40	845.22	muerto	produccion_madura	2026-06-19	\N	0101000020E6100000997E3365EE3F53C026B30825D9FF27C0	1943.40	2026-03-12	2026-07-19	Ettinger	\N	\N	f	\N	21.98	5	Et et non harum delectus voluptas.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
481	1	1	\N	A-013-001	13	1	2148.97	con_estres	establecimiento	\N	\N	0101000020E610000000000000004053C0C3ACC39CD5FF27C0	940.07	2025-10-25	\N	Ettinger	\N	\N	f	\N	126.67	6	Sit et similique cumque maxime reiciendis aut.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
482	1	1	\N	A-013-002	13	2	1385.87	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E61000001CE9708CFF3F53C0C3ACC39CD5FF27C0	1302.99	2021-09-06	2024-07-16	Hass	\N	\N	f	\N	188.36	7	Sit saepe velit ex error similique reprehenderit.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
483	1	1	\N	A-013-003	13	3	1054.26	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000F2D1E118FF3F53C0C3ACC39CD5FF27C0	1463.34	2021-09-07	2025-01-03	Hass	\N	\N	f	\N	268.78	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
484	1	1	\N	A-013-004	13	4	1414.92	excelente	vivero	\N	\N	0101000020E61000000EBB52A5FE3F53C0C3ACC39CD5FF27C0	1272.00	2024-01-18	2025-10-23	Fuerte	\N	\N	f	\N	12.05	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
485	1	1	\N	A-013-005	13	5	1775.65	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000E3A3C331FE3F53C0C3ACC39CD5FF27C0	\N	2022-10-05	2025-09-14	Ettinger	\N	\N	f	\N	358.58	6	Id quia laudantium cumque eligendi vel est.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
486	1	1	\N	A-013-006	13	6	1249.66	con_estres	produccion_madura	\N	\N	0101000020E6100000FF8C34BEFD3F53C0C3ACC39CD5FF27C0	2413.79	2021-08-27	\N	Hass	\N	\N	f	\N	158.27	5	Ipsum pariatur quia suscipit a voluptatem impedit.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
487	1	1	\N	A-013-007	13	7	2394.12	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000D575A54AFD3F53C0C3ACC39CD5FF27C0	2493.22	2025-10-09	2025-11-11	Bacon	\N	\N	t	\N	485.44	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
488	1	1	\N	A-013-008	13	8	1129.09	erradicado	vivero	2025-10-28	\N	0101000020E6100000F15E16D7FC3F53C0C3ACC39CD5FF27C0	2414.92	2024-12-02	2026-06-06	Hass	\N	\N	f	\N	118.53	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
489	1	1	\N	A-013-009	13	9	1470.96	muerto	vivero	2026-07-22	\N	0101000020E61000000D488763FC3F53C0C3ACC39CD5FF27C0	1456.52	2026-04-12	2026-06-15	Zutano	\N	\N	f	\N	180.01	0	Expedita similique sapiente quo.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
490	1	1	\N	A-013-010	13	10	978.45	con_estres	senescencia	\N	\N	0101000020E6100000E330F8EFFB3F53C0C3ACC39CD5FF27C0	\N	2024-08-30	2026-05-29	Zutano	\N	\N	f	\N	363.75	9	Aut doloribus et sunt repudiandae molestias vel hic.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
491	1	1	\N	A-013-011	13	11	1449.12	excelente	vivero	\N	\N	0101000020E6100000FF19697CFB3F53C0C3ACC39CD5FF27C0	1321.50	2024-11-04	2026-08-16	Bacon	\N	\N	f	\N	271.82	1	Aut et est quam ut quibusdam vitae quos.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
492	1	1	\N	A-013-012	13	12	1057.79	erradicado	senescencia	2026-07-11	\N	0101000020E6100000D502DA08FB3F53C0C3ACC39CD5FF27C0	1530.18	2022-09-06	2023-04-16	Bacon	\N	\N	f	\N	66.62	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
493	1	1	\N	A-013-013	13	13	2147.82	excelente	establecimiento	\N	\N	0101000020E6100000F1EB4A95FA3F53C0C3ACC39CD5FF27C0	1302.66	2023-05-03	2023-05-15	Hass	\N	\N	f	\N	482.52	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
494	1	1	\N	A-013-014	13	14	1231.35	erradicado	senescencia	2025-11-18	\N	0101000020E6100000C6D4BB21FA3F53C0C3ACC39CD5FF27C0	1954.38	2026-05-08	\N	Fuerte	\N	\N	f	\N	486.68	6	Et sequi rerum optio aliquid.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
495	1	1	\N	A-013-015	13	15	1656.67	muerto	vivero	2026-04-02	\N	0101000020E6100000E2BD2CAEF93F53C0C3ACC39CD5FF27C0	1810.73	2026-07-17	\N	Hass	\N	\N	f	\N	458.67	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
496	1	1	\N	A-013-016	13	16	2141.15	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000FEA69D3AF93F53C0C3ACC39CD5FF27C0	2070.56	2023-03-24	2024-09-30	Hass	\N	\N	f	\N	73.12	10	Atque explicabo facilis accusantium quas.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
497	1	1	\N	A-013-017	13	17	2218.77	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000D48F0EC7F83F53C0C3ACC39CD5FF27C0	\N	2024-03-20	2025-04-13	Bacon	\N	\N	f	\N	202.57	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
498	1	1	\N	A-013-018	13	18	1116.76	muerto	desarrollo_inmaduro	2026-05-02	\N	0101000020E6100000F0787F53F83F53C0C3ACC39CD5FF27C0	2485.38	2023-05-04	2025-10-21	Bacon	\N	\N	f	\N	103.79	3	Nemo laboriosam nam itaque officia iusto dolor.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
499	1	1	\N	A-013-019	13	19	1595.61	erradicado	establecimiento	2025-10-09	\N	0101000020E6100000C661F0DFF73F53C0C3ACC39CD5FF27C0	\N	2025-11-11	\N	Hass	\N	\N	f	\N	246.88	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
500	1	1	\N	A-013-020	13	20	2476.06	muerto	produccion_madura	2026-05-12	\N	0101000020E6100000E24A616CF73F53C0C3ACC39CD5FF27C0	2428.11	2022-01-21	2026-02-20	Zutano	\N	\N	f	\N	85.05	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
501	1	1	\N	A-013-021	13	21	1542.28	erradicado	produccion_madura	2026-07-30	\N	0101000020E6100000B733D2F8F63F53C0C3ACC39CD5FF27C0	1133.29	2022-01-23	\N	Bacon	\N	\N	f	\N	262.81	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
502	1	1	\N	A-013-022	13	22	1058.42	muerto	desarrollo_inmaduro	2026-05-07	\N	0101000020E6100000D31C4385F63F53C0C3ACC39CD5FF27C0	\N	2024-10-12	2026-02-25	Bacon	\N	\N	f	\N	474.50	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
503	1	1	\N	A-013-023	13	23	1176.80	con_estres	senescencia	\N	\N	0101000020E6100000EF05B411F63F53C0C3ACC39CD5FF27C0	2130.35	2025-03-21	\N	Bacon	\N	\N	f	\N	111.34	1	Pariatur suscipit soluta delectus possimus sed delectus.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
504	1	1	\N	A-013-024	13	24	913.35	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000C5EE249EF53F53C0C3ACC39CD5FF27C0	\N	2025-02-11	2025-05-14	Ettinger	\N	\N	f	\N	356.62	9	Officiis molestiae explicabo sit voluptates et enim sunt ea.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
505	1	1	\N	A-013-025	13	25	2104.21	excelente	produccion_madura	\N	\N	0101000020E6100000E1D7952AF53F53C0C3ACC39CD5FF27C0	2075.20	2023-06-28	2025-04-29	Bacon	\N	\N	f	\N	56.59	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
506	1	1	\N	A-013-026	13	26	1016.27	muerto	vivero	2026-07-17	\N	0101000020E6100000B7C006B7F43F53C0C3ACC39CD5FF27C0	2285.23	2022-06-12	2024-06-10	Hass	\N	\N	f	\N	297.94	5	Laborum est quo dignissimos commodi dolores omnis qui.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
507	1	1	\N	A-013-027	13	27	998.96	excelente	produccion_madura	\N	\N	0101000020E6100000D3A97743F43F53C0C3ACC39CD5FF27C0	1750.43	2024-02-19	2026-04-07	Zutano	\N	\N	f	\N	33.60	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
508	1	1	\N	A-013-028	13	28	945.23	erradicado	establecimiento	2026-01-14	\N	0101000020E6100000A892E8CFF33F53C0C3ACC39CD5FF27C0	\N	2025-01-08	2025-08-17	Zutano	\N	\N	f	\N	96.38	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
509	1	1	\N	A-013-029	13	29	1625.92	muerto	desarrollo_inmaduro	2026-08-11	\N	0101000020E6100000C47B595CF33F53C0C3ACC39CD5FF27C0	2089.93	2026-01-04	\N	Ettinger	\N	\N	f	\N	401.25	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
510	1	1	\N	A-013-030	13	30	1824.50	erradicado	establecimiento	2025-10-01	\N	0101000020E6100000E164CAE8F23F53C0C3ACC39CD5FF27C0	1055.90	2025-05-19	\N	Ettinger	\N	\N	f	\N	179.09	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
511	1	1	\N	A-013-031	13	31	1594.36	erradicado	establecimiento	2026-04-25	\N	0101000020E6100000B64D3B75F23F53C0C3ACC39CD5FF27C0	1862.01	2023-08-19	\N	Bacon	\N	\N	f	\N	17.43	10	Consequatur ipsum temporibus facilis et.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
512	1	1	\N	A-013-032	13	32	917.66	muerto	establecimiento	2026-05-11	\N	0101000020E6100000D236AC01F23F53C0C3ACC39CD5FF27C0	1309.29	2025-02-22	2025-03-05	Ettinger	\N	\N	f	\N	112.30	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
513	1	1	\N	A-013-033	13	33	1197.94	erradicado	senescencia	2026-04-17	\N	0101000020E6100000A81F1D8EF13F53C0C3ACC39CD5FF27C0	1341.70	2023-05-28	2025-11-08	Zutano	\N	\N	f	\N	301.62	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
514	1	1	\N	A-013-034	13	34	2273.78	muerto	vivero	2025-11-30	\N	0101000020E6100000C4088E1AF13F53C0C3ACC39CD5FF27C0	1642.47	2021-10-08	2024-02-25	Ettinger	\N	\N	f	\N	44.28	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
515	1	1	\N	A-013-035	13	35	2190.99	excelente	vivero	\N	\N	0101000020E61000009AF1FEA6F03F53C0C3ACC39CD5FF27C0	1060.71	2025-09-15	\N	Hass	\N	\N	t	\N	56.11	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
516	1	1	\N	A-013-036	13	36	938.41	excelente	senescencia	\N	\N	0101000020E6100000B6DA6F33F03F53C0C3ACC39CD5FF27C0	1004.04	2023-12-20	2024-07-23	Zutano	\N	\N	f	\N	405.50	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
517	1	1	\N	A-013-037	13	37	1410.35	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000D2C3E0BFEF3F53C0C3ACC39CD5FF27C0	1252.69	2024-06-20	2024-06-26	Ettinger	\N	\N	f	\N	434.11	7	Itaque consequuntur reprehenderit fuga unde.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
518	1	1	\N	A-013-038	13	38	1012.19	muerto	produccion_madura	2026-07-12	\N	0101000020E6100000A7AC514CEF3F53C0C3ACC39CD5FF27C0	\N	2024-08-22	2024-12-08	Hass	\N	\N	f	\N	374.79	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
519	1	1	\N	A-013-039	13	39	1828.67	excelente	senescencia	\N	\N	0101000020E6100000C395C2D8EE3F53C0C3ACC39CD5FF27C0	1911.71	2024-04-03	2024-07-25	Hass	\N	\N	f	\N	426.06	10	Iusto perferendis id aut aliquid architecto eum non temporibus.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
520	1	1	\N	A-013-040	13	40	2374.66	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000997E3365EE3F53C0C3ACC39CD5FF27C0	1024.55	2025-06-26	2025-10-29	Fuerte	\N	\N	f	\N	389.23	2	Dignissimos aperiam tenetur sit provident.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
521	1	1	\N	A-014-001	14	1	1578.02	erradicado	desarrollo_inmaduro	2026-05-15	\N	0101000020E610000000000000004053C060A67E14D2FF27C0	1231.04	2025-12-21	2026-07-27	Ettinger	\N	\N	f	\N	192.97	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
522	1	1	\N	A-014-002	14	2	813.29	muerto	produccion_madura	2025-10-06	\N	0101000020E61000001CE9708CFF3F53C060A67E14D2FF27C0	904.07	2022-01-19	\N	Ettinger	\N	\N	f	\N	197.55	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
523	1	1	\N	A-014-003	14	3	1655.73	erradicado	vivero	2026-04-26	\N	0101000020E6100000F2D1E118FF3F53C060A67E14D2FF27C0	2016.27	2025-05-15	\N	Bacon	\N	\N	t	\N	433.03	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
524	1	1	\N	A-014-004	14	4	1607.48	excelente	senescencia	\N	\N	0101000020E61000000EBB52A5FE3F53C060A67E14D2FF27C0	\N	2025-06-17	2025-08-21	Bacon	\N	\N	f	\N	369.38	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
525	1	1	\N	A-014-005	14	5	2189.44	con_estres	senescencia	\N	\N	0101000020E6100000E3A3C331FE3F53C060A67E14D2FF27C0	1484.66	2022-07-17	2022-11-17	Ettinger	\N	\N	f	\N	263.59	5	Atque laudantium culpa ut nesciunt quisquam expedita sint.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
526	1	1	\N	A-014-006	14	6	2020.56	excelente	senescencia	\N	\N	0101000020E6100000FF8C34BEFD3F53C060A67E14D2FF27C0	\N	2022-06-29	2026-06-15	Bacon	\N	\N	f	\N	92.66	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
527	1	1	\N	A-014-007	14	7	891.10	con_estres	produccion_madura	\N	\N	0101000020E6100000D575A54AFD3F53C060A67E14D2FF27C0	1886.64	2026-01-22	\N	Hass	\N	\N	f	\N	167.52	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
528	1	1	\N	A-014-008	14	8	847.47	erradicado	vivero	2025-12-28	\N	0101000020E6100000F15E16D7FC3F53C060A67E14D2FF27C0	1430.00	2021-12-29	\N	Bacon	\N	\N	f	\N	78.37	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
529	1	1	\N	A-014-009	14	9	1123.59	enfermo_critico	establecimiento	\N	\N	0101000020E61000000D488763FC3F53C060A67E14D2FF27C0	2419.61	2024-01-01	2025-11-12	Fuerte	\N	\N	f	\N	453.03	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
530	1	1	\N	A-014-010	14	10	1345.95	muerto	vivero	2026-03-02	\N	0101000020E6100000E330F8EFFB3F53C060A67E14D2FF27C0	1823.99	2023-09-02	2026-08-04	Fuerte	\N	\N	f	\N	498.23	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
531	1	1	\N	A-014-011	14	11	2182.95	con_estres	senescencia	\N	\N	0101000020E6100000FF19697CFB3F53C060A67E14D2FF27C0	1632.50	2024-06-22	2026-05-19	Hass	\N	\N	f	\N	128.39	7	Libero recusandae et beatae ut.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
532	1	1	\N	A-014-012	14	12	1662.13	excelente	vivero	\N	\N	0101000020E6100000D502DA08FB3F53C060A67E14D2FF27C0	1955.07	2026-02-18	2026-04-18	Ettinger	\N	\N	f	\N	128.09	8	Et quisquam sunt deserunt.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
533	1	1	\N	A-014-013	14	13	2306.28	enfermo_critico	vivero	\N	\N	0101000020E6100000F1EB4A95FA3F53C060A67E14D2FF27C0	2078.58	2025-04-30	2025-11-01	Hass	\N	\N	t	\N	208.00	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
534	1	1	\N	A-014-014	14	14	1406.33	enfermo_critico	establecimiento	\N	\N	0101000020E6100000C6D4BB21FA3F53C060A67E14D2FF27C0	1132.57	2024-04-15	\N	Zutano	\N	\N	f	\N	35.90	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
535	1	1	\N	A-014-015	14	15	1990.07	muerto	senescencia	2026-07-26	\N	0101000020E6100000E2BD2CAEF93F53C060A67E14D2FF27C0	1895.61	2024-03-24	2026-07-31	Ettinger	\N	\N	f	\N	422.03	2	Sed quidem dolorem nobis est provident velit.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
536	1	1	\N	A-014-016	14	16	2152.39	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000FEA69D3AF93F53C060A67E14D2FF27C0	1778.62	2024-04-08	2024-06-02	Zutano	\N	\N	f	\N	259.93	2	Architecto aut nihil pariatur voluptatem voluptates dolorem.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
537	1	1	\N	A-014-017	14	17	2167.88	con_estres	produccion_madura	\N	\N	0101000020E6100000D48F0EC7F83F53C060A67E14D2FF27C0	1108.89	2026-06-19	2026-08-11	Hass	\N	\N	f	\N	326.84	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
538	1	1	\N	A-014-018	14	18	1868.57	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000F0787F53F83F53C060A67E14D2FF27C0	\N	2026-02-02	2026-07-21	Zutano	\N	\N	f	\N	453.18	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
539	1	1	\N	A-014-019	14	19	2013.42	excelente	vivero	\N	\N	0101000020E6100000C661F0DFF73F53C060A67E14D2FF27C0	2352.61	2025-06-29	2025-12-04	Fuerte	\N	\N	f	\N	85.32	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
540	1	1	\N	A-014-020	14	20	1498.37	con_estres	establecimiento	\N	\N	0101000020E6100000E24A616CF73F53C060A67E14D2FF27C0	896.08	2023-05-26	\N	Fuerte	\N	\N	f	\N	56.50	7	Omnis deleniti est velit.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
541	1	1	\N	A-014-021	14	21	940.04	excelente	vivero	\N	\N	0101000020E6100000B733D2F8F63F53C060A67E14D2FF27C0	1432.49	2025-03-09	\N	Zutano	\N	\N	f	\N	19.18	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
542	1	1	\N	A-014-022	14	22	1923.98	enfermo_critico	vivero	\N	\N	0101000020E6100000D31C4385F63F53C060A67E14D2FF27C0	1671.26	2023-03-24	\N	Bacon	\N	\N	f	\N	31.05	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
543	1	1	\N	A-014-023	14	23	812.52	con_estres	produccion_madura	\N	\N	0101000020E6100000EF05B411F63F53C060A67E14D2FF27C0	1811.64	2023-07-24	2024-11-25	Fuerte	\N	\N	f	\N	380.76	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
544	1	1	\N	A-014-024	14	24	1366.61	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000C5EE249EF53F53C060A67E14D2FF27C0	1453.13	2021-11-19	2026-05-21	Ettinger	\N	\N	f	\N	346.42	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
545	1	1	\N	A-014-025	14	25	1728.05	con_estres	vivero	\N	\N	0101000020E6100000E1D7952AF53F53C060A67E14D2FF27C0	\N	2021-11-05	2024-03-05	Zutano	\N	\N	f	\N	78.01	7	Commodi voluptas quam quo modi ut et corporis.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
546	1	1	\N	A-014-026	14	26	2414.98	excelente	vivero	\N	\N	0101000020E6100000B7C006B7F43F53C060A67E14D2FF27C0	1037.96	2026-04-03	2026-06-21	Hass	\N	\N	t	\N	414.19	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
547	1	1	\N	A-014-027	14	27	856.47	muerto	establecimiento	2025-11-05	\N	0101000020E6100000D3A97743F43F53C060A67E14D2FF27C0	883.28	2022-01-20	2025-11-27	Bacon	\N	\N	f	\N	52.24	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
548	1	1	\N	A-014-028	14	28	2321.99	muerto	establecimiento	2026-06-08	\N	0101000020E6100000A892E8CFF33F53C060A67E14D2FF27C0	2405.45	2026-04-10	\N	Fuerte	\N	\N	f	\N	205.92	7	Dolore nobis accusantium magnam aut consectetur.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
549	1	1	\N	A-014-029	14	29	1988.34	enfermo_critico	senescencia	\N	\N	0101000020E6100000C47B595CF33F53C060A67E14D2FF27C0	1447.48	2024-03-02	2025-05-15	Hass	\N	\N	f	\N	288.51	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
550	1	1	\N	A-014-030	14	30	949.70	enfermo_critico	vivero	\N	\N	0101000020E6100000E164CAE8F23F53C060A67E14D2FF27C0	2150.49	2022-10-31	\N	Zutano	\N	\N	f	\N	265.04	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
551	1	1	\N	A-014-031	14	31	1495.04	erradicado	establecimiento	2025-09-24	\N	0101000020E6100000B64D3B75F23F53C060A67E14D2FF27C0	1846.84	2023-06-06	2025-03-03	Zutano	\N	\N	f	\N	289.66	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
552	1	1	\N	A-014-032	14	32	2284.85	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000D236AC01F23F53C060A67E14D2FF27C0	2026.93	2021-12-23	2025-06-26	Hass	\N	\N	f	\N	327.56	4	Autem non et fugit pariatur.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
553	1	1	\N	A-014-033	14	33	805.67	con_estres	vivero	\N	\N	0101000020E6100000A81F1D8EF13F53C060A67E14D2FF27C0	2478.21	2024-12-28	2025-08-10	Hass	\N	\N	f	\N	390.22	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
554	1	1	\N	A-014-034	14	34	1590.72	erradicado	senescencia	2026-02-02	\N	0101000020E6100000C4088E1AF13F53C060A67E14D2FF27C0	1563.74	2024-03-26	2024-06-23	Hass	\N	\N	f	\N	499.97	6	Et sequi possimus optio aliquid vitae ad quod.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
555	1	1	\N	A-014-035	14	35	2282.71	erradicado	establecimiento	2026-06-03	\N	0101000020E61000009AF1FEA6F03F53C060A67E14D2FF27C0	\N	2023-07-06	\N	Zutano	\N	\N	f	\N	417.52	3	Illum dolores ipsum quo doloremque tenetur praesentium quia.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
556	1	1	\N	A-014-036	14	36	1065.03	muerto	establecimiento	2026-01-09	\N	0101000020E6100000B6DA6F33F03F53C060A67E14D2FF27C0	2015.83	2026-02-03	2026-03-06	Fuerte	\N	\N	f	\N	22.51	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
557	1	1	\N	A-014-037	14	37	800.97	excelente	produccion_madura	\N	\N	0101000020E6100000D2C3E0BFEF3F53C060A67E14D2FF27C0	\N	2021-10-24	2023-10-03	Fuerte	\N	\N	t	\N	485.72	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
558	1	1	\N	A-014-038	14	38	1917.02	con_estres	vivero	\N	\N	0101000020E6100000A7AC514CEF3F53C060A67E14D2FF27C0	1850.81	2021-11-08	2023-03-25	Fuerte	\N	\N	f	\N	235.51	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
559	1	1	\N	A-014-039	14	39	1777.76	erradicado	vivero	2026-01-15	\N	0101000020E6100000C395C2D8EE3F53C060A67E14D2FF27C0	2378.40	2023-05-19	2026-06-09	Zutano	\N	\N	f	\N	340.16	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
560	1	1	\N	A-014-040	14	40	1157.06	con_estres	produccion_madura	\N	\N	0101000020E6100000997E3365EE3F53C060A67E14D2FF27C0	\N	2025-09-20	2026-05-29	Ettinger	\N	\N	f	\N	16.20	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
561	1	1	\N	A-015-001	15	1	2071.67	muerto	senescencia	2025-10-28	\N	0101000020E610000000000000004053C0FD9F398CCEFF27C0	\N	2023-09-27	2025-07-23	Fuerte	\N	\N	f	\N	18.61	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
562	1	1	\N	A-015-002	15	2	2077.24	erradicado	desarrollo_inmaduro	2026-03-25	\N	0101000020E61000001CE9708CFF3F53C0FD9F398CCEFF27C0	1887.19	2021-12-21	2022-08-05	Bacon	\N	\N	f	\N	94.86	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
563	1	1	\N	A-015-003	15	3	2025.47	con_estres	vivero	\N	\N	0101000020E6100000F2D1E118FF3F53C0FD9F398CCEFF27C0	1604.58	2022-04-23	2023-08-05	Fuerte	\N	\N	f	\N	128.43	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
564	1	1	\N	A-015-004	15	4	2487.13	erradicado	desarrollo_inmaduro	2025-11-01	\N	0101000020E61000000EBB52A5FE3F53C0FD9F398CCEFF27C0	1035.23	2023-10-11	2026-04-01	Zutano	\N	\N	t	\N	145.50	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
565	1	1	\N	A-015-005	15	5	1053.78	muerto	establecimiento	2026-07-08	\N	0101000020E6100000E3A3C331FE3F53C0FD9F398CCEFF27C0	\N	2024-11-21	2025-11-13	Bacon	\N	\N	f	\N	419.96	10	Est quidem beatae recusandae corporis distinctio recusandae harum.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
566	1	1	\N	A-015-006	15	6	1974.97	excelente	establecimiento	\N	\N	0101000020E6100000FF8C34BEFD3F53C0FD9F398CCEFF27C0	\N	2023-03-04	\N	Bacon	\N	\N	f	\N	79.45	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
567	1	1	\N	A-015-007	15	7	2394.41	muerto	vivero	2025-12-22	\N	0101000020E6100000D575A54AFD3F53C0FD9F398CCEFF27C0	2001.07	2025-02-09	\N	Fuerte	\N	\N	f	\N	421.62	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
568	1	1	\N	A-015-008	15	8	1321.18	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000F15E16D7FC3F53C0FD9F398CCEFF27C0	\N	2023-03-20	\N	Ettinger	\N	\N	f	\N	358.54	0	Qui corrupti qui aut sint modi.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
569	1	1	\N	A-015-009	15	9	1241.93	con_estres	produccion_madura	\N	\N	0101000020E61000000D488763FC3F53C0FD9F398CCEFF27C0	1981.42	2025-10-04	\N	Zutano	\N	\N	f	\N	8.90	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
570	1	1	\N	A-015-010	15	10	2379.12	erradicado	produccion_madura	2026-07-15	\N	0101000020E6100000E330F8EFFB3F53C0FD9F398CCEFF27C0	1795.81	2026-02-27	2026-06-14	Ettinger	\N	\N	f	\N	427.88	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
571	1	1	\N	A-015-011	15	11	1559.07	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000FF19697CFB3F53C0FD9F398CCEFF27C0	1241.87	2026-02-05	\N	Fuerte	\N	\N	f	\N	364.00	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
572	1	1	\N	A-015-012	15	12	1399.90	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000D502DA08FB3F53C0FD9F398CCEFF27C0	\N	2021-12-29	\N	Zutano	\N	\N	f	\N	103.26	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
573	1	1	\N	A-015-013	15	13	1531.05	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000F1EB4A95FA3F53C0FD9F398CCEFF27C0	1458.37	2022-08-30	2023-06-08	Bacon	\N	\N	f	\N	399.44	8	Et rerum aperiam nihil ducimus quidem quia et.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
574	1	1	\N	A-015-014	15	14	1437.17	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000C6D4BB21FA3F53C0FD9F398CCEFF27C0	1098.28	2025-01-28	2025-10-19	Zutano	\N	\N	f	\N	142.98	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
575	1	1	\N	A-015-015	15	15	1302.28	con_estres	senescencia	\N	\N	0101000020E6100000E2BD2CAEF93F53C0FD9F398CCEFF27C0	\N	2025-12-20	2026-01-26	Zutano	\N	\N	f	\N	487.68	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
576	1	1	\N	A-015-016	15	16	1191.89	excelente	produccion_madura	\N	\N	0101000020E6100000FEA69D3AF93F53C0FD9F398CCEFF27C0	1047.62	2021-12-03	\N	Zutano	\N	\N	f	\N	40.48	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
577	1	1	\N	A-015-017	15	17	2088.24	con_estres	vivero	\N	\N	0101000020E6100000D48F0EC7F83F53C0FD9F398CCEFF27C0	1025.61	2026-06-14	\N	Hass	\N	\N	f	\N	386.36	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
578	1	1	\N	A-015-018	15	18	1727.45	erradicado	produccion_madura	2026-05-14	\N	0101000020E6100000F0787F53F83F53C0FD9F398CCEFF27C0	2423.49	2023-05-31	2025-11-09	Hass	\N	\N	f	\N	248.37	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
579	1	1	\N	A-015-019	15	19	945.05	erradicado	senescencia	2026-07-29	\N	0101000020E6100000C661F0DFF73F53C0FD9F398CCEFF27C0	1857.61	2025-09-11	\N	Ettinger	\N	\N	f	\N	425.75	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
580	1	1	\N	A-015-020	15	20	2002.93	enfermo_critico	senescencia	\N	\N	0101000020E6100000E24A616CF73F53C0FD9F398CCEFF27C0	906.46	2023-07-02	2024-06-17	Fuerte	\N	\N	f	\N	397.02	1	Natus ullam vero est eum officia cumque doloremque a.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
581	1	1	\N	A-015-021	15	21	871.02	erradicado	vivero	2026-05-05	\N	0101000020E6100000B733D2F8F63F53C0FD9F398CCEFF27C0	1619.50	2022-02-04	2023-11-11	Zutano	\N	\N	f	\N	155.13	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
582	1	1	\N	A-015-022	15	22	1090.28	enfermo_critico	senescencia	\N	\N	0101000020E6100000D31C4385F63F53C0FD9F398CCEFF27C0	1506.48	2021-11-18	\N	Bacon	\N	\N	f	\N	40.97	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
583	1	1	\N	A-015-023	15	23	1625.09	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000EF05B411F63F53C0FD9F398CCEFF27C0	844.72	2024-09-06	2025-04-25	Ettinger	\N	\N	f	\N	282.81	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
584	1	1	\N	A-015-024	15	24	2148.65	con_estres	produccion_madura	\N	\N	0101000020E6100000C5EE249EF53F53C0FD9F398CCEFF27C0	1034.33	2023-01-04	\N	Fuerte	\N	\N	f	\N	154.84	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
585	1	1	\N	A-015-025	15	25	1041.81	muerto	desarrollo_inmaduro	2025-09-18	\N	0101000020E6100000E1D7952AF53F53C0FD9F398CCEFF27C0	973.71	2022-08-22	2023-06-26	Fuerte	\N	\N	f	\N	96.94	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
586	1	1	\N	A-015-026	15	26	2373.05	con_estres	senescencia	\N	\N	0101000020E6100000B7C006B7F43F53C0FD9F398CCEFF27C0	\N	2023-04-08	2025-07-11	Zutano	\N	\N	f	\N	94.75	10	Nesciunt magnam veniam nostrum numquam dignissimos eaque.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
587	1	1	\N	A-015-027	15	27	809.10	enfermo_critico	vivero	\N	\N	0101000020E6100000D3A97743F43F53C0FD9F398CCEFF27C0	2000.76	2022-05-07	2026-01-25	Zutano	\N	\N	f	\N	331.29	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
588	1	1	\N	A-015-028	15	28	2497.82	excelente	produccion_madura	\N	\N	0101000020E6100000A892E8CFF33F53C0FD9F398CCEFF27C0	2107.06	2025-11-23	\N	Fuerte	\N	\N	f	\N	353.58	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
589	1	1	\N	A-015-029	15	29	2094.89	erradicado	desarrollo_inmaduro	2026-08-08	\N	0101000020E6100000C47B595CF33F53C0FD9F398CCEFF27C0	827.20	2024-11-03	\N	Fuerte	\N	\N	f	\N	417.59	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
590	1	1	\N	A-015-030	15	30	2095.68	enfermo_critico	vivero	\N	\N	0101000020E6100000E164CAE8F23F53C0FD9F398CCEFF27C0	2304.85	2023-01-25	2025-04-04	Ettinger	\N	\N	f	\N	323.66	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
591	1	1	\N	A-015-031	15	31	1605.74	con_estres	produccion_madura	\N	\N	0101000020E6100000B64D3B75F23F53C0FD9F398CCEFF27C0	1973.03	2022-01-20	2024-03-14	Ettinger	\N	\N	f	\N	175.43	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
592	1	1	\N	A-015-032	15	32	997.51	con_estres	vivero	\N	\N	0101000020E6100000D236AC01F23F53C0FD9F398CCEFF27C0	1010.84	2023-06-12	2026-07-13	Bacon	\N	\N	f	\N	294.57	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
593	1	1	\N	A-015-033	15	33	1228.88	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000A81F1D8EF13F53C0FD9F398CCEFF27C0	2187.12	2025-03-03	2026-07-27	Bacon	\N	\N	f	\N	129.30	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
594	1	1	\N	A-015-034	15	34	2360.26	con_estres	vivero	\N	\N	0101000020E6100000C4088E1AF13F53C0FD9F398CCEFF27C0	1988.76	2022-05-18	2024-03-21	Fuerte	\N	\N	f	\N	206.18	2	Nam delectus voluptatibus laborum laborum minus.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
595	1	1	\N	A-015-035	15	35	1929.92	muerto	desarrollo_inmaduro	2025-12-26	\N	0101000020E61000009AF1FEA6F03F53C0FD9F398CCEFF27C0	1034.55	2024-06-11	\N	Hass	\N	\N	f	\N	67.16	7	Consequatur voluptatem et molestias sed rerum perspiciatis minus.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
596	1	1	\N	A-015-036	15	36	2407.14	enfermo_critico	vivero	\N	\N	0101000020E6100000B6DA6F33F03F53C0FD9F398CCEFF27C0	2288.07	2024-11-09	2026-04-19	Hass	\N	\N	f	\N	482.94	7	Nihil ullam tempore velit aliquid molestiae sunt perferendis et.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
597	1	1	\N	A-015-037	15	37	1482.78	con_estres	vivero	\N	\N	0101000020E6100000D2C3E0BFEF3F53C0FD9F398CCEFF27C0	\N	2026-02-09	2026-04-20	Bacon	\N	\N	f	\N	158.54	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
598	1	1	\N	A-015-038	15	38	2068.11	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000A7AC514CEF3F53C0FD9F398CCEFF27C0	2469.83	2026-04-14	2026-05-22	Fuerte	\N	\N	t	\N	71.98	4	Quos nam molestias dolor quo beatae est recusandae ut.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
599	1	1	\N	A-015-039	15	39	1941.10	muerto	produccion_madura	2025-11-07	\N	0101000020E6100000C395C2D8EE3F53C0FD9F398CCEFF27C0	2362.10	2022-02-20	\N	Fuerte	\N	\N	f	\N	463.15	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
600	1	1	\N	A-015-040	15	40	1833.29	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000997E3365EE3F53C0FD9F398CCEFF27C0	1730.47	2026-06-18	\N	Bacon	\N	\N	f	\N	77.71	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
601	1	1	\N	A-016-001	16	1	2052.45	excelente	establecimiento	\N	\N	0101000020E610000000000000004053C06797F403CBFF27C0	\N	2024-05-20	2025-09-08	Zutano	\N	\N	f	\N	66.95	5	Architecto sed enim asperiores enim.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
602	1	1	\N	A-016-002	16	2	1885.83	erradicado	desarrollo_inmaduro	2026-04-06	\N	0101000020E61000001CE9708CFF3F53C06797F403CBFF27C0	2218.75	2023-11-25	2026-03-26	Zutano	\N	\N	f	\N	260.75	9	Nam eos enim quia culpa eligendi.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
603	1	1	\N	A-016-003	16	3	944.76	erradicado	vivero	2026-03-25	\N	0101000020E6100000F2D1E118FF3F53C06797F403CBFF27C0	1503.80	2025-11-26	2026-02-12	Zutano	\N	\N	f	\N	414.44	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
604	1	1	\N	A-016-004	16	4	838.10	enfermo_critico	produccion_madura	\N	\N	0101000020E61000000EBB52A5FE3F53C06797F403CBFF27C0	2336.19	2023-09-15	\N	Zutano	\N	\N	f	\N	279.34	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
605	1	1	\N	A-016-005	16	5	2232.72	excelente	senescencia	\N	\N	0101000020E6100000E3A3C331FE3F53C06797F403CBFF27C0	1124.77	2026-01-25	2026-04-19	Fuerte	\N	\N	f	\N	212.54	9	Reprehenderit accusantium et asperiores iusto nemo suscipit rem.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
606	1	1	\N	A-016-006	16	6	1123.71	erradicado	senescencia	2026-05-26	\N	0101000020E6100000FF8C34BEFD3F53C06797F403CBFF27C0	\N	2023-12-27	2024-05-27	Ettinger	\N	\N	f	\N	36.77	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
607	1	1	\N	A-016-007	16	7	1362.91	muerto	desarrollo_inmaduro	2026-03-12	\N	0101000020E6100000D575A54AFD3F53C06797F403CBFF27C0	\N	2023-10-11	\N	Hass	\N	\N	f	\N	219.21	1	Iure minima modi explicabo minima.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
608	1	1	\N	A-016-008	16	8	2080.76	enfermo_critico	establecimiento	\N	\N	0101000020E6100000F15E16D7FC3F53C06797F403CBFF27C0	1158.88	2026-07-17	\N	Zutano	\N	\N	f	\N	485.25	7	Omnis eligendi sunt et debitis ea explicabo.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
609	1	1	\N	A-016-009	16	9	1296.61	muerto	senescencia	2026-07-27	\N	0101000020E61000000D488763FC3F53C06797F403CBFF27C0	2085.62	2022-07-02	2025-07-22	Zutano	\N	\N	f	\N	215.90	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
610	1	1	\N	A-016-010	16	10	982.10	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000E330F8EFFB3F53C06797F403CBFF27C0	\N	2021-08-27	2022-10-31	Zutano	\N	\N	f	\N	457.56	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
611	1	1	\N	A-016-011	16	11	1152.15	erradicado	desarrollo_inmaduro	2026-06-17	\N	0101000020E6100000FF19697CFB3F53C06797F403CBFF27C0	1237.32	2024-12-31	2026-01-30	Zutano	\N	\N	f	\N	256.75	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
612	1	1	\N	A-016-012	16	12	1839.20	excelente	senescencia	\N	\N	0101000020E6100000D502DA08FB3F53C06797F403CBFF27C0	1548.32	2022-08-27	2024-11-23	Zutano	\N	\N	f	\N	382.24	7	Nulla asperiores quia non autem ut aspernatur dolor.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
613	1	1	\N	A-016-013	16	13	1893.58	con_estres	produccion_madura	\N	\N	0101000020E6100000F1EB4A95FA3F53C06797F403CBFF27C0	1528.48	2024-02-14	\N	Ettinger	\N	\N	f	\N	411.23	3	Voluptatem accusantium est qui blanditiis nihil.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
614	1	1	\N	A-016-014	16	14	1191.38	con_estres	establecimiento	\N	\N	0101000020E6100000C6D4BB21FA3F53C06797F403CBFF27C0	\N	2022-04-02	\N	Bacon	\N	\N	f	\N	499.22	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
615	1	1	\N	A-016-015	16	15	1231.22	muerto	desarrollo_inmaduro	2026-08-13	\N	0101000020E6100000E2BD2CAEF93F53C06797F403CBFF27C0	1150.24	2021-11-07	2026-06-16	Zutano	\N	\N	f	\N	336.11	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
616	1	1	\N	A-016-016	16	16	1519.47	con_estres	establecimiento	\N	\N	0101000020E6100000FEA69D3AF93F53C06797F403CBFF27C0	1995.27	2022-01-30	2025-01-09	Bacon	\N	\N	f	\N	308.49	6	Ut blanditiis saepe similique expedita eum et.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
617	1	1	\N	A-016-017	16	17	1215.16	erradicado	produccion_madura	2026-07-22	\N	0101000020E6100000D48F0EC7F83F53C06797F403CBFF27C0	808.42	2024-06-10	2026-06-27	Bacon	\N	\N	f	\N	360.40	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
618	1	1	\N	A-016-018	16	18	2391.06	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000F0787F53F83F53C06797F403CBFF27C0	1158.14	2022-11-30	\N	Zutano	\N	\N	f	\N	49.54	1	Qui dolore modi cupiditate molestiae id velit aliquid.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
619	1	1	\N	A-016-019	16	19	1675.87	muerto	senescencia	2026-04-11	\N	0101000020E6100000C661F0DFF73F53C06797F403CBFF27C0	\N	2024-08-24	2025-02-11	Zutano	\N	\N	f	\N	363.29	1	Consequatur minus molestiae similique.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
620	1	1	\N	A-016-020	16	20	1932.65	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000E24A616CF73F53C06797F403CBFF27C0	2126.75	2023-03-05	\N	Hass	\N	\N	f	\N	11.39	1	Quae aliquam reiciendis eaque alias et ut.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
621	1	1	\N	A-016-021	16	21	2228.11	enfermo_critico	vivero	\N	\N	0101000020E6100000B733D2F8F63F53C06797F403CBFF27C0	\N	2025-02-17	\N	Hass	\N	\N	f	\N	25.06	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
622	1	1	\N	A-016-022	16	22	970.49	erradicado	senescencia	2026-06-17	\N	0101000020E6100000D31C4385F63F53C06797F403CBFF27C0	1153.75	2025-09-11	2026-08-06	Hass	\N	\N	f	\N	222.11	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
623	1	1	\N	A-016-023	16	23	2135.68	muerto	establecimiento	2025-12-03	\N	0101000020E6100000EF05B411F63F53C06797F403CBFF27C0	883.16	2022-11-27	2026-06-30	Hass	\N	\N	f	\N	401.99	5	Possimus libero enim repellat.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
624	1	1	\N	A-016-024	16	24	1214.58	erradicado	desarrollo_inmaduro	2025-11-10	\N	0101000020E6100000C5EE249EF53F53C06797F403CBFF27C0	1456.49	2023-11-22	\N	Fuerte	\N	\N	f	\N	256.89	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
625	1	1	\N	A-016-025	16	25	824.15	con_estres	establecimiento	\N	\N	0101000020E6100000E1D7952AF53F53C06797F403CBFF27C0	2053.76	2023-03-07	2024-06-17	Bacon	\N	\N	f	\N	338.77	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
626	1	1	\N	A-016-026	16	26	1987.18	enfermo_critico	vivero	\N	\N	0101000020E6100000B7C006B7F43F53C06797F403CBFF27C0	\N	2023-04-19	2025-09-17	Ettinger	\N	\N	f	\N	302.26	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
627	1	1	\N	A-016-027	16	27	878.91	excelente	produccion_madura	\N	\N	0101000020E6100000D3A97743F43F53C06797F403CBFF27C0	1792.72	2026-02-05	\N	Hass	\N	\N	f	\N	126.36	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
628	1	1	\N	A-016-028	16	28	2373.45	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000A892E8CFF33F53C06797F403CBFF27C0	2191.69	2025-05-19	2026-01-30	Hass	\N	\N	f	\N	434.85	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
629	1	1	\N	A-016-029	16	29	1349.29	erradicado	vivero	2026-04-17	\N	0101000020E6100000C47B595CF33F53C06797F403CBFF27C0	1007.90	2026-05-31	2026-08-09	Bacon	\N	\N	f	\N	118.57	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
630	1	1	\N	A-016-030	16	30	1964.47	erradicado	establecimiento	2025-09-11	\N	0101000020E6100000E164CAE8F23F53C06797F403CBFF27C0	2461.79	2023-08-22	2024-06-10	Fuerte	\N	\N	f	\N	219.92	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
631	1	1	\N	A-016-031	16	31	1797.40	muerto	vivero	2026-05-16	\N	0101000020E6100000B64D3B75F23F53C06797F403CBFF27C0	1119.44	2022-02-08	\N	Ettinger	\N	\N	f	\N	16.23	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
632	1	1	\N	A-016-032	16	32	2316.97	erradicado	establecimiento	2026-05-22	\N	0101000020E6100000D236AC01F23F53C06797F403CBFF27C0	2107.80	2025-07-03	2026-08-06	Bacon	\N	\N	f	\N	114.69	8	Et odio deleniti corporis accusantium esse perferendis aliquid.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
633	1	1	\N	A-016-033	16	33	1225.90	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000A81F1D8EF13F53C06797F403CBFF27C0	2285.47	2025-06-12	2026-02-22	Zutano	\N	\N	f	\N	360.82	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
634	1	1	\N	A-016-034	16	34	1778.14	muerto	vivero	2026-01-01	\N	0101000020E6100000C4088E1AF13F53C06797F403CBFF27C0	\N	2022-05-27	\N	Fuerte	\N	\N	f	\N	323.89	1	Blanditiis inventore est doloribus impedit sunt a et velit.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
635	1	1	\N	A-016-035	16	35	2043.40	con_estres	senescencia	\N	\N	0101000020E61000009AF1FEA6F03F53C06797F403CBFF27C0	824.58	2024-05-19	2025-04-13	Zutano	\N	\N	f	\N	445.91	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
636	1	1	\N	A-016-036	16	36	2210.51	excelente	establecimiento	\N	\N	0101000020E6100000B6DA6F33F03F53C06797F403CBFF27C0	1415.39	2024-05-04	\N	Fuerte	\N	\N	f	\N	429.47	9	Dolorem voluptatum numquam velit est officiis perspiciatis.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
637	1	1	\N	A-016-037	16	37	1107.22	excelente	senescencia	\N	\N	0101000020E6100000D2C3E0BFEF3F53C06797F403CBFF27C0	1016.06	2024-09-27	2026-08-04	Bacon	\N	\N	f	\N	200.03	3	Necessitatibus non quisquam impedit libero sequi et.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
638	1	1	\N	A-016-038	16	38	2433.83	muerto	vivero	2026-04-22	\N	0101000020E6100000A7AC514CEF3F53C06797F403CBFF27C0	883.10	2023-12-10	\N	Hass	\N	\N	t	\N	127.99	0	Ad et hic rerum mollitia ab architecto ducimus.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
639	1	1	\N	A-016-039	16	39	886.41	excelente	produccion_madura	\N	\N	0101000020E6100000C395C2D8EE3F53C06797F403CBFF27C0	1422.37	2023-05-22	2024-11-09	Fuerte	\N	\N	f	\N	94.06	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
640	1	1	\N	A-016-040	16	40	1809.19	erradicado	establecimiento	2025-12-06	\N	0101000020E6100000997E3365EE3F53C06797F403CBFF27C0	2136.57	2022-05-26	\N	Zutano	\N	\N	f	\N	291.97	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
641	1	1	\N	A-017-001	17	1	2206.68	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E610000000000000004053C00491AF7BC7FF27C0	1312.93	2022-03-14	2023-03-28	Fuerte	\N	\N	f	\N	396.44	9	Debitis qui voluptatibus nisi suscipit sint.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
642	1	1	\N	A-017-002	17	2	2470.47	excelente	desarrollo_inmaduro	\N	\N	0101000020E61000001CE9708CFF3F53C00491AF7BC7FF27C0	\N	2025-12-15	2026-08-19	Hass	\N	\N	f	\N	366.22	4	Rerum sunt culpa ut.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
643	1	1	\N	A-017-003	17	3	1131.97	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000F2D1E118FF3F53C00491AF7BC7FF27C0	2266.18	2024-02-26	\N	Ettinger	\N	\N	f	\N	298.08	8	Totam porro dolorem voluptas fugit rerum voluptatum.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
644	1	1	\N	A-017-004	17	4	1150.09	muerto	produccion_madura	2026-06-02	\N	0101000020E61000000EBB52A5FE3F53C00491AF7BC7FF27C0	920.69	2024-05-31	2025-03-17	Bacon	\N	\N	f	\N	317.18	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
645	1	1	\N	A-017-005	17	5	1489.62	erradicado	desarrollo_inmaduro	2026-04-26	\N	0101000020E6100000E3A3C331FE3F53C00491AF7BC7FF27C0	1916.45	2024-07-25	2026-06-19	Hass	\N	\N	f	\N	302.41	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
646	1	1	\N	A-017-006	17	6	2134.67	excelente	establecimiento	\N	\N	0101000020E6100000FF8C34BEFD3F53C00491AF7BC7FF27C0	1990.47	2024-12-06	\N	Fuerte	\N	\N	f	\N	149.70	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
647	1	1	\N	A-017-007	17	7	2155.52	excelente	establecimiento	\N	\N	0101000020E6100000D575A54AFD3F53C00491AF7BC7FF27C0	812.18	2022-01-26	\N	Fuerte	\N	\N	f	\N	320.24	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
648	1	1	\N	A-017-008	17	8	2265.95	erradicado	produccion_madura	2026-08-21	\N	0101000020E6100000F15E16D7FC3F53C00491AF7BC7FF27C0	2224.71	2022-09-01	2023-05-31	Ettinger	\N	\N	f	\N	90.05	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
649	1	1	\N	A-017-009	17	9	2438.51	muerto	produccion_madura	2025-12-19	\N	0101000020E61000000D488763FC3F53C00491AF7BC7FF27C0	\N	2023-08-17	\N	Bacon	\N	\N	f	\N	195.03	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
650	1	1	\N	A-017-010	17	10	885.10	muerto	establecimiento	2026-07-20	\N	0101000020E6100000E330F8EFFB3F53C00491AF7BC7FF27C0	\N	2021-08-28	\N	Fuerte	\N	\N	f	\N	396.16	5	Consequatur veniam eveniet dicta quia deserunt.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
651	1	1	\N	A-017-011	17	11	2451.24	con_estres	senescencia	\N	\N	0101000020E6100000FF19697CFB3F53C00491AF7BC7FF27C0	2261.96	2026-05-15	\N	Zutano	\N	\N	f	\N	208.83	10	Autem dolorem odio in iure doloremque architecto.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
652	1	1	\N	A-017-012	17	12	995.05	muerto	desarrollo_inmaduro	2026-03-10	\N	0101000020E6100000D502DA08FB3F53C00491AF7BC7FF27C0	1775.00	2025-03-30	2026-01-25	Hass	\N	\N	f	\N	259.85	0	Itaque cumque quibusdam tempore quibusdam.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
653	1	1	\N	A-017-013	17	13	1540.94	excelente	produccion_madura	\N	\N	0101000020E6100000F1EB4A95FA3F53C00491AF7BC7FF27C0	1358.37	2022-01-27	2025-12-06	Hass	\N	\N	f	\N	263.74	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
654	1	1	\N	A-017-014	17	14	948.55	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000C6D4BB21FA3F53C00491AF7BC7FF27C0	\N	2025-03-09	\N	Zutano	\N	\N	f	\N	281.25	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
655	1	1	\N	A-017-015	17	15	1329.54	muerto	senescencia	2026-05-01	\N	0101000020E6100000E2BD2CAEF93F53C00491AF7BC7FF27C0	\N	2024-07-30	2025-08-11	Ettinger	\N	\N	f	\N	447.75	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
656	1	1	\N	A-017-016	17	16	1547.33	muerto	establecimiento	2025-11-24	\N	0101000020E6100000FEA69D3AF93F53C00491AF7BC7FF27C0	1511.66	2021-12-18	2022-03-01	Hass	\N	\N	f	\N	492.23	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
657	1	1	\N	A-017-017	17	17	1892.93	erradicado	produccion_madura	2026-08-04	\N	0101000020E6100000D48F0EC7F83F53C00491AF7BC7FF27C0	2194.26	2024-05-23	2025-10-03	Zutano	\N	\N	f	\N	470.70	6	Cupiditate omnis sed sapiente omnis.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
658	1	1	\N	A-017-018	17	18	985.38	erradicado	desarrollo_inmaduro	2026-03-23	\N	0101000020E6100000F0787F53F83F53C00491AF7BC7FF27C0	1509.64	2022-04-04	2022-11-02	Bacon	\N	\N	f	\N	326.22	2	Et qui totam iusto iure aperiam aut laboriosam eos.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
659	1	1	\N	A-017-019	17	19	2461.77	con_estres	senescencia	\N	\N	0101000020E6100000C661F0DFF73F53C00491AF7BC7FF27C0	1475.28	2021-12-19	\N	Hass	\N	\N	f	\N	361.55	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
660	1	1	\N	A-017-020	17	20	2170.08	enfermo_critico	establecimiento	\N	\N	0101000020E6100000E24A616CF73F53C00491AF7BC7FF27C0	1453.00	2022-03-20	2025-03-11	Hass	\N	\N	f	\N	451.55	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
661	1	1	\N	A-017-021	17	21	2314.46	enfermo_critico	vivero	\N	\N	0101000020E6100000B733D2F8F63F53C00491AF7BC7FF27C0	2409.25	2024-08-20	2026-06-02	Fuerte	\N	\N	f	\N	394.52	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
662	1	1	\N	A-017-022	17	22	2205.04	muerto	produccion_madura	2026-06-02	\N	0101000020E6100000D31C4385F63F53C00491AF7BC7FF27C0	\N	2024-07-22	2025-01-26	Bacon	\N	\N	f	\N	279.06	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
663	1	1	\N	A-017-023	17	23	1049.33	muerto	produccion_madura	2025-12-30	\N	0101000020E6100000EF05B411F63F53C00491AF7BC7FF27C0	1603.98	2022-07-10	2023-04-26	Bacon	\N	\N	f	\N	363.86	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
664	1	1	\N	A-017-024	17	24	2368.76	excelente	establecimiento	\N	\N	0101000020E6100000C5EE249EF53F53C00491AF7BC7FF27C0	1051.81	2023-12-02	2026-07-12	Fuerte	\N	\N	f	\N	358.33	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
665	1	1	\N	A-017-025	17	25	900.80	enfermo_critico	vivero	\N	\N	0101000020E6100000E1D7952AF53F53C00491AF7BC7FF27C0	\N	2022-12-12	\N	Fuerte	\N	\N	f	\N	260.61	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
666	1	1	\N	A-017-026	17	26	2485.65	muerto	establecimiento	2026-03-25	\N	0101000020E6100000B7C006B7F43F53C00491AF7BC7FF27C0	988.48	2026-07-09	\N	Ettinger	\N	\N	f	\N	327.64	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
667	1	1	\N	A-017-027	17	27	1113.48	excelente	produccion_madura	\N	\N	0101000020E6100000D3A97743F43F53C00491AF7BC7FF27C0	1752.95	2022-07-20	2022-08-30	Hass	\N	\N	f	\N	235.32	10	Corporis voluptate porro deleniti enim qui.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
668	1	1	\N	A-017-028	17	28	1972.41	excelente	produccion_madura	\N	\N	0101000020E6100000A892E8CFF33F53C00491AF7BC7FF27C0	1198.38	2025-03-17	2025-07-31	Hass	\N	\N	f	\N	383.41	4	Qui velit illo laboriosam facere repellat.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
669	1	1	\N	A-017-029	17	29	1197.53	erradicado	senescencia	2025-09-10	\N	0101000020E6100000C47B595CF33F53C00491AF7BC7FF27C0	\N	2022-03-23	2026-02-17	Fuerte	\N	\N	f	\N	435.88	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
670	1	1	\N	A-017-030	17	30	1363.37	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000E164CAE8F23F53C00491AF7BC7FF27C0	1378.13	2026-03-11	2026-05-22	Bacon	\N	\N	f	\N	163.46	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
671	1	1	\N	A-017-031	17	31	1564.24	muerto	senescencia	2026-01-10	\N	0101000020E6100000B64D3B75F23F53C00491AF7BC7FF27C0	1674.42	2021-09-09	2026-05-20	Ettinger	\N	\N	f	\N	227.44	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
672	1	1	\N	A-017-032	17	32	1243.17	muerto	senescencia	2026-07-14	\N	0101000020E6100000D236AC01F23F53C00491AF7BC7FF27C0	\N	2022-04-20	\N	Hass	\N	\N	f	\N	67.95	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
673	1	1	\N	A-017-033	17	33	976.08	erradicado	produccion_madura	2025-10-17	\N	0101000020E6100000A81F1D8EF13F53C00491AF7BC7FF27C0	937.08	2023-01-26	2025-03-24	Fuerte	\N	\N	f	\N	18.53	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
674	1	1	\N	A-017-034	17	34	2443.63	excelente	establecimiento	\N	\N	0101000020E6100000C4088E1AF13F53C00491AF7BC7FF27C0	1933.84	2022-12-09	2024-03-10	Zutano	\N	\N	f	\N	420.73	5	Occaecati officiis natus est quidem.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
675	1	1	\N	A-017-035	17	35	1059.47	con_estres	desarrollo_inmaduro	\N	\N	0101000020E61000009AF1FEA6F03F53C00491AF7BC7FF27C0	2023.11	2025-12-23	2026-03-05	Hass	\N	\N	f	\N	255.54	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
676	1	1	\N	A-017-036	17	36	1030.66	muerto	establecimiento	2026-02-06	\N	0101000020E6100000B6DA6F33F03F53C00491AF7BC7FF27C0	1956.92	2025-11-11	2026-04-04	Hass	\N	\N	f	\N	304.65	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
677	1	1	\N	A-017-037	17	37	1505.91	excelente	senescencia	\N	\N	0101000020E6100000D2C3E0BFEF3F53C00491AF7BC7FF27C0	1315.29	2025-06-22	2026-01-08	Ettinger	\N	\N	f	\N	220.71	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
678	1	1	\N	A-017-038	17	38	1675.79	excelente	vivero	\N	\N	0101000020E6100000A7AC514CEF3F53C00491AF7BC7FF27C0	1873.92	2023-11-23	2025-06-23	Hass	\N	\N	f	\N	171.46	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
679	1	1	\N	A-017-039	17	39	947.78	muerto	desarrollo_inmaduro	2026-07-21	\N	0101000020E6100000C395C2D8EE3F53C00491AF7BC7FF27C0	1723.47	2025-02-10	\N	Bacon	\N	\N	f	\N	296.48	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
680	1	1	\N	A-017-040	17	40	2213.91	muerto	produccion_madura	2026-01-29	\N	0101000020E6100000997E3365EE3F53C00491AF7BC7FF27C0	\N	2024-10-20	2026-03-23	Bacon	\N	\N	f	\N	203.40	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
681	1	1	\N	A-018-001	18	1	1957.58	erradicado	senescencia	2025-11-23	\N	0101000020E610000000000000004053C0A18A6AF3C3FF27C0	\N	2024-04-16	2024-07-12	Bacon	\N	\N	t	\N	151.73	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
682	1	1	\N	A-018-002	18	2	1744.06	excelente	establecimiento	\N	\N	0101000020E61000001CE9708CFF3F53C0A18A6AF3C3FF27C0	2024.69	2026-01-15	\N	Hass	\N	\N	f	\N	250.58	3	Quia tenetur voluptas aut doloremque eveniet iusto.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
683	1	1	\N	A-018-003	18	3	1104.31	erradicado	establecimiento	2026-05-04	\N	0101000020E6100000F2D1E118FF3F53C0A18A6AF3C3FF27C0	1339.21	2022-05-21	\N	Bacon	\N	\N	f	\N	379.80	6	Ut iusto nam libero quibusdam eum aut cum unde.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
684	1	1	\N	A-018-004	18	4	867.92	excelente	vivero	\N	\N	0101000020E61000000EBB52A5FE3F53C0A18A6AF3C3FF27C0	\N	2026-06-01	2026-06-26	Fuerte	\N	\N	f	\N	322.16	9	Beatae facilis enim nesciunt est quia iste voluptatibus omnis.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
685	1	1	\N	A-018-005	18	5	1866.78	enfermo_critico	senescencia	\N	\N	0101000020E6100000E3A3C331FE3F53C0A18A6AF3C3FF27C0	\N	2024-12-24	2025-01-12	Ettinger	\N	\N	f	\N	174.81	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
686	1	1	\N	A-018-006	18	6	1356.27	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000FF8C34BEFD3F53C0A18A6AF3C3FF27C0	2298.10	2021-09-08	\N	Fuerte	\N	\N	t	\N	182.03	4	Consequatur quisquam sit nisi nesciunt laboriosam laboriosam saepe.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
687	1	1	\N	A-018-007	18	7	1345.53	enfermo_critico	vivero	\N	\N	0101000020E6100000D575A54AFD3F53C0A18A6AF3C3FF27C0	1323.95	2022-12-17	2024-06-06	Fuerte	\N	\N	f	\N	211.87	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
688	1	1	\N	A-018-008	18	8	1790.94	muerto	establecimiento	2026-03-28	\N	0101000020E6100000F15E16D7FC3F53C0A18A6AF3C3FF27C0	1931.06	2024-06-27	2025-12-18	Bacon	\N	\N	f	\N	369.37	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
689	1	1	\N	A-018-009	18	9	2417.32	muerto	desarrollo_inmaduro	2026-03-08	\N	0101000020E61000000D488763FC3F53C0A18A6AF3C3FF27C0	1078.25	2023-10-23	2024-10-29	Zutano	\N	\N	f	\N	389.52	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
690	1	1	\N	A-018-010	18	10	1171.13	excelente	produccion_madura	\N	\N	0101000020E6100000E330F8EFFB3F53C0A18A6AF3C3FF27C0	1696.47	2024-03-17	\N	Zutano	\N	\N	f	\N	361.58	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
691	1	1	\N	A-018-011	18	11	1696.72	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000FF19697CFB3F53C0A18A6AF3C3FF27C0	\N	2025-11-03	\N	Bacon	\N	\N	f	\N	6.15	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
692	1	1	\N	A-018-012	18	12	1025.97	con_estres	produccion_madura	\N	\N	0101000020E6100000D502DA08FB3F53C0A18A6AF3C3FF27C0	1698.63	2026-05-23	2026-07-08	Hass	\N	\N	f	\N	27.58	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
693	1	1	\N	A-018-013	18	13	1508.59	excelente	establecimiento	\N	\N	0101000020E6100000F1EB4A95FA3F53C0A18A6AF3C3FF27C0	801.63	2024-12-26	2025-08-21	Bacon	\N	\N	f	\N	213.09	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
694	1	1	\N	A-018-014	18	14	1288.57	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000C6D4BB21FA3F53C0A18A6AF3C3FF27C0	2373.70	2022-10-31	2024-11-26	Hass	\N	\N	f	\N	347.94	10	Deserunt iste est maxime.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
695	1	1	\N	A-018-015	18	15	1329.77	excelente	produccion_madura	\N	\N	0101000020E6100000E2BD2CAEF93F53C0A18A6AF3C3FF27C0	1231.67	2023-05-27	2024-11-02	Bacon	\N	\N	f	\N	113.76	9	Veritatis atque quaerat vel.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
696	1	1	\N	A-018-016	18	16	998.62	muerto	produccion_madura	2026-08-14	\N	0101000020E6100000FEA69D3AF93F53C0A18A6AF3C3FF27C0	1999.88	2026-01-25	2026-07-03	Bacon	\N	\N	f	\N	129.69	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
697	1	1	\N	A-018-017	18	17	1088.09	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000D48F0EC7F83F53C0A18A6AF3C3FF27C0	1070.80	2026-01-26	2026-05-13	Hass	\N	\N	f	\N	251.59	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
698	1	1	\N	A-018-018	18	18	1054.02	erradicado	senescencia	2026-05-11	\N	0101000020E6100000F0787F53F83F53C0A18A6AF3C3FF27C0	878.30	2025-12-07	2026-06-11	Fuerte	\N	\N	f	\N	16.98	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
699	1	1	\N	A-018-019	18	19	1597.26	excelente	senescencia	\N	\N	0101000020E6100000C661F0DFF73F53C0A18A6AF3C3FF27C0	1867.55	2023-11-03	\N	Ettinger	\N	\N	f	\N	181.56	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
700	1	1	\N	A-018-020	18	20	1619.30	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000E24A616CF73F53C0A18A6AF3C3FF27C0	1234.03	2022-03-21	2025-06-25	Fuerte	\N	\N	f	\N	21.11	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
701	1	1	\N	A-018-021	18	21	894.87	erradicado	desarrollo_inmaduro	2025-10-28	\N	0101000020E6100000B733D2F8F63F53C0A18A6AF3C3FF27C0	\N	2023-05-30	\N	Ettinger	\N	\N	f	\N	163.93	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
702	1	1	\N	A-018-022	18	22	2454.06	erradicado	vivero	2026-07-07	\N	0101000020E6100000D31C4385F63F53C0A18A6AF3C3FF27C0	1961.56	2022-07-13	2023-12-13	Hass	\N	\N	f	\N	418.61	10	Reprehenderit aut accusamus sed nesciunt.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
703	1	1	\N	A-018-023	18	23	2055.44	muerto	vivero	2025-10-18	\N	0101000020E6100000EF05B411F63F53C0A18A6AF3C3FF27C0	1055.29	2021-10-10	\N	Bacon	\N	\N	f	\N	457.88	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
704	1	1	\N	A-018-024	18	24	1256.59	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000C5EE249EF53F53C0A18A6AF3C3FF27C0	\N	2024-12-28	2025-08-21	Bacon	\N	\N	f	\N	11.25	4	Perspiciatis delectus dolorem sunt quos sit voluptate qui.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
705	1	1	\N	A-018-025	18	25	1379.05	con_estres	vivero	\N	\N	0101000020E6100000E1D7952AF53F53C0A18A6AF3C3FF27C0	1776.60	2023-07-05	2023-09-28	Ettinger	\N	\N	f	\N	310.39	2	Ipsum dolorem adipisci voluptas quos unde voluptate dolores.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
706	1	1	\N	A-018-026	18	26	1263.97	muerto	senescencia	2026-06-09	\N	0101000020E6100000B7C006B7F43F53C0A18A6AF3C3FF27C0	1544.96	2022-02-18	\N	Hass	\N	\N	f	\N	8.29	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
707	1	1	\N	A-018-027	18	27	1257.16	enfermo_critico	senescencia	\N	\N	0101000020E6100000D3A97743F43F53C0A18A6AF3C3FF27C0	\N	2022-04-04	2022-04-19	Fuerte	\N	\N	f	\N	246.82	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
708	1	1	\N	A-018-028	18	28	2249.83	con_estres	establecimiento	\N	\N	0101000020E6100000A892E8CFF33F53C0A18A6AF3C3FF27C0	2050.35	2022-03-14	2022-07-21	Bacon	\N	\N	f	\N	272.55	6	Qui placeat magni voluptas soluta aut molestias.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
709	1	1	\N	A-018-029	18	29	1355.98	erradicado	establecimiento	2026-08-18	\N	0101000020E6100000C47B595CF33F53C0A18A6AF3C3FF27C0	1753.49	2025-04-14	2026-02-13	Hass	\N	\N	f	\N	22.72	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
710	1	1	\N	A-018-030	18	30	1357.34	muerto	senescencia	2026-05-15	\N	0101000020E6100000E164CAE8F23F53C0A18A6AF3C3FF27C0	1174.42	2023-03-10	2024-03-26	Zutano	\N	\N	f	\N	362.83	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
711	1	1	\N	A-018-031	18	31	1368.80	erradicado	senescencia	2025-11-12	\N	0101000020E6100000B64D3B75F23F53C0A18A6AF3C3FF27C0	1517.02	2023-10-03	2023-11-15	Ettinger	\N	\N	f	\N	438.40	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
712	1	1	\N	A-018-032	18	32	2465.32	muerto	desarrollo_inmaduro	2025-10-10	\N	0101000020E6100000D236AC01F23F53C0A18A6AF3C3FF27C0	927.63	2024-10-23	\N	Hass	\N	\N	t	\N	74.43	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
713	1	1	\N	A-018-033	18	33	1489.38	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000A81F1D8EF13F53C0A18A6AF3C3FF27C0	1395.34	2024-07-18	2026-07-17	Ettinger	\N	\N	f	\N	364.05	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
714	1	1	\N	A-018-034	18	34	917.42	muerto	desarrollo_inmaduro	2025-09-02	\N	0101000020E6100000C4088E1AF13F53C0A18A6AF3C3FF27C0	1960.16	2024-08-22	2025-06-10	Hass	\N	\N	f	\N	59.10	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
715	1	1	\N	A-018-035	18	35	2109.28	excelente	desarrollo_inmaduro	\N	\N	0101000020E61000009AF1FEA6F03F53C0A18A6AF3C3FF27C0	1294.68	2025-07-02	2025-12-05	Ettinger	\N	\N	f	\N	111.77	6	Inventore nulla ex ut sed repudiandae hic.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
716	1	1	\N	A-018-036	18	36	2044.28	excelente	senescencia	\N	\N	0101000020E6100000B6DA6F33F03F53C0A18A6AF3C3FF27C0	2484.66	2023-08-31	2024-01-04	Hass	\N	\N	f	\N	307.16	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
717	1	1	\N	A-018-037	18	37	1803.45	erradicado	desarrollo_inmaduro	2026-07-11	\N	0101000020E6100000D2C3E0BFEF3F53C0A18A6AF3C3FF27C0	\N	2023-09-26	\N	Ettinger	\N	\N	f	\N	409.84	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
718	1	1	\N	A-018-038	18	38	1139.81	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000A7AC514CEF3F53C0A18A6AF3C3FF27C0	2392.85	2022-04-29	2023-03-25	Fuerte	\N	\N	f	\N	162.80	5	Excepturi aut delectus sit recusandae qui voluptate.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
719	1	1	\N	A-018-039	18	39	2130.06	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000C395C2D8EE3F53C0A18A6AF3C3FF27C0	1121.27	2022-02-13	2026-06-15	Fuerte	\N	\N	f	\N	185.81	6	Est similique iure rerum.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
720	1	1	\N	A-018-040	18	40	1803.55	enfermo_critico	senescencia	\N	\N	0101000020E6100000997E3365EE3F53C0A18A6AF3C3FF27C0	2204.84	2024-11-13	2025-04-30	Zutano	\N	\N	f	\N	488.40	6	Et ab voluptas consectetur aut molestias aliquid est.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
721	1	1	\N	A-019-001	19	1	1407.76	erradicado	desarrollo_inmaduro	2025-11-30	\N	0101000020E610000000000000004053C03E84256BC0FF27C0	1056.64	2024-10-05	2025-09-10	Fuerte	\N	\N	t	\N	150.62	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
722	1	1	\N	A-019-002	19	2	1311.16	muerto	senescencia	2025-09-15	\N	0101000020E61000001CE9708CFF3F53C03E84256BC0FF27C0	1987.05	2023-10-09	2024-07-26	Bacon	\N	\N	f	\N	82.98	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
723	1	1	\N	A-019-003	19	3	2072.40	excelente	establecimiento	\N	\N	0101000020E6100000F2D1E118FF3F53C03E84256BC0FF27C0	\N	2026-03-30	2026-08-25	Hass	\N	\N	f	\N	439.82	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
724	1	1	\N	A-019-004	19	4	2295.85	muerto	establecimiento	2025-09-08	\N	0101000020E61000000EBB52A5FE3F53C03E84256BC0FF27C0	1029.04	2022-11-20	\N	Hass	\N	\N	f	\N	336.32	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
725	1	1	\N	A-019-005	19	5	2026.37	con_estres	produccion_madura	\N	\N	0101000020E6100000E3A3C331FE3F53C03E84256BC0FF27C0	1641.65	2022-07-28	\N	Bacon	\N	\N	f	\N	374.51	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
726	1	1	\N	A-019-006	19	6	1856.64	muerto	senescencia	2026-06-17	\N	0101000020E6100000FF8C34BEFD3F53C03E84256BC0FF27C0	862.85	2023-03-25	\N	Ettinger	\N	\N	f	\N	424.73	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
727	1	1	\N	A-019-007	19	7	947.42	muerto	desarrollo_inmaduro	2026-06-24	\N	0101000020E6100000D575A54AFD3F53C03E84256BC0FF27C0	1212.73	2022-11-05	2024-01-03	Hass	\N	\N	f	\N	183.96	5	Eaque eos enim est perspiciatis autem eaque cum.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
728	1	1	\N	A-019-008	19	8	1169.24	con_estres	vivero	\N	\N	0101000020E6100000F15E16D7FC3F53C03E84256BC0FF27C0	2127.32	2021-11-30	2026-07-17	Ettinger	\N	\N	f	\N	267.78	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
729	1	1	\N	A-019-009	19	9	2372.30	excelente	vivero	\N	\N	0101000020E61000000D488763FC3F53C03E84256BC0FF27C0	897.56	2024-04-25	\N	Hass	\N	\N	f	\N	457.66	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
730	1	1	\N	A-019-010	19	10	1576.46	muerto	produccion_madura	2026-08-10	\N	0101000020E6100000E330F8EFFB3F53C03E84256BC0FF27C0	2343.84	2023-06-07	2025-01-05	Zutano	\N	\N	f	\N	237.79	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
731	1	1	\N	A-019-011	19	11	2406.41	muerto	desarrollo_inmaduro	2026-02-14	\N	0101000020E6100000FF19697CFB3F53C03E84256BC0FF27C0	1902.93	2022-11-26	2023-11-27	Ettinger	\N	\N	f	\N	296.21	9	Omnis maxime impedit maiores nesciunt.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
732	1	1	\N	A-019-012	19	12	2230.16	erradicado	vivero	2026-05-12	\N	0101000020E6100000D502DA08FB3F53C03E84256BC0FF27C0	\N	2022-05-12	2024-12-10	Hass	\N	\N	f	\N	229.32	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
733	1	1	\N	A-019-013	19	13	964.91	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000F1EB4A95FA3F53C03E84256BC0FF27C0	1441.40	2024-01-01	2025-01-28	Zutano	\N	\N	f	\N	289.51	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
734	1	1	\N	A-019-014	19	14	1500.99	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000C6D4BB21FA3F53C03E84256BC0FF27C0	2083.24	2024-05-04	\N	Ettinger	\N	\N	f	\N	411.86	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
735	1	1	\N	A-019-015	19	15	2205.15	con_estres	senescencia	\N	\N	0101000020E6100000E2BD2CAEF93F53C03E84256BC0FF27C0	\N	2024-11-10	\N	Bacon	\N	\N	f	\N	464.13	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
736	1	1	\N	A-019-016	19	16	1918.23	excelente	establecimiento	\N	\N	0101000020E6100000FEA69D3AF93F53C03E84256BC0FF27C0	1007.67	2022-09-06	2025-07-17	Hass	\N	\N	f	\N	105.49	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
737	1	1	\N	A-019-017	19	17	1564.32	con_estres	vivero	\N	\N	0101000020E6100000D48F0EC7F83F53C03E84256BC0FF27C0	\N	2023-11-25	2024-12-08	Fuerte	\N	\N	f	\N	332.12	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
738	1	1	\N	A-019-018	19	18	1831.68	erradicado	produccion_madura	2025-11-26	\N	0101000020E6100000F0787F53F83F53C03E84256BC0FF27C0	2166.29	2021-09-21	2024-04-08	Fuerte	\N	\N	f	\N	369.86	9	Et ut nam nostrum non et.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
739	1	1	\N	A-019-019	19	19	1379.34	excelente	senescencia	\N	\N	0101000020E6100000C661F0DFF73F53C03E84256BC0FF27C0	\N	2022-10-08	\N	Ettinger	\N	\N	f	\N	270.43	7	Voluptate eum accusamus officia sunt repudiandae odio dolorem nobis.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
740	1	1	\N	A-019-020	19	20	2220.57	muerto	vivero	2026-06-28	\N	0101000020E6100000E24A616CF73F53C03E84256BC0FF27C0	\N	2023-06-22	2024-09-13	Bacon	\N	\N	f	\N	150.53	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
741	1	1	\N	A-019-021	19	21	1508.66	con_estres	senescencia	\N	\N	0101000020E6100000B733D2F8F63F53C03E84256BC0FF27C0	2299.17	2023-06-10	2025-06-13	Hass	\N	\N	f	\N	107.75	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
742	1	1	\N	A-019-022	19	22	2061.28	excelente	establecimiento	\N	\N	0101000020E6100000D31C4385F63F53C03E84256BC0FF27C0	1675.87	2026-01-02	2026-06-24	Hass	\N	\N	f	\N	180.24	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
743	1	1	\N	A-019-023	19	23	2327.00	muerto	produccion_madura	2025-12-12	\N	0101000020E6100000EF05B411F63F53C03E84256BC0FF27C0	\N	2023-04-17	2024-06-26	Ettinger	\N	\N	f	\N	119.02	5	Rerum quia dicta assumenda cum impedit deleniti iste similique.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
744	1	1	\N	A-019-024	19	24	2024.48	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000C5EE249EF53F53C03E84256BC0FF27C0	1438.29	2023-12-13	2024-09-28	Bacon	\N	\N	f	\N	312.26	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
745	1	1	\N	A-019-025	19	25	1313.10	enfermo_critico	senescencia	\N	\N	0101000020E6100000E1D7952AF53F53C03E84256BC0FF27C0	\N	2025-10-19	\N	Zutano	\N	\N	f	\N	261.89	3	Totam possimus non voluptas est perspiciatis laborum error.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
746	1	1	\N	A-019-026	19	26	1713.73	muerto	senescencia	2025-10-19	\N	0101000020E6100000B7C006B7F43F53C03E84256BC0FF27C0	2470.51	2024-06-20	2026-07-27	Bacon	\N	\N	f	\N	228.06	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
747	1	1	\N	A-019-027	19	27	1001.87	muerto	senescencia	2026-02-17	\N	0101000020E6100000D3A97743F43F53C03E84256BC0FF27C0	2407.40	2024-02-18	2024-11-05	Hass	\N	\N	f	\N	332.02	10	Quo temporibus dolores autem quis eius mollitia rerum.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
748	1	1	\N	A-019-028	19	28	1585.95	erradicado	vivero	2026-06-21	\N	0101000020E6100000A892E8CFF33F53C03E84256BC0FF27C0	\N	2024-12-12	\N	Hass	\N	\N	f	\N	482.37	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
749	1	1	\N	A-019-029	19	29	1482.26	excelente	produccion_madura	\N	\N	0101000020E6100000C47B595CF33F53C03E84256BC0FF27C0	1088.36	2022-06-24	\N	Ettinger	\N	\N	f	\N	363.34	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
750	1	1	\N	A-019-030	19	30	940.29	muerto	produccion_madura	2026-04-14	\N	0101000020E6100000E164CAE8F23F53C03E84256BC0FF27C0	1301.20	2021-12-12	2025-12-23	Fuerte	\N	\N	f	\N	36.70	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
751	1	1	\N	A-019-031	19	31	2435.09	muerto	establecimiento	2025-09-15	\N	0101000020E6100000B64D3B75F23F53C03E84256BC0FF27C0	1026.13	2022-11-28	2025-03-01	Bacon	\N	\N	f	\N	94.48	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
752	1	1	\N	A-019-032	19	32	1050.47	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000D236AC01F23F53C03E84256BC0FF27C0	1464.82	2022-04-24	\N	Zutano	\N	\N	f	\N	183.43	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
753	1	1	\N	A-019-033	19	33	2130.47	enfermo_critico	vivero	\N	\N	0101000020E6100000A81F1D8EF13F53C03E84256BC0FF27C0	1620.53	2023-10-17	2025-06-15	Ettinger	\N	\N	f	\N	296.57	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
754	1	1	\N	A-019-034	19	34	2315.19	excelente	senescencia	\N	\N	0101000020E6100000C4088E1AF13F53C03E84256BC0FF27C0	1296.83	2025-11-26	\N	Zutano	\N	\N	f	\N	222.50	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
755	1	1	\N	A-019-035	19	35	1103.88	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E61000009AF1FEA6F03F53C03E84256BC0FF27C0	\N	2022-09-18	2023-10-09	Fuerte	\N	\N	t	\N	20.43	9	Neque veniam aperiam inventore et id qui.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
756	1	1	\N	A-019-036	19	36	1848.67	erradicado	vivero	2025-09-16	\N	0101000020E6100000B6DA6F33F03F53C03E84256BC0FF27C0	\N	2024-04-14	\N	Bacon	\N	\N	f	\N	440.49	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
757	1	1	\N	A-019-037	19	37	2192.01	erradicado	produccion_madura	2025-09-24	\N	0101000020E6100000D2C3E0BFEF3F53C03E84256BC0FF27C0	1710.02	2025-08-17	2026-08-07	Zutano	\N	\N	f	\N	180.94	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
758	1	1	\N	A-019-038	19	38	2444.97	enfermo_critico	vivero	\N	\N	0101000020E6100000A7AC514CEF3F53C03E84256BC0FF27C0	2474.69	2026-06-22	2026-08-01	Fuerte	\N	\N	f	\N	371.00	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
759	1	1	\N	A-019-039	19	39	1438.75	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000C395C2D8EE3F53C03E84256BC0FF27C0	2227.12	2024-06-23	2026-07-13	Fuerte	\N	\N	f	\N	374.28	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
760	1	1	\N	A-019-040	19	40	1521.18	erradicado	senescencia	2026-01-25	\N	0101000020E6100000997E3365EE3F53C03E84256BC0FF27C0	1359.30	2022-08-04	2024-11-01	Ettinger	\N	\N	f	\N	15.01	8	Voluptates placeat consectetur harum porro fugit vitae.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
761	1	1	\N	A-020-001	20	1	1499.66	excelente	produccion_madura	\N	\N	0101000020E610000000000000004053C0A97BE0E2BCFF27C0	\N	2023-06-15	2024-09-17	Hass	\N	\N	f	\N	234.47	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
762	1	1	\N	A-020-002	20	2	2023.56	excelente	vivero	\N	\N	0101000020E61000001CE9708CFF3F53C0A97BE0E2BCFF27C0	961.06	2023-05-08	2026-06-14	Zutano	\N	\N	f	\N	382.44	8	Itaque minima eveniet repellat voluptatem commodi quidem architecto.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
801	1	1	\N	A-021-001	21	1	1597.44	enfermo_critico	vivero	\N	\N	0101000020E610000000000000004053C046759B5AB9FF27C0	1768.63	2025-08-07	2026-07-05	Hass	\N	\N	f	\N	233.82	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
763	1	1	\N	A-020-003	20	3	2348.56	erradicado	vivero	2026-06-15	\N	0101000020E6100000F2D1E118FF3F53C0A97BE0E2BCFF27C0	1493.38	2022-08-04	2024-10-21	Zutano	\N	\N	f	\N	498.53	7	Eveniet sed laboriosam qui commodi recusandae quas et.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
764	1	1	\N	A-020-004	20	4	1515.00	muerto	senescencia	2026-05-01	\N	0101000020E61000000EBB52A5FE3F53C0A97BE0E2BCFF27C0	2374.10	2021-10-03	2026-07-11	Hass	\N	\N	f	\N	48.74	10	Molestias porro quia a corporis.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
765	1	1	\N	A-020-005	20	5	2483.67	excelente	produccion_madura	\N	\N	0101000020E6100000E3A3C331FE3F53C0A97BE0E2BCFF27C0	1706.93	2021-10-15	\N	Hass	\N	\N	f	\N	413.11	5	Et quod modi sit ex consequatur vel nobis similique.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
766	1	1	\N	A-020-006	20	6	1487.58	con_estres	vivero	\N	\N	0101000020E6100000FF8C34BEFD3F53C0A97BE0E2BCFF27C0	1181.38	2024-08-02	\N	Hass	\N	\N	f	\N	100.74	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
767	1	1	\N	A-020-007	20	7	1448.35	muerto	establecimiento	2025-11-26	\N	0101000020E6100000D575A54AFD3F53C0A97BE0E2BCFF27C0	1061.86	2022-02-07	2025-02-18	Zutano	\N	\N	f	\N	394.98	3	Ex magnam et animi est ipsam accusamus.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
768	1	1	\N	A-020-008	20	8	1976.14	muerto	produccion_madura	2026-05-24	\N	0101000020E6100000F15E16D7FC3F53C0A97BE0E2BCFF27C0	1621.54	2025-04-02	2025-10-13	Ettinger	\N	\N	f	\N	157.89	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
769	1	1	\N	A-020-009	20	9	1316.36	excelente	desarrollo_inmaduro	\N	\N	0101000020E61000000D488763FC3F53C0A97BE0E2BCFF27C0	2292.86	2023-10-02	2025-08-23	Fuerte	\N	\N	f	\N	106.29	8	Aperiam sed sint et sint amet alias sapiente.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
770	1	1	\N	A-020-010	20	10	1184.19	muerto	desarrollo_inmaduro	2025-11-10	\N	0101000020E6100000E330F8EFFB3F53C0A97BE0E2BCFF27C0	1235.90	2024-07-08	\N	Bacon	\N	\N	f	\N	467.96	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
771	1	1	\N	A-020-011	20	11	1296.51	erradicado	produccion_madura	2025-10-15	\N	0101000020E6100000FF19697CFB3F53C0A97BE0E2BCFF27C0	2499.98	2024-09-13	2026-06-12	Ettinger	\N	\N	f	\N	56.10	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
772	1	1	\N	A-020-012	20	12	1708.82	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000D502DA08FB3F53C0A97BE0E2BCFF27C0	2383.35	2022-12-16	\N	Ettinger	\N	\N	f	\N	46.39	0	Rerum nemo porro autem voluptatum beatae blanditiis.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
773	1	1	\N	A-020-013	20	13	1552.04	erradicado	desarrollo_inmaduro	2026-07-31	\N	0101000020E6100000F1EB4A95FA3F53C0A97BE0E2BCFF27C0	\N	2024-07-31	\N	Bacon	\N	\N	f	\N	324.23	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
774	1	1	\N	A-020-014	20	14	1777.14	enfermo_critico	senescencia	\N	\N	0101000020E6100000C6D4BB21FA3F53C0A97BE0E2BCFF27C0	1429.20	2021-09-25	2023-07-03	Bacon	\N	\N	t	\N	164.78	8	Non repellat quia sint dolorem reprehenderit earum.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
775	1	1	\N	A-020-015	20	15	1849.20	con_estres	produccion_madura	\N	\N	0101000020E6100000E2BD2CAEF93F53C0A97BE0E2BCFF27C0	1486.23	2026-07-20	2026-08-02	Fuerte	\N	\N	f	\N	65.54	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
776	1	1	\N	A-020-016	20	16	2190.27	muerto	produccion_madura	2026-01-25	\N	0101000020E6100000FEA69D3AF93F53C0A97BE0E2BCFF27C0	1894.40	2023-06-27	\N	Ettinger	\N	\N	f	\N	306.65	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
777	1	1	\N	A-020-017	20	17	2382.88	con_estres	establecimiento	\N	\N	0101000020E6100000D48F0EC7F83F53C0A97BE0E2BCFF27C0	1681.38	2025-05-08	2025-10-26	Zutano	\N	\N	f	\N	435.17	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
778	1	1	\N	A-020-018	20	18	1614.91	enfermo_critico	senescencia	\N	\N	0101000020E6100000F0787F53F83F53C0A97BE0E2BCFF27C0	\N	2023-12-03	2026-05-09	Ettinger	\N	\N	f	\N	152.79	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
779	1	1	\N	A-020-019	20	19	1300.22	muerto	senescencia	2026-06-11	\N	0101000020E6100000C661F0DFF73F53C0A97BE0E2BCFF27C0	1807.17	2025-07-30	2026-07-20	Fuerte	\N	\N	f	\N	308.97	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
780	1	1	\N	A-020-020	20	20	893.96	erradicado	produccion_madura	2025-12-13	\N	0101000020E6100000E24A616CF73F53C0A97BE0E2BCFF27C0	1473.42	2025-12-08	2026-06-30	Bacon	\N	\N	f	\N	365.82	9	Adipisci beatae doloribus expedita.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
781	1	1	\N	A-020-021	20	21	2072.35	muerto	desarrollo_inmaduro	2025-10-08	\N	0101000020E6100000B733D2F8F63F53C0A97BE0E2BCFF27C0	2153.37	2023-07-23	2025-12-16	Hass	\N	\N	f	\N	365.93	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
782	1	1	\N	A-020-022	20	22	1164.26	erradicado	produccion_madura	2025-11-10	\N	0101000020E6100000D31C4385F63F53C0A97BE0E2BCFF27C0	2159.71	2022-03-28	2024-01-16	Zutano	\N	\N	f	\N	472.17	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
783	1	1	\N	A-020-023	20	23	1482.27	enfermo_critico	vivero	\N	\N	0101000020E6100000EF05B411F63F53C0A97BE0E2BCFF27C0	1304.04	2026-01-15	\N	Fuerte	\N	\N	f	\N	6.17	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
784	1	1	\N	A-020-024	20	24	2243.68	erradicado	establecimiento	2026-08-19	\N	0101000020E6100000C5EE249EF53F53C0A97BE0E2BCFF27C0	2482.99	2025-02-24	2026-05-19	Fuerte	\N	\N	f	\N	377.94	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
785	1	1	\N	A-020-025	20	25	2007.76	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000E1D7952AF53F53C0A97BE0E2BCFF27C0	2426.79	2023-12-18	2026-07-06	Ettinger	\N	\N	f	\N	166.48	1	Quibusdam consequatur voluptatum facere cupiditate quas.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
786	1	1	\N	A-020-026	20	26	1282.67	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000B7C006B7F43F53C0A97BE0E2BCFF27C0	1066.34	2023-10-18	2026-02-17	Hass	\N	\N	f	\N	431.05	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
787	1	1	\N	A-020-027	20	27	901.99	muerto	senescencia	2026-03-30	\N	0101000020E6100000D3A97743F43F53C0A97BE0E2BCFF27C0	1798.84	2023-01-29	2024-09-21	Bacon	\N	\N	f	\N	406.20	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
788	1	1	\N	A-020-028	20	28	1394.16	con_estres	vivero	\N	\N	0101000020E6100000A892E8CFF33F53C0A97BE0E2BCFF27C0	\N	2025-11-22	2026-05-28	Ettinger	\N	\N	t	\N	483.76	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
789	1	1	\N	A-020-029	20	29	1326.96	muerto	desarrollo_inmaduro	2026-02-06	\N	0101000020E6100000C47B595CF33F53C0A97BE0E2BCFF27C0	959.53	2024-06-20	2025-07-31	Bacon	\N	\N	f	\N	276.19	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
790	1	1	\N	A-020-030	20	30	2485.22	excelente	establecimiento	\N	\N	0101000020E6100000E164CAE8F23F53C0A97BE0E2BCFF27C0	1268.73	2023-03-12	2025-03-30	Ettinger	\N	\N	f	\N	429.07	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
791	1	1	\N	A-020-031	20	31	980.15	muerto	desarrollo_inmaduro	2025-11-17	\N	0101000020E6100000B64D3B75F23F53C0A97BE0E2BCFF27C0	1826.89	2025-10-22	2026-01-28	Fuerte	\N	\N	f	\N	163.57	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
792	1	1	\N	A-020-032	20	32	974.93	enfermo_critico	vivero	\N	\N	0101000020E6100000D236AC01F23F53C0A97BE0E2BCFF27C0	\N	2025-02-24	2025-12-01	Zutano	\N	\N	f	\N	87.71	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
793	1	1	\N	A-020-033	20	33	1990.64	enfermo_critico	senescencia	\N	\N	0101000020E6100000A81F1D8EF13F53C0A97BE0E2BCFF27C0	834.47	2021-09-06	\N	Fuerte	\N	\N	t	\N	143.84	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
794	1	1	\N	A-020-034	20	34	2057.83	excelente	senescencia	\N	\N	0101000020E6100000C4088E1AF13F53C0A97BE0E2BCFF27C0	1788.06	2023-08-18	2025-04-29	Zutano	\N	\N	f	\N	80.92	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
795	1	1	\N	A-020-035	20	35	2215.49	erradicado	vivero	2025-12-30	\N	0101000020E61000009AF1FEA6F03F53C0A97BE0E2BCFF27C0	2013.37	2024-09-26	2025-03-10	Hass	\N	\N	f	\N	238.42	1	Magnam et aliquid ea omnis.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
796	1	1	\N	A-020-036	20	36	2122.57	erradicado	produccion_madura	2026-04-19	\N	0101000020E6100000B6DA6F33F03F53C0A97BE0E2BCFF27C0	1734.21	2023-09-27	\N	Ettinger	\N	\N	f	\N	195.14	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
797	1	1	\N	A-020-037	20	37	914.34	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000D2C3E0BFEF3F53C0A97BE0E2BCFF27C0	915.19	2026-05-05	2026-06-07	Bacon	\N	\N	f	\N	299.17	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
798	1	1	\N	A-020-038	20	38	2215.43	enfermo_critico	senescencia	\N	\N	0101000020E6100000A7AC514CEF3F53C0A97BE0E2BCFF27C0	1713.86	2023-01-19	2024-08-11	Bacon	\N	\N	f	\N	151.13	6	Sed odit saepe quasi non iste repellendus.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
799	1	1	\N	A-020-039	20	39	1073.99	enfermo_critico	vivero	\N	\N	0101000020E6100000C395C2D8EE3F53C0A97BE0E2BCFF27C0	2223.08	2026-04-06	2026-04-25	Fuerte	\N	\N	f	\N	471.41	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
800	1	1	\N	A-020-040	20	40	942.47	erradicado	senescencia	2025-12-19	\N	0101000020E6100000997E3365EE3F53C0A97BE0E2BCFF27C0	1580.22	2025-05-26	2025-08-31	Bacon	\N	\N	f	\N	141.13	5	Magnam molestiae non distinctio voluptatum.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
802	1	1	\N	A-021-002	21	2	1563.33	enfermo_critico	establecimiento	\N	\N	0101000020E61000001CE9708CFF3F53C046759B5AB9FF27C0	2348.56	2022-05-15	2025-12-03	Bacon	\N	\N	f	\N	59.04	2	In rerum velit distinctio qui est possimus ipsam.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
803	1	1	\N	A-021-003	21	3	1980.81	con_estres	produccion_madura	\N	\N	0101000020E6100000F2D1E118FF3F53C046759B5AB9FF27C0	2105.77	2022-11-18	2024-08-06	Zutano	\N	\N	f	\N	320.52	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
804	1	1	\N	A-021-004	21	4	1344.44	erradicado	desarrollo_inmaduro	2025-12-23	\N	0101000020E61000000EBB52A5FE3F53C046759B5AB9FF27C0	1065.88	2025-01-05	2025-01-28	Hass	\N	\N	f	\N	19.83	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
805	1	1	\N	A-021-005	21	5	1206.07	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000E3A3C331FE3F53C046759B5AB9FF27C0	\N	2025-07-11	2025-09-10	Zutano	\N	\N	t	\N	308.30	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
806	1	1	\N	A-021-006	21	6	1166.43	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000FF8C34BEFD3F53C046759B5AB9FF27C0	2485.98	2022-01-24	2023-08-26	Ettinger	\N	\N	f	\N	180.75	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
807	1	1	\N	A-021-007	21	7	1357.14	muerto	desarrollo_inmaduro	2025-09-07	\N	0101000020E6100000D575A54AFD3F53C046759B5AB9FF27C0	1800.30	2021-09-17	2024-09-23	Bacon	\N	\N	f	\N	354.94	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
808	1	1	\N	A-021-008	21	8	1300.24	con_estres	establecimiento	\N	\N	0101000020E6100000F15E16D7FC3F53C046759B5AB9FF27C0	\N	2022-05-11	2024-01-08	Zutano	\N	\N	f	\N	428.44	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
809	1	1	\N	A-021-009	21	9	1856.79	enfermo_critico	senescencia	\N	\N	0101000020E61000000D488763FC3F53C046759B5AB9FF27C0	1258.48	2023-06-30	2024-01-12	Bacon	\N	\N	f	\N	290.62	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
810	1	1	\N	A-021-010	21	10	1422.44	enfermo_critico	senescencia	\N	\N	0101000020E6100000E330F8EFFB3F53C046759B5AB9FF27C0	1733.17	2023-09-27	2024-03-09	Hass	\N	\N	f	\N	292.83	7	Accusantium enim officia quaerat harum.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
811	1	1	\N	A-021-011	21	11	1751.01	muerto	senescencia	2026-02-23	\N	0101000020E6100000FF19697CFB3F53C046759B5AB9FF27C0	1500.62	2024-02-04	\N	Fuerte	\N	\N	f	\N	380.28	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
812	1	1	\N	A-021-012	21	12	1739.68	excelente	produccion_madura	\N	\N	0101000020E6100000D502DA08FB3F53C046759B5AB9FF27C0	1233.85	2022-03-12	\N	Bacon	\N	\N	f	\N	282.71	8	Voluptatem iure voluptas incidunt est.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
813	1	1	\N	A-021-013	21	13	1144.61	erradicado	establecimiento	2025-12-24	\N	0101000020E6100000F1EB4A95FA3F53C046759B5AB9FF27C0	\N	2024-03-12	\N	Zutano	\N	\N	f	\N	361.53	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
814	1	1	\N	A-021-014	21	14	935.09	excelente	senescencia	\N	\N	0101000020E6100000C6D4BB21FA3F53C046759B5AB9FF27C0	\N	2022-10-04	\N	Fuerte	\N	\N	f	\N	311.27	5	Eos placeat quae quia illum nesciunt totam repudiandae magnam.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
815	1	1	\N	A-021-015	21	15	1749.35	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000E2BD2CAEF93F53C046759B5AB9FF27C0	2223.64	2022-12-15	2025-11-06	Fuerte	\N	\N	f	\N	134.18	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
816	1	1	\N	A-021-016	21	16	1230.67	excelente	vivero	\N	\N	0101000020E6100000FEA69D3AF93F53C046759B5AB9FF27C0	2099.44	2021-09-14	\N	Hass	\N	\N	f	\N	385.31	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
817	1	1	\N	A-021-017	21	17	1840.19	excelente	produccion_madura	\N	\N	0101000020E6100000D48F0EC7F83F53C046759B5AB9FF27C0	1202.68	2022-01-11	2023-11-02	Fuerte	\N	\N	f	\N	184.16	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
818	1	1	\N	A-021-018	21	18	953.31	excelente	vivero	\N	\N	0101000020E6100000F0787F53F83F53C046759B5AB9FF27C0	1428.97	2024-05-02	2025-05-10	Hass	\N	\N	f	\N	207.99	9	Ut excepturi ad ex numquam voluptate repellendus voluptatem.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
819	1	1	\N	A-021-019	21	19	2455.15	erradicado	establecimiento	2026-01-20	\N	0101000020E6100000C661F0DFF73F53C046759B5AB9FF27C0	1291.42	2021-12-03	\N	Fuerte	\N	\N	f	\N	197.19	7	Est cumque quaerat et ullam commodi dolore rem.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
820	1	1	\N	A-021-020	21	20	2446.35	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000E24A616CF73F53C046759B5AB9FF27C0	802.90	2023-07-07	\N	Bacon	\N	\N	f	\N	366.90	3	Quaerat autem consequatur iure vero.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
821	1	1	\N	A-021-021	21	21	1044.32	muerto	establecimiento	2026-02-01	\N	0101000020E6100000B733D2F8F63F53C046759B5AB9FF27C0	1529.19	2021-10-11	\N	Zutano	\N	\N	f	\N	241.12	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
822	1	1	\N	A-021-022	21	22	1635.01	muerto	senescencia	2025-12-03	\N	0101000020E6100000D31C4385F63F53C046759B5AB9FF27C0	805.13	2024-04-01	2025-10-19	Bacon	\N	\N	f	\N	180.87	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
823	1	1	\N	A-021-023	21	23	1119.36	muerto	establecimiento	2026-07-09	\N	0101000020E6100000EF05B411F63F53C046759B5AB9FF27C0	\N	2025-12-09	2026-08-11	Fuerte	\N	\N	f	\N	194.61	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
824	1	1	\N	A-021-024	21	24	1397.84	erradicado	establecimiento	2025-10-27	\N	0101000020E6100000C5EE249EF53F53C046759B5AB9FF27C0	1371.61	2024-06-24	2025-12-07	Bacon	\N	\N	f	\N	453.66	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
825	1	1	\N	A-021-025	21	25	1668.88	muerto	establecimiento	2026-07-29	\N	0101000020E6100000E1D7952AF53F53C046759B5AB9FF27C0	1875.42	2022-06-12	2022-09-23	Bacon	\N	\N	f	\N	220.45	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
826	1	1	\N	A-021-026	21	26	1204.80	enfermo_critico	vivero	\N	\N	0101000020E6100000B7C006B7F43F53C046759B5AB9FF27C0	1780.98	2026-02-28	2026-06-21	Zutano	\N	\N	f	\N	488.91	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
827	1	1	\N	A-021-027	21	27	1849.87	excelente	senescencia	\N	\N	0101000020E6100000D3A97743F43F53C046759B5AB9FF27C0	2038.38	2026-06-20	\N	Fuerte	\N	\N	f	\N	332.52	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
828	1	1	\N	A-021-028	21	28	2189.37	muerto	produccion_madura	2026-03-29	\N	0101000020E6100000A892E8CFF33F53C046759B5AB9FF27C0	1633.43	2025-06-07	\N	Ettinger	\N	\N	f	\N	243.57	5	Voluptas omnis est ea commodi.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
829	1	1	\N	A-021-029	21	29	1072.00	muerto	produccion_madura	2026-05-03	\N	0101000020E6100000C47B595CF33F53C046759B5AB9FF27C0	1459.03	2025-12-22	2026-06-29	Fuerte	\N	\N	f	\N	62.65	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
830	1	1	\N	A-021-030	21	30	836.30	erradicado	desarrollo_inmaduro	2026-05-23	\N	0101000020E6100000E164CAE8F23F53C046759B5AB9FF27C0	920.00	2021-09-29	2024-12-07	Bacon	\N	\N	f	\N	489.82	1	Aut deserunt ut magnam tempora quod voluptatum est.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
831	1	1	\N	A-021-031	21	31	1791.52	erradicado	establecimiento	2025-11-21	\N	0101000020E6100000B64D3B75F23F53C046759B5AB9FF27C0	1540.21	2026-02-11	\N	Zutano	\N	\N	f	\N	170.71	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
832	1	1	\N	A-021-032	21	32	1284.75	erradicado	establecimiento	2026-01-25	\N	0101000020E6100000D236AC01F23F53C046759B5AB9FF27C0	\N	2024-10-14	2026-05-30	Bacon	\N	\N	f	\N	111.23	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
833	1	1	\N	A-021-033	21	33	1675.62	excelente	produccion_madura	\N	\N	0101000020E6100000A81F1D8EF13F53C046759B5AB9FF27C0	1003.34	2023-02-21	2023-05-12	Zutano	\N	\N	f	\N	284.16	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
834	1	1	\N	A-021-034	21	34	1512.09	erradicado	desarrollo_inmaduro	2025-10-14	\N	0101000020E6100000C4088E1AF13F53C046759B5AB9FF27C0	1589.54	2023-04-09	2025-03-28	Hass	\N	\N	f	\N	151.10	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
835	1	1	\N	A-021-035	21	35	2096.23	con_estres	vivero	\N	\N	0101000020E61000009AF1FEA6F03F53C046759B5AB9FF27C0	1523.50	2021-12-20	2023-05-12	Bacon	\N	\N	f	\N	257.27	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
836	1	1	\N	A-021-036	21	36	1419.00	excelente	produccion_madura	\N	\N	0101000020E6100000B6DA6F33F03F53C046759B5AB9FF27C0	1700.18	2025-01-13	\N	Bacon	\N	\N	f	\N	109.37	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
837	1	1	\N	A-021-037	21	37	2319.56	excelente	senescencia	\N	\N	0101000020E6100000D2C3E0BFEF3F53C046759B5AB9FF27C0	1538.60	2023-11-21	\N	Bacon	\N	\N	f	\N	142.15	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
838	1	1	\N	A-021-038	21	38	1258.48	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000A7AC514CEF3F53C046759B5AB9FF27C0	2219.02	2024-06-17	2024-07-13	Hass	\N	\N	f	\N	172.45	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
839	1	1	\N	A-021-039	21	39	1299.01	muerto	produccion_madura	2025-09-13	\N	0101000020E6100000C395C2D8EE3F53C046759B5AB9FF27C0	828.53	2025-12-26	2026-04-27	Bacon	\N	\N	f	\N	296.43	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
878	1	1	\N	A-022-038	22	38	2177.78	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000A7AC514CEF3F53C0E36E56D2B5FF27C0	\N	2022-02-25	2024-07-05	Zutano	\N	\N	f	\N	321.29	5	Assumenda deserunt omnis quo recusandae corrupti.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
840	1	1	\N	A-021-040	21	40	1060.50	enfermo_critico	establecimiento	\N	\N	0101000020E6100000997E3365EE3F53C046759B5AB9FF27C0	1279.50	2026-04-10	2026-07-24	Ettinger	\N	\N	f	\N	425.07	0	Voluptas delectus molestias occaecati provident asperiores dignissimos necessitatibus omnis.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
841	1	1	\N	A-022-001	22	1	2147.71	erradicado	desarrollo_inmaduro	2026-04-23	\N	0101000020E610000000000000004053C0E36E56D2B5FF27C0	\N	2021-09-16	\N	Ettinger	\N	\N	f	\N	369.60	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
842	1	1	\N	A-022-002	22	2	2312.57	erradicado	establecimiento	2025-10-23	\N	0101000020E61000001CE9708CFF3F53C0E36E56D2B5FF27C0	1042.56	2023-08-27	\N	Zutano	\N	\N	t	\N	87.23	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
843	1	1	\N	A-022-003	22	3	860.66	excelente	senescencia	\N	\N	0101000020E6100000F2D1E118FF3F53C0E36E56D2B5FF27C0	\N	2023-09-29	2024-05-19	Hass	\N	\N	f	\N	91.94	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
844	1	1	\N	A-022-004	22	4	2457.60	muerto	senescencia	2025-11-15	\N	0101000020E61000000EBB52A5FE3F53C0E36E56D2B5FF27C0	2171.69	2022-10-25	\N	Bacon	\N	\N	f	\N	139.14	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
845	1	1	\N	A-022-005	22	5	1206.82	erradicado	desarrollo_inmaduro	2025-12-02	\N	0101000020E6100000E3A3C331FE3F53C0E36E56D2B5FF27C0	1664.98	2025-12-13	2026-02-23	Hass	\N	\N	f	\N	243.69	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
846	1	1	\N	A-022-006	22	6	1200.01	enfermo_critico	establecimiento	\N	\N	0101000020E6100000FF8C34BEFD3F53C0E36E56D2B5FF27C0	2059.98	2026-03-05	\N	Fuerte	\N	\N	f	\N	304.71	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
847	1	1	\N	A-022-007	22	7	800.75	enfermo_critico	establecimiento	\N	\N	0101000020E6100000D575A54AFD3F53C0E36E56D2B5FF27C0	2384.59	2023-05-15	2025-09-16	Hass	\N	\N	f	\N	364.96	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
848	1	1	\N	A-022-008	22	8	2164.33	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000F15E16D7FC3F53C0E36E56D2B5FF27C0	931.83	2026-02-09	2026-04-23	Zutano	\N	\N	f	\N	368.31	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
849	1	1	\N	A-022-009	22	9	1729.30	excelente	senescencia	\N	\N	0101000020E61000000D488763FC3F53C0E36E56D2B5FF27C0	1094.89	2024-10-07	2025-12-24	Fuerte	\N	\N	f	\N	39.31	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
850	1	1	\N	A-022-010	22	10	2333.21	con_estres	senescencia	\N	\N	0101000020E6100000E330F8EFFB3F53C0E36E56D2B5FF27C0	1567.25	2025-06-17	2026-04-23	Fuerte	\N	\N	f	\N	194.64	0	Tenetur unde et dolorum ea praesentium rerum cupiditate.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
851	1	1	\N	A-022-011	22	11	1468.39	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000FF19697CFB3F53C0E36E56D2B5FF27C0	1703.15	2023-11-02	2025-07-01	Hass	\N	\N	f	\N	235.78	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
852	1	1	\N	A-022-012	22	12	804.18	muerto	vivero	2026-04-29	\N	0101000020E6100000D502DA08FB3F53C0E36E56D2B5FF27C0	1809.25	2021-11-13	2025-09-25	Zutano	\N	\N	f	\N	145.14	8	Facere occaecati consequatur dolorem sed.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
853	1	1	\N	A-022-013	22	13	849.41	excelente	produccion_madura	\N	\N	0101000020E6100000F1EB4A95FA3F53C0E36E56D2B5FF27C0	1455.50	2022-06-11	\N	Bacon	\N	\N	f	\N	246.70	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
854	1	1	\N	A-022-014	22	14	1997.58	erradicado	vivero	2025-12-13	\N	0101000020E6100000C6D4BB21FA3F53C0E36E56D2B5FF27C0	1549.52	2023-04-17	\N	Hass	\N	\N	f	\N	490.72	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
855	1	1	\N	A-022-015	22	15	1206.65	enfermo_critico	vivero	\N	\N	0101000020E6100000E2BD2CAEF93F53C0E36E56D2B5FF27C0	2251.50	2023-03-01	\N	Bacon	\N	\N	f	\N	296.56	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
856	1	1	\N	A-022-016	22	16	1293.14	erradicado	senescencia	2025-09-24	\N	0101000020E6100000FEA69D3AF93F53C0E36E56D2B5FF27C0	1198.72	2026-05-26	2026-08-05	Ettinger	\N	\N	f	\N	258.04	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
857	1	1	\N	A-022-017	22	17	1160.78	con_estres	senescencia	\N	\N	0101000020E6100000D48F0EC7F83F53C0E36E56D2B5FF27C0	1337.82	2021-09-19	\N	Bacon	\N	\N	f	\N	273.30	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
858	1	1	\N	A-022-018	22	18	1890.09	muerto	desarrollo_inmaduro	2026-08-25	\N	0101000020E6100000F0787F53F83F53C0E36E56D2B5FF27C0	\N	2022-02-27	\N	Zutano	\N	\N	f	\N	398.51	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
859	1	1	\N	A-022-019	22	19	1847.20	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000C661F0DFF73F53C0E36E56D2B5FF27C0	2098.71	2023-10-10	2025-02-08	Fuerte	\N	\N	f	\N	268.34	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
860	1	1	\N	A-022-020	22	20	1581.54	erradicado	senescencia	2026-01-08	\N	0101000020E6100000E24A616CF73F53C0E36E56D2B5FF27C0	1301.69	2023-06-01	2024-04-06	Bacon	\N	\N	f	\N	46.19	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
861	1	1	\N	A-022-021	22	21	2397.98	erradicado	senescencia	2025-12-30	\N	0101000020E6100000B733D2F8F63F53C0E36E56D2B5FF27C0	877.67	2023-05-16	\N	Zutano	\N	\N	f	\N	38.42	6	Quam assumenda tenetur alias dignissimos.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
862	1	1	\N	A-022-022	22	22	1586.51	excelente	establecimiento	\N	\N	0101000020E6100000D31C4385F63F53C0E36E56D2B5FF27C0	1977.61	2025-09-10	2026-06-30	Fuerte	\N	\N	f	\N	353.44	6	Fugit minus dolores adipisci et necessitatibus.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
863	1	1	\N	A-022-023	22	23	1721.58	enfermo_critico	senescencia	\N	\N	0101000020E6100000EF05B411F63F53C0E36E56D2B5FF27C0	2049.35	2026-02-18	2026-04-23	Fuerte	\N	\N	f	\N	196.49	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
864	1	1	\N	A-022-024	22	24	1464.67	erradicado	establecimiento	2026-05-27	\N	0101000020E6100000C5EE249EF53F53C0E36E56D2B5FF27C0	1094.83	2023-01-10	2024-11-24	Ettinger	\N	\N	f	\N	301.80	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
865	1	1	\N	A-022-025	22	25	1421.45	erradicado	senescencia	2025-11-12	\N	0101000020E6100000E1D7952AF53F53C0E36E56D2B5FF27C0	1763.47	2023-12-19	2024-01-18	Zutano	\N	\N	f	\N	453.68	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
866	1	1	\N	A-022-026	22	26	1543.59	muerto	senescencia	2026-07-23	\N	0101000020E6100000B7C006B7F43F53C0E36E56D2B5FF27C0	\N	2022-10-24	2025-10-11	Bacon	\N	\N	f	\N	484.22	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
867	1	1	\N	A-022-027	22	27	1735.10	muerto	produccion_madura	2025-10-09	\N	0101000020E6100000D3A97743F43F53C0E36E56D2B5FF27C0	1728.85	2025-05-10	2026-05-21	Ettinger	\N	\N	f	\N	139.03	5	Et error eum quod.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
868	1	1	\N	A-022-028	22	28	849.68	muerto	establecimiento	2026-08-20	\N	0101000020E6100000A892E8CFF33F53C0E36E56D2B5FF27C0	2402.81	2026-06-22	2026-07-30	Fuerte	\N	\N	f	\N	345.22	10	Accusantium illo quo autem suscipit repellat dolorem deleniti veniam.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
869	1	1	\N	A-022-029	22	29	1668.93	erradicado	desarrollo_inmaduro	2026-07-21	\N	0101000020E6100000C47B595CF33F53C0E36E56D2B5FF27C0	2181.55	2022-07-30	2023-05-30	Bacon	\N	\N	f	\N	400.55	9	Nemo ut voluptatem dolorem tempora distinctio asperiores laboriosam.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
870	1	1	\N	A-022-030	22	30	1735.50	muerto	desarrollo_inmaduro	2026-01-27	\N	0101000020E6100000E164CAE8F23F53C0E36E56D2B5FF27C0	2145.67	2022-07-12	\N	Bacon	\N	\N	f	\N	351.70	9	Tenetur eius velit ut voluptate veritatis.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
871	1	1	\N	A-022-031	22	31	2141.46	enfermo_critico	senescencia	\N	\N	0101000020E6100000B64D3B75F23F53C0E36E56D2B5FF27C0	1028.71	2023-10-27	\N	Ettinger	\N	\N	f	\N	328.93	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
872	1	1	\N	A-022-032	22	32	2273.24	con_estres	senescencia	\N	\N	0101000020E6100000D236AC01F23F53C0E36E56D2B5FF27C0	1652.40	2026-04-03	\N	Ettinger	\N	\N	f	\N	498.18	8	Quos dolor ullam quis.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
873	1	1	\N	A-022-033	22	33	1669.53	erradicado	desarrollo_inmaduro	2026-02-02	\N	0101000020E6100000A81F1D8EF13F53C0E36E56D2B5FF27C0	1733.01	2025-10-21	2025-11-22	Bacon	\N	\N	f	\N	414.99	7	Exercitationem sit consectetur laboriosam inventore minima iure repellendus.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
874	1	1	\N	A-022-034	22	34	1575.21	muerto	establecimiento	2026-03-16	\N	0101000020E6100000C4088E1AF13F53C0E36E56D2B5FF27C0	1103.90	2021-08-30	\N	Hass	\N	\N	f	\N	60.67	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
875	1	1	\N	A-022-035	22	35	983.73	enfermo_critico	produccion_madura	\N	\N	0101000020E61000009AF1FEA6F03F53C0E36E56D2B5FF27C0	\N	2025-11-25	\N	Ettinger	\N	\N	f	\N	224.52	9	Inventore non cupiditate qui aut quis facilis.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
876	1	1	\N	A-022-036	22	36	965.91	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000B6DA6F33F03F53C0E36E56D2B5FF27C0	913.57	2025-11-07	\N	Zutano	\N	\N	f	\N	12.06	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
877	1	1	\N	A-022-037	22	37	2303.91	erradicado	vivero	2025-10-03	\N	0101000020E6100000D2C3E0BFEF3F53C0E36E56D2B5FF27C0	1631.13	2025-10-18	\N	Zutano	\N	\N	f	\N	445.26	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
879	1	1	\N	A-022-039	22	39	1277.58	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000C395C2D8EE3F53C0E36E56D2B5FF27C0	1205.84	2022-12-04	2026-03-12	Bacon	\N	\N	t	\N	145.36	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
880	1	1	\N	A-022-040	22	40	2177.70	muerto	vivero	2026-03-30	\N	0101000020E6100000997E3365EE3F53C0E36E56D2B5FF27C0	1063.42	2023-12-01	\N	Fuerte	\N	\N	f	\N	452.14	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
881	1	1	\N	A-023-001	23	1	1734.74	muerto	senescencia	2026-07-18	\N	0101000020E610000000000000004053C08068114AB2FF27C0	2175.41	2024-05-22	2026-02-02	Ettinger	\N	\N	f	\N	49.76	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
882	1	1	\N	A-023-002	23	2	1488.57	excelente	desarrollo_inmaduro	\N	\N	0101000020E61000001CE9708CFF3F53C08068114AB2FF27C0	1778.40	2024-08-09	2025-06-09	Ettinger	\N	\N	f	\N	415.93	10	Officia temporibus in odio rerum rerum quod.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
883	1	1	\N	A-023-003	23	3	2283.69	con_estres	produccion_madura	\N	\N	0101000020E6100000F2D1E118FF3F53C08068114AB2FF27C0	1224.00	2022-03-07	2025-03-27	Bacon	\N	\N	f	\N	351.39	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
884	1	1	\N	A-023-004	23	4	1014.88	con_estres	vivero	\N	\N	0101000020E61000000EBB52A5FE3F53C08068114AB2FF27C0	\N	2023-10-15	\N	Zutano	\N	\N	f	\N	236.37	5	Delectus qui vitae eligendi excepturi recusandae enim.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
885	1	1	\N	A-023-005	23	5	1172.39	muerto	desarrollo_inmaduro	2026-02-03	\N	0101000020E6100000E3A3C331FE3F53C08068114AB2FF27C0	2112.43	2024-09-02	\N	Hass	\N	\N	f	\N	131.27	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
886	1	1	\N	A-023-006	23	6	1436.24	con_estres	produccion_madura	\N	\N	0101000020E6100000FF8C34BEFD3F53C08068114AB2FF27C0	2339.15	2021-12-13	2022-12-12	Ettinger	\N	\N	f	\N	183.19	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
887	1	1	\N	A-023-007	23	7	1620.07	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000D575A54AFD3F53C08068114AB2FF27C0	1925.77	2022-02-25	2023-04-30	Fuerte	\N	\N	f	\N	68.30	7	Earum delectus ut eos ipsam omnis.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
888	1	1	\N	A-023-008	23	8	1378.42	erradicado	desarrollo_inmaduro	2025-09-26	\N	0101000020E6100000F15E16D7FC3F53C08068114AB2FF27C0	1151.84	2025-12-06	2026-08-19	Fuerte	\N	\N	f	\N	157.91	3	Voluptatem accusamus vel ut atque.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
889	1	1	\N	A-023-009	23	9	2137.30	con_estres	desarrollo_inmaduro	\N	\N	0101000020E61000000D488763FC3F53C08068114AB2FF27C0	2023.07	2024-12-20	2025-04-07	Zutano	\N	\N	f	\N	218.70	2	Nobis qui veritatis adipisci cumque accusantium.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
890	1	1	\N	A-023-010	23	10	1409.19	con_estres	vivero	\N	\N	0101000020E6100000E330F8EFFB3F53C08068114AB2FF27C0	1097.73	2025-02-20	2025-11-17	Bacon	\N	\N	f	\N	176.49	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
891	1	1	\N	A-023-011	23	11	2463.55	erradicado	establecimiento	2025-12-01	\N	0101000020E6100000FF19697CFB3F53C08068114AB2FF27C0	\N	2023-11-23	2024-04-15	Ettinger	\N	\N	f	\N	58.47	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
892	1	1	\N	A-023-012	23	12	2309.64	erradicado	vivero	2026-02-03	\N	0101000020E6100000D502DA08FB3F53C08068114AB2FF27C0	854.51	2023-08-06	2025-03-08	Ettinger	\N	\N	f	\N	244.67	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
893	1	1	\N	A-023-013	23	13	1351.37	erradicado	produccion_madura	2026-05-02	\N	0101000020E6100000F1EB4A95FA3F53C08068114AB2FF27C0	1958.73	2021-10-27	2024-07-07	Fuerte	\N	\N	f	\N	120.01	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
894	1	1	\N	A-023-014	23	14	1504.80	erradicado	establecimiento	2026-01-12	\N	0101000020E6100000C6D4BB21FA3F53C08068114AB2FF27C0	1224.85	2023-10-08	2024-08-20	Bacon	\N	\N	f	\N	339.25	3	Ea nihil maiores odio voluptas.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
895	1	1	\N	A-023-015	23	15	1314.90	muerto	produccion_madura	2026-05-20	\N	0101000020E6100000E2BD2CAEF93F53C08068114AB2FF27C0	\N	2022-10-20	\N	Hass	\N	\N	f	\N	139.96	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
896	1	1	\N	A-023-016	23	16	1387.78	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000FEA69D3AF93F53C08068114AB2FF27C0	2070.18	2026-01-08	2026-05-25	Ettinger	\N	\N	f	\N	169.50	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
897	1	1	\N	A-023-017	23	17	1562.99	enfermo_critico	establecimiento	\N	\N	0101000020E6100000D48F0EC7F83F53C08068114AB2FF27C0	\N	2023-02-23	2023-08-14	Ettinger	\N	\N	f	\N	411.70	9	Molestiae est rerum et facilis sit libero natus.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
898	1	1	\N	A-023-018	23	18	2118.67	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000F0787F53F83F53C08068114AB2FF27C0	\N	2026-05-20	2026-07-09	Zutano	\N	\N	f	\N	149.22	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
899	1	1	\N	A-023-019	23	19	1606.42	excelente	senescencia	\N	\N	0101000020E6100000C661F0DFF73F53C08068114AB2FF27C0	1760.02	2024-07-24	2025-03-31	Bacon	\N	\N	t	\N	147.97	2	Doloribus ea reiciendis quidem facilis nam dolor optio.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
900	1	1	\N	A-023-020	23	20	1353.61	con_estres	vivero	\N	\N	0101000020E6100000E24A616CF73F53C08068114AB2FF27C0	2080.23	2025-05-29	\N	Ettinger	\N	\N	f	\N	14.69	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
901	1	1	\N	A-023-021	23	21	2309.55	excelente	senescencia	\N	\N	0101000020E6100000B733D2F8F63F53C08068114AB2FF27C0	\N	2022-09-29	2024-03-25	Zutano	\N	\N	f	\N	493.99	9	Ipsum in expedita voluptate nemo et molestias voluptatem dolorum.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
902	1	1	\N	A-023-022	23	22	2155.72	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000D31C4385F63F53C08068114AB2FF27C0	2157.56	2026-03-08	\N	Ettinger	\N	\N	f	\N	268.36	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
903	1	1	\N	A-023-023	23	23	1718.03	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000EF05B411F63F53C08068114AB2FF27C0	1829.01	2022-10-26	\N	Zutano	\N	\N	f	\N	477.70	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
904	1	1	\N	A-023-024	23	24	857.59	con_estres	produccion_madura	\N	\N	0101000020E6100000C5EE249EF53F53C08068114AB2FF27C0	\N	2023-05-18	2026-05-05	Hass	\N	\N	f	\N	475.89	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
905	1	1	\N	A-023-025	23	25	896.44	enfermo_critico	senescencia	\N	\N	0101000020E6100000E1D7952AF53F53C08068114AB2FF27C0	1352.27	2024-05-26	2025-10-12	Bacon	\N	\N	f	\N	158.42	3	Earum possimus consequuntur facere nesciunt ut rerum eos.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
906	1	1	\N	A-023-026	23	26	2060.81	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000B7C006B7F43F53C08068114AB2FF27C0	1153.05	2026-01-16	\N	Fuerte	\N	\N	f	\N	306.84	9	Similique sed nobis omnis iste.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
907	1	1	\N	A-023-027	23	27	897.78	muerto	desarrollo_inmaduro	2025-11-01	\N	0101000020E6100000D3A97743F43F53C08068114AB2FF27C0	2378.56	2023-05-14	2025-07-19	Fuerte	\N	\N	f	\N	465.03	5	Tempora et voluptate delectus sit suscipit modi nisi excepturi.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
908	1	1	\N	A-023-028	23	28	969.86	excelente	senescencia	\N	\N	0101000020E6100000A892E8CFF33F53C08068114AB2FF27C0	803.16	2022-09-13	2026-06-21	Fuerte	\N	\N	f	\N	210.30	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
909	1	1	\N	A-023-029	23	29	1833.79	enfermo_critico	senescencia	\N	\N	0101000020E6100000C47B595CF33F53C08068114AB2FF27C0	\N	2022-01-19	2023-02-22	Zutano	\N	\N	f	\N	160.06	1	Earum dolor quae est sequi.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
910	1	1	\N	A-023-030	23	30	2284.88	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000E164CAE8F23F53C08068114AB2FF27C0	1262.79	2025-05-12	2025-09-28	Bacon	\N	\N	f	\N	80.20	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
911	1	1	\N	A-023-031	23	31	1698.78	con_estres	produccion_madura	\N	\N	0101000020E6100000B64D3B75F23F53C08068114AB2FF27C0	2316.87	2025-06-09	2026-03-29	Ettinger	\N	\N	f	\N	173.88	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
912	1	1	\N	A-023-032	23	32	2000.54	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000D236AC01F23F53C08068114AB2FF27C0	2038.06	2026-05-24	2026-06-17	Bacon	\N	\N	f	\N	241.65	4	Est nihil totam blanditiis consectetur.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
913	1	1	\N	A-023-033	23	33	1772.78	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000A81F1D8EF13F53C08068114AB2FF27C0	2418.04	2025-01-03	2026-03-10	Ettinger	\N	\N	f	\N	232.02	2	Debitis est voluptatem vel tempora quisquam voluptate voluptatibus.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
914	1	1	\N	A-023-034	23	34	1374.51	excelente	vivero	\N	\N	0101000020E6100000C4088E1AF13F53C08068114AB2FF27C0	971.89	2023-07-24	2024-09-21	Hass	\N	\N	f	\N	87.03	10	Consequatur omnis at aperiam hic ex sequi.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
915	1	1	\N	A-023-035	23	35	1037.76	excelente	produccion_madura	\N	\N	0101000020E61000009AF1FEA6F03F53C08068114AB2FF27C0	887.73	2022-11-02	2023-06-14	Hass	\N	\N	f	\N	34.59	4	Maiores at deserunt eos quia deleniti eum.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
916	1	1	\N	A-023-036	23	36	1122.87	muerto	produccion_madura	2025-10-07	\N	0101000020E6100000B6DA6F33F03F53C08068114AB2FF27C0	\N	2023-10-03	2024-04-16	Zutano	\N	\N	f	\N	112.62	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
917	1	1	\N	A-023-037	23	37	1211.85	muerto	desarrollo_inmaduro	2025-10-30	\N	0101000020E6100000D2C3E0BFEF3F53C08068114AB2FF27C0	1104.95	2021-12-06	2022-05-05	Hass	\N	\N	f	\N	458.34	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
918	1	1	\N	A-023-038	23	38	1365.26	erradicado	senescencia	2026-03-07	\N	0101000020E6100000A7AC514CEF3F53C08068114AB2FF27C0	1324.76	2021-09-29	2026-02-21	Ettinger	\N	\N	f	\N	13.32	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
919	1	1	\N	A-023-039	23	39	1580.78	muerto	vivero	2025-11-28	\N	0101000020E6100000C395C2D8EE3F53C08068114AB2FF27C0	2250.40	2024-06-05	2024-10-24	Hass	\N	\N	f	\N	61.66	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
920	1	1	\N	A-023-040	23	40	1327.98	enfermo_critico	senescencia	\N	\N	0101000020E6100000997E3365EE3F53C08068114AB2FF27C0	\N	2026-03-12	2026-06-01	Ettinger	\N	\N	f	\N	395.48	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
921	1	1	\N	A-024-001	24	1	1213.45	excelente	vivero	\N	\N	0101000020E610000000000000004053C0EA5FCCC1AEFF27C0	2102.50	2025-01-25	2025-11-18	Bacon	\N	\N	f	\N	320.72	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
922	1	1	\N	A-024-002	24	2	2372.97	con_estres	establecimiento	\N	\N	0101000020E61000001CE9708CFF3F53C0EA5FCCC1AEFF27C0	\N	2025-01-29	2025-02-22	Fuerte	\N	\N	f	\N	244.85	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
923	1	1	\N	A-024-003	24	3	932.81	erradicado	produccion_madura	2026-01-04	\N	0101000020E6100000F2D1E118FF3F53C0EA5FCCC1AEFF27C0	\N	2022-06-22	2023-04-13	Fuerte	\N	\N	f	\N	473.81	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
924	1	1	\N	A-024-004	24	4	861.01	con_estres	senescencia	\N	\N	0101000020E61000000EBB52A5FE3F53C0EA5FCCC1AEFF27C0	1070.36	2023-03-09	2024-07-28	Ettinger	\N	\N	f	\N	304.75	5	Eum fuga consequatur quia nihil.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
925	1	1	\N	A-024-005	24	5	1669.88	erradicado	produccion_madura	2026-03-12	\N	0101000020E6100000E3A3C331FE3F53C0EA5FCCC1AEFF27C0	\N	2022-03-06	\N	Bacon	\N	\N	f	\N	71.19	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
926	1	1	\N	A-024-006	24	6	1609.19	muerto	desarrollo_inmaduro	2025-09-27	\N	0101000020E6100000FF8C34BEFD3F53C0EA5FCCC1AEFF27C0	1073.06	2025-02-28	2025-06-08	Zutano	\N	\N	f	\N	203.80	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
927	1	1	\N	A-024-007	24	7	1686.50	muerto	vivero	2026-03-17	\N	0101000020E6100000D575A54AFD3F53C0EA5FCCC1AEFF27C0	809.27	2026-02-24	2026-06-24	Fuerte	\N	\N	f	\N	333.48	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
928	1	1	\N	A-024-008	24	8	1083.07	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000F15E16D7FC3F53C0EA5FCCC1AEFF27C0	1583.58	2022-07-15	2025-05-28	Fuerte	\N	\N	f	\N	475.72	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
929	1	1	\N	A-024-009	24	9	2080.66	enfermo_critico	senescencia	\N	\N	0101000020E61000000D488763FC3F53C0EA5FCCC1AEFF27C0	1193.53	2025-06-02	\N	Zutano	\N	\N	f	\N	44.57	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
930	1	1	\N	A-024-010	24	10	885.90	erradicado	vivero	2026-04-26	\N	0101000020E6100000E330F8EFFB3F53C0EA5FCCC1AEFF27C0	990.01	2026-02-05	\N	Hass	\N	\N	f	\N	254.10	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
931	1	1	\N	A-024-011	24	11	1540.18	excelente	produccion_madura	\N	\N	0101000020E6100000FF19697CFB3F53C0EA5FCCC1AEFF27C0	\N	2022-08-07	2022-08-22	Bacon	\N	\N	f	\N	417.79	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
932	1	1	\N	A-024-012	24	12	1716.10	excelente	senescencia	\N	\N	0101000020E6100000D502DA08FB3F53C0EA5FCCC1AEFF27C0	2249.83	2025-04-05	\N	Fuerte	\N	\N	t	\N	29.93	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
933	1	1	\N	A-024-013	24	13	1873.93	muerto	establecimiento	2025-11-22	\N	0101000020E6100000F1EB4A95FA3F53C0EA5FCCC1AEFF27C0	1012.44	2025-10-02	2026-06-28	Hass	\N	\N	f	\N	26.15	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
934	1	1	\N	A-024-014	24	14	2480.01	erradicado	vivero	2026-03-28	\N	0101000020E6100000C6D4BB21FA3F53C0EA5FCCC1AEFF27C0	1178.08	2022-07-08	2026-01-11	Hass	\N	\N	f	\N	256.88	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
935	1	1	\N	A-024-015	24	15	1822.32	muerto	vivero	2025-10-15	\N	0101000020E6100000E2BD2CAEF93F53C0EA5FCCC1AEFF27C0	\N	2022-08-25	2023-10-30	Fuerte	\N	\N	f	\N	392.04	7	Tempore dolor et ipsum nihil qui.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
936	1	1	\N	A-024-016	24	16	2287.02	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000FEA69D3AF93F53C0EA5FCCC1AEFF27C0	\N	2025-02-27	2025-05-11	Hass	\N	\N	f	\N	188.25	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
937	1	1	\N	A-024-017	24	17	2473.13	muerto	desarrollo_inmaduro	2026-04-16	\N	0101000020E6100000D48F0EC7F83F53C0EA5FCCC1AEFF27C0	1187.77	2024-06-05	2025-06-06	Zutano	\N	\N	f	\N	291.59	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
938	1	1	\N	A-024-018	24	18	1761.52	enfermo_critico	senescencia	\N	\N	0101000020E6100000F0787F53F83F53C0EA5FCCC1AEFF27C0	2476.98	2025-06-14	2026-01-31	Bacon	\N	\N	f	\N	446.24	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
939	1	1	\N	A-024-019	24	19	2335.55	excelente	establecimiento	\N	\N	0101000020E6100000C661F0DFF73F53C0EA5FCCC1AEFF27C0	1466.70	2024-03-10	2024-09-29	Fuerte	\N	\N	f	\N	87.75	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
940	1	1	\N	A-024-020	24	20	1961.53	erradicado	desarrollo_inmaduro	2026-06-02	\N	0101000020E6100000E24A616CF73F53C0EA5FCCC1AEFF27C0	2076.34	2023-05-17	\N	Bacon	\N	\N	f	\N	356.37	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
941	1	1	\N	A-024-021	24	21	2308.82	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000B733D2F8F63F53C0EA5FCCC1AEFF27C0	2189.45	2024-09-01	2024-10-21	Ettinger	\N	\N	f	\N	65.50	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
942	1	1	\N	A-024-022	24	22	1072.77	con_estres	vivero	\N	\N	0101000020E6100000D31C4385F63F53C0EA5FCCC1AEFF27C0	1651.75	2022-03-08	2022-05-04	Hass	\N	\N	f	\N	238.29	3	Ab blanditiis nulla et labore cumque non beatae.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
943	1	1	\N	A-024-023	24	23	2369.33	excelente	vivero	\N	\N	0101000020E6100000EF05B411F63F53C0EA5FCCC1AEFF27C0	\N	2023-08-16	\N	Fuerte	\N	\N	f	\N	232.68	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
944	1	1	\N	A-024-024	24	24	1824.86	con_estres	senescencia	\N	\N	0101000020E6100000C5EE249EF53F53C0EA5FCCC1AEFF27C0	\N	2023-10-27	2026-02-11	Fuerte	\N	\N	f	\N	430.78	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
945	1	1	\N	A-024-025	24	25	1133.29	enfermo_critico	senescencia	\N	\N	0101000020E6100000E1D7952AF53F53C0EA5FCCC1AEFF27C0	\N	2026-04-13	\N	Fuerte	\N	\N	f	\N	95.49	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
946	1	1	\N	A-024-026	24	26	1679.43	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000B7C006B7F43F53C0EA5FCCC1AEFF27C0	2247.28	2022-10-04	2024-11-25	Hass	\N	\N	t	\N	230.37	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
947	1	1	\N	A-024-027	24	27	2203.40	erradicado	produccion_madura	2025-10-20	\N	0101000020E6100000D3A97743F43F53C0EA5FCCC1AEFF27C0	1080.15	2022-11-30	2023-01-02	Fuerte	\N	\N	f	\N	163.85	0	Quo tenetur quo aut inventore neque quidem.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
948	1	1	\N	A-024-028	24	28	2181.20	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000A892E8CFF33F53C0EA5FCCC1AEFF27C0	836.14	2025-11-07	2025-11-16	Ettinger	\N	\N	f	\N	352.87	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
949	1	1	\N	A-024-029	24	29	928.72	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000C47B595CF33F53C0EA5FCCC1AEFF27C0	2275.35	2024-10-29	2025-07-25	Hass	\N	\N	f	\N	375.89	0	A fugiat similique ipsa.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
950	1	1	\N	A-024-030	24	30	2078.87	erradicado	vivero	2026-02-10	\N	0101000020E6100000E164CAE8F23F53C0EA5FCCC1AEFF27C0	\N	2025-07-07	2026-06-10	Ettinger	\N	\N	f	\N	108.95	2	Omnis nihil optio nam et culpa magnam.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
951	1	1	\N	A-024-031	24	31	1228.13	con_estres	establecimiento	\N	\N	0101000020E6100000B64D3B75F23F53C0EA5FCCC1AEFF27C0	1202.64	2026-03-12	2026-03-30	Bacon	\N	\N	f	\N	405.01	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
952	1	1	\N	A-024-032	24	32	1197.17	excelente	produccion_madura	\N	\N	0101000020E6100000D236AC01F23F53C0EA5FCCC1AEFF27C0	\N	2025-09-05	2026-05-21	Fuerte	\N	\N	f	\N	189.23	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
953	1	1	\N	A-024-033	24	33	1802.07	enfermo_critico	desarrollo_inmaduro	\N	\N	0101000020E6100000A81F1D8EF13F53C0EA5FCCC1AEFF27C0	1097.01	2022-04-30	2023-07-18	Hass	\N	\N	f	\N	419.74	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
954	1	1	\N	A-024-034	24	34	1463.18	erradicado	senescencia	2026-04-30	\N	0101000020E6100000C4088E1AF13F53C0EA5FCCC1AEFF27C0	982.36	2023-11-19	2025-10-31	Zutano	\N	\N	f	\N	299.72	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
955	1	1	\N	A-024-035	24	35	901.92	con_estres	desarrollo_inmaduro	\N	\N	0101000020E61000009AF1FEA6F03F53C0EA5FCCC1AEFF27C0	2055.34	2023-10-09	2024-09-22	Fuerte	\N	\N	f	\N	447.04	1	Autem et deserunt optio qui.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
956	1	1	\N	A-024-036	24	36	2005.53	erradicado	produccion_madura	2025-09-19	\N	0101000020E6100000B6DA6F33F03F53C0EA5FCCC1AEFF27C0	1079.47	2023-06-14	\N	Fuerte	\N	\N	f	\N	475.41	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
957	1	1	\N	A-024-037	24	37	1298.81	con_estres	senescencia	\N	\N	0101000020E6100000D2C3E0BFEF3F53C0EA5FCCC1AEFF27C0	\N	2022-07-23	2025-09-28	Fuerte	\N	\N	f	\N	332.80	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
958	1	1	\N	A-024-038	24	38	1271.15	excelente	establecimiento	\N	\N	0101000020E6100000A7AC514CEF3F53C0EA5FCCC1AEFF27C0	1210.93	2022-12-21	2023-11-11	Hass	\N	\N	f	\N	359.18	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
959	1	1	\N	A-024-039	24	39	2102.73	con_estres	senescencia	\N	\N	0101000020E6100000C395C2D8EE3F53C0EA5FCCC1AEFF27C0	1121.05	2024-06-18	2026-04-07	Zutano	\N	\N	f	\N	401.70	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
960	1	1	\N	A-024-040	24	40	2161.44	enfermo_critico	vivero	\N	\N	0101000020E6100000997E3365EE3F53C0EA5FCCC1AEFF27C0	1842.03	2025-05-04	2025-05-10	Bacon	\N	\N	f	\N	355.15	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
961	1	1	\N	A-025-001	25	1	1025.43	con_estres	senescencia	\N	\N	0101000020E610000000000000004053C087598739ABFF27C0	2367.54	2025-03-14	2026-01-19	Zutano	\N	\N	f	\N	430.12	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
962	1	1	\N	A-025-002	25	2	894.54	con_estres	produccion_madura	\N	\N	0101000020E61000001CE9708CFF3F53C087598739ABFF27C0	1964.75	2025-10-03	\N	Bacon	\N	\N	f	\N	86.15	4	Vitae aliquid explicabo harum.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
963	1	1	\N	A-025-003	25	3	1922.75	erradicado	establecimiento	2026-04-20	\N	0101000020E6100000F2D1E118FF3F53C087598739ABFF27C0	\N	2024-06-05	\N	Ettinger	\N	\N	f	\N	44.53	7	Dolor voluptates sed ratione ea et cumque.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
964	1	1	\N	A-025-004	25	4	1359.78	excelente	establecimiento	\N	\N	0101000020E61000000EBB52A5FE3F53C087598739ABFF27C0	2296.46	2024-12-10	2025-07-29	Ettinger	\N	\N	t	\N	42.20	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
965	1	1	\N	A-025-005	25	5	2222.96	erradicado	establecimiento	2026-03-07	\N	0101000020E6100000E3A3C331FE3F53C087598739ABFF27C0	2416.11	2026-01-16	\N	Bacon	\N	\N	f	\N	394.53	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
966	1	1	\N	A-025-006	25	6	1810.18	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000FF8C34BEFD3F53C087598739ABFF27C0	1646.58	2023-01-02	\N	Ettinger	\N	\N	f	\N	63.39	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
967	1	1	\N	A-025-007	25	7	1559.22	con_estres	senescencia	\N	\N	0101000020E6100000D575A54AFD3F53C087598739ABFF27C0	2297.86	2024-04-11	2024-06-03	Zutano	\N	\N	f	\N	484.02	1	Odit nostrum nobis consectetur nemo sit natus.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
968	1	1	\N	A-025-008	25	8	1095.70	erradicado	produccion_madura	2026-04-08	\N	0101000020E6100000F15E16D7FC3F53C087598739ABFF27C0	1896.97	2024-01-30	\N	Bacon	\N	\N	f	\N	182.04	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
969	1	1	\N	A-025-009	25	9	1107.45	enfermo_critico	vivero	\N	\N	0101000020E61000000D488763FC3F53C087598739ABFF27C0	\N	2022-03-25	\N	Zutano	\N	\N	f	\N	442.69	8	Molestiae debitis qui possimus in repellendus aut eum.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
970	1	1	\N	A-025-010	25	10	2456.91	excelente	vivero	\N	\N	0101000020E6100000E330F8EFFB3F53C087598739ABFF27C0	1807.08	2025-10-05	2025-10-27	Bacon	\N	\N	f	\N	67.89	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
971	1	1	\N	A-025-011	25	11	1756.66	erradicado	senescencia	2025-12-08	\N	0101000020E6100000FF19697CFB3F53C087598739ABFF27C0	1300.22	2022-05-07	2024-12-07	Zutano	\N	\N	f	\N	400.76	4	Sit vero laborum quam veritatis autem ab sunt.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
972	1	1	\N	A-025-012	25	12	2318.15	muerto	produccion_madura	2026-01-28	\N	0101000020E6100000D502DA08FB3F53C087598739ABFF27C0	2302.82	2024-07-25	\N	Hass	\N	\N	t	\N	182.57	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
973	1	1	\N	A-025-013	25	13	1016.88	excelente	establecimiento	\N	\N	0101000020E6100000F1EB4A95FA3F53C087598739ABFF27C0	\N	2022-08-14	2024-05-18	Fuerte	\N	\N	f	\N	127.63	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
974	1	1	\N	A-025-014	25	14	1568.86	muerto	produccion_madura	2025-10-27	\N	0101000020E6100000C6D4BB21FA3F53C087598739ABFF27C0	1229.65	2022-09-09	2025-01-24	Zutano	\N	\N	f	\N	446.25	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
975	1	1	\N	A-025-015	25	15	1907.67	erradicado	produccion_madura	2026-01-21	\N	0101000020E6100000E2BD2CAEF93F53C087598739ABFF27C0	1108.05	2026-05-05	\N	Ettinger	\N	\N	f	\N	287.53	3	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
976	1	1	\N	A-025-016	25	16	1170.13	erradicado	senescencia	2026-08-02	\N	0101000020E6100000FEA69D3AF93F53C087598739ABFF27C0	1671.59	2024-10-23	2025-11-22	Zutano	\N	\N	f	\N	415.23	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
977	1	1	\N	A-025-017	25	17	840.46	excelente	senescencia	\N	\N	0101000020E6100000D48F0EC7F83F53C087598739ABFF27C0	813.40	2024-05-22	2026-01-20	Hass	\N	\N	f	\N	55.15	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
978	1	1	\N	A-025-018	25	18	1715.04	excelente	senescencia	\N	\N	0101000020E6100000F0787F53F83F53C087598739ABFF27C0	871.92	2022-11-21	2024-08-17	Bacon	\N	\N	t	\N	258.23	4	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
979	1	1	\N	A-025-019	25	19	2130.89	excelente	senescencia	\N	\N	0101000020E6100000C661F0DFF73F53C087598739ABFF27C0	2225.10	2021-11-09	2025-07-16	Ettinger	\N	\N	t	\N	281.62	2	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
980	1	1	\N	A-025-020	25	20	2434.39	muerto	vivero	2026-06-25	\N	0101000020E6100000E24A616CF73F53C087598739ABFF27C0	1226.20	2024-12-26	2025-09-15	Fuerte	\N	\N	f	\N	23.16	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
981	1	1	\N	A-025-021	25	21	2194.91	con_estres	desarrollo_inmaduro	\N	\N	0101000020E6100000B733D2F8F63F53C087598739ABFF27C0	1800.86	2023-02-23	2023-11-04	Zutano	\N	\N	f	\N	17.02	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
982	1	1	\N	A-025-022	25	22	1110.77	erradicado	desarrollo_inmaduro	2025-10-18	\N	0101000020E6100000D31C4385F63F53C087598739ABFF27C0	\N	2024-10-10	\N	Zutano	\N	\N	f	\N	95.79	3	Laudantium voluptatem modi libero et.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
983	1	1	\N	A-025-023	25	23	1063.08	excelente	vivero	\N	\N	0101000020E6100000EF05B411F63F53C087598739ABFF27C0	2243.97	2023-07-11	2026-02-05	Ettinger	\N	\N	f	\N	406.28	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
984	1	1	\N	A-025-024	25	24	1915.77	excelente	senescencia	\N	\N	0101000020E6100000C5EE249EF53F53C087598739ABFF27C0	1332.23	2024-06-15	2026-07-06	Ettinger	\N	\N	f	\N	203.64	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
985	1	1	\N	A-025-025	25	25	2111.18	excelente	desarrollo_inmaduro	\N	\N	0101000020E6100000E1D7952AF53F53C087598739ABFF27C0	1114.94	2022-08-29	\N	Hass	\N	\N	f	\N	246.07	9	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
986	1	1	\N	A-025-026	25	26	1854.51	con_estres	vivero	\N	\N	0101000020E6100000B7C006B7F43F53C087598739ABFF27C0	1915.09	2023-08-29	\N	Ettinger	\N	\N	f	\N	476.96	1	Sit provident tenetur magni est molestiae quos.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
987	1	1	\N	A-025-027	25	27	1790.52	erradicado	desarrollo_inmaduro	2025-12-15	\N	0101000020E6100000D3A97743F43F53C087598739ABFF27C0	938.46	2025-05-18	2026-01-23	Bacon	\N	\N	f	\N	266.61	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
988	1	1	\N	A-025-028	25	28	2258.85	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000A892E8CFF33F53C087598739ABFF27C0	1909.99	2025-10-03	\N	Bacon	\N	\N	f	\N	142.00	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
989	1	1	\N	A-025-029	25	29	977.26	muerto	establecimiento	2026-06-20	\N	0101000020E6100000C47B595CF33F53C087598739ABFF27C0	825.47	2023-05-17	2024-09-09	Zutano	\N	\N	f	\N	438.81	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
990	1	1	\N	A-025-030	25	30	1833.27	muerto	senescencia	2026-05-15	\N	0101000020E6100000E164CAE8F23F53C087598739ABFF27C0	966.24	2022-07-20	2026-04-17	Ettinger	\N	\N	f	\N	471.83	10	Qui fuga occaecati et occaecati.	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
991	1	1	\N	A-025-031	25	31	1354.02	enfermo_critico	produccion_madura	\N	\N	0101000020E6100000B64D3B75F23F53C087598739ABFF27C0	\N	2023-05-11	\N	Fuerte	\N	\N	f	\N	264.17	1	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
992	1	1	\N	A-025-032	25	32	2473.92	con_estres	senescencia	\N	\N	0101000020E6100000D236AC01F23F53C087598739ABFF27C0	1098.98	2023-04-15	2023-05-24	Zutano	\N	\N	f	\N	139.44	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
993	1	1	\N	A-025-033	25	33	1849.38	excelente	vivero	\N	\N	0101000020E6100000A81F1D8EF13F53C087598739ABFF27C0	2435.57	2025-10-03	2026-02-09	Fuerte	\N	\N	f	\N	384.03	10	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
994	1	1	\N	A-025-034	25	34	930.72	muerto	vivero	2026-06-26	\N	0101000020E6100000C4088E1AF13F53C087598739ABFF27C0	\N	2024-07-05	2025-09-30	Ettinger	\N	\N	f	\N	171.09	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
995	1	1	\N	A-025-035	25	35	2253.18	excelente	establecimiento	\N	\N	0101000020E61000009AF1FEA6F03F53C087598739ABFF27C0	\N	2022-10-08	2023-01-05	Fuerte	\N	\N	f	\N	222.23	8	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
996	1	1	\N	A-025-036	25	36	1772.76	muerto	senescencia	2026-08-05	\N	0101000020E6100000B6DA6F33F03F53C087598739ABFF27C0	\N	2023-10-13	2025-04-13	Fuerte	\N	\N	f	\N	33.79	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
997	1	1	\N	A-025-037	25	37	2093.07	con_estres	produccion_madura	\N	\N	0101000020E6100000D2C3E0BFEF3F53C087598739ABFF27C0	1338.26	2025-04-30	\N	Bacon	\N	\N	f	\N	176.66	7	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
998	1	1	\N	A-025-038	25	38	2155.13	con_estres	produccion_madura	\N	\N	0101000020E6100000A7AC514CEF3F53C087598739ABFF27C0	2155.53	2022-06-03	2024-09-13	Bacon	\N	\N	f	\N	40.87	5	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
999	1	1	\N	A-025-039	25	39	2021.22	enfermo_critico	senescencia	\N	\N	0101000020E6100000C395C2D8EE3F53C087598739ABFF27C0	2087.87	2023-09-02	2024-02-24	Fuerte	\N	\N	f	\N	285.61	0	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
1000	1	1	\N	A-025-040	25	40	1699.15	muerto	vivero	2025-10-31	\N	0101000020E6100000997E3365EE3F53C087598739ABFF27C0	1330.10	2022-09-02	\N	Fuerte	\N	\N	f	\N	447.34	6	\N	\N	2026-08-26 18:02:07	2026-08-26 18:02:07
\.


--
-- Data for Name: arboles_historial_fitosanitario; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.arboles_historial_fitosanitario (id, arbol_id, fecha_hallazgo, tipo_incidencia, agente_patogeno_nombre, severidad_afectacion, descripcion_sintomas, evidencia_fotografica_url, usuario_evaluador_id, requiere_intervencion_quimica, caso_controlado, fecha_resolucion, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: arboles_metricas_historicas; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.arboles_metricas_historicas (id, arbol_id, fecha_medicion, altura_metros, diametro_tronco_cm, diametro_copa_proyeccion_m, volumen_copa_calculado_m3, indice_ndvi_medido, indice_ndre_medido, temperatura_canopia_celsius, codigo_escala_bbch, origen_datos, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: arboles_red_vecindad; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.arboles_red_vecindad (id, arbol_origen_id, arbol_destino_id, distancia_metros, probabilidad_contagio_base, tipo_contacto, created_at, updated_at) FROM stdin;
1	1	2	3.00	0.1478	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
2	1	41	3.00	0.0677	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
3	1	42	4.24	0.1171	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
4	2	3	3.00	0.0290	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
5	2	42	3.00	0.1595	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
6	2	43	4.24	0.1635	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
7	2	41	4.24	0.0143	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
8	3	4	3.00	0.1178	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
9	3	43	3.00	0.0378	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
10	3	44	4.24	0.0719	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
11	3	42	4.24	0.1083	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
12	4	5	3.00	0.1310	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
13	4	44	3.00	0.0830	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
14	4	45	4.24	0.1106	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
15	4	43	4.24	0.0493	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
16	5	6	3.00	0.1812	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
17	5	45	3.00	0.1096	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
18	5	46	4.24	0.1042	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
19	5	44	4.24	0.0123	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
20	6	7	3.00	0.0139	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
21	6	46	3.00	0.0988	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
22	6	47	4.24	0.1410	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
23	6	45	4.24	0.1210	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
24	7	8	3.00	0.1381	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
25	7	47	3.00	0.0175	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
26	7	48	4.24	0.0150	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
27	7	46	4.24	0.1711	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
28	8	9	3.00	0.0317	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
29	8	48	3.00	0.1756	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
30	8	49	4.24	0.0532	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
31	8	47	4.24	0.1917	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
32	9	10	3.00	0.0416	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
33	9	49	3.00	0.1877	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
34	9	50	4.24	0.0363	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
35	9	48	4.24	0.1108	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
36	10	11	3.00	0.0825	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
37	10	50	3.00	0.1174	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
38	10	51	4.24	0.0878	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
39	10	49	4.24	0.1397	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
40	11	12	3.00	0.0692	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
41	11	51	3.00	0.0994	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
42	11	52	4.24	0.1033	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
43	11	50	4.24	0.0112	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
44	12	13	3.00	0.1131	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
45	12	52	3.00	0.1828	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
46	12	53	4.24	0.1398	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
47	12	51	4.24	0.1196	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
48	13	14	3.00	0.1142	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
49	13	53	3.00	0.0467	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
50	13	54	4.24	0.0161	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
51	13	52	4.24	0.1792	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
52	14	15	3.00	0.0972	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
53	14	54	3.00	0.0627	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
54	14	55	4.24	0.1778	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
55	14	53	4.24	0.0309	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
56	15	16	3.00	0.1045	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
57	15	55	3.00	0.1755	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
58	15	56	4.24	0.0333	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
59	15	54	4.24	0.0675	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
60	16	17	3.00	0.0754	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
61	16	56	3.00	0.1445	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
62	16	57	4.24	0.1288	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
63	16	55	4.24	0.1032	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
64	17	18	3.00	0.0258	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
65	17	57	3.00	0.0430	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
66	17	58	4.24	0.0632	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
67	17	56	4.24	0.0949	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
68	18	19	3.00	0.1242	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
69	18	58	3.00	0.1998	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
70	18	59	4.24	0.1648	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
71	18	57	4.24	0.1259	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
72	19	20	3.00	0.1906	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
73	19	59	3.00	0.1211	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
74	19	60	4.24	0.1250	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
75	19	58	4.24	0.0964	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
76	20	21	3.00	0.1258	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
77	20	60	3.00	0.0644	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
78	20	61	4.24	0.0654	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
79	20	59	4.24	0.0164	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
80	21	22	3.00	0.0716	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
81	21	61	3.00	0.0445	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
82	21	62	4.24	0.0241	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
83	21	60	4.24	0.0342	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
84	22	23	3.00	0.0178	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
85	22	62	3.00	0.1669	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
86	22	63	4.24	0.1551	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
87	22	61	4.24	0.1787	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
88	23	24	3.00	0.0128	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
89	23	63	3.00	0.0853	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
90	23	64	4.24	0.1659	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
91	23	62	4.24	0.1708	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
92	24	25	3.00	0.0550	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
93	24	64	3.00	0.0626	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
94	24	65	4.24	0.0220	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
95	24	63	4.24	0.1259	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
96	25	26	3.00	0.0705	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
97	25	65	3.00	0.1683	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
98	25	66	4.24	0.0513	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
99	25	64	4.24	0.1786	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
100	26	27	3.00	0.1443	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
101	26	66	3.00	0.1165	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
102	26	67	4.24	0.0561	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
103	26	65	4.24	0.1794	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
104	27	28	3.00	0.0769	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
105	27	67	3.00	0.1295	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
106	27	68	4.24	0.0989	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
107	27	66	4.24	0.1801	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
108	28	29	3.00	0.0124	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
109	28	68	3.00	0.0402	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
110	28	69	4.24	0.1439	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
111	28	67	4.24	0.0782	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
112	29	30	3.00	0.1064	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
113	29	69	3.00	0.1070	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
114	29	70	4.24	0.1073	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
115	29	68	4.24	0.1745	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
116	30	31	3.00	0.0973	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
117	30	70	3.00	0.1109	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
118	30	71	4.24	0.1258	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
119	30	69	4.24	0.1064	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
120	31	32	3.00	0.1031	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
121	31	71	3.00	0.0511	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
122	31	72	4.24	0.1803	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
123	31	70	4.24	0.1827	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
124	32	33	3.00	0.0924	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
125	32	72	3.00	0.1939	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
126	32	73	4.24	0.1170	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
127	32	71	4.24	0.1199	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
128	33	34	3.00	0.1286	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
129	33	73	3.00	0.1259	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
130	33	74	4.24	0.0475	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
131	33	72	4.24	0.1139	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
132	34	35	3.00	0.1169	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
133	34	74	3.00	0.1128	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
134	34	75	4.24	0.1273	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
135	34	73	4.24	0.1000	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
136	35	36	3.00	0.1043	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
137	35	75	3.00	0.0418	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
138	35	76	4.24	0.1651	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
139	35	74	4.24	0.0117	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
140	36	37	3.00	0.1215	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
141	36	76	3.00	0.0853	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
142	36	77	4.24	0.1626	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
143	36	75	4.24	0.0667	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
144	37	38	3.00	0.1190	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
145	37	77	3.00	0.1682	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
146	37	78	4.24	0.0878	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
147	37	76	4.24	0.1110	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
148	38	39	3.00	0.0976	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
149	38	78	3.00	0.1802	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
150	38	79	4.24	0.1233	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
151	38	77	4.24	0.1197	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
152	39	40	3.00	0.0974	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
153	39	79	3.00	0.0761	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
154	39	80	4.24	0.1711	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
155	39	78	4.24	0.0844	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
156	40	80	3.00	0.1953	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
157	40	79	4.24	0.1952	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
158	41	42	3.00	0.0215	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
159	41	81	3.00	0.1564	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
160	41	82	4.24	0.1338	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
161	42	43	3.00	0.1004	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
162	42	82	3.00	0.0477	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
163	42	83	4.24	0.0420	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
164	42	81	4.24	0.0308	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
165	43	44	3.00	0.0349	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
166	43	83	3.00	0.1860	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
167	43	84	4.24	0.0448	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
168	43	82	4.24	0.1527	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
169	44	45	3.00	0.1823	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
170	44	84	3.00	0.1526	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
171	44	85	4.24	0.1962	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
172	44	83	4.24	0.0544	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
173	45	46	3.00	0.1994	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
174	45	85	3.00	0.1075	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
175	45	86	4.24	0.1375	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
176	45	84	4.24	0.0764	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
177	46	47	3.00	0.1497	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
178	46	86	3.00	0.0528	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
179	46	87	4.24	0.1821	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
180	46	85	4.24	0.0941	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
181	47	48	3.00	0.1145	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
182	47	87	3.00	0.1568	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
183	47	88	4.24	0.0628	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
184	47	86	4.24	0.1959	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
185	48	49	3.00	0.0324	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
186	48	88	3.00	0.1650	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
187	48	89	4.24	0.0380	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
188	48	87	4.24	0.1822	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
189	49	50	3.00	0.0254	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
190	49	89	3.00	0.1299	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
191	49	90	4.24	0.1476	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
192	49	88	4.24	0.1078	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
193	50	51	3.00	0.1424	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
194	50	90	3.00	0.1288	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
195	50	91	4.24	0.0574	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
196	50	89	4.24	0.1866	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
197	51	52	3.00	0.0983	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
198	51	91	3.00	0.1552	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
199	51	92	4.24	0.1719	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
200	51	90	4.24	0.1618	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
201	52	53	3.00	0.0863	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
202	52	92	3.00	0.1348	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
203	52	93	4.24	0.0634	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
204	52	91	4.24	0.1719	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
205	53	54	3.00	0.0520	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
206	53	93	3.00	0.0429	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
207	53	94	4.24	0.1172	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
208	53	92	4.24	0.1821	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
209	54	55	3.00	0.0743	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
210	54	94	3.00	0.1569	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
211	54	95	4.24	0.1700	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
212	54	93	4.24	0.1700	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
213	55	56	3.00	0.0670	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
214	55	95	3.00	0.1656	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
215	55	96	4.24	0.0910	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
216	55	94	4.24	0.1166	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
217	56	57	3.00	0.0463	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
218	56	96	3.00	0.1309	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
219	56	97	4.24	0.1899	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
220	56	95	4.24	0.1656	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
221	57	58	3.00	0.0954	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
222	57	97	3.00	0.1101	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
223	57	98	4.24	0.0481	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
224	57	96	4.24	0.0238	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
225	58	59	3.00	0.0321	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
226	58	98	3.00	0.0304	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
227	58	99	4.24	0.0420	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
228	58	97	4.24	0.1354	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
229	59	60	3.00	0.1428	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
230	59	99	3.00	0.1480	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
231	59	100	4.24	0.0620	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
232	59	98	4.24	0.0735	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
233	60	61	3.00	0.0297	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
234	60	100	3.00	0.1677	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
235	60	101	4.24	0.1339	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
236	60	99	4.24	0.1120	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
237	61	62	3.00	0.1779	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
238	61	101	3.00	0.0485	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
239	61	102	4.24	0.0254	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
240	61	100	4.24	0.0968	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
241	62	63	3.00	0.1853	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
242	62	102	3.00	0.1186	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
243	62	103	4.24	0.1244	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
244	62	101	4.24	0.0219	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
245	63	64	3.00	0.0325	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
246	63	103	3.00	0.1267	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
247	63	104	4.24	0.1996	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
248	63	102	4.24	0.1740	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
249	64	65	3.00	0.0901	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
250	64	104	3.00	0.0875	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
251	64	105	4.24	0.1479	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
252	64	103	4.24	0.0635	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
253	65	66	3.00	0.1491	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
254	65	105	3.00	0.0410	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
255	65	106	4.24	0.0811	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
256	65	104	4.24	0.0160	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
257	66	67	3.00	0.1050	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
258	66	106	3.00	0.1420	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
259	66	107	4.24	0.0501	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
260	66	105	4.24	0.0230	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
261	67	68	3.00	0.0525	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
262	67	107	3.00	0.1670	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
263	67	108	4.24	0.1926	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
264	67	106	4.24	0.1722	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
265	68	69	3.00	0.0160	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
266	68	108	3.00	0.0896	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
267	68	109	4.24	0.0936	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
268	68	107	4.24	0.1055	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
269	69	70	3.00	0.0126	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
270	69	109	3.00	0.1914	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
271	69	110	4.24	0.1747	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
272	69	108	4.24	0.1717	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
273	70	71	3.00	0.1107	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
274	70	110	3.00	0.0443	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
275	70	111	4.24	0.0257	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
276	70	109	4.24	0.0592	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
277	71	72	3.00	0.0130	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
278	71	111	3.00	0.1838	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
279	71	112	4.24	0.0846	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
280	71	110	4.24	0.1261	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
281	72	73	3.00	0.0597	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
282	72	112	3.00	0.1837	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
283	72	113	4.24	0.0519	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
284	72	111	4.24	0.1223	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
285	73	74	3.00	0.0245	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
286	73	113	3.00	0.1760	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
287	73	114	4.24	0.1912	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
288	73	112	4.24	0.0421	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
289	74	75	3.00	0.1039	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
290	74	114	3.00	0.1072	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
291	74	115	4.24	0.1678	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
292	74	113	4.24	0.0427	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
293	75	76	3.00	0.1576	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
294	75	115	3.00	0.0546	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
295	75	116	4.24	0.0187	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
296	75	114	4.24	0.1863	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
297	76	77	3.00	0.1419	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
298	76	116	3.00	0.1144	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
299	76	117	4.24	0.1208	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
300	76	115	4.24	0.0955	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
301	77	78	3.00	0.0765	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
302	77	117	3.00	0.0609	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
303	77	118	4.24	0.0490	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
304	77	116	4.24	0.0592	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
305	78	79	3.00	0.1808	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
306	78	118	3.00	0.1285	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
307	78	119	4.24	0.0811	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
308	78	117	4.24	0.0533	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
309	79	80	3.00	0.0522	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
310	79	119	3.00	0.0362	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
311	79	120	4.24	0.0784	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
312	79	118	4.24	0.0146	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
313	80	120	3.00	0.0153	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
314	80	119	4.24	0.0532	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
315	81	82	3.00	0.0760	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
316	81	121	3.00	0.0909	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
317	81	122	4.24	0.1906	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
318	82	83	3.00	0.1562	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
319	82	122	3.00	0.0218	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
320	82	123	4.24	0.0639	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
321	82	121	4.24	0.0580	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
322	83	84	3.00	0.0553	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
323	83	123	3.00	0.0631	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
324	83	124	4.24	0.0729	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
325	83	122	4.24	0.0607	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
326	84	85	3.00	0.0284	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
327	84	124	3.00	0.1529	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
328	84	125	4.24	0.0531	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
329	84	123	4.24	0.0383	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
330	85	86	3.00	0.1301	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
331	85	125	3.00	0.0342	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
332	85	126	4.24	0.1668	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
333	85	124	4.24	0.1745	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
334	86	87	3.00	0.1679	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
335	86	126	3.00	0.0595	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
336	86	127	4.24	0.1397	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
337	86	125	4.24	0.1898	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
338	87	88	3.00	0.1023	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
339	87	127	3.00	0.0641	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
340	87	128	4.24	0.1657	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
341	87	126	4.24	0.0823	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
342	88	89	3.00	0.1724	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
343	88	128	3.00	0.0418	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
344	88	129	4.24	0.0718	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
345	88	127	4.24	0.0698	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
346	89	90	3.00	0.1052	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
347	89	129	3.00	0.1175	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
348	89	130	4.24	0.1838	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
349	89	128	4.24	0.1718	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
350	90	91	3.00	0.0922	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
351	90	130	3.00	0.0192	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
352	90	131	4.24	0.0445	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
353	90	129	4.24	0.1215	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
354	91	92	3.00	0.0496	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
355	91	131	3.00	0.1900	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
356	91	132	4.24	0.1354	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
357	91	130	4.24	0.1409	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
358	92	93	3.00	0.1038	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
359	92	132	3.00	0.0783	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
360	92	133	4.24	0.1948	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
361	92	131	4.24	0.1497	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
362	93	94	3.00	0.0944	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
363	93	133	3.00	0.0441	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
364	93	134	4.24	0.1693	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
365	93	132	4.24	0.1519	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
366	94	95	3.00	0.1780	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
367	94	134	3.00	0.1476	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
368	94	135	4.24	0.0265	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
369	94	133	4.24	0.1431	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
370	95	96	3.00	0.1119	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
371	95	135	3.00	0.1024	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
372	95	136	4.24	0.1242	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
373	95	134	4.24	0.1172	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
374	96	97	3.00	0.1227	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
375	96	136	3.00	0.1392	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
376	96	137	4.24	0.0656	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
377	96	135	4.24	0.0822	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
378	97	98	3.00	0.1181	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
379	97	137	3.00	0.1787	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
380	97	138	4.24	0.0276	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
381	97	136	4.24	0.0559	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
382	98	99	3.00	0.1585	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
383	98	138	3.00	0.1847	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
384	98	139	4.24	0.0573	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
385	98	137	4.24	0.1749	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
386	99	100	3.00	0.1856	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
387	99	139	3.00	0.1593	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
388	99	140	4.24	0.0847	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
389	99	138	4.24	0.1108	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
390	100	101	3.00	0.0150	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
391	100	140	3.00	0.1815	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
392	100	141	4.24	0.0635	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
393	100	139	4.24	0.1794	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
394	101	102	3.00	0.0666	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
395	101	141	3.00	0.0607	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
396	101	142	4.24	0.0188	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
397	101	140	4.24	0.1706	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
398	102	103	3.00	0.1416	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
399	102	142	3.00	0.1608	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
400	102	143	4.24	0.1732	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
401	102	141	4.24	0.0380	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
402	103	104	3.00	0.1456	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
403	103	143	3.00	0.0203	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
404	103	144	4.24	0.1867	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
405	103	142	4.24	0.1412	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
406	104	105	3.00	0.1507	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
407	104	144	3.00	0.1301	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
408	104	145	4.24	0.0235	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
409	104	143	4.24	0.1910	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
410	105	106	3.00	0.0217	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
411	105	145	3.00	0.1046	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
412	105	146	4.24	0.1030	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
413	105	144	4.24	0.0972	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
414	106	107	3.00	0.1304	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
415	106	146	3.00	0.1452	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
416	106	147	4.24	0.1138	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
417	106	145	4.24	0.1941	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
418	107	108	3.00	0.0594	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
419	107	147	3.00	0.0516	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
420	107	148	4.24	0.1746	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
421	107	146	4.24	0.1942	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
422	108	109	3.00	0.0164	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
423	108	148	3.00	0.1756	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
424	108	149	4.24	0.1053	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
425	108	147	4.24	0.0503	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
426	109	110	3.00	0.1168	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
427	109	149	3.00	0.0288	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
428	109	150	4.24	0.1980	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
429	109	148	4.24	0.0847	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
430	110	111	3.00	0.0300	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
431	110	150	3.00	0.1061	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
432	110	151	4.24	0.1565	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
433	110	149	4.24	0.1972	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
434	111	112	3.00	0.1107	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
435	111	151	3.00	0.0114	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
436	111	152	4.24	0.1479	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
437	111	150	4.24	0.0275	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
438	112	113	3.00	0.0600	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
439	112	152	3.00	0.0163	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
440	112	153	4.24	0.0598	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
441	112	151	4.24	0.0802	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
442	113	114	3.00	0.1584	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
443	113	153	3.00	0.1846	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
444	113	154	4.24	0.1258	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
445	113	152	4.24	0.0696	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
446	114	115	3.00	0.1047	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
447	114	154	3.00	0.1254	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
448	114	155	4.24	0.1774	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
449	114	153	4.24	0.1374	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
450	115	116	3.00	0.1606	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
451	115	155	3.00	0.0487	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
452	115	156	4.24	0.0895	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
453	115	154	4.24	0.1304	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
454	116	117	3.00	0.1626	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
455	116	156	3.00	0.1153	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
456	116	157	4.24	0.0532	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
457	116	155	4.24	0.0923	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
458	117	118	3.00	0.1954	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
459	117	157	3.00	0.1484	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
460	117	158	4.24	0.0586	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
461	117	156	4.24	0.1194	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
462	118	119	3.00	0.0667	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
463	118	158	3.00	0.0277	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
464	118	159	4.24	0.1348	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
465	118	157	4.24	0.1826	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
466	119	120	3.00	0.1266	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
467	119	159	3.00	0.0991	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
468	119	160	4.24	0.1393	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
469	119	158	4.24	0.0180	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
470	120	160	3.00	0.1224	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
471	120	159	4.24	0.0632	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
472	121	122	3.00	0.1452	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
473	121	161	3.00	0.0876	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
474	121	162	4.24	0.1193	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
475	122	123	3.00	0.1300	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
476	122	162	3.00	0.1905	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
477	122	163	4.24	0.1363	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
478	122	161	4.24	0.1559	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
479	123	124	3.00	0.0521	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
480	123	163	3.00	0.0844	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
481	123	164	4.24	0.1170	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
482	123	162	4.24	0.0689	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
483	124	125	3.00	0.0384	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
484	124	164	3.00	0.1524	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
485	124	165	4.24	0.1544	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
486	124	163	4.24	0.0201	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
487	125	126	3.00	0.1422	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
488	125	165	3.00	0.0651	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
489	125	166	4.24	0.1137	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
490	125	164	4.24	0.1387	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
491	126	127	3.00	0.1442	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
492	126	166	3.00	0.0338	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
493	126	167	4.24	0.0255	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
494	126	165	4.24	0.0296	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
495	127	128	3.00	0.1091	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
496	127	167	3.00	0.1026	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
497	127	168	4.24	0.1666	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
498	127	166	4.24	0.0198	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
499	128	129	3.00	0.1761	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
500	128	168	3.00	0.1987	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
501	128	169	4.24	0.1151	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
502	128	167	4.24	0.1693	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
503	129	130	3.00	0.0248	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
504	129	169	3.00	0.0702	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
505	129	170	4.24	0.1356	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
506	129	168	4.24	0.1973	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
507	130	131	3.00	0.1136	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
508	130	170	3.00	0.1944	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
509	130	171	4.24	0.0331	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
510	130	169	4.24	0.1192	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
511	131	132	3.00	0.0943	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
512	131	171	3.00	0.1284	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
513	131	172	4.24	0.0395	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
514	131	170	4.24	0.0634	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
515	132	133	3.00	0.0651	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
516	132	172	3.00	0.1950	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
517	132	173	4.24	0.1331	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
518	132	171	4.24	0.0850	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
519	133	134	3.00	0.1531	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
520	133	173	3.00	0.1605	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
521	133	174	4.24	0.0601	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
522	133	172	4.24	0.0957	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
523	134	135	3.00	0.0553	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
524	134	174	3.00	0.0634	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
525	134	175	4.24	0.1421	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
526	134	173	4.24	0.1320	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
527	135	136	3.00	0.1463	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
528	135	175	3.00	0.1846	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
529	135	176	4.24	0.0360	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
530	135	174	4.24	0.0323	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
531	136	137	3.00	0.0690	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
532	136	176	3.00	0.1064	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
533	136	177	4.24	0.0689	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
534	136	175	4.24	0.1360	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
535	137	138	3.00	0.0948	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
536	137	177	3.00	0.1860	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
537	137	178	4.24	0.1547	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
538	137	176	4.24	0.1664	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
539	138	139	3.00	0.0600	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
540	138	178	3.00	0.0803	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
541	138	179	4.24	0.0608	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
542	138	177	4.24	0.1594	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
543	139	140	3.00	0.1458	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
544	139	179	3.00	0.1771	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
545	139	180	4.24	0.1906	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
546	139	178	4.24	0.0372	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
547	140	141	3.00	0.1427	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
548	140	180	3.00	0.1167	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
549	140	181	4.24	0.0995	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
550	140	179	4.24	0.1510	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
551	141	142	3.00	0.1830	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
552	141	181	3.00	0.0863	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
553	141	182	4.24	0.0339	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
554	141	180	4.24	0.1914	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
555	142	143	3.00	0.0517	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
556	142	182	3.00	0.0713	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
557	142	183	4.24	0.1235	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
558	142	181	4.24	0.0123	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
559	143	144	3.00	0.0450	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
560	143	183	3.00	0.0353	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
561	143	184	4.24	0.1631	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
562	143	182	4.24	0.1591	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
563	144	145	3.00	0.1520	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
564	144	184	3.00	0.0147	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
565	144	185	4.24	0.1455	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
566	144	183	4.24	0.0570	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
567	145	146	3.00	0.0676	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
568	145	185	3.00	0.1473	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
569	145	186	4.24	0.0464	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
570	145	184	4.24	0.0663	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
571	146	147	3.00	0.1413	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
572	146	186	3.00	0.0912	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
573	146	187	4.24	0.1713	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
574	146	185	4.24	0.1191	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
575	147	148	3.00	0.1716	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
576	147	187	3.00	0.1773	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
577	147	188	4.24	0.1211	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
578	147	186	4.24	0.0913	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
579	148	149	3.00	0.0869	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
580	148	188	3.00	0.1726	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
581	148	189	4.24	0.1570	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
582	148	187	4.24	0.1494	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
583	149	150	3.00	0.0721	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
584	149	189	3.00	0.1601	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
585	149	190	4.24	0.0397	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
586	149	188	4.24	0.0394	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
587	150	151	3.00	0.1760	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
588	150	190	3.00	0.1881	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
589	150	191	4.24	0.1012	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
590	150	189	4.24	0.0364	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
591	151	152	3.00	0.0161	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
592	151	191	3.00	0.0661	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
593	151	192	4.24	0.0662	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
594	151	190	4.24	0.1474	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
595	152	153	3.00	0.1661	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
596	152	192	3.00	0.0658	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
597	152	193	4.24	0.0718	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
598	152	191	4.24	0.0716	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
599	153	154	3.00	0.0837	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
600	153	193	3.00	0.0164	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
601	153	194	4.24	0.0681	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
602	153	192	4.24	0.1267	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
603	154	155	3.00	0.1822	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
604	154	194	3.00	0.0167	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
605	154	195	4.24	0.1475	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
606	154	193	4.24	0.1524	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
607	155	156	3.00	0.1295	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
608	155	195	3.00	0.1584	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
609	155	196	4.24	0.1750	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
610	155	194	4.24	0.0279	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
611	156	157	3.00	0.0475	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
612	156	196	3.00	0.0957	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
613	156	197	4.24	0.0565	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
614	156	195	4.24	0.1811	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
615	157	158	3.00	0.1495	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
616	157	197	3.00	0.1491	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
617	157	198	4.24	0.0320	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
618	157	196	4.24	0.1951	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
619	158	159	3.00	0.0807	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
620	158	198	3.00	0.0215	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
621	158	199	4.24	0.0436	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
622	158	197	4.24	0.0281	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
623	159	160	3.00	0.1952	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
624	159	199	3.00	0.0622	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
625	159	200	4.24	0.1435	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
626	159	198	4.24	0.0110	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
627	160	200	3.00	0.0237	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
628	160	199	4.24	0.0434	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
629	161	162	3.00	0.0377	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
630	161	201	3.00	0.0426	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
631	161	202	4.24	0.1604	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
632	162	163	3.00	0.0461	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
633	162	202	3.00	0.0419	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
634	162	203	4.24	0.0575	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
635	162	201	4.24	0.0142	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
636	163	164	3.00	0.0584	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
637	163	203	3.00	0.0472	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
638	163	204	4.24	0.1908	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
639	163	202	4.24	0.0499	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
640	164	165	3.00	0.0375	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
641	164	204	3.00	0.1945	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
642	164	205	4.24	0.1940	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
643	164	203	4.24	0.0331	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
644	165	166	3.00	0.0553	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
645	165	205	3.00	0.1980	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
646	165	206	4.24	0.1945	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
647	165	204	4.24	0.0714	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
648	166	167	3.00	0.1922	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
649	166	206	3.00	0.0975	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
650	166	207	4.24	0.0866	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
651	166	205	4.24	0.1233	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
652	167	168	3.00	0.1105	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
653	167	207	3.00	0.1406	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
654	167	208	4.24	0.1234	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
655	167	206	4.24	0.1513	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
656	168	169	3.00	0.1178	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
657	168	208	3.00	0.0692	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
658	168	209	4.24	0.1734	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
659	168	207	4.24	0.1289	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
660	169	170	3.00	0.0101	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
661	169	209	3.00	0.1382	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
662	169	210	4.24	0.1149	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
663	169	208	4.24	0.1462	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
664	170	171	3.00	0.1047	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
665	170	210	3.00	0.1500	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
666	170	211	4.24	0.1372	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
667	170	209	4.24	0.1067	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
668	171	172	3.00	0.1285	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
669	171	211	3.00	0.1135	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
670	171	212	4.24	0.0194	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
671	171	210	4.24	0.0427	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
672	172	173	3.00	0.0897	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
673	172	212	3.00	0.0480	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
674	172	213	4.24	0.0685	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
675	172	211	4.24	0.1401	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
676	173	174	3.00	0.0739	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
677	173	213	3.00	0.1906	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
678	173	214	4.24	0.0161	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
679	173	212	4.24	0.1875	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
680	174	175	3.00	0.0934	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
681	174	214	3.00	0.1525	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
682	174	215	4.24	0.1012	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
683	174	213	4.24	0.1382	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
684	175	176	3.00	0.0640	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
685	175	215	3.00	0.0748	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
686	175	216	4.24	0.0403	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
687	175	214	4.24	0.1949	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
688	176	177	3.00	0.1684	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
689	176	216	3.00	0.1780	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
690	176	217	4.24	0.1924	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
691	176	215	4.24	0.0256	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
692	177	178	3.00	0.0692	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
693	177	217	3.00	0.1329	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
694	177	218	4.24	0.1263	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
695	177	216	4.24	0.0941	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
696	178	179	3.00	0.0458	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
697	178	218	3.00	0.1914	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
698	178	219	4.24	0.0375	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
699	178	217	4.24	0.1171	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
700	179	180	3.00	0.1581	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
701	179	219	3.00	0.0569	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
702	179	220	4.24	0.0851	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
703	179	218	4.24	0.0182	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
704	180	181	3.00	0.0997	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
705	180	220	3.00	0.0720	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
706	180	221	4.24	0.1384	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
707	180	219	4.24	0.0876	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
708	181	182	3.00	0.0312	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
709	181	221	3.00	0.1879	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
710	181	222	4.24	0.1632	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
711	181	220	4.24	0.0466	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
712	182	183	3.00	0.0564	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
713	182	222	3.00	0.0829	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
714	182	223	4.24	0.1109	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
715	182	221	4.24	0.1906	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
716	183	184	3.00	0.1419	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
717	183	223	3.00	0.0158	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
718	183	224	4.24	0.0192	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
719	183	222	4.24	0.1585	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
720	184	185	3.00	0.1862	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
721	184	224	3.00	0.1971	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
722	184	225	4.24	0.0359	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
723	184	223	4.24	0.0830	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
724	185	186	3.00	0.1986	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
725	185	225	3.00	0.0696	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
726	185	226	4.24	0.1740	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
727	185	224	4.24	0.1853	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
728	186	187	3.00	0.1513	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
729	186	226	3.00	0.1148	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
730	186	227	4.24	0.1229	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
731	186	225	4.24	0.1412	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
732	187	188	3.00	0.0367	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
733	187	227	3.00	0.1059	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
734	187	228	4.24	0.1984	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
735	187	226	4.24	0.0115	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
736	188	189	3.00	0.1966	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
737	188	228	3.00	0.1625	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
738	188	229	4.24	0.1708	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
739	188	227	4.24	0.1354	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
740	189	190	3.00	0.0642	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
741	189	229	3.00	0.0675	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
742	189	230	4.24	0.1580	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
743	189	228	4.24	0.1249	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
744	190	191	3.00	0.0221	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
745	190	230	3.00	0.0368	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
746	190	231	4.24	0.1821	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
747	190	229	4.24	0.0102	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
748	191	192	3.00	0.0706	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
749	191	231	3.00	0.0730	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
750	191	232	4.24	0.0854	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
751	191	230	4.24	0.0523	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
752	192	193	3.00	0.1281	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
753	192	232	3.00	0.0661	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
754	192	233	4.24	0.0583	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
755	192	231	4.24	0.1688	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
756	193	194	3.00	0.0495	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
757	193	233	3.00	0.1860	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
758	193	234	4.24	0.1374	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
759	193	232	4.24	0.1144	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
760	194	195	3.00	0.0800	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
761	194	234	3.00	0.1670	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
762	194	235	4.24	0.0299	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
763	194	233	4.24	0.0820	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
764	195	196	3.00	0.1974	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
765	195	235	3.00	0.0682	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
766	195	236	4.24	0.0168	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
767	195	234	4.24	0.0336	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
768	196	197	3.00	0.1186	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
769	196	236	3.00	0.0890	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
770	196	237	4.24	0.0815	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
771	196	235	4.24	0.0725	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
772	197	198	3.00	0.0665	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
773	197	237	3.00	0.0122	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
774	197	238	4.24	0.1344	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
775	197	236	4.24	0.0754	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
776	198	199	3.00	0.1450	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
777	198	238	3.00	0.1048	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
778	198	239	4.24	0.1547	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
779	198	237	4.24	0.1804	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
780	199	200	3.00	0.0362	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
781	199	239	3.00	0.0791	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
782	199	240	4.24	0.1457	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
783	199	238	4.24	0.1568	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
784	200	240	3.00	0.1240	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
785	200	239	4.24	0.0903	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
786	201	202	3.00	0.1750	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
787	201	241	3.00	0.1293	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
788	201	242	4.24	0.1489	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
789	202	203	3.00	0.1971	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
790	202	242	3.00	0.0629	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
791	202	243	4.24	0.0962	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
792	202	241	4.24	0.1126	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
793	203	204	3.00	0.0623	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
794	203	243	3.00	0.0695	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
795	203	244	4.24	0.1952	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
796	203	242	4.24	0.1772	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
797	204	205	3.00	0.0335	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
798	204	244	3.00	0.0797	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
799	204	245	4.24	0.1943	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
800	204	243	4.24	0.0431	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
801	205	206	3.00	0.0967	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
802	205	245	3.00	0.0539	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
803	205	246	4.24	0.0184	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
804	205	244	4.24	0.1658	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
805	206	207	3.00	0.0855	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
806	206	246	3.00	0.1018	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
807	206	247	4.24	0.1706	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
808	206	245	4.24	0.1284	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
809	207	208	3.00	0.1954	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
810	207	247	3.00	0.1977	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
811	207	248	4.24	0.0898	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
812	207	246	4.24	0.0104	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
813	208	209	3.00	0.1803	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
814	208	248	3.00	0.1930	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
815	208	249	4.24	0.1404	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
816	208	247	4.24	0.0729	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
817	209	210	3.00	0.1043	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
818	209	249	3.00	0.0812	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
819	209	250	4.24	0.0920	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
820	209	248	4.24	0.1433	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
821	210	211	3.00	0.1005	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
822	210	250	3.00	0.0948	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
823	210	251	4.24	0.1898	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
824	210	249	4.24	0.0832	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
825	211	212	3.00	0.1249	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
826	211	251	3.00	0.1111	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
827	211	252	4.24	0.1010	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
828	211	250	4.24	0.0588	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
829	212	213	3.00	0.0971	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
830	212	252	3.00	0.0823	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
831	212	253	4.24	0.0575	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
832	212	251	4.24	0.1740	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
833	213	214	3.00	0.0690	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
834	213	253	3.00	0.1906	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
835	213	254	4.24	0.0711	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
836	213	252	4.24	0.0388	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
837	214	215	3.00	0.1978	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
838	214	254	3.00	0.0872	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
839	214	255	4.24	0.0370	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
840	214	253	4.24	0.0969	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
841	215	216	3.00	0.0628	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
842	215	255	3.00	0.0575	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
843	215	256	4.24	0.1531	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
844	215	254	4.24	0.1262	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
845	216	217	3.00	0.0438	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
846	216	256	3.00	0.0538	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
847	216	257	4.24	0.1557	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
848	216	255	4.24	0.0705	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
849	217	218	3.00	0.1552	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
850	217	257	3.00	0.1798	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
851	217	258	4.24	0.0703	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
852	217	256	4.24	0.1468	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
853	218	219	3.00	0.0186	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
854	218	258	3.00	0.1652	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
855	218	259	4.24	0.1063	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
856	218	257	4.24	0.1028	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
857	219	220	3.00	0.1019	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
858	219	259	3.00	0.1709	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
859	219	260	4.24	0.0389	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
860	219	258	4.24	0.1257	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
861	220	221	3.00	0.1670	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
862	220	260	3.00	0.0686	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
863	220	261	4.24	0.0797	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
864	220	259	4.24	0.1795	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
865	221	222	3.00	0.1013	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
866	221	261	3.00	0.0652	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
867	221	262	4.24	0.1958	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
868	221	260	4.24	0.1666	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
869	222	223	3.00	0.1530	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
870	222	262	3.00	0.1336	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
871	222	263	4.24	0.0863	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
872	222	261	4.24	0.1564	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
873	223	224	3.00	0.1136	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
874	223	263	3.00	0.1171	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
875	223	264	4.24	0.0797	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
876	223	262	4.24	0.1742	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
877	224	225	3.00	0.1989	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
878	224	264	3.00	0.1265	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
879	224	265	4.24	0.0119	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
880	224	263	4.24	0.0773	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
881	225	226	3.00	0.1633	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
882	225	265	3.00	0.1716	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
883	225	266	4.24	0.1537	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
884	225	264	4.24	0.1489	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
885	226	227	3.00	0.0102	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
886	226	266	3.00	0.0341	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
887	226	267	4.24	0.1060	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
888	226	265	4.24	0.0378	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
889	227	228	3.00	0.1134	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
890	227	267	3.00	0.0959	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
891	227	268	4.24	0.0717	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
892	227	266	4.24	0.0754	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
893	228	229	3.00	0.1143	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
894	228	268	3.00	0.0338	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
895	228	269	4.24	0.1060	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
896	228	267	4.24	0.0614	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
897	229	230	3.00	0.0874	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
898	229	269	3.00	0.1933	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
899	229	270	4.24	0.0353	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
900	229	268	4.24	0.1470	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
901	230	231	3.00	0.1905	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
902	230	270	3.00	0.1827	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
903	230	271	4.24	0.1768	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
904	230	269	4.24	0.1754	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
905	231	232	3.00	0.0832	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
906	231	271	3.00	0.1935	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
907	231	272	4.24	0.0241	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
908	231	270	4.24	0.1162	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
909	232	233	3.00	0.1704	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
910	232	272	3.00	0.0967	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
911	232	273	4.24	0.1942	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
912	232	271	4.24	0.1395	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
913	233	234	3.00	0.1312	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
914	233	273	3.00	0.0833	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
915	233	274	4.24	0.0723	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
916	233	272	4.24	0.0368	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
917	234	235	3.00	0.1828	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
918	234	274	3.00	0.1740	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
919	234	275	4.24	0.1840	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
920	234	273	4.24	0.0749	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
921	235	236	3.00	0.0312	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
922	235	275	3.00	0.0355	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
923	235	276	4.24	0.0388	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
924	235	274	4.24	0.0760	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
925	236	237	3.00	0.1362	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
926	236	276	3.00	0.1549	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
927	236	277	4.24	0.0735	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
928	236	275	4.24	0.1576	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
929	237	238	3.00	0.0734	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
930	237	277	3.00	0.0530	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
931	237	278	4.24	0.0102	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
932	237	276	4.24	0.1641	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
933	238	239	3.00	0.1031	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
934	238	278	3.00	0.1080	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
935	238	279	4.24	0.1467	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
936	238	277	4.24	0.0348	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
937	239	240	3.00	0.1707	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
938	239	279	3.00	0.0951	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
939	239	280	4.24	0.1777	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
940	239	278	4.24	0.0767	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
941	240	280	3.00	0.0678	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
942	240	279	4.24	0.0108	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
943	241	242	3.00	0.0146	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
944	241	281	3.00	0.1445	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
945	241	282	4.24	0.0674	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
946	242	243	3.00	0.0210	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
947	242	282	3.00	0.0326	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
948	242	283	4.24	0.0425	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
949	242	281	4.24	0.1096	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
950	243	244	3.00	0.0410	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
951	243	283	3.00	0.1012	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
952	243	284	4.24	0.1791	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
953	243	282	4.24	0.0323	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
954	244	245	3.00	0.1836	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
955	244	284	3.00	0.0652	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
956	244	285	4.24	0.0638	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
957	244	283	4.24	0.1813	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
958	245	246	3.00	0.0180	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
959	245	285	3.00	0.0516	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
960	245	286	4.24	0.0228	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
961	245	284	4.24	0.0239	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
962	246	247	3.00	0.1176	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
963	246	286	3.00	0.1930	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
964	246	287	4.24	0.1192	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
965	246	285	4.24	0.1372	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
966	247	248	3.00	0.0256	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
967	247	287	3.00	0.1915	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
968	247	288	4.24	0.1233	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
969	247	286	4.24	0.0357	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
970	248	249	3.00	0.1070	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
971	248	288	3.00	0.0482	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
972	248	289	4.24	0.1967	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
973	248	287	4.24	0.0353	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
974	249	250	3.00	0.1565	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
975	249	289	3.00	0.0855	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
976	249	290	4.24	0.1398	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
977	249	288	4.24	0.0185	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
978	250	251	3.00	0.0982	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
979	250	290	3.00	0.1931	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
980	250	291	4.24	0.1059	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
981	250	289	4.24	0.1938	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
982	251	252	3.00	0.1863	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
983	251	291	3.00	0.0935	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
984	251	292	4.24	0.1541	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
985	251	290	4.24	0.0806	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
986	252	253	3.00	0.0830	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
987	252	292	3.00	0.1542	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
988	252	293	4.24	0.0371	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
989	252	291	4.24	0.1564	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
990	253	254	3.00	0.1861	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
991	253	293	3.00	0.1955	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
992	253	294	4.24	0.1053	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
993	253	292	4.24	0.1100	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
994	254	255	3.00	0.1820	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
995	254	294	3.00	0.0412	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
996	254	295	4.24	0.1655	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
997	254	293	4.24	0.0799	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
998	255	256	3.00	0.0438	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
999	255	295	3.00	0.0700	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
1000	255	296	4.24	0.0731	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
1001	255	294	4.24	0.1414	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
1002	256	257	3.00	0.1226	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
1003	256	296	3.00	0.1516	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
1004	256	297	4.24	0.0500	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
1005	256	295	4.24	0.1484	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
1006	257	258	3.00	0.1706	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
1007	257	297	3.00	0.1543	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
1008	257	298	4.24	0.1645	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
1009	257	296	4.24	0.0520	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
1010	258	259	3.00	0.0331	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
1011	258	298	3.00	0.1833	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
1012	258	299	4.24	0.1892	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
1013	258	297	4.24	0.0974	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
1014	259	260	3.00	0.0525	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
1015	259	299	3.00	0.0819	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
1016	259	300	4.24	0.0437	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
1017	259	298	4.24	0.0572	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
1018	260	261	3.00	0.1494	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
1019	260	300	3.00	0.0677	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
1020	260	301	4.24	0.1459	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
1021	260	299	4.24	0.0978	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
1022	261	262	3.00	0.0413	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
1023	261	301	3.00	0.0227	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
1024	261	302	4.24	0.0633	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
1025	261	300	4.24	0.0320	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
1026	262	263	3.00	0.0562	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
1027	262	302	3.00	0.0553	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
1028	262	303	4.24	0.1658	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
1029	262	301	4.24	0.1530	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
1030	263	264	3.00	0.0453	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
1031	263	303	3.00	0.0439	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
1032	263	304	4.24	0.1531	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
1033	263	302	4.24	0.1823	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
1034	264	265	3.00	0.0282	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
1035	264	304	3.00	0.0104	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
1036	264	305	4.24	0.1320	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
1037	264	303	4.24	0.0648	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
1038	265	266	3.00	0.1142	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
1039	265	305	3.00	0.0969	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
1040	265	306	4.24	0.0963	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
1041	265	304	4.24	0.0369	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
1042	266	267	3.00	0.0288	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
1043	266	306	3.00	0.1856	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
1044	266	307	4.24	0.1019	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
1045	266	305	4.24	0.1727	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
1046	267	268	3.00	0.1197	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
1047	267	307	3.00	0.1700	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
1048	267	308	4.24	0.0740	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
1049	267	306	4.24	0.1239	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
1050	268	269	3.00	0.0310	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
1051	268	308	3.00	0.1114	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
1052	268	309	4.24	0.1687	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
1053	268	307	4.24	0.1292	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
1054	269	270	3.00	0.1889	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
1055	269	309	3.00	0.0402	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
1056	269	310	4.24	0.1665	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
1057	269	308	4.24	0.0662	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
1058	270	271	3.00	0.1681	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
1059	270	310	3.00	0.0612	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
1060	270	311	4.24	0.1405	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
1061	270	309	4.24	0.1829	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
1062	271	272	3.00	0.0164	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
1063	271	311	3.00	0.1070	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
1064	271	312	4.24	0.1444	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
1065	271	310	4.24	0.1811	mecanico_herramienta	2026-08-26 18:02:07	2026-08-26 18:02:07
1066	272	273	3.00	0.1319	misma_fila	2026-08-26 18:02:07	2026-08-26 18:02:07
1067	272	312	3.00	0.0358	fila_contigua	2026-08-26 18:02:07	2026-08-26 18:02:07
1068	272	313	4.24	0.1272	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
1069	272	311	4.24	0.1572	viento_predominante	2026-08-26 18:02:07	2026-08-26 18:02:07
1070	273	274	3.00	0.1695	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1071	273	313	3.00	0.1558	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1072	273	314	4.24	0.0157	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1073	273	312	4.24	0.1020	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1074	274	275	3.00	0.0940	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1075	274	314	3.00	0.0193	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1076	274	315	4.24	0.1054	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1077	274	313	4.24	0.0376	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1078	275	276	3.00	0.0915	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1079	275	315	3.00	0.0148	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1080	275	316	4.24	0.1098	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1081	275	314	4.24	0.0518	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1082	276	277	3.00	0.0719	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1083	276	316	3.00	0.0885	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1084	276	317	4.24	0.1652	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1085	276	315	4.24	0.1320	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1086	277	278	3.00	0.1721	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1087	277	317	3.00	0.0181	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1088	277	318	4.24	0.1931	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1089	277	316	4.24	0.1736	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1090	278	279	3.00	0.1861	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1091	278	318	3.00	0.1850	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1092	278	319	4.24	0.1913	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1093	278	317	4.24	0.0148	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1094	279	280	3.00	0.0569	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1095	279	319	3.00	0.0131	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1096	279	320	4.24	0.0655	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1097	279	318	4.24	0.0948	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1098	280	320	3.00	0.1665	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1099	280	319	4.24	0.0832	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1100	281	282	3.00	0.0434	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1101	281	321	3.00	0.0306	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1102	281	322	4.24	0.0719	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1103	282	283	3.00	0.1911	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1104	282	322	3.00	0.1427	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1105	282	323	4.24	0.0737	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1106	282	321	4.24	0.1314	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1107	283	284	3.00	0.1320	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1108	283	323	3.00	0.1961	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1109	283	324	4.24	0.1501	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1110	283	322	4.24	0.0153	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1111	284	285	3.00	0.1718	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1112	284	324	3.00	0.0574	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1113	284	325	4.24	0.1905	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1114	284	323	4.24	0.0481	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1115	285	286	3.00	0.0206	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1116	285	325	3.00	0.0906	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1117	285	326	4.24	0.0271	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1118	285	324	4.24	0.0693	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1119	286	287	3.00	0.1938	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1120	286	326	3.00	0.1495	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1121	286	327	4.24	0.1163	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1122	286	325	4.24	0.0461	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1123	287	288	3.00	0.1548	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1124	287	327	3.00	0.0405	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1125	287	328	4.24	0.1923	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1126	287	326	4.24	0.0106	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1127	288	289	3.00	0.1999	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1128	288	328	3.00	0.0849	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1129	288	329	4.24	0.0979	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1130	288	327	4.24	0.0435	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1131	289	290	3.00	0.0202	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1132	289	329	3.00	0.0449	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1133	289	330	4.24	0.1802	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1134	289	328	4.24	0.1195	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1135	290	291	3.00	0.0885	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1136	290	330	3.00	0.1383	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1137	290	331	4.24	0.1207	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1138	290	329	4.24	0.0266	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1139	291	292	3.00	0.1514	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1140	291	331	3.00	0.0561	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1141	291	332	4.24	0.0531	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1142	291	330	4.24	0.0317	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1143	292	293	3.00	0.1656	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1144	292	332	3.00	0.0979	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1145	292	333	4.24	0.0795	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1146	292	331	4.24	0.0249	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1147	293	294	3.00	0.1296	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1148	293	333	3.00	0.0559	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1149	293	334	4.24	0.1862	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1150	293	332	4.24	0.0363	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1151	294	295	3.00	0.1070	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1152	294	334	3.00	0.0551	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1153	294	335	4.24	0.1846	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1154	294	333	4.24	0.0602	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1155	295	296	3.00	0.1255	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1156	295	335	3.00	0.1730	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1157	295	336	4.24	0.0862	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1158	295	334	4.24	0.1341	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1159	296	297	3.00	0.0558	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1160	296	336	3.00	0.0535	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1161	296	337	4.24	0.1617	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1162	296	335	4.24	0.1505	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1163	297	298	3.00	0.0901	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1164	297	337	3.00	0.1025	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1165	297	338	4.24	0.0819	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1166	297	336	4.24	0.0974	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1167	298	299	3.00	0.0635	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1168	298	338	3.00	0.0432	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1169	298	339	4.24	0.0582	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1170	298	337	4.24	0.1739	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1171	299	300	3.00	0.1566	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1172	299	339	3.00	0.0262	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1173	299	340	4.24	0.0762	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1174	299	338	4.24	0.1528	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1175	300	301	3.00	0.0938	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1176	300	340	3.00	0.0214	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1177	300	341	4.24	0.1088	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1178	300	339	4.24	0.0888	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1179	301	302	3.00	0.1291	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1180	301	341	3.00	0.0317	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1181	301	342	4.24	0.1817	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1182	301	340	4.24	0.0568	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1183	302	303	3.00	0.0112	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1184	302	342	3.00	0.0701	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1185	302	343	4.24	0.0242	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1186	302	341	4.24	0.0744	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1187	303	304	3.00	0.0837	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1188	303	343	3.00	0.1754	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1189	303	344	4.24	0.1264	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1190	303	342	4.24	0.1173	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1191	304	305	3.00	0.0349	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1192	304	344	3.00	0.0101	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1193	304	345	4.24	0.0187	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1194	304	343	4.24	0.0809	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1195	305	306	3.00	0.1834	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1196	305	345	3.00	0.1802	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1197	305	346	4.24	0.1057	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1198	305	344	4.24	0.1264	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1199	306	307	3.00	0.1074	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1200	306	346	3.00	0.1023	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1201	306	347	4.24	0.1559	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1202	306	345	4.24	0.0876	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1203	307	308	3.00	0.1695	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1204	307	347	3.00	0.1697	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1205	307	348	4.24	0.1607	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1206	307	346	4.24	0.1680	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1207	308	309	3.00	0.1819	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1208	308	348	3.00	0.1907	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1209	308	349	4.24	0.0497	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1210	308	347	4.24	0.0577	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1211	309	310	3.00	0.1158	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1212	309	349	3.00	0.0874	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1213	309	350	4.24	0.0994	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1214	309	348	4.24	0.1427	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1215	310	311	3.00	0.0802	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1216	310	350	3.00	0.1356	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1217	310	351	4.24	0.0406	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1218	310	349	4.24	0.0847	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1219	311	312	3.00	0.0934	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1220	311	351	3.00	0.1316	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1221	311	352	4.24	0.0756	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1222	311	350	4.24	0.1778	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1223	312	313	3.00	0.0127	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1224	312	352	3.00	0.0538	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1225	312	353	4.24	0.1267	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1226	312	351	4.24	0.0173	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1227	313	314	3.00	0.0328	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1228	313	353	3.00	0.1222	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1229	313	354	4.24	0.1189	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1230	313	352	4.24	0.0838	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1231	314	315	3.00	0.0469	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1232	314	354	3.00	0.1538	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1233	314	355	4.24	0.1735	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1234	314	353	4.24	0.1947	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1235	315	316	3.00	0.1238	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1236	315	355	3.00	0.1119	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1237	315	356	4.24	0.1302	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1238	315	354	4.24	0.1717	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1239	316	317	3.00	0.0775	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1240	316	356	3.00	0.1230	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1241	316	357	4.24	0.0972	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1242	316	355	4.24	0.1477	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1243	317	318	3.00	0.0977	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1244	317	357	3.00	0.1068	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1245	317	358	4.24	0.1981	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1246	317	356	4.24	0.0614	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1247	318	319	3.00	0.0824	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1248	318	358	3.00	0.1220	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1249	318	359	4.24	0.1564	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1250	318	357	4.24	0.0983	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1251	319	320	3.00	0.0715	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1252	319	359	3.00	0.0494	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1253	319	360	4.24	0.1012	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1254	319	358	4.24	0.1095	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1255	320	360	3.00	0.1939	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1256	320	359	4.24	0.0849	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1257	321	322	3.00	0.1528	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1258	321	361	3.00	0.0190	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1259	321	362	4.24	0.1086	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1260	322	323	3.00	0.1699	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1261	322	362	3.00	0.1249	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1262	322	363	4.24	0.0740	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1263	322	361	4.24	0.1642	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1264	323	324	3.00	0.1420	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1265	323	363	3.00	0.1704	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1266	323	364	4.24	0.1701	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1267	323	362	4.24	0.0416	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1268	324	325	3.00	0.1172	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1269	324	364	3.00	0.1918	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1270	324	365	4.24	0.0991	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1271	324	363	4.24	0.0258	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1272	325	326	3.00	0.0939	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1273	325	365	3.00	0.0746	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1274	325	366	4.24	0.1923	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1275	325	364	4.24	0.1250	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1276	326	327	3.00	0.1182	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1277	326	366	3.00	0.0659	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1278	326	367	4.24	0.1488	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1279	326	365	4.24	0.1269	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1280	327	328	3.00	0.1742	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1281	327	367	3.00	0.1324	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1282	327	368	4.24	0.1412	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1283	327	366	4.24	0.0275	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1284	328	329	3.00	0.0379	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1285	328	368	3.00	0.0192	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1286	328	369	4.24	0.0192	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1287	328	367	4.24	0.0138	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1288	329	330	3.00	0.1150	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1289	329	369	3.00	0.1273	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1290	329	370	4.24	0.0437	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1291	329	368	4.24	0.0968	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1292	330	331	3.00	0.1899	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1293	330	370	3.00	0.0733	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1294	330	371	4.24	0.0764	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1295	330	369	4.24	0.0962	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1296	331	332	3.00	0.0715	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1297	331	371	3.00	0.1445	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1298	331	372	4.24	0.1322	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1299	331	370	4.24	0.1191	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1300	332	333	3.00	0.0993	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1301	332	372	3.00	0.1399	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1302	332	373	4.24	0.0425	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1303	332	371	4.24	0.0720	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1304	333	334	3.00	0.0716	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1305	333	373	3.00	0.1539	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1306	333	374	4.24	0.0209	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1307	333	372	4.24	0.0614	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1308	334	335	3.00	0.0502	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1309	334	374	3.00	0.1566	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1310	334	375	4.24	0.1416	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1311	334	373	4.24	0.0113	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1312	335	336	3.00	0.1256	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1313	335	375	3.00	0.1838	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1314	335	376	4.24	0.1397	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1315	335	374	4.24	0.1598	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1316	336	337	3.00	0.1900	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1317	336	376	3.00	0.1015	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1318	336	377	4.24	0.1319	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1319	336	375	4.24	0.1536	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1320	337	338	3.00	0.0983	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1321	337	377	3.00	0.1179	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1322	337	378	4.24	0.1658	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1323	337	376	4.24	0.1271	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1324	338	339	3.00	0.1800	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1325	338	378	3.00	0.1877	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1326	338	379	4.24	0.1129	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1327	338	377	4.24	0.0571	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1328	339	340	3.00	0.0911	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1329	339	379	3.00	0.0312	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1330	339	380	4.24	0.0866	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1331	339	378	4.24	0.1405	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1332	340	341	3.00	0.0896	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1333	340	380	3.00	0.1698	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1334	340	381	4.24	0.0858	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1335	340	379	4.24	0.1817	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1336	341	342	3.00	0.1362	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1337	341	381	3.00	0.1979	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1338	341	382	4.24	0.0170	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1339	341	380	4.24	0.0395	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1340	342	343	3.00	0.1003	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1341	342	382	3.00	0.0588	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1342	342	383	4.24	0.1959	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1343	342	381	4.24	0.1491	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1344	343	344	3.00	0.0229	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1345	343	383	3.00	0.1636	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1346	343	384	4.24	0.0504	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1347	343	382	4.24	0.1579	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1348	344	345	3.00	0.0441	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1349	344	384	3.00	0.0859	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1350	344	385	4.24	0.0528	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1351	344	383	4.24	0.0412	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1352	345	346	3.00	0.1664	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1353	345	385	3.00	0.0249	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1354	345	386	4.24	0.0101	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1355	345	384	4.24	0.0551	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1356	346	347	3.00	0.1348	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1357	346	386	3.00	0.0722	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1358	346	387	4.24	0.1560	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1359	346	385	4.24	0.0943	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1360	347	348	3.00	0.0932	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1361	347	387	3.00	0.1200	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1362	347	388	4.24	0.1593	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1363	347	386	4.24	0.1766	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1364	348	349	3.00	0.1426	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1365	348	388	3.00	0.0301	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1366	348	389	4.24	0.0199	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1367	348	387	4.24	0.0668	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1368	349	350	3.00	0.1634	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1369	349	389	3.00	0.1484	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1370	349	390	4.24	0.1951	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1371	349	388	4.24	0.1253	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1372	350	351	3.00	0.1709	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1373	350	390	3.00	0.1297	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1374	350	391	4.24	0.1668	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1375	350	389	4.24	0.0877	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1376	351	352	3.00	0.0643	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1377	351	391	3.00	0.1144	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1378	351	392	4.24	0.1302	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1379	351	390	4.24	0.1050	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1380	352	353	3.00	0.0682	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1381	352	392	3.00	0.1061	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1382	352	393	4.24	0.1183	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1383	352	391	4.24	0.0574	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1384	353	354	3.00	0.0166	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1385	353	393	3.00	0.0586	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1386	353	394	4.24	0.0884	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1387	353	392	4.24	0.0841	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1388	354	355	3.00	0.0789	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1389	354	394	3.00	0.1091	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1390	354	395	4.24	0.1028	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1391	354	393	4.24	0.1296	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1392	355	356	3.00	0.0348	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1393	355	395	3.00	0.1472	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1394	355	396	4.24	0.1170	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1395	355	394	4.24	0.0925	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1396	356	357	3.00	0.1190	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1397	356	396	3.00	0.1478	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1398	356	397	4.24	0.1519	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1399	356	395	4.24	0.0990	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1400	357	358	3.00	0.1686	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1401	357	397	3.00	0.0801	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1402	357	398	4.24	0.0353	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1403	357	396	4.24	0.1775	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1404	358	359	3.00	0.1728	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1405	358	398	3.00	0.0410	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1406	358	399	4.24	0.1950	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1407	358	397	4.24	0.1795	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1408	359	360	3.00	0.1872	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1409	359	399	3.00	0.0599	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1410	359	400	4.24	0.1673	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1411	359	398	4.24	0.0386	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1412	360	400	3.00	0.0320	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1413	360	399	4.24	0.1480	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1414	361	362	3.00	0.0475	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1415	361	401	3.00	0.1799	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1416	361	402	4.24	0.1719	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1417	362	363	3.00	0.0296	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1418	362	402	3.00	0.0995	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1419	362	403	4.24	0.1667	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1420	362	401	4.24	0.1335	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1421	363	364	3.00	0.1095	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1422	363	403	3.00	0.1685	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1423	363	404	4.24	0.1037	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1424	363	402	4.24	0.0639	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1425	364	365	3.00	0.0498	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1426	364	404	3.00	0.1364	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1427	364	405	4.24	0.1288	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1428	364	403	4.24	0.0327	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1429	365	366	3.00	0.1414	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1430	365	405	3.00	0.0851	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1431	365	406	4.24	0.1035	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1432	365	404	4.24	0.1876	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1433	366	367	3.00	0.1189	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1434	366	406	3.00	0.0634	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1435	366	407	4.24	0.0676	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1436	366	405	4.24	0.0571	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1437	367	368	3.00	0.1559	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1438	367	407	3.00	0.0122	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1439	367	408	4.24	0.0738	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1440	367	406	4.24	0.0159	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1441	368	369	3.00	0.0724	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1442	368	408	3.00	0.0203	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1443	368	409	4.24	0.0107	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1444	368	407	4.24	0.0167	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1445	369	370	3.00	0.0547	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1446	369	409	3.00	0.1067	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1447	369	410	4.24	0.1051	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1448	369	408	4.24	0.1448	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1449	370	371	3.00	0.0966	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1450	370	410	3.00	0.1431	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1451	370	411	4.24	0.0811	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1452	370	409	4.24	0.1705	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1453	371	372	3.00	0.0102	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1454	371	411	3.00	0.0483	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1455	371	412	4.24	0.0941	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1456	371	410	4.24	0.1963	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1457	372	373	3.00	0.1594	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1458	372	412	3.00	0.1547	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1459	372	413	4.24	0.0400	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1460	372	411	4.24	0.1173	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1461	373	374	3.00	0.1315	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1462	373	413	3.00	0.1920	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1463	373	414	4.24	0.0204	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1464	373	412	4.24	0.0657	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1465	374	375	3.00	0.1386	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1466	374	414	3.00	0.1942	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1467	374	415	4.24	0.0638	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1468	374	413	4.24	0.1582	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1469	375	376	3.00	0.0647	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1470	375	415	3.00	0.0533	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1471	375	416	4.24	0.1583	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1472	375	414	4.24	0.0126	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1473	376	377	3.00	0.1426	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1474	376	416	3.00	0.1885	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1475	376	417	4.24	0.0693	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1476	376	415	4.24	0.1915	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1477	377	378	3.00	0.1331	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1478	377	417	3.00	0.0879	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1479	377	418	4.24	0.1259	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1480	377	416	4.24	0.1435	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1481	378	379	3.00	0.1530	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1482	378	418	3.00	0.1494	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1483	378	419	4.24	0.1673	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1484	378	417	4.24	0.1977	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1485	379	380	3.00	0.1460	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1486	379	419	3.00	0.0680	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1487	379	420	4.24	0.1355	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1488	379	418	4.24	0.0604	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1489	380	381	3.00	0.1068	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1490	380	420	3.00	0.1186	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1491	380	421	4.24	0.0577	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1492	380	419	4.24	0.1781	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1493	381	382	3.00	0.1771	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1494	381	421	3.00	0.1556	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1495	381	422	4.24	0.1204	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1496	381	420	4.24	0.1594	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1497	382	383	3.00	0.1309	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1498	382	422	3.00	0.1525	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1499	382	423	4.24	0.0346	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1500	382	421	4.24	0.0382	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1501	383	384	3.00	0.1740	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1502	383	423	3.00	0.0370	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1503	383	424	4.24	0.0289	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1504	383	422	4.24	0.1642	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1505	384	385	3.00	0.1351	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1506	384	424	3.00	0.0295	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1507	384	425	4.24	0.1121	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1508	384	423	4.24	0.0439	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1509	385	386	3.00	0.0746	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1510	385	425	3.00	0.0941	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1511	385	426	4.24	0.1486	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1512	385	424	4.24	0.1467	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1513	386	387	3.00	0.0539	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1514	386	426	3.00	0.1319	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1515	386	427	4.24	0.0331	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1516	386	425	4.24	0.1470	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1517	387	388	3.00	0.0748	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1518	387	427	3.00	0.1649	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1519	387	428	4.24	0.1561	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1520	387	426	4.24	0.1134	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1521	388	389	3.00	0.1409	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1522	388	428	3.00	0.0197	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1523	388	429	4.24	0.1888	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1524	388	427	4.24	0.0620	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1525	389	390	3.00	0.0561	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1526	389	429	3.00	0.0920	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1527	389	430	4.24	0.0347	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1528	389	428	4.24	0.1409	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1529	390	391	3.00	0.0927	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1530	390	430	3.00	0.1722	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1531	390	431	4.24	0.0403	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1532	390	429	4.24	0.1273	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1533	391	392	3.00	0.1252	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1534	391	431	3.00	0.1698	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1535	391	432	4.24	0.1647	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1536	391	430	4.24	0.1160	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1537	392	393	3.00	0.0608	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1538	392	432	3.00	0.0666	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1539	392	433	4.24	0.0365	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1540	392	431	4.24	0.0783	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1541	393	394	3.00	0.1524	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1542	393	433	3.00	0.0688	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1543	393	434	4.24	0.0136	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1544	393	432	4.24	0.1568	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1545	394	395	3.00	0.0189	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1546	394	434	3.00	0.0713	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1547	394	435	4.24	0.0819	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1548	394	433	4.24	0.0790	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1549	395	396	3.00	0.1068	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1550	395	435	3.00	0.0616	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1551	395	436	4.24	0.1189	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1552	395	434	4.24	0.0870	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1553	396	397	3.00	0.1296	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1554	396	436	3.00	0.1842	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1555	396	437	4.24	0.0812	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1556	396	435	4.24	0.0743	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1557	397	398	3.00	0.0955	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1558	397	437	3.00	0.1590	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1559	397	438	4.24	0.1197	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1560	397	436	4.24	0.1553	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1561	398	399	3.00	0.0198	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1562	398	438	3.00	0.0815	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1563	398	439	4.24	0.1310	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1564	398	437	4.24	0.0385	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1565	399	400	3.00	0.0496	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1566	399	439	3.00	0.0391	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1567	399	440	4.24	0.1142	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1568	399	438	4.24	0.0198	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1569	400	440	3.00	0.1158	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1570	400	439	4.24	0.1012	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1571	401	402	3.00	0.0830	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1572	401	441	3.00	0.1934	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1573	401	442	4.24	0.1130	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1574	402	403	3.00	0.0469	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1575	402	442	3.00	0.1398	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1576	402	443	4.24	0.0442	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1577	402	441	4.24	0.1552	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1578	403	404	3.00	0.1488	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1579	403	443	3.00	0.0595	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1580	403	444	4.24	0.0141	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1581	403	442	4.24	0.0698	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1582	404	405	3.00	0.1321	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1583	404	444	3.00	0.0869	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1584	404	445	4.24	0.0440	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1585	404	443	4.24	0.1286	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1586	405	406	3.00	0.1527	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1587	405	445	3.00	0.0776	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1588	405	446	4.24	0.0675	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1589	405	444	4.24	0.0154	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1590	406	407	3.00	0.1836	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1591	406	446	3.00	0.1559	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1592	406	447	4.24	0.1390	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1593	406	445	4.24	0.1038	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1594	407	408	3.00	0.0644	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1595	407	447	3.00	0.1996	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1596	407	448	4.24	0.1607	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1597	407	446	4.24	0.1391	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1598	408	409	3.00	0.0898	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1599	408	448	3.00	0.0154	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1600	408	449	4.24	0.0935	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1601	408	447	4.24	0.1499	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1602	409	410	3.00	0.0392	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1603	409	449	3.00	0.1286	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1604	409	450	4.24	0.1442	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1605	409	448	4.24	0.0237	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1606	410	411	3.00	0.1566	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1607	410	450	3.00	0.0873	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1608	410	451	4.24	0.0705	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1609	410	449	4.24	0.1568	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1610	411	412	3.00	0.1523	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1611	411	451	3.00	0.1468	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1612	411	452	4.24	0.1146	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1613	411	450	4.24	0.1071	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1614	412	413	3.00	0.1028	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1615	412	452	3.00	0.0224	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1616	412	453	4.24	0.0171	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1617	412	451	4.24	0.0554	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1618	413	414	3.00	0.0254	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1619	413	453	3.00	0.0203	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1620	413	454	4.24	0.0398	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1621	413	452	4.24	0.1271	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1622	414	415	3.00	0.0815	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1623	414	454	3.00	0.0913	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1624	414	455	4.24	0.0326	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1625	414	453	4.24	0.1175	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1626	415	416	3.00	0.1069	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1627	415	455	3.00	0.1632	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1628	415	456	4.24	0.1414	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1629	415	454	4.24	0.0862	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1630	416	417	3.00	0.1806	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1631	416	456	3.00	0.1555	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1632	416	457	4.24	0.0718	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1633	416	455	4.24	0.0596	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1634	417	418	3.00	0.0597	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1635	417	457	3.00	0.0238	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1636	417	458	4.24	0.1205	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1637	417	456	4.24	0.0565	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1638	418	419	3.00	0.0947	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1639	418	458	3.00	0.0166	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1640	418	459	4.24	0.1963	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1641	418	457	4.24	0.1577	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1642	419	420	3.00	0.0971	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1643	419	459	3.00	0.1714	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1644	419	460	4.24	0.0573	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1645	419	458	4.24	0.0769	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1646	420	421	3.00	0.0942	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1647	420	460	3.00	0.1602	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1648	420	461	4.24	0.1482	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1649	420	459	4.24	0.0944	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1650	421	422	3.00	0.0906	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1651	421	461	3.00	0.1710	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1652	421	462	4.24	0.1727	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1653	421	460	4.24	0.1790	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1654	422	423	3.00	0.1603	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1655	422	462	3.00	0.0342	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1656	422	463	4.24	0.1013	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1657	422	461	4.24	0.1481	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1658	423	424	3.00	0.0377	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1659	423	463	3.00	0.0449	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1660	423	464	4.24	0.0582	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1661	423	462	4.24	0.1671	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1662	424	425	3.00	0.1523	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1663	424	464	3.00	0.0774	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1664	424	465	4.24	0.1581	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1665	424	463	4.24	0.1500	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1666	425	426	3.00	0.0732	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1667	425	465	3.00	0.1230	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1668	425	466	4.24	0.0451	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1669	425	464	4.24	0.1610	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1670	426	427	3.00	0.0687	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1671	426	466	3.00	0.1018	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1672	426	467	4.24	0.1150	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1673	426	465	4.24	0.0383	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1674	427	428	3.00	0.0107	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1675	427	467	3.00	0.1480	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1676	427	468	4.24	0.1219	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1677	427	466	4.24	0.0454	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1678	428	429	3.00	0.0349	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1679	428	468	3.00	0.1924	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1680	428	469	4.24	0.0731	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1681	428	467	4.24	0.0497	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1682	429	430	3.00	0.1971	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1683	429	469	3.00	0.0607	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1684	429	470	4.24	0.1114	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1685	429	468	4.24	0.1006	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1686	430	431	3.00	0.0140	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1687	430	470	3.00	0.1103	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1688	430	471	4.24	0.0511	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1689	430	469	4.24	0.1475	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1690	431	432	3.00	0.1738	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1691	431	471	3.00	0.0539	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1692	431	472	4.24	0.0294	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1693	431	470	4.24	0.0370	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1694	432	433	3.00	0.1689	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1695	432	472	3.00	0.0183	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1696	432	473	4.24	0.1551	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1697	432	471	4.24	0.0306	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1698	433	434	3.00	0.0707	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1699	433	473	3.00	0.0740	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1700	433	474	4.24	0.1349	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1701	433	472	4.24	0.1429	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1702	434	435	3.00	0.0477	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1703	434	474	3.00	0.1259	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1704	434	475	4.24	0.1741	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1705	434	473	4.24	0.0510	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1706	435	436	3.00	0.0899	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1707	435	475	3.00	0.1265	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1708	435	476	4.24	0.1496	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1709	435	474	4.24	0.1042	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1710	436	437	3.00	0.1713	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1711	436	476	3.00	0.1989	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1712	436	477	4.24	0.1222	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1713	436	475	4.24	0.0342	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1714	437	438	3.00	0.1826	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1715	437	477	3.00	0.0896	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1716	437	478	4.24	0.1177	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1717	437	476	4.24	0.0686	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1718	438	439	3.00	0.0617	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1719	438	478	3.00	0.1137	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1720	438	479	4.24	0.0153	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1721	438	477	4.24	0.1705	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1722	439	440	3.00	0.1826	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1723	439	479	3.00	0.1570	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1724	439	480	4.24	0.1603	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1725	439	478	4.24	0.1787	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1726	440	480	3.00	0.1426	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1727	440	479	4.24	0.1093	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1728	441	442	3.00	0.0716	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1729	441	481	3.00	0.1698	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1730	441	482	4.24	0.0512	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1731	442	443	3.00	0.1181	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1732	442	482	3.00	0.1316	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1733	442	483	4.24	0.0840	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1734	442	481	4.24	0.1923	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1735	443	444	3.00	0.1228	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1736	443	483	3.00	0.0325	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1737	443	484	4.24	0.0837	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1738	443	482	4.24	0.0457	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1739	444	445	3.00	0.0928	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1740	444	484	3.00	0.1252	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1741	444	485	4.24	0.0565	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1742	444	483	4.24	0.1569	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1743	445	446	3.00	0.0503	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1744	445	485	3.00	0.1454	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1745	445	486	4.24	0.1920	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1746	445	484	4.24	0.1789	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1747	446	447	3.00	0.0649	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1748	446	486	3.00	0.0623	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1749	446	487	4.24	0.0245	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1750	446	485	4.24	0.1127	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1751	447	448	3.00	0.1603	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1752	447	487	3.00	0.1384	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1753	447	488	4.24	0.0482	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1754	447	486	4.24	0.0462	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1755	448	449	3.00	0.1088	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1756	448	488	3.00	0.0439	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1757	448	489	4.24	0.1243	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1758	448	487	4.24	0.1269	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1759	449	450	3.00	0.1031	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1760	449	489	3.00	0.1181	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1761	449	490	4.24	0.1265	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1762	449	488	4.24	0.0388	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1763	450	451	3.00	0.1584	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1764	450	490	3.00	0.1761	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1765	450	491	4.24	0.1347	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1766	450	489	4.24	0.0508	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1767	451	452	3.00	0.1592	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1768	451	491	3.00	0.1998	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1769	451	492	4.24	0.1916	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1770	451	490	4.24	0.0863	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1771	452	453	3.00	0.0265	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1772	452	492	3.00	0.0662	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1773	452	493	4.24	0.1101	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1774	452	491	4.24	0.1481	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1775	453	454	3.00	0.0838	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1776	453	493	3.00	0.1641	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1777	453	494	4.24	0.1769	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1778	453	492	4.24	0.1545	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1779	454	455	3.00	0.0867	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1780	454	494	3.00	0.0619	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1781	454	495	4.24	0.1014	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1782	454	493	4.24	0.0326	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1783	455	456	3.00	0.1112	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1784	455	495	3.00	0.0741	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1785	455	496	4.24	0.0335	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1786	455	494	4.24	0.1907	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1787	456	457	3.00	0.1707	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1788	456	496	3.00	0.0763	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1789	456	497	4.24	0.0810	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1790	456	495	4.24	0.0639	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1791	457	458	3.00	0.0590	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1792	457	497	3.00	0.0829	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1793	457	498	4.24	0.0920	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1794	457	496	4.24	0.0316	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1795	458	459	3.00	0.0301	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1796	458	498	3.00	0.1526	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1797	458	499	4.24	0.0643	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1798	458	497	4.24	0.0228	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1799	459	460	3.00	0.1365	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1800	459	499	3.00	0.0269	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1801	459	500	4.24	0.1523	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1802	459	498	4.24	0.0381	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1803	460	461	3.00	0.0606	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1804	460	500	3.00	0.1659	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1805	460	501	4.24	0.0783	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1806	460	499	4.24	0.0284	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1807	461	462	3.00	0.1151	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1808	461	501	3.00	0.1149	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1809	461	502	4.24	0.0570	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1810	461	500	4.24	0.0294	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1811	462	463	3.00	0.0799	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1812	462	502	3.00	0.1717	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1813	462	503	4.24	0.0421	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1814	462	501	4.24	0.1603	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1815	463	464	3.00	0.0560	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1816	463	503	3.00	0.0784	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1817	463	504	4.24	0.0404	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1818	463	502	4.24	0.0313	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1819	464	465	3.00	0.0539	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1820	464	504	3.00	0.0825	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1821	464	505	4.24	0.1253	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1822	464	503	4.24	0.0917	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1823	465	466	3.00	0.0891	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1824	465	505	3.00	0.0962	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1825	465	506	4.24	0.1410	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1826	465	504	4.24	0.0896	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1827	466	467	3.00	0.0593	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1828	466	506	3.00	0.0860	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1829	466	507	4.24	0.0607	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1830	466	505	4.24	0.1108	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1831	467	468	3.00	0.0290	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1832	467	507	3.00	0.1321	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1833	467	508	4.24	0.1556	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1834	467	506	4.24	0.0979	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1835	468	469	3.00	0.0199	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1836	468	508	3.00	0.0299	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1837	468	509	4.24	0.0225	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1838	468	507	4.24	0.1081	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1839	469	470	3.00	0.0143	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1840	469	509	3.00	0.1468	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1841	469	510	4.24	0.0376	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1842	469	508	4.24	0.1416	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1843	470	471	3.00	0.0323	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1844	470	510	3.00	0.1578	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1845	470	511	4.24	0.1942	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1846	470	509	4.24	0.1361	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1847	471	472	3.00	0.0168	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1848	471	511	3.00	0.1612	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1849	471	512	4.24	0.0541	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1850	471	510	4.24	0.0976	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1851	472	473	3.00	0.0683	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1852	472	512	3.00	0.1461	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1853	472	513	4.24	0.1378	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1854	472	511	4.24	0.1180	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1855	473	474	3.00	0.1133	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1856	473	513	3.00	0.0706	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1857	473	514	4.24	0.1496	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1858	473	512	4.24	0.1723	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1859	474	475	3.00	0.1851	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1860	474	514	3.00	0.1608	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1861	474	515	4.24	0.0636	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1862	474	513	4.24	0.0766	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1863	475	476	3.00	0.1364	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1864	475	515	3.00	0.0230	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1865	475	516	4.24	0.0330	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1866	475	514	4.24	0.0865	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1867	476	477	3.00	0.0858	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1868	476	516	3.00	0.0879	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1869	476	517	4.24	0.1462	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1870	476	515	4.24	0.0864	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1871	477	478	3.00	0.0199	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1872	477	517	3.00	0.1912	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1873	477	518	4.24	0.1490	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1874	477	516	4.24	0.0136	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1875	478	479	3.00	0.1358	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1876	478	518	3.00	0.0226	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1877	478	519	4.24	0.1252	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1878	478	517	4.24	0.0538	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1879	479	480	3.00	0.1675	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1880	479	519	3.00	0.1054	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1881	479	520	4.24	0.1802	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1882	479	518	4.24	0.1260	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1883	480	520	3.00	0.1653	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1884	480	519	4.24	0.1948	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1885	481	482	3.00	0.0383	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1886	481	521	3.00	0.0132	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1887	481	522	4.24	0.1147	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1888	482	483	3.00	0.1801	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1889	482	522	3.00	0.1794	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1890	482	523	4.24	0.1210	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1891	482	521	4.24	0.0827	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1892	483	484	3.00	0.1402	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1893	483	523	3.00	0.1185	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1894	483	524	4.24	0.1539	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1895	483	522	4.24	0.0998	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1896	484	485	3.00	0.1021	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1897	484	524	3.00	0.0335	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1898	484	525	4.24	0.1352	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1899	484	523	4.24	0.1916	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1900	485	486	3.00	0.0684	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1901	485	525	3.00	0.0441	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1902	485	526	4.24	0.0647	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1903	485	524	4.24	0.0792	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1904	486	487	3.00	0.0272	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1905	486	526	3.00	0.0665	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1906	486	527	4.24	0.1141	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1907	486	525	4.24	0.1109	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1908	487	488	3.00	0.1176	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1909	487	527	3.00	0.0206	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1910	487	528	4.24	0.0376	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1911	487	526	4.24	0.1588	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1912	488	489	3.00	0.1964	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1913	488	528	3.00	0.1860	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1914	488	529	4.24	0.1313	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1915	488	527	4.24	0.0378	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1916	489	490	3.00	0.1711	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1917	489	529	3.00	0.0982	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1918	489	530	4.24	0.1774	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1919	489	528	4.24	0.0179	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1920	490	491	3.00	0.0186	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1921	490	530	3.00	0.0348	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1922	490	531	4.24	0.1336	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1923	490	529	4.24	0.0587	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1924	491	492	3.00	0.1053	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1925	491	531	3.00	0.1140	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1926	491	532	4.24	0.0790	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1927	491	530	4.24	0.0502	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1928	492	493	3.00	0.1766	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1929	492	532	3.00	0.0543	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1930	492	533	4.24	0.1875	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1931	492	531	4.24	0.0365	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1932	493	494	3.00	0.1362	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1933	493	533	3.00	0.0309	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1934	493	534	4.24	0.1111	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1935	493	532	4.24	0.1897	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1936	494	495	3.00	0.0641	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1937	494	534	3.00	0.1753	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1938	494	535	4.24	0.0859	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1939	494	533	4.24	0.0717	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1940	495	496	3.00	0.1629	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1941	495	535	3.00	0.1815	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1942	495	536	4.24	0.1911	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1943	495	534	4.24	0.1970	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1944	496	497	3.00	0.1388	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1945	496	536	3.00	0.1820	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1946	496	537	4.24	0.0958	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1947	496	535	4.24	0.1549	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1948	497	498	3.00	0.0801	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1949	497	537	3.00	0.1577	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1950	497	538	4.24	0.1283	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1951	497	536	4.24	0.0656	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1952	498	499	3.00	0.0708	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1953	498	538	3.00	0.1379	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1954	498	539	4.24	0.1966	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1955	498	537	4.24	0.0881	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1956	499	500	3.00	0.1928	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1957	499	539	3.00	0.1799	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1958	499	540	4.24	0.0635	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1959	499	538	4.24	0.0924	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1960	500	501	3.00	0.0963	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1961	500	540	3.00	0.1767	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1962	500	541	4.24	0.1330	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1963	500	539	4.24	0.0923	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1964	501	502	3.00	0.1651	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1965	501	541	3.00	0.1808	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1966	501	542	4.24	0.0475	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1967	501	540	4.24	0.0466	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1968	502	503	3.00	0.1371	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1969	502	542	3.00	0.0105	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1970	502	543	4.24	0.1046	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1971	502	541	4.24	0.0909	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1972	503	504	3.00	0.0343	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1973	503	543	3.00	0.0447	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1974	503	544	4.24	0.0111	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1975	503	542	4.24	0.0707	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1976	504	505	3.00	0.1253	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1977	504	544	3.00	0.0953	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1978	504	545	4.24	0.0977	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1979	504	543	4.24	0.0943	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1980	505	506	3.00	0.1649	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1981	505	545	3.00	0.1462	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1982	505	546	4.24	0.0947	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1983	505	544	4.24	0.1722	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1984	506	507	3.00	0.1415	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1985	506	546	3.00	0.0858	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1986	506	547	4.24	0.0386	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1987	506	545	4.24	0.1329	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1988	507	508	3.00	0.0745	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1989	507	547	3.00	0.1875	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1990	507	548	4.24	0.1727	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
1991	507	546	4.24	0.1864	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1992	508	509	3.00	0.1948	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1993	508	548	3.00	0.1632	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1994	508	549	4.24	0.0616	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1995	508	547	4.24	0.1548	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1996	509	510	3.00	0.0745	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
1997	509	549	3.00	0.1171	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
1998	509	550	4.24	0.1473	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
1999	509	548	4.24	0.0293	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2000	510	511	3.00	0.0112	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2001	510	550	3.00	0.0695	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2002	510	551	4.24	0.0590	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2003	510	549	4.24	0.1146	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2004	511	512	3.00	0.1752	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2005	511	551	3.00	0.1776	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2006	511	552	4.24	0.0513	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2007	511	550	4.24	0.1048	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2008	512	513	3.00	0.0873	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2009	512	552	3.00	0.0880	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2010	512	553	4.24	0.1322	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2011	512	551	4.24	0.0536	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2012	513	514	3.00	0.0204	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2013	513	553	3.00	0.0816	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2014	513	554	4.24	0.1904	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2015	513	552	4.24	0.0436	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2016	514	515	3.00	0.0695	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2017	514	554	3.00	0.0279	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2018	514	555	4.24	0.0661	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2019	514	553	4.24	0.1724	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2020	515	516	3.00	0.0405	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2021	515	555	3.00	0.1760	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2022	515	556	4.24	0.0861	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2023	515	554	4.24	0.0734	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2024	516	517	3.00	0.1201	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2025	516	556	3.00	0.0693	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2026	516	557	4.24	0.0983	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2027	516	555	4.24	0.0569	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2028	517	518	3.00	0.0421	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2029	517	557	3.00	0.0401	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2030	517	558	4.24	0.0509	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2031	517	556	4.24	0.0920	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2032	518	519	3.00	0.1399	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2033	518	558	3.00	0.0429	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2034	518	559	4.24	0.1773	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2035	518	557	4.24	0.1493	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2036	519	520	3.00	0.0149	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2037	519	559	3.00	0.0350	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2038	519	560	4.24	0.1274	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2039	519	558	4.24	0.0770	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2040	520	560	3.00	0.1161	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2041	520	559	4.24	0.0330	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2042	521	522	3.00	0.1458	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2043	521	561	3.00	0.1120	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2044	521	562	4.24	0.1962	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2045	522	523	3.00	0.1969	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2046	522	562	3.00	0.0830	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2047	522	563	4.24	0.1641	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2048	522	561	4.24	0.1704	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2049	523	524	3.00	0.0706	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2050	523	563	3.00	0.1761	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2051	523	564	4.24	0.1197	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2052	523	562	4.24	0.0309	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2053	524	525	3.00	0.1586	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2054	524	564	3.00	0.1505	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2055	524	565	4.24	0.1168	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2056	524	563	4.24	0.0854	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2057	525	526	3.00	0.1286	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2058	525	565	3.00	0.0320	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2059	525	566	4.24	0.0859	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2060	525	564	4.24	0.0638	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2061	526	527	3.00	0.0172	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2062	526	566	3.00	0.1635	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2063	526	567	4.24	0.0645	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2064	526	565	4.24	0.1545	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2065	527	528	3.00	0.0292	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2066	527	567	3.00	0.0394	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2067	527	568	4.24	0.0933	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2068	527	566	4.24	0.1994	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2069	528	529	3.00	0.1564	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2070	528	568	3.00	0.0957	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2071	528	569	4.24	0.0153	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2072	528	567	4.24	0.1245	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2073	529	530	3.00	0.0484	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2074	529	569	3.00	0.1023	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2075	529	570	4.24	0.1802	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2076	529	568	4.24	0.0716	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2077	530	531	3.00	0.1090	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2078	530	570	3.00	0.1280	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2079	530	571	4.24	0.1789	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2080	530	569	4.24	0.1768	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2081	531	532	3.00	0.0276	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2082	531	571	3.00	0.0887	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2083	531	572	4.24	0.1493	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2084	531	570	4.24	0.0563	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2085	532	533	3.00	0.1093	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2086	532	572	3.00	0.1615	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2087	532	573	4.24	0.0925	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2088	532	571	4.24	0.1453	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2089	533	534	3.00	0.1661	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2090	533	573	3.00	0.0853	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2091	533	574	4.24	0.1946	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2092	533	572	4.24	0.1599	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2093	534	535	3.00	0.1295	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2094	534	574	3.00	0.0438	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2095	534	575	4.24	0.1915	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2096	534	573	4.24	0.1185	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2097	535	536	3.00	0.1564	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2098	535	575	3.00	0.1348	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2099	535	576	4.24	0.0504	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2100	535	574	4.24	0.1188	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2101	536	537	3.00	0.0323	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2102	536	576	3.00	0.1548	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2103	536	577	4.24	0.0414	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2104	536	575	4.24	0.1360	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2105	537	538	3.00	0.0886	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2106	537	577	3.00	0.0663	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2107	537	578	4.24	0.0691	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2108	537	576	4.24	0.1474	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2109	538	539	3.00	0.0391	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2110	538	578	3.00	0.1937	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2111	538	579	4.24	0.0400	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2112	538	577	4.24	0.1454	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2113	539	540	3.00	0.0512	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2114	539	579	3.00	0.1157	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2115	539	580	4.24	0.1141	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2116	539	578	4.24	0.0903	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2117	540	541	3.00	0.1041	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2118	540	580	3.00	0.0390	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2119	540	581	4.24	0.1699	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2120	540	579	4.24	0.0809	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2121	541	542	3.00	0.0749	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2122	541	581	3.00	0.1571	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2123	541	582	4.24	0.0818	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2124	541	580	4.24	0.0819	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2125	542	543	3.00	0.1682	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2126	542	582	3.00	0.0714	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2127	542	583	4.24	0.1506	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2128	542	581	4.24	0.0260	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2129	543	544	3.00	0.1212	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2130	543	583	3.00	0.1738	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2131	543	584	4.24	0.1735	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2132	543	582	4.24	0.1641	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2133	544	545	3.00	0.0635	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2134	544	584	3.00	0.0236	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2135	544	585	4.24	0.1799	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2136	544	583	4.24	0.1815	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2137	545	546	3.00	0.1742	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2138	545	585	3.00	0.0389	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2139	545	586	4.24	0.1757	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2140	545	584	4.24	0.0390	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2141	546	547	3.00	0.1793	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2142	546	586	3.00	0.0545	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2143	546	587	4.24	0.1931	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2144	546	585	4.24	0.0609	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2145	547	548	3.00	0.1905	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2146	547	587	3.00	0.1657	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2147	547	588	4.24	0.1863	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2148	547	586	4.24	0.1739	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2149	548	549	3.00	0.0488	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2150	548	588	3.00	0.0725	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2151	548	589	4.24	0.0960	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2152	548	587	4.24	0.0480	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2153	549	550	3.00	0.1138	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2154	549	589	3.00	0.1698	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2155	549	590	4.24	0.0620	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2156	549	588	4.24	0.0161	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2157	550	551	3.00	0.0410	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2158	550	590	3.00	0.1646	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2159	550	591	4.24	0.1956	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2160	550	589	4.24	0.1799	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2161	551	552	3.00	0.1093	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2162	551	591	3.00	0.0731	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2163	551	592	4.24	0.1074	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2164	551	590	4.24	0.0357	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2165	552	553	3.00	0.0701	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2166	552	592	3.00	0.1059	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2167	552	593	4.24	0.1734	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2168	552	591	4.24	0.0707	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2169	553	554	3.00	0.1592	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2170	553	593	3.00	0.1333	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2171	553	594	4.24	0.0903	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2172	553	592	4.24	0.0202	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2173	554	555	3.00	0.0380	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2174	554	594	3.00	0.0727	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2175	554	595	4.24	0.0521	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2176	554	593	4.24	0.0412	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2177	555	556	3.00	0.0608	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2178	555	595	3.00	0.0120	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2179	555	596	4.24	0.1387	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2180	555	594	4.24	0.1553	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2181	556	557	3.00	0.1029	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2182	556	596	3.00	0.1593	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2183	556	597	4.24	0.1672	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2184	556	595	4.24	0.0692	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2185	557	558	3.00	0.0194	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2186	557	597	3.00	0.1369	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2187	557	598	4.24	0.1673	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2188	557	596	4.24	0.0212	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2189	558	559	3.00	0.1685	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2190	558	598	3.00	0.0683	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2191	558	599	4.24	0.1898	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2192	558	597	4.24	0.1597	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2193	559	560	3.00	0.1167	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2194	559	599	3.00	0.0280	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2195	559	600	4.24	0.1856	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2196	559	598	4.24	0.0370	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2197	560	600	3.00	0.0997	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2198	560	599	4.24	0.0194	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2199	561	562	3.00	0.0383	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2200	561	601	3.00	0.0555	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2201	561	602	4.24	0.1655	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2202	562	563	3.00	0.0281	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2203	562	602	3.00	0.0434	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2204	562	603	4.24	0.1365	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2205	562	601	4.24	0.0277	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2206	563	564	3.00	0.0681	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2207	563	603	3.00	0.1112	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2208	563	604	4.24	0.0210	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2209	563	602	4.24	0.1393	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2210	564	565	3.00	0.1577	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2211	564	604	3.00	0.0253	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2212	564	605	4.24	0.0312	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2213	564	603	4.24	0.0571	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2214	565	566	3.00	0.0948	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2215	565	605	3.00	0.0581	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2216	565	606	4.24	0.0126	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2217	565	604	4.24	0.1488	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2218	566	567	3.00	0.0578	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2219	566	606	3.00	0.0945	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2220	566	607	4.24	0.1660	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2221	566	605	4.24	0.1340	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2222	567	568	3.00	0.1341	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2223	567	607	3.00	0.0862	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2224	567	608	4.24	0.0291	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2225	567	606	4.24	0.0409	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2226	568	569	3.00	0.0545	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2227	568	608	3.00	0.0975	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2228	568	609	4.24	0.0689	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2229	568	607	4.24	0.0224	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2230	569	570	3.00	0.1020	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2231	569	609	3.00	0.1622	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2232	569	610	4.24	0.1983	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2233	569	608	4.24	0.0940	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2234	570	571	3.00	0.1704	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2235	570	610	3.00	0.0933	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2236	570	611	4.24	0.0346	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2237	570	609	4.24	0.0659	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2238	571	572	3.00	0.1338	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2239	571	611	3.00	0.1261	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2240	571	612	4.24	0.0662	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2241	571	610	4.24	0.0661	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2242	572	573	3.00	0.1673	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2243	572	612	3.00	0.0110	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2244	572	613	4.24	0.1634	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2245	572	611	4.24	0.1332	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2246	573	574	3.00	0.0723	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2247	573	613	3.00	0.0915	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2248	573	614	4.24	0.0187	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2249	573	612	4.24	0.1291	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2250	574	575	3.00	0.0580	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2251	574	614	3.00	0.1644	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2252	574	615	4.24	0.1284	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2253	574	613	4.24	0.1477	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2254	575	576	3.00	0.1547	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2255	575	615	3.00	0.0219	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2256	575	616	4.24	0.1578	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2257	575	614	4.24	0.0647	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2258	576	577	3.00	0.1263	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2259	576	616	3.00	0.1733	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2260	576	617	4.24	0.0483	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2261	576	615	4.24	0.1895	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2262	577	578	3.00	0.1257	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2263	577	617	3.00	0.1939	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2264	577	618	4.24	0.0289	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2265	577	616	4.24	0.1355	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2266	578	579	3.00	0.1248	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2267	578	618	3.00	0.1649	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2268	578	619	4.24	0.0608	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2269	578	617	4.24	0.0233	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2270	579	580	3.00	0.0102	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2271	579	619	3.00	0.0127	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2272	579	620	4.24	0.1585	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2273	579	618	4.24	0.1866	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2274	580	581	3.00	0.1367	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2275	580	620	3.00	0.0666	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2276	580	621	4.24	0.0814	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2277	580	619	4.24	0.0722	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2278	581	582	3.00	0.0748	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2279	581	621	3.00	0.1657	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2280	581	622	4.24	0.1253	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2281	581	620	4.24	0.1576	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2282	582	583	3.00	0.1516	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2283	582	622	3.00	0.0144	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2284	582	623	4.24	0.1301	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2285	582	621	4.24	0.0625	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2286	583	584	3.00	0.1370	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2287	583	623	3.00	0.0264	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2288	583	624	4.24	0.1619	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2289	583	622	4.24	0.0454	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2290	584	585	3.00	0.1671	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2291	584	624	3.00	0.0840	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2292	584	625	4.24	0.1293	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2293	584	623	4.24	0.1333	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2294	585	586	3.00	0.0992	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2295	585	625	3.00	0.1014	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2296	585	626	4.24	0.1715	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2297	585	624	4.24	0.0180	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2298	586	587	3.00	0.0469	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2299	586	626	3.00	0.1915	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2300	586	627	4.24	0.1558	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2301	586	625	4.24	0.0597	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2302	587	588	3.00	0.0750	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2303	587	627	3.00	0.0849	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2304	587	628	4.24	0.0760	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2305	587	626	4.24	0.0632	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2306	588	589	3.00	0.1444	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2307	588	628	3.00	0.0372	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2308	588	629	4.24	0.0141	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2309	588	627	4.24	0.0440	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2310	589	590	3.00	0.1146	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2311	589	629	3.00	0.1771	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2312	589	630	4.24	0.1581	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2313	589	628	4.24	0.0476	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2314	590	591	3.00	0.1682	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2315	590	630	3.00	0.1632	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2316	590	631	4.24	0.1594	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2317	590	629	4.24	0.0968	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2318	591	592	3.00	0.0730	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2319	591	631	3.00	0.0256	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2320	591	632	4.24	0.1712	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2321	591	630	4.24	0.0333	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2322	592	593	3.00	0.0887	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2323	592	632	3.00	0.0293	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2324	592	633	4.24	0.1321	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2325	592	631	4.24	0.0228	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2326	593	594	3.00	0.1446	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2327	593	633	3.00	0.1606	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2328	593	634	4.24	0.0720	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2329	593	632	4.24	0.1561	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2330	594	595	3.00	0.1007	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2331	594	634	3.00	0.1341	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2332	594	635	4.24	0.1016	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2333	594	633	4.24	0.1100	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2334	595	596	3.00	0.0732	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2335	595	635	3.00	0.0170	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2336	595	636	4.24	0.1361	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2337	595	634	4.24	0.0421	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2338	596	597	3.00	0.1788	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2339	596	636	3.00	0.1158	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2340	596	637	4.24	0.0761	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2341	596	635	4.24	0.1652	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2342	597	598	3.00	0.1654	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2343	597	637	3.00	0.0896	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2344	597	638	4.24	0.0847	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2345	597	636	4.24	0.1247	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2346	598	599	3.00	0.1924	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2347	598	638	3.00	0.1006	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2348	598	639	4.24	0.1126	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2349	598	637	4.24	0.1671	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2350	599	600	3.00	0.0933	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2351	599	639	3.00	0.1985	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2352	599	640	4.24	0.1328	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2353	599	638	4.24	0.1137	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2354	600	640	3.00	0.1562	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2355	600	639	4.24	0.0971	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2356	601	602	3.00	0.1955	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2357	601	641	3.00	0.0346	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2358	601	642	4.24	0.1812	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2359	602	603	3.00	0.0961	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2360	602	642	3.00	0.0648	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2361	602	643	4.24	0.1189	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2362	602	641	4.24	0.1467	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2363	603	604	3.00	0.1184	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2364	603	643	3.00	0.0968	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2365	603	644	4.24	0.0556	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2366	603	642	4.24	0.0472	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2367	604	605	3.00	0.0411	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2368	604	644	3.00	0.1268	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2369	604	645	4.24	0.0672	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2370	604	643	4.24	0.0729	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2371	605	606	3.00	0.1715	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2372	605	645	3.00	0.0491	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2373	605	646	4.24	0.1307	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2374	605	644	4.24	0.0265	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2375	606	607	3.00	0.1122	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2376	606	646	3.00	0.1205	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2377	606	647	4.24	0.0700	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2378	606	645	4.24	0.0266	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2379	607	608	3.00	0.1977	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2380	607	647	3.00	0.1504	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2381	607	648	4.24	0.1678	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2382	607	646	4.24	0.0527	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2383	608	609	3.00	0.1474	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2384	608	648	3.00	0.1038	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2385	608	649	4.24	0.0357	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2386	608	647	4.24	0.1778	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2387	609	610	3.00	0.1720	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2388	609	649	3.00	0.1707	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2389	609	650	4.24	0.1366	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2390	609	648	4.24	0.1603	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2391	610	611	3.00	0.0464	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2392	610	650	3.00	0.1413	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2393	610	651	4.24	0.0921	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2394	610	649	4.24	0.0695	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2395	611	612	3.00	0.0503	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2396	611	651	3.00	0.1067	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2397	611	652	4.24	0.1454	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2398	611	650	4.24	0.1597	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2399	612	613	3.00	0.0135	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2400	612	652	3.00	0.1333	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2401	612	653	4.24	0.0831	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2402	612	651	4.24	0.0119	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2403	613	614	3.00	0.0476	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2404	613	653	3.00	0.1316	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2405	613	654	4.24	0.1619	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2406	613	652	4.24	0.1287	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2407	614	615	3.00	0.0822	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2408	614	654	3.00	0.0324	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2409	614	655	4.24	0.0421	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2410	614	653	4.24	0.1432	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2411	615	616	3.00	0.0766	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2412	615	655	3.00	0.0475	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2413	615	656	4.24	0.1876	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2414	615	654	4.24	0.1175	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2415	616	617	3.00	0.0811	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2416	616	656	3.00	0.0461	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2417	616	657	4.24	0.0420	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2418	616	655	4.24	0.0144	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2419	617	618	3.00	0.1190	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2420	617	657	3.00	0.0838	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2421	617	658	4.24	0.0685	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2422	617	656	4.24	0.0348	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2423	618	619	3.00	0.1930	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2424	618	658	3.00	0.0480	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2425	618	659	4.24	0.1450	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2426	618	657	4.24	0.0663	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2427	619	620	3.00	0.0124	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2428	619	659	3.00	0.1072	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2429	619	660	4.24	0.1535	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2430	619	658	4.24	0.1745	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2431	620	621	3.00	0.0257	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2432	620	660	3.00	0.1779	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2433	620	661	4.24	0.1437	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2434	620	659	4.24	0.0496	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2435	621	622	3.00	0.1630	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2436	621	661	3.00	0.0172	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2437	621	662	4.24	0.0549	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2438	621	660	4.24	0.0204	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2439	622	623	3.00	0.1014	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2440	622	662	3.00	0.0600	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2441	622	663	4.24	0.1307	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2442	622	661	4.24	0.0385	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2443	623	624	3.00	0.1107	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2444	623	663	3.00	0.0235	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2445	623	664	4.24	0.1369	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2446	623	662	4.24	0.1728	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2447	624	625	3.00	0.1914	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2448	624	664	3.00	0.1465	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2449	624	665	4.24	0.0810	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2450	624	663	4.24	0.1539	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2451	625	626	3.00	0.0463	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2452	625	665	3.00	0.0962	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2453	625	666	4.24	0.0202	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2454	625	664	4.24	0.0591	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2455	626	627	3.00	0.0642	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2456	626	666	3.00	0.0703	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2457	626	667	4.24	0.1174	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2458	626	665	4.24	0.1150	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2459	627	628	3.00	0.0478	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2460	627	667	3.00	0.0468	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2461	627	668	4.24	0.1307	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2462	627	666	4.24	0.1985	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2463	628	629	3.00	0.1044	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2464	628	668	3.00	0.1924	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2465	628	669	4.24	0.0173	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2466	628	667	4.24	0.1625	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2467	629	630	3.00	0.1505	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2468	629	669	3.00	0.0854	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2469	629	670	4.24	0.0434	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2470	629	668	4.24	0.1329	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2471	630	631	3.00	0.1178	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2472	630	670	3.00	0.1747	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2473	630	671	4.24	0.1643	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2474	630	669	4.24	0.0116	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2475	631	632	3.00	0.0660	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2476	631	671	3.00	0.1501	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2477	631	672	4.24	0.0211	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2478	631	670	4.24	0.0930	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2479	632	633	3.00	0.1794	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2480	632	672	3.00	0.1264	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2481	632	673	4.24	0.1949	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2482	632	671	4.24	0.0177	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2483	633	634	3.00	0.1993	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2484	633	673	3.00	0.1429	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2485	633	674	4.24	0.0897	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2486	633	672	4.24	0.1972	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2487	634	635	3.00	0.1191	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2488	634	674	3.00	0.1761	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2489	634	675	4.24	0.0326	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2490	634	673	4.24	0.0655	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2491	635	636	3.00	0.1787	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2492	635	675	3.00	0.0903	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2493	635	676	4.24	0.1285	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2494	635	674	4.24	0.1673	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2495	636	637	3.00	0.1038	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2496	636	676	3.00	0.1404	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2497	636	677	4.24	0.0584	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2498	636	675	4.24	0.0106	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2499	637	638	3.00	0.1433	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2500	637	677	3.00	0.1410	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2501	637	678	4.24	0.0320	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2502	637	676	4.24	0.1783	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2503	638	639	3.00	0.1460	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2504	638	678	3.00	0.0756	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2505	638	679	4.24	0.1713	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2506	638	677	4.24	0.1898	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2507	639	640	3.00	0.1827	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2508	639	679	3.00	0.1860	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2509	639	680	4.24	0.1491	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2510	639	678	4.24	0.0749	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2511	640	680	3.00	0.1667	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2512	640	679	4.24	0.1759	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2513	641	642	3.00	0.0889	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2514	641	681	3.00	0.0350	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2515	641	682	4.24	0.0742	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2516	642	643	3.00	0.1046	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2517	642	682	3.00	0.0358	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2518	642	683	4.24	0.0280	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2519	642	681	4.24	0.1626	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2520	643	644	3.00	0.1344	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2521	643	683	3.00	0.0596	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2522	643	684	4.24	0.1079	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2523	643	682	4.24	0.1290	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2524	644	645	3.00	0.1343	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2525	644	684	3.00	0.0947	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2526	644	685	4.24	0.0473	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2527	644	683	4.24	0.1604	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2528	645	646	3.00	0.0303	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2529	645	685	3.00	0.1764	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2530	645	686	4.24	0.1795	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2531	645	684	4.24	0.1168	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2532	646	647	3.00	0.0164	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2533	646	686	3.00	0.1758	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2534	646	687	4.24	0.0313	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2535	646	685	4.24	0.0273	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2536	647	648	3.00	0.1707	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2537	647	687	3.00	0.1281	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2538	647	688	4.24	0.1426	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2539	647	686	4.24	0.0298	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2540	648	649	3.00	0.0176	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2541	648	688	3.00	0.0467	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2542	648	689	4.24	0.1400	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2543	648	687	4.24	0.0983	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2544	649	650	3.00	0.1827	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2545	649	689	3.00	0.1294	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2546	649	690	4.24	0.0145	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2547	649	688	4.24	0.0352	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2548	650	651	3.00	0.1009	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2549	650	690	3.00	0.0774	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2550	650	691	4.24	0.1546	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2551	650	689	4.24	0.1019	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2552	651	652	3.00	0.0311	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2553	651	691	3.00	0.0608	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2554	651	692	4.24	0.1285	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2555	651	690	4.24	0.1496	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2556	652	653	3.00	0.0877	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2557	652	692	3.00	0.1751	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2558	652	693	4.24	0.1222	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2559	652	691	4.24	0.0314	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2560	653	654	3.00	0.0908	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2561	653	693	3.00	0.1163	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2562	653	694	4.24	0.1602	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2563	653	692	4.24	0.0525	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2564	654	655	3.00	0.1796	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2565	654	694	3.00	0.0827	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2566	654	695	4.24	0.1971	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2567	654	693	4.24	0.1667	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2568	655	656	3.00	0.1403	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2569	655	695	3.00	0.0630	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2570	655	696	4.24	0.1636	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2571	655	694	4.24	0.1445	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2572	656	657	3.00	0.1359	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2573	656	696	3.00	0.0894	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2574	656	697	4.24	0.0492	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2575	656	695	4.24	0.0309	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2576	657	658	3.00	0.0665	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2577	657	697	3.00	0.0969	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2578	657	698	4.24	0.1383	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2579	657	696	4.24	0.1781	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2580	658	659	3.00	0.1647	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2581	658	698	3.00	0.0293	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2582	658	699	4.24	0.0696	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2583	658	697	4.24	0.1601	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2584	659	660	3.00	0.0869	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2585	659	699	3.00	0.0731	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2586	659	700	4.24	0.0942	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2587	659	698	4.24	0.1892	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2588	660	661	3.00	0.1839	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2589	660	700	3.00	0.0771	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2590	660	701	4.24	0.1821	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2591	660	699	4.24	0.1893	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2592	661	662	3.00	0.0467	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2593	661	701	3.00	0.0603	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2594	661	702	4.24	0.1110	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2595	661	700	4.24	0.1206	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2596	662	663	3.00	0.1904	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2597	662	702	3.00	0.1146	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2598	662	703	4.24	0.0534	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2599	662	701	4.24	0.0116	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2600	663	664	3.00	0.1603	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2601	663	703	3.00	0.1833	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2602	663	704	4.24	0.1417	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2603	663	702	4.24	0.0838	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2604	664	665	3.00	0.1305	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2605	664	704	3.00	0.1547	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2606	664	705	4.24	0.0118	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2607	664	703	4.24	0.1660	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2608	665	666	3.00	0.1329	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2609	665	705	3.00	0.1200	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2610	665	706	4.24	0.1190	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2611	665	704	4.24	0.0762	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2612	666	667	3.00	0.0882	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2613	666	706	3.00	0.0782	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2614	666	707	4.24	0.1814	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2615	666	705	4.24	0.0614	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2616	667	668	3.00	0.1253	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2617	667	707	3.00	0.0688	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2618	667	708	4.24	0.1723	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2619	667	706	4.24	0.1133	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2620	668	669	3.00	0.1506	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2621	668	708	3.00	0.0509	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2622	668	709	4.24	0.0209	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2623	668	707	4.24	0.0903	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2624	669	670	3.00	0.1060	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2625	669	709	3.00	0.0283	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2626	669	710	4.24	0.1873	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2627	669	708	4.24	0.1238	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2628	670	671	3.00	0.1268	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2629	670	710	3.00	0.0783	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2630	670	711	4.24	0.0705	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2631	670	709	4.24	0.0211	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2632	671	672	3.00	0.1180	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2633	671	711	3.00	0.1223	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2634	671	712	4.24	0.0321	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2635	671	710	4.24	0.1156	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2636	672	673	3.00	0.0104	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2637	672	712	3.00	0.1081	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2638	672	713	4.24	0.1439	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2639	672	711	4.24	0.1505	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2640	673	674	3.00	0.0651	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2641	673	713	3.00	0.1285	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2642	673	714	4.24	0.1041	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2643	673	712	4.24	0.0608	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2644	674	675	3.00	0.1124	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2645	674	714	3.00	0.1115	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2646	674	715	4.24	0.1835	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2647	674	713	4.24	0.0490	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2648	675	676	3.00	0.1233	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2649	675	715	3.00	0.1690	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2650	675	716	4.24	0.1163	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2651	675	714	4.24	0.0299	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2652	676	677	3.00	0.1404	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2653	676	716	3.00	0.1884	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2654	676	717	4.24	0.1030	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2655	676	715	4.24	0.0556	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2656	677	678	3.00	0.1775	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2657	677	717	3.00	0.0349	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2658	677	718	4.24	0.0731	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2659	677	716	4.24	0.1922	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2660	678	679	3.00	0.0584	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2661	678	718	3.00	0.1167	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2662	678	719	4.24	0.0121	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2663	678	717	4.24	0.1545	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2664	679	680	3.00	0.1735	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2665	679	719	3.00	0.0624	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2666	679	720	4.24	0.1367	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2667	679	718	4.24	0.0871	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2668	680	720	3.00	0.1731	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2669	680	719	4.24	0.1540	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2670	681	682	3.00	0.1615	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2671	681	721	3.00	0.0189	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2672	681	722	4.24	0.1513	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2673	682	683	3.00	0.1576	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2674	682	722	3.00	0.0645	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2675	682	723	4.24	0.0799	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2676	682	721	4.24	0.0387	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2677	683	684	3.00	0.0394	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2678	683	723	3.00	0.0470	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2679	683	724	4.24	0.1815	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2680	683	722	4.24	0.1858	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2681	684	685	3.00	0.1931	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2682	684	724	3.00	0.1438	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2683	684	725	4.24	0.0776	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2684	684	723	4.24	0.0445	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2685	685	686	3.00	0.1515	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2686	685	725	3.00	0.1950	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2687	685	726	4.24	0.0599	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2688	685	724	4.24	0.1562	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2689	686	687	3.00	0.1429	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2690	686	726	3.00	0.0892	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2691	686	727	4.24	0.1413	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2692	686	725	4.24	0.1329	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2693	687	688	3.00	0.1143	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2694	687	727	3.00	0.1216	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2695	687	728	4.24	0.1124	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2696	687	726	4.24	0.0762	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2697	688	689	3.00	0.0756	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2698	688	728	3.00	0.1254	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2699	688	729	4.24	0.1829	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2700	688	727	4.24	0.0531	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2701	689	690	3.00	0.1344	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2702	689	729	3.00	0.0722	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2703	689	730	4.24	0.0927	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2704	689	728	4.24	0.0536	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2705	690	691	3.00	0.1575	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2706	690	730	3.00	0.1517	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2707	690	731	4.24	0.0155	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2708	690	729	4.24	0.1433	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2709	691	692	3.00	0.0204	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2710	691	731	3.00	0.1936	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2711	691	732	4.24	0.0667	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2712	691	730	4.24	0.0454	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2713	692	693	3.00	0.1725	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2714	692	732	3.00	0.1311	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2715	692	733	4.24	0.0490	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2716	692	731	4.24	0.1337	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2717	693	694	3.00	0.1450	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2718	693	733	3.00	0.0578	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2719	693	734	4.24	0.0756	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2720	693	732	4.24	0.0311	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2721	694	695	3.00	0.0855	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2722	694	734	3.00	0.1107	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2723	694	735	4.24	0.0707	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2724	694	733	4.24	0.0240	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2725	695	696	3.00	0.0375	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2726	695	735	3.00	0.0134	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2727	695	736	4.24	0.1610	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2728	695	734	4.24	0.1385	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2729	696	697	3.00	0.0508	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2730	696	736	3.00	0.0287	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2731	696	737	4.24	0.1745	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2732	696	735	4.24	0.1153	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2733	697	698	3.00	0.0501	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2734	697	737	3.00	0.0722	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2735	697	738	4.24	0.1391	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2736	697	736	4.24	0.1723	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2737	698	699	3.00	0.0586	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2738	698	738	3.00	0.0608	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2739	698	739	4.24	0.1380	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2740	698	737	4.24	0.1275	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2741	699	700	3.00	0.1682	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2742	699	739	3.00	0.0450	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2743	699	740	4.24	0.1748	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2744	699	738	4.24	0.0248	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2745	700	701	3.00	0.0895	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2746	700	740	3.00	0.0499	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2747	700	741	4.24	0.0568	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2748	700	739	4.24	0.1760	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2749	701	702	3.00	0.1465	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2750	701	741	3.00	0.1134	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2751	701	742	4.24	0.0749	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2752	701	740	4.24	0.1307	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2753	702	703	3.00	0.1946	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2754	702	742	3.00	0.0678	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2755	702	743	4.24	0.0490	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2756	702	741	4.24	0.1019	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2757	703	704	3.00	0.1046	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2758	703	743	3.00	0.0636	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2759	703	744	4.24	0.1970	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2760	703	742	4.24	0.0211	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2761	704	705	3.00	0.1010	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2762	704	744	3.00	0.0976	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2763	704	745	4.24	0.0827	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2764	704	743	4.24	0.1173	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2765	705	706	3.00	0.1865	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2766	705	745	3.00	0.0842	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2767	705	746	4.24	0.0966	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2768	705	744	4.24	0.1842	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2769	706	707	3.00	0.1690	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2770	706	746	3.00	0.0143	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2771	706	747	4.24	0.1302	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2772	706	745	4.24	0.0155	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2773	707	708	3.00	0.0503	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2774	707	747	3.00	0.0861	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2775	707	748	4.24	0.0160	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2776	707	746	4.24	0.0914	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2777	708	709	3.00	0.1256	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2778	708	748	3.00	0.1860	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2779	708	749	4.24	0.1468	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2780	708	747	4.24	0.0169	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2781	709	710	3.00	0.1901	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2782	709	749	3.00	0.0703	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2783	709	750	4.24	0.0104	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2784	709	748	4.24	0.1483	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2785	710	711	3.00	0.1960	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2786	710	750	3.00	0.0262	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2787	710	751	4.24	0.0754	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2788	710	749	4.24	0.0357	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2789	711	712	3.00	0.1248	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2790	711	751	3.00	0.1554	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2791	711	752	4.24	0.1930	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2792	711	750	4.24	0.0159	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2793	712	713	3.00	0.1489	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2794	712	752	3.00	0.0594	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2795	712	753	4.24	0.0482	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2796	712	751	4.24	0.0251	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2797	713	714	3.00	0.1153	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2798	713	753	3.00	0.0579	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2799	713	754	4.24	0.0461	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2800	713	752	4.24	0.0961	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2801	714	715	3.00	0.0104	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2802	714	754	3.00	0.1968	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2803	714	755	4.24	0.1681	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2804	714	753	4.24	0.0446	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2805	715	716	3.00	0.1477	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2806	715	755	3.00	0.1808	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2807	715	756	4.24	0.0667	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2808	715	754	4.24	0.1231	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2809	716	717	3.00	0.0541	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2810	716	756	3.00	0.1989	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2811	716	757	4.24	0.0755	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2812	716	755	4.24	0.1484	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2813	717	718	3.00	0.1662	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2814	717	757	3.00	0.0719	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2815	717	758	4.24	0.1715	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2816	717	756	4.24	0.1599	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2817	718	719	3.00	0.0176	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2818	718	758	3.00	0.1458	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2819	718	759	4.24	0.0186	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2820	718	757	4.24	0.1662	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2821	719	720	3.00	0.0272	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2822	719	759	3.00	0.1773	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2823	719	760	4.24	0.0505	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2824	719	758	4.24	0.0202	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2825	720	760	3.00	0.0472	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2826	720	759	4.24	0.1298	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2827	721	722	3.00	0.1903	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2828	721	761	3.00	0.0892	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2829	721	762	4.24	0.0417	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2830	722	723	3.00	0.1908	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2831	722	762	3.00	0.0312	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2832	722	763	4.24	0.1128	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2833	722	761	4.24	0.1381	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2834	723	724	3.00	0.1080	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2835	723	763	3.00	0.1177	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2836	723	764	4.24	0.1410	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2837	723	762	4.24	0.0476	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2838	724	725	3.00	0.0434	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2839	724	764	3.00	0.1619	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2840	724	765	4.24	0.0484	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2841	724	763	4.24	0.0420	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2842	725	726	3.00	0.1401	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2843	725	765	3.00	0.0522	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2844	725	766	4.24	0.0668	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2845	725	764	4.24	0.1135	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2846	726	727	3.00	0.1382	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2847	726	766	3.00	0.1947	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2848	726	767	4.24	0.0111	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2849	726	765	4.24	0.1403	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2850	727	728	3.00	0.1758	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2851	727	767	3.00	0.0132	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2852	727	768	4.24	0.0933	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2853	727	766	4.24	0.0440	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2854	728	729	3.00	0.0965	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2855	728	768	3.00	0.1525	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2856	728	769	4.24	0.1287	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2857	728	767	4.24	0.0776	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2858	729	730	3.00	0.1236	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2859	729	769	3.00	0.1347	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2860	729	770	4.24	0.0368	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2861	729	768	4.24	0.1808	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2862	730	731	3.00	0.0783	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2863	730	770	3.00	0.1896	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2864	730	771	4.24	0.1064	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2865	730	769	4.24	0.1174	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2866	731	732	3.00	0.0294	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2867	731	771	3.00	0.0175	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2868	731	772	4.24	0.0155	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2869	731	770	4.24	0.0221	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2870	732	733	3.00	0.0225	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2871	732	772	3.00	0.1816	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2872	732	773	4.24	0.0256	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2873	732	771	4.24	0.0377	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2874	733	734	3.00	0.1311	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2875	733	773	3.00	0.0304	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2876	733	774	4.24	0.0256	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2877	733	772	4.24	0.0420	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2878	734	735	3.00	0.1655	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2879	734	774	3.00	0.1986	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2880	734	775	4.24	0.1822	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2881	734	773	4.24	0.0961	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2882	735	736	3.00	0.1496	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2883	735	775	3.00	0.1192	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2884	735	776	4.24	0.0399	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2885	735	774	4.24	0.0850	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2886	736	737	3.00	0.1435	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2887	736	776	3.00	0.1545	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2888	736	777	4.24	0.1729	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2889	736	775	4.24	0.1512	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2890	737	738	3.00	0.1122	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2891	737	777	3.00	0.1056	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2892	737	778	4.24	0.1700	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2893	737	776	4.24	0.1534	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2894	738	739	3.00	0.0175	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2895	738	778	3.00	0.1726	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2896	738	779	4.24	0.0797	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2897	738	777	4.24	0.0174	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2898	739	740	3.00	0.1988	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2899	739	779	3.00	0.1000	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2900	739	780	4.24	0.1015	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2901	739	778	4.24	0.0894	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2902	740	741	3.00	0.0716	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2903	740	780	3.00	0.1393	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2904	740	781	4.24	0.1054	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2905	740	779	4.24	0.0835	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2906	741	742	3.00	0.0426	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2907	741	781	3.00	0.1775	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2908	741	782	4.24	0.0674	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2909	741	780	4.24	0.1879	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2910	742	743	3.00	0.1010	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2911	742	782	3.00	0.0625	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2912	742	783	4.24	0.1631	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2913	742	781	4.24	0.1802	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2914	743	744	3.00	0.0485	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2915	743	783	3.00	0.1930	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2916	743	784	4.24	0.0230	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2917	743	782	4.24	0.1664	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2918	744	745	3.00	0.1363	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2919	744	784	3.00	0.1233	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2920	744	785	4.24	0.1130	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2921	744	783	4.24	0.0278	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2922	745	746	3.00	0.1149	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2923	745	785	3.00	0.0791	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2924	745	786	4.24	0.1013	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2925	745	784	4.24	0.1816	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2926	746	747	3.00	0.1045	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2927	746	786	3.00	0.0509	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2928	746	787	4.24	0.1491	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2929	746	785	4.24	0.1022	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2930	747	748	3.00	0.0327	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2931	747	787	3.00	0.0916	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2932	747	788	4.24	0.1572	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2933	747	786	4.24	0.1514	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2934	748	749	3.00	0.0212	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2935	748	788	3.00	0.1639	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2936	748	789	4.24	0.1084	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2937	748	787	4.24	0.1905	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2938	749	750	3.00	0.1454	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2939	749	789	3.00	0.0531	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2940	749	790	4.24	0.1156	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2941	749	788	4.24	0.1907	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2942	750	751	3.00	0.0204	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2943	750	790	3.00	0.1886	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2944	750	791	4.24	0.1170	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2945	750	789	4.24	0.0314	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2946	751	752	3.00	0.0883	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2947	751	791	3.00	0.0644	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2948	751	792	4.24	0.1703	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2949	751	790	4.24	0.1470	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2950	752	753	3.00	0.0882	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2951	752	792	3.00	0.0762	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2952	752	793	4.24	0.0133	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2953	752	791	4.24	0.1983	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2954	753	754	3.00	0.0880	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2955	753	793	3.00	0.0160	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2956	753	794	4.24	0.0532	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2957	753	792	4.24	0.1745	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2958	754	755	3.00	0.0256	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2959	754	794	3.00	0.0778	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2960	754	795	4.24	0.0470	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2961	754	793	4.24	0.1668	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2962	755	756	3.00	0.1152	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2963	755	795	3.00	0.1274	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2964	755	796	4.24	0.0954	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2965	755	794	4.24	0.0660	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2966	756	757	3.00	0.1765	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2967	756	796	3.00	0.1255	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2968	756	797	4.24	0.1506	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2969	756	795	4.24	0.0294	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2970	757	758	3.00	0.1155	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2971	757	797	3.00	0.0967	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2972	757	798	4.24	0.0695	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2973	757	796	4.24	0.1778	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2974	758	759	3.00	0.0892	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2975	758	798	3.00	0.1806	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2976	758	799	4.24	0.1877	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2977	758	797	4.24	0.0958	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2978	759	760	3.00	0.1459	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2979	759	799	3.00	0.1394	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2980	759	800	4.24	0.1847	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2981	759	798	4.24	0.1080	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2982	760	800	3.00	0.0621	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2983	760	799	4.24	0.0379	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2984	761	762	3.00	0.1453	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2985	761	801	3.00	0.0326	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2986	761	802	4.24	0.1731	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2987	762	763	3.00	0.1245	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2988	762	802	3.00	0.0898	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2989	762	803	4.24	0.1317	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2990	762	801	4.24	0.0257	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2991	801	802	3.00	0.1012	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2992	801	841	3.00	0.1737	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2993	801	842	4.24	0.1726	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2994	763	764	3.00	0.0389	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2995	763	803	3.00	0.0785	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
2996	763	804	4.24	0.1853	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
2997	763	802	4.24	0.0700	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
2998	764	765	3.00	0.1830	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
2999	764	804	3.00	0.1144	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3000	764	805	4.24	0.1518	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3001	764	803	4.24	0.0729	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3002	765	766	3.00	0.0383	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3003	765	805	3.00	0.0489	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3004	765	806	4.24	0.0966	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3005	765	804	4.24	0.0334	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3006	766	767	3.00	0.1792	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3007	766	806	3.00	0.1244	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3008	766	807	4.24	0.1885	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3009	766	805	4.24	0.0673	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3010	767	768	3.00	0.0995	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3011	767	807	3.00	0.1821	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3012	767	808	4.24	0.0176	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3013	767	806	4.24	0.0193	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3014	768	769	3.00	0.0936	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3015	768	808	3.00	0.1885	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3016	768	809	4.24	0.0944	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3017	768	807	4.24	0.0510	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3018	769	770	3.00	0.1056	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3019	769	809	3.00	0.0327	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3020	769	810	4.24	0.1393	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3021	769	808	4.24	0.1610	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3022	770	771	3.00	0.0910	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3023	770	810	3.00	0.1440	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3024	770	811	4.24	0.0661	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3025	770	809	4.24	0.1903	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3026	771	772	3.00	0.1870	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3027	771	811	3.00	0.1522	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3028	771	812	4.24	0.0772	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3029	771	810	4.24	0.0433	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3030	772	773	3.00	0.1524	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3031	772	812	3.00	0.1726	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3032	772	813	4.24	0.0258	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3033	772	811	4.24	0.0700	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3034	773	774	3.00	0.1134	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3035	773	813	3.00	0.1263	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3036	773	814	4.24	0.0573	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3037	773	812	4.24	0.1081	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3038	774	775	3.00	0.1965	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3039	774	814	3.00	0.0959	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3040	774	815	4.24	0.1809	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3041	774	813	4.24	0.1506	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3042	775	776	3.00	0.1738	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3043	775	815	3.00	0.0684	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3044	775	816	4.24	0.0882	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3045	775	814	4.24	0.1752	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3046	776	777	3.00	0.0228	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3047	776	816	3.00	0.1701	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3048	776	817	4.24	0.1742	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3049	776	815	4.24	0.1791	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3050	777	778	3.00	0.1589	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3051	777	817	3.00	0.0784	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3052	777	818	4.24	0.1964	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3053	777	816	4.24	0.1997	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3054	778	779	3.00	0.0698	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3055	778	818	3.00	0.0756	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3056	778	819	4.24	0.1557	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3057	778	817	4.24	0.1219	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3058	779	780	3.00	0.1610	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3059	779	819	3.00	0.1817	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3060	779	820	4.24	0.0993	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3061	779	818	4.24	0.1483	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3062	780	781	3.00	0.0811	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3063	780	820	3.00	0.1893	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3064	780	821	4.24	0.1984	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3065	780	819	4.24	0.0208	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3066	781	782	3.00	0.0487	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3067	781	821	3.00	0.0832	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3068	781	822	4.24	0.0992	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3069	781	820	4.24	0.0417	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3070	782	783	3.00	0.1686	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3071	782	822	3.00	0.0386	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3072	782	823	4.24	0.1651	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3073	782	821	4.24	0.0657	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3074	783	784	3.00	0.1919	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3075	783	823	3.00	0.0144	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3076	783	824	4.24	0.1529	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3077	783	822	4.24	0.1832	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3078	784	785	3.00	0.1420	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3079	784	824	3.00	0.1321	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3080	784	825	4.24	0.1731	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3081	784	823	4.24	0.0746	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3082	785	786	3.00	0.0182	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3083	785	825	3.00	0.0685	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3084	785	826	4.24	0.1657	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3085	785	824	4.24	0.0975	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3086	786	787	3.00	0.1375	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3087	786	826	3.00	0.1862	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3088	786	827	4.24	0.1286	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3089	786	825	4.24	0.1943	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3090	787	788	3.00	0.1157	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3091	787	827	3.00	0.1715	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3092	787	828	4.24	0.0135	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3093	787	826	4.24	0.1539	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3094	788	789	3.00	0.0411	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3095	788	828	3.00	0.0993	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3096	788	829	4.24	0.1076	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3097	788	827	4.24	0.1195	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3098	789	790	3.00	0.0305	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3099	789	829	3.00	0.1884	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3100	789	830	4.24	0.0491	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3101	789	828	4.24	0.1989	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3102	790	791	3.00	0.1416	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3103	790	830	3.00	0.0492	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3104	790	831	4.24	0.1889	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3105	790	829	4.24	0.1996	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3106	791	792	3.00	0.0949	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3107	791	831	3.00	0.1286	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3108	791	832	4.24	0.0932	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3109	791	830	4.24	0.0325	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3110	792	793	3.00	0.0566	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3111	792	832	3.00	0.1040	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3112	792	833	4.24	0.0283	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3113	792	831	4.24	0.1022	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3114	793	794	3.00	0.0329	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3115	793	833	3.00	0.0711	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3116	793	834	4.24	0.1962	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3117	793	832	4.24	0.1089	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3118	794	795	3.00	0.1330	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3119	794	834	3.00	0.0482	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3120	794	835	4.24	0.1315	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3121	794	833	4.24	0.0571	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3122	795	796	3.00	0.1535	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3123	795	835	3.00	0.1118	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3124	795	836	4.24	0.1748	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3125	795	834	4.24	0.1858	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3126	796	797	3.00	0.1150	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3127	796	836	3.00	0.1279	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3128	796	837	4.24	0.1625	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3129	796	835	4.24	0.1182	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3130	797	798	3.00	0.1384	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3131	797	837	3.00	0.1863	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3132	797	838	4.24	0.1054	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3133	797	836	4.24	0.1243	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3134	798	799	3.00	0.0696	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3135	798	838	3.00	0.0117	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3136	798	839	4.24	0.0248	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3137	798	837	4.24	0.0698	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3138	799	800	3.00	0.1528	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3139	799	839	3.00	0.1323	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3140	799	840	4.24	0.2000	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3141	799	838	4.24	0.1057	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3142	800	840	3.00	0.1522	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3143	800	839	4.24	0.1060	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3144	802	803	3.00	0.1847	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3145	802	842	3.00	0.1013	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3146	802	843	4.24	0.0687	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3147	802	841	4.24	0.0984	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3148	803	804	3.00	0.1566	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3149	803	843	3.00	0.0641	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3150	803	844	4.24	0.0833	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3151	803	842	4.24	0.0750	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3152	804	805	3.00	0.0665	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3153	804	844	3.00	0.1195	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3154	804	845	4.24	0.1512	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3155	804	843	4.24	0.0386	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3156	805	806	3.00	0.1365	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3157	805	845	3.00	0.0764	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3158	805	846	4.24	0.1615	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3159	805	844	4.24	0.0509	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3160	806	807	3.00	0.1029	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3161	806	846	3.00	0.1506	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3162	806	847	4.24	0.0870	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3163	806	845	4.24	0.1802	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3164	807	808	3.00	0.0100	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3165	807	847	3.00	0.1022	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3166	807	848	4.24	0.0181	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3167	807	846	4.24	0.0282	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3168	808	809	3.00	0.1122	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3169	808	848	3.00	0.0454	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3170	808	849	4.24	0.1657	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3171	808	847	4.24	0.1479	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3172	809	810	3.00	0.1040	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3173	809	849	3.00	0.1894	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3174	809	850	4.24	0.0447	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3175	809	848	4.24	0.0255	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3176	810	811	3.00	0.1450	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3177	810	850	3.00	0.0352	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3178	810	851	4.24	0.1920	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3179	810	849	4.24	0.1753	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3180	811	812	3.00	0.0743	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3181	811	851	3.00	0.1605	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3182	811	852	4.24	0.0235	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3183	811	850	4.24	0.1828	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3184	812	813	3.00	0.1819	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3185	812	852	3.00	0.1701	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3186	812	853	4.24	0.1795	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3187	812	851	4.24	0.1416	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3188	813	814	3.00	0.1183	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3189	813	853	3.00	0.1092	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3190	813	854	4.24	0.1768	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3191	813	852	4.24	0.0805	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3192	814	815	3.00	0.1363	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3193	814	854	3.00	0.0826	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3194	814	855	4.24	0.0232	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3195	814	853	4.24	0.0681	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3196	815	816	3.00	0.1983	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3197	815	855	3.00	0.0424	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3198	815	856	4.24	0.1650	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3199	815	854	4.24	0.1550	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3200	816	817	3.00	0.1122	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3201	816	856	3.00	0.0557	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3202	816	857	4.24	0.1229	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3203	816	855	4.24	0.1782	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3204	817	818	3.00	0.1969	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3205	817	857	3.00	0.1571	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3206	817	858	4.24	0.1881	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3207	817	856	4.24	0.0282	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3208	818	819	3.00	0.1771	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3209	818	858	3.00	0.0368	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3210	818	859	4.24	0.1324	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3211	818	857	4.24	0.0975	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3212	819	820	3.00	0.1012	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3213	819	859	3.00	0.0977	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3214	819	860	4.24	0.1608	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3215	819	858	4.24	0.0757	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3216	820	821	3.00	0.1811	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3217	820	860	3.00	0.0949	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3218	820	861	4.24	0.0744	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3219	820	859	4.24	0.0958	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3220	821	822	3.00	0.1148	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3221	821	861	3.00	0.0303	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3222	821	862	4.24	0.0892	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3223	821	860	4.24	0.1826	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3224	822	823	3.00	0.0970	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3225	822	862	3.00	0.0273	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3226	822	863	4.24	0.1214	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3227	822	861	4.24	0.0847	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3228	823	824	3.00	0.1079	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3229	823	863	3.00	0.1587	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3230	823	864	4.24	0.0790	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3231	823	862	4.24	0.1949	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3232	824	825	3.00	0.0211	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3233	824	864	3.00	0.1794	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3234	824	865	4.24	0.0835	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3235	824	863	4.24	0.1729	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3236	825	826	3.00	0.1601	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3237	825	865	3.00	0.1128	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3238	825	866	4.24	0.0657	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3239	825	864	4.24	0.1704	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3240	826	827	3.00	0.1287	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3241	826	866	3.00	0.0375	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3242	826	867	4.24	0.1308	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3243	826	865	4.24	0.1876	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3244	827	828	3.00	0.0128	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3245	827	867	3.00	0.1436	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3246	827	868	4.24	0.0749	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3247	827	866	4.24	0.1797	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3248	828	829	3.00	0.0596	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3249	828	868	3.00	0.0878	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3250	828	869	4.24	0.0425	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3251	828	867	4.24	0.0193	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3252	829	830	3.00	0.1554	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3253	829	869	3.00	0.1306	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3254	829	870	4.24	0.1850	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3255	829	868	4.24	0.0642	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3256	830	831	3.00	0.1050	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3257	830	870	3.00	0.1856	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3258	830	871	4.24	0.1230	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3259	830	869	4.24	0.0683	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3260	831	832	3.00	0.0734	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3261	831	871	3.00	0.0298	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3262	831	872	4.24	0.1694	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3263	831	870	4.24	0.0568	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3264	832	833	3.00	0.0151	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3265	832	872	3.00	0.0176	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3266	832	873	4.24	0.1577	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3267	832	871	4.24	0.1669	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3268	833	834	3.00	0.0516	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3269	833	873	3.00	0.1183	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3270	833	874	4.24	0.1780	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3271	833	872	4.24	0.0153	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3272	834	835	3.00	0.0572	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3273	834	874	3.00	0.0812	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3274	834	875	4.24	0.0121	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3275	834	873	4.24	0.1215	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3276	835	836	3.00	0.0928	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3277	835	875	3.00	0.0656	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3278	835	876	4.24	0.0889	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3279	835	874	4.24	0.0251	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3280	836	837	3.00	0.1730	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3281	836	876	3.00	0.0745	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3282	836	877	4.24	0.1491	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3283	836	875	4.24	0.1856	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3284	837	838	3.00	0.0615	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3285	837	877	3.00	0.1758	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3286	837	878	4.24	0.1669	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3287	837	876	4.24	0.1005	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3288	838	839	3.00	0.1158	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3289	838	878	3.00	0.0715	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3290	838	879	4.24	0.1400	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3291	838	877	4.24	0.0119	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3292	839	840	3.00	0.0191	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3293	839	879	3.00	0.1407	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3294	839	880	4.24	0.0397	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3295	839	878	4.24	0.1170	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3296	878	879	3.00	0.0855	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3297	878	918	3.00	0.1155	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3298	878	919	4.24	0.0560	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3299	878	917	4.24	0.0371	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3300	878	877	3.00	0.1786	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3301	840	880	3.00	0.1025	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3302	840	879	4.24	0.0712	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3303	841	842	3.00	0.0804	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3304	841	881	3.00	0.1580	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3305	841	882	4.24	0.0172	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3306	842	843	3.00	0.0260	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3307	842	882	3.00	0.0928	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3308	842	883	4.24	0.1413	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3309	842	881	4.24	0.0701	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3310	843	844	3.00	0.1340	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3311	843	883	3.00	0.0626	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3312	843	884	4.24	0.1724	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3313	843	882	4.24	0.1699	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3314	844	845	3.00	0.0686	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3315	844	884	3.00	0.1903	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3316	844	885	4.24	0.0470	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3317	844	883	4.24	0.1836	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3318	845	846	3.00	0.1331	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3319	845	885	3.00	0.0348	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3320	845	886	4.24	0.1288	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3321	845	884	4.24	0.1193	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3322	846	847	3.00	0.0744	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3323	846	886	3.00	0.0560	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3324	846	887	4.24	0.0818	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3325	846	885	4.24	0.1395	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3326	847	848	3.00	0.0400	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3327	847	887	3.00	0.1530	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3328	847	888	4.24	0.1912	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3329	847	886	4.24	0.0297	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3330	848	849	3.00	0.0268	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3331	848	888	3.00	0.0760	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3332	848	889	4.24	0.0345	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3333	848	887	4.24	0.1954	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3334	849	850	3.00	0.1127	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3335	849	889	3.00	0.1579	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3336	849	890	4.24	0.0863	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3337	849	888	4.24	0.0405	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3338	850	851	3.00	0.0128	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3339	850	890	3.00	0.1792	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3340	850	891	4.24	0.1546	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3341	850	889	4.24	0.1721	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3342	851	852	3.00	0.1912	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3343	851	891	3.00	0.0429	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3344	851	892	4.24	0.0139	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3345	851	890	4.24	0.1971	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3346	852	853	3.00	0.1515	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3347	852	892	3.00	0.0710	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3348	852	893	4.24	0.0542	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3349	852	891	4.24	0.0160	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3350	853	854	3.00	0.0540	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3351	853	893	3.00	0.1871	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3352	853	894	4.24	0.1729	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3353	853	892	4.24	0.0734	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3354	854	855	3.00	0.1002	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3355	854	894	3.00	0.0384	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3356	854	895	4.24	0.0569	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3357	854	893	4.24	0.1607	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3358	855	856	3.00	0.0200	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3359	855	895	3.00	0.1698	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3360	855	896	4.24	0.1907	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3361	855	894	4.24	0.1324	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3362	856	857	3.00	0.0710	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3363	856	896	3.00	0.1732	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3364	856	897	4.24	0.0263	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3365	856	895	4.24	0.0429	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3366	857	858	3.00	0.1888	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3367	857	897	3.00	0.1663	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3368	857	898	4.24	0.1725	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3369	857	896	4.24	0.0382	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3370	858	859	3.00	0.0138	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3371	858	898	3.00	0.1718	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3372	858	899	4.24	0.1923	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3373	858	897	4.24	0.1586	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3374	859	860	3.00	0.1019	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3375	859	899	3.00	0.1971	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3376	859	900	4.24	0.1051	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3377	859	898	4.24	0.0168	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3378	860	861	3.00	0.1939	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3379	860	900	3.00	0.1987	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3380	860	901	4.24	0.1668	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3381	860	899	4.24	0.1225	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3382	861	862	3.00	0.1818	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3383	861	901	3.00	0.0524	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3384	861	902	4.24	0.1525	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3385	861	900	4.24	0.0663	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3386	862	863	3.00	0.1909	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3387	862	902	3.00	0.0562	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3388	862	903	4.24	0.0348	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3389	862	901	4.24	0.0418	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3390	863	864	3.00	0.1560	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3391	863	903	3.00	0.1802	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3392	863	904	4.24	0.0995	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3393	863	902	4.24	0.0880	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3394	864	865	3.00	0.0651	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3395	864	904	3.00	0.1293	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3396	864	905	4.24	0.1613	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3397	864	903	4.24	0.1201	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3398	865	866	3.00	0.0685	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3399	865	905	3.00	0.1447	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3400	865	906	4.24	0.0788	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3401	865	904	4.24	0.1805	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3402	866	867	3.00	0.0178	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3403	866	906	3.00	0.0628	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3404	866	907	4.24	0.1579	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3405	866	905	4.24	0.1066	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3406	867	868	3.00	0.0354	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3407	867	907	3.00	0.0661	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3408	867	908	4.24	0.0838	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3409	867	906	4.24	0.1305	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3410	868	869	3.00	0.0672	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3411	868	908	3.00	0.1551	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3412	868	909	4.24	0.0477	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3413	868	907	4.24	0.0483	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3414	869	870	3.00	0.1759	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3415	869	909	3.00	0.1485	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3416	869	910	4.24	0.1881	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3417	869	908	4.24	0.1999	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3418	870	871	3.00	0.1574	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3419	870	910	3.00	0.0323	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3420	870	911	4.24	0.0800	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3421	870	909	4.24	0.0257	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3422	871	872	3.00	0.1782	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3423	871	911	3.00	0.1694	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3424	871	912	4.24	0.1154	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3425	871	910	4.24	0.0787	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3426	872	873	3.00	0.0462	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3427	872	912	3.00	0.0569	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3428	872	913	4.24	0.0214	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3429	872	911	4.24	0.0914	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3430	873	874	3.00	0.0258	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3431	873	913	3.00	0.0938	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3432	873	914	4.24	0.1385	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3433	873	912	4.24	0.0932	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3434	874	875	3.00	0.0823	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3435	874	914	3.00	0.0865	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3436	874	915	4.24	0.1306	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3437	874	913	4.24	0.0720	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3438	875	876	3.00	0.1508	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3439	875	915	3.00	0.1376	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3440	875	916	4.24	0.1440	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3441	875	914	4.24	0.1561	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3442	876	877	3.00	0.0384	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3443	876	916	3.00	0.1678	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3444	876	917	4.24	0.0192	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3445	876	915	4.24	0.1942	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3446	877	917	3.00	0.0879	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3447	877	918	4.24	0.1001	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3448	877	916	4.24	0.0419	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3449	879	880	3.00	0.1801	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3450	879	919	3.00	0.1100	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3451	879	920	4.24	0.0169	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3452	879	918	4.24	0.1703	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3453	880	920	3.00	0.1099	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3454	880	919	4.24	0.1836	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3455	881	882	3.00	0.1567	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3456	881	921	3.00	0.0846	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3457	881	922	4.24	0.1155	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3458	882	883	3.00	0.0780	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3459	882	922	3.00	0.0409	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3460	882	923	4.24	0.1031	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3461	882	921	4.24	0.0823	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3462	883	884	3.00	0.1965	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3463	883	923	3.00	0.1316	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3464	883	924	4.24	0.1441	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3465	883	922	4.24	0.0729	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3466	884	885	3.00	0.1607	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3467	884	924	3.00	0.1454	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3468	884	925	4.24	0.1472	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3469	884	923	4.24	0.1904	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3470	885	886	3.00	0.0540	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3471	885	925	3.00	0.1509	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3472	885	926	4.24	0.0780	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3473	885	924	4.24	0.0665	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3474	886	887	3.00	0.1070	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3475	886	926	3.00	0.0265	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3476	886	927	4.24	0.1019	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3477	886	925	4.24	0.0264	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3478	887	888	3.00	0.0644	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3479	887	927	3.00	0.0833	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3480	887	928	4.24	0.1653	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3481	887	926	4.24	0.0353	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3482	888	889	3.00	0.1119	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3483	888	928	3.00	0.1208	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3484	888	929	4.24	0.0226	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3485	888	927	4.24	0.0616	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3486	889	890	3.00	0.0296	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3487	889	929	3.00	0.0688	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3488	889	930	4.24	0.1215	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3489	889	928	4.24	0.0325	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3490	890	891	3.00	0.1383	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3491	890	930	3.00	0.0311	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3492	890	931	4.24	0.0463	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3493	890	929	4.24	0.1219	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3494	891	892	3.00	0.1517	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3495	891	931	3.00	0.0485	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3496	891	932	4.24	0.1836	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3497	891	930	4.24	0.1064	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3498	892	893	3.00	0.1998	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3499	892	932	3.00	0.1297	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3500	892	933	4.24	0.0216	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3501	892	931	4.24	0.1533	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3502	893	894	3.00	0.0913	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3503	893	933	3.00	0.0662	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3504	893	934	4.24	0.1121	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3505	893	932	4.24	0.1181	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3506	894	895	3.00	0.1421	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3507	894	934	3.00	0.1716	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3508	894	935	4.24	0.0129	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3509	894	933	4.24	0.1290	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3510	895	896	3.00	0.0821	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3511	895	935	3.00	0.0533	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3512	895	936	4.24	0.1095	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3513	895	934	4.24	0.1585	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3514	896	897	3.00	0.1775	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3515	896	936	3.00	0.1102	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3516	896	937	4.24	0.0931	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3517	896	935	4.24	0.0277	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3518	897	898	3.00	0.1040	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3519	897	937	3.00	0.1307	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3520	897	938	4.24	0.1637	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3521	897	936	4.24	0.0952	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3522	898	899	3.00	0.0309	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3523	898	938	3.00	0.1507	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3524	898	939	4.24	0.1649	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3525	898	937	4.24	0.0471	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3526	899	900	3.00	0.0851	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3527	899	939	3.00	0.0309	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3528	899	940	4.24	0.0209	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3529	899	938	4.24	0.1973	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3530	900	901	3.00	0.0258	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3531	900	940	3.00	0.1444	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3532	900	941	4.24	0.1030	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3533	900	939	4.24	0.0556	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3534	901	902	3.00	0.1744	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3535	901	941	3.00	0.0300	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3536	901	942	4.24	0.0655	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3537	901	940	4.24	0.0207	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3538	902	903	3.00	0.1205	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3539	902	942	3.00	0.1178	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3540	902	943	4.24	0.1458	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3541	902	941	4.24	0.0663	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3542	903	904	3.00	0.0703	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3543	903	943	3.00	0.1352	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3544	903	944	4.24	0.1811	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3545	903	942	4.24	0.1635	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3546	904	905	3.00	0.1278	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3547	904	944	3.00	0.1233	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3548	904	945	4.24	0.0880	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3549	904	943	4.24	0.0300	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3550	905	906	3.00	0.0898	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3551	905	945	3.00	0.0371	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3552	905	946	4.24	0.0277	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3553	905	944	4.24	0.0494	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3554	906	907	3.00	0.1332	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3555	906	946	3.00	0.1636	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3556	906	947	4.24	0.1547	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3557	906	945	4.24	0.0353	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3558	907	908	3.00	0.1421	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3559	907	947	3.00	0.0177	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3560	907	948	4.24	0.1300	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3561	907	946	4.24	0.1994	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3562	908	909	3.00	0.0367	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3563	908	948	3.00	0.1645	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3564	908	949	4.24	0.1773	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3565	908	947	4.24	0.0189	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3566	909	910	3.00	0.1685	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3567	909	949	3.00	0.0337	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3568	909	950	4.24	0.1168	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3569	909	948	4.24	0.0580	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3570	910	911	3.00	0.0947	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3571	910	950	3.00	0.0556	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3572	910	951	4.24	0.1430	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3573	910	949	4.24	0.0130	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3574	911	912	3.00	0.1079	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3575	911	951	3.00	0.1447	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3576	911	952	4.24	0.0605	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3577	911	950	4.24	0.0380	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3578	912	913	3.00	0.1998	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3579	912	952	3.00	0.1591	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3580	912	953	4.24	0.1853	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3581	912	951	4.24	0.0317	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3582	913	914	3.00	0.0828	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3583	913	953	3.00	0.0566	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3584	913	954	4.24	0.1947	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3585	913	952	4.24	0.1834	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3586	914	915	3.00	0.0654	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3587	914	954	3.00	0.0931	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3588	914	955	4.24	0.1492	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3589	914	953	4.24	0.0263	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3590	915	916	3.00	0.1714	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3591	915	955	3.00	0.0313	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3592	915	956	4.24	0.1847	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3593	915	954	4.24	0.1132	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3594	916	917	3.00	0.1705	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3595	916	956	3.00	0.1295	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3596	916	957	4.24	0.1317	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3597	916	955	4.24	0.0990	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3598	917	918	3.00	0.1227	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3599	917	957	3.00	0.1627	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3600	917	958	4.24	0.0809	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3601	917	956	4.24	0.0747	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3602	918	919	3.00	0.1370	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3603	918	958	3.00	0.1670	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3604	918	959	4.24	0.0970	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3605	918	957	4.24	0.1486	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3606	919	920	3.00	0.0683	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3607	919	959	3.00	0.0659	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3608	919	960	4.24	0.1207	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3609	919	958	4.24	0.1815	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3610	920	960	3.00	0.0794	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3611	920	959	4.24	0.1582	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3612	921	922	3.00	0.0979	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3613	921	961	3.00	0.1813	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3614	921	962	4.24	0.1531	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3615	922	923	3.00	0.0104	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3616	922	962	3.00	0.1055	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3617	922	963	4.24	0.1550	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3618	922	961	4.24	0.0249	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3619	923	924	3.00	0.0630	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3620	923	963	3.00	0.0473	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3621	923	964	4.24	0.1854	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3622	923	962	4.24	0.0727	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3623	924	925	3.00	0.1136	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3624	924	964	3.00	0.1048	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3625	924	965	4.24	0.0654	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3626	924	963	4.24	0.1272	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3627	925	926	3.00	0.1240	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3628	925	965	3.00	0.1920	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3629	925	966	4.24	0.0871	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3630	925	964	4.24	0.0906	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3631	926	927	3.00	0.1614	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3632	926	966	3.00	0.0388	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3633	926	967	4.24	0.1540	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3634	926	965	4.24	0.0390	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3635	927	928	3.00	0.1690	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3636	927	967	3.00	0.0183	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3637	927	968	4.24	0.0372	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3638	927	966	4.24	0.0838	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3639	928	929	3.00	0.0619	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3640	928	968	3.00	0.1333	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3641	928	969	4.24	0.1642	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3642	928	967	4.24	0.0226	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3643	929	930	3.00	0.0278	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3644	929	969	3.00	0.0314	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3645	929	970	4.24	0.1211	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3646	929	968	4.24	0.1614	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3647	930	931	3.00	0.1790	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3648	930	970	3.00	0.1178	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3649	930	971	4.24	0.1048	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3650	930	969	4.24	0.1942	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3651	931	932	3.00	0.1765	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3652	931	971	3.00	0.0242	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3653	931	972	4.24	0.0471	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3654	931	970	4.24	0.1448	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3655	932	933	3.00	0.1462	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3656	932	972	3.00	0.1053	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3657	932	973	4.24	0.1452	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3658	932	971	4.24	0.0715	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3659	933	934	3.00	0.0905	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3660	933	973	3.00	0.0683	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3661	933	974	4.24	0.1018	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3662	933	972	4.24	0.1848	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3663	934	935	3.00	0.0889	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3664	934	974	3.00	0.1659	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3665	934	975	4.24	0.1530	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3666	934	973	4.24	0.1826	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3667	935	936	3.00	0.0181	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3668	935	975	3.00	0.1976	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3669	935	976	4.24	0.1916	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3670	935	974	4.24	0.1874	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3671	936	937	3.00	0.0205	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3672	936	976	3.00	0.0165	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3673	936	977	4.24	0.0198	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3674	936	975	4.24	0.0771	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3675	937	938	3.00	0.0250	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3676	937	977	3.00	0.0898	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3677	937	978	4.24	0.1932	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3678	937	976	4.24	0.1210	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3679	938	939	3.00	0.0923	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3680	938	978	3.00	0.1528	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3681	938	979	4.24	0.1531	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3682	938	977	4.24	0.0598	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3683	939	940	3.00	0.1206	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3684	939	979	3.00	0.1264	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3685	939	980	4.24	0.1458	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3686	939	978	4.24	0.1649	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3687	940	941	3.00	0.0409	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3688	940	980	3.00	0.1899	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3689	940	981	4.24	0.0343	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3690	940	979	4.24	0.0303	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3691	941	942	3.00	0.0997	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3692	941	981	3.00	0.1155	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3693	941	982	4.24	0.1904	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3694	941	980	4.24	0.1612	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3695	942	943	3.00	0.0133	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3696	942	982	3.00	0.1345	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3697	942	983	4.24	0.1627	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3698	942	981	4.24	0.1366	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3699	943	944	3.00	0.0291	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3700	943	983	3.00	0.1464	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3701	943	984	4.24	0.0407	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3702	943	982	4.24	0.1735	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3703	944	945	3.00	0.0507	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3704	944	984	3.00	0.1330	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3705	944	985	4.24	0.0342	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3706	944	983	4.24	0.1903	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3707	945	946	3.00	0.1256	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3708	945	985	3.00	0.1486	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3709	945	986	4.24	0.1697	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3710	945	984	4.24	0.1917	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3711	946	947	3.00	0.1229	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3712	946	986	3.00	0.0369	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3713	946	987	4.24	0.1389	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3714	946	985	4.24	0.1175	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3715	947	948	3.00	0.0103	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3716	947	987	3.00	0.1864	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3717	947	988	4.24	0.1114	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3718	947	986	4.24	0.0473	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3719	948	949	3.00	0.0478	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3720	948	988	3.00	0.1762	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3721	948	989	4.24	0.1562	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3722	948	987	4.24	0.0250	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3723	949	950	3.00	0.0811	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3724	949	989	3.00	0.0999	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3725	949	990	4.24	0.0184	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3726	949	988	4.24	0.1208	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3727	950	951	3.00	0.1021	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3728	950	990	3.00	0.1233	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3729	950	991	4.24	0.0843	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3730	950	989	4.24	0.0670	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3731	951	952	3.00	0.0355	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3732	951	991	3.00	0.0876	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3733	951	992	4.24	0.1318	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3734	951	990	4.24	0.1258	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3735	952	953	3.00	0.0803	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3736	952	992	3.00	0.1907	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3737	952	993	4.24	0.1639	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3738	952	991	4.24	0.0335	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3739	953	954	3.00	0.0155	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3740	953	993	3.00	0.0204	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3741	953	994	4.24	0.0829	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3742	953	992	4.24	0.1337	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3743	954	955	3.00	0.1960	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3744	954	994	3.00	0.1853	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3745	954	995	4.24	0.1257	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3746	954	993	4.24	0.0629	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3747	955	956	3.00	0.0544	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3748	955	995	3.00	0.1098	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3749	955	996	4.24	0.0670	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3750	955	994	4.24	0.1667	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3751	956	957	3.00	0.0662	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3752	956	996	3.00	0.0953	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3753	956	997	4.24	0.0980	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3754	956	995	4.24	0.0309	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3755	957	958	3.00	0.0920	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3756	957	997	3.00	0.1901	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3757	957	998	4.24	0.0849	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3758	957	996	4.24	0.0253	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3759	958	959	3.00	0.1874	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3760	958	998	3.00	0.1774	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3761	958	999	4.24	0.1840	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3762	958	997	4.24	0.1817	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3763	959	960	3.00	0.0976	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3764	959	999	3.00	0.1839	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3765	959	1000	4.24	0.0381	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3766	959	998	4.24	0.0283	viento_predominante	2026-08-26 18:02:08	2026-08-26 18:02:08
3767	960	1000	3.00	0.0410	fila_contigua	2026-08-26 18:02:08	2026-08-26 18:02:08
3768	960	999	4.24	0.0361	mecanico_herramienta	2026-08-26 18:02:08	2026-08-26 18:02:08
3769	961	962	3.00	0.0770	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3770	962	963	3.00	0.1801	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3771	963	964	3.00	0.1816	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3772	964	965	3.00	0.0327	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3773	965	966	3.00	0.1498	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3774	966	967	3.00	0.1404	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3775	967	968	3.00	0.1622	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3776	968	969	3.00	0.0884	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3777	969	970	3.00	0.0620	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3778	970	971	3.00	0.1410	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3779	971	972	3.00	0.0769	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3780	972	973	3.00	0.0556	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3781	973	974	3.00	0.1762	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3782	974	975	3.00	0.1920	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3783	975	976	3.00	0.1166	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3784	976	977	3.00	0.0820	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3785	977	978	3.00	0.1259	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3786	978	979	3.00	0.1133	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3787	979	980	3.00	0.1107	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3788	980	981	3.00	0.1246	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3789	981	982	3.00	0.1490	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3790	982	983	3.00	0.0978	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3791	983	984	3.00	0.1710	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3792	984	985	3.00	0.0485	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3793	985	986	3.00	0.0917	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3794	986	987	3.00	0.0382	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3795	987	988	3.00	0.1795	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3796	988	989	3.00	0.0377	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3797	989	990	3.00	0.0685	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3798	990	991	3.00	0.0969	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3799	991	992	3.00	0.0387	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3800	992	993	3.00	0.0644	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3801	993	994	3.00	0.1444	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3802	994	995	3.00	0.1968	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3803	995	996	3.00	0.0790	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3804	996	997	3.00	0.0688	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3805	997	998	3.00	0.1788	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3806	998	999	3.00	0.0831	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
3807	999	1000	3.00	0.0194	misma_fila	2026-08-26 18:02:08	2026-08-26 18:02:08
\.


--
-- Data for Name: bitacoras; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.bitacoras (id, bitacorable_type, bitacorable_id, tipo, prioridad, titulo, contenido, estado, archivo_adjunto, user_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: carta_porte; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.carta_porte (id, despacho_id, numero_carta_porte, tipo_transportador, transportador_id, nombre_conductor, cedula_conductor, telefono_conductor, placa_vehiculo, tipo_vehiculo, placa_trailer, municipio_origen, departamento_origen, municipio_destino, departamento_destino, ruta_descripcion, valor_flete, quien_paga_flete, forma_pago_flete, peso_declarado_kg, descripcion_carga, numero_sello, fecha_salida, fecha_llegada_real, observaciones, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: categorias_insumo; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.categorias_insumo (id, nombre, maneja_vencimiento, maneja_toxicidad, created_at, updated_at) FROM stdin;
1	Pesticidas y Químicos	t	t	2026-08-26 17:59:36	2026-08-26 17:59:36
2	Semillas y Plantones	t	f	2026-08-26 17:59:36	2026-08-26 17:59:36
3	Herramientas y Maquinaria	f	f	2026-08-26 17:59:36	2026-08-26 17:59:36
4	Fertilizantes Orgánicos	t	f	2026-08-26 17:59:36	2026-08-26 17:59:36
5	Equipo de Protección	f	f	2026-08-26 17:59:36	2026-08-26 17:59:36
\.


--
-- Data for Name: ciclo_productivo_zona_manejo; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.ciclo_productivo_zona_manejo (id, ciclo_productivo_id, zona_id, toneladas_producidas, area_hectareas_momento, geometria_zona_momento, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: ciclos_productivos; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.ciclos_productivos (id, lote_id, cultivo_id, estado, tipo, nombre_campana, fecha_inicio, fecha_estimada_cosecha, fecha_real_inicio_cosecha, fecha_estimada_fin_cosecha, fecha_real_fin_cosecha, fecha_finalizacion_ciclo, modalidad_siembra, distancia_entre_hileras_metros, distancia_entre_plantas_metros, plantas_por_hectarea_real, proveedor_material_vegetal_id, codigo_lote_vivero_origen, registro_autorizacion_institucional, es_organico_certificado, agronomo_responsable_id, costo_acumulado_directo, costo_acumulado_indirecto, created_at, updated_at) FROM stdin;
1	1	4	siembra_establecimiento	perenne	LULO-01	2026-08-26	2026-09-11	\N	2026-12-31	\N	\N	semilla_directa	2.00	2.00	2500.00	\N	\N	\N	f	\N	0.00	0.00	2026-08-26 18:01:08	2026-08-26 18:01:08
\.


--
-- Data for Name: clientes; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.clientes (id, razon_social, nombre_comercial, tipo_persona, nit, dv, contacto_nombre, contacto_telefono, contacto_email, direccion_fiscal, municipio, cupo_credito, saldo_actual, dias_credito, estado_cuenta, cultivos_interes, categoria_cliente, autoriza_factura_electronica, email_recepcion_facturas, codigo_postal, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- Data for Name: componentes_riego; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.componentes_riego (id, sistema_riego_id, tipo_componente, nombre_identificador, diametro_pulgadas, presion_trabajo_psi, caudal_estimado_litros_minuto, activo, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: compra_items; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.compra_items (id, compra_id, insumo_id, cantidad, precio_unitario, subtotal, fecha_vencimiento, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: compras; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.compras (id, proveedor_id, fecha, tipo, ciclo_productivo_id, estado, factura_pdf_path, ciudad, departamento, ubicacion, plazo_pago_dias, descuento_pronto_pago, numero_factura, subtotal, descuento_total, iva_total, total, porcentaje_iva_general, fecha_pedido, estado_pago, observaciones, user_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: compras_pagos; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.compras_pagos (id, compra_id, monto, fecha_pago, metodo, referencia_transaccion, user_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: contenedores; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.contenedores (id, sesion_id, cliente_id, orden_pedido_id, estado, nombre, variedad, calidad, tipo_destino, calibre_talla, kilos_acumulados, peso_tara, peso_total, kilos_merma_acumulada, client_updated_at, synced_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: cultivos; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.cultivos (id, tipo, nombre_cultivo, descripcion, created_at, updated_at) FROM stdin;
1	perenne	Palma de Aceite	Cultivo de palma para producción de aceite.	\N	\N
2	transitorio	Maíz	Cultivo anual de maíz para consumo humano y animal.	\N	\N
3	transitorio	Frijol	Cultivo anual de frijol para consumo humano.	\N	\N
4	perenne	Lulo	Cultivo de lulo para producción de jugo.	\N	\N
\.


--
-- Data for Name: despacho_items; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.despacho_items (id, despacho_id, contenedor_id, ciclo_productivo_id, variedad, calidad, tipo_empaque, cantidad_unidades, peso_promedio_unidad, peso_bruto_total, tara_total, precio_unitario_kg, precio_liquidado_kg, descuento_kg, motivo_descuento, descripcion, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: despachos; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.despachos (id, numero_remision, tipo_destino, nombre_destino, ciudad_destino, departamento_destino, fecha_despacho, fecha_estimada_llegada, fecha_liquidado, estado, modalidad_precio, precio_referencia_kg, observaciones, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: evento_arbol; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.evento_arbol (id, evento_campo_id, arbol_id, novedad_arbol, nota_individual, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: evento_insumo_lotes; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.evento_insumo_lotes (id, evento_insumo_id, lote_insumo_id, cantidad, precio, area_aplicada, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: evento_insumos; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.evento_insumos (id, evento_campo_id, insumo_id, cantidad, area_aplicada, metodo_aplicacion, unidad_medida, costo_total, observaciones, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: evento_mano_obra; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.evento_mano_obra (id, evento_campo_id, sesion_id, ciclo_id, tipo_labor, trabajador_id, nombre_trabajador, cedula, cantidad, unidad_destajo, valor_unitario, costo_total, estado_pago, fecha_pago, observaciones, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: evento_maquinaria; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.evento_maquinaria (id, evento_id, maquina_id, trabajador_id, estado, horometro_inicial, horometro_final, horas_trabajadas, tipo_combustible, litros_consumidos, costo_total, observaciones, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: evento_riego_componentes; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.evento_riego_componentes (id, evento_riego_id, componente_id, estaba_activo, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: eventos_campo; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.eventos_campo (id, ciclo_productivo_id, lote_id, zona_id, hora_inicio, hora_fin, tipo_evento_id, fecha_programada, fecha_ejecucion, coordenada_gps, estado, observaciones, deleted_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: eventos_riego; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.eventos_riego (id, sistema_riego_id, responsable_id, fecha_hora_inicio, fecha_hora_fin, duracion_total_minutos, presion_promedio_psi, caudal_estimado_litros_minuto, volumen_estimado_litros, estado, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: fenologia_etapas; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.fenologia_etapas (id, cultivo_id, nombre, orden, duracion_dias_desde_inicio, duracion_dias_estimada, descripcion, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: fincas; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.fincas (id, nombre, ubicacion, created_at, updated_at) FROM stdin;
1	Finca El Manzano	Camara	\N	\N
\.


--
-- Data for Name: gastos; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.gastos (id, gastable_type, gastable_id, ciclo_productivo_id, categoria, naturaleza, numero_soporte, comprobante_archivo, metodo_pago, concepto, monto, fecha, descripcion, user_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: insumo_componentes; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.insumo_componentes (id, insumo_id, tipo_componente, componente, unidad, concentracion, created_at, updated_at) FROM stdin;
1	1	nutriente	Nitrógeno	%	46.0000	2026-08-26 17:59:36	2026-08-26 17:59:36
2	1	otros	Bióxido de carbono	%	0.5000	2026-08-26 17:59:36	2026-08-26 17:59:36
3	2	activo	Glifosato	%	48.0000	2026-08-26 17:59:36	2026-08-26 17:59:36
4	2	coadyuvante	Surfactante	%	5.0000	2026-08-26 17:59:36	2026-08-26 17:59:36
5	3	activo	Trichoderma harzianum	UFC/g	1000.0000	2026-08-26 17:59:36	2026-08-26 17:59:36
\.


--
-- Data for Name: insumos; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.insumos (id, categoria_id, nombre, registro_ica, ingrediente_principal, unidad_base_id, unidad_uso_id, nivel_toxicidad, estado, rei_horas, phi_dias, clasificacion_toxicologica, equipo_proteccion, franja_color, almacenamiento_temp_min, almacenamiento_temp_max, almacenamiento_humedad, requiere_refrigeracion, sensible_luz, stock_minimo, dias_aviso_vencimiento, metadata, deleted_at, created_at, updated_at) FROM stdin;
1	1	Urea 46%	ICA-1234	Nitrógeno	1	1	\N	activo	0	0	\N	\N	\N	0	40	<70%	f	f	100.00	30	\N	\N	2026-08-26 17:59:36	2026-08-26 17:59:36
2	2	Glifosato 48%	ICA-5678	Glifosato	2	2	medio	activo	12	7	II	Guantes, mascarilla, overol	amarillo	5	35	<60%	f	t	50.00	30	\N	\N	2026-08-26 17:59:36	2026-08-26 17:59:36
3	3	Trichoderma harzianum	ICA-9012	Trichoderma	3	4	bajo	activo	\N	\N	\N	Guantes	verde	4	25	<50%	t	t	10.00	60	{"cepa": "T-22", "formulacion": "polvo mojable"}	\N	2026-08-26 17:59:36	2026-08-26 17:59:36
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: labores_plantilla; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.labores_plantilla (id, cultivo_id, nombre_labor, descripcion, momento_tipo, dias_desde_siembra, fenologia_etapa_id, periodicidad_dias, duracion_estimada_horas, tipo_evento_id, requiere_insumos, requiere_mano_obra, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: lecturas_sensores_tanque; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.lecturas_sensores_tanque (id, tanque_id, lectura_distancia_cm, porcentaje_volumen, calculo_litros_actuales, fecha_hora_lectura, dispositivo_mac, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: liquidaciones_despacho; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.liquidaciones_despacho (id, despacho_id, despacho_item_id, fecha_liquidacion, numero_identificacion, precio_unitario_kg, valor_bruto_venta, comision_porcentaje, valor_comision, valor_flete_descontado, otros_descuentos, detalle_otros_descuentos, valor_neto_item, estado_pago, valor_pagado, saldo_pendiente, fecha_pago, medio_pago, observaciones, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: lotes; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.lotes (id, finca_id, nombre_lote, codigo_lote, area_hectareas_declaradas, area_hectareas_gis, altitud_mediana_msnm, pendiente_promedio_porcentaje, pendiente_terreno, tipo_suelo, ph_suelo, tiene_riego_instalado, fuente_agua, tenencia, registro_ica, geometria_gps, activo, deleted_at, created_at, updated_at) FROM stdin;
1	1	LOTE-001	123123	3.00	3.0000	2332.00	12.00	plano	arenoso	2.00	f	acueducto	propio	3	0106000020E61000000100000001030000000100000006000000F7EAE3A1EF3552C0C4279D4830A51B40B1868BDCD33552C024986A662DA51B40C007AF5DDA3552C0DBBFB2D2A4A41B40FDC1C073EF3552C0533C2EAA45A41B400A0F9A5DF73552C042D0D1AA96A41B40F7EAE3A1EF3552C0C4279D4830A51B40	t	\N	2026-08-26 18:00:43	2026-08-26 18:00:43
\.


--
-- Data for Name: lotes_analiticas_suelo; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.lotes_analiticas_suelo (id, lote_id, fecha_muestreo, numero_laboratorio_ticket, ph, conductividad_electrica_ds_m, materia_organica_porcentaje, capacidad_intercambio_cationico_meq, textura_predominante, porcentaje_arena, porcentaje_limo, porcentaje_arcilla, analista_user_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: lotes_insumos; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.lotes_insumos (id, insumo_id, proveedor_id, compra_id, codigo_lote, fecha_vencimiento, fecha_ingreso, cantidad_inicial, cantidad_actual, unidad_id, costo_unitario, estado, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: lotes_sistemas_riego; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.lotes_sistemas_riego (id, lote_id, nombre_sistema, tipo_riego, fuente_agua, caudal_diseno_litros_segundo, presion_operacion_psi, coeficiente_uniformidad, espaciamiento_emisores_metros, descarga_emisor_litros_hora, activo, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: lotes_zonas_manejo; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.lotes_zonas_manejo (id, lote_id, nombre_zona, codigo_zona, area_hectareas, geometria_zona, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: mantenimientos_maquinaria; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.mantenimientos_maquinaria (id, maquina_id, tipo_mantenimiento, fecha, descripcion_trabajo, costo_mano_obra_mecanico, costo_materiales, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: maquinaria; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.maquinaria (id, codigo_interno, nombre, tipo_maquinaria_id, marca, modelo, placa, serial, fecha_compra, valor_compra, vida_util_anios, fuente_energia, capacidad_tanque, consumo_hora, horometro_actual, kilometraje, estado, responsable_id, observaciones, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- Data for Name: mermas; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.mermas (id, recepcion_campo_id, contenedor_id, fecha_registro, kilos_merma, motivo, costo_estimado, destino_final, comentarios, client_updated_at, synced_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	0001_01_01_000003_create_tipos_evento_table	1
5	0001_01_01_000004_create_cultivos_table	1
6	0002_02_02_000002_create_trabajadores_table	1
7	0002_02_02_000003_create_clientes_table	1
8	2026_03_22_100000_create_proveedores_table	1
9	2026_03_22_142442_create_bitacoras_table	1
10	2026_03_22_143519_create_fincas_table	1
11	2026_03_22_144309_create_categorias_insumo_table	1
12	2026_03_22_144405_create_insumos_table	1
13	2026_03_22_171632_create_stock_insumos_table	1
14	2026_03_26_154757_create_lotes_table	1
15	2026_03_26_155406_create_ciclos_productivos_table	1
16	2026_03_26_999999_create_gastos_table	1
17	2026_03_27_000000_create_compras_table	1
18	2026_03_27_000001_create_compra_items_table	1
19	2026_03_28_141341_create_arboles_table	1
20	2026_04_04_000000_create_ordenes_cosecha_table	1
21	2026_04_04_000011_create_lotes_insumos_table	1
22	2026_04_04_200000_create_eventos_campo_table	1
23	2026_04_04_235212_create_sesiones_cosecha_table	1
24	2026_04_05_000356_create_recepciones_campo_table	1
25	2026_04_05_003652_create_contenedores_table	1
26	2026_04_05_011516_create_movimientos_clasificacion_table	1
27	2026_04_05_011628_create_mermas_table	1
28	2026_04_05_014033_create_despachos_table	1
29	2026_04_05_124127_create_despacho_items_table	1
30	2026_04_05_124515_create_carta_porte_table	1
31	2026_04_05_124643_create_recepciones_destino_table	1
32	2026_04_05_124812_create_liquidaciones_despacho_table	1
33	2026_06_05_191109_add_two_factor_columns_to_users_table	1
34	2026_06_05_191131_create_personal_access_tokens_table	1
35	2026_06_05_191131_create_teams_table	1
36	2026_06_05_191132_create_team_user_table	1
37	2026_06_05_191133_create_team_invitations_table	1
38	2026_06_18_141422_create_evento_mano_obra_table	1
39	2026_06_18_161028_create_maquinaria_table	1
40	2026_06_18_162159_create_evento_maquinaria_table	1
41	2026_06_18_162210_create_mantenimientos_maquinaria_table	1
42	2026_06_26_133735_create_tanques_table	1
43	2026_07_26_130115_create_movimientos_stock_table	1
\.


--
-- Data for Name: movimientos_clasificacion; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.movimientos_clasificacion (id, recepcion_campo_id, contenedor_id, kilos_asignados, observaciones, client_updated_at, synced_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: movimientos_stock; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.movimientos_stock (id, lote_insumo_id, tipo_movimiento, cantidad, movimientoable_type, movimientoable_id, stock_resultante, observacion, user_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: ordenes_cosecha; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.ordenes_cosecha (id, cliente_id, ciclo_productivo_id, lote_cultivo_id, lote_zona_id, fecha_programada, fecha_entrega, responsable_id, cantidad_solicitada_kg, variedad_requerida, cantidad_planificada_kg, cantidad_recolectada_kg, fecha_inicio, fecha_fin, estado, notas, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: pagos_liquidacion; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.pagos_liquidacion (id, liquidacion_id, despacho_id, valor_pagado, fecha_pago, medio_pago, referencia_pago, banco_origen, registrado_por, observaciones, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: proveedores; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.proveedores (id, nombre, nit, telefono, email, direccion, tiene_credito, cuente_banco_1, cuente_banco_2, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: recepcion_arboles; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.recepcion_arboles (id, recepcion_campo_id, arbol_id, peso_estimado_kg, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: recepciones_campo; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.recepciones_campo (id, sesion_cosecha_id, lote_zona_id, trabajador_id, peso_bruto, tara_costal, peso_neto, hora_pesaje, foto_evidencia, metodo_pesaje, costal_codigo, numero_corte, estado_clasificacion, client_updated_at, synced_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: recepciones_destino; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.recepciones_destino (id, despacho_id, fecha_recepcion, recibido_por, peso_recibido_kg, merma_transito_kg, estado_carga, novedad_descripcion, kg_rechazados, motivo_rechazo, observaciones, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: sesiones_cosecha; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.sesiones_cosecha (id, orden_cosecha_id, evento_campo_id, responsable_id, fecha, estado, meta_kg_dia, numero_recolectores, hora_inicio, hora_fin, total_recolectado_kg, client_updated_at, synced_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
hHYPVlZMTDiygVmUxfhObGxYcdgunlZsLtkVRHod	1	172.26.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36	eyJfdG9rZW4iOiJIMnZ4NjZsQUUyZ3Q4ck5RQTRzUzNHT2tOVWNkYmxVVVFIdnJvQ1RLIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvY2ljbG9zLXByb2R1Y3Rpdm9zXC8xXC9ncmFmby1lc3RhZGlzdGljYXMiLCJyb3V0ZSI6ImNpY2xvcy1wcm9kdWN0aXZvcy5ncmFmby1lc3RhZGlzdGljYXMifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MSwicGFzc3dvcmRfaGFzaF9zYW5jdHVtIjoiZmYzOTUxYmM4MWZlMTVkMmZkYzNkZGQ5MDcwNzU2OGMwMTc2MjZmNGY4ZDA5Y2NmMDc5MjY3MTNhNzE4YmViMiJ9	1787767336
\.


--
-- Data for Name: sistemas_riego; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.sistemas_riego (id, tanque_id, lote_id, nombre_sistema, tipo_riego, activo, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: spatial_ref_sys; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.spatial_ref_sys (srid, auth_name, auth_srid, srtext, proj4text) FROM stdin;
\.


--
-- Data for Name: stock_insumos; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.stock_insumos (id, insumo_id, cantidad_disponible, unidad_base, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: tanques; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.tanques (id, nombre, capacidad_litros, altura_maxima_cm, lote_id, nivel_actual_litros, tiene_sensor_iot, activo, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: team_invitations; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.team_invitations (id, team_id, email, role, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: team_user; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.team_user (id, team_id, user_id, role, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: teams; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.teams (id, user_id, name, personal_team, created_at, updated_at) FROM stdin;
1	1	manu's Team	t	2026-08-26 17:59:58	2026-08-26 17:59:58
\.


--
-- Data for Name: tipo_maquinaria; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.tipo_maquinaria (id, nombre, descripcion, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: tipos_evento; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.tipos_evento (id, nombre, categoria, consume_insumos, consume_mano_obra, genera_ingreso, genera_movimiento_stock, requiere_area_ha, aplica_a_arbol, aplica_a_ciclo, periodo_reingreso_horas, periodo_carencia_dias, created_at, updated_at) FROM stdin;
1	Riego	Mantenimiento	f	t	f	f	t	f	t	24	14	\N	\N
2	Deshierbe	Mantenimiento	f	t	f	f	t	f	t	24	14	\N	\N
3	Poda	Mantenimiento	f	t	f	f	f	t	t	24	14	\N	\N
4	Tutorado	Mantenimiento	t	t	f	t	f	f	t	24	14	\N	\N
5	Fertilización química	Fertilización	t	t	f	t	t	f	t	24	14	\N	\N
6	Fertilización orgánica	Fertilización	t	t	f	t	t	f	t	24	14	\N	\N
7	Encalado	Fertilización	t	t	f	t	t	f	t	24	14	\N	\N
8	Aplicación de fungicida	Fitosanitario	t	t	f	t	t	f	t	24	14	\N	\N
9	Aplicación de insecticida	Fitosanitario	t	t	f	t	t	f	t	24	14	\N	\N
10	Aplicación de herbicida	Fitosanitario	t	t	f	t	t	f	t	24	21	\N	\N
11	Aplicación de bioinsumos	Fitosanitario	t	t	f	t	t	f	t	24	14	\N	\N
12	Cosecha	Cosecha	f	t	t	t	f	f	t	24	14	\N	\N
13	Recepción de cosecha	Logística	f	t	f	t	f	f	t	24	14	\N	\N
14	Transporte	Logística	f	t	f	f	f	f	t	24	14	\N	\N
15	Clasificación	Logística	f	t	f	t	f	f	t	24	14	\N	\N
\.


--
-- Data for Name: trabajadores; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.trabajadores (id, user_id, tipo_documento, numero_documento, nombres, apellidos, fecha_nacimiento, genero, cargo, fecha_ingreso, fecha_retiro, tipo_contrato, salario_base, forma_pago, banco_numero_cuenta, eps, arl, afp, habilidades, ubicacion_actual, ultima_ubicacion_at, activo, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: unidades_medida; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.unidades_medida (id, nombre, abreviatura, tipo, factor_conversion, created_at, updated_at) FROM stdin;
1	Kilogramo	kg	masa	1.0000	\N	\N
2	Gramo	g	masa	0.0010	\N	\N
3	Litro	L	volumen	1.0000	\N	\N
4	Mililitro	mL	volumen	0.0010	\N	\N
5	Unidad	u	unidad	1.0000	\N	\N
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, current_team_id, profile_photo_path, created_at, updated_at, two_factor_secret, two_factor_recovery_codes, two_factor_confirmed_at) FROM stdin;
1	manu	manu@gmail.com	\N	$2y$12$FtX0Pz.Reoo3U4hsiGZFrulCM9G.0Dnw7OjjiRuNdR7M.o27Yrrkq	\N	1	\N	2026-08-26 17:59:58	2026-08-26 17:59:58	\N	\N	\N
\.


--
-- Data for Name: validaciones_riego; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.validaciones_riego (id, evento_riego_id, tanque_id, nivel_tanque_antes_litros, nivel_tanque_despues_litros, volumen_real_consumido_litros, volumen_estimado_litros, diferencia_litros, porcentaje_error, observaciones_auditoria, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: geocode_settings; Type: TABLE DATA; Schema: tiger; Owner: sail
--

COPY tiger.geocode_settings (name, setting, unit, category, short_desc) FROM stdin;
\.


--
-- Data for Name: pagc_gaz; Type: TABLE DATA; Schema: tiger; Owner: sail
--

COPY tiger.pagc_gaz (id, seq, word, stdword, token, is_custom) FROM stdin;
\.


--
-- Data for Name: pagc_lex; Type: TABLE DATA; Schema: tiger; Owner: sail
--

COPY tiger.pagc_lex (id, seq, word, stdword, token, is_custom) FROM stdin;
\.


--
-- Data for Name: pagc_rules; Type: TABLE DATA; Schema: tiger; Owner: sail
--

COPY tiger.pagc_rules (id, rule, is_custom) FROM stdin;
\.


--
-- Data for Name: topology; Type: TABLE DATA; Schema: topology; Owner: sail
--

COPY topology.topology (id, name, srid, "precision", hasz) FROM stdin;
\.


--
-- Data for Name: layer; Type: TABLE DATA; Schema: topology; Owner: sail
--

COPY topology.layer (topology_id, layer_id, schema_name, table_name, feature_column, feature_type, level, child_id) FROM stdin;
\.


--
-- Name: arboles_historial_fitosanitario_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.arboles_historial_fitosanitario_id_seq', 1, false);


--
-- Name: arboles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.arboles_id_seq', 1000, true);


--
-- Name: arboles_metricas_historicas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.arboles_metricas_historicas_id_seq', 1, false);


--
-- Name: arboles_red_vecindad_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.arboles_red_vecindad_id_seq', 3807, true);


--
-- Name: bitacoras_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.bitacoras_id_seq', 1, false);


--
-- Name: carta_porte_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.carta_porte_id_seq', 1, false);


--
-- Name: categorias_insumo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.categorias_insumo_id_seq', 5, true);


--
-- Name: ciclo_productivo_zona_manejo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.ciclo_productivo_zona_manejo_id_seq', 1, false);


--
-- Name: ciclos_productivos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.ciclos_productivos_id_seq', 1, true);


--
-- Name: clientes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.clientes_id_seq', 1, false);


--
-- Name: componentes_riego_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.componentes_riego_id_seq', 1, false);


--
-- Name: compra_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.compra_items_id_seq', 1, false);


--
-- Name: compras_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.compras_id_seq', 1, false);


--
-- Name: compras_pagos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.compras_pagos_id_seq', 1, false);


--
-- Name: cultivos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.cultivos_id_seq', 4, true);


--
-- Name: despacho_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.despacho_items_id_seq', 1, false);


--
-- Name: despachos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.despachos_id_seq', 1, false);


--
-- Name: evento_arbol_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.evento_arbol_id_seq', 1, false);


--
-- Name: evento_insumo_lotes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.evento_insumo_lotes_id_seq', 1, false);


--
-- Name: evento_insumos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.evento_insumos_id_seq', 1, false);


--
-- Name: evento_mano_obra_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.evento_mano_obra_id_seq', 1, false);


--
-- Name: evento_maquinaria_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.evento_maquinaria_id_seq', 1, false);


--
-- Name: evento_riego_componentes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.evento_riego_componentes_id_seq', 1, false);


--
-- Name: eventos_campo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.eventos_campo_id_seq', 1, false);


--
-- Name: eventos_riego_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.eventos_riego_id_seq', 1, false);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: fenologia_etapas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.fenologia_etapas_id_seq', 1, false);


--
-- Name: fincas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.fincas_id_seq', 1, true);


--
-- Name: gastos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.gastos_id_seq', 1, false);


--
-- Name: insumo_componentes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.insumo_componentes_id_seq', 5, true);


--
-- Name: insumos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.insumos_id_seq', 3, true);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: labores_plantilla_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.labores_plantilla_id_seq', 1, false);


--
-- Name: liquidaciones_despacho_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.liquidaciones_despacho_id_seq', 1, false);


--
-- Name: lotes_analiticas_suelo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.lotes_analiticas_suelo_id_seq', 1, false);


--
-- Name: lotes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.lotes_id_seq', 1, true);


--
-- Name: lotes_insumos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.lotes_insumos_id_seq', 1, false);


--
-- Name: lotes_sistemas_riego_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.lotes_sistemas_riego_id_seq', 1, false);


--
-- Name: lotes_zonas_manejo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.lotes_zonas_manejo_id_seq', 1, false);


--
-- Name: mantenimientos_maquinaria_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.mantenimientos_maquinaria_id_seq', 1, false);


--
-- Name: maquinaria_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.maquinaria_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.migrations_id_seq', 43, true);


--
-- Name: movimientos_stock_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.movimientos_stock_id_seq', 1, false);


--
-- Name: ordenes_cosecha_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.ordenes_cosecha_id_seq', 1, false);


--
-- Name: pagos_liquidacion_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.pagos_liquidacion_id_seq', 1, false);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 1, false);


--
-- Name: proveedores_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.proveedores_id_seq', 1, false);


--
-- Name: recepciones_destino_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.recepciones_destino_id_seq', 1, false);


--
-- Name: sistemas_riego_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.sistemas_riego_id_seq', 1, false);


--
-- Name: stock_insumos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.stock_insumos_id_seq', 1, false);


--
-- Name: tanques_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.tanques_id_seq', 1, false);


--
-- Name: team_invitations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.team_invitations_id_seq', 1, false);


--
-- Name: team_user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.team_user_id_seq', 1, false);


--
-- Name: teams_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.teams_id_seq', 1, true);


--
-- Name: tipo_maquinaria_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.tipo_maquinaria_id_seq', 1, false);


--
-- Name: tipos_evento_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.tipos_evento_id_seq', 15, true);


--
-- Name: trabajadores_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.trabajadores_id_seq', 1, false);


--
-- Name: unidades_medida_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.unidades_medida_id_seq', 5, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.users_id_seq', 1, true);


--
-- Name: validaciones_riego_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.validaciones_riego_id_seq', 1, false);


--
-- Name: topology_id_seq; Type: SEQUENCE SET; Schema: topology; Owner: sail
--

SELECT pg_catalog.setval('topology.topology_id_seq', 1, false);


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
-- Name: arboles_red_vecindad arboles_red_vecindad_arbol_origen_id_arbol_destino_id_unique; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.arboles_red_vecindad
    ADD CONSTRAINT arboles_red_vecindad_arbol_origen_id_arbol_destino_id_unique UNIQUE (arbol_origen_id, arbol_destino_id);


--
-- Name: arboles_red_vecindad arboles_red_vecindad_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.arboles_red_vecindad
    ADD CONSTRAINT arboles_red_vecindad_pkey PRIMARY KEY (id);


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
-- Name: ciclo_productivo_zona_manejo ciclo_productivo_zona_manejo_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.ciclo_productivo_zona_manejo
    ADD CONSTRAINT ciclo_productivo_zona_manejo_pkey PRIMARY KEY (id);


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
-- Name: recepcion_arboles recepcion_arboles_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.recepcion_arboles
    ADD CONSTRAINT recepcion_arboles_pkey PRIMARY KEY (id);


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
-- Name: unidades_medida unidades_medida_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.unidades_medida
    ADD CONSTRAINT unidades_medida_pkey PRIMARY KEY (id);


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
-- Name: arboles_red_vecindad_arbol_origen_id_distancia_metros_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX arboles_red_vecindad_arbol_origen_id_distancia_metros_index ON public.arboles_red_vecindad USING btree (arbol_origen_id, distancia_metros);


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
-- Name: idx_nomina_trabajador; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX idx_nomina_trabajador ON public.evento_mano_obra USING btree (trabajador_id, estado_pago);


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
-- Name: arboles_red_vecindad arboles_red_vecindad_arbol_destino_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.arboles_red_vecindad
    ADD CONSTRAINT arboles_red_vecindad_arbol_destino_id_foreign FOREIGN KEY (arbol_destino_id) REFERENCES public.arboles(id) ON DELETE CASCADE;


--
-- Name: arboles_red_vecindad arboles_red_vecindad_arbol_origen_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.arboles_red_vecindad
    ADD CONSTRAINT arboles_red_vecindad_arbol_origen_id_foreign FOREIGN KEY (arbol_origen_id) REFERENCES public.arboles(id) ON DELETE CASCADE;


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
-- Name: ciclo_productivo_zona_manejo ciclo_productivo_zona_manejo_ciclo_productivo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.ciclo_productivo_zona_manejo
    ADD CONSTRAINT ciclo_productivo_zona_manejo_ciclo_productivo_id_foreign FOREIGN KEY (ciclo_productivo_id) REFERENCES public.ciclos_productivos(id) ON DELETE CASCADE;


--
-- Name: ciclo_productivo_zona_manejo ciclo_productivo_zona_manejo_zona_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.ciclo_productivo_zona_manejo
    ADD CONSTRAINT ciclo_productivo_zona_manejo_zona_id_foreign FOREIGN KEY (zona_id) REFERENCES public.lotes_zonas_manejo(id) ON DELETE CASCADE;


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
-- Name: insumos insumos_unidad_base_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.insumos
    ADD CONSTRAINT insumos_unidad_base_id_foreign FOREIGN KEY (unidad_base_id) REFERENCES public.unidades_medida(id);


--
-- Name: insumos insumos_unidad_uso_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.insumos
    ADD CONSTRAINT insumos_unidad_uso_id_foreign FOREIGN KEY (unidad_uso_id) REFERENCES public.unidades_medida(id);


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
-- Name: lotes_insumos lotes_insumos_compra_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.lotes_insumos
    ADD CONSTRAINT lotes_insumos_compra_id_foreign FOREIGN KEY (compra_id) REFERENCES public.compras(id);


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
-- Name: lotes_insumos lotes_insumos_unidad_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.lotes_insumos
    ADD CONSTRAINT lotes_insumos_unidad_id_foreign FOREIGN KEY (unidad_id) REFERENCES public.unidades_medida(id);


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
-- Name: recepcion_arboles recepcion_arboles_arbol_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.recepcion_arboles
    ADD CONSTRAINT recepcion_arboles_arbol_id_foreign FOREIGN KEY (arbol_id) REFERENCES public.arboles(id) ON DELETE CASCADE;


--
-- Name: recepcion_arboles recepcion_arboles_recepcion_campo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.recepcion_arboles
    ADD CONSTRAINT recepcion_arboles_recepcion_campo_id_foreign FOREIGN KEY (recepcion_campo_id) REFERENCES public.recepciones_campo(id) ON DELETE CASCADE;


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

