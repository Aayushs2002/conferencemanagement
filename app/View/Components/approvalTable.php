<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class approvalTable extends Component
{
    /**
     * Create a new component instance.
     */
    public $type;
    public $others;
    public $existing;

    public function __construct($type, $others, $existing)
    {
        $this->type = $type;
        $this->others = $others;
        $this->existing = $existing;
    }

    public function render(): View|Closure|string
    {
        return view('components.approval-table');
    }
}
