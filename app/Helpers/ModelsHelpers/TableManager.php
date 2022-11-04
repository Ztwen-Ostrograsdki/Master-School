<?php
namespace App\Helpers\ModelsHelpers;

use App\Models\User;

class TableManager{

    public $response = [
        'type' => 'error',
        'title' => "ECHEC DES OPERATIONS",
        'message' => "La table sur laquelle vous essayez d'effectuer une restauration de données n'est pas SOLFTDELETE. << Elle n'est pas Restaurable >>"
    ];


    /**
     * Determine if a model table has a specific column
     *
     * @param model $classMapping
     * @param string $columName
     * @return bool
     */
    public function tableHasColumn($classMapping, string $columnName = 'deleted_at')
    {
        return $classMapping->getConnection()
                        ->getSchemaBuilder()
                        ->hasColumn($classMapping->getTable(), $columnName);
    }

    /**
     * To reset the images of a mass data from a db
     *
     * @param model $model
     * @param array $mass
     * @return bool
     */
    public function resetGaleryMass($model, array $mass)
    {
        dd($model);
    }



    /**
     * To reset the basket of a mass data from a db
     *
     * @param model $model
     * @param array $mass
     * @return bool
     */
    public function resetBasket($model, array $mass)
    {
        dd($model);
    }


    /**
     * To delete a mass data from a db
     *
     * @param model $model
     * @param array $mass
     * @return bool
     */
    public function deleteMass($model, array $mass)
    {
        $response = [];
        $hasSoftDelete = $model->getConnection()
                               ->getSchemaBuilder()
                               ->hasColumn($model->getTable(), "deleted_at");
        if($hasSoftDelete){
            $counter = count($mass);
            if($counter > 0){
                foreach ($mass as $m){
                    $target = $model::whereId($m)->first();
                    if($target){
                        $target->delete();
                    }
                }
                $response = [
                    'type' => 'success',
                    'title' => " ENVOI DES DONNÉES DANS LA CORBEILE REUSSI",
                    'message' => "Les " . $counter . " données ont été envoyé dans la corbeile avec succès!"

                ];
                return $response;
            }
        }
        else{
            return $this->response;
        }
    }


     /**
     * To delete a mass data from a db
     *
     * @param model $model
     * @param array $mass
     * @return bool
     */
    public function forceDeleteMass($model, array $mass)
    {
        dd($model);
    }


     /**
     * To restore a mass data from a db
     *
     * @param model $model
     * @param array $mass
     * @return array|bool
     */
    public function restoreMass($model, array $mass)
    {
        $response = [];
        $hasSoftDelete = $model->getConnection()
                               ->getSchemaBuilder()
                               ->hasColumn($model->getTable(), "deleted_at");
        if($hasSoftDelete){
            $counter = count($mass);
            if($counter > 0){
                foreach ($mass as $m){
                    $target = $model::onlyTrashed()->whereId($m)->first();
                    if($target){
                        $target->restore();
                    }
                }
                $response = [
                    'type' => 'success',
                    'title' => " RESTAURATION DES DONNÉES REUSSIES",
                    'message' => "Les " . $counter . " données ont été restauré avec succès!"

                ];
                return $response;
            }
        }
        else{
            return $this->response;
        }
    }


    /**
     * To block a mass data from a db
     *
     * @param User $model
     * @param array $mass
     * @return bool
     */
    public function blockMass($model, array $mass)
    {
        dd($model);
    }



    /**
     * To confirm a mass data from a db
     *
     * @param User $model
     * @param array $mass
     * @return bool
     */
    public function confirmUserEmailMass($model, array $mass)
    {
        dd($model);
    }








}