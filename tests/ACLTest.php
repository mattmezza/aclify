<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Aclify\ACL;
use Aclify\Exceptions\MissingACLSpecsFile;
use Aclify\ACLUser;

class ACLTest extends TestCase
{

    private $acl;

    public function setup() : void
    {
        $this->acl = new ACL(__DIR__ . "/acl.yml");
    }

    public function testRolesOfBase() : void
    {
        $roles = $this->acl->rolesOf("base");
        $this->assertEquals($roles, ["support"]);
    }

    public function testRolesOfAdvanced() : void
    {
        $roles = $this->acl->rolesOf("advanced");
        $this->assertEquals($roles, ["marketing","billing","support"]);
    }

    public function testAbilitiesOfBilling() : void
    {
        $abilities = $this->acl->abilities("billing");
        $this->assertEquals($abilities, ["payments", "pluto"]);
    }

    public function testNoFile() : void
    {
        $this->expectException(MissingACLSpecsFile::class);
        new ACL("pipppppppppppo.yml");
    }

    public function testBaseUserCanDoAbilityPippo() : void
    {
        $this->assertTrue($this->acl->can("base", "pippo"));
    }

    public function testBaseUserCanDoAbilityPluto() : void
    {
        $this->assertFalse($this->acl->can("base", "pluto"));
    }

    public function testBaseUserCannotDoAbilityPippo() : void
    {
        $this->assertFalse($this->acl->cannot("base", "pippo"));
    }

    public function testBaseUserCannotDoAbilityPluto() : void
    {
        $this->assertTrue($this->acl->cannot("base", "pluto"));
    }

    public function testAdvancedUserCanDoAbilityPippo() : void
    {
        $this->assertTrue($this->acl->can("advanced", "pippo"));
    }

    public function testAdvancedUserCanDoAbilityPluto() : void
    {
        $this->assertTrue($this->acl->can("advanced", "pluto"));
    }

    public function testAdvancedUserCannotDoAbilityPippo() : void
    {
        $this->assertFalse($this->acl->cannot("advanced", "pippo"));
    }

    public function testAdvancedUserCannotDoAbilityPluto() : void
    {
        $this->assertFalse($this->acl->cannot("advanced", "pluto"));
    }

    public function testBaseUserCanDoPippoCannotDoPluto() : void
    {
        $base = new User($this->acl, "base");
        $this->assertTrue($base->can("pippo"));
        $this->assertFalse($base->can("pluto"));
    }

    public function testAdvancedUserCanDoPippoCanDoPluto() : void
    {
        $base = new User($this->acl, "advanced");
        $this->assertTrue($base->can("pippo"));
        $this->assertTrue($base->can("pluto"));
    }

    public function testBaseUserGetRoles() : void
    {
        $base = new User($this->acl, "base");
        $roles = $base->roles();
        $this->assertEquals($roles, ["support"]);
    }

    public function testBaseUserGetAbilities() : void
    {
        $base = new User($this->acl, "base");
        $abilities = $base->abilities();
        $this->assertEquals($abilities, ["cms_tools", "pippo"]);
    }

    public function testAdvancedUserGetAbilities() : void
    {
        $advanced = new User($this->acl, "advanced");
        $abilities = $advanced->abilities();
        $this->assertEquals($abilities, ["mailchimp", "facebook", "payments", "pluto", "cms_tools", "pippo"]);
    }

}

class User extends ACLUser
{
    private $acl;
    private $id;
    public function __construct(ACL $acl, string $id) 
    {
        $this->acl = $acl;
        $this->id = $id;
    }
    public function getACL() : ACL
    {
        return $this->acl;
    }
    public function getId() : string
    {
        return $this->id;
    }
}
