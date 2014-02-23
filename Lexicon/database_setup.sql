-- Lexicon database setup
-- version: 05.10.2010


-- create database if necessary - otherwise simply use an existing db
CREATE DATABASE `lexicon` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `lexicon`;

-- create table
CREATE TABLE IF NOT EXISTS `lexicon` (
	`lang` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
	`namespace` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'core',
	`key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
	`value` text COLLATE utf8_unicode_ci NOT NULL,
	`comment` text COLLATE utf8_unicode_ci,
	PRIMARY KEY (`lang`,`namespace`,`key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- example data
INSERT INTO `lexicon` (`lang`, `namespace`, `key`, `value`, `comment`) VALUES
('de', 'search', 'not_found', 'Ihr Suchbegriff konnte nicht gefunden werden.', NULL),
('en', 'search', 'not_found', 'The word you searched could not be found.', NULL),
('es', 'search', 'not_found', 'No se ha encontrado el término que busca.', NULL),
('fr', 'search', 'not_found', 'Aucun résultat correspondant à votre recherche n''a pu être trouvé.', NULL),
('it', 'search', 'not_found', 'La ricerca non ha prodotto risultati', NULL),
('ja', 'search', 'not_found', '検索キーワードが見つかりませんでした。', NULL),
('pt', 'search', 'not_found', 'Não foi possível encontrar a sua chave de pesquisa.', NULL),
('ru', 'search', 'not_found', 'Поисковое понятие не было найдено.', NULL),
('zh', 'search', 'not_found', '未能找到您的搜索关键词。', NULL),
('de', 'search', 'search', 'suchen', NULL),
('en', 'search', 'search', 'search', NULL),
('es', 'search', 'search', 'Buscar', NULL),
('fr', 'search', 'search', 'Rechercher', NULL),
('it', 'search', 'search', 'Cercare', NULL),
('ja', 'search', 'search', '検索', NULL),
('pt', 'search', 'search', 'pesquisar', NULL),
('ru', 'search', 'search', 'Найти', NULL),
('zh', 'search', 'search', '进行搜索', NULL),
('de', 'login', 'login', 'Einloggen', NULL),
('en', 'login', 'login', 'Log in', NULL),
('es', 'login', 'login', 'Iniciar sesión', NULL),
('fr', 'login', 'login', 'Se connecter', NULL),
('it', 'login', 'login', 'Accedere', NULL),
('ja', 'login', 'login', 'ログイン', NULL),
('pt', 'login', 'login', 'Iiniciar sessão', NULL),
('ru', 'login', 'login', 'Авторизовать в системе', NULL),
('zh', 'login', 'login', '登录', NULL),
('de', 'login', 'min_username_length', 'Der Nutzername soll min. {CHARCOUNT} Zeichen lang sein.', '{CHARCOUNT} would be replaced by a number'),
('en', 'login', 'min_username_length', 'Minimum length of user name is {CHARCOUNT} chars.', '{CHARCOUNT} would be replaced by a number'),
('es', 'login', 'min_username_length', 'Longitud mínima del nombre de usuario {CHARCOUNT}', '{CHARCOUNT} would be replaced by a number'),
('fr', 'login', 'min_username_length', 'Longueur minimale du pseudo {CHARCOUNT}', '{CHARCOUNT} would be replaced by a number'),
('it', 'login', 'min_username_length', 'Lunghezza minima del nome utente {CHARCOUNT}', '{CHARCOUNT} would be replaced by a number'),
('ja', 'login', 'min_username_length', 'ユーザー名の最低文字数 {CHARCOUNT}', '{CHARCOUNT} would be replaced by a number'),
('pt', 'login', 'min_username_length', 'Tamanho mínimo do nome do utilizador {CHARCOUNT}', '{CHARCOUNT} would be replaced by a number'),
('ru', 'login', 'min_username_length', 'Минимальная длина  имени пользователя {CHARCOUNT}', '{CHARCOUNT} would be replaced by a number'),
('zh', 'login', 'min_username_length', '用户名最小长度 {CHARCOUNT}', '{CHARCOUNT} would be replaced by a number'),
('de', 'contact', 'location', 'Ort', 'Stadt/Gemeinde'),
('en', 'contact', 'location', 'Location', 'Stadt/Gemeinde'),
('es', 'contact', 'location', 'Localidad', 'Stadt/Gemeinde'),
('fr', 'contact', 'location', 'Lieu', 'Stadt/Gemeinde'),
('it', 'contact', 'location', 'Luogo', 'Stadt/Gemeinde'),
('ja', 'contact', 'location', '場所', 'Stadt/Gemeinde'),
('pt', 'contact', 'location', 'Localidade', 'Stadt/Gemeinde'),
('ru', 'contact', 'location', 'Населенный пункт', 'Stadt/Gemeinde'),
('zh', 'contact', 'location', '地区', 'Stadt/Gemeinde');
