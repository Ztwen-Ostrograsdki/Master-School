<?php
namespace App\Helpers\ZtwenDrivers;


class ClasseMarksInsertionDriver{

	public $classe;

    public $user;

    public $subject;

    public $marks = [];

    public $semestre = 1;

    public $school_year_model;

	public function __construct(array $data)
	{
        $this->classe = $data['classe'];

        $this->user = $data['user'];

        $this->subject = $data['subject'];

        $this->marks = $data['marks'];

        $this->semestre = $data['semestre'];

        $this->school_year_model = $data['school_year_model'];


	}


}