<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/picture', [ProfileController::class, 'updatePicture'])->name('profile.update-picture');
    Route::delete('/profile/picture', [ProfileController::class, 'removePicture'])->name('profile.remove-picture');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'organizer'])->prefix('events')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('events.index');
    Route::get('/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/', [EventController::class, 'store'])->name('events.store');
    Route::get('/{event}', [EventController::class, 'show'])->name('events.show');
    Route::get('/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/{event}', [EventController::class, 'destroy'])->name('events.destroy');
});

Route::middleware(['auth', 'participant'])->prefix('participant')->group(function () {
    Route::get('/events', [ParticipantController::class, 'index'])->name('participant.events.index');
    Route::get('/events/{event}', [ParticipantController::class, 'show'])->name('participant.events.show');
    Route::post('/events/{event}/purchase', [ParticipantController::class, 'purchase'])->name('participant.events.purchase');
    Route::get('/payment/success', [ParticipantController::class, 'paymentSuccess'])->name('participant.payment.success');
    Route::get('/payment/cancel', [ParticipantController::class, 'paymentCancel'])->name('participant.payment.cancel');
    Route::get('/tickets', [ParticipantController::class, 'tickets'])->name('participant.tickets.index');
    Route::get('/tickets/{purchasedTicket}/download', [ParticipantController::class, 'download'])->name('participant.tickets.download');
});

require __DIR__.'/auth.php';
