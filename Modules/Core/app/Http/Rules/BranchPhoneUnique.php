<?php

namespace Modules\Core\Http\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Modules\Core\Models\Branch;
use Propaganistas\LaravelPhone\PhoneNumber;
use Closure;

class BranchPhoneUnique implements ValidationRule
{
    private ?string $countryDial;
    private ?array $args;

    public function __construct(string $countryDial = 'sa', array $args = [])
    {
        $this->countryDial = $countryDial;
        $this->args = $args;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $mobile = (string) new PhoneNumber($value, $this->countryDial);
        if (!is_null(Branch::where('mobile', $mobile)->where('id', '!=', $this->args['id'])->first())) {
            $fail(__('accounting::validation.unique', ['attribute' => $attribute]));
        }
    }
}
