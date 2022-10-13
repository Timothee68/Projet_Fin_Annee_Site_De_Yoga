<?php

namespace App\Controller;

use App\Entity\User;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user/{id}", name="app_user")
     */
    public function index(User $user): Response
    {
        return $this->render('user/index.html.twig', [
        ]);
    }

    /**
     * @Route("/presentation", name="app_presentation")
     */
    public function presentation(): Response
    {
        return $this->render('user/presentation.html.twig', [
        ]);
    }
    /**
    * @Route("/user/edit/{id}", name="edit_register")
    */
    public function editRegister(Request $request, UserPasswordHasherInterface $userPasswordHasher, User $user, EntityManagerInterface $entityManager): Response
    {
        if(!$user){
            $user = new User();
        }
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imgFile = $form->get('img')->getData();
            if ($imgFile) {
                $newFilename = 'img/imported/'.uniqid().'.'.$imgFile->guessExtension();
                try {
                    $imgFile->move(
                        $this->getParameter('img_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $user->setImg($newFilename);
            }
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
            $this->addFlash('success', 'Ton Compte a bien été modifié !');
            return $this->redirectToRoute('app_user' ,['id' => $user->getId()] ); 
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'edit' => $user->getId(),
        ]);
    }
   
    /**
    * @Route("/contact", name="app_contact")
    */
    public function Contact(ManagerRegistry $doctrine , Contact $contact = null ,MailerInterface $mailer, Request $request): Response
    {
        $contact =new Contact();

        $form = $this->createForm(ContactType::class , $contact );
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $contact = $form->getData();           
            $entityManager = $doctrine->getManager();
            $entityManager->persist($contact);
            $entityManager->flush();

            $email = $contact->getEmail();
            $message = $contact->getMessageContent();

            $emails = (new TemplatedEmail())
            ->from(new Address( $email))
            ->to('Om-nada-braham@exemple.com')
            ->subject('Mail contact Site Om nada Braham')
            ->text($message)
            ;
            $mailer->send($emails);

            $this->addFlash("success" ,"Message envoyez avec succès");

            return $this->redirectToRoute('app_contact'); 
        }
        return $this->render('contact/index.html.twig', [
            'formContact' =>  $form->createView(),
          
        ]);
    }
        /**
     * @Route("/profil/{id}", name="app_delete")
     */
    public function delete(ManagerRegistry $doctrine, User $user) :Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash("success" , "Compte à été supprimé avec succès");
        return $this->redirectToRoute("app_home");
    }
}