SET session_replication_role = replica;

--
-- PostgreSQL database dump
--

-- \restrict lBfsh4dBl1csE8ATse3kFmgCoAwiTKR9KgueX1ZGGezsTWuyzwMlGpaTeY1xlu9

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
-- Data for Name: audit_log_entries; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--



--
-- Data for Name: custom_oauth_providers; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--



--
-- Data for Name: flow_state; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

INSERT INTO "auth"."flow_state" ("id", "user_id", "auth_code", "code_challenge_method", "code_challenge", "provider_type", "provider_access_token", "provider_refresh_token", "created_at", "updated_at", "authentication_method", "auth_code_issued_at", "invite_token", "referrer", "oauth_client_state_id", "linking_target_id", "email_optional") VALUES
	('f27d1548-15fb-42d7-aa78-e2305bafcded', NULL, NULL, NULL, NULL, 'google', '', '', '2026-08-13 23:15:09.64353+00', '2026-08-13 23:15:09.64353+00', 'oauth', NULL, NULL, 'http://localhost:3000/auth/callback', NULL, NULL, false),
	('2a014aa9-d34d-4d84-8d9d-f594aa125464', NULL, NULL, NULL, NULL, 'google', '', '', '2026-08-13 23:15:56.875223+00', '2026-08-13 23:15:56.875223+00', 'oauth', NULL, NULL, 'http://localhost:3000/auth/callback', NULL, NULL, false),
	('798d2be2-689f-4665-9fff-b97f20d17d9d', NULL, NULL, NULL, NULL, 'google', '', '', '2026-08-13 23:31:35.148193+00', '2026-08-13 23:31:35.148193+00', 'oauth', NULL, NULL, 'http://localhost:3000/auth/callback', NULL, NULL, false),
	('3ef18523-78a3-4751-a6d4-bb893f5df09e', NULL, NULL, NULL, NULL, 'google', '', '', '2026-08-13 23:36:29.435208+00', '2026-08-13 23:36:29.435208+00', 'oauth', NULL, NULL, 'http://localhost:3000/auth/callback', NULL, NULL, false),
	('fc3bc955-6e58-48a7-900a-3c50f175147a', NULL, NULL, NULL, NULL, 'google', '', '', '2026-08-13 23:38:28.884871+00', '2026-08-13 23:38:28.884871+00', 'oauth', NULL, NULL, 'http://localhost:3000/auth/callback', NULL, NULL, false),
	('1a9bb6d8-eb80-499f-be9c-bc119f433501', NULL, NULL, NULL, NULL, 'google', '', '', '2026-08-13 23:42:34.194734+00', '2026-08-13 23:42:34.194734+00', 'oauth', NULL, NULL, 'http://localhost:3000/auth/callback', NULL, NULL, false),
	('765d4e73-794f-4343-9a7e-749045504257', NULL, NULL, NULL, NULL, 'google', '', '', '2026-08-13 23:43:46.347627+00', '2026-08-13 23:43:46.347627+00', 'oauth', NULL, NULL, 'http://localhost:3000/auth/callback', NULL, NULL, false),
	('6c505a78-35a5-46b5-8917-ccd8a29433e2', NULL, NULL, NULL, NULL, 'google', '', '', '2026-08-14 00:15:04.04637+00', '2026-08-14 00:15:04.04637+00', 'oauth', NULL, NULL, 'http://localhost:3000/auth/callback', NULL, NULL, false),
	('177b4c17-e63e-4946-8b26-7f22b7d77233', NULL, NULL, NULL, NULL, 'google', '', '', '2026-08-14 00:31:08.038888+00', '2026-08-14 00:31:08.038888+00', 'oauth', NULL, NULL, 'http://localhost:3000/auth/callback', NULL, NULL, false),
	('b155344f-f711-4563-af33-ad0d312490b3', NULL, NULL, NULL, NULL, 'google', '', '', '2026-08-14 00:57:44.507424+00', '2026-08-14 00:57:44.507424+00', 'oauth', NULL, NULL, 'http://localhost:3000/auth/callback', NULL, NULL, false),
	('47bbb9c5-7e7b-45ab-8fb3-c88c8bf5c72b', NULL, NULL, NULL, NULL, 'google', '', '', '2026-08-14 02:59:52.247801+00', '2026-08-14 02:59:52.247801+00', 'oauth', NULL, NULL, 'http://localhost:3000/auth/callback', NULL, NULL, false),
	('6602d1e2-4f97-4da5-bd04-814d7261791f', NULL, NULL, NULL, NULL, 'google', '', '', '2026-08-14 03:00:17.21571+00', '2026-08-14 03:00:17.21571+00', 'oauth', NULL, NULL, 'http://localhost:3000/auth/callback', NULL, NULL, false),
	('352f9b65-dc17-468a-a456-21d4f4eb7dd2', NULL, NULL, NULL, NULL, 'google', '', '', '2026-08-14 03:01:30.431137+00', '2026-08-14 03:01:30.431137+00', 'oauth', NULL, NULL, 'http://localhost:3000/auth/callback', NULL, NULL, false);


--
-- Data for Name: users; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

INSERT INTO "auth"."users" ("instance_id", "id", "aud", "role", "email", "encrypted_password", "email_confirmed_at", "invited_at", "confirmation_token", "confirmation_sent_at", "recovery_token", "recovery_sent_at", "email_change_token_new", "email_change", "email_change_sent_at", "last_sign_in_at", "raw_app_meta_data", "raw_user_meta_data", "is_super_admin", "created_at", "updated_at", "phone", "phone_confirmed_at", "phone_change", "phone_change_token", "phone_change_sent_at", "email_change_token_current", "email_change_confirm_status", "banned_until", "reauthentication_token", "reauthentication_sent_at", "is_sso_user", "deleted_at", "is_anonymous") VALUES
	('00000000-0000-0000-0000-000000000000', '91889163-12b1-4726-b97a-a3cdd9cffd70', 'authenticated', 'authenticated', 'crismancoinbase@gmail.com', '$2a$10$u4OcmtmIV1XzHNS.CaxnFuD9aWszdTHxXK53BgnZ95G5.LRTe7YDe', '2026-08-19 15:40:07.803038+00', NULL, '', NULL, '', NULL, '', '', NULL, '2026-08-19 15:41:35.52641+00', '{"provider": "email", "providers": ["email"]}', '{"email_verified": true}', NULL, '2026-08-19 15:40:07.785746+00', '2026-08-19 15:41:35.534764+00', NULL, NULL, '', '', NULL, '', 0, NULL, '', NULL, false, NULL, false),
	('00000000-0000-0000-0000-000000000000', '09aa157e-955e-484b-a764-b5409207eee1', 'authenticated', 'authenticated', 'crismantlf@gmail.com', '$2a$10$E518mT4drEakvycYV.g/2.T56SFnuqQ0UBVxilEMV3xbIC5LJ5uRa', '2026-08-17 15:38:22.364336+00', NULL, '', NULL, '', NULL, '', '', NULL, '2026-08-19 16:30:14.572647+00', '{"provider": "email", "providers": ["email"]}', '{"email_verified": true}', NULL, '2026-08-17 15:38:22.333227+00', '2026-08-19 16:30:14.578145+00', NULL, NULL, '', '', NULL, '', 0, NULL, '', NULL, false, NULL, false),
	('00000000-0000-0000-0000-000000000000', '29d80632-d7a2-4b10-80e7-2eccbddb1568', 'authenticated', 'authenticated', 'cristhianb397@gmail.com', '$2a$10$yzLrrymZ9FJUmauupZkN4.xlWD9MAzp3t/PF9PdeZIK3BpkXvqJYC', '2026-08-14 13:57:26.608353+00', NULL, '', '2026-08-14 13:57:07.958347+00', '', NULL, '', '', NULL, '2026-08-21 16:20:02.529809+00', '{"provider": "email", "providers": ["email"]}', '{"sub": "29d80632-d7a2-4b10-80e7-2eccbddb1568", "email": "cristhianb397@gmail.com", "cedula": "V-30541863", "nombres": "Cristhian", "telefono": "0412-5994461", "apellidos": "Blanco", "accepted_terms": true, "email_verified": true, "phone_verified": false}', NULL, '2026-08-14 13:57:07.932317+00', '2026-08-21 16:20:02.551611+00', NULL, NULL, '', '', NULL, '', 0, NULL, '', NULL, false, NULL, false),
	('00000000-0000-0000-0000-000000000000', '6001894e-9640-4619-bc56-a506046e8d86', 'authenticated', 'authenticated', 'bermudezpao340@gmail.com', '$2a$10$xc5E7Euz48eyBBuYEAfEh..48GZKviGFCNr4pr68uVxuvBsrLexM2', '2026-08-14 14:17:53.156197+00', NULL, '', '2026-08-14 14:17:13.491699+00', '', NULL, '', '', NULL, '2026-08-14 14:19:46.174798+00', '{"provider": "email", "providers": ["email"]}', '{"sub": "6001894e-9640-4619-bc56-a506046e8d86", "email": "bermudezpao340@gmail.com", "cedula": "V-29533075", "nombres": "Paola", "telefono": "0424-1960818", "apellidos": "Bermudez", "accepted_terms": true, "email_verified": true, "phone_verified": false}', NULL, '2026-08-14 14:17:13.463924+00', '2026-08-14 14:19:46.185962+00', NULL, NULL, '', '', NULL, '', 0, NULL, '', NULL, false, NULL, false),
	('00000000-0000-0000-0000-000000000000', '22e120a2-3cda-4197-bdb3-536806df7ac7', 'authenticated', 'authenticated', 'plus.instala@gmail.com', '$2a$10$z3pFC0/CJLSbSSOTmCAtDOjeLAkL3ZipLJdPsRuyEPTgYfPtrtvq2', '2026-08-14 15:14:46.671435+00', NULL, '', '2026-08-14 15:14:31.565362+00', '', NULL, '', '', NULL, '2026-08-14 15:15:22.441016+00', '{"provider": "email", "providers": ["email"]}', '{"sub": "22e120a2-3cda-4197-bdb3-536806df7ac7", "email": "plus.instala@gmail.com", "cedula": "V-29533078", "nombres": "Paola", "telefono": "0424-1960818", "apellidos": "Bermudez", "accepted_terms": true, "email_verified": true, "phone_verified": false}', NULL, '2026-08-14 15:14:31.522637+00', '2026-08-14 15:15:22.457211+00', NULL, NULL, '', '', NULL, '', 0, NULL, '', NULL, false, NULL, false),
	('00000000-0000-0000-0000-000000000000', '99c6a4d2-d8f0-4c38-a23c-62c0d5c3e5a7', 'authenticated', 'authenticated', 'paolariveratm1985@gmail.com', '$2a$10$vg0HwldDsh4VIDHmLL07FOaaYsMZbh2rK4ov8MSSFi80ihMPwsy06', '2026-08-13 20:07:57.187163+00', NULL, '', NULL, '', NULL, '', '', NULL, '2026-08-14 18:28:24.867669+00', '{"provider": "email", "providers": ["email", "google"]}', '{"iss": "https://accounts.google.com", "sub": "103969074915960738098", "name": "Paola Rivera", "email": "paolariveratm1985@gmail.com", "cedula": "V-29533074", "nombres": "Paola", "picture": "https://lh3.googleusercontent.com/a/ACg8ocIe1l-QR4jMcx7IYkL4x5QPtWls1XeimR4eQe5kErJd8D1J0lM=s96-c", "telefono": "0412-9671838", "apellidos": "Rivera", "full_name": "Paola Rivera", "avatar_url": "https://lh3.googleusercontent.com/a/ACg8ocIe1l-QR4jMcx7IYkL4x5QPtWls1XeimR4eQe5kErJd8D1J0lM=s96-c", "provider_id": "103969074915960738098", "accepted_terms": true, "email_verified": true, "phone_verified": false}', NULL, '2026-08-13 20:07:12.048321+00', '2026-08-17 13:19:06.64607+00', NULL, NULL, '', '', NULL, '', 0, NULL, '', NULL, false, NULL, false);


--
-- Data for Name: identities; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

INSERT INTO "auth"."identities" ("provider_id", "user_id", "identity_data", "provider", "last_sign_in_at", "created_at", "updated_at", "id") VALUES
	('91889163-12b1-4726-b97a-a3cdd9cffd70', '91889163-12b1-4726-b97a-a3cdd9cffd70', '{"sub": "91889163-12b1-4726-b97a-a3cdd9cffd70", "email": "crismancoinbase@gmail.com", "email_verified": false, "phone_verified": false}', 'email', '2026-08-19 15:40:07.798157+00', '2026-08-19 15:40:07.798245+00', '2026-08-19 15:40:07.798245+00', '81267e99-b5fc-43f7-adc5-446065d57abf'),
	('99c6a4d2-d8f0-4c38-a23c-62c0d5c3e5a7', '99c6a4d2-d8f0-4c38-a23c-62c0d5c3e5a7', '{"sub": "99c6a4d2-d8f0-4c38-a23c-62c0d5c3e5a7", "email": "paolariveratm1985@gmail.com", "cedula": "V-29533074", "nombres": "Paola", "telefono": "0412-9671838", "apellidos": "Rivera", "accepted_terms": true, "email_verified": true, "phone_verified": false}', 'email', '2026-08-13 20:07:12.060983+00', '2026-08-13 20:07:12.06106+00', '2026-08-13 20:07:12.06106+00', 'ea3fd5b2-5876-4d2b-8c9d-74af7fd9d5a9'),
	('29d80632-d7a2-4b10-80e7-2eccbddb1568', '29d80632-d7a2-4b10-80e7-2eccbddb1568', '{"sub": "29d80632-d7a2-4b10-80e7-2eccbddb1568", "email": "cristhianb397@gmail.com", "cedula": "V-30541863", "nombres": "Cristhian", "telefono": "0412-5994461", "apellidos": "Blanco", "accepted_terms": true, "email_verified": true, "phone_verified": false}', 'email', '2026-08-14 13:57:07.95048+00', '2026-08-14 13:57:07.950537+00', '2026-08-14 13:57:07.950537+00', 'd107aa37-1d9f-4947-8ee0-1b1daaff5200'),
	('22e120a2-3cda-4197-bdb3-536806df7ac7', '22e120a2-3cda-4197-bdb3-536806df7ac7', '{"sub": "22e120a2-3cda-4197-bdb3-536806df7ac7", "email": "plus.instala@gmail.com", "cedula": "V-29533078", "nombres": "Paola", "telefono": "0424-1960818", "apellidos": "Bermudez", "accepted_terms": true, "email_verified": true, "phone_verified": false}', 'email', '2026-08-14 15:14:31.557123+00', '2026-08-14 15:14:31.55717+00', '2026-08-14 15:14:31.55717+00', '3c2673c9-a72f-497d-86a8-a83c4f26c51e'),
	('6001894e-9640-4619-bc56-a506046e8d86', '6001894e-9640-4619-bc56-a506046e8d86', '{"sub": "6001894e-9640-4619-bc56-a506046e8d86", "email": "bermudezpao340@gmail.com", "cedula": "V-29533075", "nombres": "Paola", "telefono": "0424-1960818", "apellidos": "Bermudez", "accepted_terms": true, "email_verified": true, "phone_verified": false}', 'email', '2026-08-14 14:17:13.484283+00', '2026-08-14 14:17:13.484342+00', '2026-08-14 14:17:13.484342+00', 'a75e6697-ce30-4a66-97f5-8dc55906f3d7'),
	('103969074915960738098', '99c6a4d2-d8f0-4c38-a23c-62c0d5c3e5a7', '{"iss": "https://accounts.google.com", "sub": "103969074915960738098", "name": "Paola Rivera", "email": "paolariveratm1985@gmail.com", "picture": "https://lh3.googleusercontent.com/a/ACg8ocIe1l-QR4jMcx7IYkL4x5QPtWls1XeimR4eQe5kErJd8D1J0lM=s96-c", "full_name": "Paola Rivera", "avatar_url": "https://lh3.googleusercontent.com/a/ACg8ocIe1l-QR4jMcx7IYkL4x5QPtWls1XeimR4eQe5kErJd8D1J0lM=s96-c", "provider_id": "103969074915960738098", "email_verified": true, "phone_verified": false}', 'google', '2026-08-14 00:21:42.605344+00', '2026-08-14 00:21:42.605388+00', '2026-08-14 18:28:24.851081+00', '980f51ac-1512-4f90-b92e-7fc85682e76e'),
	('09aa157e-955e-484b-a764-b5409207eee1', '09aa157e-955e-484b-a764-b5409207eee1', '{"sub": "09aa157e-955e-484b-a764-b5409207eee1", "email": "crismantlf@gmail.com", "email_verified": false, "phone_verified": false}', 'email', '2026-08-17 15:38:22.355166+00', '2026-08-17 15:38:22.355221+00', '2026-08-17 15:38:22.355221+00', 'b86cbba3-4186-48aa-87f0-f7b259a7cb89');


--
-- Data for Name: instances; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--



--
-- Data for Name: oauth_clients; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--



--
-- Data for Name: sessions; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

INSERT INTO "auth"."sessions" ("id", "user_id", "created_at", "updated_at", "factor_id", "aal", "not_after", "refreshed_at", "user_agent", "ip", "tag", "oauth_client_id", "refresh_token_hmac_key", "refresh_token_counter", "scopes") VALUES
	('671b205c-2983-4b86-a8fb-a77f489ae716', '99c6a4d2-d8f0-4c38-a23c-62c0d5c3e5a7', '2026-08-14 15:42:24.525945+00', '2026-08-17 13:19:06.659933+00', NULL, 'aal1', NULL, '2026-08-17 13:19:06.65982', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '190.6.47.217', NULL, NULL, NULL, NULL, NULL),
	('b60abefd-8649-4063-9408-24c338bdb2a5', '29d80632-d7a2-4b10-80e7-2eccbddb1568', '2026-08-21 16:20:02.529909+00', '2026-08-21 16:20:02.529909+00', NULL, 'aal1', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '190.153.2.184', NULL, NULL, NULL, NULL, NULL),
	('82d23871-21b3-4c64-8a56-1ed0ab618e6d', '6001894e-9640-4619-bc56-a506046e8d86', '2026-08-14 14:17:53.161885+00', '2026-08-14 14:17:53.161885+00', NULL, 'aal1', NULL, NULL, 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Mobile Safari/537.36', '190.153.1.45', NULL, NULL, NULL, NULL, NULL),
	('57f48745-5932-4b62-9eb7-14f92b4e378d', '6001894e-9640-4619-bc56-a506046e8d86', '2026-08-14 14:18:20.378221+00', '2026-08-14 14:18:20.378221+00', NULL, 'aal1', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '190.153.1.45', NULL, NULL, NULL, NULL, NULL),
	('e604f647-d569-48b6-b2f7-073c95ad066f', '6001894e-9640-4619-bc56-a506046e8d86', '2026-08-14 14:19:46.175459+00', '2026-08-14 14:19:46.175459+00', NULL, 'aal1', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '190.153.1.45', NULL, NULL, NULL, NULL, NULL),
	('fdb9e321-f794-4ddc-855f-d3ceb4d8e753', '22e120a2-3cda-4197-bdb3-536806df7ac7', '2026-08-14 15:14:46.687707+00', '2026-08-14 15:14:46.687707+00', NULL, 'aal1', NULL, NULL, 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Mobile Safari/537.36', '190.153.1.45', NULL, NULL, NULL, NULL, NULL),
	('fa2bedb8-cb4a-4d1d-ad27-bd35ce46e00e', '22e120a2-3cda-4197-bdb3-536806df7ac7', '2026-08-14 15:15:22.441862+00', '2026-08-14 15:15:22.441862+00', NULL, 'aal1', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '190.153.1.45', NULL, NULL, NULL, NULL, NULL),
	('e56b96ac-3a04-4214-b66d-51f45cf87017', '99c6a4d2-d8f0-4c38-a23c-62c0d5c3e5a7', '2026-08-14 15:41:15.628542+00', '2026-08-14 15:41:15.628542+00', NULL, 'aal1', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '190.153.1.45', NULL, NULL, NULL, NULL, NULL),
	('89c2ce8b-ac3e-49cc-8318-4f07c01446f2', '99c6a4d2-d8f0-4c38-a23c-62c0d5c3e5a7', '2026-08-14 18:28:24.869293+00', '2026-08-14 18:28:24.869293+00', NULL, 'aal1', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '153.51.233.52', NULL, NULL, NULL, NULL, NULL);


--
-- Data for Name: mfa_amr_claims; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

INSERT INTO "auth"."mfa_amr_claims" ("session_id", "created_at", "updated_at", "authentication_method", "id") VALUES
	('b60abefd-8649-4063-9408-24c338bdb2a5', '2026-08-21 16:20:02.555809+00', '2026-08-21 16:20:02.555809+00', 'password', '2828a1a3-14e2-4c67-af74-d3f4362dbd7d'),
	('82d23871-21b3-4c64-8a56-1ed0ab618e6d', '2026-08-14 14:17:53.166481+00', '2026-08-14 14:17:53.166481+00', 'otp', '168a8da6-ece3-4946-8ed0-c52ca390de1c'),
	('57f48745-5932-4b62-9eb7-14f92b4e378d', '2026-08-14 14:18:20.383255+00', '2026-08-14 14:18:20.383255+00', 'password', 'b3520f92-bcc7-4abe-ac62-7c0f684f0423'),
	('e604f647-d569-48b6-b2f7-073c95ad066f', '2026-08-14 14:19:46.187571+00', '2026-08-14 14:19:46.187571+00', 'password', '5d1bb692-aa28-4f77-84d9-97f75030a34c'),
	('fdb9e321-f794-4ddc-855f-d3ceb4d8e753', '2026-08-14 15:14:46.702706+00', '2026-08-14 15:14:46.702706+00', 'otp', '1f0095ca-f9f0-4fa7-9f3f-008c0ed6c60e'),
	('fa2bedb8-cb4a-4d1d-ad27-bd35ce46e00e', '2026-08-14 15:15:22.458976+00', '2026-08-14 15:15:22.458976+00', 'password', 'fb998c00-876b-4640-89eb-576c74dae8cb'),
	('e56b96ac-3a04-4214-b66d-51f45cf87017', '2026-08-14 15:41:15.636121+00', '2026-08-14 15:41:15.636121+00', 'oauth', '08d76333-752e-48da-9d71-43f1babac36c'),
	('671b205c-2983-4b86-a8fb-a77f489ae716', '2026-08-14 15:42:24.529697+00', '2026-08-14 15:42:24.529697+00', 'oauth', 'cf32a38f-7ae4-450b-a395-5c2c692c5ee1'),
	('89c2ce8b-ac3e-49cc-8318-4f07c01446f2', '2026-08-14 18:28:24.897613+00', '2026-08-14 18:28:24.897613+00', 'oauth', 'd5329ff4-0699-4b35-94bb-26b2a95c1c02');


--
-- Data for Name: mfa_factors; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--



--
-- Data for Name: mfa_challenges; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--



--
-- Data for Name: oauth_authorizations; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--



--
-- Data for Name: oauth_client_states; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--



--
-- Data for Name: oauth_consents; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--



--
-- Data for Name: one_time_tokens; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--



--
-- Data for Name: refresh_tokens; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--

INSERT INTO "auth"."refresh_tokens" ("instance_id", "id", "token", "user_id", "revoked", "created_at", "updated_at", "parent", "session_id") VALUES
	('00000000-0000-0000-0000-000000000000', 58, 'uykjf2ltfvi5', '99c6a4d2-d8f0-4c38-a23c-62c0d5c3e5a7', true, '2026-08-14 15:42:24.527483+00', '2026-08-17 13:19:06.636499+00', NULL, '671b205c-2983-4b86-a8fb-a77f489ae716'),
	('00000000-0000-0000-0000-000000000000', 66, 'pv753v53zcow', '99c6a4d2-d8f0-4c38-a23c-62c0d5c3e5a7', false, '2026-08-17 13:19:06.642684+00', '2026-08-17 13:19:06.642684+00', 'uykjf2ltfvi5', '671b205c-2983-4b86-a8fb-a77f489ae716'),
	('00000000-0000-0000-0000-000000000000', 95, 'l7icjajclrgk', '29d80632-d7a2-4b10-80e7-2eccbddb1568', false, '2026-08-21 16:20:02.541979+00', '2026-08-21 16:20:02.541979+00', NULL, 'b60abefd-8649-4063-9408-24c338bdb2a5'),
	('00000000-0000-0000-0000-000000000000', 46, 'x5jmt5qd5cg6', '6001894e-9640-4619-bc56-a506046e8d86', false, '2026-08-14 14:17:53.164063+00', '2026-08-14 14:17:53.164063+00', NULL, '82d23871-21b3-4c64-8a56-1ed0ab618e6d'),
	('00000000-0000-0000-0000-000000000000', 47, 'lki5u2fudbwg', '6001894e-9640-4619-bc56-a506046e8d86', false, '2026-08-14 14:18:20.381134+00', '2026-08-14 14:18:20.381134+00', NULL, '57f48745-5932-4b62-9eb7-14f92b4e378d'),
	('00000000-0000-0000-0000-000000000000', 48, 'mopyesl5upq4', '6001894e-9640-4619-bc56-a506046e8d86', false, '2026-08-14 14:19:46.179016+00', '2026-08-14 14:19:46.179016+00', NULL, 'e604f647-d569-48b6-b2f7-073c95ad066f'),
	('00000000-0000-0000-0000-000000000000', 54, 'doywpj2jgpnl', '22e120a2-3cda-4197-bdb3-536806df7ac7', false, '2026-08-14 15:14:46.696967+00', '2026-08-14 15:14:46.696967+00', NULL, 'fdb9e321-f794-4ddc-855f-d3ceb4d8e753'),
	('00000000-0000-0000-0000-000000000000', 55, 'bnjrvauqme4t', '22e120a2-3cda-4197-bdb3-536806df7ac7', false, '2026-08-14 15:15:22.454035+00', '2026-08-14 15:15:22.454035+00', NULL, 'fa2bedb8-cb4a-4d1d-ad27-bd35ce46e00e'),
	('00000000-0000-0000-0000-000000000000', 57, 'kshfgkfbjfy7', '99c6a4d2-d8f0-4c38-a23c-62c0d5c3e5a7', false, '2026-08-14 15:41:15.630041+00', '2026-08-14 15:41:15.630041+00', NULL, 'e56b96ac-3a04-4214-b66d-51f45cf87017'),
	('00000000-0000-0000-0000-000000000000', 59, 'jm673tdyikk4', '99c6a4d2-d8f0-4c38-a23c-62c0d5c3e5a7', false, '2026-08-14 18:28:24.884863+00', '2026-08-14 18:28:24.884863+00', NULL, '89c2ce8b-ac3e-49cc-8318-4f07c01446f2');


--
-- Data for Name: sso_providers; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--



--
-- Data for Name: saml_providers; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--



--
-- Data for Name: saml_relay_states; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--



--
-- Data for Name: sso_domains; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--



--
-- Data for Name: webauthn_challenges; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--



--
-- Data for Name: webauthn_credentials; Type: TABLE DATA; Schema: auth; Owner: supabase_auth_admin
--



--
-- Name: refresh_tokens_id_seq; Type: SEQUENCE SET; Schema: auth; Owner: supabase_auth_admin
--

SELECT pg_catalog.setval('"auth"."refresh_tokens_id_seq"', 95, true);


--
-- PostgreSQL database dump complete
--

-- \unrestrict lBfsh4dBl1csE8ATse3kFmgCoAwiTKR9KgueX1ZGGezsTWuyzwMlGpaTeY1xlu9

RESET ALL;
