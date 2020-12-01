<?php


use Slim\Routing\RouteCollectorProxy;
use Slim\Factory\AppFactory;
use Config\Database;
use App\Controllers\UsuarioController;
use App\Controllers\LoginController;
use App\Controllers\TurnoController;
use App\Controllers\MascotaController;
use App\Controllers\FacturaController;
use App\Middlewares\JsonMiddleware;
use App\Middlewares\AuthMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$conn = new Database;

$app = AppFactory::create();

$app->setBasePath('/Recu_SP_PerezYanina/public');


$app->group('/users', function (RouteCollectorProxy $group) {
    $group->get('[/]', UsuarioController::class . ":getAll")->add(new AuthMiddleware("admin"));
    $group->post('[/]', UsuarioController::class . ":addOne");
})
    ->add(new JsonMiddleware);



$app->group('/login', function (RouteCollectorProxy $group) {
    $group->post('[/]', LoginController::class . ":login");
})
    ->add(new JsonMiddleware);



$app->group('/mascota', function (RouteCollectorProxy $group) {
    $group->get('[/]', MascotaController::class . ":getAll")->add(new AuthMiddleware("admin"))
        ->add(new JsonMiddleware);
    $group->post('[/]', MascotaController::class . ":addOne")->add(new AuthMiddleware("admin"))
        ->add(new JsonMiddleware);
})
    ->add(new JsonMiddleware);

    
$app->group('/turno', function (RouteCollectorProxy $group) {
    $group->put('/{idTurno}', TurnoController::class . ":getTurno")
        ->add(new AuthMiddleware("admin"));
    $group->post('[/]', TurnoController::class . ":addOne")
        ->add(new AuthMiddleware("cliente"));
})
    ->add(new JsonMiddleware);


    
$app->get('/factura[/]', FacturaController::class . ':getAll')
    ->add(new AuthMiddleware('cliente'))->add(new JsonMiddleware);


$app->get('/turnos[/]', TurnoController::class . ':getAll')->add(new AuthMiddleware("admin"))
->add(new JsonMiddleware);


$app->addBodyParsingMiddleware();
$app->run();
