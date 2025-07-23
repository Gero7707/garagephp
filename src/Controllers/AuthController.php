<?php
namespace App\Controllers;

use App\Models\User;
use App\Security\Validator;
use App\Security\TokenManager;
use App\Utils\Logger;

/**
 * Cette classe gère les actions liées à l'authentification et à l'inscription des utilisateurs
 */

class AuthController extends BaseController{
    
    //Attributs
    private User $userModel;
    private TokenManager $tokenManager;
    private Logger $logger;

    /**
     * Constructeur est apellé à chaque création d'un objet AuthController, 
     * on en profite pour instancier les modèles dont on aura besoin

     * */    
    public function __construct(){

        parent::__construct();
        $this->userModel = new User();
        $this->tokenManager = new TokenManager();
        $this->logger = new Logger();
    }


    /**
     * Métode qui affiche la page avec le formulaire de connexion
     */
    public function showLogin():void{

        $this->render('auth/login',[
            'title' =>'Connexion',
            'csrf_token'=> $this->tokenManager->generateCsrfToken()
        ]);
        
    }

    public function login():void{

        //On s'assure que la requète est de type POST
        if($_SERVER['REQUEST8METHOD'] !== 'POST'){

            $this->response->redirect('/logon');
        }

        $data = $this->getPostData();

        //Validation du jeton CSRF
        if(!$this->tokenManager->validateCsrfToken($data['csrf_token'] ?? '')){
            $this->>response->error('Token de sécurité invalide' , 403);
        }

        //Le modele user s'occupe de la logique d'authentification
        $user = $this->userModel->authenticate($data['email'], $data['password']);
        if($user){

            //Si l'authentification réussi, on stoque les informations en session
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_role'] = $user->getRole();
            $_SESSION['user_username'] = $user->getUsername();

            //Redirection vers le taleau de bord
            $this->response->redirect('/cars');
        }else{

            //Si l authentification échoue , on réaffiche le formulaire avec un message d'erreur
            $this->render('auth/login', [
                'title'=> 'Connexion',
                'error'=> 'Email ou mot de passe incorrect.',
                'old'=>['email' => $data['email']],
                'crsf_token'=>$this->tokenManager->generateCsrfToken()
            ]);
        }
    }


}