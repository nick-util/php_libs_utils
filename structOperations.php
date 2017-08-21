<?php

namespace App\Traits;

/*
 *  The purpose of this class is set manipulation.
 *  Only functions that manipulate the entire set of struct properties should go into this trait
 *  Single property manipulations should be done in other locations.
 */
trait structOperations
{
    // return a new object from an array
    public static function from_array($in){
            $instance = new self();
            $instance->populate($in);
            return $instance;
    }

    // convert object to array
    public function to_array(){
        return (array)$this;
    }

    // decode/unserialize object and returns a new object from the result
    public static function from_serial($in){
        $decoded = base64_decode($in);
        $unserial = unserialize($decoded);
        $instance = new self();
        $instance->populate($unserial);
        return $instance;
    }

    // serialize/encode the object
    public function to_serial(){
        $out = serialize($this);
        $out = base64_encode($out);
        return $out;
    }

    // populates the object with fields from the array, wiping any existing
    public function populate($array){
        if(empty($array)) {
            return;
        }
        foreach ($array as $key => $value) {
            if (!$this->property_exists($key)) {
                continue;
            }
            $this->$key = $value;
        }
    }

    //  sets the object property to the value, IF the field is empty
    public function populate_if_empty($array){
        if(empty($array)) {
            return;
        }
        foreach ($array as $key=>$value){
            if(!$this->property_exists($key)){
                continue;
            }
            if( empty($this->$key) ){
                $this->$key = $value;
            }
        }
    }

    // the key is the property, the value is the filter_var validation
    // ex:  ["ip_address" => FILTER_VALIDATE_IP, "some_id" => FILTER_VALIDATE_INT]
    public function validate($validations_array){
        $invalid = [];

        if(empty($validations_array)) {
            return [];
        }

        foreach ($validations_array as $key=>$value){

            if( !$this->property_exists($key) ){
                $invalid[] = "$key";
                continue;
            }

            if(!filter_var($this->$key, $value)){
                $out[] = "$key";
            }
        }
        return $invalid;
    }

    // tests if a property exists on the object
    public function property_exists($property){
        return property_exists( get_class($this) , $property);
    }
}