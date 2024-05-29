<?php

namespace App\Helpers\FileManager;

use App\Models\Classe;
use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Support\Facades\File;

class ZtwenEpreuveFileManager{

	public $classe;

	public $subject;

	public $school_year_model;

	public $file_name;

	public $file_path;

	public $base_folder = 'dossier';

	public $file_sheet;

	public $semestre;

	public $storage_path;

	public $extension = '.docx';

	public function __construct(Classe $classe, $base_folder, $name = null, SchoolYear $school_year_model, $semestre, Subject $subject, $extension = '.docx')
	{

		$this->classe = $classe;

		$this->name = $name;

		$this->semestre = $semestre;

		$this->base_folder = $base_folder;

		$this->school_year_model = $school_year_model;

		$this->subject = $subject;

		$this->extension = $extension;

		self::builder();

	}



	public function nameBuilder()
	{
		if($this->name){

			$this->file_name = $this->name;

		}
		else{

			$file_name = "Epreuves-" . $this->classe->name . "-" . $this->subject->name . "-" . $this->semestre . "-" . $this->school_year_model->school_year;

			$this->file_name = str_replace(' ', '', $file_name);
		}
	}


	public function folderBuider()
	{

		$path = storage_path() . '/app/' . $this->base_folder .'/' . $this->school_year_model->school_year . '/' . $this->semestre;

		$new_path = str_replace(' ', '', $path);

		$this->storage_path = $new_path;

        if(!File::isDirectory($new_path)){

            File::makeDirectory($new_path, 0777, true, true);
        }
	}



	public function builder()
	{
		self::folderBuider();

		self::nameBuilder();
	}






}