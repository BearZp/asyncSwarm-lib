<?php
declare(strict_types=1);

namespace Lib\types\primitives;

use InvalidArgumentException;
use function filter_var;
use function mb_strlen;
use function mb_substr;
use const FILTER_VALIDATE_EMAIL;

class Email
{
    /**
     * @var string
     */
    protected $email;

    /**
     * Email constructor.
     * @param string $email
     */
    public function __construct(string $email)
    {
        if (mb_strlen($email) > 150) {
            throw new InvalidArgumentException('Invalid email length: ' . mb_strlen($email));
        }
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('Invalid email: ' . mb_substr($email, 0, 100));
        }
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->email;
    }
}
