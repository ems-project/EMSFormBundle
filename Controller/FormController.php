<?php

namespace EMS\FormBundle\Controller;

use EMS\FormBundle\Service\FormClient;
use EMS\SubmissionBundle\Service\SubmissionClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class FormController
{
    /** @var FormClient */
    private $formClient;
    /** @var SubmissionClient */
    private $submissionClient;
    /** @var Environment */
    private $twig;

    public function __construct(FormClient $formClient, SubmissionClient $submissionClient, Environment $twig)
    {
        $this->formClient = $formClient;
        $this->submissionClient = $submissionClient;
        $this->twig = $twig;
    }

    public function iframe(Request $request, $ouuid)
    {
        return new Response($this->twig->render('@EMSForm/iframe.html.twig', [
            'trans_default_domain' => $this->formClient->getCacheKey(),
            'config' => $this->formClient->getFormConfiguration($ouuid, $request->getLocale()),
        ]));
    }

    public function jsonForm(Request $request, $ouuid)
    {
        $configuration = $this->formClient->getFormConfiguration($ouuid, $request->getLocale());
        $form = $this->formClient->getFormInstance($configuration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return new JsonResponse([
                'instruction' => 'submitted',
                'response' => $this->submissionClient->submit($ouuid, $request->getLocale(), $form->getData()),
            ]);
        }

        $render = $this->twig->render('@EMSForm/form.json.twig', [
            'trans_default_domain' => $this->formClient->getCacheKey(),
            'form' => $form->createView(),
            'form_theme' => $configuration->getFormTheme(),
        ]);

        return new JsonResponse(json_decode($render));
    }

    public function debugForm(Request $request, $ouuid)
    {
        $configuration = $this->formClient->getFormConfiguration($ouuid, $request->getLocale());
        $form = $this->formClient->getFormInstance($configuration);
        $form->handleRequest($request);

        return new Response($this->twig->render('@EMSForm/debug.html.twig', [
            'trans_default_domain' => $this->formClient->getCacheKey(),
            'form' => $form->createView(),
            'form_theme' => $configuration->getFormTheme(),
        ]));
    }
}
