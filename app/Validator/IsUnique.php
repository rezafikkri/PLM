<?php

namespace RezaFikkri\PLM\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_PROPERTY)]
class IsUnique extends Constraint
{
    public function __construct(
        public string $table,
        public string $field,
        public ?string $ignoreField = null,
        public ?string $ignoreValue = null,
        public string $mode = 'strict',
        public string $message = '{{ field }} already exist. Please choose another {{ field }}.',
        ?array $groups = null,
        $payload = null
    ) {
        parent::__construct([], $groups, $payload);
    }
}
