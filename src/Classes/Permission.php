<?php

namespace KikCMS\Classes;


use KikCMS\Classes\Frontend\Extendables\WebsiteSettingsBase;
use KikCMS\Classes\Phalcon\AccessControl;
use KikCMS\DataTables\Languages;
use KikCMS\DataTables\Pages;
use KikCMS\DataTables\Translations;
use KikCMS\DataTables\Users;
use KikCMS\Services\UserService;
use Phalcon\Acl;
use Phalcon\Acl\Resource;
use Phalcon\Acl\Role;
use Phalcon\Di\Injectable;

/**
 * @property UserService $userService
 * @property WebsiteSettingsBase $websiteSettings
 */
class Permission extends Injectable
{
    const DEVELOPER = 'developer';
    const ADMIN     = 'admin';
    const USER      = 'user';
    const CLIENT    = 'client';
    const VISITOR   = 'visitor';

    const ACCESS_ANY    = '*';
    const ACCESS_ADD    = 'add';
    const ACCESS_DELETE = 'delete';
    const ACCESS_EDIT   = 'edit';
    const ACCESS_VIEW   = 'view';

    const ACCESS_DATATABLES = 'AccessDataTables';
    const ACCESS_FINDER     = 'AccessFinder';
    const PAGE_MENU         = 'pageMenu';
    const PAGE_KEY          = 'pageKey';

    const ROLES = [
        self::DEVELOPER,
        self::ADMIN,
        self::USER,
        self::CLIENT,
        self::VISITOR,
    ];

    /**
     * @return AccessControl
     */
    public function getAcl()
    {
        if (isset($this->persistent->acl) && ! $this->persistent->acl->requiresUpdate()) {
            return $this->persistent->acl;
        }

        $acl = new AccessControl($this->getCurrentRole());

        $acl->setDefaultAction(Acl::DENY);

        $acl->addRole(new Role(self::DEVELOPER));
        $acl->addRole(new Role(self::ADMIN));
        $acl->addRole(new Role(self::USER));
        $acl->addRole(new Role(self::CLIENT));

        $this->addDataTablePermissions($acl);
        $this->addMenuPermissions($acl);
        $this->addPagePermissions($acl);

        $this->websiteSettings->addPermissions($acl);

        $acl->update();

        $this->persistent->acl = $acl;

        return $acl;
    }

    /**
     * Get the role of the current logged in user, if not logged in, the role is visitor
     *
     * @return string
     */
    public function getCurrentRole(): string
    {
        $role = $this->session->get('role');

        if ( ! $role) {
            return Permission::VISITOR;
        }

        return $role;
    }

    /**
     * @param AccessControl $acl
     */
    private function addDataTablePermissions(AccessControl $acl)
    {
        $acl->addResource(self::ACCESS_DATATABLES);
        $acl->addResource(self::ACCESS_FINDER);
        $acl->addResource(Languages::class);
        $acl->addResource(Pages::class);
        $acl->addResource(Translations::class);
        $acl->addResource(Users::class);

        $acl->allow(self::DEVELOPER, self::ACCESS_DATATABLES);
        $acl->allow(self::ADMIN, self::ACCESS_DATATABLES);
        $acl->allow(self::USER, self::ACCESS_DATATABLES);
        $acl->allow(self::CLIENT, self::ACCESS_DATATABLES);

        $acl->allow(self::DEVELOPER, self::ACCESS_FINDER);
        $acl->allow(self::ADMIN, self::ACCESS_FINDER);
        $acl->allow(self::USER, self::ACCESS_FINDER);

        $acl->allow(self::DEVELOPER, Pages::class);
        $acl->allow(self::ADMIN, Pages::class);
        $acl->allow(self::USER, Pages::class);

        $acl->allow(self::DEVELOPER, Translations::class);
        $acl->allow(self::ADMIN, Translations::class);
        $acl->allow(self::USER, Translations::class);

        $acl->allow(self::DEVELOPER, Users::class);
        $acl->allow(self::ADMIN, Users::class);

        $acl->allow(self::DEVELOPER, Languages::class);
    }

    /**
     * @param AccessControl $acl
     */
    private function addMenuPermissions(AccessControl $acl)
    {
        $acl->addResource(new Resource(self::PAGE_MENU), self::ACCESS_ANY);
        $acl->allow(self::DEVELOPER, self::PAGE_MENU, self::ACCESS_ANY);
    }

    /**
     * @param AccessControl $acl
     */
    private function addPagePermissions(AccessControl $acl)
    {
        $acl->addResource(new Resource(self::PAGE_KEY), self::ACCESS_ANY);
        $acl->allow(self::DEVELOPER, self::PAGE_KEY, self::ACCESS_ANY);
    }
}