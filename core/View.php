<?php
class View {
    public function render($viewName, $data = array()) {
        // Incluir el header
        include('../app/views/templates/header.php');
        
        // Incluir la vista solicitada
        $viewFile = "../app/views/{$viewName}.php";
        if (file_exists($viewFile)) {
            extract($data);  // Extrae las variables de $data para que estén disponibles en la vista
            include($viewFile);
        } else {
            die("Vista no encontrada: {$viewFile}");
        }
        
        // Incluir el footer
        include('../app/views/templates/footer.php');
    }
}
