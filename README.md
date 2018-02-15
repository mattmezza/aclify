# aclify
A simple and plain ACL component based on a YAML file.
## Specs
Aclify is a really simple component that halps you manage roles and abilities for a small set of users.

Have you ever been in the context of a small projects that requires no database, no authentication (cause it is accomplished using an external provider), small set of roles and abilities???

If you have, well, this package is for you.

Aclify allows you to define roles, each with a set of abilities (operations you can do if you belong to the role). It also allows you to specify for each user, a set of roles.

It can be used in a tight way, coupled with your User object representation or it can be also used as an external component you call specifying the user each time.

## Installation

`composer require mattmezza/aclify`

## Usage 

Write down your users and roles with abilities in a YAML file as follows:

```yaml
roles: 
  billing: 
    - payments
  marketing: 
    - mailchimp
    - facebook
  support: 
    - cms_tools
users: 
  mary: 
    - marketing
    - billing
    - support
  gigi: 
    - support

```
We specified in this way three roles: `billing` (users with this role can manage the `payments`), `marketing` (users with this role can access to tools like `mailchimp` or `facebook`) and `support` (users with this role can access the `cms_tools`).

Then we specified the users allowed: `mary` is an advanced user, she can check pretty much everything (`marketing`, `support`, `billing`) while `gigi` is a basic user with just the `support` abilities enabled.

### Usage tight to your User object
If you wanna use Aclify tight with your User object representation you can do that by extending the abstract class `Aclify\ACLUser` which forces you to define two methods (user id and acl object retrieval - you can use your project's dependency injection component) and gives you some inherited methods to check whether the `User` can or cannot use some abilities.

Define your User object as follows (you can add your methods to the class): 

```php
use Aclify\ACL;

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
```

Then use it in this way:

```php
$acl = new Aclify\ACL();
$gigi = new User($acl, "gigi@domain.com");
if ($gigi->can("create_post"))
{
    // ...
}
```

### Usage not tight to the User object

If you don't wanna bind your user representation to the component you can avoid extending the class provided `ACLUser` and you can just use `ACL` instead, pls do as follow:

```php
$acl = new Aclify\ACL();
if ($acl->can("gigi@domain.com", "create_post"))
{
    // ...
}
```

### Exceptions

When instanciating the `ACL` class, the component tries to read the specs from a default `./acl.yml` file. If you wanna specify a different file you can pass the file path as a parameter in the contructor. If the file is not readable or not found (or not a yaml file) an exception will be thrown, so better use it in this way:

```php
use Aclify\ACL;
use Aclify\Exceptions\MissingACLSpecsFile;

try {
    $acl = new ACL("./config/acl-new.yml");
    // ...
} catch (MissingACLSpecsFile $e) {
    // do something with $e
}