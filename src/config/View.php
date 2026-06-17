<?php

namespace Andi\ProjectWebPeche\Config;

// ============================================================
// src/Config/View.php
// Petite classe qui charge un fichier de vue depuis src/views/
// ============================================================

class View
{
    /**
     * Affiche une vue.
     *
     * @param string $name Nom du fichier de vue, sans ".php" (ex: "home")
     * @param array  $data Variables à transmettre à la vue (optionnel)
     */
    public function render(string $name, array $data = []): void
    {
        // Transforme les clés du tableau $data en variables PHP
        // ex: ['title' => 'Accueil'] devient $title dans la vue
        extract($data);

        $viewFile = __DIR__ . '/../views/' . $name . '.php';

        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "Erreur : la vue « {$name} » est introuvable ({$viewFile}).";
            return;
        }

        require $viewFile;
    }
}