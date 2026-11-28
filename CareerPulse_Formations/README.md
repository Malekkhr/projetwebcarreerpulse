# CareerPulse — Formations, Offres, Catégories & Comptes

Sous-projet autonome extrait de **CareerPulse** conservant :
- La même charte graphique (`#1f3d2b`, `#0f2a1d`, `#d9a531`), le même logo **CareerPulse** et la typographie `Arial, Helvetica, sans-serif`.
- La gestion complète des **Formations** et des **Catégories** (Front-office filtrable en JavaScript ES6+ / DOM Events et Back-office CRUD complet).
- La gestion des **Comptes Utilisateurs** (`Inscription`, `Connexion`, `Mon profil`, `Admin > Utilisateurs`) ainsi que l'inscription des utilisateurs aux formations, reliés à la même base de données MySQL **`formation`**.

## Installation rapide (XAMPP / WAMP)
1. Importer `database/formation.sql` dans **phpMyAdmin** (base `formation`).
   *Base déjà importée avec l'ancienne structure ?* Exécuter `database/migration_workshop.sql` **ou** supprimer la base puis réimporter.
2. Vérifier `config/database.php` (`host: localhost`, `name: formation`, `user: root`, `password: ''`).
3. Lancer `Lancer_CareerPulse.bat` ou ouvrir `http://localhost/CareerPulse_Formations/index.php`.

### Comptes de test (mots de passe hachés en base)
| Rôle | E-mail | Mot de passe | Redirection après connexion |
|---|---|---|---|
| `admin` | `admin@careerpulse.test` | `Admin@1234` | `index.php?action=admin` (**backoffice**) |
| `user` | `etudiant@careerpulse.test` | `User@1234` | `index.php?action=dashboard` (**frontoffice**) |

> ⚠ Changer ces mots de passe avant toute mise en ligne. Les rôles se gèrent depuis le backoffice : Gestion des utilisateurs.

## Gestion complète des utilisateurs (CRUD + rôles + autorisations)

Backoffice, réservé au rôle `admin` (contrôle refait en base à chaque requête dans `Admin/AdminController`).

| Fonctionnalité | Route | Méthode | Détails |
|---|---|---|---|
| **Read** : liste, recherche, filtre par rôle | `admin/users` | GET | recherche nom / e-mail / formation, filtre `user` / `admin` |
| **Read** : fiche détaillée | `admin/users/show?id=` | GET | infos + formations suivies |
| **Create** : ajout | `admin/users/create` | GET / POST | validation serveur, `password_hash`, choix du rôle et du statut |
| **Update** : modification | `admin/users/edit?id=` | GET / POST | identité, rôle, statut, mot de passe facultatif |
| **Delete** : suppression | `admin/users/delete` | POST | confirmation JS + CSRF ; inscriptions supprimées en cascade (FK) |
| **Rôles** : changement de rôle | `admin/users/role` | POST | sélecteur `user` / `admin` dans la liste |
| **Statut** : suspension / réactivation | `admin/users/status` | POST | un compte suspendu perd l'accès immédiatement |

**Garde-fous** : un admin ne peut ni se supprimer, ni se suspendre, ni changer son propre rôle ; l'application refuse toute opération qui supprimerait le **dernier administrateur actif**. Rôles et statuts reçus du formulaire sont validés par liste blanche.

**MVC** : `Models/UserModel.php` (attributs + getters/setters + `fromArray`, sans SQL), `Controllers/Admin/UserController.php` (logique + SQL préparé), `Views/admin/pages/users.php`, `user-form.php`, `user-show.php` (HTML + `e()`).

**Côté utilisateur (frontoffice)** : `profile` permet aussi de changer son mot de passe (mot de passe actuel exigé, `session_regenerate_id` ensuite). La déconnexion se fait en **POST + CSRF** (formulaire), plus par simple lien.

### Tests rapides (gestion des utilisateurs)
- **Autorisation** : connecté en `etudiant`, ouvrir `index.php?action=admin/users` (ou `/create`, `/edit`) → page 403 ; déconnecté → redirection vers la connexion.
- **Create** : ajouter un utilisateur avec un e-mail déjà pris → message « déjà utilisé » ; mot de passe `abc` → erreurs de validation.
- **Update** : modifier le rôle d'un compte, puis se connecter avec ce compte → le nouvel accès est appliqué immédiatement.
- **Delete** : supprimer l'étudiant → il disparaît de la liste ; tenter de supprimer son propre compte → refusé.
- **Dernier admin** : avec un seul admin, tout changement de son rôle / statut / suppression est refusé.
- **CSRF** : retirer le champ `csrf_token` (F12) puis valider un formulaire → « Jeton de sécurité invalide ».

## Conformité au workshop « Sessions et Sécurité PHP »
Point d'entrée unique `index.php?action=...` (table de routage : `app/routes.php`).

| Exigence du workshop | Où dans le projet |
|---|---|
| **MVC — Modèle** : attributs privés + getters/setters + `fromArray()`, sans SQL | `app/Models/UserModel.php`, `CourseModel.php`, `CategoryModel.php` |
| **MVC — Contrôleur** : toute la logique (SQL préparé, hachage, validation, sessions, CSRF, rôles) | `app/Controllers/AuthController.php` + `ProfileController`, `CourseController`, `Admin/*` ; requêtes partagées : `UserQueries.php`, `CatalogQueries.php` |
| **MVC — Vue** : HTML + `htmlspecialchars()` (helper `e()`), aucun SQL | `app/Views/front/**` (frontoffice), `app/Views/admin/**` (backoffice) |
| Connexion PDO **Singleton** `getConnexion()` (`ERRMODE_EXCEPTION`, `EMULATE_PREPARES=false`) | `app/Database.php` + `config/database.php` |
| Table `users` (`username`, `email`, `password`, `role ENUM('user','admin')`) | `database/formation.sql` |
| Inscription : validation serveur (regex username, `filter_var`, ≥ 8 car.), `password_hash()`, rôle forcé à `user` | `AuthController::register()` |
| Connexion : `password_verify()`, message générique, `session_regenerate_id(true)` | `AuthController::login()` |
| Déconnexion : vidage `$_SESSION`, cookie supprimé, `session_destroy()` | `AuthController::logout()` |
| Session sécurisée : `use_strict_mode`, `cookie_httponly`, `cookie_samesite=Lax`, expiration 30 min | `index.php` (blocs 1 et 2) |
| `isAuthenticated()`, `requireAuth()`, `hasRole()`, `requireRole()` (403) | `AuthController` — `requireRole` relit le rôle **en base** à chaque page admin |
| **Frontoffice** (rôle `user`) : accueil, formations, inscription aux formations, `dashboard`, `profile` | routes `home`, `courses`, `register`, `login`, `dashboard`, `profile` |
| **Backoffice** (rôle `admin` uniquement) : tableau de bord, formations, catégories, utilisateurs (CRUD + rôles) | routes `admin`, `admin/courses`, `admin/categories`, `admin/users*` (contrôle dans `Admin/AdminController`) |
| Interfaces visuellement distinctes front / back | front : CSS3 Flexbox/Grid ; back : template TailAdmin (barre latérale, étiquette BACKOFFICE) |
| Injections SQL | requêtes préparées partout (aucune concaténation de saisie) |
| XSS | `e()` sur toute sortie + validation des entrées + en-tête CSP |
| CSRF | jeton `csrf_token` par session, champ caché `csrf_input()`, `hash_equals()` |
| En-têtes HTTP | `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Content-Security-Policy` (`index.php`) |

La validation JavaScript du formulaire d'inscription (`assets/front/js/app.js`) n'est qu'une **aide à la saisie** : les mêmes règles sont revérifiées côté serveur.

### Tests rapides
- **403 / accès refusé** : connecté en `etudiant`, ouvrir `index.php?action=admin` → page « Accès refusé » ; déconnecté → redirection vers la connexion.
- **Injection SQL** : dans la connexion, remplacer `type="email"` par `type="text"` (F12) et saisir `' OR '1'='1` → « E-mail ou mot de passe incorrect. »
- **XSS** : insérer un compte `<script>alert('test')</script>` via phpMyAdmin (mot de passe `User@1234`) → le nom s'affiche en texte brut.
- **CSRF** : supprimer le champ caché `csrf_token` (F12) puis valider → « Jeton de sécurité invalide ».
- **Expiration** : mettre `$sessionLifetime = 10;` dans `index.php`, attendre 10 s, recharger `?action=dashboard`.

## Contrôle et validation des saisies (Input Validation) — PHP **et** JavaScript

**Côté serveur (PHP, validation de référence)** : tous les formulaires sont revérifiés (`trim`, regex, `filter_var`, longueurs max, listes blanches, `(int)`) :
inscription, connexion, profil, admin utilisateurs, admin formations (durée au format « nombre + unité », description ≤ 2000 car.), admin catégories.
En cas d'erreur, la saisie est **conservée** (`remember_input()` / `old_input()` dans `app/helpers.php`) : l'utilisateur corrige au lieu de tout retaper.

**Côté navigateur (JavaScript, aide à la saisie)** : `app/Views/assets/shared/validation.js` valide de façon déclarative tout formulaire marqué `data-cp-validate`
(règles `required`, `username`, `email`, `password`, `match`, `max`, `pattern`, `data-required-if`). Il est actif sur :

| Formulaire | Règles JS principales |
|---|---|
| Inscription (`register-form`, `app.js`) | username, e-mail, mot de passe (+ aide visuelle), confirmation |
| Connexion (`login-form`) | e-mail valide, mot de passe obligatoire |
| Profil (`profile-form`) | username, e-mail, longueurs, **changement de mot de passe** (actuel obligatoire si nouveau, confirmation identique) |
| Admin utilisateur (`user-form`) | identité, mot de passe obligatoire à la création / facultatif en édition |
| Admin formation (`course-form`) | titre, catégorie, durée (format), longueurs |
| Admin catégorie (`category-form`) | nom obligatoire ≤ 100, description ≤ 255 |

Les attributs `maxlength` sont aussi posés dans les vues. Le JavaScript ne remplace jamais le PHP : les mêmes règles sont refaites côté serveur.

## Sécurité des sessions — compléments

- **Compte suspendu / supprimé** : `AuthController::requireAuth()` relit désormais le compte en base à chaque requête protégée (frontoffice **et** backoffice) : un compte suspendu perd son accès immédiatement (inscription aux formations, profil, dashboard...).
- **Durée de session** : `session.gc_maxlifetime = 1800` (aligné sur les 30 min d'inactivité), `use_only_cookies`, `use_trans_sid=0`, cookie de session sans date d'expiration.
- **HTTPS** : `session.cookie_secure` est activé automatiquement dès que le site est servi en HTTPS.
- **Anti force brute** : 5 échecs de connexion consécutifs => connexion bloquée 5 minutes (compteur en session) ; `password_verify()` est exécuté même si l'e-mail n'existe pas (temps de réponse constant). *Limite : compteur lié à la session ; une protection par IP/compte en base serait l'étape suivante en production.*

## Module Offres et Types d’offres

Le module ajoute un CRUD administrateur des types et des offres, puis un catalogue public avec recherche, filtres par type et mode de travail, et une fiche détaillée.

- Routes publiques : index.php?action=offers et index.php?action=offers&id=1.
- Routes administrateur (rôle admin) : admin/offers et admin/offer-types.
- Modèles : app/Models/OfferModel.php et OfferTypeModel.php.
- Contrôleurs PDO : app/Controllers/OfferQueries.php, OfferController.php et Admin/OfferController.php / OfferTypeController.php.
- Jointure métier : offers.type_id référence offer_types.id ; la liste d’administration et le catalogue public chargent le type avec une jointure SQL préparée.
- Règles métier : nom de type unique ; type relié à une offre non supprimable ; type avec offres publiées non désactivable ; seules les offres publiées, non expirées et de type actif sont visibles du public ; date passée refusée pour une offre publiée.
- Validation des saisies : longueurs et listes autorisées vérifiées en PHP, contrôle de saisie ajouté dans les formulaires avec validation.js, jeton CSRF sur chaque écriture.
- Base déjà installée : importer database/migration_offres.sql dans phpMyAdmin. Nouvelle installation : database/formation.sql crée aussi les tables et insère des exemples de démonstration.

## Architecture CSS du front-office (Chapitre 3 : Flexbox & Grid)
- `careerpulse.css` : CSS3 pur, **sans Bootstrap**, approche **mobile-first** (`min-width: 576 / 768 / 1024 / 1200px`).
- **Flexbox** : navbar, boutons, cartes formations (`flex: 1 0 auto`), pastilles, CTA, centrage des formulaires, footer sticky (`body` en colonne).
- **Grid** : hero `7fr 5fr`, grilles `repeat(auto-fit, minmax(250px, 1fr))`, formulaire profil, page Formations et Footer en `grid-template-areas`.
- `order`, `align-self`, `gap`, `box-sizing: border-box`, meta viewport et media queries conformément au cours.
