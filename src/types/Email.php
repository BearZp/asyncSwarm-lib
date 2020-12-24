<?php
declare(strict_types=1);

/**
 * Created by PhpStorm for WL
 * User: BearZp aka Gulidov Vadim (gulidov.vadim@gmail.com)
 * Date: 14.03.19
 * Time: 11:10
 */

namespace Lib\types;

use Lib\types\base\TypeInterface;

class Email implements TypeInterface
{
    /**
     * @var string
     */
    private $email;

    /**
     * Email constructor.
     * @param string $email
     */
    public function __construct(string $email)
    {
        if (\mb_strlen($email) > 150) {
            throw new \InvalidArgumentException('Invalid email length: ' . \mb_strlen($email));
        }
        if (filter_var($email, \FILTER_VALIDATE_EMAIL) === false) {
            throw new \InvalidArgumentException('Invalid email: ' . mb_substr($email, 0, 100));
        }
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->email;
    }

    /**
     * @param TypeInterface $obj
     * @return bool
     */
    public function isEqual(TypeInterface $obj): bool
    {
        return $obj instanceof static and $this->email = $obj->email;
    }
}
