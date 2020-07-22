<?php

declare(strict_types=1);

namespace EMS\FormBundle\Controller;

use EMS\FormBundle\Security\Guard;
use EMS\FormBundle\Service\Confirmation\ConfirmationRequest;
use EMS\FormBundle\Service\Confirmation\ConfirmationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class ConfirmationController extends AbstractController
{
    /** @var Guard */
    private $guard;
    /** @var ConfirmationService */
    private $confirmationService;

    public function __construct(Guard $guard, ConfirmationService $confirmationService)
    {
        $this->guard = $guard;
        $this->confirmationService = $confirmationService;
    }

    public function postSend(Request $request, string $ouuid): Response
    {
        if (!$this->guard->check($request)) {
            throw new AccessDeniedHttpException('access denied');
        }

        $result = $this->confirmationService->send(new ConfirmationRequest($request), $ouuid);

        return new JsonResponse(['result' => $result]);
    }

    public function postDebug(Request $request, string $ouuid): Response
    {
        $message = $this->confirmationService->getMessage(new ConfirmationRequest($request), $ouuid);

        return new JsonResponse(['message' => $message]);
    }
}
