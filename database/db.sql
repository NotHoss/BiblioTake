DROP TABLE IF EXISTS recensione;
DROP TABLE IF EXISTS prestito;
DROP TABLE IF EXISTS libro_tag;
DROP TABLE IF EXISTS tag;
DROP TABLE IF EXISTS libro;
DROP TABLE IF EXISTS utente;
DROP TABLE IF EXISTS biblioteca;

CREATE TABLE biblioteca (
	id INT AUTO_INCREMENT PRIMARY KEY,
	indirizzo VARCHAR(255) NOT NULL UNIQUE,
	telefono VARCHAR(20) NOT NULL,
	email VARCHAR(255) NOT NULL,
	note TEXT NOT NULL,
	orario_lun_ven VARCHAR(100) NOT NULL,
	orario_sabato VARCHAR(100) NOT NULL,
	orario_domenica VARCHAR(100) NOT NULL
);

CREATE TABLE utente (
	id INT AUTO_INCREMENT PRIMARY KEY,
	email VARCHAR(255) NOT NULL UNIQUE,
	username VARCHAR(100) NOT NULL,
	password VARCHAR(255) NOT NULL,
	foto_profilo VARCHAR(255) NOT NULL,
	attivo BOOLEAN NOT NULL DEFAULT TRUE,
	ruolo ENUM('utente', 'admin') NOT NULL DEFAULT 'utente'
);

CREATE TABLE libro (
	id INT AUTO_INCREMENT PRIMARY KEY,
	codice_isbn VARCHAR(20) NOT NULL UNIQUE,
	titolo VARCHAR(255) NOT NULL,
	autore VARCHAR(255) NOT NULL,
	casa_editrice VARCHAR(255) NOT NULL,
	edizione INT NOT NULL,
	anno YEAR NOT NULL,
	lingua VARCHAR(100) NOT NULL,
	descrizione TEXT,
	pagine INT NOT NULL,
	copertina VARCHAR(255) NOT NULL,
	categoria VARCHAR(100) NOT NULL,
	CHECK (edizione > 0),
	CHECK (pagine > 0)
);

CREATE TABLE tag (
	id INT AUTO_INCREMENT PRIMARY KEY,
	nome VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE libro_tag (
	libro_id INT NOT NULL,
	tag_id   INT NOT NULL,
	PRIMARY KEY (libro_id, tag_id),
	CONSTRAINT libro_tag_libro_fk FOREIGN KEY (libro_id)
		REFERENCES libro(id)
		ON UPDATE CASCADE
		ON DELETE CASCADE,
	CONSTRAINT libro_tag_tag_fk FOREIGN KEY (tag_id)
		REFERENCES tag(id)
		ON UPDATE CASCADE
		ON DELETE CASCADE
);

CREATE TABLE prestito (
	id INT AUTO_INCREMENT PRIMARY KEY,
	data_inizio DATETIME NOT NULL,
	data_fine DATETIME NOT NULL,
	stato ENUM('attivo', 'concluso', 'in_ritardo') NOT NULL,
	biblioteca_id INT NOT NULL,
	libro_id INT NOT NULL,
	utente_id INT NOT NULL,
	CONSTRAINT prestito_biblioteca_fk FOREIGN KEY (biblioteca_id)
		REFERENCES biblioteca(id)
		ON UPDATE CASCADE
		ON DELETE RESTRICT,
	CONSTRAINT prestito_libro_fk FOREIGN KEY (libro_id)
		REFERENCES libro(id)
		ON UPDATE CASCADE
		ON DELETE RESTRICT,
	CONSTRAINT prestito_utente_fk FOREIGN KEY (utente_id)
		REFERENCES utente(id)
		ON UPDATE CASCADE
		ON DELETE RESTRICT,
	CHECK (data_fine >= data_inizio)
);

CREATE TABLE recensione (
	id INT AUTO_INCREMENT PRIMARY KEY,
	valutazione INT NOT NULL,
	testo TEXT,
	censura BOOLEAN NOT NULL DEFAULT FALSE,
	data DATETIME NOT NULL,
	libro_id INT NOT NULL,
	utente_id INT NOT NULL,
	CONSTRAINT recensione_libro_fk FOREIGN KEY (libro_id)
		REFERENCES libro(id)
		ON UPDATE CASCADE
		ON DELETE CASCADE,
	CONSTRAINT recensione_utente_fk FOREIGN KEY (utente_id)
		REFERENCES utente(id)
		ON UPDATE CASCADE
		ON DELETE CASCADE,
	CHECK (valutazione BETWEEN 1 AND 5)
);

CREATE INDEX idx_prestito_libro_stato ON prestito (libro_id, stato);
CREATE INDEX idx_recensione_libro ON recensione (libro_id);

INSERT INTO biblioteca (id, indirizzo, telefono, email, note, orario_lun_ven, orario_sabato, orario_domenica) VALUES
	(1, 'Via Garibaldi 12, Padova', '+39 02 88997766', 'contatti@bibliotake-padova.it', 'Abbiamo l''aria condizionata rotta, abbiamo avvisato i tecnici che arriveranno il prima possibile.', '9:00 - 19:00', '9:00 - 13:00', 'Chiuso');

INSERT INTO utente (id, email, username, password, foto_profilo, attivo, ruolo) VALUES
	(101, 'luca.rossi@gmail.com', 'luca.rossi', '$2y$10$e0NRm7l8iA92f1bR6xL4fOd8kSMNw2w5sY4qC8x0Qh7oA3fXvYf4K', 'images/default-avatar.jpg', TRUE, 'utente'),
	(102, 'user@gmail.com', 'user', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente'),
	(103, 'marco.verdi@gmail.com', 'marco.verdi', '$2y$10$A8h2kP5tR3mQ7vX1nD6yEuL4bS9zF2cH5jK8qW3rN0pT6mV4xC7Zg', 'images/default-avatar.jpg', FALSE, 'utente'),
	(104, 'admin@bibliotake.it', 'admin', '$2y$10$JRxG6EJzVeJG3.41PVJGUuF4D7YElYoN9c99XydwmJPCzd2jO3IPm', 'images/default-avatar.jpg', TRUE, 'admin'),
	(105, 'anna.neri@gmail.com', 'anna.neri', '$2y$10$V4nK2mP8qR1sT6uY3zX7cE4hJ9mL0oP2rS5tV8wZ1aC4dF7gH6Jk', 'images/default-avatar.jpg', TRUE, 'utente'),
	(106, 'paolo.mazza@gmail.com', 'paolo.mazza', '$2y$10$P1qW3eR5tY7uI9oP2aS4dF6gH8jK0lZ2xC4vB6nM8qW1eR3tY5uI', 'images/default-avatar.jpg', TRUE, 'utente'),
	(107, 'alessia.ferri@gmail.com', 'alessia.ferri', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente'),
	(108, 'davide.conti@gmail.com', 'davide.conti', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente'),
	(109, 'giulia.rizzo@gmail.com', 'giulia.rizzo', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente'),
	(110, 'federico.sanna@gmail.com', 'federico.sanna', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente'),
	(111, 'camilla.leone@gmail.com', 'camilla.leone', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente'),
	(112, 'matteo.galli@gmail.com', 'matteo.galli', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente'),
	(113, 'sofia.bernardi@gmail.com', 'sofia.bernardi', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente'),
	(114, 'lorenzo.greco@gmail.com', 'lorenzo.greco', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente'),
	(115, 'elena.marini@gmail.com', 'elena.marini', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente'),
	(116, 'marco.seri@gmail.com', 'marco.seri', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente'),
	(117, 'iris.novelli@gmail.com', 'iris.novelli', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente'),
	(118, 'gianluca.moretti@gmail.com', 'gianluca.moretti', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente'),
	(119, 'beatrice.ortolani@gmail.com', 'beatrice.ortolani', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente'),
	(120, 'nicola.palma@gmail.com', 'nicola.palma', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente'),
	(121, 'caterina.piras@gmail.com', 'caterina.piras', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente'),
	(122, 'riccardo.fabbri@gmail.com', 'riccardo.fabbri', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente'),
	(123, 'valentina.serra@gmail.com', 'valentina.serra', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente'),
	(124, 'tommaso.baldi@gmail.com', 'tommaso.baldi', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente'),
	(125, 'martina.longo@gmail.com', 'martina.longo', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente'),
	(126, 'andrea.monti@gmail.com', 'andrea.monti', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente'),
	(127, 'chiara.vitali@gmail.com', 'chiara.vitali', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente'),
	(128, 'simone.costa@gmail.com', 'simone.costa', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente'),
	(129, 'laura.fontana@gmail.com', 'laura.fontana', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente'),
	(130, 'diego.pellegrini@gmail.com', 'diego.pellegrini', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente');

INSERT INTO libro (
	id, codice_isbn, titolo, autore, casa_editrice, edizione, anno, lingua, descrizione, pagine, copertina, categoria
) VALUES
	(201, '9788804668236', 'Il nome della rosa', 'Umberto Eco', 'Bompiani', 15, 2021, 'Italiano', 'Romanzo storico investigativo ambientato in un monastero medievale.', 624, 'images/default-cover.jpg', 'Romanzo storico'),
	(202, '9788806223129', 'Sapiens', 'Yuval Noah Harari', 'Bompiani', 9, 2020, 'Italiano', 'Saggio sulla storia dell''umanità dalle origini all''età contemporanea.', 538, 'images/default-cover.jpg', 'Saggistica'),
	(203, '9780140449136', 'The Odyssey', 'Homer', 'Penguin Classics', 3, 2018, 'Inglese', 'Traduzione annotata del poema epico greco.', 560, 'images/default-cover.jpg', 'Classici'),
	(204, '9788817168358', 'Norwegian Wood', 'Haruki Murakami', 'Einaudi', 12, 2019, 'Italiano', 'Romanzo di formazione ambientato nel Giappone degli anni Sessanta.', 336, 'images/default-cover.jpg', 'Narrativa contemporanea'),
	(205, '9788807891234', 'Il barone rampante', 'Italo Calvino', 'Mondadori', 11, 2022, 'Italiano', 'Romanzo di formazione e avventura ambientato sugli alberi.', 320, 'images/default-cover.jpg', 'Classici italiani'),
	(206, '9788806228889', 'L''ordine del tempo', 'Carlo Rovelli', 'Adelphi', 5, 2021, 'Italiano', 'Saggio divulgativo sul tempo e sulla fisica contemporanea.', 256, 'images/default-cover.jpg', 'Saggistica scientifica'),
	(207, '9781408855652', 'Harry Potter e la pietra filosofale', 'J.K. Rowling', 'Salani', 8, 2023, 'Italiano', 'Primo capitolo della saga del maghetto più famoso.', 336, 'images/default-cover.jpg', 'Fantasy'),
	(208, '9788804685271', 'Se questo è un uomo', 'Primo Levi', 'Einaudi', 14, 2024, 'Italiano', 'Testimonianza fondamentale sulla deportazione nei lager.', 208, 'images/default-cover.jpg', 'Memoria'),
	(209, '9788807000001', 'Orizzonti di vetro', 'Alba Ferri', 'Lumen', 1, 2016, 'Italiano', 'Storia di una ricerca personale tra città e ricordi.', 240, 'images/default-cover.jpg', 'Narrativa'),
	(210, '9788807000002', 'Mappe del silenzio', 'Pietro Rinaldi', 'Arca', 2, 2017, 'Italiano', 'Un viaggio lungo linee invisibili e relazioni sospese.', 288, 'images/default-cover.jpg', 'Romanzo'),
	(211, '9788807000003', 'La città sommersa', 'Nora Conti', 'Orione', 1, 2018, 'Italiano', 'Una memoria che riemerge da strade dimenticate.', 304, 'images/default-cover.jpg', 'Thriller'),
	(212, '9788807000004', 'Manuale delle ombre', 'Edoardo Villa', 'Lumen', 3, 2019, 'Italiano', 'Appunti narrativi su paura, coraggio e scelta.', 272, 'images/default-cover.jpg', 'Saggio narrativo'),
	(213, '9788807000005', 'Il ponte delle maree', 'Sara De Santis', 'Arca', 2, 2020, 'Italiano', 'Una famiglia divisa tra partenze e ritorni.', 320, 'images/default-cover.jpg', 'Narrativa contemporanea'),
	(214, '9788807000006', 'Cronache di un inverno lento', 'Marco Bellini', 'Orione', 1, 2021, 'Italiano', 'Una piccola comunità attraversa un tempo sospeso.', 352, 'images/default-cover.jpg', 'Romanzo'),
	(215, '9788807000007', 'Atlante delle rotte perdute', 'Elisa Moretti', 'Lumen', 4, 2022, 'Italiano', 'Percorsi geografici e umani verso luoghi remoti.', 368, 'images/default-cover.jpg', 'Avventura'),
	(216, '9788807000008', 'La misura del vento', 'Luca Ferraro', 'Arca', 2, 2023, 'Italiano', 'Una riflessione sul cambiamento e sull''attesa.', 224, 'images/default-cover.jpg', 'Poesia'),
	(217, '9788807000009', 'Racconti del quartiere nord', 'Giada Neri', 'Orione', 1, 2024, 'Italiano', 'Scene brevi e precise da un quartiere in trasformazione.', 192, 'images/default-cover.jpg', 'Racconti'),
	(218, '9788807000010', 'Quaderno di stelle', 'Matteo Riva', 'Lumen', 5, 2025, 'Italiano', 'Un quaderno di osservazioni tra scienza e meraviglia.', 280, 'images/default-cover.jpg', 'Saggistica scientifica'),
	(219, '9788807000011', 'Luce dopo la pioggia', 'Irene Costa', 'Arca', 1, 2016, 'Italiano', 'Un gruppo di amici ritrova il proprio equilibrio.', 264, 'images/default-cover.jpg', 'Formazione'),
	(220, '9788807000012', 'Geometrie dell''alba', 'Paolo Marin', 'Orione', 3, 2017, 'Italiano', 'Linee, forme e relazioni in una città che si sveglia.', 312, 'images/default-cover.jpg', 'Narrativa sperimentale'),
	(221, '9788807000013', 'Il custode delle chiavi', 'Claudia Gatti', 'Lumen', 2, 2018, 'Italiano', 'Segreti di famiglia e archivi nascosti.', 336, 'images/default-cover.jpg', 'Giallo'),
	(222, '9788807000014', 'Sotto il cielo di rame', 'Andrea Fonti', 'Arca', 1, 2019, 'Italiano', 'Una storia di frontiera e coraggio.', 248, 'images/default-cover.jpg', 'Avventura'),
	(223, '9788807000015', 'Inventario delle memorie', 'Silvia Romano', 'Orione', 2, 2020, 'Italiano', 'Un catalogo di ricordi che cambia il presente.', 360, 'images/default-cover.jpg', 'Narrativa'),
	(224, '9788807000016', 'Tracce nel bosco', 'Davide Serra', 'Lumen', 1, 2021, 'Italiano', 'Indizi minimi per una storia di sopravvivenza.', 296, 'images/default-cover.jpg', 'Thriller'),
	(225, '9788807000017', 'La formula dei giorni', 'Martina Leone', 'Arca', 4, 2022, 'Italiano', 'Equilibrio fra routine, lavoro e desiderio di cambiamento.', 240, 'images/default-cover.jpg', 'Formazione'),
	(226, '9788807000018', 'Il respiro delle pietre', 'Fabio Greco', 'Orione', 2, 2023, 'Italiano', 'Un paesaggio antico osservato attraverso le persone.', 288, 'images/default-cover.jpg', 'Storico'),
	(227, '9788807000019', 'Mosaico di voci', 'Chiara Sala', 'Lumen', 1, 2024, 'Italiano', 'Vite diverse si intrecciano in una sola narrazione.', 344, 'images/default-cover.jpg', 'Corale'),
	(228, '9788807000020', 'Nodi di carta', 'Roberto Pini', 'Arca', 3, 2025, 'Italiano', 'Lettere, appunti e relazioni che cambiano tutto.', 216, 'images/default-cover.jpg', 'Narrativa epistolare'),
	(229, '9788807000021', 'La forma del ritorno', 'Elena Ricci', 'Orione', 2, 2026, 'Italiano', 'Il ritorno in un luogo familiare rivela nuove distanze.', 312, 'images/default-cover.jpg', 'Narrativa contemporanea'),
	(230, '9788807000022', 'Oltre la linea calma', 'Simone Valenti', 'Lumen', 1, 2021, 'Italiano', 'Un viaggio lento verso una scelta definitiva.', 260, 'images/default-cover.jpg', 'Avventura');

INSERT INTO tag (id, nome) VALUES
	(1, 'Medioevo'),
	(2, 'Giallo'),
	(3, 'Storia'),
	(4, 'Filosofia'),
	(5, 'Giappone'),
	(6, 'Formazione'),
	(7, 'Avventura urbana'),
	(8, 'Cittadinanza'),
	(9, 'Design'),
	(10, 'Economia'),
	(11, 'Fotografia'),
	(12, 'Frontiera'),
	(13, 'Geografia'),
	(14, 'Introspezione'),
	(15, 'Leggende'),
	(16, 'Letteratura'),
	(17, 'Montagna'),
	(18, 'Musica'),
	(19, 'Natura'),
	(20, 'Nuove generazioni'),
	(21, 'Oriente'),
	(22, 'Racconti brevi'),
	(23, 'Relazioni'),
	(24, 'Ricerca'),
	(25, 'Romanzo'),
	(26, 'Scienze'),
	(27, 'Spazio'),
	(28, 'Tecnologia'),
	(29, 'Tradizione'),
	(30, 'Viaggio');

INSERT INTO libro_tag (libro_id, tag_id) VALUES
	(201, 1),
	(201, 2),
	(202, 3),
	(202, 4),
	(204, 5),
	(204, 6),
	(201, 7),
	(202, 8),
	(209, 9),
	(210, 10),
	(211, 11),
	(212, 12),
	(213, 13),
	(214, 14),
	(215, 15),
	(216, 16),
	(217, 17),
	(218, 18),
	(219, 19),
	(220, 20),
	(221, 21),
	(222, 22),
	(223, 23),
	(224, 24),
	(225, 25),
	(226, 26),
	(227, 27),
	(228, 28),
	(229, 29),
	(230, 30);

INSERT INTO prestito (
	id, data_inizio, data_fine, stato, biblioteca_id, libro_id, utente_id
) VALUES
	(401, '2026-03-05 10:30:00', '2026-03-19 10:30:00', 'concluso', 1, 201, 101),
	(402, '2026-03-12 16:00:00', '2026-03-26 16:00:00', 'in_ritardo', 1, 202, 102),
	(403, '2026-03-22 11:15:00', '2027-04-05 11:15:00', 'attivo', 1, 204, 101),
	(404, '2026-04-02 09:00:00', '2026-04-16 09:00:00', 'attivo', 1, 205, 105),
	(405, '2026-04-10 14:20:00', '2026-04-24 14:20:00', 'concluso', 1, 206, 106),
	(406, '2026-04-15 11:00:00', '2026-04-29 11:00:00', 'in_ritardo', 1, 207, 101),
	(407, '2026-04-20 17:30:00', '2026-05-04 17:30:00', 'attivo', 1, 208, 102),
	(408, '2026-05-02 09:00:00', '2026-05-16 09:00:00', 'attivo', 1, 209, 107),
	(409, '2026-05-04 10:00:00', '2026-05-18 10:00:00', 'concluso', 1, 210, 108),
	(410, '2026-05-06 11:30:00', '2026-05-20 11:30:00', 'in_ritardo', 1, 211, 109),
	(411, '2026-05-08 12:15:00', '2026-05-22 12:15:00', 'attivo', 1, 212, 110),
	(412, '2026-05-10 14:00:00', '2026-05-24 14:00:00', 'concluso', 1, 213, 111),
	(413, '2026-05-12 15:30:00', '2026-05-26 15:30:00', 'attivo', 1, 214, 112),
	(414, '2026-05-14 09:45:00', '2026-05-28 09:45:00', 'in_ritardo', 1, 215, 113),
	(415, '2026-05-16 16:20:00', '2026-05-30 16:20:00', 'concluso', 1, 216, 114),
	(416, '2026-05-18 10:10:00', '2026-06-01 10:10:00', 'attivo', 1, 217, 115),
	(417, '2026-05-20 11:40:00', '2026-06-03 11:40:00', 'concluso', 1, 218, 116),
	(418, '2026-05-22 13:05:00', '2026-06-05 13:05:00', 'attivo', 1, 219, 117),
	(419, '2026-05-24 17:00:00', '2026-06-07 17:00:00', 'in_ritardo', 1, 220, 118),
	(420, '2026-05-26 08:30:00', '2026-06-09 08:30:00', 'concluso', 1, 221, 119),
	(421, '2026-05-28 09:50:00', '2026-06-11 09:50:00', 'attivo', 1, 222, 120),
	(422, '2026-05-30 11:25:00', '2026-06-13 11:25:00', 'concluso', 1, 223, 121),
	(423, '2026-06-01 14:15:00', '2026-06-15 14:15:00', 'attivo', 1, 224, 122),
	(424, '2026-06-03 15:45:00', '2026-06-17 15:45:00', 'in_ritardo', 1, 225, 123),
	(425, '2026-06-05 10:05:00', '2026-06-19 10:05:00', 'concluso', 1, 226, 124),
	(426, '2026-06-07 12:35:00', '2026-06-21 12:35:00', 'attivo', 1, 227, 125),
	(427, '2026-06-09 16:50:00', '2026-06-23 16:50:00', 'concluso', 1, 228, 126),
	(428, '2026-06-11 09:15:00', '2026-06-25 09:15:00', 'attivo', 1, 229, 127),
	(429, '2026-06-13 13:55:00', '2026-06-27 13:55:00', 'in_ritardo', 1, 230, 128),
	(430, '2026-06-15 18:10:00', '2026-06-29 18:10:00', 'attivo', 1, 201, 129);

INSERT INTO recensione (id, valutazione, testo, censura, data, libro_id, utente_id) VALUES
	(501, 5, 'Trama avvincente e personaggi memorabili, uno dei miei preferiti.', FALSE, '2026-03-20 18:45:00', 201, 101),
	(502, 4, 'Molto interessante e ricco di spunti, a tratti impegnativo.', FALSE, '2026-03-27 09:20:00', 202, 102),
	(503, 5, 'Edizione ottima, note utili e traduzione scorrevole.', TRUE, '2026-03-25 14:10:00', 203, 104),
	(504, 4, 'Storia intensa e atmosfera molto particolare.', FALSE, '2026-04-05 12:00:00', 204, 101),
	(505, 5, 'Ottimo per chi ama il fantasy classico e ben scritto.', FALSE, '2026-04-18 19:30:00', 207, 105),
	(506, 5, 'Testo fondamentale, molto forte e necessario.', FALSE, '2026-04-22 21:15:00', 208, 102),
	(507, 5, 'Narrazione limpida e finale molto ben costruito.', FALSE, '2026-05-03 18:00:00', 209, 107),
	(508, 4, 'Ricco di dettagli e con un ritmo equilibrato.', FALSE, '2026-05-05 19:10:00', 210, 108),
	(509, 3, 'Interessante ma alcuni passaggi risultano lenti.', FALSE, '2026-05-07 17:45:00', 211, 109),
	(510, 5, 'Atmosfera intensa e personaggi credibili.', FALSE, '2026-05-09 20:30:00', 212, 110),
	(511, 4, 'Ottima struttura e stile molto scorrevole.', FALSE, '2026-05-11 16:25:00', 213, 111),
	(512, 5, 'Mi ha coinvolto dall''inizio alla fine.', FALSE, '2026-05-13 21:05:00', 214, 112),
	(513, 4, 'Buon equilibrio tra tema e sviluppo narrativo.', FALSE, '2026-05-15 15:15:00', 215, 113),
	(514, 2, 'Idea valida, ma la resa finale convince poco.', FALSE, '2026-05-17 14:40:00', 216, 114),
	(515, 5, 'Molto curato e con una voce originale.', FALSE, '2026-05-19 18:35:00', 217, 115),
	(516, 4, 'Lettura piacevole e piena di spunti.', FALSE, '2026-05-21 19:50:00', 218, 116),
	(517, 5, 'Un racconto delicato e ben orchestrato.', TRUE, '2026-05-23 11:20:00', 219, 117),
	(518, 3, 'Buona idea, ma avrei voluto più approfondimento.', FALSE, '2026-05-25 12:55:00', 220, 118),
	(519, 4, 'Ottima cura editoriale e contenuti solidi.', FALSE, '2026-05-27 13:15:00', 221, 119),
	(520, 5, 'Molto riuscito, lascia una sensazione positiva.', FALSE, '2026-05-29 18:05:00', 222, 120),
	(521, 4, 'Interessante la costruzione del mondo narrativo.', FALSE, '2026-05-31 17:10:00', 223, 121),
	(522, 2, 'Alcuni capitoli sono più deboli del previsto.', FALSE, '2026-06-02 15:20:00', 224, 122),
	(523, 5, 'Testo denso ma estremamente piacevole.', FALSE, '2026-06-04 20:00:00', 225, 123),
	(524, 4, 'Scrittura pulita e temi ben bilanciati.', FALSE, '2026-06-06 16:45:00', 226, 124),
	(525, 3, 'Sufficiente, ma non tra i miei preferiti.', FALSE, '2026-06-08 19:30:00', 227, 125),
	(526, 5, 'Una lettura vivace e molto curata.', FALSE, '2026-06-10 11:05:00', 228, 126),
	(527, 4, 'Ottimo per chi cerca una storia compatta.', FALSE, '2026-06-12 12:20:00', 229, 127),
	(528, 5, 'Bellissimo equilibrio tra forma e contenuto.', FALSE, '2026-06-14 14:55:00', 230, 128),
	(529, 4, 'Solido, scorrevole e ben costruito.', FALSE, '2026-06-16 10:35:00', 201, 129),
	(530, 5, 'Rilettura molto soddisfacente e sempre attuale.', TRUE, '2026-06-18 11:45:00', 202, 130);
