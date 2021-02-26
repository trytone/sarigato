<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;

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
      // return user or null
      return new JsonResponse( ($userArray ? $userArray : NULL) );
    }

    public function createAction(Request $request): JsonResponse
    {
        $json = $this->decode($request);
        if(!$json) return new JsonResponse(['status' => 'error', 'message' => 'JSON syntax error.']);

        $userId = $this->repository->add([
          'email' => $json['email'],
          'password' => $json['password']
        ]);

        // return user_id or error
        return new JsonResponse(
          ($userId ? ['status' => 'success',  'user_id'  => $userId] : ['status' => 'error', 'message' => 'E-mail not valid.'])
        );
    }

    public function updateAction(int $id, Request $request): JsonResponse
    {
        $json = $this->decode($request);
        if(!$json) return new JsonResponse(['status' => 'error', 'message' => 'JSON syntax error.']);

        $userArray['id'] = $id;
        $userArray['email'] = $json['email'];
        $userArray['password'] = $json['password'];

        $result = $this->repository->update($userArray);

        return new JsonResponse(($result ? ['status' => 'success'] : ['status' => 'error', 'message' => 'E-mail not valid.'] ));
    }

    public function deleteAction(int $id): JsonResponse
    {
        $result = $this->repository->remove($id);
        return new JsonResponse(['status' => ($result ? 'success' : 'error')]);
    }

    private function decode(Request $request){
      try{
        return $request->toArray();
      } catch(\Exception $e){
        return false;
      }
    }
}
