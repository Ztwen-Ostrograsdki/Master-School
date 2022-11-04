<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ZTableRow extends Component
{
    public $mark_type;
    public $mark_id;
    public $pupil_id;
    public $mark_key;
    public $modelName;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($mark_type = 'epe', $mark_key = null, $modelName = 'edit_mark_value', $mark_id = null, $pupil_id = null)
    {
        $this->mark_id = $mark_id;
        $this->pupil_id = $pupil_id;
        $this->mark_type = $mark_type;
        $this->modelName = $modelName;
        $this->mark_key = $mark_key;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.z-table-row');
    }
}
