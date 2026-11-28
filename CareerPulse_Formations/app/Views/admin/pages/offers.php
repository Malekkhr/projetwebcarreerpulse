<?php
$firstActiveType = null;
foreach ($offerTypes as $type) {
    if ($type->getStatus() === 'active') { $firstActiveType = $type->getId(); break; }
}
$form = [
    'type_id' => (int) old_input('type_id', (string) ($editingOffer?->getTypeId() ?? $firstActiveType ?? 0)),
    'title' => old_input('title', $editingOffer?->getTitle() ?? ''),
    'company' => old_input('company', $editingOffer?->getCompany() ?? ''),
    'location' => old_input('location', $editingOffer?->getLocation() ?? ''),
    'work_mode' => old_input('work_mode', $editingOffer?->getWorkMode() ?? 'Sur site'),
    'description' => old_input('description', $editingOffer?->getDescription() ?? ''),
    'requirements' => old_input('requirements', $editingOffer?->getRequirements() ?? ''),
    'deadline' => old_input('deadline', $editingOffer?->getDeadline() ?? ''),
    'status' => old_input('status', $editingOffer?->getStatus() ?? 'Brouillon'),
];
?>
<div class="mb-6">
    <p class="mb-2 text-xs font-bold uppercase tracking-[.16em] text-brand-600 dark:text-brand-400">OPPORTUNITÉS PROFESSIONNELLES</p>
    <h1 class="text-title-md font-bold text-gray-800 dark:text-white/90">Gestion des offres</h1>
    <p class="mt-2 text-theme-sm text-gray-500 dark:text-gray-400">Les offres sont reliées à un type, puis affichées dans le catalogue public lorsqu’elles sont publiées.</p>
</div>

<div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
    <section class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <h2 class="text-theme-xl font-bold text-gray-800 dark:text-white/90"><?= $editingOffer ? 'Modifier l’offre' : 'Ajouter une offre' ?></h2>
        <?php if ($firstActiveType === null): ?>
            <p class="mt-4 rounded-xl bg-warning-50 p-3 text-sm text-warning-700">Activez d’abord au moins un type d’offre pour pouvoir ajouter une offre.</p>
            <a class="mt-3 inline-flex rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white" href="<?= e(route('admin/offer-types')) ?>">Gérer les types</a>
        <?php else: ?>
        <form method="post" action="<?= e(route('admin/offers')) ?>" class="mt-4 space-y-4" data-cp-validate novalidate>
            <?= csrf_input() ?>
            <input type="hidden" name="action" value="<?= $editingOffer ? 'update' : 'create' ?>">
            <?php if ($editingOffer): ?><input type="hidden" name="offer_id" value="<?= $editingOffer->getId() ?>"><?php endif; ?>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-500" for="offer-type-id">Type d’offre *</label>
                <select class="w-full rounded-xl border border-gray-200 px-3.5 py-2.5 text-theme-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" id="offer-type-id" name="type_id" data-label="Le type d’offre" data-v="required" required>
                    <option value="">-- Choisir un type actif --</option>
                    <?php foreach ($offerTypes as $type): ?>
                        <?php if ($type->getStatus() !== 'active' && (!$editingOffer || $editingOffer->getTypeId() !== $type->getId())) { continue; } ?>
                        <option value="<?= $type->getId() ?>" <?= $form['type_id'] === $type->getId() ? 'selected' : '' ?>><?= e($type->getName()) ?><?= $type->getStatus() === 'inactive' ? ' (inactif)' : '' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-500" for="offer-title">Intitulé *</label>
                <input class="w-full rounded-xl border border-gray-200 px-3.5 py-2.5 text-theme-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" id="offer-title" name="title" value="<?= e($form['title']) ?>" maxlength="160" data-label="L’intitulé" data-v="required max" data-max="160" required>
            </div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div><label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-500" for="offer-company">Entreprise *</label><input class="w-full rounded-xl border border-gray-200 px-3.5 py-2.5 text-theme-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" id="offer-company" name="company" value="<?= e($form['company']) ?>" maxlength="120" data-label="L’entreprise" data-v="required max" data-max="120" required></div>
                <div><label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-500" for="offer-location">Lieu *</label><input class="w-full rounded-xl border border-gray-200 px-3.5 py-2.5 text-theme-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" id="offer-location" name="location" value="<?= e($form['location']) ?>" maxlength="120" data-label="Le lieu" data-v="required max" data-max="120" required></div>
            </div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div><label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-500" for="offer-work-mode">Mode de travail *</label><select class="w-full rounded-xl border border-gray-200 px-3.5 py-2.5 text-theme-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" id="offer-work-mode" name="work_mode"><?php foreach (['Sur site','Hybride','Télétravail'] as $mode): ?><option value="<?= e($mode) ?>" <?= $form['work_mode'] === $mode ? 'selected' : '' ?>><?= e($mode) ?></option><?php endforeach; ?></select></div>
                <div><label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-500" for="offer-deadline">Date limite</label><input class="w-full rounded-xl border border-gray-200 px-3.5 py-2.5 text-theme-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" id="offer-deadline" name="deadline" type="date" value="<?= e($form['deadline']) ?>" data-label="La date limite" data-v="pattern" data-pattern="^\d{4}-\d{2}-\d{2}$" data-pattern-message="Date invalide."></div>
            </div>
            <div><label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-500" for="offer-description">Description *</label><textarea class="w-full rounded-xl border border-gray-200 px-3.5 py-2.5 text-theme-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" id="offer-description" name="description" rows="5" maxlength="3000" data-label="La description" data-v="required max" data-max="3000" required><?= e($form['description']) ?></textarea></div>
            <div><label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-500" for="offer-requirements">Profil et compétences recherchés</label><textarea class="w-full rounded-xl border border-gray-200 px-3.5 py-2.5 text-theme-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" id="offer-requirements" name="requirements" rows="3" maxlength="2000" data-label="Les compétences" data-v="max" data-max="2000"><?= e($form['requirements']) ?></textarea></div>
            <div><label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-500" for="offer-status">Statut</label><select class="w-full rounded-xl border border-gray-200 px-3.5 py-2.5 text-theme-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" id="offer-status" name="status"><?php foreach (['Brouillon','Publiée','Clôturée'] as $status): ?><option value="<?= e($status) ?>" <?= $form['status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option><?php endforeach; ?></select><p class="mt-1 text-xs text-gray-400">Seules les offres publiées et non expirées apparaissent au public.</p></div>
            <div class="flex gap-2"><button class="flex-1 rounded-xl bg-brand-600 px-4 py-2.5 text-theme-sm font-semibold text-white hover:bg-brand-700" type="submit"><?= $editingOffer ? 'Enregistrer' : 'Ajouter l’offre' ?></button><?php if ($editingOffer): ?><a href="<?= e(route('admin/offers')) ?>" class="rounded-xl border border-gray-200 px-4 py-2.5 text-theme-sm font-semibold text-gray-600">Annuler</a><?php endif; ?></div>
        </form>
        <?php endif; ?>
    </section>

    <section class="rounded-2xl border border-gray-200 bg-white p-5 xl:col-span-2 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-theme-xl font-bold text-gray-800 dark:text-white/90">Offres enregistrées (<?= count($offers) ?>)</h2>
            <input type="search" data-table-search="#offers-table" placeholder="Filtrer les offres..." class="rounded-xl border border-gray-200 px-3.5 py-2 text-theme-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" aria-label="Filtrer les offres">
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left text-theme-sm" id="offers-table">
                <thead><tr class="border-y border-gray-100 text-xs uppercase tracking-wider text-gray-400 dark:border-gray-800"><th class="py-3 pr-4">Offre</th><th class="py-3 pr-4">Entreprise / lieu</th><th class="py-3 pr-4">Type</th><th class="py-3 pr-4">Mode</th><th class="py-3 pr-4">Clôture</th><th class="py-3 pr-4">Statut</th><th class="py-3">Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($offers as $offer): ?>
                        <tr class="border-b border-gray-100 last:border-0 dark:border-gray-800">
                            <td class="py-4 pr-4 font-semibold text-gray-800 dark:text-white/90"><?= e($offer->getTitle()) ?></td>
                            <td class="py-4 pr-4 text-gray-600 dark:text-gray-300"><?= e($offer->getCompany()) ?><small class="mt-1 block text-gray-400"><?= e($offer->getLocation()) ?></small></td>
                            <td class="py-4 pr-4"><span class="rounded-full bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-700 dark:bg-brand-500/15 dark:text-brand-400"><?= e($offer->getTypeName()) ?></span></td>
                            <td class="py-4 pr-4"><?= e($offer->getWorkMode()) ?></td>
                            <td class="py-4 pr-4"><?= e($offer->getDeadline() ?: '—') ?></td>
                            <td class="py-4 pr-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold <?= $offer->getStatus() === 'Publiée' ? 'bg-success-50 text-success-700' : ($offer->getStatus() === 'Clôturée' ? 'bg-gray-100 text-gray-600' : 'bg-warning-50 text-warning-700') ?>"><?= e($offer->getStatus()) ?></span></td>
                            <td class="py-4"><div class="flex gap-2">
                                <a href="<?= e(route('admin/offers', ['edit' => $offer->getId()])) ?>" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 dark:border-gray-700 dark:text-gray-200">Modifier</a>
                                <form method="post" action="<?= e(route('admin/offers')) ?>" class="js-confirm-delete" data-label="<?= e($offer->getTitle()) ?>">
                                    <?= csrf_input() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="offer_id" value="<?= $offer->getId() ?>">
                                    <button type="submit" class="rounded-lg bg-error-50 px-3 py-1.5 text-xs font-semibold text-error-700">Supprimer</button>
                                </form>
                            </div></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
