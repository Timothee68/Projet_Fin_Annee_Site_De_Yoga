<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Input;
use App\Form\BlogType;
use App\Entity\Benefit;
use App\Form\InputType;
use App\Entity\Category;
use App\Form\BenefitType;
use App\Form\CategoryType;
use App\Repository\InputRepository;
use App\Entity\ImgCollectionBenefit;
use App\Entity\Session;
use App\Repository\BenefitRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ManagementController extends AbstractController
{

    /**
     * fonction pour afficher les catégories des différentes préstations
     * @Route("/admin/management", name="app_management")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $categorys = $doctrine->getRepository(Category::class)->findAll();
        return $this->render('management/index.html.twig', [
            'categorys' => $categorys,
        ]);
    }

    
    /**
     * fonction pour afficher les bénéfices 
     * @Route("/admin/management/input", name="app_input")
     */
    public function showInput(ManagerRegistry $doctrine)
    {
        $inputs = $doctrine->getRepository(Input::class)->findAll();
        return $this->render('management/input.hmtl.twig', [
            'inputs' => $inputs,
        ]);
    }

    /**
     * fonction pour afficher les blogs
     * @Route("/admin/management/blog", name="show_blog")
     */
    public function showBlog(ManagerRegistry $doctrine):Response
    {
        $blogs = $doctrine->getRepository(Blog::class)->findBy([] , ["publicationDate" => "ASC"]);
        return $this->render('blog/index.html.twig', [
            'blogs' => $blogs,
        ]);
    }

      /**
     * fonction pour ajouter et/ou modifier un blog
     * @Route("/admin/management/blog/add", name="add_blog")
     * @Route("/admin/management/blog/edit/{id}" , name="edit_blog" )
     */
    public function addBlog(ManagerRegistry $doctrine , Blog $blog= null , Request $request):Response
    {
        if(!$blog){
            $blog =new Blog();
        }
        // crée le formulaire de type Blog 
        $form = $this->createForm(BlogType::class , $blog );
        $form->handleRequest($request);
        // si envoye et sanitise avec les filter etc protection faille xss puis on execute le tout 
        if($form->isSubmitted() && $form->isValid())
        {
            $imgFile = $form->get('image')->getData();
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
                $blog->setImage($newFilename);
            }
            $blog = $form->getData();           
            $entityManager = $doctrine->getManager();
            // hydrate et protection faille sql 
            $entityManager->persist($blog);
            $entityManager->flush();
            $this->addFlash("success" , $blog->getTitle()." à été ajouté/Modifié avec succès");
            return $this->redirectToRoute('show_blog'); 
        }
        return $this->render('management/addBlog.html.twig', [
            'formAddBlog' =>  $form->createView(),
            'edit' => $blog->getId(),
        ]);
    }

    /**
     * fonction pour ajouter et/ou modifier une catégorie
     * @Route("/admin/management/category/add", name="add_category")
     * @Route("/admin/management/category/edit/{id}" , name="edit_category" )
     */
    public function addCategory(ManagerRegistry $doctrine,Category $category = null, Request $request): Response
    {
        // si le film existe pas on crée un nouvelle objet sinon on modifie 
        if(!$category){
            $category =new Category();
        }
        // crée le formulaire de type category 
        $form = $this->createForm(CategoryType::class , $category );
        $form->handleRequest($request);
        // si envoye et sanitise avec les filter etc protection faille xss puis on execute le tout 
        if($form->isSubmitted() && $form->isValid())
        {
            $category = $form->getData();           
            $entityManager = $doctrine->getManager();
            // hydrate et protection faille sql 
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash("success" , $category->getCategoryName()." à été ajouté/Modifié avec succès");
            return $this->redirectToRoute('app_management'); 
        }
        return $this->render('management/addCategory.html.twig', [
            'formAddCategory' =>  $form->createView(),
            'edit' => $category->getId(),
        ]);
    }


    /**
     * function pour ajouter une préstation et l'ajouter à une catégorie précise par son id
     * @Route("/admin/management/addBenefit/{id}", name="add_benefit")
     */
    public function addBenefit(Category $category , ManagerRegistry $doctrine, Benefit $benefit = null, Request $request): Response
    {
        $benefit =new Benefit();
        $form = $this->createForm(BenefitType::class,$benefit);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            // gestionn de l'image de la préstation en elle meme
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
                $benefit->setImg($newFilename);
            }
             // on récuperes les images transmises pour la gallerie d'image de la préstation
            $imgMultiple = $form->get('imagCollectionBenefits')->getData();
            // dd($imgMultiple);
             // $alt = $form->get('alt')->getData();
            // on boucle sur les images
            foreach ( $imgMultiple as $image) {
                // on génére un nouveau nom de fichier
                $fichier = 'img/imported/'.uniqid().'.'.$image->guessExtension();
                // on copie le fichier dans le fichier upload
                $image->move(
                    $this->getParameter('img_directory'),
                    $fichier
                );
                // on stocke l'image dans la BDD (son nom)
                $img = new ImgCollectionBenefit();
                $img->setImg($fichier);
                $benefit->addImgCollectionBenefit($img);
                
            }
            $benefit = $form->getData();
            $category->addBenefit($benefit);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($benefit);
            $entityManager->flush();
            $this->addFlash("success" , $benefit->getTitle()." à été ajouté/Modifié avec succès");
            return $this->redirectToRoute('detail_category' , ['id' => $benefit->getCategory()->getId()]); 
        }
        return $this->render('management/addBenefit.html.twig', [
            'form' =>  $form->createView(),
            'sessionId' => $benefit->getId(),
        ]);
    }

    /**
     * function pour modifié une préstation 
     * @Route("/admin/management/edit/{id}" , name="edit_benefit" )
     */
    public function editBenefit( ManagerRegistry $doctrine, Benefit $benefit = null, Request $request): Response
    {
        $form = $this->createForm(BenefitType::class,$benefit);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
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
                $benefit->setImg($newFilename);
            }
               // on récuperes les images transmises
               $imgMultiple = $form->get('imagCollectionBenefits')->getData();
               // dd($imgMultiple);
                // $alt = $form->get('alt')->getData();
               // on boucle sur les images
               foreach ( $imgMultiple as $image) {
                   // on génére un nouveau nom de fichier
                   $fichier = 'img/imported/'.uniqid().'.'.$image->guessExtension();
                   // on copie le fichier dans le fichier upload
                   $image->move(
                       $this->getParameter('img_directory'),
                       $fichier
                   );
                   // on stocke l'image dans la BDD (son nom)
                   $img = new ImgCollectionBenefit();
                   $img->setImg($fichier);
                   $benefit->addImgCollectionBenefit($img);
                   
               }
            $benefit = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($benefit);
            $entityManager->flush();

            $this->addFlash("success" , $benefit->getTitle()." à été ajouté/Modifié avec succès");

            return $this->redirectToRoute('detail_category' , ['id' => $benefit->getCategory()->getId()]); 
        }
        return $this->render('management/addBenefit.html.twig', [
            'form' =>  $form->createView(),
            'sessionId' => $benefit->getId(),
        ]);
    }


    /**
     * function pour ajouter un bénéfice apporter pour une préstation 
     * @Route("/admin/management/input/add", name="add_input")
     */
    public function addInput(ManagerRegistry $doctrine, Input $input = null, Request $request): Response
    {
        $input =new Input();
        $form = $this->createForm(InputType::class,$input);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $input = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($input);
            $entityManager->flush();
            $this->addFlash("success" , $input->getDescription()." à été ajouté/Modifié avec succès");
            return $this->redirectToRoute('app_input'); 
        }
        return $this->render('management/addInput.html.twig', [
            'formAddInput' =>  $form->createView(),
        ]);
    }
    /**
     * function pour modifié un bénéfices liée a une préstation
     * @Route("/admin/management/input/edit/{id}" , name="edit_input" )
     */
    public function editInput( ManagerRegistry $doctrine, Input $input = null, Request $request): Response
    {
        $form = $this->createForm(InputType::class,$input);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {

            $input = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($input);
            $entityManager->flush();

            $this->addFlash("success" , $input->getDescription()." à été ajouté/Modifié avec succès");

            return $this->redirectToRoute('app_input'); 
        }
        return $this->render('management/addInput.html.twig', [
            'formAddInput' =>  $form->createView(),
            'edit' => $input->getId(),
        ]);
    }

   

    /**
     * fonction pour afficher les préstation d'une catégorie précise
    * @Route("/admin/management/category/detail/{id}", name="detail_category")
    */
    public function detailCategory(Category $category) : Response
    {
        return $this->render('management/detailCategory.hmtl.twig', [
            'category' => $category,
        ]);
    }

    /**
     * fonction pour afficher le detail d'un blog
    * @Route("/admin/management/blog/detail/{id}", name="detail_blog")
    */
    public function detailBlog(Blog $blog) : Response
    {
        return $this->render('blog/detail.html.twig', [
            'blog' => $blog,
        ]);
    }

    /**
     * fonction pour afficher le details d'une préstation dans le but de pouvoir ajouter des bénéfices liées a celle-çi
    * @Route("/admin/management/benefit/detail/{id}", name="detail_edit_benefit")
    */
    public function detailBenefit( Benefit $benefit, BenefitRepository $sr) : Response
    {
       
        $noBenefits = $sr->findInputNotInBenefit($benefit->getId());
        return $this->render('management/detailBenefit.html.twig', [
            'benefit' => $benefit,
            'noBenefits' => $noBenefits,
           
        ]);
    }

    /**
     * fonction pour ajouter des bénéfices liée a une préstation
     * @Route("/admin/management/addInput/{benefit_id}/{input_id}", name="add_input_benefit")
     * @ParamConverter("benefit", options={"id" = "benefit_id"})
     * @ParamConverter("input", options={"id" = "input_id"})
     */
    public function addInputInBenefit(ManagerRegistry $doctrine, Benefit $benefit ,Input $input)
    {
        // ici on utilise la fonction addIntern de l'entité via la relation manyToMany puis on persit et envoie les infos en BDD
        $benefit->addInput($input);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($benefit);
        $entityManager->flush();
        return $this->redirectToRoute('detail_edit_benefit' , [ 'id' => $benefit->getId() ]);
    }

    /**
     * fonction pour enlever des bénéfices liée a une préstation
     * @Route("/admin/management/removeInput/{benefit_id}/{input_id}", name="remove_input_benefit")
     * @ParamConverter("benefit", options={"id" = "benefit_id"})
     * @ParamConverter("input", options={"id" = "input_id"})
     */
    public function removeInputInBenefit(ManagerRegistry $doctrine, Benefit $benefit ,Input $input)
    {
    
        $benefit->removeInput($input);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($benefit);
        $entityManager->flush();
        return $this->redirectToRoute('detail_edit_benefit' , [ 'id' => $benefit->getId() ]);
    }




    /**
    * @Route("/admin/management/delete_Category/{id}", name="delete_category")
    */
    public function deleteCategory(ManagerRegistry $doctrine, Category $category ) :Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($category);
        $entityManager->flush();
        $this->addFlash("success" , $category->getCategoryName()." à été supprimé avec succès");

        return $this->redirectToRoute("app_management");
    }

    /**
    * @Route("/admin/management/delete_Benefit/{id}", name="delete_benefit")
    */
    public function deleteBenefit(ManagerRegistry $doctrine, Benefit $benefit ) :Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($benefit);
        $entityManager->flush();
        $this->addFlash("success" , $benefit->getTitle()." à été supprimé avec succès");

        return $this->redirectToRoute("detail_category",['id' => $benefit->getCategory()->getId()]);
    }

    /**
    * @Route("/admin/management/delete_input/{id}", name="delete_input")
    */
    public function deleteInput(ManagerRegistry $doctrine, Input $input) :Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($input);
        $entityManager->flush();
        $this->addFlash("success" , $input->getDescription()." à été supprimé avec succès");
        return $this->redirectToRoute("app_input");
    }

    /**
    * @Route("/admin/management/delete_blog/{id}", name="delete_blog")
    */
    public function deleteblog(ManagerRegistry $doctrine, Blog $blog) :Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($blog);
        $entityManager->flush();
        $this->addFlash("success" , $blog->getTitle()." à été supprimé avec succès");
        return $this->redirectToRoute("show_blog");
    }

}
