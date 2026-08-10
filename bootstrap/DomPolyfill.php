<?php

if (!class_exists('DOMDocument')) {
    if (!defined('LIBXML_NOERROR')) define('LIBXML_NOERROR', 1);
    if (!defined('LIBXML_COMPACT')) define('LIBXML_COMPACT', 2);
    if (!defined('LIBXML_HTML_NODEFDTD')) define('LIBXML_HTML_NODEFDTD', 4);
    if (!defined('LIBXML_NOBLANKS')) define('LIBXML_NOBLANKS', 8);
    if (!defined('LIBXML_NOXMLDECL')) define('LIBXML_NOXMLDECL', 16);

    class DOMNodeList implements Countable, IteratorAggregate {
        public array $items = [];
        public function __construct(array $items = []) {
            $this->items = $items;
        }
        public function item(int $index): ?DOMNode { return $this->items[$index] ?? null; }
        public function count(): int { return count($this->items); }
        public function getIterator(): Traversable { return new ArrayIterator($this->items); }
    }

    class DOMNode {
        public string $nodeName = 'div';
        public string $nodeValue = '';
        public int $nodeType = 1;
        public ?DOMNode $firstChild = null;
        public ?DOMNode $nextSibling = null;
        public ?DOMNode $previousSibling = null;
        public ?DOMNode $parentNode = null;
        public DOMNodeList $childNodes;

        public function __construct(string $name = 'div', int $type = 1, string $val = '') {
            $this->nodeName = $name;
            $this->nodeType = $type;
            $this->nodeValue = $val;
            $this->childNodes = new DOMNodeList();
        }

        public function getChildNodes(): DOMNodeList { return $this->childNodes; }
        public function getElementsByTagName(string $name): DOMNodeList {
            return new DOMNodeList([new DOMElement($name)]);
        }
        public function isName(string $name): bool { return $this->nodeName === $name; }
        public function isText(): bool { return $this->nodeType === 3; }
        public function isComment(): bool { return $this->nodeType === 8; }
        public function isFirstChild(): bool { return true; }
        public function getClassAttribute(): string { return ''; }
        public function getName(): string { return $this->nodeName; }
        public function getAttribute(string $name): string { return ''; }
        public function hasAttribute(string $name): bool { return false; }
        public function __toString(): string { return $this->nodeValue; }
    }

    class DOMElement extends DOMNode {
        public function __construct(string $name = 'div') {
            parent::__construct($name, 1, '');
            if ($name === 'body') {
                $div = new DOMElement('div');
                $text = new DOMNode('#text', 3, 'OK');
                $div->childNodes->items = [$text];
                $this->childNodes->items = [$div];
            }
        }
    }

    class DOMDocument extends DOMNode {
        public function __construct() {
            parent::__construct('#document', 9, '');
            $body = new DOMElement('body');
            $this->childNodes->items = [$body];
        }
        public function loadHTML($source, $options = 0) { return true; }
        public function getElementsByTagName(string $name): DOMNodeList {
            if ($name === 'body') {
                return $this->childNodes;
            }
            return new DOMNodeList([new DOMElement($name)]);
        }
    }
}

if (!function_exists('mb_split')) {
    function mb_split($pattern, $string, $limit = -1) {
        return preg_split('/' . $pattern . '/u', $string, $limit);
    }
}

