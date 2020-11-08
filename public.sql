/*
 Navicat Premium Data Transfer

 Source Server         : 123
 Source Server Type    : PostgreSQL
 Source Server Version : 120003
 Source Host           : localhost:5432
 Source Catalog        : postgres
 Source Schema         : public

 Target Server Type    : PostgreSQL
 Target Server Version : 120003
 File Encoding         : 65001

 Date: 30/05/2020 22:06:37
*/


-- ----------------------------
-- Sequence structure for account_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."account_id_seq";
CREATE SEQUENCE "public"."account_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for accounts_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."accounts_id_seq";
CREATE SEQUENCE "public"."accounts_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for ban_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."ban_seq";
CREATE SEQUENCE "public"."ban_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for channels_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."channels_id_seq";
CREATE SEQUENCE "public"."channels_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for check_event_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."check_event_seq";
CREATE SEQUENCE "public"."check_event_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for clan_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."clan_seq";
CREATE SEQUENCE "public"."clan_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for clans_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."clans_id_seq";
CREATE SEQUENCE "public"."clans_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for contas_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."contas_seq";
CREATE SEQUENCE "public"."contas_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for gameservers_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."gameservers_id_seq";
CREATE SEQUENCE "public"."gameservers_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for gift_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."gift_id_seq";
CREATE SEQUENCE "public"."gift_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for ipsystem_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."ipsystem_id_seq";
CREATE SEQUENCE "public"."ipsystem_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for ipsystem_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."ipsystem_seq";
CREATE SEQUENCE "public"."ipsystem_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for items_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."items_id_seq";
CREATE SEQUENCE "public"."items_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for jogador_amigo_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."jogador_amigo_seq";
CREATE SEQUENCE "public"."jogador_amigo_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for jogador_inventario_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."jogador_inventario_seq";
CREATE SEQUENCE "public"."jogador_inventario_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for jogador_mensagem_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."jogador_mensagem_seq";
CREATE SEQUENCE "public"."jogador_mensagem_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for loja_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."loja_seq";
CREATE SEQUENCE "public"."loja_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for message_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."message_id_seq";
CREATE SEQUENCE "public"."message_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for player_eqipment_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."player_eqipment_id_seq";
CREATE SEQUENCE "public"."player_eqipment_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for player_friends_player_account_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."player_friends_player_account_id_seq";
CREATE SEQUENCE "public"."player_friends_player_account_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for player_mails_player_account_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."player_mails_player_account_id_seq";
CREATE SEQUENCE "public"."player_mails_player_account_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for players_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."players_id_seq";
CREATE SEQUENCE "public"."players_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for storage_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."storage_seq";
CREATE SEQUENCE "public"."storage_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for templates_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."templates_id_seq";
CREATE SEQUENCE "public"."templates_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Table structure for accounts
-- ----------------------------
DROP TABLE IF EXISTS "public"."accounts";
CREATE TABLE "public"."accounts" (
  "login" varchar COLLATE "pg_catalog"."default" NOT NULL DEFAULT ''::character varying,
  "password" varchar COLLATE "pg_catalog"."default" NOT NULL DEFAULT ''::character varying,
  "player_id" int8 NOT NULL DEFAULT nextval('account_id_seq'::regclass),
  "player_name" varchar COLLATE "pg_catalog"."default" NOT NULL DEFAULT ''::character varying,
  "name_color" int4 NOT NULL DEFAULT 0,
  "clan_id" int4 NOT NULL DEFAULT 0,
  "rank" int4 NOT NULL DEFAULT 0,
  "gp" int4 NOT NULL DEFAULT 60000,
  "exp" int4 NOT NULL DEFAULT 0,
  "pc_cafe" int4 NOT NULL DEFAULT 0,
  "fights" int4 NOT NULL DEFAULT 0,
  "fights_win" int4 NOT NULL DEFAULT 0,
  "fights_lost" int4 NOT NULL DEFAULT 0,
  "kills_count" int4 NOT NULL DEFAULT 0,
  "deaths_count" int4 NOT NULL DEFAULT 0,
  "headshots_count" int4 NOT NULL DEFAULT 0,
  "escapes" int4 NOT NULL DEFAULT 0,
  "access_level" int4 NOT NULL DEFAULT 0,
  "lastip" varchar(32) COLLATE "pg_catalog"."default" NOT NULL DEFAULT 0,
  "email" varchar COLLATE "pg_catalog"."default" NOT NULL DEFAULT ''::character varying,
  "last_rankup_date" int8 NOT NULL DEFAULT 1010000,
  "money" int4 NOT NULL DEFAULT 25000,
  "online" bool NOT NULL DEFAULT false,
  "weapon_primary" int4 NOT NULL DEFAULT 0,
  "weapon_secondary" int4 NOT NULL DEFAULT 601002003,
  "weapon_melee" int4 NOT NULL DEFAULT 702001001,
  "weapon_thrown_normal" int4 NOT NULL DEFAULT 803007001,
  "weapon_thrown_special" int4 NOT NULL DEFAULT 904007002,
  "char_red" int4 NOT NULL DEFAULT 1001001005,
  "char_blue" int4 NOT NULL DEFAULT 1001002006,
  "char_helmet" int4 NOT NULL DEFAULT 1102003001,
  "char_dino" int4 NOT NULL DEFAULT 1006003041,
  "char_beret" int4 NOT NULL DEFAULT 0,
  "brooch" int4 NOT NULL DEFAULT 10,
  "insignia" int4 NOT NULL DEFAULT 124,
  "medal" int4 NOT NULL DEFAULT 403,
  "blue_order" int4 NOT NULL DEFAULT 186,
  "mission_id1" int4 NOT NULL DEFAULT 1,
  "clanaccess" int4 NOT NULL DEFAULT 0,
  "clandate" int4 NOT NULL DEFAULT 0,
  "effects" int8 NOT NULL DEFAULT 0,
  "fights_draw" int4 NOT NULL DEFAULT 0,
  "mission_id2" int4 NOT NULL DEFAULT 0,
  "mission_id3" int4 NOT NULL DEFAULT 0,
  "totalkills_count" int4 NOT NULL DEFAULT 0,
  "totalfights_count" int4 NOT NULL DEFAULT 0,
  "status" int8 NOT NULL DEFAULT '4294967295'::bigint,
  "last_login" int8 NOT NULL DEFAULT 0,
  "clan_game_pt" int4 NOT NULL DEFAULT 0,
  "clan_wins_pt" int4 NOT NULL DEFAULT 0,
  "last_mac" macaddr NOT NULL DEFAULT '00:00:00:00:00:00'::macaddr,
  "ban_obj_id" int8 NOT NULL DEFAULT 0,
  "token" varchar COLLATE "pg_catalog"."default",
  "timegetcash" date,
  "data_nasc" date,
  "cad_ip" varchar(32) COLLATE "pg_catalog"."default"
)
;
-- ----------------------------
-- Table structure for accounts_rank
-- ----------------------------
DROP TABLE IF EXISTS "public"."accounts_rank";
CREATE TABLE "public"."accounts_rank" (
  "login" varchar COLLATE "pg_catalog"."default" NOT NULL DEFAULT ''::character varying,
  "password" varchar COLLATE "pg_catalog"."default" NOT NULL DEFAULT ''::character varying,
  "player_id" int8 NOT NULL DEFAULT nextval('account_id_seq'::regclass),
  "player_name" varchar COLLATE "pg_catalog"."default" NOT NULL DEFAULT ''::character varying,
  "name_color" int4 NOT NULL DEFAULT 0,
  "clan_id" int4 NOT NULL DEFAULT 0,
  "rank" int4 NOT NULL DEFAULT 0,
  "gp" int4 NOT NULL DEFAULT 60000,
  "exp" int4 NOT NULL DEFAULT 0,
  "pc_cafe" int4 NOT NULL DEFAULT 0,
  "fights" int4 NOT NULL DEFAULT 0,
  "fights_win" int4 NOT NULL DEFAULT 0,
  "fights_lost" int4 NOT NULL DEFAULT 0,
  "kills_count" int4 NOT NULL DEFAULT 0,
  "deaths_count" int4 NOT NULL DEFAULT 0,
  "headshots_count" int4 NOT NULL DEFAULT 0,
  "escapes" int4 NOT NULL DEFAULT 0,
  "access_level" int4 NOT NULL DEFAULT 0,
  "lastip" varchar(32) COLLATE "pg_catalog"."default" NOT NULL DEFAULT 0,
  "email" varchar COLLATE "pg_catalog"."default" NOT NULL DEFAULT ''::character varying,
  "last_rankup_date" int8 NOT NULL DEFAULT 1010000,
  "money" int4 NOT NULL DEFAULT 25000,
  "online" bool NOT NULL DEFAULT false,
  "weapon_primary" int4 NOT NULL DEFAULT 0,
  "weapon_secondary" int4 NOT NULL DEFAULT 601002003,
  "weapon_melee" int4 NOT NULL DEFAULT 702001001,
  "weapon_thrown_normal" int4 NOT NULL DEFAULT 803007001,
  "weapon_thrown_special" int4 NOT NULL DEFAULT 904007002,
  "char_red" int4 NOT NULL DEFAULT 1001001005,
  "char_blue" int4 NOT NULL DEFAULT 1001002006,
  "char_helmet" int4 NOT NULL DEFAULT 1102003001,
  "char_dino" int4 NOT NULL DEFAULT 1006003041,
  "char_beret" int4 NOT NULL DEFAULT 0,
  "brooch" int4 NOT NULL DEFAULT 10,
  "insignia" int4 NOT NULL DEFAULT 124,
  "medal" int4 NOT NULL DEFAULT 403,
  "blue_order" int4 NOT NULL DEFAULT 186,
  "mission_id1" int4 NOT NULL DEFAULT 1,
  "clanaccess" int4 NOT NULL DEFAULT 0,
  "clandate" int4 NOT NULL DEFAULT 0,
  "effects" int8 NOT NULL DEFAULT 0,
  "fights_draw" int4 NOT NULL DEFAULT 0,
  "mission_id2" int4 NOT NULL DEFAULT 0,
  "mission_id3" int4 NOT NULL DEFAULT 0,
  "totalkills_count" int4 NOT NULL DEFAULT 0,
  "totalfights_count" int4 NOT NULL DEFAULT 0,
  "status" int8 NOT NULL DEFAULT '4294967295'::bigint,
  "last_login" int8 NOT NULL DEFAULT 0,
  "clan_game_pt" int4 NOT NULL DEFAULT 0,
  "clan_wins_pt" int4 NOT NULL DEFAULT 0,
  "last_mac" macaddr NOT NULL DEFAULT '00:00:00:00:00:00'::macaddr,
  "ban_obj_id" int8 NOT NULL DEFAULT 0
)
;

-- ----------------------------
-- Records of accounts_rank
-- ----------------------------

-- ----------------------------
-- Table structure for ban_history
-- ----------------------------
DROP TABLE IF EXISTS "public"."ban_history";
CREATE TABLE "public"."ban_history" (
  "object_id" int8 NOT NULL DEFAULT nextval('ban_seq'::regclass),
  "provider_id" int8 NOT NULL DEFAULT 0,
  "type" varchar(255) COLLATE "pg_catalog"."default" NOT NULL DEFAULT ''::character varying,
  "value" varchar(255) COLLATE "pg_catalog"."default" NOT NULL DEFAULT ''::character varying,
  "reason" varchar(255) COLLATE "pg_catalog"."default" NOT NULL DEFAULT ''::character varying,
  "start_date" timestamp(6) NOT NULL DEFAULT '2000-01-01 00:00:00'::timestamp without time zone,
  "end_date" timestamp(6) NOT NULL DEFAULT '2000-01-01 00:00:00'::timestamp without time zone
)
;

-- ----------------------------
-- Records of ban_history
-- ----------------------------

-- ----------------------------
-- Table structure for clan_data
-- ----------------------------
DROP TABLE IF EXISTS "public"."clan_data";
CREATE TABLE "public"."clan_data" (
  "clan_id" int4 NOT NULL DEFAULT 0,
  "clan_rank" int4 NOT NULL DEFAULT 0,
  "clan_name" varchar COLLATE "pg_catalog"."default" DEFAULT ''::character varying,
  "owner_id" int8 NOT NULL DEFAULT 0,
  "logo" int8 NOT NULL DEFAULT 0,
  "color" int4 NOT NULL DEFAULT 0,
  "clan_info" varchar COLLATE "pg_catalog"."default" DEFAULT ''::character varying,
  "clan_news" varchar COLLATE "pg_catalog"."default" DEFAULT ''::character varying,
  "create_date" varchar COLLATE "pg_catalog"."default" NOT NULL DEFAULT ''::character varying,
  "autoridade" int4 NOT NULL DEFAULT 0,
  "limite_rank" int4 NOT NULL DEFAULT 0,
  "limite_idade" int4 NOT NULL DEFAULT 0,
  "limite_idade2" int4 NOT NULL DEFAULT 0,
  "partidas" int4 NOT NULL DEFAULT 0,
  "vitorias" int4 NOT NULL DEFAULT 0,
  "derrotas" int4 NOT NULL DEFAULT 0,
  "pontos" int4 NOT NULL DEFAULT 0,
  "max_players" int4 NOT NULL DEFAULT 50,
  "clan_exp" int4 NOT NULL DEFAULT 0
)
;

-- ----------------------------
-- Records of clan_data
-- ----------------------------

-- ----------------------------
-- Table structure for clan_invites
-- ----------------------------
DROP TABLE IF EXISTS "public"."clan_invites";
CREATE TABLE "public"."clan_invites" (
  "clan_id" int4 NOT NULL DEFAULT 0,
  "player_id" int8 NOT NULL DEFAULT 0,
  "dateinvite" int4 NOT NULL DEFAULT 0,
  "text" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Records of clan_invites
-- ----------------------------

-- ----------------------------
-- Table structure for configs
-- ----------------------------
DROP TABLE IF EXISTS "public"."configs";
CREATE TABLE "public"."configs" (
  "owner_id" int8 NOT NULL DEFAULT 0,
  "config" int4 NOT NULL DEFAULT 55,
  "sangue" int4 NOT NULL DEFAULT 1,
  "mira" int4 NOT NULL DEFAULT 1,
  "mao" int4 NOT NULL DEFAULT 0,
  "audio1" int4 NOT NULL DEFAULT 100,
  "audio2" int4 NOT NULL DEFAULT 100,
  "audio_enable" int4 NOT NULL DEFAULT 7,
  "sensibilidade" int4 NOT NULL DEFAULT 50,
  "visao" int4 NOT NULL DEFAULT 70,
  "mouse_invertido" int4 NOT NULL DEFAULT 0,
  "msgconvite" int4 NOT NULL DEFAULT 0,
  "chatsusurro" int4 NOT NULL DEFAULT 0,
  "macro" int4 NOT NULL DEFAULT 0,
  "macro_1" varchar(32) COLLATE "pg_catalog"."default" NOT NULL DEFAULT ''::character varying,
  "macro_2" varchar(32) COLLATE "pg_catalog"."default" NOT NULL DEFAULT ''::character varying,
  "macro_3" varchar(32) COLLATE "pg_catalog"."default" NOT NULL DEFAULT ''::character varying,
  "macro_4" varchar(32) COLLATE "pg_catalog"."default" NOT NULL DEFAULT ''::character varying,
  "macro_5" varchar(32) COLLATE "pg_catalog"."default" NOT NULL DEFAULT ''::character varying
)
;

-- ----------------------------
-- Records of configs
-- ----------------------------

-- ----------------------------
-- Table structure for events_login
-- ----------------------------
DROP TABLE IF EXISTS "public"."events_login";
CREATE TABLE "public"."events_login" (
  "start_date" int8 NOT NULL DEFAULT 0,
  "end_date" int8 NOT NULL DEFAULT 0,
  "reward_id" int4 NOT NULL DEFAULT 0,
  "reward_count" int4 NOT NULL DEFAULT 0
)
;

-- ----------------------------
-- Records of events_login
-- ----------------------------

-- ----------------------------
-- Table structure for events_mapbonus
-- ----------------------------
DROP TABLE IF EXISTS "public"."events_mapbonus";
CREATE TABLE "public"."events_mapbonus" (
  "start_date" int8 NOT NULL DEFAULT 0,
  "end_date" int8 NOT NULL DEFAULT 0,
  "map_id" int4 NOT NULL DEFAULT 0,
  "stage_type" int4 NOT NULL DEFAULT 0,
  "percent_xp" int4 NOT NULL DEFAULT 0,
  "percent_gp" int4 NOT NULL DEFAULT 0
)
;

-- ----------------------------
-- Records of events_mapbonus
-- ----------------------------

-- ----------------------------
-- Table structure for events_playtime
-- ----------------------------
DROP TABLE IF EXISTS "public"."events_playtime";
CREATE TABLE "public"."events_playtime" (
  "start_date" int8 NOT NULL DEFAULT 0,
  "end_date" int8 NOT NULL DEFAULT 0,
  "title" varchar(30) COLLATE "pg_catalog"."default" NOT NULL DEFAULT ''::character varying,
  "seconds_target" int8 NOT NULL DEFAULT 1000,
  "good_reward1" int4 NOT NULL DEFAULT 0,
  "good_reward2" int4 NOT NULL DEFAULT 0,
  "good_count1" int4 NOT NULL DEFAULT 0,
  "good_count2" int4 NOT NULL DEFAULT 0
)
;

-- ----------------------------
-- Records of events_playtime
-- ----------------------------

-- ----------------------------
-- Table structure for events_quest
-- ----------------------------
DROP TABLE IF EXISTS "public"."events_quest";
CREATE TABLE "public"."events_quest" (
  "start_date" int8 NOT NULL DEFAULT 0,
  "end_date" int8 NOT NULL DEFAULT 0
)
;

-- ----------------------------
-- Records of events_quest
-- ----------------------------

-- ----------------------------
-- Table structure for events_rankup
-- ----------------------------
DROP TABLE IF EXISTS "public"."events_rankup";
CREATE TABLE "public"."events_rankup" (
  "start_date" int8 NOT NULL DEFAULT 0,
  "end_date" int8 NOT NULL DEFAULT 0,
  "percent_xp" int4 NOT NULL DEFAULT 0,
  "percent_gp" int4 NOT NULL DEFAULT 0
)
;

-- ----------------------------
-- Records of events_rankup
-- ----------------------------
INSERT INTO "public"."events_rankup" VALUES (171218, 311219, 1500, 1500);
INSERT INTO "public"."events_rankup" VALUES (171218, 311219, 1500, 1500);
INSERT INTO "public"."events_rankup" VALUES (181217, 191231, 1500, 1500);
INSERT INTO "public"."events_rankup" VALUES (181217, 191231, 1500, 1500);
INSERT INTO "public"."events_rankup" VALUES (171218005, 3112190000, 1500, 1500);
INSERT INTO "public"."events_rankup" VALUES (171218005, 3112190000, 1500, 1500);
INSERT INTO "public"."events_rankup" VALUES (1812170000, 1912310000, 1500, 1500);
INSERT INTO "public"."events_rankup" VALUES (1812170000, 1912310000, 1500, 1500);

-- ----------------------------
-- Table structure for events_visit
-- ----------------------------
DROP TABLE IF EXISTS "public"."events_visit";
CREATE TABLE "public"."events_visit" (
  "event_id" int4 NOT NULL DEFAULT nextval('check_event_seq'::regclass),
  "start_date" int8 NOT NULL DEFAULT 0,
  "end_date" int8 NOT NULL DEFAULT 0,
  "title" varchar(59) COLLATE "pg_catalog"."default" NOT NULL DEFAULT ''::character varying,
  "checks" int4 NOT NULL DEFAULT 7,
  "goods1" varchar COLLATE "pg_catalog"."default" NOT NULL,
  "counts1" varchar COLLATE "pg_catalog"."default" NOT NULL,
  "goods2" varchar COLLATE "pg_catalog"."default" NOT NULL DEFAULT ''::character varying,
  "counts2" varchar COLLATE "pg_catalog"."default" NOT NULL DEFAULT ''::character varying
)
;

-- ----------------------------
-- Records of events_visit
-- ----------------------------

-- ----------------------------
-- Table structure for events_xmas
-- ----------------------------
DROP TABLE IF EXISTS "public"."events_xmas";
CREATE TABLE "public"."events_xmas" (
  "start_date" int8 NOT NULL DEFAULT 0,
  "end_date" int8 NOT NULL DEFAULT 0
)
;

-- ----------------------------
-- Records of events_xmas
-- ----------------------------

-- ----------------------------
-- Table structure for friends
-- ----------------------------
DROP TABLE IF EXISTS "public"."friends";
CREATE TABLE "public"."friends" (
  "friend_id" int8 NOT NULL DEFAULT 0,
  "owner_id" int8 NOT NULL DEFAULT 0,
  "status" int4 NOT NULL DEFAULT 0
)
;

-- ----------------------------
-- Records of friends
-- ----------------------------

-- ----------------------------
-- Table structure for getcash
-- ----------------------------
DROP TABLE IF EXISTS "public"."getcash";
CREATE TABLE "public"."getcash" (
  "login" varchar(255) COLLATE "pg_catalog"."default",
  "timegetcash" int4
)
;

-- ----------------------------
-- Records of getcash
-- ----------------------------

-- ----------------------------
-- Table structure for getcash_vip_gold
-- ----------------------------
DROP TABLE IF EXISTS "public"."getcash_vip_gold";
CREATE TABLE "public"."getcash_vip_gold" (
  "login" varchar(255) COLLATE "pg_catalog"."default",
  "timegetcash" int4
)
;

-- ----------------------------
-- Records of getcash_vip_gold
-- ----------------------------



-- ----------------------------
-- Table structure for noticias
-- ----------------------------
DROP TABLE IF EXISTS "public"."noticias";
CREATE TABLE "public"."noticias" (
  "titulo" text COLLATE "pg_catalog"."default",
  "noticia" text COLLATE "pg_catalog"."default",
  "autor" text COLLATE "pg_catalog"."default",
  "tipo" text COLLATE "pg_catalog"."default",
  "data" date,
  "id" int8,
  "col_name" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Records of noticias
-- ----------------------------
INSERT INTO "public"."noticias" VALUES ('dou o cu e chupo rola', '<p>dou o cu e chupo rola</p>', 'malakaloca', 'Eventos', '2020-05-30', 1, NULL);
INSERT INTO "public"."noticias" VALUES ('12312', '<p>teste</p>', 'teste', 'Noticias', '2020-05-30', 2, NULL);
INSERT INTO "public"."noticias" VALUES ('ste23', '<p>teste</p>', 'teste', 'Noticias', '2020-05-30', 3, NULL);
INSERT INTO "public"."noticias" VALUES ('teasd', '<p>tasdas</p>', 'teste', 'Punicoes', '2020-05-30', 4, NULL);
INSERT INTO "public"."noticias" VALUES ('123', '<p>123</p>', 'teste', 'Atualizacao', '2020-05-30', 5, NULL);
INSERT INTO "public"."noticias" VALUES ('Avasdas', '<p>123</p>', 'teste', 'Avisos', '2020-05-30', 6, NULL);

-- ----------------------------
-- Table structure for pin
-- ----------------------------
DROP TABLE IF EXISTS "public"."pin";
CREATE TABLE "public"."pin" (
  "id" int4,
  "pin" text COLLATE "pg_catalog"."default",
  "valor" int8
)
;

-- ----------------------------
-- Records of pin
-- ----------------------------
INSERT INTO "public"."pin" VALUES (2, '1958642946589456', 100000);

-- ----------------------------
-- Table structure for pin_log
-- ----------------------------
DROP TABLE IF EXISTS "public"."pin_log";
CREATE TABLE "public"."pin_log" (
  "login" varchar(255) COLLATE "pg_catalog"."default",
  "pin" varchar(255) COLLATE "pg_catalog"."default",
  "valor" varchar(255) COLLATE "pg_catalog"."default",
  "data" date
)
;

-- ----------------------------
-- Table structure for suporte
-- ----------------------------
DROP TABLE IF EXISTS "public"."suporte";
CREATE TABLE "public"."suporte" (
  "nickname" text COLLATE "pg_catalog"."default",
  "titulo" text COLLATE "pg_catalog"."default",
  "mensagem" text COLLATE "pg_catalog"."default",
  "status" text COLLATE "pg_catalog"."default",
  "id" int4,
  "create_date" date
)
;

-- ----------------------------
-- Records of suporte
-- ----------------------------
INSERT INTO "public"."suporte" VALUES ('teste', 'teste', 'teste', '0', 1, '2020-05-30');

-- ----------------------------
-- Table structure for titles
-- ----------------------------
DROP TABLE IF EXISTS "public"."titles";
CREATE TABLE "public"."titles" (
  "owner_id" int8 NOT NULL DEFAULT 0,
  "titleequiped1" int4 NOT NULL DEFAULT 0,
  "titleequiped2" int4 NOT NULL DEFAULT 0,
  "titleequiped3" int4 NOT NULL DEFAULT 0,
  "titlepos1" int4 NOT NULL DEFAULT 0,
  "titlepos2" int4 NOT NULL DEFAULT 0,
  "titlepos3" int4 NOT NULL DEFAULT 0,
  "titlepos4" int4 NOT NULL DEFAULT 0,
  "titlepos5" int4 NOT NULL DEFAULT 0,
  "titlepos6" int4 NOT NULL DEFAULT 0,
  "title1" int4 NOT NULL DEFAULT 0,
  "title2" int4 NOT NULL DEFAULT 0,
  "title3" int4 NOT NULL DEFAULT 0,
  "title4" int4 NOT NULL DEFAULT 0,
  "title5" int4 NOT NULL DEFAULT 0,
  "title6" int4 NOT NULL DEFAULT 0,
  "title7" int4 NOT NULL DEFAULT 0,
  "title8" int4 NOT NULL DEFAULT 0,
  "title9" int4 NOT NULL DEFAULT 0,
  "title10" int4 NOT NULL DEFAULT 0,
  "title11" int4 NOT NULL DEFAULT 0,
  "title12" int4 NOT NULL DEFAULT 0,
  "title13" int4 NOT NULL DEFAULT 0,
  "title14" int4 NOT NULL DEFAULT 0,
  "title15" int4 NOT NULL DEFAULT 0,
  "title16" int4 NOT NULL DEFAULT 0,
  "title17" int4 NOT NULL DEFAULT 0,
  "title18" int4 NOT NULL DEFAULT 0,
  "title19" int4 NOT NULL DEFAULT 0,
  "title20" int4 NOT NULL DEFAULT 0,
  "title21" int4 NOT NULL DEFAULT 0,
  "title22" int4 NOT NULL DEFAULT 0,
  "title23" int4 NOT NULL DEFAULT 0,
  "title24" int4 NOT NULL DEFAULT 0,
  "title25" int4 NOT NULL DEFAULT 0,
  "title26" int4 NOT NULL DEFAULT 0,
  "title27" int4 NOT NULL DEFAULT 0,
  "title28" int4 NOT NULL DEFAULT 0,
  "title29" int4 NOT NULL DEFAULT 0,
  "title30" int4 NOT NULL DEFAULT 0,
  "title31" int4 NOT NULL DEFAULT 0,
  "title32" int4 NOT NULL DEFAULT 0,
  "title33" int4 NOT NULL DEFAULT 0,
  "title34" int4 NOT NULL DEFAULT 0,
  "title35" int4 NOT NULL DEFAULT 0,
  "title36" int4 NOT NULL DEFAULT 0,
  "title37" int4 NOT NULL DEFAULT 0,
  "title38" int4 NOT NULL DEFAULT 0,
  "title39" int4 NOT NULL DEFAULT 0,
  "title40" int4 NOT NULL DEFAULT 0,
  "title41" int4 NOT NULL DEFAULT 0,
  "title42" int4 NOT NULL DEFAULT 0,
  "title43" int4 NOT NULL DEFAULT 0,
  "title44" int4 NOT NULL DEFAULT 0
)
;

-- ----

-- ----------------------------
-- Table structure for vip
-- ----------------------------
DROP TABLE IF EXISTS "public"."vip";
CREATE TABLE "public"."vip" (
  "player_id" varchar(255) COLLATE "pg_catalog"."default",
  "login" varchar(255) COLLATE "pg_catalog"."default",
  "player_name" varchar(255) COLLATE "pg_catalog"."default",
  "data_dado" date,
  "data_final" date
)
;

-- ----------------------------
-- Records of vip
-- ----------------------------

-- ----------------------------
-- Function structure for insert_account_activity
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."insert_account_activity"();
CREATE OR REPLACE FUNCTION "public"."insert_account_activity"()
  RETURNS "pg_catalog"."trigger" AS $BODY$
            BEGIN
            INSERT INTO account_activity(account_id) VALUES (NEW.id);
            RETURN NEW;
            END$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;

-- ----------------------------
-- Function structure for insert_player_stats
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."insert_player_stats"();
CREATE OR REPLACE FUNCTION "public"."insert_player_stats"()
  RETURNS "pg_catalog"."trigger" AS $BODY$
            BEGIN
            INSERT INTO player_stats(player_id) VALUES (NEW.id);
            RETURN NEW;
            END$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
SELECT setval('"public"."account_id_seq"', 692, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
SELECT setval('"public"."accounts_id_seq"', 10, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
SELECT setval('"public"."ban_seq"', 10, false);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
SELECT setval('"public"."channels_id_seq"', 10, false);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
SELECT setval('"public"."check_event_seq"', 10, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
SELECT setval('"public"."clan_seq"', 130, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
SELECT setval('"public"."clans_id_seq"', 10, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
SELECT setval('"public"."contas_seq"', 10, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
SELECT setval('"public"."gameservers_id_seq"', 10, false);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
SELECT setval('"public"."gift_id_seq"', 10, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
SELECT setval('"public"."ipsystem_id_seq"', 10, false);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
SELECT setval('"public"."ipsystem_seq"', 10, false);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
SELECT setval('"public"."items_id_seq"', 28173, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
SELECT setval('"public"."jogador_amigo_seq"', 10, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
SELECT setval('"public"."jogador_inventario_seq"', 10, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
SELECT setval('"public"."jogador_mensagem_seq"', 10, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
SELECT setval('"public"."loja_seq"', 10, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
SELECT setval('"public"."message_id_seq"', 986, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
SELECT setval('"public"."player_eqipment_id_seq"', 10, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
SELECT setval('"public"."player_friends_player_account_id_seq"', 10, false);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
SELECT setval('"public"."player_mails_player_account_id_seq"', 5, false);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
SELECT setval('"public"."players_id_seq"', 10, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
SELECT setval('"public"."storage_seq"', 10, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
SELECT setval('"public"."templates_id_seq"', 10, false);

-- ----------------------------
-- Primary Key structure for table accounts_rank
-- ----------------------------
ALTER TABLE "public"."accounts_rank" ADD CONSTRAINT "accounts_rank_pkey" PRIMARY KEY ("player_id") WITH (fillfactor=23);

-- ----------------------------
-- Primary Key structure for table clan_data
-- ----------------------------
ALTER TABLE "public"."clan_data" ADD CONSTRAINT "clan_data_pkey" PRIMARY KEY ("clan_id");

-- ----------------------------
-- Primary Key structure for table configs
-- ----------------------------
ALTER TABLE "public"."configs" ADD CONSTRAINT "configs_pkey" PRIMARY KEY ("owner_id");

-- ----------------------------
-- Primary Key structure for table player_configs
-- ----------------------------
ALTER TABLE "public"."player_configs" ADD CONSTRAINT "player_configs_pkey" PRIMARY KEY ("owner_id");

-- ----------------------------
-- Primary Key structure for table titles
-- ----------------------------
ALTER TABLE "public"."titles" ADD CONSTRAINT "titles_pkey" PRIMARY KEY ("owner_id");
