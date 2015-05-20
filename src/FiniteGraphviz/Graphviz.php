<?php

namespace FiniteGraphviz;

use Finite\StateMachine\StateMachineInterface;
use Finite\State\StateInterface;
use Alom\Graphviz\Digraph;
use Finite\Transition\TransitionInterface;

class Graphviz implements VisualisationInterface
{
    /**
     * the graphviz graph representation
     *
     * @var \Alom\Graphviz\Digraph
     */
    private $graph;

    /**
     * @var GraphVisitorInterface[]
     */
    private $visitors;

    /**
     * @param GraphVisitorInterface $visitor
     */
    public function addVisitor(GraphVisitorInterface $visitor)
    {
        $this->visitors[get_class($visitor)] = $visitor;
    }

    /**
     * Renders the state machine.
     *
     * @param  \Finite\StateMachine\StateMachineInterface $stateMachine
     *
     * @return string
     * @throws Exception
     */
    public function render(StateMachineInterface $stateMachine)
    {
        $this->graph = new Digraph('state_machine');
        $this->addNodes($stateMachine);
        $this->addEdges($stateMachine);
        $this->graph->end();

        return $this->graph->render();
    }

    /**
     * Adds the states as nodes.
     *
     * @param \Finite\StateMachine\StateMachineInterface $stateMachine
     */
    private function addNodes(StateMachineInterface $stateMachine)
    {
        $states = $stateMachine->getStates();
        foreach ($states as $name) {
            $state = $stateMachine->getState($name);
            $attributes = $this->getDefaultNodeAttributes($state, $stateMachine);
            if ($this->visitors) {
                try {
                    foreach ($this->visitors as $visitor) {
                        $attributes = $visitor->getNodeAttributes($attributes, $state, $stateMachine);
                    }
                }
                catch (SkipElementException $ex) {
                    continue;
                }
            }

            $this->graph->beginNode($name, $attributes)->end();
        }
    }

    /**
     * @param StateInterface $state
     * @param StateMachineInterface $stateMachine
     *
     * @return array
     */
    private function getDefaultNodeAttributes(StateInterface $state, StateMachineInterface $stateMachine)
    {
        return [
            'shape' => $state->getType() != StateInterface::TYPE_NORMAL ? 'doublecircle' : 'circle',
            'label' => $state->getName(),
        ];
    }

    /**
     * Adds all transitions as edges.
     *
     * @param \Finite\StateMachine\StateMachineInterface $stateMachine
     */
    private function addEdges(StateMachineInterface $stateMachine)
    {
        $states = $stateMachine->getStates();
        foreach ($states as $sName) {
            $state = $stateMachine->getState($sName);
            $transitions = $state->getTransitions();
            foreach ($transitions as $tName) {
                $trans = $stateMachine->getTransition($tName);
                $attributes = $this->getEdgeDefaultAttributes($trans);
                if ($this->visitors) {
                    try {
                        foreach ($this->visitors as $visitor) {
                            $attributes = $visitor->getEdgeAttributes($attributes, $trans, $state, $stateMachine);
                        }
                    }
                    catch (SkipElementException $ex) {
                        continue;
                    }
                }
                $this
                    ->graph
                    ->beginEdge([$state->getName(), $trans->getState()], $attributes)
                    ->end();
            }
        }
    }

    /**
     * Default attributes for an edge (transition)
     *
     * @param $trans
     *
     * @return array
     */
    private function getEdgeDefaultAttributes(TransitionInterface $trans)
    {
        return [
            'label' => $trans->getName()
        ];
    }

}
