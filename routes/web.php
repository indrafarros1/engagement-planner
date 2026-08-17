<?php

use App\Filament\Pages\Dashboard;
use Illuminate\Support\Facades\Route;

// Root → dashboard lamaran (Filament panel); otentikasi di-handle middleware panel.
Route::redirect('/', '/admin');
