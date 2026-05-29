<?php
// ============================================================
// src/controllers/HomeController.php
// ============================================================

class HomeController {

    // Appelé quand on visite "/"
    public function index(): void {
        // Charge et affiche la vue home.php
        require_once __DIR__ . '/../views/home.php';
    }
}