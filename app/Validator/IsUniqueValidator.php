<?php

namespace RezaFikkri\PLM\Validator;

use PDO;
use RezaFikkri\PLM\Config\Database;
use Symfony\Component\Validator\{
    Constraint,
    ConstraintValidator,
    Exception\UnexpectedTypeException,
};

class IsUniqueValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof IsUnique) {
            throw new UnexpectedTypeException($constraint, IsUnique::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        $dbc = Database::getConnection();

        $sql = "SELECT $constraint->field FROM $constraint->table WHERE $constraint->field = :fieldValue";
        if (!is_null($constraint->ignoreField)) {
            $sql .= " AND $constraint->ignoreField != :ignoreFieldValue";
        }

        $stmt = $dbc->prepare($sql);

        $params = [ ':fieldValue' => $value ];
        if (!is_null($constraint->ignoreField)) {
            $params[':ignoreFieldValue'] = $constraint->ignoreValue;
        }
        $stmt->execute($params);
        
        $resultDb = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($resultDb) > 0) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ field }}', $constraint->field)
                ->addViolation();
        }
    }
}
