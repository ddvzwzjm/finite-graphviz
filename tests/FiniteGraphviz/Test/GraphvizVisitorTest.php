<?php
namespace FiniteGraphviz\Test;

use Finite\State\State;
use Finite\StateMachine\StateMachine;
use FiniteGraphviz\Configuration;
use FiniteGraphviz\GraphVisitorInterface;
use FiniteGraphviz\Graphviz;
use FiniteGraphviz\SkipElementException;

class GraphvizVisitorTest extends \PHPUnit_Framework_TestCase
{
    protected function providerGraphviz()
    {
        $config = new Configuration(false);
        return  new Graphviz($config);
    }

    protected function mockVisitor()
    {
        $visitor = $this
            ->getMockBuilder(GraphVisitorInterface::class)
            ->setMethods(['getNodeAttributes', 'getEdgeAttributes'])
            ->getMock();

        $visitor->expects($this->any())->method('getNodeAttributes')->willReturnArgument(0);
        $visitor->expects($this->any())->method('getEdgeAttributes')->willReturnArgument(0);

        return $visitor;
    }

    public function testIsVisitingNodesAndEdges()
    {
        $visitor = $this->mockVisitor();
        $visitor
            ->expects($this->exactly(2))
            ->method('getNodeAttributes');
        $visitor
            ->expects($this->exactly(1))
            ->method('getEdgeAttributes');


        $g = $this->providerGraphviz();
        $sm = new StateMachine();
        $sm->addState(new State('s1', State::TYPE_INITIAL));
        $sm->addState(new State('s2', State::TYPE_FINAL));
        $sm->addTransition('t12', 's1', 's2');

        $g->addVisitor($visitor);

        $g->render($sm);
    }

    public function testSkipNodeException()
    {
        $visitor = $this->mockVisitor();
        $visitor->expects($this->once())
            ->method('getNodeAttributes')
            ->willThrowException(new SkipElementException());

        $g = $this->providerGraphviz();
        $sm = new StateMachine();
        $sm->addState(new State('s1', State::TYPE_INITIAL));
        $g->addVisitor($visitor);
        $this->assertNotContains('"s1"', $g->render($sm));
    }

    public function testSkipEdgeException()
    {
        $visitor = $this->mockVisitor();
        $visitor->expects($this->once())
                ->method('getEdgeAttributes')
                ->willThrowException(new SkipElementException());

        $g = $this->providerGraphviz();
        $sm = new StateMachine();
        $sm->addState(new State('s1', State::TYPE_INITIAL));
        $sm->addState(new State('s2', State::TYPE_FINAL));
        $sm->addTransition('t12', 's1', 's2');
        $g->addVisitor($visitor);
        $dot = $g->render($sm);
        $this->assertNotContains('"t12"', $dot);
        $this->assertContains('"s1"', $dot);
        $this->assertContains('"s2"', $dot);
    }

}
