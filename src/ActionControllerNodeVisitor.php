<?php

declare(strict_types=1);

namespace App;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitorAbstract;

final class ActionControllerNodeVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private readonly TestabilityScoreCounter $testabilityScoreCounter
    ) {
    }

    public function enterNode(Node $node): ?Node
    {
        if (!$node instanceof ClassMethod) {
            return null;
        }

        // there is no return type → skip it
        if (!$node->returnType instanceof Name) {
            // no return type
            return null;
        }

        $returnedClass = $node->returnType->toString();

        // check against your favorite framework "Response" class name
        if ('Response' !== $returnedClass) {
            return null;
        }

        // now we know the returns a response → give it a penalty of 1000 points
        $this->testabilityScoreCounter->increase(1000);
        return $node;
    }
}
