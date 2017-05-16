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

Route::get('/placeholder', function () {
    return view('placeholder');
});

Route::group(['middleware' => 'auth'], function () {  
	    Route::get('/', 'HomeController@index');
	    Route::get('/auth/login', 'Auth\LoginController@login');
	    Route::post('/auth/login', ['before' => 'throttle:2,60', 'uses' => 'Auth\LoginController@login']);
	    Route::get('/financialservices', 'Group\FinancialServicesController@index');
		Route::get('/humanresource', 'Group\HumanResourceController@index');
		Route::get('/procurement', 'Group\ProcurementController@index');
		Route::get('/it', 'Group\ITController@index');
		Route::get('/home', 'HomeController@index');
		Route::get('/site', 'Site\SiteController@index');

		Route::resource('area', 'Master\AreaController');
		Route::resource('bank', 'Master\BankController');
		Route::resource('plant', 'Master\PlantController');
		Route::resource('currency', 'Master\CurrencyController');
		Route::resource('storagelocation', 'Master\StoragelocationController');
		Route::resource('materialtypegroup', 'Master\MaterialtypegroupController');
		Route::resource('department', 'Master\DepartmentController');
		Route::resource('subdepartment', 'Master\SubdepartmentController');
		Route::resource('term-payment', 'Master\TermpaymentController');
		Route::resource('asset-master', 'Master\AssetmasterController');
		Route::resource('asset-owner', 'Master\AssetownerController');
		Route::resource('asset-location', 'Master\AssetlocationController');
		Route::resource('cost-center', 'Master\CostcenterController');
		Route::resource('asset-class', 'Master\AssetclassController');
		Route::resource('asset-subclass', 'Master\AssetsubclassController');
		Route::resource('product-disc', 'Master\ProductdiscController');
		Route::resource('product-price-padimas', 'Master\ProductpricepadimasController');
		Route::resource('faq', 'Master\FaqController');
		Route::resource('form-gallery', 'Master\FormgalleryController');
		Route::resource('office-agreement', 'Master\OfficeagreementController');
		// garmelia agreement
		Route::resource('garmelia-agreement', 'Group\GarmeliaagreementController');
		Route::get('garmelia-agreement/view/{doc_id}', array('as' => 'garmelia-agreement.view', 'uses' => 'Group\GarmeliaagreementController@view'));
		Route::get('garmelia-agreement/admin', array('as' => 'garmelia-agreement.admin', 'uses' => 'Group\GarmeliaagreementController@show'));
		Route::get('garmelia-agreement/approve/{doc_id}', array('as' => 'garmelia-agreement.approve', 'uses' => 'Group\GarmeliaagreementController@approve'));
		Route::match(['put', 'patch'], '/garmelia-agreement/goupdate/{doc_id}', array('as' => 'garmelia-agreement.goupdate', 'uses' => 'Group\GarmeliaagreementController@goupdate'));
		Route::get('garmelia-agreement/viewpdf/{doc_id}', array('as' => 'garmelia-agreement.viewpdf', 'uses' => 'Group\GarmeliaagreementController@viewpdf'));
	
		// padimas agreement

		Route::get('padimas-agreement', [
			    'middleware' => 'auth',
			    'as'=>'padimas-agreement.index',
			    'uses' => 'Group\PadimasagreementController@index'
			]);
		Route::post('padimas-agreement/store', ['as'=>'padimas-agreement.store','uses'=>'Group\PadimasagreementController@store']);
		Route::get('padimas-agreement/create', ['as'=>'padimas-agreement.create','uses'=>'Group\PadimasagreementController@create']);
		Route::get('padimas-agreement/edit/{doc_id}', ['as'=>'padimas-agreement.edit','uses'=>'Group\PadimasagreementController@edit']);
		Route::match(['put', 'patch'], '/padimas-agreement/update/{doc_id}', array('as' => 'padimas-agreement.update', 'uses' => 'Group\PadimasagreementController@update'));
		Route::get('padimas-agreement/find_area', ['as'=>'padimas-agreement.find_area','uses'=>'Group\PadimasagreementController@findArea']);
		Route::get('padimas-agreement/find_product', ['as'=>'padimas-agreement.find_product','uses'=>'Group\PadimasagreementController@findproduct']);
		Route::get('padimas-agreement/find_product_disc', ['as'=>'padimas-agreement.find_product_disc','uses'=>'Group\PadimasagreementController@findproduct_disc']);
		Route::get('padimas-agreement/getproduct', ['as'=>'padimas-agreement.getproduct','uses'=>'Group\PadimasagreementController@getproduct']);
		

		Route::get('padimas-agreement/view/{doc_id}', array('as' => 'padimas-agreement.view', 'uses' => 'Group\PadimasagreementController@view'));
		Route::get('padimas-agreement/admin', array('as' => 'padimas-agreement.admin', 'uses' => 'Group\PadimasagreementController@show'));
		Route::get('padimas-agreement/approve/{doc_id}', array('as' => 'padimas-agreement.approve', 'uses' => 'Group\PadimasagreementController@approve'));
		Route::match(['put', 'patch'], '/padimas-agreement/goupdate/{doc_id}', array('as' => 'padimas-agreement.goupdate', 'uses' => 'Group\PadimasagreementController@goupdate'));
		Route::get('padimas-agreement/viewpdf/{doc_id}', array('as' => 'padimas-agreement.viewpdf', 'uses' => 'Group\PadimasagreementController@viewpdf'));


		Route::get('payment-form', [
			    'middleware' => 'auth',
			    'uses' => 'Group\PaymentFormController@index'
			]);
		Route::post('payment-form/store', ['as'=>'payment-form.store','uses'=>'Group\PaymentFormController@store']);
		Route::get('payment-form/create', ['as'=>'payment-form.create','uses'=>'Group\PaymentFormController@create']);
		Route::get('payment-form/find_dept', ['as'=>'payment-form.find_dept','uses'=>'Group\PaymentFormController@findept']);
		Route::get('payment-form/suborg', ['as'=>'payment-form.suborg','uses'=>'Group\PaymentFormController@suborg']);
		Route::get('payment-form/bank', ['as'=>'payment-form.bank','uses'=>'Group\PaymentFormController@findBank']);



		Route::resource('asset-master-form', 'Group\AssetMasterFormController');
		Route::resource('material-master-form', 'Group\MaterialMasterFormController');
 });

Auth::routes();





