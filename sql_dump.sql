--
-- PostgreSQL database dump
--

-- Dumped from database version 16.3 (Debian 16.3-1.pgdg120+1)
-- Dumped by pg_dump version 16.3 (Debian 16.3-1.pgdg120+1)

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

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: blockchains; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.blockchains (
    blockchain_id integer NOT NULL,
    name character varying(50) NOT NULL
);


ALTER TABLE public.blockchains OWNER TO docker;

--
-- Name: blockchains_blockchain_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.blockchains_blockchain_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.blockchains_blockchain_id_seq OWNER TO docker;

--
-- Name: blockchains_blockchain_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.blockchains_blockchain_id_seq OWNED BY public.blockchains.blockchain_id;


--
-- Name: options; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.options (
    option_id integer NOT NULL,
    question_id integer NOT NULL,
    text text NOT NULL,
    is_correct boolean DEFAULT false NOT NULL
);


ALTER TABLE public.options OWNER TO docker;

--
-- Name: options_option_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.options_option_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.options_option_id_seq OWNER TO docker;

--
-- Name: options_option_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.options_option_id_seq OWNED BY public.options.option_id;


--
-- Name: pictures; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.pictures (
    picture_id integer NOT NULL,
    picture_url text NOT NULL
);


ALTER TABLE public.pictures OWNER TO docker;

--
-- Name: pictures_picture_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.pictures_picture_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.pictures_picture_id_seq OWNER TO docker;

--
-- Name: pictures_picture_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.pictures_picture_id_seq OWNED BY public.pictures.picture_id;


--
-- Name: quest_progress; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.quest_progress (
    wallet_id integer NOT NULL,
    quest_id integer NOT NULL,
    completion_date date,
    score integer DEFAULT 0 NOT NULL,
    next_question_id integer NOT NULL,
    state integer NOT NULL
);


ALTER TABLE public.quest_progress OWNER TO docker;

--
-- Name: questions; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.questions (
    question_id integer NOT NULL,
    quest_id integer NOT NULL,
    text text NOT NULL,
    type character varying(50) NOT NULL,
    points integer NOT NULL
);


ALTER TABLE public.questions OWNER TO docker;

--
-- Name: questions_question_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.questions_question_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.questions_question_id_seq OWNER TO docker;

--
-- Name: questions_question_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.questions_question_id_seq OWNED BY public.questions.question_id;


--
-- Name: quests; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.quests (
    quest_id integer NOT NULL,
    creator_id integer NOT NULL,
    picture_id integer NOT NULL,
    blockchain_id integer NOT NULL,
    token_id integer NOT NULL,
    title character varying(90) NOT NULL,
    description text NOT NULL,
    expiry_date date NOT NULL,
    participants_limit integer NOT NULL,
    pool_amount numeric(10,2) DEFAULT 0 NOT NULL,
    required_minutes integer DEFAULT 5 NOT NULL,
    approved boolean DEFAULT false NOT NULL,
    payout_date date NOT NULL
);


ALTER TABLE public.quests OWNER TO docker;

--
-- Name: quests_quest_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.quests_quest_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.quests_quest_id_seq OWNER TO docker;

--
-- Name: quests_quest_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.quests_quest_id_seq OWNED BY public.quests.quest_id;


--
-- Name: ratings; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.ratings (
    user_id integer NOT NULL,
    quest_id integer NOT NULL,
    rating integer NOT NULL
);


ALTER TABLE public.ratings OWNER TO docker;

--
-- Name: roles; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.roles (
    role_id integer NOT NULL,
    name character varying(20) DEFAULT 'normal'::character varying NOT NULL
);


ALTER TABLE public.roles OWNER TO docker;

--
-- Name: roles_role_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.roles_role_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.roles_role_id_seq OWNER TO docker;

--
-- Name: roles_role_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.roles_role_id_seq OWNED BY public.roles.role_id;


--
-- Name: similarities; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.similarities (
    user_id_1 integer NOT NULL,
    user_id_2 integer NOT NULL,
    similarity_score double precision NOT NULL
);


ALTER TABLE public.similarities OWNER TO docker;

--
-- Name: tokens; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.tokens (
    token_id integer NOT NULL,
    name character varying(20) NOT NULL
);


ALTER TABLE public.tokens OWNER TO docker;

--
-- Name: tokens_token_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.tokens_token_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tokens_token_id_seq OWNER TO docker;

--
-- Name: tokens_token_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.tokens_token_id_seq OWNED BY public.tokens.token_id;


--
-- Name: user_responses; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.user_responses (
    user_id integer NOT NULL,
    option_id integer NOT NULL
);


ALTER TABLE public.user_responses OWNER TO docker;

--
-- Name: users; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.users (
    user_id integer NOT NULL,
    role_id integer NOT NULL,
    avatar_id integer DEFAULT 1 NOT NULL,
    email character varying(255) NOT NULL,
    username character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    join_date date NOT NULL
);


ALTER TABLE public.users OWNER TO docker;

--
-- Name: users_user_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.users_user_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_user_id_seq OWNER TO docker;

--
-- Name: users_user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.users_user_id_seq OWNED BY public.users.user_id;


--
-- Name: wallets; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.wallets (
    wallet_id integer NOT NULL,
    blockchain_id integer NOT NULL,
    user_id integer NOT NULL,
    address character varying(255) NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.wallets OWNER TO docker;

--
-- Name: wallets_wallet_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.wallets_wallet_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.wallets_wallet_id_seq OWNER TO docker;

--
-- Name: wallets_wallet_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.wallets_wallet_id_seq OWNED BY public.wallets.wallet_id;


--
-- Name: blockchains blockchain_id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.blockchains ALTER COLUMN blockchain_id SET DEFAULT nextval('public.blockchains_blockchain_id_seq'::regclass);


--
-- Name: options option_id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.options ALTER COLUMN option_id SET DEFAULT nextval('public.options_option_id_seq'::regclass);


--
-- Name: pictures picture_id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.pictures ALTER COLUMN picture_id SET DEFAULT nextval('public.pictures_picture_id_seq'::regclass);


--
-- Name: questions question_id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.questions ALTER COLUMN question_id SET DEFAULT nextval('public.questions_question_id_seq'::regclass);


--
-- Name: quests quest_id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.quests ALTER COLUMN quest_id SET DEFAULT nextval('public.quests_quest_id_seq'::regclass);


--
-- Name: roles role_id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.roles ALTER COLUMN role_id SET DEFAULT nextval('public.roles_role_id_seq'::regclass);


--
-- Name: tokens token_id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.tokens ALTER COLUMN token_id SET DEFAULT nextval('public.tokens_token_id_seq'::regclass);


--
-- Name: users user_id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.users ALTER COLUMN user_id SET DEFAULT nextval('public.users_user_id_seq'::regclass);


--
-- Name: wallets wallet_id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.wallets ALTER COLUMN wallet_id SET DEFAULT nextval('public.wallets_wallet_id_seq'::regclass);


--
-- Data for Name: blockchains; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.blockchains (blockchain_id, name) FROM stdin;
1	Solana
2	Bitcoin
3	Etherum
4	Cosmos
\.


--
-- Data for Name: options; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.options (option_id, question_id, text, is_correct) FROM stdin;
1	1	cryptocurrency	f
2	1	distributed ledger technology	t
3	1	centralized database	f
4	1	internet protocol	f
5	2	Decentralization	t
6	2	Immutability	f
7	2	High transaction fees	f
8	2	Transparency	t
9	3	JavaScript	f
10	3	Python	f
11	3	Solidity	t
12	3	C++	f
13	4	Automated payments	f
14	4	Digital voting systems	t
15	4	Centralized banking	f
16	4	Supply chain management	t
17	6	decentralized	t
18	6	immutable	f
19	6	centrally controlled	f
20	6	cryptographic algorithms	t
21	7	Distributed Finance	f
22	7	Decentralized Finance	t
23	7	 Digital Finance	f
24	7	 Dynamic Finance	f
25	8	Lending platforms	t
26	8	Decentralized exchanges	t
27	8	Traditional stock exchanges	f
28	8	Prediction markets	f
29	9	Proof of Stake	t
30	9	Proof of Work	t
31	9	Consensus algorithms	f
32	9	Centralized validation	f
33	10	Its fungibility	f
34	10	Its digital certificate of authenticity	t
35	10	Its use of physical assets	f
36	10	Its replication capability	f
\.


--
-- Data for Name: pictures; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.pictures (picture_id, picture_url) FROM stdin;
1	https://picsum.photos/300/200
2	666b6d7ad07e5.png
3	666b70f96a020.png
4	666b711570a28.png
5	666b7161c288f.png
6	666b7301e8511.png
7	666b73fc89693.jpg
8	666b74a67f7f9.png
9	666b750113d26.png
\.


--
-- Data for Name: quest_progress; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.quest_progress (wallet_id, quest_id, completion_date, score, next_question_id, state) FROM stdin;
\.


--
-- Data for Name: questions; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.questions (question_id, quest_id, text, type, points) FROM stdin;
1	1	 What is a blockchain?	single_choice	15
2	1	 Which of the following are characteristics of blockchain technology? 	multiple_choice	15
3	2	 What language is primarily used to write Ethereum smart contracts?	single_choice	17
4	2	 Which of the following are use cases for smart contracts? 	multiple_choice	33
5	2	 Blockchain is a decentralized, distributed ledger technology that allows data to be stored globally on thousands of servers. This technology ensures that the information is transparent and secure, making it nearly impossible for a single entity to control or manipulate the data. Each block in a blockchain contains a set of transactions, and once a block is added to the chain, it is immutable, meaning it cannot be altered retroactively without altering all subsequent blocks, which requires conse	read_text	22
6	2	Why is blockchain considered more secure than traditional centralized databases?	multiple_choice	44
7	3	 What does DeFi stand for?	single_choice	11
8	3	 Which of the following are common DeFi applications? 	multiple_choice	55
9	4	 Which mechanism ensures the integrity of transactions in a blockchain?	multiple_choice	33
10	5	 What makes an NFT unique?	single_choice	11
\.


--
-- Data for Name: quests; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.quests (quest_id, creator_id, picture_id, blockchain_id, token_id, title, description, expiry_date, participants_limit, pool_amount, required_minutes, approved, payout_date) FROM stdin;
2	1	6	2	3	Ethereum Smart Contracts	Dive into Ethereum and learn how to create and deploy smart contracts.	2024-06-20	200	1300.00	20	f	2024-06-22
3	1	7	3	4	DeFi (Decentralized Finance)	Understand the basics of DeFi and how it is transforming traditional finance.	2024-06-19	80	600.00	45	f	2024-06-22
5	1	9	2	5	NFT (Non-Fungible Tokens) Basics	Explore the world of NFTs and their impact on digital ownership.	2024-06-25	249	321.00	20	f	2024-06-26
1	1	5	1	2	Blockchain Basics	Learn the basics of blockchain technology and understand its core principles.	2024-06-18	120	500.00	15	t	2024-06-20
4	1	8	4	3	Blockchain Security	Learn about the security features of blockchain and how to protect your digital assets.	2024-06-26	120	133.00	24	t	2024-06-28
\.


--
-- Data for Name: ratings; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.ratings (user_id, quest_id, rating) FROM stdin;
1	4	5
1	5	1
2	2	5
2	3	1
2	4	2
2	5	3
3	2	3
3	3	4
3	4	1
3	5	5
4	1	2
4	2	4
4	3	1
4	4	5
4	5	3
5	1	4
5	2	3
5	3	2
5	4	5
5	5	1
6	1	5
6	2	1
6	3	4
6	4	2
6	5	3
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.roles (role_id, name) FROM stdin;
1	normal
2	admin
3	creator
\.


--
-- Data for Name: similarities; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.similarities (user_id_1, user_id_2, similarity_score) FROM stdin;
1	1	1
1	2	0.70710678118655
1	3	0.38461538461538
1	4	0.94174191159484
1	5	1
1	6	0.70710678118655
2	1	0.70710678118655
2	2	1
2	3	0.80720735279557
2	4	0.89689705866175
2	5	0.76923076923077
2	6	0.64317588082515
3	1	0.38461538461538
3	2	0.80720735279557
3	3	1
3	4	0.70588235294118
3	5	0.60540551459668
3	6	0.92035798661684
4	1	0.94174191159484
4	2	0.89689705866175
4	3	0.70588235294118
4	4	1
4	5	0.90909090909091
4	6	0.67272727272727
5	1	1
5	2	0.76923076923077
5	3	0.60540551459668
5	4	0.90909090909091
5	5	1
5	6	0.8
6	1	0.70710678118655
6	2	0.64317588082515
6	3	0.92035798661684
6	4	0.67272727272727
6	5	0.8
6	6	1
\.


--
-- Data for Name: tokens; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.tokens (token_id, name) FROM stdin;
1	guest
2	USDC
3	SOL
4	GEN
5	USDT
\.


--
-- Data for Name: user_responses; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.user_responses (user_id, option_id) FROM stdin;
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.users (user_id, role_id, avatar_id, email, username, password, join_date) FROM stdin;
3	1	1	user@example.com	user	$2y$10$u6d7PHyEF8khtbxHX05thOHq4seWq5rlIb5eO/49QnOEGhRJhTNs2	2024-06-13
2	2	1	admin@example.com	admin	$2y$10$PEMfmKMPg2NzY25ZaQkDxu/bRXvLkfdVL3n3OUcgv.9DdDaEmArQC	2024-06-13
1	3	1	creator@example.com	creator	$2y$10$FW75TfuS93WDF.BzCXPR2.uHwC9AWPmbn2ykrsxn0ZtULNVsylhvm	2024-06-13
4	1	1	user1@example.com	user1	$2y$10$pGMcfMndn4J2ARRwPPF9Bemky2/9ksE1XdWFqrhLfFL/V91gz/DtK	2024-06-13
5	1	1	user2@example.com	user2	$2y$10$dMoJW/jo1K86upX0o104kOarWkOGLKnYjonT2S4YvBKuWDJ8NzIny	2024-06-13
6	1	1	user3@example.com	user3	$2y$10$Yp3NP77D3IoqfirDi/7AVeeTUrqzOvv4niVW..z8o5VZGZVbimhFi	2024-06-13
\.


--
-- Data for Name: wallets; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.wallets (wallet_id, blockchain_id, user_id, address, created_at, updated_at) FROM stdin;
\.


--
-- Name: blockchains_blockchain_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.blockchains_blockchain_id_seq', 4, true);


--
-- Name: options_option_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.options_option_id_seq', 36, true);


--
-- Name: pictures_picture_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.pictures_picture_id_seq', 9, true);


--
-- Name: questions_question_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.questions_question_id_seq', 10, true);


--
-- Name: quests_quest_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.quests_quest_id_seq', 5, true);


--
-- Name: roles_role_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.roles_role_id_seq', 3, true);


--
-- Name: tokens_token_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.tokens_token_id_seq', 5, true);


--
-- Name: users_user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.users_user_id_seq', 6, true);


--
-- Name: wallets_wallet_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.wallets_wallet_id_seq', 1, false);


--
-- Name: blockchains blockchain_name_unique; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.blockchains
    ADD CONSTRAINT blockchain_name_unique UNIQUE (name);


--
-- Name: blockchains blockchains_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.blockchains
    ADD CONSTRAINT blockchains_pkey PRIMARY KEY (blockchain_id);


--
-- Name: options options_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.options
    ADD CONSTRAINT options_pkey PRIMARY KEY (option_id);


--
-- Name: quests picture_id_unique; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.quests
    ADD CONSTRAINT picture_id_unique UNIQUE (picture_id);


--
-- Name: pictures picture_url_unique; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.pictures
    ADD CONSTRAINT picture_url_unique UNIQUE (picture_url);


--
-- Name: pictures pictures_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.pictures
    ADD CONSTRAINT pictures_pkey PRIMARY KEY (picture_id);


--
-- Name: quest_progress quest_progress_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.quest_progress
    ADD CONSTRAINT quest_progress_pkey PRIMARY KEY (quest_id, wallet_id);


--
-- Name: questions questions_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.questions
    ADD CONSTRAINT questions_pkey PRIMARY KEY (question_id);


--
-- Name: quests quests_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.quests
    ADD CONSTRAINT quests_pkey PRIMARY KEY (quest_id);


--
-- Name: ratings rating_pk; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.ratings
    ADD CONSTRAINT rating_pk PRIMARY KEY (user_id, quest_id);


--
-- Name: roles role_unique; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT role_unique UNIQUE (name);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (role_id);


--
-- Name: similarities similarities_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.similarities
    ADD CONSTRAINT similarities_pkey PRIMARY KEY (user_id_1, user_id_2);


--
-- Name: tokens token_unique; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.tokens
    ADD CONSTRAINT token_unique UNIQUE (name);


--
-- Name: tokens tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.tokens
    ADD CONSTRAINT tokens_pkey PRIMARY KEY (token_id);


--
-- Name: wallets unique_address; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.wallets
    ADD CONSTRAINT unique_address UNIQUE (address);


--
-- Name: user_responses user_responses_pk; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.user_responses
    ADD CONSTRAINT user_responses_pk PRIMARY KEY (user_id, option_id);


--
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (user_id);


--
-- Name: users users_username_key; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_username_key UNIQUE (username);


--
-- Name: wallets wallets_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.wallets
    ADD CONSTRAINT wallets_pkey PRIMARY KEY (wallet_id);


--
-- Name: quests fk_blockchain_id; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.quests
    ADD CONSTRAINT fk_blockchain_id FOREIGN KEY (blockchain_id) REFERENCES public.blockchains(blockchain_id) NOT VALID;


--
-- Name: wallets fk_blockchain_id; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.wallets
    ADD CONSTRAINT fk_blockchain_id FOREIGN KEY (blockchain_id) REFERENCES public.blockchains(blockchain_id) NOT VALID;


--
-- Name: quests fk_creator_user; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.quests
    ADD CONSTRAINT fk_creator_user FOREIGN KEY (creator_id) REFERENCES public.users(user_id);


--
-- Name: quests fk_picture_id; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.quests
    ADD CONSTRAINT fk_picture_id FOREIGN KEY (picture_id) REFERENCES public.pictures(picture_id) NOT VALID;


--
-- Name: users fk_picture_id; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT fk_picture_id FOREIGN KEY (avatar_id) REFERENCES public.pictures(picture_id) NOT VALID;


--
-- Name: quest_progress fk_quest_id; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.quest_progress
    ADD CONSTRAINT fk_quest_id FOREIGN KEY (quest_id) REFERENCES public.quests(quest_id) ON DELETE CASCADE;


--
-- Name: questions fk_quest_id; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.questions
    ADD CONSTRAINT fk_quest_id FOREIGN KEY (quest_id) REFERENCES public.quests(quest_id) NOT VALID;


--
-- Name: ratings fk_quest_id; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.ratings
    ADD CONSTRAINT fk_quest_id FOREIGN KEY (quest_id) REFERENCES public.quests(quest_id) NOT VALID;


--
-- Name: users fk_role_id; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT fk_role_id FOREIGN KEY (role_id) REFERENCES public.roles(role_id) NOT VALID;


--
-- Name: quests fk_token_id; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.quests
    ADD CONSTRAINT fk_token_id FOREIGN KEY (token_id) REFERENCES public.tokens(token_id) NOT VALID;


--
-- Name: ratings fk_user_id; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.ratings
    ADD CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES public.users(user_id) NOT VALID;


--
-- Name: wallets fk_user_id; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.wallets
    ADD CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES public.users(user_id) NOT VALID;


--
-- Name: similarities fk_user_id_1; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.similarities
    ADD CONSTRAINT fk_user_id_1 FOREIGN KEY (user_id_1) REFERENCES public.users(user_id) NOT VALID;


--
-- Name: similarities fk_user_id_2; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.similarities
    ADD CONSTRAINT fk_user_id_2 FOREIGN KEY (user_id_2) REFERENCES public.users(user_id) NOT VALID;


--
-- Name: quest_progress fk_wallet_id; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.quest_progress
    ADD CONSTRAINT fk_wallet_id FOREIGN KEY (wallet_id) REFERENCES public.wallets(wallet_id) ON DELETE SET NULL;


--
-- Name: options options_questionid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.options
    ADD CONSTRAINT options_questionid_fkey FOREIGN KEY (question_id) REFERENCES public.questions(question_id) ON DELETE CASCADE;


--
-- Name: user_responses user_responses_option_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.user_responses
    ADD CONSTRAINT user_responses_option_id_fk FOREIGN KEY (option_id) REFERENCES public.options(option_id) ON DELETE CASCADE;


--
-- Name: user_responses user_responses_user_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.user_responses
    ADD CONSTRAINT user_responses_user_id_fk FOREIGN KEY (user_id) REFERENCES public.users(user_id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

