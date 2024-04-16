<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ZGaleryManagerComponent extends Component
{
    public $modalName;
    public $theModel;
    public $modalHeaderTitle = '';
    public $modelName;
    public $labelTitle = '';
    public $submitMethodName;
    public $isImage = true;
    public $error = null;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($modalName, $submitMethodName = 'submit', $modalHeaderTitle = '',$labelTitle = "L'image à télécharger ...", $theModel = 'profil_image', $modelName = 'profil_image', $error = false, $isImage = true)
    {
        $this->modalName = $modalName;
        $this->modalHeaderTitle = $modalHeaderTitle;
        $this->theModel = $theModel;
        $this->error = $error;
        $this->labelTitle = $labelTitle;
        $this->modelName = $modelName;
        $this->submitMethodName = $submitMethodName;
        $this->isImage = $isImage;
    }
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.z-galery-manager-component');
    }
}
