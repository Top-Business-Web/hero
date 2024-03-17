<?php declare(strict_types = 1);
/*
 * This file is part of PharIo\Manifest.
 *
<<<<<<< HEAD
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
=======
 * (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
 */
namespace PharIo\Manifest;

use LibXMLError;
<<<<<<< HEAD
use function sprintf;
=======
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786

class ManifestDocumentLoadingException extends \Exception implements Exception {
    /** @var LibXMLError[] */
    private $libxmlErrors;

    /**
     * ManifestDocumentLoadingException constructor.
     *
     * @param LibXMLError[] $libxmlErrors
     */
    public function __construct(array $libxmlErrors) {
        $this->libxmlErrors = $libxmlErrors;
        $first              = $this->libxmlErrors[0];

        parent::__construct(
<<<<<<< HEAD
            sprintf(
=======
            \sprintf(
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
                '%s (Line: %d / Column: %d / File: %s)',
                $first->message,
                $first->line,
                $first->column,
                $first->file
            ),
            $first->code
        );
    }

    /**
     * @return LibXMLError[]
     */
    public function getLibxmlErrors(): array {
        return $this->libxmlErrors;
    }
}
