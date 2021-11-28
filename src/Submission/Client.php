<?php

declare(strict_types=1);

namespace EMS\FormBundle\Submission;

use EMS\ClientHelperBundle\Contracts\Elasticsearch\ClientRequestInterface;
use EMS\ClientHelperBundle\Contracts\Elasticsearch\ClientRequestManagerInterface;
use EMS\CommonBundle\Common\Standard\Json;
use EMS\FormBundle\FormConfig\FormConfig;
use EMS\FormBundle\FormConfig\SubmissionConfig;
use Symfony\Component\Form\FormInterface;

class Client
{
    private ClientRequestInterface $clientRequest;
    /** @var \Traversable<AbstractHandler> */
    private $handlers;

    /** @param \Traversable<AbstractHandler> $handlers */
    public function __construct(ClientRequestManagerInterface $clientRequestManager, \Traversable $handlers)
    {
        $this->clientRequest = $clientRequestManager->getDefault();
        $this->handlers = $handlers;
    }

    /**
     * @param FormInterface<FormInterface> $form
     *
     * @return array<string, array<array<string, string>>|string>
     */
    public function submit(FormInterface $form, string $ouuid): array
    {
        /** @var FormConfig $formConfig */
        $formConfig = $form->getConfig()->getOption('config');
        $this->loadSubmissions($formConfig);

        $responseCollector = new HandleResponseCollector();
        $submissions = $formConfig->getSubmissions();
        if (!\is_array($submissions)) {
            throw new \RuntimeException('Unexpected not loaded submissions (Still in JSON serialized format)');
        }

        foreach ($submissions as $submissionConfig) {
            if (!$submissionConfig instanceof SubmissionConfig) {
                throw new \RuntimeException('Unexpected not loaded submissions');
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
            'summaries' => $responseCollector->getSummaries(),
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
        $submissionsConfig = $config->getSubmissions();
        if (\is_string($submissionsConfig)) {
            $submissionsConfig = Json::decode($submissionsConfig);
        }

        $submissions = [];

        foreach ($submissionsConfig as $submission) {
            if ($submission instanceof SubmissionConfig) {
                $submissions[] = $submission; //This is here to please phpstan, caused because we use the $config->submissions property for initialisation and the end result!
                continue;
            }

            if (\is_string($submission)) {
                $submission = $this->clientRequest->getByEmsKey($submission, []);
                if (false === $submission) {
                    continue;
                }
                $submission = $submission['_source'];
            } else {
                $submission = $submission['object'];
            }

            $submissions[] = new SubmissionConfig($submission['type'], $submission['endpoint'], $submission['message']);
        }

        $config->setSubmissions($submissions);
    }
}
