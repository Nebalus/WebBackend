<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Slim;

use Nebalus\Webapi\Api\Admin\Permission\Get\GetPermissionAction;
use Nebalus\Webapi\Api\Admin\Permission\GetAll\GetAllPermissionAction;
use Nebalus\Webapi\Api\Admin\Role\Create\AddRoleToUserAction;
use Nebalus\Webapi\Api\Admin\Role\Delete\DeleteRoleAction;
use Nebalus\Webapi\Api\Admin\Role\Edit\EditRoleAction;
use Nebalus\Webapi\Api\Admin\Role\Get\GetRoleAction;
use Nebalus\Webapi\Api\Admin\Role\GetAll\GetAllRoleAction;
use Nebalus\Webapi\Api\Admin\Role\Permission\Delete\RemoveRoleFromUserAction;
use Nebalus\Webapi\Api\Admin\Role\Permission\GetAll\GetAllRoleFromUserAction;
use Nebalus\Webapi\Api\Admin\Role\Permission\Upsert\UpsertRolePermissionAction;
use Nebalus\Webapi\Api\Metrics\MetricsAction;
use Nebalus\Webapi\Api\Module\Linktree\Click\ClickLinktreeAction;
use Nebalus\Webapi\Api\Module\Linktree\Delete\DeleteLinktreeAction;
use Nebalus\Webapi\Api\Module\Linktree\Edit\EditLinktreeAction;
use Nebalus\Webapi\Api\Module\Linktree\Get\GetLinktreeAction;
use Nebalus\Webapi\Api\Module\Referral\Analytics\Click\ClickReferralAction;
use Nebalus\Webapi\Api\Module\Referral\Analytics\ClickHistory\ClickHistoryReferralAction;
use Nebalus\Webapi\Api\Module\Blog\Create\CreateBlogAction;
use Nebalus\Webapi\Api\Module\Referral\Delete\DeleteReferralAction;
use Nebalus\Webapi\Api\Module\Referral\Edit\EditReferralAction;
use Nebalus\Webapi\Api\Module\Referral\Get\GetReferralAction;
use Nebalus\Webapi\Api\Module\Referral\GetAll\GetAllReferralAction;
use Nebalus\Webapi\Api\User\Auth\AuthUserAction;
use Nebalus\Webapi\Api\User\GetUserPermissions\GetUserPermissionsAction;
use Nebalus\Webapi\Api\User\Register\RegisterUserAction;
use Nebalus\Webapi\Config\GeneralConfig;
use Nebalus\Webapi\Slim\Handler\DefaultErrorHandler;
use Nebalus\Webapi\Slim\Middleware\AuthMiddleware;
use Nebalus\Webapi\Slim\Middleware\CorsMiddleware;
use Nebalus\Webapi\Slim\Middleware\MetricsMiddleware;
use Nebalus\Webapi\Slim\Middleware\PermissionMiddleware;
use Resend\Client;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

readonly class RouteCollector
{
    public function __construct(
        private App $app,
        private GeneralConfig $generalConfig,
    ) {
    }

    public function init(): void
    {
        $this->app->addRoutingMiddleware();
        $this->registerErrorHandler();
        $this->app->add(MetricsMiddleware::class);
        $this->app->addBodyParsingMiddleware();
        $this->app->add(CorsMiddleware::class);
        $this->initRoutes();
    }

    private function registerErrorHandler(): void
    {
        $errorMiddleware = $this->app->addErrorMiddleware($this->generalConfig->isDevelopment(), true, true);
        $errorMiddleware->setDefaultErrorHandler(DefaultErrorHandler::class);
    }

    private function initRoutes(): void
    {
        $this->app->group("/ui", function (RouteCollectorProxy $group) {
            $group->map(["POST"], "/auth", AuthUserAction::class);
            $group->map(["POST"], "/register", RegisterUserAction::class);
            $group->group("/admin", function (RouteCollectorProxy $group) {
                $group->group("/permission", function (RouteCollectorProxy $group) {
                    $group->map(["GET"], "/all", GetAllPermissionAction::class);
                    $group->group("/{permission_id}", function (RouteCollectorProxy $group) {
                        $group->map(["GET"], "", GetPermissionAction::class);
                    });
                });
                $group->group("/role", function (RouteCollectorProxy $group) {
                    $group->map(["GET"], "/all", GetAllRoleAction::class);
                    $group->map(["POST"], "", AddRoleToUserAction::class);
                    $group->group("/{role_id}", function (RouteCollectorProxy $group) {
                        $group->map(["GET"], "", GetRoleAction::class);
                        $group->map(["PUT"], "", EditRoleAction::class);
                        $group->map(["DELETE"], "", DeleteRoleAction::class);
                        $group->group("/permission", function (RouteCollectorProxy $group) {
                            $group->map(["GET"], "/all", GetAllRoleFromUserAction::class);
                            $group->map(["DELETE"], "", RemoveRoleFromUserAction::class);
                            $group->map(["PUT", "POST"], "", UpsertRolePermissionAction::class);
                        });
                    });
                });
            })->add(PermissionMiddleware::class)->add(AuthMiddleware::class);
            $group->group("/user/{user_id}", function (RouteCollectorProxy $group) {
                $group->map(["GET"], "/permissions", GetUserPermissionsAction::class);
                $group->group("/services", function (RouteCollectorProxy $group) {
                    $group->group("/invitation_tokens", function (RouteCollectorProxy $group) {
                    });
                    $group->group("/blogs", function (RouteCollectorProxy $group) {
                    });
                    $group->group("/linktree", function (RouteCollectorProxy $group) {
                        $group->map(["GET"], "", GetLinktreeAction::class);
                        $group->map(["PUT"], "", EditLinktreeAction::class);
                        $group->map(["DELETE"], "", DeleteLinktreeAction::class);
                    });
                    $group->group("/referrals", function (RouteCollectorProxy $group) {
                        $group->map(["POST"], "", CreateBlogAction::class);
                        $group->map(["GET"], "/all", GetAllReferralAction::class);
                        $group->group("/{code}", function (RouteCollectorProxy $group) {
                            $group->map(["GET"], "", GetReferralAction::class);
                            $group->map(["PUT"], "", EditReferralAction::class);
                            $group->map(["DELETE"], "", DeleteReferralAction::class);
                            $group->map(["GET"], "/click_history", ClickHistoryReferralAction::class);
                        });
                    });
                });
            })->add(PermissionMiddleware::class)->add(AuthMiddleware::class);
        });

        $this->app->map(["GET"], "/metrics", MetricsAction::class);

        $this->app->group("/services", function (RouteCollectorProxy $group) {
            $group->map(["GET"], "/referral/{code}", ClickReferralAction::class);
            $group->map(["GET"], "/linktree/{username}", ClickLinktreeAction::class);
        });
    }
}
