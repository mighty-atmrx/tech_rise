<?php

namespace App\Controller;

use App\Dto\RegistrationRequest;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use HttpResponseException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UserController extends AbstractController
{
    public function __construct(
        UserService $userService,
        private EntityManagerInterface $em,
    ){
        $this->userService = $userService;
    }

    #[Route('/api/registration', name: 'registration', methods: ['POST'])]
    public function registration(Request $request, ValidatorInterface $validator,
                                 LoggerInterface $logger): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $dto = new RegistrationRequest();
        $dto->login = $data['login'] ?? '';
        $dto->password = $data['password'] ?? '';
        $dto->email = $data['email'] ?? '';

        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $errorMessages = [];

            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            return $this->json([
                'errors' => $errorMessages
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->em->beginTransaction();
        try {
            $this->userService->registration($dto);
            $this->em->commit();
            return $this->json([
                'message' => 'Registration successful'
            ], Response::HTTP_OK);
        } catch (HttpResponseException $e) {
            $this->em->rollback();
            throw $e;
        } catch (\Exception $e) {
            $this->em->rollback();
            $logger->error('Registration error: ' . $e->getMessage());
            return $this->json([
                'error' => 'Registration failed. Please try again.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
