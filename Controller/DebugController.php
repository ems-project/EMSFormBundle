<?php

namespace EMS\FormBundle\Controller;

use EMS\FormBundle\Components\Form;
use EMS\FormBundle\Submit\Client;
use EMS\FormBundle\Submit\Responses;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class DebugController
{
    /** @var FormFactory */
    private $formFactory;
    /** @var Client */
    private $client;
    /** @var Environment */
    private $twig;
    /** @var array */
    private $locales = [];

    public function __construct(FormFactory $formFactory, Client $client, Environment $twig, array $locales)
    {
        $this->formFactory = $formFactory;
        $this->client = $client;
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

        $responses = null;
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Responses $responses */
            $responses = \json_encode(($this->client->submit($form))->getResponses());
        }

        return new Response($this->twig->render('@EMSForm/debug/form.html.twig', [
            'form' => $form->createView(),
            'locales' => $this->locales,
            'response' => $responses,
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
