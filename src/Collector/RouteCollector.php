<?php

namespace Nebalus\Ownsite\Collector;

use Nebalus\Ownsite\Controller\DocsController;
use Nebalus\Ownsite\Controller\HomeController;
use Nebalus\Ownsite\Controller\Referral\Api\ReferralApiCreateController;
use Nebalus\Ownsite\Controller\Referral\Api\ReferralApiDeleteController;
use Nebalus\Ownsite\Controller\Referral\Api\ReferralApiReadController;
use Nebalus\Ownsite\Controller\Referral\Api\ReferralApiUpdateController;
use Nebalus\Ownsite\Controller\Referral\ReferralController;
use Nebalus\Ownsite\Controller\Linktree\Api\LinktreeApiCreateController;
use Nebalus\Ownsite\Controller\Linktree\Api\LinktreeApiDeleteController;
use Nebalus\Ownsite\Controller\Linktree\Api\LinktreeApiReadController;
use Nebalus\Ownsite\Controller\Linktree\Api\LinktreeApiUpdateController;
use Nebalus\Ownsite\Controller\Linktree\LinktreeController;
use Nebalus\Ownsite\Controller\TempController;
use Nebalus\Ownsite\Middleware\JsonValidatorMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Exception\HttpNotFoundException;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

class RouteCollector
{
    private App $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function initRoutes(): void
    {
        $this->app->addRoutingMiddleware();

        // Definiert die ErrorMiddleware
        $displayErrorDetails = (strtolower(getenv("APP_ENV")) === "development");
        $errorMiddleware = $this->app->addErrorMiddleware($displayErrorDetails, true, true);
        /*
        $errorMiddleware->setErrorHandler(HttpNotFoundException::class, function (Request $request) use ($this->app) {
            $view = Twig::fromRequest($request);
            $response = $app->getResponseFactory()->createResponse();
            return $view->render($response, 'codes/404.twig')->withStatus(404);
        });
        */

        // Definiert die Route
        $this->app->group("/api", function (RouteCollectorProxy $group) {
            $group->get("/account", [TempController::class, "action"]);
            $group->group("/linktree", function (RouteCollectorProxy $group) {
                $group->post("/create", [LinktreeApiCreateController::class, "action"]);
                $group->get("/read", [LinktreeApiReadController::class, "action"]);
                $group->post("/update", [LinktreeApiUpdateController::class, "action"]);
                $group->delete("/delete", [LinktreeApiDeleteController::class, "action"]);
            });
            $group->group("/referral", function (RouteCollectorProxy $group) {
                $group->post("/create", [ReferralApiCreateController::class, "action"]);
                $group->get("/read", [ReferralApiReadController::class, "action"]);
                $group->post("/update", [ReferralApiUpdateController::class, "action"]);
                $group->delete("/delete", [ReferralApiDeleteController::class, "action"]);
            });
            $group->group("/game", function (RouteCollectorProxy $group) {
                $group->group("/cosmoventure", function (RouteCollectorProxy $group) {
                    $group->get("/version", [TempController::class, "action"]);
                });
            });
        })->add(JsonValidatorMiddleware::class);

        $this->app->group("/u", function (RouteCollectorProxy $group) {
            $group->get("/{username}", [TempController::class, "action"]);
        });

        $this->app->group("/account", function (RouteCollectorProxy $group) {
            $group->get("/register", [TempController::class, "action"]);
            $group->get("/login", [TempController::class, "action"]);
            $group->get("/dashboard", [TempController::class, "action"]);
        });

        $this->app->group("/terms", function (RouteCollectorProxy $group) {
            $group->get("/privacy", [TempController::class, "action"]);
        });

        $this->app->group("/projects", function (RouteCollectorProxy $group) {
            $group->get("/mandelbrot", [TempController::class, "action"]);
            $group->get("/oriri", [TempController::class, "action"]);
            $group->get("/cosmoventure", [TempController::class, "action"]);
            $group->get("/melody", [TempController::class, "action"]);
        });
        
        $this->app->get("/", [HomeController::class, "action"]);
        $this->app->get("/docs", [DocsController::class, "action"]);
        $this->app->get("/linktree", [LinktreeController::class, "action"]);
        $this->app->get("/ref", [ReferralController::class, "action"]);
    }
}
