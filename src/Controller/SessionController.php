<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Stage;
use App\Entity\Benefit;
use App\Entity\Session;
use App\Form\StageType;
use App\Entity\Category;
use App\Form\SessionType;
use App\Entity\Reservation;
use App\Form\ReservationType;
use Symfony\Component\Mime\Address;
use App\Repository\SessionRepository;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\ManagerRegistry as DoctrineManagerRegistry;

class SessionController extends AbstractController
{    
    /**
     * @Route("/session", name="app_session")
     */
    public function index(ManagerRegistry $doctrine , Request $request):Response
    {   
        // je récupère avec le request les valeurs dans le fichier twig id catégorie et id préstation au recahrgement de la page on récupère les valeurs
        $idCategory = $request->get("category");
        $idBenefit = $request->get("benefit");
        // si j'ai l'idBenefit 
        if($idBenefit){
            // on recherche avec la requête dql préparer les sessions liée a cet id 
            $sessionsById = $doctrine->getRepository(Session::class)->foundSession($idBenefit);
        }

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
                'nbPlaceMax' => $event->getNbPlaceMax(),
                'backgroundColor' => $event->getBackgroundColor(),

            ];
        }
        // on encode $data en format Json 
        $datas=json_encode($rdvs);
        $benefits = $doctrine->getRepository(Benefit::class)->findAll();
        $sessions = $doctrine->getRepository(Session::class)->findBy([], ['endTime' => "DESC"]);
        $categorys = $doctrine->getRepository(Category::class)->findAll();

        return $this->render('session/index.html.twig', [
           'benefits' => $benefits,
           'sessions' => $sessions,
           'datas' =>  $datas,
           'categorys'=> $categorys,
           "sessionsById" => $sessionsById ?? null,  //  ??  null   => renvoie null si sessionsById n'est pas définis 
           "idCategory" => $idCategory,
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

                'nbPlaceMax' => $event->getNbPlaceMax(),
                'backgroundColor' => $event->getBackgroundColor(),    
                                       
            ];
        }
        // on encode $data en format Json 
        $datas=json_encode($rdvs);
        $sessions = $doctrine->getRepository(Session::class)->findBy([], ['startTime' => 'DESC']);
        return $this->render('session/event.html.twig' , [ 
            'sessions' => $sessions,
            'datas' =>  $datas,
        ]);
    }

    

     /**
     * @Route("/admin/calendar/{id}/edit", name="api_event_edit", methods={"PUT"})  // on le block sur une méthode PUT on ne peut donc pas l'ouvrir il y auras un 405 erreur 
     */
    // on doit pouvoir mettre a jour ou crée un évenement si il n'existe pas 
    public function majEvent(?Session $session ,ManagerRegistry $doctrine, Request $request): Response   // le point d'interogation signifie que qu'on peut potentiellement passez un Id qui n'existe pas l'inconveniant est qu'il faut récuperer l'intégralité des données
    {
        // on récupère  les données envoyer par fullCalendar
        $donnees = json_decode($request->getContent());
       
        if(   
            isset($donnees->title) && !empty($donnees->title) &&     
            isset($donnees->start) && !empty($donnees->start) &&
            isset($donnees->end) && !empty($donnees->end) &&
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
                $session->getBenefit();
                $session->setStartTime(new DateTime($donnees->start));
                $session->setEndTime(new DateTime($donnees->end));
                $session->setNbPlaceMax($donnees->nbPlaceMax);
                $session->setBackgroundColor($donnees->backgroundColor);

                $entityManager = $doctrine->getManager();
                $entityManager->persist($session);
                $entityManager->flush();

                // return new Response('ok', $code);
                return $this->render('session/event.html.twig', [
                    'controller_name' => 'ApiController',
                ]);

            }else{
                    //soit les données sont incomplètes
                    return new Response('données incomplètes', 404);  // on crée une reponse et l'envoie sur une page 404
            }
    }

   
    /**
     * fonction réserver une stage de prestation
     * @Route("/session/reservation/{idSession}/{idUser}" , name="app_reservation" )
     * @ParamConverter("session", options={"id" = "idSession"})
     * @ParamConverter("user", options={"id" = "idUser"})\_profiler\open
     */
    public function reservation(ManagerRegistry $doctrine,Reservation  $reservation = null ,Session $session, User $user, Request $request ) : Response
    {
            if(isset($_POST['submit']))
            {
                //on récupère les info du formulaire avec la méthode request
                $nbPlaceMax = $request->request->get("nbPlaceMax");
                $name = $request->request->get("name");
                $firstName = $request->request->get("firstName");
                $telephone = $request->request->get("telephone");
                // on set les informations 
                $reservation = new Reservation();
                $reservation->setSession($session);
                $reservation->setUser($user);
                $reservation->setNbPlaces($nbPlaceMax);
                $reservation->setFirstName($firstName);
                $reservation->setName($name);
                $reservation->setTelephone($telephone);
                //on les rentre en base de donnée
                $entityManager = $doctrine->getManager();
                $entityManager->persist($reservation);
                $entityManager->flush();
            
            }
            $this->addFlash("success" , $reservation->getSession()->getBenefit()->getTitle()." à été reserver avec succès");
        return $this->redirectToRoute('app_showReservation', ['id' => $user->getId()]); 
    }
    

    /**
     * fonction réserver une stage de prestation
     * @Route("/session/reservation-stage/{idstage}/{idUser}" , name="app_reservation_stage" )
     * @ParamConverter("stage", options={"id" = "idstage"})
     * @ParamConverter("user", options={"id" = "idUser"})
     */
    public function stageReservation(ManagerRegistry $doctrine,Reservation  $reservation = null ,stage $stage, User $user, Request $request ) : Response
    {
            if(isset($_POST['submit']))
            {
                $nbPlaceMax = $request->request->get("nbPlaceMax");
                $name = $request->request->get("name");
                $firstName = $request->request->get("firstName");
                $telephone = $request->request->get("telephone");
                // on set les informations 
                $reservation = new Reservation();
                $reservation->setstage($stage);
                $reservation->setUser($user);
                $reservation->setNbPlaces($nbPlaceMax);
                $reservation->setFirstName($firstName);
                $reservation->setName($name);
                $reservation->setTelephone($telephone);
                //on les rentre en base de donnée
                $entityManager = $doctrine->getManager();
                $entityManager->persist($reservation);
                $entityManager->flush();
            
            }
            $this->addFlash("success" , $stage->getTitle()." à été reserver avec succès");
        return $this->redirectToRoute('app_showReservation', ['id' => $user->getId()]); 
    }

     /**
     * fonction pour voir et gerer ses reservations
     * @Route("/session/showReservation/{id}" , name="app_showReservation" )
     */
    public function showReservation(ManagerRegistry $doctrine , User $user ,Request $request) : Response
    {
        $user = $request->get("id");
        $events = $doctrine->getRepository(Reservation::class)->findBy(['user' => $user]); 
        // $events = $doctrine->getRepository(Reservation::class)->foundReservation(['id' => $user]); 
        // on boucle sur les évenements qui récupère toutes les sessions 
       
        foreach($events as $event){
                $session = $event->getSession();
                $stage = $event->getStage();
                if(isset( $session ))
                {
                    // $rdv seras un tableau associatifs pour le transformer en donnée json par la suite 
                    $rdvs[] = [
                        // on récupère toutes les infos d'un sessions  une par une 
                        'id' =>  $event->getId(),
                        'title' => $event->getSession()->getBenefit()->getTitle(),
                        'start' => $event->getSession()->getStartTime()->format('Y-m-d H:i:s'),
                        'end' => $event->getSession()->getEndTime()->format('Y-m-d H:i:s'),
                        // 'nbPlaceMax' => $event->getSession()->getNbPlaceMax(),
                        'backgroundColor' => $event->getSession()->getBackgroundColor(),
                    ];
                }

                if(isset( $stage ))
                {
                    // $rdv seras un tableau associatifs pour le transformer en donnée json par la suite 
                    $rdvs[] = [
                        // on récupère toutes les infos d'un sessions  une par une 
                        'id' =>  $event->getId(),
                        'title' => $event->getStage()->getTitle(),
                        'start' => $event->getStage()->getStartTime()->format('Y-m-d H:i:s'),
                        'end' => $event->getStage()->getEndTime()->format('Y-m-d H:i:s'),
                        // 'nbPlaceMax' => $event->getStage()->getNbPlaceMax(),
                        'backgroundColor' => $event->getStage()->getBackgroundColor(),
                    ];
                }
        }
        // on encode $data en format Json 
        $datas = json_encode($rdvs?? null) ;
        return $this->render('user/reservation.html.twig', [
            'datas' =>  $datas ?? null,
            "events" => $events,
        ]);
    }

    /**
     * fonction pour ajouter une session d'une préstation
     * @Route("session/stage" , name="app_stage" )
     */ 
    public function showStage(ManagerRegistry $doctrine, Stage $stage=null) :Response
    {
        $stages = $doctrine->getRepository(Stage::class)->findAll();
        return $this->render('stage/index.html.twig', [
            "stages" => $stages,
        ]);
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
     * @Route("/reservation/edit/{id}/{id_user}" , name="edit_reservation" )
     * @ParamConverter("reservation", options={"id" = "id"})
     * @ParamConverter("user", options={"id" = "id_user"})
     */
    public function editReservation(ManagerRegistry $doctrine,Reservation $reservation = null, Request $request , User $user=null): Response
    {

        // si le film existe pas on crée un nouvelle objet sinon on modifie 
        if(!$reservation)
        {
            $reservation =new Reservation();
        }
        // crée le formulaire de type reservation 
        $form = $this->createForm(ReservationType::class , $reservation );
        $form->handleRequest($request);
        // si envoye et sanitise avec les filter etc protection faille xss puis on execute le tout 
        if($form->isSubmitted() && $form->isValid())
        {
            $reservation = $form->getData();           
            $entityManager = $doctrine->getManager();
            // hydrate et protection faille sql 
            $entityManager->persist($reservation);
            $entityManager->flush();
            $this->addFlash("success" , "la réservation à été Modifié avec succès");
            return $this->redirectToRoute('app_showReservation', ['id' => $user->getId()]); 
        }
        return $this->render('user/editReservation.html.twig', [
            'formEditReservation' =>  $form->createView(),
            'edit' => $reservation->getId(),
            "reservation" => $reservation,
        ]); 
    }

    /**
     * fonction pour ajouter une session d'une préstation
     * @Route("/session/delete/{id}/{id_user}" , name="delete_reservation" )
     * @ParamConverter("reservation", options={"id" = "id"})
     * @ParamConverter("user", options={"id" = "id_user"})
     */
    public function deleteReservation(ManagerRegistry $doctrine, Reservation $reservation, Request $request,MailerInterface $mailer, User $user) :Response
    {

        $user = $request->get("id_user");
        $userFound = $doctrine->getRepository(User::class)->findOneBy(["id" => $user]);
      
        $email= $userFound->getEmail();

        $entityManager = $doctrine->getManager();
        $entityManager->remove($reservation);
        $entityManager->flush();
        $this->addFlash("success" , "Réservation annulé avec succès");
        //  L'IDEE EST DE SUPPRIMER LE RDV ET ENVOYE UN MAIL A L ADMIN
        if($reservation->getSession() != null && $reservation->getStage() == null )
        {
            $emails = (new TemplatedEmail())
            ->from(new Address( $email))
            ->to('Om-nada-braham@exemple.com')
            ->subject('Annulation du rdv'.$reservation->getId()." ".$reservation->getSession()->getBenefit()->getTitle()."." )
            ->htmlTemplate('session/annulation.html.twig')
            ;
        }else if ($reservation->getSession() == null && $reservation->getStage() != null) {
                $emails = (new TemplatedEmail())
                ->from(new Address( $email))
                ->to('Om-nada-braham@exemple.com')                                
                ->htmlTemplate('session/annulation.html.twig')
                ->subject('Annulation du rdv'.$reservation->getId()." ". $reservation->getStage()->getTitle()."." )
                ;
        }
        $mailer->send($emails);
        return $this->redirectToRoute('app_showReservation', ['id' => $user]);
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