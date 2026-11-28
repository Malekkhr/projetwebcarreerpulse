-- CareerPulse : migration du module Offres et Types d’offres
-- À importer une seule fois dans phpMyAdmin pour une base formation déjà existante.
USE formation;
-- ---------- Types d’offres (Stage, Alternance, Emploi étudiant, etc.) ----------
CREATE TABLE IF NOT EXISTS offer_types (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(80) NOT NULL UNIQUE,
    description VARCHAR(255) NOT NULL DEFAULT '',
    status      ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------- Offres reliées à un type (jointure offers.type_id -> offer_types.id) ----------
CREATE TABLE IF NOT EXISTS offers (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    type_id      INT UNSIGNED NOT NULL,
    title        VARCHAR(160) NOT NULL,
    company      VARCHAR(120) NOT NULL,
    location     VARCHAR(120) NOT NULL,
    work_mode    ENUM('Sur site','Hybride','Télétravail') NOT NULL DEFAULT 'Sur site',
    description  TEXT NOT NULL,
    requirements TEXT NULL,
    deadline     DATE NULL,
    status       ENUM('Brouillon','Publiée','Clôturée') NOT NULL DEFAULT 'Brouillon',
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_offer_company_title (company, title),
    KEY idx_offer_type_status (type_id, status),
    KEY idx_offer_deadline (deadline),
    CONSTRAINT fk_offer_type FOREIGN KEY (type_id) REFERENCES offer_types(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Données de démonstration : types et offres CareerPulse.
INSERT IGNORE INTO offer_types (name, description) VALUES
('Stage', 'Stages de fin d’études, d’été et projets de pré-embauche.'),
('Alternance', 'Opportunités en alternance combinant cours et expérience en entreprise.'),
('Emploi étudiant', 'Missions à temps partiel compatibles avec les études.');

INSERT IGNORE INTO offers (type_id, title, company, location, work_mode, description, requirements, deadline, status)
SELECT ot.id, 'Stage Développeur PHP / MySQL', 'TechNova', 'Tunis', 'Hybride',
       'Participez à la création d’applications web en PHP et contribuez à la conception de bases de données relationnelles.',
       'Bases en PHP, HTML, CSS et SQL ; curiosité et envie d’apprendre.',
       DATE_ADD(CURDATE(), INTERVAL 60 DAY), 'Publiée'
FROM offer_types ot WHERE ot.name = 'Stage';

INSERT IGNORE INTO offers (type_id, title, company, location, work_mode, description, requirements, deadline, status)
SELECT ot.id, 'Stage UX/UI Designer', 'Campus Digital', 'Ariana', 'Sur site',
       'Aidez l’équipe produit à analyser les besoins des utilisateurs et à concevoir des prototypes d’interfaces.',
       'Notions de Figma, sens de l’observation et intérêt pour l’expérience utilisateur.',
       DATE_ADD(CURDATE(), INTERVAL 45 DAY), 'Publiée'
FROM offer_types ot WHERE ot.name = 'Stage';

INSERT IGNORE INTO offers (type_id, title, company, location, work_mode, description, requirements, deadline, status)
SELECT ot.id, 'Alternance Développeur Front-end', 'BrightLab', 'Tunis', 'Hybride',
       'Développez des interfaces web accessibles et adaptez-les aux différents formats d’écran.',
       'HTML, CSS, JavaScript et bases de Git.',
       DATE_ADD(CURDATE(), INTERVAL 75 DAY), 'Publiée'
FROM offer_types ot WHERE ot.name = 'Alternance';

INSERT IGNORE INTO offers (type_id, title, company, location, work_mode, description, requirements, deadline, status)
SELECT ot.id, 'Assistant support informatique étudiant', 'ServicePlus', 'Sfax', 'Télétravail',
       'Accompagnez les utilisateurs dans la résolution de demandes simples et documentez les solutions apportées.',
       'Aisance relationnelle, bases informatiques et disponibilité à temps partiel.',
       DATE_ADD(CURDATE(), INTERVAL 30 DAY), 'Publiée'
FROM offer_types ot WHERE ot.name = 'Emploi étudiant';
