<?php

namespace EMS\FormBundle\Controller;

use EMS\FormBundle\Components\Form;
use EMS\FormBundle\Components\ValueObject\SymfonyFormFieldsByNameArray;
use EMS\FormBundle\Security\Guard;
use EMS\FormBundle\Submission\Client;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
            'config' => $this->getFormConfig($form),
        ]));
    }

    public function submitForm(Request $request, string $ouuid): JsonResponse
    {
        if (!$this->guard->checkForm($request)) {
            throw new AccessDeniedHttpException('access denied');
        }

        $form = $this->formFactory->create(Form::class, [], ['ouuid' => $ouuid, 'locale' => $request->getLocale()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return new JsonResponse($this->client->submit($form, $ouuid));
        }

        return $this->generateFormResponse($ouuid, $form);
    }

    public function initForm(Request $request, string $ouuid): JsonResponse
    {
        $content = $request->getContent();
        if (!\is_string($content)) {
            throw new \RuntimeException('Unexpected non-string request content');
        }
        $data = \json_decode($content, true);
        if (null === $data) {
            $data = [];
        }
        if (!\is_array($data)) {
            throw new \RuntimeException('Unexpected non-array request data');
        }

        $form = $this->formFactory->create(Form::class, $data, ['ouuid' => $ouuid, 'locale' => $request->getLocale()]);

        return $this->generateFormResponse($ouuid, $form);
    }

    public function dynamicFieldAjax(Request $request, string $ouuid): Response
    {
        $form = $this->formFactory->create(Form::class, [], $this->getDisabledValidationsFormOptions($ouuid, $request->getLocale()));
        $form->handleRequest($request);

        $dynamicFields = new SymfonyFormFieldsByNameArray($request->request->all());
        $excludeFields = ['form__token'];

        return new JsonResponse([
            'ouuid' => $ouuid,
            'instruction' => 'dynamic',
            'response' => $this->twig->render('@EMSForm/nested_choice_form.html.twig', [
                'form' => $form->createView(),
            ]),
            'dynamicFields' => $dynamicFields->getFieldIdsJson($excludeFields),
        ]);
    }

    /**
     * @param FormInterface<FormInterface> $form
     */
    private function generateFormResponse(string $ouuid, FormInterface $form): JsonResponse
    {
        $template = $this->getFormConfig($form)->getTemplate();

        return new JsonResponse([
            'ouuid' => $ouuid,
            'instruction' => 'form',
            'response' => $this->twig->render($template, ['form' => $form->createView()]),
            'difficulty' => $this->guard->getDifficulty(),
        ]);
    }
}
