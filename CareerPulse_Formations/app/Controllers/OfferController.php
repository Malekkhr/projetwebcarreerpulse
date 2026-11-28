<?php
declare(strict_types=1);

namespace CareerPulse\Controllers;

/** Consultation publique et filtres du catalogue des offres. */
final class OfferController extends DatabaseController
{
    use OfferQueries;

    private const WORK_MODES = ['Sur site', 'Hybride', 'Télétravail'];

    public function index(): void
    {
        $offerId = (int) ($_GET['id'] ?? 0);
        $types = $this->fetchOfferTypes(true);

        if ($offerId > 0) {
            $offer = $this->findOffer($offerId, true);
            if ($offer === null) {
                $this->notFound();
                return;
            }
            $this->render('front', 'offers', $offer->getTitle() . ' — Offre CareerPulse', 'offers', [
                'selectedOffer' => $offer,
                'offers' => [],
                'offerTypes' => $types,
                'search' => '',
                'selectedType' => 0,
                'selectedWorkMode' => '',
            ]);
            return;
        }

        $search = trim((string) ($_GET['q'] ?? ''));
        $search = mb_substr($search, 0, 100);
        $typeId = max(0, (int) ($_GET['type_id'] ?? 0));
        $workMode = (string) ($_GET['work_mode'] ?? '');
        if (!in_array($workMode, self::WORK_MODES, true)) {
            $workMode = '';
        }

        $this->render('front', 'offers', 'Offres d’emploi et de stage', 'offers', [
            'selectedOffer' => null,
            'offers' => $this->fetchOffers(['q' => $search, 'type_id' => $typeId, 'work_mode' => $workMode], true),
            'offerTypes' => $types,
            'search' => $search,
            'selectedType' => $typeId,
            'selectedWorkMode' => $workMode,
        ]);
    }
}
