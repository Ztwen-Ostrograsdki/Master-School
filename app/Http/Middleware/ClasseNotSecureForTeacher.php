<?php

namespace App\Http\Middleware;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use Closure;
use Illuminate\Http\Request;

class ClasseNotSecureForTeacher
{
    use ModelQueryTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $school_year_model = $this->getSchoolYear();

        $classe_id = $request->route('classe_id');
        $teacher_id = (int)$request->route('id');
        $teacher = $school_year_model->teachers()->where('teachers.id', $teacher_id)->first();
        $classe = $school_year_model->classes()->where('classes.id', $classe_id)->first();
        $classeSelf = Classe::find($classe_id);

        if($classe && $teacher){
            $teacher_classes = auth()->user()->teacher->getTeachersCurrentClasses();

            if(array_key_exists($classe->id, $teacher_classes)){
                if(!$classe->hasSecurities()){
                    if($classe->classeWasNotClosedForTeacher($teacher->id)){
                        return $next($request);
                    }
                    else{
                        return abort(404, "Votre accès à cette classe est temporairement bloqué. Contactez un administrateur");
                    }
                }
                else{
                    return abort(404, "Cette classe est temporairement fermée ou verrouillée");
                }
            }
            else{
                return abort(404);
            }
        }
        else{
            if($classeSelf && $teacher){
                return $next($request);
            }
            return abort(404);
        }

    }
}
