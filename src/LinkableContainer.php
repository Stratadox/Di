<?php

namespace Stratadox\Di;

interface LinkableContainer extends Container
{
    /**
     * Links an abstraction to a concrete class.
     *
     * @param string $interface           The interface or abstract class.
     * @param string $class               The concrete class to link to.
     * @return LinkableContainer The updated container.
     * @throws InvalidServiceDefinition   When the link is invalid.
     */
    public function link(string $interface, string $class): LinkableContainer;
}
