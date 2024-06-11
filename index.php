<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Middleware\Authentication\AuthenticationMiddleware;
use App\Middleware\Authorization\RoleAuthorizationMiddleware;
use App\Middleware\LoginValidation\LoginValidationMiddleware;
use App\Middleware\QuestAuthorization\QuestAuthorizationMiddleware;
use App\Middleware\QuestValidation\QuestValidationMiddleware;
use App\Middleware\RedirectResponse;
use App\Middleware\RegisterValidation\RegisterValidationMiddleware;
use App\Routing\Router;
use App\Request\Request;
use App\Emitter\Emitter;

$app = require_once __DIR__ .'/bootstrap.php';
$r = new Router($app);

// ERROR ROUTES
$r->get('/error/{code}', 'ErrorController@error');

// QUEST DATA VIEW / FETCH ROUTES
$r->get('/', 'QuestViewController@showQuests', [AuthenticationMiddleware::class, QuestAuthorizationMiddleware::class]);
$r->get('/showCreatedQuests', 'QuestViewController@showCreatedQuests', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class]);
$r->get('/showQuestsToApproval', 'QuestViewController@showQuestsToApproval', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class]);
$r->get('/showApprovedQuests', 'QuestViewController@showApprovedQuests', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class]);
$r->get('/reportQuest/{questId}', 'QuestViewController@questReport', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class]);
$r->get('/showQuests', 'QuestViewController@showQuests', [AuthenticationMiddleware::class, QuestAuthorizationMiddleware::class]);
$r->get('/showTopRatedQuests', 'QuestViewController@showTopRatedQuests', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class, QuestAuthorizationMiddleware::class]);
$r->get('/showRecommendedQuests', 'QuestViewController@showRecommendedQuests', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class, QuestAuthorizationMiddleware::class]);

// PROFILE ROUTES
$r->post('/changePassword', 'ProfileController@changePassword', [AuthenticationMiddleware::class]);
$r->get('/dashboard', 'ProfileController@showProfile', [AuthenticationMiddleware::class]);

// AUTHENTICATION ROUTES
$r->get('/login', 'LoginController@login', [AuthenticationMiddleware::class]);
$r->get('/logout', 'LoginController@logout', [AuthenticationMiddleware::class]);
$r->post('/login', 'LoginController@login', [LoginValidationMiddleware::class, AuthenticationMiddleware::class]);

// REGISTRATION ROUTES
$r->get('/register', 'RegisterController@register', [AuthenticationMiddleware::class]);
$r->post('/register', 'RegisterController@register', [AuthenticationMiddleware::class, RegisterValidationMiddleware::class]);

// QUEST MANAGEMENT ROUTES - CREATOR
$r->get('/showCreateQuest', 'QuestManagementController@showCreateQuest', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class]);
$r->get('/showEditQuest/{questId}', 'QuestManagementController@showEditQuest', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class, QuestAuthorizationMiddleware::class]);
$r->post('/createQuest', 'QuestManagementController@createQuest', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class, QuestValidationMiddleware::class, QuestAuthorizationMiddleware::class]);
$r->post('/editQuest/{questId}', 'QuestManagementController@editQuest', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class, QuestValidationMiddleware::class, QuestAuthorizationMiddleware::class]);

// WALLET MANAGEMENT ROUTES
$r->get('/showQuestWallets/{questId}', 'WalletManagementController@showQuestWallets', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class, QuestAuthorizationMiddleware::class]);
$r->post('/addWallet/{blockchain}', 'WalletManagementController@addWallet', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class, QuestAuthorizationMiddleware::class]);

// FILE UPLOAD FOR CREATORS
$r->post('/uploadPicture', 'UploadController@uploadPicture', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class]);

// ADMIN CONTROLLER ROUTES
$r->get('/refreshRecommendations', 'AdminController@refreshRecommendations', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class]);
$r->post('/publishQuest/{questId}', 'AdminController@publishQuest', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class]);
$r->post('/unpublishQuest/{questId}', 'AdminController@unpublishQuest', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class]);
$r->get('/promoteToCreator/{userName}', 'AdminController@promoteUser', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class]);

// GAME ROUTES
$r->post('/enterQuest/{questId}', 'QuestController@enterQuest', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class, QuestAuthorizationMiddleware::class]);
$r->get('/play', 'QuestionController@play', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class, QuestAuthorizationMiddleware::class]);
$r->post('/answer/{questionId}', 'QuestionController@answer', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class, QuestAuthorizationMiddleware::class]);
$r->get('/rating', 'RatingController@rating', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class, QuestAuthorizationMiddleware::class]);
$r->get('/rating', 'RatingController@rating', [AuthenticationMiddleware::class, RoleAuthorizationMiddleware::class, QuestAuthorizationMiddleware::class]);
$r->post('/abandonQuest', 'QuestController@abandonQuest', [AuthenticationMiddleware::class]);
$r->get('/endQuest', 'QuestController@reset', [AuthenticationMiddleware::class]);

$request = new Request($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
$emitter = new Emitter();

try {
  $response = $r->dispatch($request);

  $emitter->emit($response);
} catch (Exception $e) {
  error_log($e->getMessage());
  $emitter->emit(new RedirectResponse('/error/500', ['internal server error. contact with the administrator']));
}

