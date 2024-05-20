<?php

namespace App\Helpers\FileManager;

class ZtwenFileManager{

	public $classMapping;

	public $file_name;

	public $file_path;

	public $file_sheet;

	public $storage_path;

	public function __construct($classMapping, $storage_path, $name = null)
	{




	}



	public static function nameBuilder()
	{
		$file_name = "Notes-" . $this->classe->name . "-" . $this->subject->name . ".XLS";

        $path = storage_path() . '/app/excels';

        if(!File::isDirectory($path)){

            File::makeDirectory($path, 0777, true, true);
        }
	}






}