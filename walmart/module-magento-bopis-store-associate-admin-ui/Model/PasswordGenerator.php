<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateAdminUi\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;

class PasswordGenerator
{
    /**
     * @var string
     */
    private const PASSWORD_LENGTH = 16;

    /**
     * @var Random
     */
    private Random $mathRandom;

    /**
     * @param Random $mathRandom
     */
    public function __construct(
        Random $mathRandom
    ) {
        $this->mathRandom = $mathRandom;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function generateRandomPassword(): string
    {
        return $this->mathRandom->getRandomString(
            self::PASSWORD_LENGTH,
            Random::CHARS_DIGITS . Random::CHARS_LOWERS . Random::CHARS_UPPERS
        );
    }
}
