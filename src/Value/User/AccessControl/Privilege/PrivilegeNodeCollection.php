<?php

namespace Nebalus\Webapi\Value\User\AccessControl\Privilege;

use IteratorAggregate;

class PrivilegeNodeCollection implements IteratorAggregate
{
    private array $privilegeNodeIndex = [];
    private array $privilegeNodes = [];

    private function __construct(array $privilegeNodeIndex, PrivilegeNode ...$privilegeNodes)
    {
        $this->privilegeNodeIndex = $privilegeNodeIndex;
        $this->privilegeNodes = $privilegeNodes;
    }

    public static function fromObjects(PrivilegeNode ...$privilegeNodes): self
    {
        $cache = [];
        foreach ($privilegeNodes as $privilegeNode) {
            $cache[] = $privilegeNode->getNode()->asDestructured();
        }

        $privilegeNodeIndex = array_replace_recursive([], ...$cache);

        var_dump($privilegeNodeIndex);

        return new self($privilegeNodeIndex, ...$privilegeNodes);
    }

    public function contains(PurePrivilegeNode $node): bool
    {
        foreach ($this->privilegeNodes as $privilegeNode) {
            if ($node->isParentOf($privilegeNode->getNode())) {
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
}
