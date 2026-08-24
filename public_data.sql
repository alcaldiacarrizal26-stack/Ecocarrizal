SET session_replication_role = replica;

--
-- PostgreSQL database dump
--

-- \restrict nyOCAZnCIBE72Sbb9WxaB46Xsui9rGLwpAgK5CezrcEwKjbJb8eP72VlGMfe7cw

-- Dumped from database version 17.6
-- Dumped by pg_dump version 17.6

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Data for Name: direcciones; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO "public"."direcciones" ("id_direccion", "nombre_direc", "descripcion_direc", "created_at") VALUES
	(1, 'Ecosocialismo', 'Gestión ambiental y denuncias', '2026-08-13 20:32:58.08634+00'),
	(2, 'Residuos', 'Gestión de residuos sólidos', '2026-08-13 20:32:58.08634+00');


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO "public"."roles" ("id_roles", "nombre", "descripcion", "created_at") VALUES
	(1, 'admin', 'Acceso total al sistema', '2026-08-13 03:28:03.243442+00'),
	(2, 'director', 'Director de dirección (Ecosocialismo o Residuos)', '2026-08-13 03:28:03.243442+00'),
	(3, 'ciudadano', 'Usuario regular', '2026-08-13 03:28:03.243442+00'),
	(4, 'inspector', 'Inspector de campo', '2026-08-13 03:28:03.243442+00');


--
-- Data for Name: profiles; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO "public"."profiles" ("id_profiles", "email", "nombres", "apellidos", "cedula", "telefono", "accepted_terms", "role_id", "is_active", "created_at", "updated_at", "direction_id") VALUES
	('99c6a4d2-d8f0-4c38-a23c-62c0d5c3e5a7', 'paolariveratm1985@gmail.com', 'Paola', 'Rivera', 'V-29533074', '0412-9671838', true, 2, true, '2026-08-13 20:07:12.047942+00', '2026-08-14 00:22:55.472249+00', 1),
	('29d80632-d7a2-4b10-80e7-2eccbddb1568', 'cristhianb397@gmail.com', 'Cristhian', 'Blanco', 'V-30541863', '0412-5994461', true, 3, true, '2026-08-14 13:57:07.931867+00', '2026-08-14 13:57:07.931867+00', NULL),
	('6001894e-9640-4619-bc56-a506046e8d86', 'bermudezpao340@gmail.com', 'Paola', 'Bermudez', 'V-29533075', '0424-1960818', true, 3, true, '2026-08-14 14:17:13.463591+00', '2026-08-14 14:17:13.463591+00', NULL),
	('22e120a2-3cda-4197-bdb3-536806df7ac7', 'plus.instala@gmail.com', 'Paola', 'Bermudez', 'V-29533078', '0424-1960818', true, 3, true, '2026-08-14 15:14:31.521466+00', '2026-08-14 15:14:31.521466+00', NULL),
	('91889163-12b1-4726-b97a-a3cdd9cffd70', 'crismancoinbase@gmail.com', 'Inspector', 'Random', 'V-8624329', '0416-2754637', true, 4, true, '2026-08-19 15:40:07.785346+00', '2026-08-19 15:41:07.894615+00', NULL),
	('09aa157e-955e-484b-a764-b5409207eee1', 'crismantlf@gmail.com', 'Director', 'Crisman', 'V-18563245', '0412-6781523', true, 2, true, '2026-08-17 15:38:22.331368+00', '2026-08-19 16:29:51.442348+00', 2);


--
-- Data for Name: denuncias; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO "public"."denuncias" ("id", "user_id", "tipo_contaminacion", "sector", "punto_referencia", "descripcion", "evidencias", "created_at") VALUES
	('DEN-2026-0001', '29d80632-d7a2-4b10-80e7-2eccbddb1568', 'desechos', 'Brisas de Oriente', 'Lorem Ipsum', 'wevqweasdv', '{V-30541863/DEN-2026-0001/foto_1_1787150060043.jpeg}', '2026-08-19 14:34:01.052002+00'),
	('DEN-2026-0002', '29d80632-d7a2-4b10-80e7-2eccbddb1568', 'humo', 'Montaña Alta', 'Frente a los Edificios Verdes', 'Lorem Ipsum', '{V-30541863/DEN-2026-0002/foto_1_1787151287771.jpeg}', '2026-08-19 14:54:28.883941+00');


--
-- Data for Name: empresas; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Data for Name: documentos_expediente; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Data for Name: login_attempts; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO "public"."login_attempts" ("email", "attempt_count", "last_failed_at", "locked_until") VALUES
	('paolariverattm1985@gmail.com', 1, '2026-08-14 03:05:18.534962+00', NULL),
	('bermudezpao340@gmail.com', 0, '2026-08-14 14:25:25.625642+00', '2026-08-14 14:40:25.625642+00'),
	('paolariveratm1985@gmail.com', 1, '2026-08-17 15:37:30.687103+00', NULL);


--
-- Data for Name: perfiles_ambientales; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Data for Name: solicitudes; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Data for Name: solicitud_recaudos; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Name: direcciones_id_direccion_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('"public"."direcciones_id_direccion_seq"', 2, true);


--
-- Name: roles_id_roles_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('"public"."roles_id_roles_seq"', 4, true);


--
-- PostgreSQL database dump complete
--

-- \unrestrict nyOCAZnCIBE72Sbb9WxaB46Xsui9rGLwpAgK5CezrcEwKjbJb8eP72VlGMfe7cw

RESET ALL;
