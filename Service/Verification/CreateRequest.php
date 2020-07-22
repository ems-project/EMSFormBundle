<?php

declare(strict_types=1);

namespace EMS\FormBundle\Service\Verification;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CreateRequest
{
    /** @var string */
    private $codeField;
    /** @var string */
    private $locale;
    /** @var string */
    private $token;
    /** @var string */
    private $value;

    public function __construct(Request $request)
    {
        $json = json_decode((string) $request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($json)) {
            throw new \Exception('invalid JSON!');
        }

        $data = $this->resolveJson(array_filter($json));

        $this->codeField = $data['code-field'];
        $this->locale = $request->getLocale();
        $this->token = $data['token'];
        $this->value = $data['value'];
    }

    public function getCodeField(): string
    {
        return $this->codeField;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function resolveJson(array $json): array
    {
        $jsonResolver = new OptionsResolver();
        $jsonResolver->setRequired(['code-field', 'token', 'value']);

        return $jsonResolver->resolve($json);
    }
}
