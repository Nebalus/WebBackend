<?php

namespace Nebalus\Webapi\Value\User\AccessControl\Privilege;

use IteratorAggregate;

class PrivilegeNodeCollection implements IteratorAggregate
{
    private array $nodeIndex = [];
    private array $privilegeNodes = [];

    private function __construct(array $nodeIndex, PrivilegeNode ...$privilegeNodes)
    {
        $this->nodeIndex = $nodeIndex;
        $this->privilegeNodes = $privilegeNodes;
    }

    public static function fromObjects(PrivilegeNode ...$privilegeNodes): self
    {
        $cache = [];
        foreach ($privilegeNodes as $privilegeNode) {
            $cache[] = self::destructureNode($privilegeNode);
        }

        $nodeIndex = array_replace_recursive([], ...$cache);

        return new self($nodeIndex, ...$privilegeNodes);
    }

    public function contains(PrivilegeNode $node): bool
    {
        foreach ($this->privilegeNodes as $privilegeNode) {
            if (str_starts_with($privilegeNode->getNode(), $node->getNode())) {
                return true;
            }
        }
        return false;
    }

    // TODO: NOT FINISHED
    public function containsSomeNodes(PrivilegeNodeCollection $nodeCollection): bool
    {
        $nodeCollection->privilegeNodes = array_filter($nodeCollection->privilegeNodes, function (PrivilegeNode $node) {
            return $this->contains($node);
        });
        foreach ($this->privilegeNodes as $privilegeNode) {
            if (str_starts_with($privilegeNode->getNode(), $node->asString())) {
                return true;
            }
        }
        return false;
    }

    public function getIterator(): \Traversable
    {
        yield from $this->privilegeNodes;
    }

    private static function destructureNode(PrivilegeNode $node): array
    {
        $nodeAsString = $node->getNode();
        $nodeAsArray = explode('.', $nodeAsString);

        $finalArray = [];

        $ref = &$finalArray;
        foreach ($nodeAsArray as $key) {
            $ref = &$ref[$key];
        }
        $ref = $node->getValue();
        return $finalArray; // DO NOT REMOVE // THIS IS THE FINAL RETURN VALUE
    }
}
