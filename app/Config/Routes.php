<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Test route
$routes->get('/test', function() {
    return view('test');
});

// Simple dashboard test
$routes->get('/dashboard-simple', function() {
    return view('dashboard_simple');
});

// Authentication routes
$routes->group('auth', static function ($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('authenticate', 'Auth::authenticate');
    $routes->get('register', 'Auth::register');
    $routes->post('store', 'Auth::store');
    $routes->get('logout', 'Auth::logout');
    $routes->get('forgot-password', 'Auth::forgotPassword');
    $routes->post('send-reset-link', 'Auth::sendResetLink');
    $routes->get('reset-password/(:segment)', 'Auth::resetPassword/$1');
    $routes->post('update-password', 'Auth::updatePassword');
});

// Default route - redirect to login
$routes->get('/', function() {
    return redirect()->to('/auth/login');
});

// Dashboard route
$routes->group('dashboard', static function ($routes) {
    $routes->get('/', 'Dashboard::index');
});

// Content Management routes
$routes->group('content-plan', static function ($routes) {
    $routes->get('/', 'ContentPlan::index');
    $routes->get('getStats', 'ContentPlan::getStats');
    $routes->get('getContents', 'ContentPlan::getContents');
    $routes->get('create', 'ContentPlan::create');
    $routes->post('store', 'ContentPlan::store');
    $routes->get('edit/(:num)', 'ContentPlan::edit/$1');
    $routes->post('update/(:num)', 'ContentPlan::update/$1');
    $routes->post('update-status/(:num)', 'ContentPlan::updateStatus/$1');
    $routes->get('delete/(:num)', 'ContentPlan::delete/$1');
    $routes->post('store-platform', 'ContentPlan::storePlatform');
    $routes->post('store-type', 'ContentPlan::storeType');
    $routes->post('store-pillar', 'ContentPlan::storePillar');
});

$routes->group('content-ideas', static function ($routes) {
    $routes->get('/', 'ContentIdeas::index');
    $routes->get('getIdeas', 'ContentIdeas::getIdeas');
    $routes->get('create', 'ContentIdeas::create');
    $routes->post('store', 'ContentIdeas::store');
    $routes->get('edit/(:num)', 'ContentIdeas::edit/$1');
    $routes->post('update/(:num)', 'ContentIdeas::update/$1');
    $routes->post('delete/(:num)', 'ContentIdeas::delete/$1'); // Changed to POST for AJAX
    $routes->post('convertToContent/(:num)', 'ContentIdeas::convertToContent/$1');
});

$routes->group('campaigns', static function ($routes) {
    $routes->get('/', 'Campaigns::index');
    $routes->post('store', 'Campaigns::store');
    $routes->get('detail/(:num)', 'Campaigns::detail/$1');
    $routes->post('update/(:num)', 'Campaigns::update/$1');
    $routes->get('delete/(:num)', 'Campaigns::delete/$1');
});

// Production & Review routes
$routes->group('content-brief', static function ($routes) {
    $routes->get('/', 'ContentBrief::index');
    $routes->get('getBriefs', 'ContentBrief::getBriefs');
    $routes->get('create', 'ContentBrief::create');
    $routes->get('create/(:num)', 'ContentBrief::create/$1');
    $routes->post('store', 'ContentBrief::store');
    $routes->get('edit/(:num)', 'ContentBrief::edit/$1');
    $routes->post('update/(:num)', 'ContentBrief::update/$1');
    $routes->post('delete/(:num)', 'ContentBrief::delete/$1');
    $routes->get('generateWithAI/(:num)', 'ContentBrief::generateWithAI/$1');
});

$routes->group('content-approval', ['filter' => 'role:reviewer,admin'], static function ($routes) {
    $routes->get('/', 'ContentApproval::index');
    $routes->get('getData', 'ContentApproval::getData');
    $routes->post('approve/(:num)', 'ContentApproval::approve/$1');
    $routes->post('request-revision/(:num)', 'ContentApproval::requestRevision/$1');
});

$routes->group('asset-library', static function ($routes) {
    $routes->get('/', 'AssetLibrary::index');
    $routes->get('getAssets', 'AssetLibrary::getAssets');
    $routes->post('upload', 'AssetLibrary::upload');
    $routes->post('delete/(:num)', 'AssetLibrary::delete/$1');
});

// Publishing & Engagement routes
$routes->group('publishing', static function ($routes) {
    $routes->get('/', 'Publishing::index');
    $routes->get('getData', 'Publishing::getData');
    $routes->post('store-schedule', 'Publishing::storeSchedule');
    $routes->post('publish-now/(:num)', 'Publishing::publishNow/$1');
});

$routes->group('social-inbox', static function ($routes) {
    $routes->get('/', 'SocialInbox::index');
    $routes->get('getData', 'SocialInbox::getData');
    $routes->post('markAsRead/(:num)', 'SocialInbox::markAsRead/$1');
    $routes->post('archive/(:num)', 'SocialInbox::archive/$1');
    $routes->post('delete/(:num)', 'SocialInbox::delete/$1');
    $routes->post('reply/(:num)', 'SocialInbox::reply/$1');
});

// AI Integration Routes
$routes->group('ai', static function ($routes) {
    $routes->post('generateIdeas', 'AiController::generateIdeas');
    $routes->post('polishText', 'AiController::polishText');
    $routes->post('generateCaption', 'AiController::generateCaption');
    $routes->post('generateCampaign', 'AiController::generateCampaign');
});

// Analytics & Admin routes
$routes->group('analytics', static function ($routes) {
    $routes->get('/', 'Analytics::index');
    $routes->get('getData', 'Analytics::getData');
});

$routes->group('user-management', static function ($routes) {
    $routes->get('/', 'UserManagement::index');
    $routes->post('store', 'UserManagement::store');
    $routes->get('getUser/(:num)', 'UserManagement::getUser/$1');
    $routes->post('update/(:num)', 'UserManagement::update/$1');
    $routes->get('delete/(:num)', 'UserManagement::delete/$1');
    $routes->get('toggleStatus/(:num)', 'UserManagement::toggleStatus/$1');
});
