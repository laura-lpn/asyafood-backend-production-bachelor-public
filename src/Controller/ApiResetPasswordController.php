<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;


class ApiResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Display & process form to request a password reset.
     */
    #[Route('/api/forgot-password', name: 'api_forgot_password_request', methods: ['POST'])]
    public function forgotPassword(Request $request, MailerInterface $mailer, TranslatorInterface $translator): Response
    {

        $email = $request->request->get('email');

        if (!isset($email)) {
            return new JsonResponse(['message' => 'Email is required'], JsonResponse::HTTP_BAD_REQUEST);
        }
        return ($this->processSendingPasswordResetEmail(
            $email,
            $mailer,
            $translator
        ));
    }
    #[Route('/api/reset/{token}', name: 'api_reset_password', methods: ['GET'], requirements: ['token' => '[A-Za-z0-9\-_~+/=]+'])]
    public function reset(string $token): Response
    {
        if ($token) {
            $this->storeTokenInSession($token);
        }
        $token = $this->getTokenFromSession();
        if ($token === null) {
            return new JsonResponse(['message' => 'Token not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            return new JsonResponse(['message' => 'Token not valid'], JsonResponse::HTTP_NOT_FOUND);
        }
        return new JsonResponse(['message' => 'Token valid'], JsonResponse::HTTP_OK);
    }
    #[Route('/api/change-password/{token}', name: 'api_change_password', methods: ['POST'], requirements: ['token' => '[A-Za-z0-9\-_~+/=]+'])]
    public function resetPassword(Request $request, UserPasswordHasherInterface $passwordHasher, string $token, TranslatorInterface $translator): Response
    {
        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            return new JsonResponse([
                'message' =>
                $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE, [], 'ResetPasswordBundle'),
                $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            ], JsonResponse::HTTP_NOT_FOUND);
        }
        $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);

        $data = json_decode($request->getContent(), true);
        $NewPassword = $data['newPassword'];
        if (!$NewPassword) {
            return new JsonResponse(['message' => 'Password is required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->resetPasswordHelper->removeResetRequest($token);

        $encodedPassword = $passwordHasher->hashPassword(
            $user,
            $NewPassword
        );

        $user->setPassword($encodedPassword);
        $this->entityManager->flush();

        $this->cleanSessionAfterReset();

        return new JsonResponse(['message' => 'Password change'], JsonResponse::HTTP_OK);
    }

    private function processSendingPasswordResetEmail(string $email, MailerInterface $mailer, TranslatorInterface $translator): Response
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user) {
            return new JsonResponse(['message' => 'Email not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {

            return new JsonResponse([
                'message' => 'Un lien a déjà été envoyer à cette adresse email',
            ], JsonResponse::HTTP_ACCEPTED);
        }
        $emailSend = (new TemplatedEmail())
            ->from(new Address('support@asyafood.fr', 'Asya Food'))
            ->to($user->getEmail())
            ->subject('Votre lien de réinitialisation de mot de passe')
            ->htmlTemplate('email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ]);

        $mailer->send($emailSend);

        $this->setTokenObjectInSession($resetToken);

        return new JsonResponse(['message' => 'Password reset email sent'], JsonResponse::HTTP_OK);
    }
}
