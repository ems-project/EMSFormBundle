<?php

namespace EMS\FormBundle\Controller;

use EMS\FormBundle\Components\Form;
use EMS\FormBundle\Submit\Client;
use EMS\FormBundle\Submit\Responses;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class FormController
{
    /** @var FormFactory */
    private $formFactory;
    /** @var Client */
    private $client;
    /** @var Environment */
    private $twig;

    public function __construct(FormFactory $formFactory, Client $client, Environment $twig)
    {
        $this->formFactory = $formFactory;
        $this->client = $client;
        $this->twig = $twig;
    }

    public function iframe(Request $request, $ouuid)
    {
        $form = $this->formFactory->create(Form::class, [], ['ouuid' => $ouuid, 'locale' => $request->getLocale()]);

        return new Response($this->twig->render('@EMSForm/iframe.html.twig', [
            'config' => $form->getConfig()->getOption('config'),
        ]));
    }

    public function jsonForm(Request $request, $ouuid)
    {
        $form = $this->formFactory->create(Form::class, [], ['ouuid' => $ouuid, 'locale' => $request->getLocale()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Responses $responses */
            $responses = $this->client->submit($form);
            return new JsonResponse([
                'instruction' => 'submitted',
                'response' => \json_encode($responses->getResponses()),
            ]);
        }

        return new JsonResponse([
            'instruction' => 'form',
            'response' => $this->twig->render('@EMSForm/form.html.twig', ['form' => $form->createView()]),
        ]);
    }
}
