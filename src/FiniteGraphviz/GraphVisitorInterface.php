<?php
namespace FiniteGraphviz;

use Finite\State\StateInterface;
use Finite\StateMachine\StateMachineInterface;
use Finite\Transition\TransitionInterface;

interface GraphVisitorInterface {
    public function getNodeAttributes($attributes, StateInterface $state, StateMachineInterface $stateMachine);
    public function getEdgeAttributes($attributes, TransitionInterface $trans, StateInterface $fromState, StateMachineInterface $stateMachine);
}