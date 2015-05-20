<?php
namespace FiniteGraphviz\Visitor;

class PropertiesVisitor implements \FiniteGraphviz\GraphVisitorInterface {
    public function getNodeAttributes($attributes, \Finite\State\StateInterface $state, \Finite\StateMachine\StateMachineInterface $stateMachine)
    {
        $props = $state->getProperties();

        if (count($props) > 0) {
            foreach (array_keys($props) as $prop) {
                $attributes['label'] .= "\\n* " . $prop;
            }
        }

        return $attributes;
    }

    public function getEdgeAttributes($attributes, \Finite\Transition\TransitionInterface $trans, \Finite\State\StateInterface $fromState, \Finite\StateMachine\StateMachineInterface $stateMachine)
    {
        return $attributes;
    }
}