<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Slim;

use Nebalus\Webapi\Api\Metrics\MetricsAction;
use Nebalus\Webapi\Api\Module\Linktree\Analytics\Click\ClickLinktreeAction;
use Nebalus\Webapi\Api\Module\Linktree\Delete\DeleteLinktreeAction;
use Nebalus\Webapi\Api\Module\Linktree\Edit\EditLinktreeAction;
use Nebalus\Webapi\Api\Module\Linktree\Get\GetLinktreeAction;
use Nebalus\Webapi\Api\Module\Referral\Analytics\Click\ClickReferralAction;
use Nebalus\Webapi\Api\Module\Referral\Analytics\ClickHistory\ClickHistoryReferralAction;
use Nebalus\Webapi\Api\Module\Referral\Create\CreateReferralAction;
use Nebalus\Webapi\Api\Module\Referral\Delete\DeleteReferralAction;
use Nebalus\Webapi\Api\Module\Referral\Edit\EditReferralAction;
use Nebalus\Webapi\Api\Module\Referral\Get\GetReferralAction;
use Nebalus\Webapi\Api\Module\Referral\GetAll\GetAllReferralAction;
use Nebalus\Webapi\Api\User\Auth\AuthUserAction;
use Nebalus\Webapi\Api\User\GetPrivileges\GetPrivilegesUserAction;
use Nebalus\Webapi\Api\User\Register\RegisterUserAction;
use Nebalus\Webapi\Option\EnvData;
use Nebalus\Webapi\Slim\Middleware\AuthMiddleware;
use Nebalus\Webapi\Slim\Middleware\CorsMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

readonly class RouteCollector
{
    public function __construct(
        private App $app,
        private EnvData $env
    ) {
    }

    public function init(): void
    {
        $this->app->addRoutingMiddleware();
        $this->app->addBodyParsingMiddleware();
        $this->app->add(CorsMiddleware::class);
        $this->registerErrorHandler();
        $this->initRoutes();
    }

    private function registerErrorHandler(): void
    {
        $errorMiddleware = $this->app->addErrorMiddleware($this->env->isDevelopment(), true, true);
    }

    private function initRoutes(): void
    {
        $this->app->group("/ui", function (RouteCollectorProxy $group) {
            $group->map(["POST"], "/auth", AuthUserAction::class);
            $group->map(["POST"], "/register", RegisterUserAction::class);
            $group->group("/admin", function (RouteCollectorProxy $group) {
                $group->group("/role/{roleName}", function (RouteCollectorProxy $group) {
                });
                $group->group("/user/{username}", function (RouteCollectorProxy $group) {
                });
            })->add(AuthMiddleware::class);
            $group->group("/user", function (RouteCollectorProxy $group) {
                $group->map(["GET"], "/privileges", GetPrivilegesUserAction::class);
                $group->group("/services", function (RouteCollectorProxy $group) {
                    $group->group("/invitation_tokens", function (RouteCollectorProxy $group) {
                    });
                    $group->group("/forms", function (RouteCollectorProxy $group) {
                    });
                    $group->group("/linktree", function (RouteCollectorProxy $group) {
                        $group->map(["GET"], "", GetLinktreeAction::class);
                        $group->map(["PUT"], "", EditLinktreeAction::class);
                        $group->map(["DELETE"], "", DeleteLinktreeAction::class);
                    });
                    $group->group("/referrals", function (RouteCollectorProxy $group) {
                        $group->map(["POST"], "", CreateReferralAction::class);
                        $group->map(["GET"], "/all", GetAllReferralAction::class);
                        $group->group("/{code}", function (RouteCollectorProxy $group) {
                            $group->map(["GET"], "", GetReferralAction::class);
                            $group->map(["PUT"], "", EditReferralAction::class);
                            $group->map(["DELETE"], "", DeleteReferralAction::class);
                            $group->map(["GET"], "/click_history", ClickHistoryReferralAction::class);
                        });
                    });
                });
            })->add(AuthMiddleware::class);
        });

        $this->app->map(["GET"], "/metrics", MetricsAction::class);

        $this->app->group("/services", function (RouteCollectorProxy $group) {
            $group->map(["GET"], "/referral/{code}", ClickReferralAction::class);
            $group->map(["GET"], "/linktree/{username}", ClickLinktreeAction::class);
        });
    }
}
