<?php
/**
 * Created by PhpStorm.
 * User: mfarid
 * Date: 30/03/19
 * Time: 16.44
 */

namespace Faridzy\RestApiTrait;


/**
 * Trait Config
 * @package Faridzy\RestApiTrait
 */
trait Config
{
    //   on Controller
    // protected $restConfig = [
    // 	'limit' 				=> 10,
    // 	'searchable_field' 		=> ['name'],
    // 	'sort_field' 			=> null,
    // 	'sort_direction'		=> 'ASC',
    // ];


    /**
     * @return array
     */
    protected function getDefaultValue()
    {
        return [

            'limit' 					=> 25,
            'paginate_index_result' 	=> true,
            'enable_custom_limit'		=> true,
            'enable_custom_search' 		=> true,
            'searchable_field' 			=> ['id'],
            'enable_search'				=> true,
            'default_sort_field' 		=> null,
            'default_sort_direction'	=> 'ASC',
            'sortable_field'			=> ['id'],
            'enable_custom_sort'		=> true,

        ];
    }

    /**
     * @param $key
     * @return mixed
     */
    protected function getRestConfig($key)
    {
        $config = $this->getDefaultValue();
        if ( isset( $this->restConfig)) {
            $config = array_merge( $config,$this->restConfig);
        }

        return $config[$key];
    }
}