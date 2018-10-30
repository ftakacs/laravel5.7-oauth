<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/info', function () {
    $query = http_build_query([
        'client_id' => 3, // Replace with Client ID
        'redirect_uri' => 'http://127.0.0.1:8081/cliente1-callback',
        'response_type' => 'code',
        'scope' => ''
    ]);

    return redirect('http://localhost:8081/oauth/authorize?'.$query);
});

Route::get('/cliente1-callback', function (Illuminate\Http\Request $request) {
    $response = (new GuzzleHttp\Client)->post('http://localhost:8080/oauth/token', [
        'form_params' => [
            'grant_type' => 'authorization_code',
            'client_id' => 3, 
            'client_secret' => '444pQYwPKiaxQ1993UrPVAeLb4DW2SoGMkuHmBry',
            'redirect_uri' => 'http://127.0.0.1:8081/cliente1-callback',
            'code' => $request->code,
        ]
    ]);

    session()->put('token', json_decode((string) $response->getBody(), true));

    return redirect('/todos');
});

Route::get('/todos', function () {
    $response = (new GuzzleHttp\Client)->get('http://localhost:8080/api/teste', [
        'headers' => [
            'Authorization' => 'Bearer '.session()->get('token.access_token')
        ]
    ]);

    return json_decode((string) $response->getBody(), true);
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/settings', 'HomeController@settings')->name('settings');
