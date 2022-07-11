<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use App\Repository\StoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    #[Route('/api/store/{store}/menu', methods: ["GET"])]
    public function list(int $store, StoreRepository $storeRepository): JsonResponse
    {
        $store = $storeRepository->find($store);

        if(!$store) {
            return $this->json([
                "error" => "Estabelecimento não encontrado!"
            ],status: 404);
        }

        $menus = $store->getMenus();
        $res = [];
        foreach ($menus as $menu) {
            $res = [
                ...$res,
                [
                    "id" => $menu->getId()
                ]
            ];
        }
        return $this->json($res);
    }

    #[Route('/api/store/{store}/menu/{id}', methods: ["GET"])]
    public function getMenu(int $store, int $id, StoreRepository $storeRepository, MenuRepository $menuRepository): JsonResponse
    {
        $menu = $menuRepository->find($id);

        if(!$menu) {
            return $this->json([
                "error" => "Menu não encontrado!"
            ],status: 404);
        }

        return $this->json([
            "id" => $menu->getId(),
            "name" => $menu->getName(),
            "img" => $this->getParameter('kernel.project_dir')."/public/uploads/".$menu->getName()
        ]);
    }

    #[Route('/api/user/store/{storeId}/menu', methods: ["POST"])]
    public function add(int $storeId, Request $request, StoreRepository $storeRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        if(!$request->files->has("image"))
        {
            return $this->json([
                "error" => "Parâmetros inválidos!"
            ],status: 400);
        }

        $store = $storeRepository->find($storeId);
        if(!$store)
        {
            return $this->json([
                "error" => "Estabelecimento não encontrado!"
            ],status: 404);
        }


        /** @var UploadedFile $file */
        $file = $request->files->get("image");

        $menu = new Menu();
        $menu->setStore($store);

        $entityManager->persist($menu);
        $entityManager->flush();

        $menu->setName($storeId.'-'.$menu->getId().'.'.$file->getClientOriginalExtension());
        $entityManager->flush();
        $file->move($this->getParameter('kernel.project_dir').'/public/uploads',name: $menu->getName());
        return $this->json([
            "msg" => "Menu adicionado!",
            "id" => $menu->getId()
        ]);
    }

    #[Route('/api/user/store/{storeId}/menu/{id}', methods: ["POST"])]
    public function edit(int $storeId, int $id, Request $request, MenuRepository $menuRepository, StoreRepository $storeRepository): JsonResponse
    {
        if(!$request->files->has("image"))
        {
            return $this->json([
                "error" => "Parâmetros inválidos!"
            ],status: 400);
        }

        $store = $storeRepository->find($storeId);
        if(!$store)
        {
            return $this->json([
                "error" => "Estabelecimento não encontrado!"
            ],status: 404);
        }

        $menu = $menuRepository->find($id);
        if(!$menu)
        {
            return $this->json([
                "error" => "Menu não encontrado!"
            ],status: 404);
        }

        /** @var UploadedFile $file */
        $file = $request->files->get("image");

        $file->move($this->getParameter('kernel.project_dir').'/public/uploads',name: $menu->getName());
        return $this->json([
            "msg" => "Menu atualizado!",
            "id" => $menu->getId()
        ]);
    }
}
