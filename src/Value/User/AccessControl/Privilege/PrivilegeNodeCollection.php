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

        return new self($privilegeNodeIndex, ...$privilegeNodes);
    }

    public function contains(PurePrivilegeNode $node): bool
    {
        $fields = $node->asDestructured();

        $cache = $this->privilegeNodeIndex;
        foreach ($fields as $privilegeNode) {
            if ($node->isParentOf($privilegeNode->getNode())) {
                return true;
            }
        }
        return false;
    }

    private function containsNodeRecursive(array $currentLayer, array $searchPath): null|int {
        if (key_exists($searchPath[0], $currentLayer)) {
            $nextLayer = $currentLayer[$searchPath[0]];

            return $this->findNodeRecursive($nextLayer, array_slice($searchPath, 1));
        }
        return null;
    }

    // TODO: NOT FINISHED
    public function containsSomeNodes(PrivilegeNodeCollection $nodeCollection): bool
    {
        $privilegeNodes = array_filter($nodeCollection->privilegeNodes, function (PrivilegeNode $node) {
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
