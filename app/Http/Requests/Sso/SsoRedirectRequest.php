<?php

namespace App\Http\Requests\Sso;

use App\Sso\SsoManager;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SsoRedirectRequest extends FormRequest
{
    public function __construct(private readonly SsoManager $manager)
    {
        parent::__construct();
    }

    public function authorize(): bool
    {
        return $this->manager->enabled() && $this->manager->knows((string) $this->route('provider'));
    }

    protected function failedAuthorization(): void
    {
        throw new NotFoundHttpException;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [];
    }
}
