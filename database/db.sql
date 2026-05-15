DROP TABLE IF EXISTS recensione;
DROP TABLE IF EXISTS prestito;
DROP TABLE IF EXISTS libro;
DROP TABLE IF EXISTS utente;
DROP TABLE IF EXISTS biblioteca;

CREATE TABLE biblioteca (
	id INT AUTO_INCREMENT PRIMARY KEY,
	indirizzo VARCHAR(255) NOT NULL UNIQUE,
	telefono VARCHAR(20) NOT NULL,
	email VARCHAR(255) NOT NULL
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

INSERT INTO biblioteca (id, indirizzo, telefono, email) VALUES
	(1, 'Via Garibaldi 12, Padova', '+39 02 88997766', 'contatti@bibliotake-padova.it');

INSERT INTO utente (id, email, username, password, foto_profilo, attivo, ruolo) VALUES
	(101, 'luca.rossi@gmail.com', 'luca.rossi', '$2y$10$e0NRm7l8iA92f1bR6xL4fOd8kSMNw2w5sY4qC8x0Qh7oA3fXvYf4K', 'images/place-holder.jpg', TRUE, 'utente'),
	(102, 'chiara.bianchi@gmail.com', 'chiara.b', '$2y$10$N4f7mZ3sWQk2p8r9dT1uMuoYV6W8h2xVb9jQ4mN3eL7pR6cS2dA1O', 'images/place-holder.jpg', TRUE, 'utente'),
	(103, 'marco.verdi@gmail.com', 'marco.verdi', '$2y$10$A8h2kP5tR3mQ7vX1nD6yEuL4bS9zF2cH5jK8qW3rN0pT6mV4xC7Zg', 'images/place-holder.jpg', FALSE, 'utente'),
	(104, 'admin@bibliotake.it', 'admin.bibliotake', '$2y$10$T7pQ2mN5vR8xC1zK4hD6yEa9sL3fW0uJ7bM2nP5qR8tV1xC4zH6Yd', 'images/place-holder.jpg', TRUE, 'admin');

INSERT INTO libro (
	id, codice_isbn, titolo, autore, casa_editrice, edizione, anno, lingua, descrizione, pagine, copertina, categoria
) VALUES
	(201, '9788804668236', 'Il nome della rosa', 'Umberto Eco', 'Bompiani', 15, 2021, 'Italiano', 'Romanzo storico investigativo ambientato in un monastero medievale.', 624, 'images/place-holder.jpg', 'Romanzo storico'),
	(202, '9788806223129', 'Sapiens', 'Yuval Noah Harari', 'Bompiani', 9, 2020, 'Italiano', 'Saggio sulla storia dell umanita dalle origini all eta contemporanea.', 538, 'images/place-holder.jpg', 'Saggistica'),
	(203, '9780140449136', 'The Odyssey', 'Homer', 'Penguin Classics', 3, 2018, 'Inglese', 'Traduzione annotata del poema epico greco.', 560, 'images/place-holder.jpg', 'Classici'),
	(204, '9788817168358', 'Norwegian Wood', 'Haruki Murakami', 'Einaudi', 12, 2019, 'Italiano', 'Romanzo di formazione ambientato nel Giappone degli anni Sessanta.', 336, 'images/place-holder.jpg', 'Narrativa contemporanea');

INSERT INTO prestito (
	id, data_inizio, data_fine, stato, biblioteca_id, libro_id, utente_id
) VALUES
	(401, '2026-03-05 10:30:00', '2026-03-19 10:30:00', 'concluso', 1, 201, 101),
	(402, '2026-03-12 16:00:00', '2026-03-26 16:00:00', 'in_ritardo', 1, 202, 102),
	(403, '2026-03-22 11:15:00', '2026-04-05 11:15:00', 'attivo', 1, 204, 101);

INSERT INTO recensione (id, valutazione, testo, data, libro_id, utente_id) VALUES
	(501, 5, 'Trama avvincente e personaggi memorabili, uno dei miei preferiti.', '2026-03-20 18:45:00', 201, 101),
	(502, 4, 'Molto interessante e ricco di spunti, a tratti impegnativo.', '2026-03-27 09:20:00', 202, 102),
	(503, 5, 'Edizione ottima, note utili e traduzione scorrevole.', '2026-03-25 14:10:00', 203, 104);

