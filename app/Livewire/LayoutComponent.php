<?php

namespace App\Livewire;

use Livewire\Component;

class LayoutComponent extends Component
{
    public $layout;
    public $popup;
    public function mount($layout,$popup)
    {
        $this->$popup = $popup;
        $this->layout = $layout;
    }
    public function render()
    {
        return view('livewire.layout-component');
    }
}
