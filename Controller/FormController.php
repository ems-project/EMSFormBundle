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

    public function getForm(Request $request, string $domainId, string $formId)
    {
        //TODO pass locale
        $form = $this->formClient->getForm($formId, 'nl');
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->render('@EMSForm/form-api/postmessage-handler.html.twig', [
                'form' => $form->createView(),
                'domains' => $this->formClient->getAllowedDomains($domainId),
            ]);
        }

        if ($form->isValid()) {
            //TODO use submission handler
            return $this->render('@EMSForm/form-api/success.html.twig');
        }

        return $this->render('@EMSForm/form-api/validation-error.html.twig', [
           'form' => $form->createView(),
        ]);
    }
}