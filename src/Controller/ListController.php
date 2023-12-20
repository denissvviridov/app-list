<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ListController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/list', name: 'app_list')]
    public function index(): Response
    {

        $postRepository = $this->entityManager->getRepository(Post::class);
        $posts = $postRepository->findAll();

        return $this->render('list/index.html.twig', [
            'posts' => $posts,
        ]);

    }

    #[Route('/delete/{id}', name: 'app_delete')]
    public function delete($id, SessionInterface $session)
    {

        $post = $this->entityManager->getRepository(Post::class)->find($id);
        $this->entityManager->remove($post);
        $this->entityManager->flush();

        $session->getFlashBag()->add('success', 'Post was deleted!');

        return $this->redirectToRoute('app_list');

    }
}
