<main>
    <section class="cp-hero">
        <div class="cp-container cp-hero-grid">
            <div class="cp-hero-text">
                <p class="cp-eyebrow">ORIENTATION · CATÉGORIES · FORMATIONS</p>
                <h1>Développez vos compétences avec CareerPulse</h1>
                <p class="cp-hero-copy">Explorez nos catégories pédagogiques, inscrivez-vous aux formations adaptées à votre parcours et suivez votre progression depuis votre compte étudiant.</p>
                <div class="cp-hero-actions">
                    <a href="<?= e(route('courses')) ?>" class="cp-btn cp-btn--primary cp-btn--lg">Explorer les formations <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                    <a href="<?= e(route('profile')) ?>" class="cp-btn cp-btn--light cp-btn--lg">Mon compte étudiant</a>
                    <a href="<?= e(route('offers')) ?>" class="cp-btn cp-btn--light cp-btn--lg">Découvrir les offres</a>
                </div>
                <div class="cp-proof-row">
                    <span><i class="bi bi-person-check" aria-hidden="true"></i> Compte &amp; inscription liés</span>
                    <span><i class="bi bi-grid" aria-hidden="true"></i> Catégories structurées</span>
                    <span><i class="bi bi-mortarboard" aria-hidden="true"></i> Catalogue de formations</span>
                </div>
            </div>
            <div class="cp-hero-visual">
                <img src="app/Views/assets/front/img/carousel-1.png" alt="Étudiants en formation" class="cp-hero-image">
                <div class="cp-match-card"><span class="cp-match-dot"></span><div><strong>Catalogue actif</strong><small><?= count($categories) ?> catégories · formations certifiantes</small></div><span class="cp-match-score">100%</span></div>
                <div class="cp-hero-caption"><span class="cp-pulse-line" aria-hidden="true">⌁</span> Votre parcours, en mouvement</div>
            </div>
        </div>
    </section>

    <section class="cp-section">
        <div class="cp-container">
            <div class="cp-section-head">
                <div><p class="cp-eyebrow">DOMAINES D’ÉTUDE</p><h2 class="cp-section-title">Catégories de formations</h2></div>
                <a href="<?= e(route('courses')) ?>" class="cp-text-link">Filtrer par catégorie <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
            </div>
            <div class="cp-grid-auto">
                <?php foreach ($categories as $cat): ?>
                    <div class="cp-feature">
                        <span class="cp-feature-icon"><i class="bi bi-folder2-open" aria-hidden="true"></i></span>
                        <h3><?= e($cat->getName()) ?></h3>
                        <p><?= e($cat->getDescription() !== '' ? $cat->getDescription() : 'Formations spécialisées dans ce domaine.') ?></p>
                        <p class="cp-feature-count"><?= (int) $cat->getCourseCount() ?> formation(s)</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="cp-section cp-section--soft">
        <div class="cp-container">
            <div class="cp-section-head">
                <div><p class="cp-eyebrow">À DÉCOUVRIR</p><h2 class="cp-section-title">Formations récentes</h2><p class="cp-muted">Catalogue synchronisé avec la base de données CareerPulse.</p></div>
                <a href="<?= e(route('courses')) ?>" class="cp-text-link">Voir toutes les formations <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
            </div>
            <div class="cp-grid-auto">
                <?php foreach ($courses as $course): ?>
                    <article class="cp-course-card">
                        <div class="cp-course-media">
                            <img src="app/Views/assets/front/img/<?= e($course->getImage()) ?>" alt="Illustration de la formation <?= e($course->getTitle()) ?>">
                            <span class="cp-course-label"><?= e($course->getCategory()) ?></span>
                        </div>
                        <div class="cp-course-body">
                            <h3><?= e($course->getTitle()) ?></h3>
                            <p class="cp-muted"><?= e($course->getSkills()) ?></p>
                        </div>
                        <div class="cp-course-meta">
                            <small><i class="fa fa-clock" aria-hidden="true"></i><?= e($course->getDuration()) ?></small>
                            <small><i class="fa fa-signal" aria-hidden="true"></i><?= e($course->getLevel()) ?></small>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="cp-cta-band">
        <div class="cp-container cp-cta-inner">
            <div><p class="cp-eyebrow">VOTRE PROCHAIN PAS</p><h2>Créez votre compte pour vous inscrire aux formations</h2></div>
            <a class="cp-btn cp-btn--light cp-btn--lg" href="<?= e(route('register')) ?>">Créer un compte</a>
        </div>
    </section>
</main>
