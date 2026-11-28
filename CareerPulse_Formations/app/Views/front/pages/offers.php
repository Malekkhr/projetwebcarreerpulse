<main class="cp-section cp-offers-page">
    <div class="cp-container">
        <?php if ($selectedOffer !== null): ?>
            <a class="cp-text-link" href="<?= e(route('offers')) ?>">← Retour aux offres</a>
            <article class="cp-panel cp-offer-detail">
                <div class="cp-offer-tags"><span class="cp-offer-type"><?= e($selectedOffer->getTypeName()) ?></span><span class="cp-offer-mode"><?= e($selectedOffer->getWorkMode()) ?></span></div>
                <h1><?= e($selectedOffer->getTitle()) ?></h1>
                <p class="cp-offer-company"><?= e($selectedOffer->getCompany()) ?> · <?= e($selectedOffer->getLocation()) ?></p>
                <p class="cp-muted">Date limite : <?= e($selectedOffer->getDeadline() ?: 'Non précisée') ?></p>
                <hr>
                <h2>Description</h2>
                <p><?= nl2br(e($selectedOffer->getDescription())) ?></p>
                <?php if ($selectedOffer->getRequirements() !== ''): ?><h2>Profil recherché</h2><p><?= nl2br(e($selectedOffer->getRequirements())) ?></p><?php endif; ?>
                <a href="<?= e(route('offers')) ?>" class="cp-btn cp-btn--primary">Parcourir les autres offres</a>
            </article>
        <?php else: ?>
            <header class="cp-page-heading cp-offers-heading">
                <p class="cp-eyebrow">CARRIÈRE &amp; OPPORTUNITÉS</p>
                <h1>Offres de stage et d’emploi</h1>
                <p class="cp-muted">Consultez les opportunités publiées par les entreprises et filtrez-les par type ou mode de travail.</p>
            </header>

            <form method="get" action="index.php" class="cp-panel cp-offer-filters">
                <input type="hidden" name="action" value="offers">
                <div class="cp-field"><label for="offer-search">Mot-clé, entreprise ou ville</label><input class="cp-input" id="offer-search" type="search" name="q" value="<?= e($search) ?>" maxlength="100" placeholder="Ex. développeur, stage, Tunis"></div>
                <div class="cp-field"><label for="offer-type-filter">Type d’offre</label><select class="cp-input" id="offer-type-filter" name="type_id"><option value="0">Tous les types</option><?php foreach ($offerTypes as $type): ?><option value="<?= $type->getId() ?>" <?= $selectedType === $type->getId() ? 'selected' : '' ?>><?= e($type->getName()) ?></option><?php endforeach; ?></select></div>
                <div class="cp-field"><label for="offer-mode-filter">Mode de travail</label><select class="cp-input" id="offer-mode-filter" name="work_mode"><option value="">Tous les modes</option><?php foreach (['Sur site','Hybride','Télétravail'] as $mode): ?><option value="<?= e($mode) ?>" <?= $selectedWorkMode === $mode ? 'selected' : '' ?>><?= e($mode) ?></option><?php endforeach; ?></select></div>
                <div class="cp-offer-filter-actions"><button type="submit" class="cp-btn cp-btn--primary">Rechercher</button><a href="<?= e(route('offers')) ?>" class="cp-btn cp-btn--outline">Réinitialiser</a></div>
            </form>

            <div class="cp-offers-results-head"><h2 class="cp-section-title"><?= count($offers) ?> offre(s) disponible(s)</h2></div>
            <?php if ($offers === []): ?>
                <div class="cp-panel cp-empty-state"><h2>Aucune offre trouvée</h2><p class="cp-muted">Essayez un autre mot-clé ou retirez un filtre.</p><a href="<?= e(route('offers')) ?>" class="cp-btn cp-btn--outline">Effacer les filtres</a></div>
            <?php else: ?>
                <div class="cp-offers-grid">
                    <?php foreach ($offers as $offer): ?>
                        <article class="cp-panel cp-offer-card">
                            <div class="cp-offer-tags"><span class="cp-offer-type"><?= e($offer->getTypeName()) ?></span><span class="cp-offer-mode"><?= e($offer->getWorkMode()) ?></span></div>
                            <h2><?= e($offer->getTitle()) ?></h2>
                            <p class="cp-offer-company"><?= e($offer->getCompany()) ?></p>
                            <p class="cp-muted"><i class="fa fa-map-marker" aria-hidden="true"></i> <?= e($offer->getLocation()) ?></p>
                            <p><?= e(mb_substr($offer->getDescription(), 0, 180)) ?><?= mb_strlen($offer->getDescription()) > 180 ? '…' : '' ?></p>
                            <div class="cp-offer-card-footer"><small>Date limite : <?= e($offer->getDeadline() ?: 'Non précisée') ?></small><a class="cp-btn cp-btn--primary cp-btn--sm" href="<?= e(route('offers', ['id' => $offer->getId()])) ?>">Voir le détail</a></div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>
