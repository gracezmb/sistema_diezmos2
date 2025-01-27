PGDMP  ;    :                |         	   db_sobre4    17.2    17.2 A    D           0    0    ENCODING    ENCODING        SET client_encoding = 'UTF8';
                           false            E           0    0 
   STDSTRINGS 
   STDSTRINGS     (   SET standard_conforming_strings = 'on';
                           false            F           0    0 
   SEARCHPATH 
   SEARCHPATH     8   SELECT pg_catalog.set_config('search_path', '', false);
                           false            G           1262    16923 	   db_sobre4    DATABASE     |   CREATE DATABASE db_sobre4 WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'Spanish_Spain.1252';
    DROP DATABASE db_sobre4;
                     postgres    false            �            1255    16980    check_sobre_unique()    FUNCTION     �  CREATE FUNCTION public.check_sobre_unique() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF EXISTS (
        SELECT 1
        FROM sobres
        WHERE numero_sobre = NEW.numero_sobre
        AND EXTRACT(YEAR FROM fecha) = EXTRACT(YEAR FROM NEW.fecha)
        AND EXTRACT(MONTH FROM fecha) = EXTRACT(MONTH FROM NEW.fecha)
        AND id != NEW.id
    ) THEN
        RAISE EXCEPTION 'Ya existe un sobre con este número para el mismo mes y año';
    END IF;
    RETURN NEW;
END;
$$;
 +   DROP FUNCTION public.check_sobre_unique();
       public               postgres    false            �            1259    16947    bancos    TABLE     �   CREATE TABLE public.bancos (
    id integer NOT NULL,
    nombre character varying(100) NOT NULL,
    codigo character varying(20),
    activo boolean DEFAULT true
);
    DROP TABLE public.bancos;
       public         heap r       postgres    false            �            1259    16946    bancos_id_seq    SEQUENCE     �   CREATE SEQUENCE public.bancos_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 $   DROP SEQUENCE public.bancos_id_seq;
       public               postgres    false    222            H           0    0    bancos_id_seq    SEQUENCE OWNED BY     ?   ALTER SEQUENCE public.bancos_id_seq OWNED BY public.bancos.id;
          public               postgres    false    221            �            1259    16956    personas    TABLE     �  CREATE TABLE public.personas (
    id_cedula character varying(20) NOT NULL,
    nombre character varying(100) NOT NULL,
    apellido character varying(100) NOT NULL,
    email character varying(100),
    telefono character varying(20),
    direccion text,
    iglesia character varying(100),
    fecha_registro timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    activo boolean DEFAULT true
);
    DROP TABLE public.personas;
       public         heap r       postgres    false            �            1259    16983    sobre_ofrendas    TABLE     �   CREATE TABLE public.sobre_ofrendas (
    id integer NOT NULL,
    id_sobre integer,
    id_tipo_ofrenda integer,
    id_tipo_moneda integer,
    monto numeric(10,2) NOT NULL,
    fecha_registro timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);
 "   DROP TABLE public.sobre_ofrendas;
       public         heap r       postgres    false            �            1259    16982    sobre_ofrendas_id_seq    SEQUENCE     �   CREATE SEQUENCE public.sobre_ofrendas_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 ,   DROP SEQUENCE public.sobre_ofrendas_id_seq;
       public               postgres    false    227            I           0    0    sobre_ofrendas_id_seq    SEQUENCE OWNED BY     O   ALTER SEQUENCE public.sobre_ofrendas_id_seq OWNED BY public.sobre_ofrendas.id;
          public               postgres    false    226            �            1259    16966    sobres    TABLE     �   CREATE TABLE public.sobres (
    id integer NOT NULL,
    numero_sobre integer NOT NULL,
    fecha date NOT NULL,
    id_persona character varying(20),
    fecha_registro timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    observaciones text
);
    DROP TABLE public.sobres;
       public         heap r       postgres    false            �            1259    16965    sobres_id_seq    SEQUENCE     �   CREATE SEQUENCE public.sobres_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 $   DROP SEQUENCE public.sobres_id_seq;
       public               postgres    false    225            J           0    0    sobres_id_seq    SEQUENCE OWNED BY     ?   ALTER SEQUENCE public.sobres_id_seq OWNED BY public.sobres.id;
          public               postgres    false    224            �            1259    16925    tipos_moneda    TABLE     �   CREATE TABLE public.tipos_moneda (
    id integer NOT NULL,
    codigo character varying(3) NOT NULL,
    nombre character varying(50) NOT NULL,
    simbolo character varying(5) NOT NULL,
    activo boolean DEFAULT true
);
     DROP TABLE public.tipos_moneda;
       public         heap r       postgres    false            �            1259    16924    tipos_moneda_id_seq    SEQUENCE     �   CREATE SEQUENCE public.tipos_moneda_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 *   DROP SEQUENCE public.tipos_moneda_id_seq;
       public               postgres    false    218            K           0    0    tipos_moneda_id_seq    SEQUENCE OWNED BY     K   ALTER SEQUENCE public.tipos_moneda_id_seq OWNED BY public.tipos_moneda.id;
          public               postgres    false    217            �            1259    16935    tipos_ofrenda    TABLE     �   CREATE TABLE public.tipos_ofrenda (
    id integer NOT NULL,
    nombre character varying(100) NOT NULL,
    descripcion text,
    activo boolean DEFAULT true
);
 !   DROP TABLE public.tipos_ofrenda;
       public         heap r       postgres    false            �            1259    16934    tipos_ofrenda_id_seq    SEQUENCE     �   CREATE SEQUENCE public.tipos_ofrenda_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 +   DROP SEQUENCE public.tipos_ofrenda_id_seq;
       public               postgres    false    220            L           0    0    tipos_ofrenda_id_seq    SEQUENCE OWNED BY     M   ALTER SEQUENCE public.tipos_ofrenda_id_seq OWNED BY public.tipos_ofrenda.id;
          public               postgres    false    219            �            1259    17006    transferencias    TABLE     v  CREATE TABLE public.transferencias (
    id integer NOT NULL,
    id_sobre integer,
    fecha_transf date NOT NULL,
    num_transferencia character varying(50),
    id_banco integer,
    id_tipo_moneda integer,
    monto_transferencia numeric(10,2) NOT NULL,
    banco_otro character varying(100),
    fecha_registro timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);
 "   DROP TABLE public.transferencias;
       public         heap r       postgres    false            �            1259    17005    transferencias_id_seq    SEQUENCE     �   CREATE SEQUENCE public.transferencias_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 ,   DROP SEQUENCE public.transferencias_id_seq;
       public               postgres    false    229            M           0    0    transferencias_id_seq    SEQUENCE OWNED BY     O   ALTER SEQUENCE public.transferencias_id_seq OWNED BY public.transferencias.id;
          public               postgres    false    228            z           2604    16950 	   bancos id    DEFAULT     f   ALTER TABLE ONLY public.bancos ALTER COLUMN id SET DEFAULT nextval('public.bancos_id_seq'::regclass);
 8   ALTER TABLE public.bancos ALTER COLUMN id DROP DEFAULT;
       public               postgres    false    221    222    222            �           2604    16986    sobre_ofrendas id    DEFAULT     v   ALTER TABLE ONLY public.sobre_ofrendas ALTER COLUMN id SET DEFAULT nextval('public.sobre_ofrendas_id_seq'::regclass);
 @   ALTER TABLE public.sobre_ofrendas ALTER COLUMN id DROP DEFAULT;
       public               postgres    false    227    226    227            ~           2604    16969 	   sobres id    DEFAULT     f   ALTER TABLE ONLY public.sobres ALTER COLUMN id SET DEFAULT nextval('public.sobres_id_seq'::regclass);
 8   ALTER TABLE public.sobres ALTER COLUMN id DROP DEFAULT;
       public               postgres    false    225    224    225            v           2604    16928    tipos_moneda id    DEFAULT     r   ALTER TABLE ONLY public.tipos_moneda ALTER COLUMN id SET DEFAULT nextval('public.tipos_moneda_id_seq'::regclass);
 >   ALTER TABLE public.tipos_moneda ALTER COLUMN id DROP DEFAULT;
       public               postgres    false    217    218    218            x           2604    16938    tipos_ofrenda id    DEFAULT     t   ALTER TABLE ONLY public.tipos_ofrenda ALTER COLUMN id SET DEFAULT nextval('public.tipos_ofrenda_id_seq'::regclass);
 ?   ALTER TABLE public.tipos_ofrenda ALTER COLUMN id DROP DEFAULT;
       public               postgres    false    219    220    220            �           2604    17009    transferencias id    DEFAULT     v   ALTER TABLE ONLY public.transferencias ALTER COLUMN id SET DEFAULT nextval('public.transferencias_id_seq'::regclass);
 @   ALTER TABLE public.transferencias ALTER COLUMN id DROP DEFAULT;
       public               postgres    false    228    229    229            :          0    16947    bancos 
   TABLE DATA           <   COPY public.bancos (id, nombre, codigo, activo) FROM stdin;
    public               postgres    false    222   vQ       ;          0    16956    personas 
   TABLE DATA           |   COPY public.personas (id_cedula, nombre, apellido, email, telefono, direccion, iglesia, fecha_registro, activo) FROM stdin;
    public               postgres    false    223   �Q       ?          0    16983    sobre_ofrendas 
   TABLE DATA           n   COPY public.sobre_ofrendas (id, id_sobre, id_tipo_ofrenda, id_tipo_moneda, monto, fecha_registro) FROM stdin;
    public               postgres    false    227   �Q       =          0    16966    sobres 
   TABLE DATA           d   COPY public.sobres (id, numero_sobre, fecha, id_persona, fecha_registro, observaciones) FROM stdin;
    public               postgres    false    225   �Q       6          0    16925    tipos_moneda 
   TABLE DATA           K   COPY public.tipos_moneda (id, codigo, nombre, simbolo, activo) FROM stdin;
    public               postgres    false    218   �Q       8          0    16935    tipos_ofrenda 
   TABLE DATA           H   COPY public.tipos_ofrenda (id, nombre, descripcion, activo) FROM stdin;
    public               postgres    false    220   R       A          0    17006    transferencias 
   TABLE DATA           �   COPY public.transferencias (id, id_sobre, fecha_transf, num_transferencia, id_banco, id_tipo_moneda, monto_transferencia, banco_otro, fecha_registro) FROM stdin;
    public               postgres    false    229   $R       N           0    0    bancos_id_seq    SEQUENCE SET     ;   SELECT pg_catalog.setval('public.bancos_id_seq', 4, true);
          public               postgres    false    221            O           0    0    sobre_ofrendas_id_seq    SEQUENCE SET     C   SELECT pg_catalog.setval('public.sobre_ofrendas_id_seq', 5, true);
          public               postgres    false    226            P           0    0    sobres_id_seq    SEQUENCE SET     ;   SELECT pg_catalog.setval('public.sobres_id_seq', 3, true);
          public               postgres    false    224            Q           0    0    tipos_moneda_id_seq    SEQUENCE SET     A   SELECT pg_catalog.setval('public.tipos_moneda_id_seq', 4, true);
          public               postgres    false    217            R           0    0    tipos_ofrenda_id_seq    SEQUENCE SET     B   SELECT pg_catalog.setval('public.tipos_ofrenda_id_seq', 3, true);
          public               postgres    false    219            S           0    0    transferencias_id_seq    SEQUENCE SET     C   SELECT pg_catalog.setval('public.transferencias_id_seq', 3, true);
          public               postgres    false    228            �           2606    16955    bancos bancos_nombre_key 
   CONSTRAINT     U   ALTER TABLE ONLY public.bancos
    ADD CONSTRAINT bancos_nombre_key UNIQUE (nombre);
 B   ALTER TABLE ONLY public.bancos DROP CONSTRAINT bancos_nombre_key;
       public                 postgres    false    222            �           2606    16953    bancos bancos_pkey 
   CONSTRAINT     P   ALTER TABLE ONLY public.bancos
    ADD CONSTRAINT bancos_pkey PRIMARY KEY (id);
 <   ALTER TABLE ONLY public.bancos DROP CONSTRAINT bancos_pkey;
       public                 postgres    false    222            �           2606    16964    personas personas_pkey 
   CONSTRAINT     [   ALTER TABLE ONLY public.personas
    ADD CONSTRAINT personas_pkey PRIMARY KEY (id_cedula);
 @   ALTER TABLE ONLY public.personas DROP CONSTRAINT personas_pkey;
       public                 postgres    false    223            �           2606    16989 "   sobre_ofrendas sobre_ofrendas_pkey 
   CONSTRAINT     `   ALTER TABLE ONLY public.sobre_ofrendas
    ADD CONSTRAINT sobre_ofrendas_pkey PRIMARY KEY (id);
 L   ALTER TABLE ONLY public.sobre_ofrendas DROP CONSTRAINT sobre_ofrendas_pkey;
       public                 postgres    false    227            �           2606    16974    sobres sobres_pkey 
   CONSTRAINT     P   ALTER TABLE ONLY public.sobres
    ADD CONSTRAINT sobres_pkey PRIMARY KEY (id);
 <   ALTER TABLE ONLY public.sobres DROP CONSTRAINT sobres_pkey;
       public                 postgres    false    225            �           2606    16933 $   tipos_moneda tipos_moneda_codigo_key 
   CONSTRAINT     a   ALTER TABLE ONLY public.tipos_moneda
    ADD CONSTRAINT tipos_moneda_codigo_key UNIQUE (codigo);
 N   ALTER TABLE ONLY public.tipos_moneda DROP CONSTRAINT tipos_moneda_codigo_key;
       public                 postgres    false    218            �           2606    16931    tipos_moneda tipos_moneda_pkey 
   CONSTRAINT     \   ALTER TABLE ONLY public.tipos_moneda
    ADD CONSTRAINT tipos_moneda_pkey PRIMARY KEY (id);
 H   ALTER TABLE ONLY public.tipos_moneda DROP CONSTRAINT tipos_moneda_pkey;
       public                 postgres    false    218            �           2606    16945 &   tipos_ofrenda tipos_ofrenda_nombre_key 
   CONSTRAINT     c   ALTER TABLE ONLY public.tipos_ofrenda
    ADD CONSTRAINT tipos_ofrenda_nombre_key UNIQUE (nombre);
 P   ALTER TABLE ONLY public.tipos_ofrenda DROP CONSTRAINT tipos_ofrenda_nombre_key;
       public                 postgres    false    220            �           2606    16943     tipos_ofrenda tipos_ofrenda_pkey 
   CONSTRAINT     ^   ALTER TABLE ONLY public.tipos_ofrenda
    ADD CONSTRAINT tipos_ofrenda_pkey PRIMARY KEY (id);
 J   ALTER TABLE ONLY public.tipos_ofrenda DROP CONSTRAINT tipos_ofrenda_pkey;
       public                 postgres    false    220            �           2606    17012 "   transferencias transferencias_pkey 
   CONSTRAINT     `   ALTER TABLE ONLY public.transferencias
    ADD CONSTRAINT transferencias_pkey PRIMARY KEY (id);
 L   ALTER TABLE ONLY public.transferencias DROP CONSTRAINT transferencias_pkey;
       public                 postgres    false    229            �           1259    17031    idx_sobre_ofrendas_sobre    INDEX     W   CREATE INDEX idx_sobre_ofrendas_sobre ON public.sobre_ofrendas USING btree (id_sobre);
 ,   DROP INDEX public.idx_sobre_ofrendas_sobre;
       public                 postgres    false    227            �           1259    17028    idx_sobres_fecha    INDEX     D   CREATE INDEX idx_sobres_fecha ON public.sobres USING btree (fecha);
 $   DROP INDEX public.idx_sobres_fecha;
       public                 postgres    false    225            �           1259    17029    idx_sobres_persona    INDEX     K   CREATE INDEX idx_sobres_persona ON public.sobres USING btree (id_persona);
 &   DROP INDEX public.idx_sobres_persona;
       public                 postgres    false    225            �           1259    17030    idx_transferencias_sobre    INDEX     W   CREATE INDEX idx_transferencias_sobre ON public.transferencias USING btree (id_sobre);
 ,   DROP INDEX public.idx_transferencias_sobre;
       public                 postgres    false    229            �           2620    16981    sobres ensure_sobre_unique    TRIGGER     �   CREATE TRIGGER ensure_sobre_unique BEFORE INSERT OR UPDATE ON public.sobres FOR EACH ROW EXECUTE FUNCTION public.check_sobre_unique();
 3   DROP TRIGGER ensure_sobre_unique ON public.sobres;
       public               postgres    false    230    225            �           2606    16990 +   sobre_ofrendas sobre_ofrendas_id_sobre_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.sobre_ofrendas
    ADD CONSTRAINT sobre_ofrendas_id_sobre_fkey FOREIGN KEY (id_sobre) REFERENCES public.sobres(id);
 U   ALTER TABLE ONLY public.sobre_ofrendas DROP CONSTRAINT sobre_ofrendas_id_sobre_fkey;
       public               postgres    false    225    227    4757            �           2606    17000 1   sobre_ofrendas sobre_ofrendas_id_tipo_moneda_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.sobre_ofrendas
    ADD CONSTRAINT sobre_ofrendas_id_tipo_moneda_fkey FOREIGN KEY (id_tipo_moneda) REFERENCES public.tipos_moneda(id);
 [   ALTER TABLE ONLY public.sobre_ofrendas DROP CONSTRAINT sobre_ofrendas_id_tipo_moneda_fkey;
       public               postgres    false    218    227    4743            �           2606    16995 2   sobre_ofrendas sobre_ofrendas_id_tipo_ofrenda_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.sobre_ofrendas
    ADD CONSTRAINT sobre_ofrendas_id_tipo_ofrenda_fkey FOREIGN KEY (id_tipo_ofrenda) REFERENCES public.tipos_ofrenda(id);
 \   ALTER TABLE ONLY public.sobre_ofrendas DROP CONSTRAINT sobre_ofrendas_id_tipo_ofrenda_fkey;
       public               postgres    false    227    4747    220            �           2606    16975    sobres sobres_id_persona_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.sobres
    ADD CONSTRAINT sobres_id_persona_fkey FOREIGN KEY (id_persona) REFERENCES public.personas(id_cedula);
 G   ALTER TABLE ONLY public.sobres DROP CONSTRAINT sobres_id_persona_fkey;
       public               postgres    false    225    4753    223            �           2606    17018 +   transferencias transferencias_id_banco_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.transferencias
    ADD CONSTRAINT transferencias_id_banco_fkey FOREIGN KEY (id_banco) REFERENCES public.bancos(id);
 U   ALTER TABLE ONLY public.transferencias DROP CONSTRAINT transferencias_id_banco_fkey;
       public               postgres    false    222    4751    229            �           2606    17013 +   transferencias transferencias_id_sobre_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.transferencias
    ADD CONSTRAINT transferencias_id_sobre_fkey FOREIGN KEY (id_sobre) REFERENCES public.sobres(id);
 U   ALTER TABLE ONLY public.transferencias DROP CONSTRAINT transferencias_id_sobre_fkey;
       public               postgres    false    4757    229    225            �           2606    17023 1   transferencias transferencias_id_tipo_moneda_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.transferencias
    ADD CONSTRAINT transferencias_id_tipo_moneda_fkey FOREIGN KEY (id_tipo_moneda) REFERENCES public.tipos_moneda(id);
 [   ALTER TABLE ONLY public.transferencias DROP CONSTRAINT transferencias_id_tipo_moneda_fkey;
       public               postgres    false    229    218    4743            :      x������ � �      ;      x������ � �      ?      x������ � �      =      x������ � �      6      x������ � �      8      x������ � �      A      x������ � �     