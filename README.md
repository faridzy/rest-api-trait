# Laravel REST Trait
A collection of trait to build simple REST controller in Laravel.
    

**Installing**

`composer require mfarid/rest-api-trait`



**Add it into your class**
    
Example usage on a Controller Class :

    use Illuminate\Http\Request;
    use App\Traits\TraitRestController;

    class BookController extends Controller
    {
        use TraitRestController;

        const MODEL = \App\Models\Book::class;
        
        protected $restConfig = [];
    }

just `use` the trait, and define `const MODEL` on your class.

`$restConfig` property is custom configuration and it's optional.

`TraitRestController` is trait that contain `index`, `show`, `create`, `update`, and `delete` method. 

Please note that `store` and `update` **NOT** have any validation yet, so becareful when using it.


## Usage
After you use this trait on the controller, the next step is setup your application routes to point on that controller, please see Laravel routes documentation for it.

Example routes:

`Route::group(['prefix'=>'book'], function(){
 	Route::get('/', 'BookController@index');
    Route::get('/show/{id}', 'BookController@show');
    Route::post('/create', 'BookController@create');
    Route::put('/update/{id}', 'BookController@update');
    Route::delete('/delete/{id}', 'BookController@delete');
});`

all method will **always** return JSON result, 

Example response on Index method with paginated result:


    {
        "code": 200,
        "status": "success",
        "data": {
            "current_page": 1,
            "data": [
                {
                    "id": 1,
                    "book_name": "Matematika 3",
                    "book_description": "Buku untuk kelas 3 SMP",
                    "book_price": 50000,
                    "book_author": "Airlangga"
                },
                {
                    "id": 2,
                    "book_name": "Matematika 3",
                    "book_description": "Buku untuk kelas 3 SMP",
                    "book_price": 50000,
                    "book_author": "Airlangga"
                },
                {
                    "id": 3,
                    "book_name": "Matematika 3",
                    "book_description": "Buku untuk kelas 3 SMP",
                    "book_price": 50000,
                    "book_author": "Airlangga"
                }
            ],
            "first_page_url": "http://localhost/laravel-coba/public/api/book?page=1",
            "from": 1,
            "last_page": 7,
            "last_page_url": "http://localhost/laravel-coba/public/api/book?page=7",
            "next_page_url": "http://localhost/laravel-coba/public/api/book?page=2",
            "path": "http://localhost/laravel-coba/public/api/book",
            "per_page": 3,
            "prev_page_url": null,
            "to": 3,
            "total": 19
        }
    }
        
Example response on Show method :

    
    {
        "code": 200,
        "status": "success",
        "data": {
            "id": 1,
            "book_name": "Matematika 3",
            "book_description": "Buku untuk kelas 3 SMP",
            "book_price": 50000,
            "book_author": "Airlangga"
        }
    } 

### Example
**Custom Limit**

By default you can limit paginated result by passing `limit` key on the request url
    
    BASE_URL+/api/book?limit=10


**Searching**
    
The example below will run query like `->where( 'book_name','LIKE', '%' . 'matematika' . '%' )`

    BASE_URL+/api/book?search=matematika

make sure you've set `searchable_field` configuration.

    
**Search Specific Field**

The example below will run query like `->where( 'book_name', 'matematika' )` exact match

    BASE_URL+/api/book?book_name=matematika
    
    
**Custom Sort**

make sure you've added the sorted field to `sortable_field` configuration.    

   BASE_URL+/api/book?search=matematika&sort=id&sort_direction=DESC
    
    
## Configuration
see on `Config.php` file


