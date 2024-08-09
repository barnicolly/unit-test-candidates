<?php

declare(strict_types=1);

namespace App;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;

final class Parser
{
    public function calculateTestabilityScore(array $ast): array
    {
        /** @var ClassMethod[] $publicClassMethods */
        $publicClassMethods = $this->getPublicClassMethods($ast);

        $testabilityScoreResults = [];
        foreach ($publicClassMethods as $publicClassMethod) {
            $nodeTraverser = new NodeTraverser();

            $testabilityScoreCounter = new TestabilityScoreCounter();

            $nodeTraverser->addVisitor(new ActionControllerNodeVisitor($testabilityScoreCounter));
            $nodeTraverser->traverse([$publicClassMethod]);

            $methodName = $publicClassMethod->name->toString();
            $testabilityScoreResults[$methodName] = $testabilityScoreCounter->getScore();
        }
        return $testabilityScoreResults;
    }

    private function getPublicClassMethods(array $ast): array
    {
        return (new NodeFinder())->find($ast, function (Node $node) {
            if (!$node instanceof ClassMethod) {
                return false;
            }
            return $node->isPublic();
        });
    }
}
