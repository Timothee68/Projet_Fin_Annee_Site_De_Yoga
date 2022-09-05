<?php

namespace App\Controller;

use App\Entity\User;

use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
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
