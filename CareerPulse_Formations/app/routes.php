<?php
declare(strict_types=1);

use CareerPulse\Controllers\Admin;
use CareerPulse\Controllers\AuthController;
use CareerPulse\Controllers\CourseController;
use CareerPulse\Controllers\HomeController;
use CareerPulse\Controllers\OfferController;
use CareerPulse\Controllers\ProfileController;

/**
 * Table de routage : index.php?action=<clé> => [Contrôleur, méthode].
 */
return [
    // ---------------- FRONTOFFICE ----------------
    // Public
    'home'     => [HomeController::class, 'index'],
    'courses'  => [CourseController::class, 'index'],
    'offers'   => [OfferController::class, 'index'],
    'register' => [AuthController::class, 'registerPage'],
    'login'    => [AuthController::class, 'loginPage'],
    // Réservé aux utilisateurs connectés (requireAuth)
    'logout'    => [AuthController::class, 'logout'],
    'dashboard' => [AuthController::class, 'dashboard'],
    'profile'   => [ProfileController::class, 'index'],

    // ---------------- BACKOFFICE (rôle admin uniquement : requireRole('admin')) ----------------
    'admin'            => [Admin\DashboardController::class, 'index'],
    'admin/dashboard'  => [Admin\DashboardController::class, 'index'],
    'admin/courses'    => [Admin\CourseController::class, 'index'],
    'admin/offers'      => [Admin\OfferController::class, 'index'],
    'admin/offer-types' => [Admin\OfferTypeController::class, 'index'],
    'admin/categories' => [Admin\CategoryController::class, 'index'],
    // CRUD utilisateurs + rôles (écritures : POST + CSRF uniquement)
    'admin/users'        => [Admin\UserController::class, 'index'],
    'admin/users/show'   => [Admin\UserController::class, 'show'],
    'admin/users/create' => [Admin\UserController::class, 'create'],
    'admin/users/edit'   => [Admin\UserController::class, 'edit'],
    'admin/users/delete' => [Admin\UserController::class, 'delete'],
    'admin/users/role'   => [Admin\UserController::class, 'role'],
    'admin/users/status' => [Admin\UserController::class, 'status'],
];
