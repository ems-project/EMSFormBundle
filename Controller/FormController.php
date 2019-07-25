<?php

namespace EMS\FormBundle\Controller;

use EMS\FormBundle\Form\FormFactory;
use EMS\SubmissionBundle\Service\SubmissionClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class FormController
{
    /** @var FormFactory */
    private $formFactory;
    /** @var SubmissionClient */
    private $submissionClient;
    /** @var Environment */
    private $twig;

    public function __construct(FormFactory $formFactory, SubmissionClient $submissionClient, Environment $twig)
    {
        $this->formFactory = $formFactory;
        $this->submissionClient = $submissionClient;
        $this->twig = $twig;
    }

    public function iframe(Request $request, $ouuid)
    {
        $emsForm = $this->formFactory->create($ouuid, $request->getLocale());

        return new Response($this->twig->render('@EMSForm/iframe.html.twig', [
            'trans_default_domain' => $emsForm->config->getTranslationDomain(),
            'config' => $emsForm->config,
        ]));
    }

    public function jsonForm(Request $request, $ouuid)
    {
        $emsForm = $this->formFactory->create($ouuid, $request->getLocale());
        $emsForm->form->handleRequest($request);

        if ($emsForm->form->isSubmitted() && $emsForm->form->isValid()) {
            return new JsonResponse([
                'instruction' => 'submitted',
                'response' => $this->submissionClient->submit($ouuid, $request->getLocale(), $emsForm->form->getData()),
            ]);
        }

        $render = $this->twig->render('@EMSForm/form.json.twig', [
            'trans_default_domain' => $emsForm->config->getTranslationDomain(),
            'form' => $emsForm->form->createView(),
            'form_theme' => $emsForm->config->getTheme(),
        ]);

        return new JsonResponse(json_decode($render));
    }

    public function debugForm(Request $request, $ouuid)
    {
        $emsForm = $this->formFactory->create($ouuid, $request->getLocale());
        $emsForm->form->handleRequest($request);

        return new Response($this->twig->render('@EMSForm/debug.html.twig', [
            'form' => $emsForm->form->createView(),
            'form_theme' => $emsForm->config->getTheme(),
            'trans_default_domain' => $emsForm->config->getTranslationDomain(),
            'config' => $emsForm->config,
        ]));
    }
}
