DROP TABLE IF EXISTS recensione;
DROP TABLE IF EXISTS prestito;
DROP TABLE IF EXISTS libro;
DROP TABLE IF EXISTS utente;
DROP TABLE IF EXISTS biblioteca;

CREATE TABLE biblioteca (
	id INT AUTO_INCREMENT PRIMARY KEY,
	indirizzo VARCHAR(255) NOT NULL UNIQUE,
	telefono VARCHAR(20) NOT NULL,
	email VARCHAR(255) NOT NULL,
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

INSERT INTO biblioteca (id, indirizzo, telefono, email, orario_lun_ven, orario_sabato, orario_domenica) VALUES
	(1, 'Via Garibaldi 12, Padova', '+39 02 88997766', 'contatti@bibliotake-padova.it', '9:00 - 19:00', '9:00 - 13:00', 'Chiuso');

INSERT INTO utente (id, email, username, password, foto_profilo, attivo, ruolo) VALUES
	(101, 'luca.rossi@gmail.com', 'luca.rossi', '$2y$10$e0NRm7l8iA92f1bR6xL4fOd8kSMNw2w5sY4qC8x0Qh7oA3fXvYf4K', 'images/default-avatar.jpg', TRUE, 'utente'),
	(102, 'user@gmail.com', 'user', '$2y$10$CMi/jExR0PTN.f4K6TJwkejd3hL4yV03Gkyt/3v7ALhq7rWcHHO7y', 'images/default-avatar.jpg', TRUE, 'utente'),
	(103, 'marco.verdi@gmail.com', 'marco.verdi', '$2y$10$A8h2kP5tR3mQ7vX1nD6yEuL4bS9zF2cH5jK8qW3rN0pT6mV4xC7Zg', 'images/default-avatar.jpg', FALSE, 'utente'),
	(104, 'admin@bibliotake.it', 'admin', '$2y$10$JRxG6EJzVeJG3.41PVJGUuF4D7YElYoN9c99XydwmJPCzd2jO3IPm', 'images/default-avatar.jpg', TRUE, 'admin'),
	(105, 'anna.neri@gmail.com', 'anna.neri', '$2y$10$V4nK2mP8qR1sT6uY3zX7cE4hJ9mL0oP2rS5tV8wZ1aC4dF7gH6Jk', 'images/default-avatar.jpg', TRUE, 'utente'),
	(106, 'paolo.mazza@gmail.com', 'paolo.mazza', '$2y$10$P1qW3eR5tY7uI9oP2aS4dF6gH8jK0lZ2xC4vB6nM8qW1eR3tY5uI', 'images/default-avatar.jpg', TRUE, 'utente');

INSERT INTO libro (
	id, codice_isbn, titolo, autore, casa_editrice, edizione, anno, lingua, descrizione, pagine, copertina, categoria
) VALUES
	(201, '9788804668236', 'Il nome della rosa', 'Umberto Eco', 'Bompiani', 15, 2021, 'Italiano', 'Romanzo storico investigativo ambientato in un monastero medievale.', 624, 'images/default-cover.jpg', 'Romanzo storico'),
	(202, '9788806223129', 'Sapiens', 'Yuval Noah Harari', 'Bompiani', 9, 2020, 'Italiano', 'Saggio sulla storia dell umanita dalle origini all eta contemporanea.', 538, 'images/default-cover.jpg', 'Saggistica'),
	(203, '9780140449136', 'The Odyssey', 'Homer', 'Penguin Classics', 3, 2018, 'Inglese', 'Traduzione annotata del poema epico greco.', 560, 'images/default-cover.jpg', 'Classici'),
	(204, '9788817168358', 'Norwegian Wood', 'Haruki Murakami', 'Einaudi', 12, 2019, 'Italiano', 'Romanzo di formazione ambientato nel Giappone degli anni Sessanta.', 336, 'images/default-cover.jpg', 'Narrativa contemporanea'),
	(205, '9788807891234', 'Il barone rampante', 'Italo Calvino', 'Mondadori', 11, 2022, 'Italiano', 'Romanzo di formazione e avventura ambientato sugli alberi.', 320, 'images/default-cover.jpg', 'Classici italiani'),
	(206, '9788806228889', 'L ordine del tempo', 'Carlo Rovelli', 'Adelphi', 5, 2021, 'Italiano', 'Saggio divulgativo sul tempo e sulla fisica contemporanea.', 256, 'images/default-cover.jpg', 'Saggistica scientifica'),
	(207, '9781408855652', 'Harry Potter e la pietra filosofale', 'J.K. Rowling', 'Salani', 8, 2023, 'Italiano', 'Primo capitolo della saga del maghetto più famoso.', 336, 'images/default-cover.jpg', 'Fantasy'),
	(208, '9788804685271', 'Se questo è un uomo', 'Primo Levi', 'Einaudi', 14, 2024, 'Italiano', 'Testimonianza fondamentale sulla deportazione nei lager.', 208, 'images/default-cover.jpg', 'Memoria');

INSERT INTO prestito (
	id, data_inizio, data_fine, stato, biblioteca_id, libro_id, utente_id
) VALUES
	(401, '2026-03-05 10:30:00', '2026-03-19 10:30:00', 'concluso', 1, 201, 101),
	(402, '2026-03-12 16:00:00', '2026-03-26 16:00:00', 'in_ritardo', 1, 202, 102),
	(403, '2026-03-22 11:15:00', '2027-04-05 11:15:00', 'attivo', 1, 204, 101),
	(404, '2026-04-02 09:00:00', '2026-04-16 09:00:00', 'attivo', 1, 205, 105),
	(405, '2026-04-10 14:20:00', '2026-04-24 14:20:00', 'concluso', 1, 206, 106),
	(406, '2026-04-15 11:00:00', '2026-04-29 11:00:00', 'in_ritardo', 1, 207, 101),
	(407, '2026-04-20 17:30:00', '2026-05-04 17:30:00', 'attivo', 1, 208, 102);

INSERT INTO recensione (id, valutazione, testo, censura, data, libro_id, utente_id) VALUES
	(501, 5, 'Trama avvincente e personaggi memorabili, uno dei miei preferiti.', FALSE, '2026-03-20 18:45:00', 201, 101),
	(502, 4, 'Molto interessante e ricco di spunti, a tratti impegnativo.', FALSE, '2026-03-27 09:20:00', 202, 102),
	(503, 5, 'Edizione ottima, note utili e traduzione scorrevole.', TRUE, '2026-03-25 14:10:00', 203, 104),
	(504, 4, 'Storia intensa e atmosfera molto particolare.', FALSE, '2026-04-05 12:00:00', 204, 101),
	(505, 5, 'Ottimo per chi ama il fantasy classico e ben scritto.', FALSE, '2026-04-18 19:30:00', 207, 105),
	(506, 5, 'Testo fondamentale, molto forte e necessario.', FALSE, '2026-04-22 21:15:00', 208, 102);
