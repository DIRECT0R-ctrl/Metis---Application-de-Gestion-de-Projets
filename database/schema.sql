CREATE TYPE projet_type AS ENUM('court', 'long');

CREATE TABLE membres (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL
);

CREATE TABLE projets (
    id SERIAL PRIMARY KEY,
    titre VARCHAR(150) NOT NULL,
    type projet_type NOT NULL,
    date_debut TIMESTAMP NOT NULL,
    membre_id INT NOT NULL,
    CONSTRAINT fk_projet_membre
        FOREIGN KEY (membre_id)
        REFERENCES membres(id)
        ON DELETE RESTRICT
);

CREATE TABLE activites (
    id SERIAL PRIMARY KEY,
    description TEXT NOT NULL,
    date_activite TIMESTAMP NOT NULL,
    projet_id INT NOT NULL,
    CONSTRAINT fk_activite_projet
        FOREIGN KEY (projet_id)
        REFERENCES projets(id)
        ON DELETE RESTRICT
);

