<?php

use App\Http\Controllers\AlertController;
use App\Http\Controllers\ArchivosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\InstanceController;
use App\Http\Controllers\JudicialDistrictController;
use App\Http\Controllers\ProceedingController;
use App\Http\Controllers\LawyerController;
use App\Http\Controllers\AssistantController;
use App\Http\Controllers\AudienceController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProceedingTypeController;
use App\Http\Controllers\FiscaliaController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportControllerpenal;
use App\Http\Controllers\ReportControllerarbitral;
use App\Http\Controllers\ReportControllerindecopi;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TradeReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WhatsappController;

// Define the route for getting office details by proceeding number outside of the auth middleware
Route::get('/public/proceeding/{expNumber}', [ProceedingController::class, 'showOfficeDetailsByExpNumber'])
    ->name('proceeding.showOfficeDetailsByExpNumber');

Route::prefix('/user')->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
});

Route::get('/pdf', 'App\Http\Controllers\ReportController@contarExpedientesPorAboTipo');

//Rutas protegidas por autenticación
Route::middleware(['auth:api'])->group(function () {
    Route::prefix('department')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('department.index');
        Route::get('/{id}/show', [DepartmentController::class, 'show'])->name('department.show');
        Route::post('/provincias', [DepartmentController::class, 'provincias'])->name('department.provincias');
        Route::post('/distritos', [DepartmentController::class, 'distritos'])->name('department.distritos');
    });

    // Abogados
    Route::prefix('lawyer')->group(function () {
        Route::get('/', [LawyerController::class, 'index'])->name('lawyer.index');
        Route::post('/listTrades', [LawyerController::class, 'listTrades'])->name('lawyer.trades');
        Route::post('/show', [LawyerController::class, 'show'])->name('lawyer.show');
        Route::post('/store', [LawyerController::class, 'store'])->name('lawyer.store');
        Route::post('/update', [LawyerController::class, 'update'])->name('lawyer.update');
        Route::post('/delete/{id}', [LawyerController::class, 'destroy'])->name('lawyer.destroy');
        Route::post('/audiencias', [LawyerController::class, 'audiencias'])->name('lawyer.audiencias');
        Route::post('/alertas', [LawyerController::class, 'alertas'])->name('lawyer.alertas');
        Route::post('/calendario', [LawyerController::class, 'calendario'])->name('lawyer.calendario');
        Route::post('/expedientes', [LawyerController::class, 'expedientes'])->name('lawyer.expedientes');
        Route::post('/changeOfLawyer', [LawyerController::class, 'changeOfLawyer'])->name('lawyer.changeOfLawyer');
    });

    // Expedientes
    Route::prefix('proceeding')->group(function () {
        Route::get('/', [ProceedingController::class, 'index'])->name('proceeding.index');
        Route::get('/{id}', [ProceedingController::class, 'show'])->name('proceeding.show');
        Route::get('/{id}/show', [ProceedingController::class, 'showupdate'])->name('proceeding.showupdate');
        Route::post('/take', [ProceedingController::class, 'take'])->name('proceeding.take');
        Route::post('/update', [ProceedingController::class, 'update'])->name('proceeding.update');
        Route::post('/registrarcaso', [ProceedingController::class, 'registrarcaso'])->name('proceeding.registrarcaso');
        Route::post('/listarestado', [ProceedingController::class, 'listarestado'])->name('proceeding.listarestado');
        Route::post('/buscarPorId', [ProceedingController::class, 'buscarPorId'])->name('proceeding.buscarPorId');
        Route::post('/filterprocesal', [ProceedingController::class, 'filterprocesal'])->name('proceeding.filterprocesal');
        Route::post('/archivados', [ProceedingController::class, 'archivados'])->name('proceeding.archivados');
        Route::post('/destroy', [ProceedingController::class, 'destroy'])->name('proceeding.destroy');
        Route::get('/delete/list', [ProceedingController::class, 'deletelist'])->name('proceeding.deletelist');
        Route::post('/audiencias', [ProceedingController::class, 'audiencias'])->name('proceeding.audiencias');
        Route::post('/alertas', [ProceedingController::class, 'alertas'])->name('proceeding.alertas');
        Route::post('/lawyer', [ProceedingController::class, 'lawyer'])->name('proceeding.lawyer');
    });

    //  Distritos Judiciales
    Route::prefix('judicialdistrict')->group(function () {
        Route::get('/', [JudicialDistrictController::class, 'index'])->name('judicialdistrict.index');
        Route::get('/show', [JudicialDistrictController::class, 'show'])->name('judicialdistrict.show');
        Route::post('/store', [JudicialDistrictController::class, 'store'])->name('judicialdistrict.store');
        Route::put('/update', [JudicialDistrictController::class, 'update'])->name('judicialdistrict.update');
        Route::delete('/destroy', [JudicialDistrictController::class, 'destroy'])->name('judicialdistrict.destroy');
    });
    //instancias
    Route::prefix('instance')->group(function () {
        Route::get('/', [InstanceController::class, 'index'])->name('Instance.index');
        Route::get('/show', [InstanceController::class, 'show'])->name('Instance.show');
        Route::post('/store', [InstanceController::class, 'store'])->name('Instance.store');
        Route::put('/update', [InstanceController::class, 'update'])->name('Instance.update');
        Route::delete('/destroy/{id}', [InstanceController::class, 'destroy'])->name('Instance.destroy');
    });
    //fiscalias
    Route::prefix('fiscalia')->group(function () {
        Route::post('/', [FiscaliaController::class, 'index'])->name('Fiscalia.index');
        Route::post('/show', [FiscaliaController::class, 'show'])->name('Fiscalia.show');
        Route::post('/store', [FiscaliaController::class, 'store'])->name('Fiscalia.store');
        Route::put('/update', [FiscaliaController::class, 'update'])->name('Fiscalia.update');
        Route::delete('/destroy', [FiscaliaController::class, 'destroy'])->name('Fiscalia.destroy');
    });
    //especialidades
    Route::prefix('specialty')->group(function () {
        Route::get('/', [SpecialtyController::class, 'index'])->name('specialty.index');
        Route::post('/show', [SpecialtyController::class, 'show'])->name('specialty.show');
        Route::post('/store', [SpecialtyController::class, 'store'])->name('specialty.registrar');
        Route::post('/update', [SpecialtyController::class, 'update'])->name('specialty.update');
        Route::post('/destroy', [SpecialtyController::class, 'destroy'])->name('specialty.eliminar');
    });

    Route::prefix('personas')->group(function () {
        Route::get('/', [PersonController::class, 'index'])->name('person.index');
        Route::post('/equipo', [PersonController::class, 'equipo'])->name('person.equipo');
        Route::post('/crearIntegrante', [LawyerController::class, 'crearIntegrante'])->name('lawyer.crearIntegrante');
        Route::post('/detallePersona', [PersonController::class, 'detallePersona'])->name('person.detallePersona');
        Route::post('/sucesor', [PersonController::class, 'listarSucesor'])->name('person.listarSucesor');
        Route::post('/storesucesor', [PersonController::class, 'añadirSucesor'])->name('person.añadirSucesor');
    });


    // Demandantes
    Route::prefix('demandante')->group(function () {
        Route::get('/', [PersonController::class, 'index'])->name('demandante.index');
        Route::get('/detalledemandante/{doc}', [PersonController::class, 'detalledemandante'])->name('demandante.detalledemandante');
        Route::post('/expedientes', [PersonController::class, 'traerexpedientes'])->name('demandante.traerexpedientes');
        Route::get('/direccion/{doc}', [PersonController::class, 'getAddressByDocument'])->name('demandante.getaddressbydocument');
        Route::get('/historial/{doc}', [PersonController::class, 'getHistoryByDocument'])->name('demandante.gethistorybydocument');
        Route::get('/pagos/{doc}', [PersonController::class, 'getPaymentsByDocument'])->name('demandante.getpaymentsbydocument');
        Route::post('/updateDni', [PersonController::class, 'updateDni'])->name('demandante.updateDni');
        Route::post('/logout', [PersonController::class, 'salir'])->name('demandante.salir');
    });

    Route::prefix('demandado')->group(function () {
        Route::get('/', [PersonController::class, 'indexdemandados'])->name('demandado.indexdemandados');
        Route::get('/detalledemandado/{doc}', [PersonController::class, 'detalledemandado'])->name('demandado.detalledemandado');
        Route::get('/historial/{doc}', [PersonController::class, 'getHistoryByDocument'])->name('demandado.gethistorybydocument');
        Route::post('/expedientes', [PersonController::class, 'traerexpedientesDemandado'])->name('demandado.traerexpedientesDemandado');
    });

    // Historial de Comunicaciones
    Route::prefix('history')->group(function () {
        Route::get('/', [HistoryController::class, 'index'])->name('history.index');
        Route::post('/store', [HistoryController::class, 'store'])->name('history.store');
        Route::get('data/{doc}', [HistoryController::class, 'data'])->name('history.data');
        Route::post('/showPerson', [HistoryController::class, 'showPerson'])->name('history.showPerson');
    });

    // Historial de Pagos
    // Route::prefix('payment')->group(function () {
    //     Route::get('/', [PaymentC::class, 'index'])->name('payment.index');
    //     Route::post('/store', [PaymentController::class, 'store'])->name('payment.store');
    // });

    // Generacion de Reportes
    Route::prefix('reportes')->group(function () {
        Route::post('/inicio', [ReportController::class, 'inicio'])->name('reportes.inicio');
        Route::post('/inicioadmin', [ReportController::class, 'inicioAdmin'])->name('reportes.inicioAdmin');
        Route::post('/exprecientes', [ReportController::class, 'exprecientes'])->name('reportes.exprecientes');
        Route::post('/distritos', [ReportController::class, 'distritos'])->name('reportes.distritos');
    });

    // Generacion de Reportes  pdf
    Route::prefix('reportespfd')->group(function () {
        Route::get('/pdfexparchivados', [ReportController::class, 'pdfexparchivados'])->name('reportes.pdfexptramite');
        Route::get('/pdfabogados', [ReportController::class, 'pdfabogados'])->name('reportes.pdfabogados');
        Route::get('/pdfexptramite', [ReportController::class, 'pdfexptramite'])->name('reportes.pdfexptramite');
        Route::get('/pdfexpejecucion', [ReportController::class, 'pdfexpejecucion'])->name('reportes.pdfexpejecucion');
        Route::get('/pdfexps', [ReportController::class, 'pdfexps'])->name('reportes.pdfexps');
        Route::get('/pdfdemandantes', [ReportController::class, 'pdfdemandantes'])->name('reportes.pdfdemandantes');
        Route::get('/pdffechaaño', [ReportController::class, 'pdffechaaño'])->name('reportes.pdffechaaño');
        Route::get('/pdfmateria', [ReportController::class, 'pdfmateria'])->name('reportes.pdfmateria');
        Route::get('/pdfexpsabogado', [ReportController::class, 'pdfexpsabogado'])->name('reportes.pdfexpsabogado');
        Route::get('/pdfpretensiones', [ReportController::class, 'pdfpretenciones'])->name('reportes.pdfpretenciones');
        Route::get('/pdfejecuciones', [ReportController::class, 'pdfejecuciones'])->name('reportes.pdfejecuciones');
        Route::get('/pdfpretension', [ReportController::class, 'pdfpretension'])->name('reportes.pdfpretension');
        Route::get('/pdffechas', [ReportController::class, 'pdffechas'])->name('reportes.pdffechas');
        Route::get('/pdfdistrito', [ReportController::class, 'pdfdistrito'])->name('reportes.pdfdistrito');
        Route::get('/pdfbarras', [ReportController::class, 'contarExpedientesPorAnio'])->name('reportes.contarExpedientesPorAnio');
        Route::get('/abocantidad', [ReportController::class, 'contarExpedientesPorAboTipo'])->name('reportes.contarExpedientesPorAboTipo');
        Route::get('/proceedingType', [ReportController::class, 'proceedingType'])->name('reportes.proceedingType');
    });

    Route::prefix('reportespfdpenal')->group(function () {
        Route::get('/pdfexparchivados', [ReportControllerpenal::class, 'pdfexparchivados'])->name('reportes.pdfexptramite');
        Route::get('/pdfabogados', [ReportControllerpenal::class, 'pdfabogados'])->name('reportes.pdfabogados');
        Route::get('/pdfexptramite', [ReportControllerpenal::class, 'pdfexptramite'])->name('reportes.pdfexptramite');
        Route::get('/pdfexpejecucion', [ReportControllerpenal::class, 'pdfexpejecucion'])->name('reportes.pdfexpejecucion');
        Route::get('/pdfexps', [ReportControllerpenal::class, 'pdfexps'])->name('reportes.pdfexps');
        Route::get('/pdfdemandantes', [ReportControllerpenal::class, 'pdfdemandantes'])->name('reportes.pdfdemandantes');
        Route::get('/pdffechaaño', [ReportControllerpenal::class, 'pdffechaaño'])->name('reportes.pdffechaaño');
        Route::get('/pdfmateria', [ReportControllerpenal::class, 'pdfmateria'])->name('reportes.pdfmateria');
        Route::get('/pdfexpsabogado', [ReportControllerpenal::class, 'pdfexpsabogado'])->name('reportes.pdfexpsabogado');
        Route::get('/pdfpretensiones', [ReportControllerpenal::class, 'pdfpretenciones'])->name('reportes.pdfpretenciones');
        Route::get('/pdfejecuciones', [ReportControllerpenal::class, 'pdfejecuciones'])->name('reportes.pdfejecuciones');
        Route::get('/pdfpretension', [ReportControllerpenal::class, 'pdfpretension'])->name('reportes.pdfpretension');
        Route::get('/pdffechas', [ReportControllerpenal::class, 'pdffechas'])->name('reportes.pdffechas');
        Route::get('/pdfdistrito', [ReportControllerpenal::class, 'pdfdistrito'])->name('reportes.pdfdistrito');
        Route::get('/pdfbarras', [ReportControllerpenal::class, 'contarExpedientesPorAnio'])->name('reportes.contarExpedientesPorAnio');
        Route::get('/abocantidad', [ReportControllerpenal::class, 'contarExpedientesPorAboTipo'])->name('reportes.contarExpedientesPorAboTipo');
        Route::get('/proceedingType', [ReportControllerpenal::class, 'proceedingType'])->name('reportes.proceedingType');
    });
    Route::prefix('reportespfdarbitral')->group(function () {
        Route::get('/pdfexparchivados', [ReportControllerarbitral::class, 'pdfexparchivados'])->name('reportes.pdfexptramite');
        Route::get('/pdfabogados', [ReportControllerarbitral::class, 'pdfabogados'])->name('reportes.pdfabogados');
        Route::get('/pdfexptramite', [ReportControllerarbitral::class, 'pdfexptramite'])->name('reportes.pdfexptramite');
        Route::get('/pdfexpejecucion', [ReportControllerarbitral::class, 'pdfexpejecucion'])->name('reportes.pdfexpejecucion');
        Route::get('/pdfexps', [ReportControllerarbitral::class, 'pdfexps'])->name('reportes.pdfexps');
        Route::get('/pdfdemandantes', [ReportControllerarbitral::class, 'pdfdemandantes'])->name('reportes.pdfdemandantes');
        Route::get('/pdffechaaño', [ReportControllerarbitral::class, 'pdffechaaño'])->name('reportes.pdffechaaño');
        Route::get('/pdfmateria', [ReportControllerarbitral::class, 'pdfmateria'])->name('reportes.pdfmateria');
        Route::get('/pdfexpsabogado', [ReportControllerarbitral::class, 'pdfexpsabogado'])->name('reportes.pdfexpsabogado');
        Route::get('/pdfpretensiones', [ReportControllerarbitral::class, 'pdfpretenciones'])->name('reportes.pdfpretenciones');
        Route::get('/pdfejecuciones', [ReportControllerarbitral::class, 'pdfejecuciones'])->name('reportes.pdfejecuciones');
        Route::get('/pdfpretension', [ReportControllerarbitral::class, 'pdfpretension'])->name('reportes.pdfpretension');
        Route::get('/pdffechas', [ReportControllerarbitral::class, 'pdffechas'])->name('reportes.pdffechas');
        Route::get('/pdfdistrito', [ReportControllerarbitral::class, 'pdfdistrito'])->name('reportes.pdfdistrito');
        Route::get('/pdfbarras', [ReportControllerarbitral::class, 'contarExpedientesPorAnio'])->name('reportes.contarExpedientesPorAnio');
        Route::get('/abocantidad', [ReportControllerarbitral::class, 'contarExpedientesPorAboTipo'])->name('reportes.contarExpedientesPorAboTipo');
        Route::get('/proceedingType', [ReportControllerarbitral::class, 'proceedingType'])->name('reportes.proceedingType');
    });
    Route::prefix('reportespfdindecopi')->group(function () {
        Route::get('/pdfexparchivados', [ReportControllerindecopi::class, 'pdfexparchivados'])->name('reportes.pdfexptramite');
        Route::get('/pdfabogados', [ReportControllerindecopi::class, 'pdfabogados'])->name('reportes.pdfabogados');
        Route::get('/pdfexptramite', [ReportControllerindecopi::class, 'pdfexptramite'])->name('reportes.pdfexptramite');
        Route::get('/pdfexpejecucion', [ReportControllerindecopi::class, 'pdfexpejecucion'])->name('reportes.pdfexpejecucion');
        Route::get('/pdfexps', [ReportControllerindecopi::class, 'pdfexps'])->name('reportes.pdfexps');
        Route::get('/pdfdemandantes', [ReportControllerindecopi::class, 'pdfdemandantes'])->name('reportes.pdfdemandantes');
        Route::get('/pdffechaaño', [ReportControllerindecopi::class, 'pdffechaaño'])->name('reportes.pdffechaaño');
        Route::get('/pdfmateria', [ReportControllerindecopi::class, 'pdfmateria'])->name('reportes.pdfmateria');
        Route::get('/pdfexpsabogado', [ReportControllerindecopi::class, 'pdfexpsabogado'])->name('reportes.pdfexpsabogado');
        Route::get('/pdfpretensiones', [ReportControllerindecopi::class, 'pdfpretenciones'])->name('reportes.pdfpretenciones');
        Route::get('/pdfejecuciones', [ReportControllerindecopi::class, 'pdfejecuciones'])->name('reportes.pdfejecuciones');
        Route::get('/pdfpretension', [ReportControllerindecopi::class, 'pdfpretension'])->name('reportes.pdfpretension');
        Route::get('/pdffechas', [ReportControllerindecopi::class, 'pdffechas'])->name('reportes.pdffechas');
        Route::get('/pdfdistrito', [ReportControllerindecopi::class, 'pdfdistrito'])->name('reportes.pdfdistrito');
        Route::get('/pdfbarras', [ReportControllerindecopi::class, 'contarExpedientesPorAnio'])->name('reportes.contarExpedientesPorAnio');
        Route::get('/abocantidad', [ReportControllerindecopi::class, 'contarExpedientesPorAboTipo'])->name('reportes.contarExpedientesPorAboTipo');
        Route::get('/proceedingType', [ReportControllerindecopi::class, 'proceedingType'])->name('reportes.proceedingType');
    });
    // Audiencias
    Route::prefix('audiences')->group(function () {
        Route::get('/', [AudienceController::class, 'index'])->name('audiences.index');
        Route::post('/store', [AudienceController::class, 'store'])->name('audiences.store');
    });

    // guardar y descargar archivos
    Route::prefix('archivos')->group(function () {
        Route::get('/descargar', [ArchivosController::class, 'descargar'])->name('archivos.descargar');
        Route::post('/guardar', [ArchivosController::class, 'guardar'])->name('archivos.guardar');
        Route::post('/actualizar/eje', [ArchivosController::class, 'actualizarEje'])->name('archivos.actualizarEje');
        Route::post('/guardarArchivoAdm', [ArchivosController::class, 'guardarArchivoAdm'])->name('archivos.guardarArchivoAdm');
        Route::post('/actualizarArchivoAdm', [ArchivosController::class, 'actualizarArchivoAdm'])->name('archivos.actualizarArchivoAdm');
    });

    Route::prefix('excel')->group(function () {
        Route::post('/cargar', [ExcelController::class, 'index'])->name('excel.index');
    });

    Route::prefix('traer')->group(function () {
        Route::get('/archivo', [ArchivosController::class, 'traerpdfprincipal'])->name('traer.traerpdfprincipal');
    });

    Route::prefix('mensajes')->group(function () {
        Route::get('/', [WhatsappController::class, 'index'])->name('mensajes.index');
    });

    Route::prefix('alerta')->group(function () {
        Route::get('/', [AlertController::class, 'index'])->name('alerta.index');
        Route::post('/store', [AlertController::class, 'store'])->name('mensajes.store');
    });

    Route::prefix('calendario')->group(function () {
        Route::get('/', [CalendarioController::class, 'index'])->name('calendario.index');
    });

    //Juzgados
    Route::prefix('juzgado')->group(function () {
        Route::post('/', [CourtController::class, 'index'])->name('juzgado.index');
        Route::post('/store', [CourtController::class, 'store'])->name('juzgado.store');
        Route::post('/destroy', [CourtController::class, 'destroy'])->name('juzgado.destroy');
        Route::post('/update', [CourtController::class, 'update'])->name('juzgado.update');
        Route::post('/favorite', [CourtController::class, 'favorite'])->name('juzgado.favorite');
    });

    //materias
    Route::prefix('subject')->group(function () {
        Route::get('/', [SubjectController::class, 'index'])->name('subject.index');
        Route::post('/show', [SubjectController::class, 'show'])->name('subject.show');
        Route::post('/store', [SubjectController::class, 'registrar'])->name('subject.registrar');
        Route::post('/update', [SubjectController::class, 'update'])->name('subject.update');
        Route::post('/destroy', [SubjectController::class, 'eliminar'])->name('subject.eliminar');
    });

    //pretensiones
    Route::prefix('claim')->group(function () {
        Route::get('/', [ClaimController::class, 'index'])->name('claim.index');
        Route::post('/show', [ClaimController::class, 'show'])->name('claim.show');
        Route::post('/store', [ClaimController::class, 'registrar'])->name('claim.registrar');
        Route::post('/update', [ClaimController::class, 'update'])->name('claim.update');
        Route::post('/destroy', [ClaimController::class, 'eliminar'])->name('claim.eliminar');
    });

    Route::prefix('mail')->group(function () {
        Route::post('/', [MailController::class, 'mail'])->name('mail.mail');
    });

    //GESTION ADMINISTRATIVA
    //areas
    Route::prefix('area')->group(function () {
        Route::get('/', 'App\Http\Controllers\AreaController@index')->name('area.index');
    });

    //type_references
    Route::prefix('type_reference')->group(function () {
        Route::get('/', 'App\Http\Controllers\TypeReferenceController@index')->name('type_ref.index');
    });

    //Oficio
    Route::prefix('trade')->group(function () {
        Route::get('/', 'App\Http\Controllers\TradeController@index')->name('trade.index');
        Route::get('/nextTraNumber', 'App\Http\Controllers\TradeController@getNextTraNumber')->name('trade.nextTraNumber');
        Route::get('/{id}', 'App\Http\Controllers\TradeController@show')->name('trade.show');
        Route::post('/create', 'App\Http\Controllers\TradeController@create')->name('trade.create');
    });

    //GESTION ADMINISTRATIVA
    //areas
    Route::prefix('assistant')->group(function () {
        Route::get('/', 'App\Http\Controllers\AssistantController@index')->name('assistant.index');
        Route::post('/listTrades', [AssistantController::class, 'listTrades'])->name('assistant.trades');
    });

    //Clientes
    Route::prefix('person')->group(function () {
        Route::get('/', 'App\Http\Controllers\PersonController@indexPersons')->name('person.index');
    });

    //Tipo de expedientes
    Route::prefix('proceedingTypes')->group(function () {
        Route::get('/', [ProceedingTypeController::class, 'index'])->name('proceedingTypes.index');
    });

    //Report
    Route::prefix('report')->group(function () {
        Route::get('/', 'App\Http\Controllers\TradeReportController@index')->name('report.index');
        Route::post('/create', [TradeReportController::class, 'create'])->name('report.index');
        Route::post('/createExpLeg', [TradeReportController::class, 'createExpLeg'])->name('report.createExpLeg');
        Route::get('/getNextRepNumber', [TradeReportController::class, 'getNextRepNumber'])->name('report.getNextRepNumber');
        Route::get('/numInfoNumber/{abo}', [TradeReportController::class, 'getNumInfoNumber'])->name('report.getNumInfoNumber');
        Route::put('/update', 'App\Http\Controllers\TradeReportController@update')->name('report.update');
    });

    //Oficios de expediente
    Route::prefix('proceeding-oficios')->group(function () {

        Route::get('/', 'App\Http\Controllers\OfficeProceedingController@getOfficeProceeding');

        Route::get('/{id}', 'App\Http\Controllers\OfficeProceedingController@getOfficeProceedingxid');

        Route::post('/', 'App\Http\Controllers\OfficeProceedingController@insertOfficeProceeding');

        Route::put('/{id}', 'App\Http\Controllers\OfficeProceedingController@updateOfficeProceeding');

        Route::delete('/{id}', 'App\Http\Controllers\OfficeProceedingController@deleteOfficeProceeding');
    });
});
