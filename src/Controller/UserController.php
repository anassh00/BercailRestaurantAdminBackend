<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\User;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Persistence\ManagerRegistry;
// use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/userInfo/{id}')]
    public function getUserInfo($id, ManagerRegistry $doctrine): Response
    {
        try{
        $em = $doctrine->getManager();
        $repository = $em->getRepository(User::class);
        $query = $repository->createQueryBuilder('e')
            ->where('e.username = :value')
            ->setParameter('value', $id)
            ->getQuery();
        $user = $query->getResult();
        } catch (\Exception $e) {
            return new JsonResponse(["error" => $e->getMessage()], 500);
        }
            return new JsonResponse($user);
        }

    #[Route('/user/create', name: 'app_user', methods : ['POST'])]
    public function create(Request $request, UserPasswordHasherInterface $passwordEncoder, ManagerRegistry $doctrine)
    {
    
            $parameters = json_decode($request->getContent(), true);
            //echo $parameters['username'];

            $password = $parameters['password'];
            $username = $parameters['username'];
            $email = $parameters['email'];
            $user = new User();
            $user->setPassword($password);
            $user->setUsername($username);
            $user->setEmail($email);
            // Hash the password
            $hashedPassword = $this->hasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);
    
            try {
                // Save the user to the database
                $em = $doctrine->getManager();
                $em->persist($user);
                $em->flush();
            } catch (\Exception $e) {
                return new JsonResponse(["error" => $e->getMessage()], 500);
            }
            return new JsonResponse(["success" => $user->getUsername(). " has been registered!"], 200);
    }
}
