<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Reply;
use App\Form\PostType;
use App\Entity\Benefit;
use App\Form\ReplyType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Entity\ImgCollectionBenefit;
use App\Repository\ImgCollectionBenefitRepository;

class HomeController extends AbstractController
{

    /**
     * @Route("/home", name="app_home")
     */
    public function index(ManagerRegistry $doctrine, Post $post = null, Request $request): Response 
    {
        $post =new Post();
        $form = $this->createForm(PostType::class,$post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $post = $form->getData();
            $post->setUser($this->getUser());
            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash("success" , "le commentaires à été ajouté/Modifié avec succès");

            return $this->redirectToRoute('app_home'); 
        }

            $blogs = $doctrine->getRepository(Blog::class)->findBy([] , ["publicationDate" => "DESC"] , 4);
            $benefits = $doctrine->getRepository(Benefit::class)->findBy([] , ['title' => 'DESC']);
            $posts = $doctrine->getRepository(Post::class)->findBy([] , ['creationDate' => 'DESC']);
            return $this->render('home/index.html.twig', [
                'benefits' => $benefits,
                'posts' => $posts,
                'blogs' => $blogs,
                'formAddPost' =>  $form->createView(),
                'edit' => 'null',
            ]);
        }

    /**
     * @Route("/home/editPost/{id}", name="edit_post")
     */
    public function editPost(ManagerRegistry $doctrine, Post $post = null, Request $request): Response 
    {
        if(!$post){
            $post =new Post();
        }
        $form = $this->createForm(PostType::class,$post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $post = $form->getData();
            $post->setUser($this->getUser());
            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash("success" , "le commentaires à été ajouté/Modifié avec succès");

            return $this->redirectToRoute('app_home'); 
        }

            $blogs = $doctrine->getRepository(Blog::class)->findBy([] , ["publicationDate" => "DESC"] , 4);
            $benefits = $doctrine->getRepository(Benefit::class)->findBy([] , ['title' => 'DESC']);
            $posts = $doctrine->getRepository(Post::class)->findBy([] , ['creationDate' => 'DESC']);
            return $this->render('home/index.html.twig', [
                'benefits' => $benefits,
                'posts' => $posts,
                'blogs' => $blogs,
                'formAddPost' =>  $form->createView(),
                'edit' => 'edit',
            ]);
        }

    /**
     * fonction pour afficher le com du livre d'or
     * @Route("/admin/home/reply", name="app_reply")
     */
    public function showReply(ManagerRegistry $doctrine, Post $post=null, Request $request): Response 
    {
        $posts = $doctrine->getRepository(Post::class)->findBy([] , ['creationDate' => 'DESC']);
        return $this->render('management/reply.html.twig', [
            'posts' => $posts,   
        ]);
    }     

    /**
     * fonction pour répondre le com du livre d'or
     * @Route("/admin/home/reply/{id}", name="add_reply")
     */
    public function replyPost(ManagerRegistry $doctrine, Post $post , Reply $reply=null, Request $request): Response 
    {
        $reply =new Reply();
        $formReply = $this->createForm(ReplyType::class,$reply);
        $formReply->handleRequest($request);
        if($formReply->isSubmitted() && $formReply->isValid())
        {
            $reply = $formReply->getData();
            $reply->setSender($this->getUser());
            $reply->setRecipient($post);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($reply);
            $entityManager->flush();

            $this->addFlash("success" , "le commentaires à été ajouté/Modifié avec succès");
            return $this->redirectToRoute('app_reply'); 
        }
        return $this->render('management/addReply.html.twig', [
            'formAddReply' =>  $formReply->createView(),
            'post' => $post,
        ]);
    }     


    /**
     * @Route("/home/mentionLegal", name="app_mention")
     */       
    public function detailMention(): Response
    {
        return $this->render('home/mentionLegal.html.twig');
    }
    /**
     * @Route("/home/detail/{id}", name="detail_benefit")
     */
    public function detailBenefit(Benefit $benefit , ImgCollectionBenefitRepository $img): Response
    {
        // requête DQL pour trouver les imgs par l'id du benefit 
        $images = $img->findById($benefit->getId());
        return $this->render('home/detailbenefit.html.twig', [
            'benefit' => $benefit,
            'images' => $images,
        ]);
    }
    /**
    * @Route("/home/delete_post/{id}", name="delete_post")
    */
    public function deleteblog(ManagerRegistry $doctrine, Post $post) :Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($post);
        $entityManager->flush();
        $this->addFlash("success" , "le message à été supprimé avec succès");
        return $this->redirectToRoute("app_home");
    }
}
