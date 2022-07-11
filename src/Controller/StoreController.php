<?php

namespace App\Controller;

use App\Entity\Store;
use App\Entity\User;
use App\Repository\StoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class StoreController extends AbstractController
{
    #[Route('/api/store', methods: ["GET"])]
    public function list(Request $request, StoreRepository $storeRepository): JsonResponse
    {
        $max = 10;
        $page = 1;
        if($request->query->has("max"))
            $max = $request->query->get("max");
        if($request->query->has("page"))
            $page = $request->query->get("page");
        try
        {
            $res = $storeRepository->list($max,$page);
        }
        catch (\Exception $exception)
        {
            return $this->json([
                "error" => "Erro interno!",
                "msg"   => $exception->getMessage()
            ],status: 500);
        }
        return $this->json($res);
    }

    #[Route('/api/user/store', methods: ["GET"])]
    public function show(#[CurrentUser] User $user): JsonResponse
    {
        $stores = $user->getStores();

        $res = [];

        foreach ($stores as $store) {
            $res = [
                ...$res,
                [
                    "id" => $store->getId(),
                    "name" => $store->getName(),
                    "description" => $store->getDescription(),
                    "phone" => $store->getPhone(),
                    "address" => $store->getAddress()
                ]
            ];
        }

        return $this->json($res);
    }

    #[Route('/api/user/store/{id}', methods: ["GET"])]
    public function getStore(#[CurrentUser] User $user, int $id): JsonResponse
    {
        $store = $user->getStores()->get($id);
        if(!$store) {
            return $this->json([
                "error" => "Estabelecimento não encontrado!"
            ],status: 404);
        }
        return $this->json([
            "id" => $store->getId(),
            "name" => $store->getName(),
            "description" => $store->getDescription(),
            "phone" => $store->getPhone(),
            "address" => $store->getAddress()
        ]);
    }

    #[Route('/api/user/store', methods: ["POST"])]
    public function add(#[CurrentUser] User $user, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $params = json_decode($request->getContent(),true);
        if(
            !isset($params["name"]) ||
            !is_array($params["phone"]) ||
            (isset($params["address"]) && !is_array($params["address"]))
        ) {
            return $this->json([
                "error" => "Parâmetros inválidos!"
            ],status: 400);
        }

        try {
            $store = new Store();
            $store->setOwner($user);
            $store->setName($params["name"]);
            if(isset($params["description"]))
                $store->setDescription($params["description"]);
            $store->setPhone($params["phone"]);
            if(isset($params["address"]))
                $store->setAddress($params["address"]);
            $entityManager->persist($store);
            $entityManager->flush();
        } catch (\Exception $exception) {
            return $this->json([
                "error" => "Erro interno!",
                "msg"   => $exception->getMessage()
            ],status: 500);
        }
        return $this->json([
            "msg" => "Cadastrado com sucesso!",
            "store" => [
                "id" => $store->getId(),
                "name" => $store->getName(),
                "description" => $store->getDescription(),
                "phone" => $store->getPhone(),
                "address" => $store->getAddress()
            ]
        ]);
    }

    #[Route('/api/user/store/{id}', methods: ["PUT"])]
    public function edit(#[CurrentUser] User $user,Request $request, EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $params = json_decode($request->getContent(),true);
        if(
            !isset($params["name"]) ||
            !is_array($params["phone"]) ||
            (isset($params["address"]) && !is_array($params["address"]))
        ) {
            return $this->json([
                "error" => "Parâmetros inválidos!"
            ],status: 400);
        }

        $store = $user->getStores()->get($id);
        if(!$store) {
            return $this->json([
                "error" => "Estabelecimento não encontrado!"
            ],status: 404);
        }

        try {
            $store->setOwner($user);
            $store->setName($params["name"]);
            if(isset($params["description"]))
                $store->setDescription($params["description"]);
            $store->setPhone($params["phone"]);
            if(isset($params["address"]))
                $store->setAddress($params["address"]);
            $entityManager->flush();
        } catch (\Exception $exception) {
            return $this->json([
                "error" => "Erro interno!",
                "msg"   => $exception->getMessage()
            ],status: 500);
        }

        return $this->json([
            "msg" => "Estabelecimento atualizado!",
            "store" => [
                "id" => $store->getId(),
                "name" => $store->getName(),
                "description" => $store->getDescription(),
                "phone" => $store->getPhone(),
                "address" => $store->getAddress()
            ]
        ]);
    }

    #[Route('/api/user/store/{id}', methods: ["DELETE"])]
    public function delete(#[CurrentUser] User $user, int $id, StoreRepository $storeRepository, EntityManagerInterface $entityManager): JsonResponse {
        $store = $storeRepository->find($id);
        if(!$store || $store->getOwner() !== $user) {
            return $this->json([
                "error" => "Estabelecimento não encontrado!"
            ],status: 404);
        }

        try {
            $entityManager->remove($store);
            $entityManager->flush();
        } catch (\Exception $exception) {
            return $this->json([
                "error" => "Erro interno!",
                "msg"   => $exception->getMessage()
            ],status: 500);
        }

        return $this->json([
            "msg" => "Estabelecimento removido!",
        ]);
    }
}
