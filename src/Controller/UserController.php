<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    #[Route('/api/register', methods: ["POST"])]
    public function register(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $params = json_decode($request->getContent(),true);
        if(
            !isset($params["email"]) ||
            !isset($params["name"]) ||
            !isset($params["password"])
        ) {
            return $this->json([
                "error" => "Parâmetros inválidos!"
            ],status: 400);
        }
        $user = $userRepository->findBy(["email" => $params["email"]]);

        if($user) {
            return $this->json([
                "error" => "Email já cadastrado no sistema!"
            ],status: 401);
        }

        try {
            $user = new User();
            $hashedPassword = $passwordHasher->hashPassword($user,$params["password"]);
            $user->setPassword($hashedPassword);
            $user->setEmail($params["email"]);
            $user->setName($params["name"]);

            $userRepository->add($user,true);
        } catch (\Exception $exception) {
            return $this->json([
                "error" => "Erro interno!",
                "msg"   => $exception->getMessage()
            ],status: 500);
        }

        return $this->json([
            "id"    => $user->getId(),
            'email' => $user->getEmail(),
            'name'  => $user->getName()
        ]);
    }
}
