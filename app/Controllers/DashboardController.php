<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\ViewRenderer;

final class DashboardController
{
    public function __construct(private readonly ViewRenderer $views)
    {
    }

    public function index(): void
    {
        $this->views->render('dashboard/index');
    }
}
