<?php

namespace Nebalus\Webapi\Collector;

use Nebalus\Webapi\Controller\Linktree\LinktreeApiCreateController;
use Nebalus\Webapi\Controller\Linktree\LinktreeApiDeleteController;
use Nebalus\Webapi\Controller\Linktree\LinktreeApiReadController;
use Nebalus\Webapi\Controller\Linktree\LinktreeApiUpdateController;
use Nebalus\Webapi\Controller\Referral\ReferralApiCreateController;
use Nebalus\Webapi\Controller\Referral\ReferralApiDeleteController;
use Nebalus\Webapi\Controller\Referral\ReferralApiGetController;
use Nebalus\Webapi\Controller\Referral\ReferralApiUpdateController;
use Nebalus\Webapi\Controller\TempController;
use Nebalus\Webapi\Handler\DefaultErrorHandler;
use Nebalus\Webapi\Middleware\AuthenticationMiddleware;
use Slim\App;
use Slim\Exception\HttpException;
use Slim\Routing\RouteCollectorProxy;

class RouteCollector
{
    private App $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function init(): void
    {
        $this->app->addRoutingMiddleware();

        $this->initRoutes();
        $this->initErrorMiddleware();
    }

    private function initErrorMiddleware(): void
    {
        // Definiert die ErrorMiddleware
        $isDevelopment = (strtolower(getenv("APP_ENV")) === "development");
        $isProduction = (strtolower(getenv("APP_ENV")) === "production");

        $errorMiddleware = $this->app->addErrorMiddleware($isDevelopment, true, true);

        if ($isProduction || true) {
            $errorMiddleware->setDefaultErrorHandler(DefaultErrorHandler::class);
        }
    }

    private function initRoutes(): void
    {
        // Definiert die Route
        $this->app->get("/users", [TempController::class, "action"]);
        $this->app->group("/linktree", function (RouteCollectorProxy $group) {
            $group->post("/create", [LinktreeApiCreateController::class, "action"]);
            $group->get("/read", [LinktreeApiReadController::class, "action"]);
            $group->post("/update", [LinktreeApiUpdateController::class, "action"]);
            $group->delete("/delete", [LinktreeApiDeleteController::class, "action"]);
        });
        $this->app->group("/referral", function (RouteCollectorProxy $group) {
            $group->map(["PUT"], "/create", [ReferralApiCreateController::class, "action"]);
            $group->map(["GET"], "/get", [ReferralApiGetController::class, "action"]);
            $group->map(["POST"], "/update", [ReferralApiUpdateController::class, "action"]);
            $group->map(["DELETE"], "/delete", [ReferralApiDeleteController::class, "action"]);
        });
        $this->app->group("/games", function (RouteCollectorProxy $group) {
            $group->group("/cosmoventure", function (RouteCollectorProxy $group) {
                $group->get("/version", [TempController::class, "action"]);
            });
        });

        $this->app->add(AuthenticationMiddleware::class);
    }
}
