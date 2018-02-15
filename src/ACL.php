<?php
declare(strict_types=1);

namespace Aclify;

use Symfony\Component\Yaml\Yaml;
use Aclify\Exceptions\MissingACLSpecsFile;
use Illuminate\Support\Collection;

class ACL
{
    private $specs;

    public function __construct($filename = './acl.yml')
    {
        try {
            $this->specs = Yaml::parseFile($filename);
        } catch (\Exception $e) {
            throw new MissingACLSpecsFile($filename);
        }
    }

    public function abilities(string $role) : array
    {
        return $this->specs["roles"][$role];
    }

    public function roles() : array
    {
        return collect($this->specs["roles"])->keys();
    }

    public function rolesOf(string $user) : array 
    {
        return $this->specs["users"][$user];
    }

    public function abilitiesOf(string $user) : array
    {
        return collect($this->rolesOf($user))->map(function ($role) {
            return $this->abilities($role);
        })->flatten()->unique()->toArray();
    }

    public function can(string $user, string $ability) : bool
    {
        return collect($this->abilitiesOf($user))->contains($ability);
    }

    public function cannot(string $user, string $ability) : bool
    {
        return !$this->can($user, $ability);
    }
}
