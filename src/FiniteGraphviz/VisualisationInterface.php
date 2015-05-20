<?php

namespace FiniteGraphviz;

use Finite\StateMachine\StateMachineInterface;

interface VisualisationInterface
{
    public function render(StateMachineInterface $stateMachine);
}
