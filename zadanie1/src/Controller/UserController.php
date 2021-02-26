<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;

use App\Entity\User;
use App\Repository\UserRepository;

class UserController extends AbstractController
{
    private $repository;

    public function __construct(){
      $this->repository = new UserRepository;
    }

    public function getAction(int $id): JsonResponse
    {
      $userArray = $this->repository->find($id);
      return new JsonResponse(json_encode($userArray));
    }

    public function createAction(Request $request): JsonResponse
    {
        $data = $request->toArray();

        $userEntity = new User();
        $userEntity->setEmail($data['email']);
        $userEntity->setPassword($data['password']);

        $this->repository->add($userEntity);

        return new JsonResponse((array)$userEntity ?? ['error' => 'Error while creating user. Check if email is valid.']);
    }

    public function updateAction(int $id, Request $request): JsonResponse
    {
        $data = $request->toArray();

        $userEntity = $this->repository->find($id);
        $userEntity->setEmail($data['email']);
        $userEntity->setPassword($data['password']);

        $this->repository->update($userEntity);

        return new JsonResponse($userEntity ?? ['error' => 'No user found.']);
    }

    public function deleteAction(int $id): JsonResponse
    {
        return new JsonResponse(['status' => $this->repository->remove($id)]);
    }
}
