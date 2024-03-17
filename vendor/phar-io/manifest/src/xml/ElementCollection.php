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

use DOMElement;
use DOMNodeList;
<<<<<<< HEAD
use Iterator;
use ReturnTypeWillChange;
use function count;
use function get_class;
use function sprintf;

/** @template-implements Iterator<int,DOMElement> */
abstract class ElementCollection implements Iterator {
=======

abstract class ElementCollection implements \Iterator {
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
    /** @var DOMElement[] */
    private $nodes = [];

    /** @var int */
    private $position;

    public function __construct(DOMNodeList $nodeList) {
        $this->position = 0;
        $this->importNodes($nodeList);
    }

<<<<<<< HEAD
    #[ReturnTypeWillChange]
=======
    #[\ReturnTypeWillChange]
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
    abstract public function current();

    public function next(): void {
        $this->position++;
    }

    public function key(): int {
        return $this->position;
    }

    public function valid(): bool {
<<<<<<< HEAD
        return $this->position < count($this->nodes);
=======
        return $this->position < \count($this->nodes);
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
    }

    public function rewind(): void {
        $this->position = 0;
    }

    protected function getCurrentElement(): DOMElement {
        return $this->nodes[$this->position];
    }

    private function importNodes(DOMNodeList $nodeList): void {
        foreach ($nodeList as $node) {
            if (!$node instanceof DOMElement) {
                throw new ElementCollectionException(
<<<<<<< HEAD
                    sprintf('\DOMElement expected, got \%s', get_class($node))
=======
                    \sprintf('\DOMElement expected, got \%s', \get_class($node))
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
                );
            }

            $this->nodes[] = $node;
        }
    }
}
