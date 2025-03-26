<?php
class HomeController extends Controller {
    public function index() {
        // Cargar el modelo si es necesario
        //$userModel = $this->loadModel('User');
        
        // Pasar datos a la vista
        $this->loadView('home');
    }
}
?>
