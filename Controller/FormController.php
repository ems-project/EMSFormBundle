<?php

namespace EMS\FormBundle\Controller;

use EMS\FormBundle\Service\FormClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class FormController extends AbstractController
{
    /** @var FormClient */
    private $formClient;

    public function __construct(FormClient $formClient)
    {
        $this->formClient = $formClient;
    }

    public function getFormInstance(Request $request, string $domainId, string $formId)
    {
        $configuration = $this->formClient->getFormConfiguration($domainId, $formId, $request->getLocale());
        $form = $this->formClient->getFormInstance($configuration);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->render('@EMSForm/form-api/postmessage-handler.html.twig', [
                'trans_default_domain' => $this->formClient->getCacheKey(),
                'form' => $form->createView(),
                'form_theme' => $configuration->getFormTheme(),
                'domains' => $configuration->getDomains(),
            ]);
        }

        if ($form->isValid()) {
            return $this->forward('EMS\SubmissionBundle\Controller\SubmissionController::submit', [
                'submissionId' => sprintf('%s-%s', $domainId, $formId),
                'data' => $form->getData(),
                'locale' => $request->getLocale(),
            ]);
        }

        return $this->render('@EMSForm/form-api/validation-error.html.twig', [
            'trans_default_domain' => $this->formClient->getCacheKey(),
            'form' => $form->createView(),
            'form_theme' => $configuration->getFormTheme(),
        ]);
    }
}
