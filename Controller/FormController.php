<?php

namespace EMS\FormBundle\Controller;

use EMS\FormBundle\Service\FormClient;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class FormController extends Controller
{
    public function getForm(FormClient $formClient, string $domainId, string $formId, Request $request)
    {
        //TODO pass locale
        $form = $formClient->getForm($formId, 'nl');
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->render('form-api/postmessage-handler.html.twig', [
                'form' => $form->createView(),
                'domains' => $formClient->getAllowedDomains($domainId),
            ]);
        }

        if ($form->isValid()) {
            //TODO use submission handler
            return $this->render('form-api/success.html.twig');
        }

        return $this->render('form-api/validation-error.html.twig', [
           'form' => $form->createView(),
        ]);
    }
}