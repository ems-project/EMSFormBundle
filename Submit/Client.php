<?php

namespace EMS\FormBundle\Submit;

use EMS\ClientHelperBundle\Helper\Elasticsearch\ClientRequest;
use EMS\FormBundle\FormConfig\FormConfig;
use EMS\FormBundle\FormConfig\SubmissionConfig;
use EMS\FormBundle\Handler\AbstractHandler;
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

    public function submit(FormInterface $form): Responses
    {
        /** @var FormConfig $config */
        $config = $form->getConfig()->getOption('config');
        $this->loadSubmissions($config);

        $responses = new Responses();

        foreach ($config->getSubmissions() as $submission) {
            $this->handle($submission, $responses, $form, $config);
        }

        return $responses;
    }

    private function handle(SubmissionConfig $submission, Responses $response, FormInterface $form, FormConfig $config) : void
    {
        foreach ($this->handlers as $handler) {
            if (! $handler instanceof AbstractHandler) {
                continue;
            }
            if ($handler->canHandle($submission->getClass())) {
                $response->addResponse($handler->handle($submission, $form, $config));
            }
        }
    }

    private function loadSubmissions(FormConfig $config): void
    {
        $emsLinkSubmissions = $config->getSubmissions();
        $config->setSubmissions([]);

        foreach ($emsLinkSubmissions as $emsLinkSubmission) {
            $submission = $this->clientRequest->getByEmsKey($emsLinkSubmission, [])['_source'];
            $config->addSubmission(new SubmissionConfig($submission['type'], $submission['endpoint'], $submission['message']));
        }
    }
}
