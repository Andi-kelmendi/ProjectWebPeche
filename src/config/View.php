<?php

namespace Andi\ProjectWebPeche\Config;

class View {

    public function render(string $view, array $data = []): void {
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "View not found: {$viewFile}";
            return;
        }

        extract($data, EXTR_SKIP);
        include $viewFile;
    }

}
