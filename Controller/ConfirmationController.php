<?php

declare(strict_types=1);

namespace EMS\FormBundle\Controller;

use EMS\FormBundle\Security\Guard;
use EMS\FormBundle\Service\Confirmation\ConfirmationRequest;
use EMS\FormBundle\Service\Confirmation\ConfirmationService;
use Psr\Log\LoggerInterface;
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
    /** @var LoggerInterface */
    private $logger;

    public function __construct(Guard $guard, ConfirmationService $confirmationService, LoggerInterface $logger)
    {
        $this->guard = $guard;
        $this->confirmationService = $confirmationService;
        $this->logger = $logger;
    }

    public function postSend(Request $request, string $ouuid): Response
    {
        return $this->send($request, $ouuid);
    }

    public function postDebug(Request $request, string $ouuid): Response
    {
        return $this->send($request, $ouuid, true);
    }

    private function send(Request $request, string $ouuid, bool $debug = false): Response
    {
        $response = ['instruction' => 'send-confirmation', 'response' => false, 'ouuid' => $ouuid];

        try {
            if (!$debug && !$this->guard->check($request)) {
                throw new AccessDeniedHttpException('access denied');
            }

            $response['response'] = $this->confirmationService->send(new ConfirmationRequest($request), $ouuid);

            return new JsonResponse($response);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            return new JsonResponse($response);
        }
    }
}
