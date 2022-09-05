<?php

namespace App\Controller;

use DateTime;
use App\Entity\Benefit;
use App\Entity\Session;
use App\Form\SessionType;
use App\Repository\SessionRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SessionController extends AbstractController
{
    /**
     * @Route("/session", name="app_session")
     */
    public function index(ManagerRegistry $doctrine , Benefit $benefit= null ,SessionRepository $sessionRepository ,Session $session= null, Request $request):Response
    {   
        $benefits = $doctrine->getRepository(Benefit::class)->findAll();
        $sessions = $doctrine->getRepository(Session::class)->findAll();
        return $this->render('session/index.html.twig', [
           'benefits' => $benefits,
           'sessions' => $sessions,
        ]);
    }

    /**
     * @Route("/session/show", name="show_event")
    */
    public function showEvent(ManagerRegistry $doctrine ):Response
    {
        $events = $doctrine->getRepository(Session::class)->findAll();
        // on boucle sur les évenements qui récupère toutes les sessions 
        foreach($events as $event){
            // $rdv seras un tableau associatifs pour le transformer en donnée json par la suite 
            $rdvs[] = [
                // on récupère toutes les infos d'un sessions  une par une 
                'id' =>  $event->getId(),
                'title' => $event->getBenefit()->getTitle(),
                'start' => $event->getStartTime()->format('Y-m-d H:i:s'),
                'end' => $event->getEndTime()->format('Y-m-d H:i:s'),
                'sessionDate' =>  $event->getSessionDate()->format('Y-m-d H:i:s'),
                'nbPlaceMax' => $event->getNbPlaceMax(),
                'backgroundColor' => $event->getBackgroundColor(),

            ];
        }
        // on encode $data en format Json 
        $data=json_encode($rdvs);
        $sessions = $doctrine->getRepository(Session::class)->findBy([], ['sessionDate' => 'ASC']);
        return $this->render('session/event.html.twig' , [ 
            'sessions' => $sessions,
            'data' =>  $data,
        ]);
    }

    

     /**
     * @Route("/admin/calendar/{id}/edit", name="api_event_edit",  methods={"PUT"})  // on le block sur une méthode PUT on ne peut donc pas l'ouvrir il y auras un 405 erreur 
     */
    // on doit pouvoir mettre a jour ou crée un évenement si il n'existe pas 
    public function majEvent(?Session $session ,ManagerRegistry $doctrine, Request $request): Response   // le point d'interogation signifie que qu'on peut potentiellement passez un Id qui n'existe pas l'inconveniant est qu'il faut récuperer l'intégralité des données
    {
        // on récupère  les données envoyer par fullCalendar
        $donnees = json_decode($request->getContent());
        if(
            isset($donnees->benefit) && !empty($donnees->benefit) &&     
            isset($donnees->startTime) && !empty($donnees->startTime) &&
            isset($donnees->endTime) && !empty($donnees->endTime) &&
            isset($donnees->sessionDate) && !empty($donnees->sessionDate) &&
            isset($donnees->nbPlaceMax) && !empty($donnees->nbPlaceMax) &&        
            isset($donnees->backgroundColor) && !empty($donnees->backgroundColor) 
            ){
                // soit les données sont complètes
                // on initialise un code qui va etre le 200 = mise a jour
                $code=200;
                //on vérifie si l'id existe
                if(!$session){
                    // on instancie un rdv 
                    $session = new Session();
                    // on change le code 201 qui = a created
                    $code=201;
                }
                // on hydrate l'objet avec les données
                $session->setBenefit($donnees->benefit);
                $session->setStartTime(new DateTime($donnees->startTime));
                $session->setEndTime(new DateTime($donnees->endTime));
                $session->setSessionDate(new DateTime($donnees->sessionDate));
                $session->setNbPlaceMax($donnees->nbPlaceMax);
                $session->setBackgroundColor($donnees->backgroundColor);

                $entityManager = $doctrine->getManager();
                $entityManager->persist($session);
                $entityManager->flush();

                // return new Response('ok', $code);
                return $this->render('session/index.html.twig', [
                    'controller_name' => 'ApiController',
                ]);

            }else{
                    //soit les données sont incomplètes
                    return new Response('données incomplètes', 404);  // on crée une reponse et l'envoie sur une page 404
            }
    }


     /**
     * fonction pour ajouter une session d'une préstation
     * @Route("/admin/session/add" , name="add_session" )
     */
    public function addSession(ManagerRegistry $doctrine,Session $session = null, Request $request): Response
    {
        // si le film existe pas on crée un nouvelle objet sinon on modifie 
        $session =new Session();
        // crée le formulaire de type session 
        $form = $this->createForm(SessionType::class , $session );
        $form->handleRequest($request);
        // si envoye et sanitise avec les filter etc protection faille xss puis on execute le tout 
        if($form->isSubmitted() && $form->isValid())
        {
            $session = $form->getData();           
            $entityManager = $doctrine->getManager();
            // hydrate et protection faille sql 
            $entityManager->persist($session);
            $entityManager->flush();
            $this->addFlash("success" , " à été ajouté avec succès");
            return $this->redirectToRoute('show_event'); 
        }
        return $this->render('session/add.html.twig', [
            'formAddSession' =>  $form->createView(),
        ]);
    }


    /**
     * fonction pour ajouter une session d'une préstation
     * @Route("/admin/session/edit/{id}" , name="edit_session" )
     */
    public function editSession(ManagerRegistry $doctrine,Session $session = null, Request $request): Response
    {
        // si le film existe pas on crée un nouvelle objet sinon on modifie 
        if(!$session)
        {
            $session =new Session();
        }
        // crée le formulaire de type session 
        $form = $this->createForm(SessionType::class , $session );
        $form->handleRequest($request);
        // si envoye et sanitise avec les filter etc protection faille xss puis on execute le tout 
        if($form->isSubmitted() && $form->isValid())
        {
            $session = $form->getData();           
            $entityManager = $doctrine->getManager();
            // hydrate et protection faille sql 
            $entityManager->persist($session);
            $entityManager->flush();
            $this->addFlash("success" , " à été Modifié avec succès");
            return $this->redirectToRoute('show_event'); 
        }
        return $this->render('session/add.html.twig', [
            'formAddSession' =>  $form->createView(),
            'edit' => $session->getId(),
        ]); 
    }

    /**
     * fonction pour ajouter une session d'une préstation
     * @Route("/admin/session/delete/{id}" , name="delete_session" )
     */
    public function deleteSession(ManagerRegistry $doctrine, Session $session) :Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($session);
        $entityManager->flush();
        $this->addFlash("success" , "supprimé avec succès");
        return $this->redirectToRoute("show_event");
    }

}
