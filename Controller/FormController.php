<?php

namespace EMS\FormBundle\Controller;

use EMS\FormBundle\Components\Form;
use EMS\FormBundle\Components\ValueObject\SymfonyFormFieldsByNameArray;
use EMS\FormBundle\Security\Guard;
use EMS\FormBundle\Submit\Client;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Twig\Environment;

class FormController extends AbstractFormController
{
    /** @var FormFactory */
    private $formFactory;
    /** @var Client */
    private $client;
    /** @var Guard */
    private $guard;
    /** @var Environment */
    private $twig;

    public function __construct(FormFactory $formFactory, Client $client, Guard $guard, Environment $twig)
    {
        $this->formFactory = $formFactory;
        $this->client = $client;
        $this->guard = $guard;
        $this->twig = $twig;
    }

    public function iframe(Request $request, string $ouuid): Response
    {
        $form = $this->formFactory->create(Form::class, [], ['ouuid' => $ouuid, 'locale' => $request->getLocale()]);

        return new Response($this->twig->render('@EMSForm/iframe.html.twig', [
            'config' => $form->getConfig()->getOption('config'),
        ]));
    }

    public function form(Request $request, string $ouuid): JsonResponse
    {
        if (!$this->guard->check($request)) {
            throw new AccessDeniedHttpException('access denied');
        }

        $form = $this->formFactory->create(Form::class, [], ['ouuid' => $ouuid, 'locale' => $request->getLocale()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return new JsonResponse($this->client->submit($form));
        }

        return new JsonResponse([
            'instruction' => 'form',
            'response' => $this->twig->render('@EMSForm/form.html.twig', ['form' => $form->createView()]),
            'difficulty' => $this->guard->getDifficulty(),
        ]);
    }

    public function dynamicFieldAjax(Request $request, string $ouuid): Response
    {
        $form = $this->formFactory->create(Form::class, [], $this->getDisabledValidationsFormOptions($ouuid, $request->getLocale()));
        $form->handleRequest($request);

        $dynamicFields = new SymfonyFormFieldsByNameArray($request->request->all());
        $excludeFields = ['form__token'];

        return new JsonResponse([
            'instruction' => 'dynamic',
            'response' => $this->twig->render('@EMSForm/nested_choice_form.html.twig', [
                'form' => $form->createView(),
            ]),
            'dynamicFields' => $dynamicFields->getFieldIdsJson($excludeFields),
        ]);
    }
}
