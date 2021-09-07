<?php
namespace App\Service;

abstract class AbstractEntity
{
    protected static function hydrate($data, $object)
    {
        // $data = ["id" => 1, "pseudo" => "Squalli", etc.]
        foreach($data as $field => $value){
            //$field = "address_id", $value = 1
            $explodedField = explode("_", $field); //=> ["address", "id"]
            if(count($explodedField) >= 2 && array_pop($explodedField) == "id"){//est ce une clé étrangère ?
                $field = array_shift($explodedField);//$field = "address"
                $id = $value;//$id = 1
                $managerName = ucfirst($field)."Manager";//$managerName = "AddressManager"
                $managerFQCN = "App\\Model\\Manager\\".$managerName;//App\Model\Manager\AddressManager
                $manager = new $managerFQCN();
                $value = $manager->findOneById($id);//$value = Address(1, "...", "...", "....")
            }
            $setter = "set".ucfirst($field);
            if(method_exists($object, $setter)){
                $object->$setter($value);
            }
        }
    }

    protected static function formatDate($date)
    {
        $formatter = \IntlDateFormatter::create(
            \Locale::getDefault(),
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::NONE,
        );
        return $formatter->format($date);
    }
}