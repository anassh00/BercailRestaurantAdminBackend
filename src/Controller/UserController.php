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

    #[Route('/user/create', name: 'app_user', methods : ['POST'])]
    public function create(Request $request, UserPasswordHasherInterface $passwordEncoder, ManagerRegistry $doctrine)
    {
    //     $data = [
    //         // 'firstName' => $request->request->get('firstName'),
    //         // 'lastName' => $request->request->get('lastName'),
    //         'password' => $request->request->get('password'),
    //         'username' => $request->request->get('username')
    //     ];

    //     $validator = Validation::createValidator();
    //     $constraint = new Assert\Collection(array(
    //         // the keys correspond to the keys in the input array
    //         // 'firstName' => new Assert\Length(array('min' => 1)),
    //         // 'lastName' => new Assert\Length(array('min' => 1)),
    //         'password' => new Assert\Length(array('min' => 1)),
    //         'username' => new Assert\Length(array('min' => 1))
    //         // 'userEmail' => new Assert\Email()
    //     ));
    //     $violations = $validator->validate($data, $constraint);
    //     if ($violations->count() > 0) {
    //         return new JsonResponse(["error" => (string)$violations], 500);
    //     }
    //     // $firstName = $data['firstName'];
    //     // $lastName = $data['lastName'];
    //     $password = $data['password'];
    //     $username = $data['username'];
    //     // $userRole = $request->request->get('userRole');

    //     $user = new User();
    //     $user
    //         // ->setFirstName($firstName)
    //         // ->setLastName($lastName)
    //         ->setPassword($password)
    //         ->setUsername($username)
    //         // ->setRoles($userRole)
    //         ->onPrePersist()
    //     ;

    //     $password = $passwordEncoder->encodePassword($user, $user->getPassword());
    //     $user->setPassword($password);

        // try {
        //     $entityManager = $this->getDoctrine()->getManager();
        //     $entityManager->persist($user);
        //     $entityManager->flush();
        // } catch (\Exception $e) {
        //     return new JsonResponse(["error" => $e->getMessage()], 500);
        // }
        // return new JsonResponse(["success" => $user->getUsername(). " has been registered!"], 200);
            // Get the plain-text password from the request
            $parameters = json_decode($request->getContent(), true);
            echo $parameters['username']; // will print 'user'

            $password = $parameters['password'];
            $username = $parameters['username'];
            $user = new User();
            $user->setPassword($password);
            $user->setUsername($username);
            // Hash the password
            $hashedPassword = $this->hasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);
    
            // Create a new user and set the hashed password
            // $user = new User();
            // $request->setPassword($hashedPassword);
    
            // ...
    
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
