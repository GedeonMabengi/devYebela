<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RequirementController;

use App\Http\Controllers\GeminiController;

use App\Http\Controllers\ApiController;

use App\Http\Controllers\PromptController;

Route::get('/showResponse', [PromptController::class, 'showResponse']);
Route::get('/generatePrompt', [PromptController::class, 'generatePrompt'])->name('generatePrompt');
Route::get('/generateDiagram', [PromptController::class, 'generateDiagram'])->name('generateDiagram');

Route::post('/generatePrompt', [PromptController::class, 'generatePrompt'])->name('generatePrompt');
Route::post('/generateDiagram', [PromptController::class, 'generateDiagram'])->name('generateDiagram');

Route::get('/prompts', [PromptController::class, 'index'])->name('prompts.index');

Route::get('/', function () {
    return Inertia::render('DiagramDrawer');
});

Route::post('/firstRequest',[PromptController::class, 'sendFistRequest'])->name('firstRequest');
Route::post('/firstRequest',[PromptController::class, 'sendFistRequest'])->name('firstRequest');

// Route::get('/', [ApiController::class, 'getRequirements']);
// Route::post('/send-requirements', [ApiController::class, 'sendRequirements']);
// Route::get('/first-requirement', [ApiController::class, 'firstRequirement']);

// Route::get('/getRequirements', [ApiController::class, 'getRequirements'])->name('getRequirements');
// Route::post('/sendRequirements', [ApiController::class, 'sendRequirements'])->name('sendRequirements');
// Route::get('/firstRequirement', [ApiController::class, 'firstRequirement'])->name('firstRequirement');

// Route::post('/requirements', [ApiController::class, 'updateRequirements'])->name('requirements.update');
// Route::post('/send-requirements', [ApiController::class, 'sendRequirements'])->name('requirements.send');
// Route::get('/response/{requestId}', [ApiController::class, 'getResponse'])->name('response.show');
// Route::post('/send-requirements-epuration', [ApiController::class, 'sendRequirementsForEpuration'])->name('requirements.epurate.send');
// Route::get('/requirements-epuration/{requestId}', [ApiController::class, 'getRequirementsForEpuration'])->name('requirements.epurate.show');


/*Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';*/
