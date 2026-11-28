<?php
$isLoggedIn = !empty($_SESSION['user_id']);
$isAdmin = $isLoggedIn && ($_SESSION['role'] ?? '') === 'admin';
?>
<nav class="cp-nav" aria-label="Navigation principale">
    <a href="<?= e(route('home')) ?>" class="cp-brand">
        <span class="cp-brand-mark" aria-hidden="true">C</span>
        <span class="cp-brand-name">Career<span>Pulse</span></span>
    </a>
    <button class="cp-nav-toggle" type="button" data-cp-toggle="nav" aria-expanded="false" aria-controls="navbarCollapse" aria-label="Ouvrir le menu">
        <i class="fa fa-bars" aria-hidden="true"></i>
    </button>
    <div class="cp-nav-menu" id="navbarCollapse">
        <div class="cp-nav-links">
            <a href="<?= e(route('home')) ?>" class="cp-nav-link <?= $currentPage === 'home' ? 'active' : '' ?>">Accueil</a>
            <a href="<?= e(route('courses')) ?>" class="cp-nav-link <?= $currentPage === 'courses' ? 'active' : '' ?>">Formations &amp; Catégories</a>
            <a href="<?= e(route('offers')) ?>" class="cp-nav-link <?= $currentPage === 'offers' ? 'active' : '' ?>">Offres &amp; Stages</a>
            <?php if ($isLoggedIn): ?>
                <a href="<?= e(route('dashboard')) ?>" class="cp-nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">Mon espace</a>
                <a href="<?= e(route('profile')) ?>" class="cp-nav-link <?= $currentPage === 'profile' ? 'active' : '' ?>">Mon profil</a>
                <?php if ($isAdmin): ?>
                    <a href="<?= e(route('admin')) ?>" class="cp-nav-link">Back-office</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="<?= e(route('register')) ?>" class="cp-nav-link <?= $currentPage === 'register' ? 'active' : '' ?>">Inscription</a>
            <?php endif; ?>
        </div>
        <div class="cp-nav-actions">
            <?php if ($isLoggedIn): ?>
                <span class="cp-nav-user"><i class="fa fa-user" aria-hidden="true"></i> <?= e($_SESSION['username'] ?? '') ?></span>
                <form method="post" action="<?= e(route('logout')) ?>" class="cp-inline-form"><?= csrf_input() ?><button type="submit" class="cp-btn cp-btn--primary">Déconnexion</button></form>
            <?php else: ?>
                <a href="<?= e(route('login')) ?>" class="cp-btn cp-btn--primary">Connexion <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
            <?php endif; ?>
        </div>
    </div>
</nav>
