<?php

namespace EMS\FormBundle\Controller;

use EMS\FormBundle\Components\Form;
use EMS\SubmissionBundle\Submission\SubmitResponse;
use EMS\SubmissionBundle\Service\SubmissionClient;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class DebugController
{
    /** @var FormFactory */
    private $formFactory;
    /** @var SubmissionClient */
    private $submissionClient;
    /** @var Environment */
    private $twig;
    /** @var array */
    private $locales = [];

    public function __construct(FormFactory $formFactory, SubmissionClient $submissionClient, Environment $twig, array $locales)
    {
        $this->formFactory = $formFactory;
        $this->submissionClient = $submissionClient;
        $this->twig = $twig;
        $this->locales = $locales;
    }

    public function form(Request $request, string $ouuid)
    {
        $formOptions = ['ouuid' => $ouuid, 'locale' => $request->getLocale()];

        if ($request->query->has('novalidate')) {
            $formOptions['attr'] = ['novalidate' => 'novalidate'];
        }

        $form = $this->formFactory->create(Form::class, [], $formOptions);
        $form->handleRequest($request);

        $response = null;
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SubmitResponse $response */
            $response = \json_encode(($this->submissionClient->submit($form))->getResponses());
        }

        return new Response($this->twig->render('@EMSForm/debug/form.html.twig', [
            'form' => $form->createView(),
            'locales' => $this->locales,
            'response' => $response,
        ]));
    }

    public function iframe(Request $request, string $ouuid)
    {
        $form = $this->formFactory->create(Form::class, [], ['ouuid' => $ouuid, 'locale' => $request->getLocale()]);

        return new Response($this->twig->render('@EMSForm/debug/iframe.html.twig', [
            'config' => $form->getConfig()->getOption('config'),
        ]));
    }
}
