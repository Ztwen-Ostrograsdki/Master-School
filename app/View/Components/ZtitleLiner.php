<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ZtitleLiner extends Component
{
    public $title;
    public $value;
    public $smallTitle = false;
    public $icon = '';
    public $useIcon = false;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $title = '', $value = '', $icon = '', $useIcon = false, $smallTitle = false)
    {
        $this->title = $title;
        $this->value = $value;
        $this->icon = $icon;
        $this->useIcon = $useIcon;
        $this->smallTitle = $smallTitle;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ztitle-liner');
    }
}
