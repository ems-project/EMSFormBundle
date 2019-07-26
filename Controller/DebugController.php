<?php

namespace EMS\FormBundle\Controller;

use EMS\FormBundle\Components\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class DebugController
{
    /** @var FormFactory */
    private $formFactory;
    /** @var Environment */
    private $twig;
    /** @var array */
    private $locales = [];

    public function __construct(FormFactory $formFactory, Environment $twig, array $locales)
    {
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->locales = $locales;
    }

    public function form(Request $request, $ouuid)
    {
        $locale = $request->query->get('_locale', $request->getLocale());

        $form = $this->formFactory->create(Form::class, [], ['ouuid' => $ouuid, 'locale' => $locale]);
        $form->handleRequest($request);

        return new Response($this->twig->render('@EMSForm/debug.html.twig', [
            'form' => $form->createView(),
            'locales' => $this->locales,
        ]));
    }
}
