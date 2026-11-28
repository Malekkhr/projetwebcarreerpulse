<?php
$form = [
    'name' => old_input('name', $editingType?->getName() ?? ''),
    'description' => old_input('description', $editingType?->getDescription() ?? ''),
    'status' => old_input('status', $editingType?->getStatus() ?? 'active'),
];
?>
<div class="mb-6">
    <p class="mb-2 text-xs font-bold uppercase tracking-[.16em] text-brand-600 dark:text-brand-400">TAXONOMIE DES OPPORTUNITÉS</p>
    <h1 class="text-title-md font-bold text-gray-800 dark:text-white/90">Gestion des types d’offres</h1>
    <p class="mt-2 text-theme-sm text-gray-500 dark:text-gray-400">Créez les types utilisés pour classer les offres de CareerPulse.</p>
</div>

<div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
    <section class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <h2 class="text-theme-xl font-bold text-gray-800 dark:text-white/90"><?= $editingType ? 'Modifier un type' : 'Nouveau type' ?></h2>
        <form method="post" action="<?= e(route('admin/offer-types')) ?>" class="mt-4 space-y-4" data-cp-validate novalidate>
            <?= csrf_input() ?>
            <input type="hidden" name="action" value="<?= $editingType ? 'update' : 'create' ?>">
            <?php if ($editingType): ?><input type="hidden" name="type_id" value="<?= (int) $editingType->getId() ?>"><?php endif; ?>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-500" for="offer-type-name">Nom *</label>
                <input class="w-full rounded-xl border border-gray-200 px-3.5 py-2.5 text-theme-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" id="offer-type-name" name="name" value="<?= e($form['name']) ?>" maxlength="80" data-label="Le nom du type" data-v="required max" data-max="80" required>
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-500" for="offer-type-description">Description</label>
                <textarea class="w-full rounded-xl border border-gray-200 px-3.5 py-2.5 text-theme-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" id="offer-type-description" name="description" rows="4" maxlength="255" data-label="La description" data-v="max" data-max="255"><?= e($form['description']) ?></textarea>
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-500" for="offer-type-status">Statut</label>
                <select class="w-full rounded-xl border border-gray-200 px-3.5 py-2.5 text-theme-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" id="offer-type-status" name="status">
                    <option value="active" <?= $form['status'] === 'active' ? 'selected' : '' ?>>Actif</option>
                    <option value="inactive" <?= $form['status'] === 'inactive' ? 'selected' : '' ?>>Inactif</option>
                </select>
                <p class="mt-1 text-xs text-gray-400">Un type inactif ne peut plus être choisi et n’apparaît plus au public.</p>
            </div>
            <div class="flex gap-2">
                <button class="flex-1 rounded-xl bg-brand-600 px-4 py-2.5 text-theme-sm font-semibold text-white hover:bg-brand-700" type="submit"><?= $editingType ? 'Enregistrer' : 'Créer le type' ?></button>
                <?php if ($editingType): ?><a href="<?= e(route('admin/offer-types')) ?>" class="rounded-xl border border-gray-200 px-4 py-2.5 text-theme-sm font-semibold text-gray-600">Annuler</a><?php endif; ?>
            </div>
        </form>
    </section>

    <section class="rounded-2xl border border-gray-200 bg-white p-5 xl:col-span-2 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-theme-xl font-bold text-gray-800 dark:text-white/90">Types enregistrés (<?= count($offerTypes) ?>)</h2>
            <input type="search" data-table-search="#offer-types-table" placeholder="Filtrer les types..." class="rounded-xl border border-gray-200 px-3.5 py-2 text-theme-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" aria-label="Filtrer les types">
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[620px] text-left text-theme-sm" id="offer-types-table">
                <thead><tr class="border-y border-gray-100 text-xs uppercase tracking-wider text-gray-400 dark:border-gray-800"><th class="py-3 pr-4">Type</th><th class="py-3 pr-4">Description</th><th class="py-3 pr-4">Offres liées</th><th class="py-3 pr-4">Statut</th><th class="py-3">Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($offerTypes as $type): ?>
                        <tr class="border-b border-gray-100 last:border-0 dark:border-gray-800">
                            <td class="py-4 pr-4 font-semibold text-gray-800 dark:text-white/90"><?= e($type->getName()) ?></td>
                            <td class="py-4 pr-4 text-gray-600 dark:text-gray-300"><?= e($type->getDescription() ?: '—') ?></td>
                            <td class="py-4 pr-4"><?= (int) $type->getOfferCount() ?></td>
                            <td class="py-4 pr-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold <?= $type->getStatus() === 'active' ? 'bg-success-50 text-success-700' : 'bg-gray-100 text-gray-500' ?>"><?= $type->getStatus() === 'active' ? 'Actif' : 'Inactif' ?></span></td>
                            <td class="py-4"><div class="flex gap-2">
                                <a href="<?= e(route('admin/offer-types', ['edit' => $type->getId()])) ?>" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 dark:border-gray-700 dark:text-gray-200">Modifier</a>
                                <form method="post" action="<?= e(route('admin/offer-types')) ?>" class="js-confirm-delete" data-label="<?= e($type->getName()) ?>">
                                    <?= csrf_input() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="type_id" value="<?= $type->getId() ?>">
                                    <button type="submit" class="rounded-lg bg-error-50 px-3 py-1.5 text-xs font-semibold text-error-700">Supprimer</button>
                                </form>
                            </div></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">Règle métier : un type rattaché à une offre ne peut pas être supprimé. Un type avec des offres publiées ne peut pas être désactivé.</p>
    </section>
</div>
