<?php
namespace App\Controller;

use App\Entity\Article;
use App\Entity\Blog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    /**
     * @Route("/")
     */
    public function index()
    {
        return $this->render('base.html.twig');
    }

    /**
     * @Route("/hello/{name}")
     */
    public function hello($name){
        return $this->render('default/index.html.twig', [
            'name' => $name,
        ]);
    }

    /**
     * @Route("/blog", name="blog")
     */
    public function simple(\Symfony\Component\HttpFoundation\Request $request)
    {
        $task = new Blog();

        $form = $this->createFormBuilder($task)
            ->add('title', TextType::class)
            ->add('body', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Create Blog'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
             $em = $this->getDoctrine()->getManager();
             $em->persist($task);
             $em->flush();

            return $this->redirectToRoute('blog');
        }

        return $this->render('default/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/articles-list")
     */
    public function artilesList(){
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();

        return $this->render('article/list.html.twig', [
            'articles' => $articles,
        ]);
    }
}