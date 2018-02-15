<?php
declare(strict_types=1);

namespace Aclify;

abstract class ACLUser {

    abstract public function getId() : string;

    abstract public function getACL() : ACL;

    public function can(string $ability) : bool
    {
        return $this->getACL()->can($this->getId(), $ability);
    }

    public function cannot(string $ability) : bool
    {
        return $this->getACL()->cannot($this->getId(), $ability);
    }

    public function roles() : array
    {
        return $this->getACL()->rolesOf($this->getId());
    }

    public function abilities() : array
    {
        return (array) $this->getACL()->abilitiesOf($this->getId());
    }
}