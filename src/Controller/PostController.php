<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\MediaObject;
use App\Entity\Post;

class PostController extends AbstractController
{
    #[Route('/post', name: 'app_post')]
    public function index(): Response
    {
        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }

    #[Route('/posts/create', name: 'app_post', methods : ['POST'])]
    public function createPost(Request $request, ManagerRegistry $doctrine)
    {
            $parameters = json_decode($request->getContent(), true);
            $description = $parameters['description'];
            $image = $parameters['image'];

            list($first, $api, $mediaObject, $id) = explode("/", $image);
            
            echo $id;
            $repository = $doctrine->getRepository(MediaObject::class);
            $imagePath = $repository->findOneBy(['id' => $id]);

            $post = new Post();
            $post->setFilename($imagePath->getFilePath());
            $post->setDescription($description);
            $post->setImage($imagePath);

            try {
                // Save the post to the database
                $em = $doctrine->getManager();
                $em->persist($post);
                $em->flush();
            } catch (\Exception $e) {
                return new JsonResponse(["error" => $e->getMessage()], 500);
            }
            return new JsonResponse(["success" => "Post created"], 200);
    
    }
}
