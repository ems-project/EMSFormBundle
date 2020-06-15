<?php

declare(strict_types=1);

namespace EMS\FormBundle\Submission;

use EMS\ClientHelperBundle\Helper\Elasticsearch\ClientRequest;
use EMS\FormBundle\FormConfig\FormConfig;
use EMS\FormBundle\FormConfig\SubmissionConfig;
use Symfony\Component\Form\FormInterface;

class Client
{
    /** @var ClientRequest */
    private $clientRequest;

    /** @var \Traversable */
    private $handlers;

    public function __construct(ClientRequest $clientRequest, \Traversable $handlers)
    {
        $this->clientRequest = $clientRequest;
        $this->handlers = $handlers;
    }

    public function submit(FormInterface $form): array
    {
        /** @var FormConfig $formConfig */
        $formConfig = $form->getConfig()->getOption('config');
        $this->loadSubmissions($formConfig);

        $responseCollector = new HandleResponseCollector();

        foreach ($formConfig->getSubmissions() as $submissionConfig) {
            $handleRequest = new HandleRequest($form, $formConfig, $responseCollector, $submissionConfig);
            $this->handle($handleRequest);
        }

        return [
            'instruction' => 'submitted',
            'response' => $responseCollector->toJson(),
        ];
    }

    private function handle(HandleRequestInterface $handleRequest): void
    {
        foreach ($this->handlers as $handler) {
            if (! $handler instanceof AbstractHandler) {
                continue;
            }
            if ($handler->canHandle($handleRequest->getClass())) {
                $handleRequest->addResponse($handler->handle($handleRequest));
            }
        }
    }

    private function loadSubmissions(FormConfig $config): void
    {
        $emsLinkSubmissions = $config->getSubmissions();

        $submissions = [];

        foreach ($emsLinkSubmissions as $emsLinkSubmission) {
            $submission = $this->clientRequest->getByEmsKey($emsLinkSubmission, [])['_source'];
            $submissions[] = new SubmissionConfig($submission['type'], $submission['endpoint'], $submission['message']);
        }

        $config->setSubmissions($submissions);
    }
}
