<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Form\NewsletterType;
use App\Security\EmailVerifier;
use App\Security\AppAuthenticator;
use Symfony\Component\Mime\Address;
use App\Repository\NewsletterRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Doctrine\Migrations\Configuration\EntityManager\ManagerRegistryEntityManager;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;

class NewsletterController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/newsletter", name="app_newsletter")
     */
    public function index(ManagerRegistry $doctrine ,Newsletter $newsletter = null, Request $request, MailerInterface $mailerInterface): Response
    {
        // crée le formulaire de type newsletter 
        $newsletter = new Newsletter();
        $form = $this->createForm(NewsletterType::class , $newsletter );
        $form->handleRequest($request);

        // si envoye et sanitise avec les filter etc protection faille xss puis on execute le tout 
        if($form->isSubmitted() && $form->isValid())
        {
            $newsletter = $form->getData();
            $newsletter->setIsRegister(true);    
            // hydrate et protection faille sql
            $entityManager = $doctrine->getManager();
            $entityManager->persist($newsletter);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $email = (new TemplatedEmail())
                    ->from(new Address('Om-nada-braham@exemple.com', 'admin-yoga'))
                    ->to($newsletter->getEmail())
                    ->subject('S\'il vous plais comfirmer votre Email')
                    ->htmlTemplate('newsletter/confirmation_email.html.twig')
            ;
            $this->addFlash('success', 'Ton Compte a bien été enreigstrer, vérifie ton email pour confirmer la souscription a la newsletter!');
            $mailerInterface->send($email);
            
            return $this->render('newsletter/index.html.twig');
        }

        return $this->render('newsletter/registrationNewsletter.html.twig', [
            'formNewsletter' => $form->createView(),
        ]);
    }

    public function sendMailToClient(){

    }


    /**
     * @Route("/newsletter/desinscription", name="delete_newsletter")
     */
    public function desinscription(ManagerRegistry $doctrine ,Newsletter $newsletter =null, Request $request, MailerInterface $mailerInterface): Response
    {
        // crée le formulaire de type newsletter 
        
        $form = $this->createForm(NewsletterType::class , $newsletter );
        $form->handleRequest($request);
        // si envoye et sanitise avec les filter etc protection faille xss puis on execute le tout 
        if($form->isSubmitted() && $form->isValid())
        {
            $newsletter = $form->getData();
            $emailVerifier = $doctrine->getRepository(Newsletter::class)->findOneBy(["email" => $newsletter->getEmail()]);
            if($emailVerifier)
            {
                // hydrate et protection faille sql
                $entityManager = $doctrine->getManager();
                $entityManager->remove($emailVerifier);
                $entityManager->flush();
    
                // generate a signed url and email it to the user
                $email = (new TemplatedEmail())
                        ->from(new Address('Om-nada-braham@exemple.com', 'admin-yoga'))
                        ->to($newsletter->getEmail())
                        ->subject('Vous avez bien été désabonné(e)')
                        ->htmlTemplate('newsletter/delete_email.html.twig')
                ;
                $this->addFlash('success', 'Ton Compte a bien été supprimer de la newsletter!');
                $mailerInterface->send($email);
                
                return $this->redirectToRoute('app_home');
            }else{
                $this->addFlash('error', 'Aucun Email correspondant !! ');
            }
             
         }
 
         return $this->render('newsletter/desinscription.html.twig', [
             'formNewsletterDelete' => $form->createView(),
         ]);
     }

         /**
     * @Route("/newsletter/prepare", name="app_prepare")
     */
    public function prepare(ManagerRegistry $doctrine ,Newsletter $newsletter =null, Request $request, MailerInterface $mailerInterface): Response
    {
    
    }
   
}
