<?php
/**
 * Created by PhpStorm.
 * User: mfarid
 * Date: 30/03/19
 * Time: 16.37
 */

namespace Faridzy\RestApiTrait;


use Illuminate\Http\Request;

/**
 * Trait RestApiTrait
 * @package Faridzy\RestApiTrait
 */
trait RestApiTrait
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request )
    {
        try {
            return $this->restIndex( $request );
        } catch ( \Exception $e ) {
            return $this->errorResponse( $e->getMessage() );
        }

    }


    /**
     * @param $request
     * @return mixed
     */
    protected function restIndex($request )
    {
        if ( ! defined ( self::class . '::MODEL' )) {
            return $this->errorResponse('MODEL not defined');
        }

        $model = self::MODEL;
        $model = $model::query();

        // Searching
        if ( $this->getRestConfig('enable_search') == true) {
            if ( $request->has('search')){
                foreach ( $this->getRestConfig( 'searchable_field') as $field ) {
                    $model->where($field, 'LIKE', '%' . $request->input( 'search' ) . '%');
                }
            }
        }
        if ( $this->getRestConfig('enable_custom_search') == true ) {
            foreach ( $this->getRestConfig( 'searchable_field') as $field ) {
                if ( $request->has( $field)) {
                    $model->where( $field,$request->input($field));
                }
            }
        }

        // Sorting
        $sortField 	= $this->getRestConfig('default_sort_field');
        $sortDirection = $this->getRestConfig('default_sort_direction');
        $sortableField = $this->getRestConfig('sortable_field');
        if ($sortableField != null && $this->getRestConfig('enable_custom_sort') && $request->has('sort'))
        {

            if (in_array( $request->input('sort'),$sortableField)) {
                $sort_field = $request->input('sort');
            }

            if ($request->has('sort_direction')) {
                $sortDirection = $request->input('sort_direction');
            }
        }
        if (!empty( $sort_field)) {
            $model->orderBy($sortField,$sortDirection);
        }

        // Limit Paginated Result
        $limit = $this->getRestConfig('limit');
        if ( $this->getRestConfig( 'enable_custom_limit' ) == true ) {
            if ( $request->has('limit')) {
                $limit = (int)$request->input('limit');
            }
        }


        $data = [];

        if (!empty($limit)) {
            if ( $this->getRestConfig('paginate_index_result') == true ) {
                $data = $model->paginate($limit);
                return $this->successResponse($data);
            } else {
                $model->limit($limit);
            }
        }

        $data = $model->get()->all();
        return $this->successResponse($data);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function create(Request $request )
    {
        if ( ! defined ( self::class . '::MODEL' ) ) {
            return $this->errorResponse( 'MODEL not defined' );
        }

        $model = self::MODEL;
        try {

            $entity = $model::create($request->input());

            if($entity->save()){
                return $this->successResponse($entity);
            }
        } catch ( \Exception $e){
            return $this->errorResponse($e->getMessage());
        }
        return $this->errorResponse();
    }


    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request )
    {
        if (!defined( self::class . '::MODEL')) {
            return $this->errorResponse( 'MODEL not defined' );
        }

        $model = self::MODEL;

        try {
            $entity = $model::find($id);

            if (!$entity) {
                return $this->notFoundResponse();
            }

            $entity->fill($request->input());

            if( $entity->save()) {
                return $this->successResponse($entity);
            }
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }

        return $this->errorResponse();
    }


    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function show($id, Request $request)
    {
        if (!defined(self::class . '::MODEL')) {
            return $this->errorResponse('MODEL not defined');
        }

        $model = self::MODEL;

        try {

            $entity = $model::find($id);
            return $this->successResponse($entity);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }

        return $this->errorResponse();
    }


    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function delete($id, Request $request)
    {
        if ( ! defined ( self::class . '::MODEL' ) ) {
            return $this->errorResponse( 'MODEL not defined' );
        }

        $model = self::MODEL;

        try {

            $entity = $model::find( $id );

            if (!$entity){
                return $this->notFoundResponse();
            }
            if ($entity->delete()){
                return $this->deleteResponse();
            }

        } catch (\Exception $e){
            return $this->errorResponse($e->getMessage());
        }

        return $this->errorResponse();
    }


    /**
     * @param $data
     * @return mixed
     */
    protected function successResponse($data)
    {
        $response = [
            'code' 		=> 200,
            'status' 	=> 'success',
            'data' 		=> $data
        ];
        return response()->json($response,$response['code'] );
    }

    /**
     * @return mixed
     */
    protected function notFoundResponse()
    {
        $response = [
            'code' 		=> 404,
            'status'	=> 'error',
            'data' 		=> 'Resource Not Found',
            'message' 	=> 'Not Found'
        ];
        return response()->json( $response, $response['code'] );
    }

    /**
     * @return mixed
     */
    protected function authenticationRequiredResponse()
    {
        $response = [
            'code' 		=> 401,
            'status' 	=> 'error',
            'data' 		=> 'Authentication Required',
            'message' 	=> 'Unauthorized'
        ];
        return response()->json( $response, $response['code'] );
    }

    /**
     * @return mixed
     */
    protected function forbiddenResponse()
    {
        $response = [
            'code' 		=> 403,
            'status' 	=> 'error',
            'data' 		=> 'Forbidden Request',
            'message' 	=> 'Forbidden'
        ];

        return response()->json($response,$response['code'] );
    }

    /**
     * @return mixed
     */
    protected function deleteResponse()
    {
        $response = [
            'code' 		=> 204,
            'status' 	=> 'success',
            'data' 		=> [],
            'message' 	=> 'Delete Successfull !'
        ];
        return response()->json($response,$response['code'] );
    }

    /**
     * @return mixed
     */
    protected function emptyResponse()
    {
        $response = [
            'code' 		=> 204,
            'status'	=> 'success',
            'data' 		=> [],
            'message' 	=> 'Resource Empty'
        ];
        return response()->json($response,$response['code'] );
    }

    /**
     * @param null $data
     * @return mixed
     */
    protected function errorResponse($data = null)
    {
        $response = [
            'code' 		=> 422,
            'status' 	=> 'error',
            'data' 		=> $data,
            'message' 	=> 'Unprocessable Entity'
        ];
        return response()->json($response,$response['code'] );
    }



}