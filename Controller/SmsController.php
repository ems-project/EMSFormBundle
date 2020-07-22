<?php

declare(strict_types=1);

namespace EMS\FormBundle\Controller;

use EMS\FormBundle\Security\Guard;
use EMS\FormBundle\Service\Verification\CreateRequest;
use EMS\FormBundle\Service\Verification\VerificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class SmsController extends AbstractController
{
    /** @var Guard */
    private $guard;
    /** @var VerificationService */
    private $verificationCodeService;

    public function __construct(Guard $guard, VerificationService $verificationCodeService)
    {
        $this->guard = $guard;
        $this->verificationCodeService = $verificationCodeService;
    }

    public function postSend(Request $request, string $ouuid): Response
    {
        if (!$this->guard->check($request)) {
            throw new AccessDeniedHttpException('access denied');
        }

        return new JsonResponse();
    }

    public function postDebug(Request $request, string $ouuid): Response
    {
        $verificationCode = $this->verificationCodeService->create(new CreateRequest($request), $ouuid);

        return new JsonResponse(['code' => $verificationCode]);
    }
}
