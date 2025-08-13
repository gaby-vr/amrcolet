<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyCsrfToken;

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

Route::namespace('App\Http\Controllers')->group(function() {
    Route::group(['controller' => 'HomeController'], function () {
        Route::get('/', 'index')->name('home');
        Route::post('countries-iso', 'getCountriesIso')->name('countries.iso');
        Route::group(['prefix' => 'info'], function () {
            Route::get('packaging-policy', 'showPackagingPolicy')->name('packaging.show');
            Route::get('cookies-policy', 'showCookiesPolicy')->name('cookies.show');
            Route::get('postal-policy', 'showPostalPolicy')->name('postal.show');
        });
        Route::get('contact', 'showContact')->name('contact');
        Route::post('contact/email', 'sendMail')->name('contact.send');
        Route::post('contract/email', 'sendContractMail')->name('contract.send');
    });

    Route::group(['controller' => 'SearchController'], function () {
        Route::group(['prefix' => 'cauta', 'as' => 'search.'], function () {
            Route::get('servicii', 'searchServices')->name('services');
            Route::post('judet', 'getCounty')->name('county');
            Route::post('localitate', 'getLocality')->name('locality');
        });

        Route::group(['prefix' => 'order', 'as' => 'order.get.'], function () {
            Route::post('/county', 'getCountyAndLocality')->name('county');
            Route::post('/street', 'getStreet')->name('street');
        });
    });

    Route::group(['prefix' => 'order', 'as' => 'order.', 'controller' => 'OrderController'], function () {
        Route::get('/', 'index')->name('index');
        Route::get('free-session', 'freeSession')->name('free.session');
        Route::post('get/invoice', 'getInvoice')->name('get.invoice');
        Route::post('expeditor', 'stepExpeditor')->name('expeditor');
        Route::post('receiver', 'stepReceiver')->name('receiver');
        Route::post('package', 'stepPackage')->name('package');
        Route::post('invoice', 'stepInvoice')->name('invoice');
        Route::post('service', 'stepService')->name('service');
        Route::post('check/input', 'checkInput')->name('check');
        Route::post('submit', 'storePackage')->name('submit');
        Route::post('confirm', 'confirm')
            ->withoutMiddleware([VerifyCsrfToken::class])
            ->name('confirm');

        Route::get('return/{livrare?}', 'return')->name('return');
    });

    Route::middleware(['auth', 'verified'])->group(function () {

        Route::group([
            'as' => 'dashboard.get.', 
            'prefix' => 'dashboard/adrese', 
            'controller' => 'SearchController'
        ], function () {
            Route::post('county', 'getCountyAndLocality')->name('county');
            Route::post('street', 'getStreet')->name('street');
        });

        Route::group([
            'as' => 'dashboard.',
            'prefix' => 'dashboard',
            'namespace' => 'Dashboard',
        ], function () {

            Route::group(['controller' => 'DashboardController'], function () {
                Route::get('/', 'index')->name('show');
                Route::get('/announcement', 'announcement')->name('announcement');
                Route::post('/check/input', 'checkInput')->name('check');
            });

            Route::group(['prefix' => 'facturare', 'as' => 'invoice.', 'controller' => 'InvoiceController'], function () {
                Route::get('/facturare', 'index')->name('show');
                Route::post('/facturare/update', 'update')->name('update');
            });

            Route::group(['prefix' => 'setari', 'as' => 'settings.', 'controller' => 'SettingsController'], function () {

                Route::group(['prefix' => 'rambursare', 'as' => 'repayment.'], function () {
                    Route::get('/', 'index')->name('show');
                    Route::post('/update', 'updateRepaymentsSettings')->name('update');
                });

                Route::group(['prefix' => 'securitate', 'as' => 'security.'], function () {
                    Route::get('/', 'index')->name('show');
                    Route::post('/update', 'updateSecuritySettings')->name('update');
                });

                Route::group(['prefix' => 'notificari', 'as' => 'notifications.'], function () {
                    Route::get('/', 'index')->name('show');
                    Route::post('/update', 'updateNotificationsSettings')->name('update');
                });

                Route::group(['prefix' => 'printare', 'as' => 'print.'], function () {
                    Route::get('/', 'index')->name('show');
                    Route::post('/update', 'updatePrintSettings')->name('update');
                });

                Route::group(['prefix' => 'program', 'as' => 'schedule.'], function () {
                    Route::get('/', 'index')->name('show');
                    Route::post('update', 'updateScheduleSettings')->name('update');
                });
            });

            Route::group(['prefix' => 'adrese', 'as' => 'addresses.', 'controller' => 'AddressController'], function () {
                Route::get('/', 'index')->name('show');
                Route::post('get/{address?}', 'get')->name('get');
                Route::post('sterge/{address}', 'delete')->name('delete');
                Route::post('actualizare/{address?}', 'update')->name('update');
            });

            Route::group(['prefix' => 'plata-in-avans', 'as' => 'purse.', 'controller' => 'PurseController'], function () {
                Route::get('/', 'index')->name('show');
                Route::post('achizitioneaza', 'buy')->name('buy');
                Route::get('factura/{invoice}', 'showPDF')->name('pdf');
                Route::post('plateste-comenzi', 'payInvoices')->name('pay.orders');
                Route::post('confirm', 'confirm')
                    ->withoutMiddleware(['auth', 'verified', VerifyCsrfToken::class])
                    ->name('confirm');
            });

            Route::group(['prefix' => 'sabloane', 'as' => 'templates.', 'controller' => 'TemplateController'], function () {
                Route::get('/', 'index')->name('show');
                Route::post('get/{template?}', 'get')->name('get');
                Route::post('sterge/{template}', 'delete')->name('delete');
                Route::post('actualizare/{template?}', 'update')->name('update');
            });

            Route::group(['prefix' => 'comenzi', 'as' => 'orders.', 'controller' => 'LivrariController'], function () {
                Route::get('/', 'index')->name('show');
                Route::get('/export', 'downloadExcel')->name('excel');

                Route::get('/in-asteptare', function () {
                    return view('profile.dashboard', [
                        'section' => 'orders',
                        'subsection' => 'pending',
                        'title' => __('Comenzi în așteptare'),
                    ]);
                })->name('pending');

                Route::middleware('can:read,livrare')->group(function () {
                    Route::get('{livrare}', 'view')->name('view');
                    Route::post('{livrare}', 'cancel')->name('cancel');
                    Route::get('{livrare}/awb', 'awb')->name('awb');
                    Route::get('{livrare}/repeta', 'repeat')->name('repeat');
                    Route::get('{livrare}/{invoice}', 'showPDF')->middleware(['forget.parameters:livrare'])->name('pdf');
                    //Route::get('p/{livrare}/{invoice}', 'showFile')->name('pdfp');
                });
            });

            Route::group(['prefix' => 'borderouri', 'as' => 'borderouri.', 'controller' => 'BorderouriController'], function () {
                Route::get('/', 'index')->name('show');
                Route::get('/{borderou}/export', 'downloadExcel')->name('excel');
            });

            Route::group(['prefix' => 'facturi', 'as' => 'invoices.', 'controller' => 'InvoicesController'], function () {
                Route::get('/', 'index')->name('show');
                Route::get('{invoice}', 'showPDF')->name('pdf');
            });

            Route::group(['prefix' => 'plugin', 'as' => 'plugin.', 'controller' => 'PluginController'], function () {
                Route::get('/', 'index')->name('show');
                Route::get('download', 'downloadWordpressPlugin')->name('download');
            });

            Route::group(['prefix' => 'rambursuri', 'as' => 'repayments.', 'controller' => 'RepaymentsController'], function () {
                Route::get('/', 'index')->name('show');
                Route::get('/rambursuri/download-excel', 'downloadExcel')->name('excel');
                // Route::get('/rambursuri/email-excel', 'emailExcel')->name('excel');
            });

            Route::group(['prefix' => 'financiar', 'as' => 'financiar.', 'controller' => 'FinanciarController'], function () {
                Route::get('/', 'index')->name('show');
                Route::get('{invoice}', 'showPDF')->name('pdf');
            });
        });

        Route::group([
            'as' => 'admin.',
            'prefix' => 'admin',
            'middleware' => ['auth.admin'],
            'namespace' => 'Admin',
        ], function () {
            Route::get('/', function () {
                return redirect()->route('admin.users.show');
            })->name('view');

            Route::group(['prefix' => 'utilizatori', 'as' => 'users.', 'controller' => 'UsersController'], function () {
                Route::get('/', 'index')->name('show');
                Route::post('obtine', 'get')->name('get');
                Route::get('creare', 'create')->name('create');
                Route::post('salveaza', 'store')->name('store');
                Route::get('{user}/editare', 'edit')->name('edit');
                Route::post('{user}/actualizare', 'update')->name('update');
                Route::delete('{user}/sterge', 'destroy')->name('destroy');
                Route::get('{user}/informatii-factura', 'invoice')->name('invoice');
                Route::post('{user}/impersonare', 'impersonate')->name('login.as');
            });

            Route::group(['prefix' => 'istoric-plati', 'as' => 'invoices.', 'controller' => 'PaymentsController'], function () {
                Route::get('/', 'index')->name('show');
                Route::get('creare', 'create')->name('create');
                Route::post('salveaza', 'store')->name('store');
                Route::get('informatii-factura/{user?}', 'getInvoice')->name('get.invoice');
                Route::get('{invoice}/editare', 'edit')->name('edit');
                Route::post('{invoice}/actualizare', 'update')->name('update');
                Route::get('{invoice}/send/api/{payed?}', 'sendInvoiceToApi')->name('send.api');
                Route::get('{invoice}/actualizare/api', 'updateInvoiceFromApi')->name('update.api');
                Route::get('{invoice}/storneaza', 'storn')->name('storn');
                Route::get('{invoice}/anuleaza/api', 'cancelInvoiceApi')->name('cancel.api');
                Route::get('{invoice}/restore/api', 'restoreInvoiceApi')->name('restore.api');
                Route::get('{invoice}/spv/api', 'sendInvoiceSPVApi')->name('spv.api');
                Route::get('factura/{invoice}', 'showPDF')->name('pdf');
                Route::post('factura/descarcare-in-masa', 'downloadMultipleInvoicePDF')->name('pdf.multiple');
                Route::get('excel', 'downloadExcel')->name('excel');
            });

            Route::group(['prefix' => 'comenzi', 'as' => 'orders.', 'controller' => 'OrdersController'], function () {
                Route::get('/', 'index')->name('show');
                Route::get('excel', 'downloadExcel')->name('excel');
                Route::post('import', 'import')->name('import');
                Route::get('{livrare}', 'details')->name('details');
                Route::get('{livrare}/awb', 'awb')->name('awb');
                Route::post('{livrare}/update/status', 'updateStatus')->name('update.status');
                Route::delete('{livrare}/sterge', 'destroy')->name('destroy');
                Route::get('{cancelRequest}/accepta', 'acceptCancelRequest')->name('accept.cancel');
                Route::get('{cancelRequest}/refuza', 'refuseCancelRequest')->name('refuse.cancel');
                Route::get('{livrare}/{invoice}', 'showPDF')->middleware(['forget.parameters:livrare'])->name('pdf');
            });

            Route::group(['prefix' => 'comenzi-in-asteptare', 'as' => 'pending_orders.', 'controller' => 'OrdersController'], function () {
                Route::get('/', 'pending')->name('show');
                Route::post('importTwoShip', 'importTwoShip')->name('importTwoShip');
                Route::get('{livrare}', 'editPending')->name('edit');
                Route::post('{livrare}/update', 'updatePending')->name('updatePending');
                // Route::get('{livrare}/awb', 'awb')->name('awb');
                // Route::delete('{livrare}/sterge', 'destroy')->name('destroy');
                // Route::get('{cancelRequest}/accepta', 'acceptCancelRequest')->name('accept.cancel');
                // Route::get('{cancelRequest}/refuza', 'refuseCancelRequest')->name('refuse.cancel');
            });

            Route::group(['prefix' => 'rambursuri', 'as' => 'repayments.', 'controller' => 'RepaymentsController'], function () {
                Route::get('/', 'index')->name('show');
                Route::post('{repayment}', 'complete')->name('complete');
            });

            Route::group(['prefix' => 'anunt', 'as' => 'announcement.', 'controller' => 'AnnouncementController'], function () {
                Route::get('/', 'edit')->name('edit');
                Route::post('update', 'update')->name('update');
            });

            Route::group(['prefix' => 'curieri', 'as' => 'curieri.', 'controller' => 'CurieriController'], function () {
                Route::get('/', 'index')->name('show');
                Route::get('gateway', 'gatewaycall')->name('gate');
                Route::get('creare', 'create')->name('create');
                Route::post('salveaza', 'store')->name('store');
                Route::get('{curier}/editare', 'edit')->name('edit');
                Route::get('{id?}/adauga/logo', 'storeLogo')->name('store.logo');
                Route::post('{curier}/actualizare', 'update')->name('update');
                Route::delete('{curier}/sterge', 'destroy')->name('destroy');
                Route::get('{curier}/tarife/{user?}', 'editRates')->name('edit.rates');
                Route::post('{curier}/tarife/{user?}', 'updateRates')->name('update.rates');
            });

            Route::group(['prefix' => 'pagini', 'as' => 'pages.', 'controller' => 'PagesController'], function () {
                Route::get('/', 'index')->name('show');
                Route::get('creare', 'create')->name('create');
                Route::post('salveaza', 'store')->name('store');
                Route::get('{page}/editare', 'edit')->name('edit');
                Route::post('{page}/actualizare', 'update')->name('update');
                Route::get('{page}/modificare', 'editor')->name('editor');
                Route::get('{page}/raw', 'raw')->name('raw');
                Route::delete('{page}/sterge', 'destroy')->name('destroy');
            });

            Route::group(['prefix' => 'borderouri', 'as' => 'borderouri.', 'controller' => 'BorderouriController'], function () {
                Route::get('/', 'index')->name('show');
                Route::post('obtine/{user?}', 'get')->name('get');
                Route::get('creare', 'create')->name('create');
                Route::post('salveaza', 'save')->name('store');
                Route::get('{borderou}/editare', 'edit')->name('edit');
                Route::post('{borderou}/actualizare', 'save')->name('update');
                Route::delete('{borderou}/sterge', 'destroy')->name('destroy');

                Route::get('{borderou}/excel', 'downloadExcel')->name('export');
                Route::get('{borderou}/actualizare/api', 'updateBorderouManual')->name('update.api');
                Route::get('{borderou}/plata/api', 'sendApiRequestsBorderouManual')->name('payment.api');
            });

            Route::group(['prefix' => 'invoice-sheets', 'as' => 'invoice-sheets.', 'controller' => 'InvoiceSheetsController'], function () {
                Route::get('/', 'index')->name('show');
                Route::post('obtine/{user?}', 'get')->name('get');
                Route::get('creare', 'create')->name('create');
                Route::post('salveaza', 'save')->name('store');
                Route::get('{invoiceSheet}/editare', 'edit')->name('edit');
                Route::post('{invoiceSheet}/actualizare', 'save')->name('update');
                Route::delete('{invoiceSheet}/sterge', 'destroy')->name('destroy');

                Route::get('{invoiceSheet}/excel', 'downloadExcel')->name('export');
                Route::get('{invoiceSheet}/actualizare/api', 'updateInvoiceSheetManual')->name('update.api');
                Route::get('{invoiceSheet}/creaza/factura', 'createInvoiceFromSheet')->name('create.invoice');
            });
        });
    });

    Route::group(['controller' => 'HomeController'], function () {
        Route::get('/{page:slug}', 'page')->name('page');
    });
});