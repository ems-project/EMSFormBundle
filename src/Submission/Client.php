<?php

declare(strict_types=1);

namespace EMS\FormBundle\Submission;

use EMS\ClientHelperBundle\Helper\Elasticsearch\ClientRequest;
use EMS\FormBundle\FormConfig\FormConfig;
use EMS\FormBundle\FormConfig\SubmissionConfig;
use Symfony\Component\Form\FormInterface;

class Client
{
    private ClientRequest $clientRequest;
    /** @var \Traversable<AbstractHandler> */
    private $handlers;

    /** @param \Traversable<AbstractHandler> $handlers */
    public function __construct(ClientRequest $clientRequest, \Traversable $handlers)
    {
        $this->clientRequest = $clientRequest;
        $this->handlers = $handlers;
    }

    /**
     * @param FormInterface<FormInterface> $form
     *
     * @return array<string, string>
     */
    public function submit(FormInterface $form, string $ouuid): array
    {
        /** @var FormConfig $formConfig */
        $formConfig = $form->getConfig()->getOption('config');
        $this->loadSubmissions($formConfig);

        $responseCollector = new HandleResponseCollector();

        foreach ($formConfig->getSubmissions() as $submissionConfig) {
            if (!$submissionConfig instanceof SubmissionConfig) {
                continue;
            }
            $handleRequest = new HandleRequest($form, $formConfig, $responseCollector, $submissionConfig);
            $handler = $this->getHandler($handleRequest);

            if (null === $handler) {
                continue;
            }

            $handleResponse = $handler->handle($handleRequest);
            $handleRequest->addResponse($handleResponse);

            if ($handleResponse instanceof AbortHandleResponse) {
                break;
            }
        }

        return [
            'instruction' => 'submitted',
            'ouuid' => $ouuid,
            'response' => $responseCollector->toJson(),
        ];
    }

    private function getHandler(HandleRequestInterface $handleRequest): ?AbstractHandler
    {
        foreach ($this->handlers as $handler) {
            if ($handler instanceof AbstractHandler && $handler->canHandle($handleRequest->getClass())) {
                return $handler;
            }
        }

        return null;
    }

    private function loadSubmissions(FormConfig $config): void
    {
        $emsLinkSubmissions = $config->getSubmissions();

        $submissions = [];

        foreach ($emsLinkSubmissions as $emsLinkSubmission) {
            if ($emsLinkSubmission instanceof SubmissionConfig) {
                $submissions[] = $emsLinkSubmission; //This is here to please phpstan, caused because we use the $config->submissions property for initialisation and the end result!
                continue;
            }

            $submission = $this->clientRequest->getByEmsKey($emsLinkSubmission, []);
            if (false === $submission) {
                continue;
            }

            $submissions[] = new SubmissionConfig($submission['_source']['type'], $submission['_source']['endpoint'], $submission['_source']['message']);
        }

        $config->setSubmissions($submissions);
    }
}
