<?php
namespace App\Controllers;
use App\Security\Validator;
use App\Utils\Response;


/**
 * Controleur de base 
 * Toutes les autres classes de controleur hériteront de celle ci
 */

abstract class BaseController{

    protected Response $response;
    protected Validator $validator;

    public function __construct(){
        $this->response = new Response();
        $this->validator = new Validator();
    }


    /**
     * 
     * Affiche une vue en l'injectant dans le layout principal
     * @param string $view  le nom du fichier de vue
     * @param array  $data les données à rendre accessibles dans la vue 
     * 
     */
    protected function render(string $view, array $data = []):void{

        //On construit le chemin complet vers le fichier de vue
        $viewPath = __DIR__ .'/views/' . $view . '.php';

        //On vérifie que le fichier de vue existe bien
        if(!file_exists($viewPath)){
            $this->response->error("Vue non trouvée : $viewPath" , 500);
            return;
        }

        //Extract transforme les clés d'un tableau en variables 
        //Exemple : $data = ['title' => 'Accueil'] devient $title = 'Acueil'
        extract($data);

        //On utilise la mise en tampon de sortie (output buffering) pour le HTML de la vue
        ob_start();
        include $viewPath;

        //Ici on vide le cache la variable $content contient la vue 
        $content = ob_get_clean();

        //Finalement , on inclut le layout principal, qui peut maintenant utiliser la variable $content
        include __DIR__ .'/views/layout.php';
    }

    /**
     * Récupère et nettoie les données envoyées via une requete POST
     */

    protected function getPostData():array{

        return $this->validator->sanitize($_POST);
    }

    /**
     * Verifie si l'utilisateur est connecté sinon le redirige vers la page login
     */

    protected function requireAuth():void{

        if(!isset($_SESSION['user_id'])){
            $this->response->redirect('/login');
        }
    }
}Summary of render