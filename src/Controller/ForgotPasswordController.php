<?php

namespace App\Controller;

use App\Service\ForgotPasswordService;
use Doctrine\ORM\EntityManagerInterface;
use HttpResponseException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class ForgotPasswordController extends AbstractController
{
    protected $service;
    protected $logger;

    public function __construct(ForgotPasswordService $service, LoggerInterface $logger,
                                private EntityManagerInterface $em,
    ){
        $this->service = $service;
        $this->logger = $logger;
    }

    #[Route('/api/forgot-password', name: 'app_forgot_password', methods: ['POST'])]
    public function index(Request $request): JsonResponse
    {
        $this->logger->info('Forgot password method received.');

        $this->em->beginTransaction();
        try {
            $data = json_decode($request->getContent(), true);
            $email = $data['email'] ?? null;

            $this->service->forgotPassword($email);

            return $this->json([
                'message' => 'Welcome to your new controller!',
                'path' => 'src/Controller/ForgotPasswordController.php',
            ]);
        }catch (HttpResponseException $e){
            $this->em->rollback();
            throw $e;
        } catch (\Exception $e) {
            $this->em->rollback();
            $this->logger->error('Forgot-password error: ' . $e->getMessage());
            return $this->json([
                'message' => 'Something went wrong!',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/reset-password', name: 'reset_password')]
    public function resetPassword(Request $request)
    {
        dd(1111);
    }
}
