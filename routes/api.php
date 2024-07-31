<?php

use App\Http\Controllers\AboutUsController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BeforeAndAfterController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BrancheController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactFormController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\GlobalSeoController;
use App\Http\Controllers\GovernorateController;
use App\Http\Controllers\HealthcareProviderController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\MedicalConditionController;
// use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\SubSpecialtyController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\TermsAndConditionController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\VideoController;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('users', [AuthController::class, 'index']);
Route::delete('user/{user}', [AuthController::class, 'destroy']);
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

############################################################ Resourses Section ########################################################################
// Route::apiResource('sliders', SliderController::class);
Route::apiResource('roles', RoleController::class);
Route::apiResource('permissions', PermissionController::class);
Route::apiResource('folders', FolderController::class);
Route::apiResource('folders.files', FileController::class);
Route::apiResource('blogs', BlogController::class);
// Route::apiResource('countries', CountryController::class);
// Route::apiResource('governorates', GovernorateController::class);
// Route::apiResource('areas', AreaController::class);
Route::apiResource('services', ServiceController::class);
// Route::apiResource('subSpecialties', SubSpecialtyController::class);
// Route::apiResource('healthcareProviders', HealthcareProviderController::class);
Route::apiResource('doctors', DoctorController::class);
// Route::apiResource('insurances', InsuranceController::class);
Route::apiResource('templates', TemplateController::class);
Route::apiResource('globalseo', GlobalSeoController::class);
Route::apiResource('contacts', ContactController::class);
Route::apiResource('faqs', FaqController::class);
Route::apiResource('termsandconditions', TermsAndConditionController::class);
Route::apiResource('privacypolicies', PrivacyPolicyController::class);
Route::apiResource('news', NewsController::class);
Route::apiResource('schedules', ScheduleController::class);
// Route::apiResource('branches', BrancheController::class);
Route::apiResource('aboutus', AboutUsController::class);
Route::apiResource('baf', BeforeAndAfterController::class);
Route::apiResource('testimonials', TestimonialController::class);
Route::apiResource('cases', MedicalConditionController::class);
Route::apiResource('reviews', ReviewController::class);
Route::apiResource('enquiries', EnquiryController::class);
Route::apiResource('contact_form', ContactFormController::class);
Route::apiResource('sliders', SliderController::class);
Route::apiResource('playlists', PlaylistController::class);
Route::apiResource('videos', VideoController::class);

// Route::apiResource('categories', CategoryController::class);
###############################################################Front Section############################################################
Route::middleware(['setLanguage'])->group(function () {
    #Blogs
    Route::get('allBlogs', [BlogController::class, 'getAllBlogs']);
    Route::get('featuredBlogs', [BlogController::class, 'getFeaturedBlogs']);
    Route::get('singleBlog/{id}', [BlogController::class, 'getSingleBlog']);
    #Services
    Route::get('allServices', [ServiceController::class, 'getAllServices']);
    Route::get('featuredServices', [ServiceController::class, 'getFeaturedServices']);
    Route::get('singleService/{id}', [ServiceController::class, 'getSingleService']);

    #Doctor
    Route::get('allDoctors', [DoctorController::class, 'getAll']);
    Route::get('featuredDoctors', [DoctorController::class, 'getFeatured']);
    Route::get('singleDoctor/{id}', [DoctorController::class, 'getSingle']);
    #Country
    // Route::get('allCountries', [CountryController::class, 'getAllCountries']);
    // #Governorate 
    // Route::get('allGovernorates', [GovernorateController::class, 'getAllGovernorates']);
    // #area
    // Route::get('allAreas/{governorate}', [AreaController::class, 'getGovernoratesAreas']);
    #Templates
    Route::get('singleTemplate/{id}', [TemplateController::class, 'getSingleTemplate']);
    #Globle Seo
    Route::get('singleGlobalSeo/{id}', [GlobalSeoController::class, 'getSingleGlobaleSeo']);
    #contat
    Route::get('singleContact/{id}', [ContactController::class, 'getSingleContact']);
    #FAQS
    Route::get('allFaqs', [FaqController::class, 'getAllFaqs']);

    Route::get('allPrivacyPolicies', [PrivacyPolicyController::class, 'getAllData']);

    Route::get('allTermsandConditions', [TermsAndConditionController::class, 'getAllData']);
    Route::get('/about-us/{id}', [AboutUsController::class, 'getSingleAboutus']);
    // Before And After
    Route::get('featuredBafs', [BeforeAndAfterController::class, 'getFeatured']);
    Route::get('allBafs', [BeforeAndAfterController::class, 'getAll']);

    Route::get('allTestimonials', [TestimonialController::class, 'getAll']);
    Route::get('allCases', [MedicalConditionController::class, 'getAll']);
    Route::get('allReviews', [ReviewController::class, 'getAll']);
    Route::get('day-slots/{day_name}', [ScheduleController::class, 'getDaySlots']);
    Route::get('allSliders', [SliderController::class, 'getAll']);
    // Route::get('/filter/healthcare-providers', [HealthcareProviderController::class, 'filter']);
    // Route::get('/singleHealthcareProvider/{id}', [HealthcareProviderController::class, 'singleHealthcareProvider']);
    Route::get('allPlaylists', [PlaylistController::class, 'getAll']);
    Route::get('allVideos', [VideoController::class, 'getAll']);
});

###################################################Delete Section ##########################################################################
#Country Delete
// Route::delete('/countries/{country}', [CountryController::class, 'softDelete']);
// Route::delete('/countries/{country}/force', [CountryController::class, 'forceDelete']);
// Route::get('/countries/deleted', [CountryController::class,'getDeletedCountries']);

// #Governorate Delete
// Route::delete('/governorates/{governorate}', [GovernorateController::class, 'softDelete']);
// Route::delete('/governorates/{governorate}/force', [GovernorateController::class, 'forceDelete']);
// Route::get('/countries/deleted', [CountryController::class,'getDeletedCountries']);

#Area Delete
// Route::delete('/areas/{area}', [AreaController::class, 'softDelete']);
// Route::delete('/areas/{area}/force', [AreaController::class, 'forceDelete']);
// Route::get('/countries/deleted', [CountryController::class,'getDeletedCountries']);

#Specialty Delete
Route::delete('/services/{service}', [ServiceController::class, 'softDelete']);
Route::delete('/services/{service}/force', [ServiceController::class, 'forceDelete']);
// Route::get('/countries/deleted', [CountryController::class,'getDeletedCountries']);

#SubSpecialty Delete
Route::delete('/subSpecialties/{specialty}', [SubSpecialtyController::class, 'softDelete']);
Route::delete('/subSpecialties/{specialty}/force', [SubSpecialtyController::class, 'forceDelete']);
// Route::get('/countries/deleted', [CountryController::class,'getDeletedCountries']);

#Healthcare ProviderController Delete
// Route::delete('/healthcareProviders/{healthcareProvider}', [HealthcareProviderController::class, 'softDelete']);
// Route::delete('/healthcareProviders/{healthcareProvider}/force', [HealthcareProviderController::class, 'forceDelete']);
// Route::get('/countries/deleted', [CountryController::class,'getDeletedCountries']);

#Branches Delete
// Route::delete('/branches/{branche}', [BrancheController::class, 'softDelete']);
// Route::delete('/branches/{branche}/force', [BrancheController::class, 'forceDelete']);
// Route::get('/countries/deleted', [CountryController::class,'getDeletedCountries']);
#Doctors Delete
Route::delete('/doctors/{specialty}', [DoctorController::class, 'softDelete']);
Route::delete('/doctors/{specialty}/force', [DoctorController::class, 'forceDelete']);
// Route::get('/countries/deleted', [CountryController::class,'getDeletedCountries']);

############################################################################################################
// Route::get('/governoratesByCountryid/{country_id}', [GovernorateController::class,'getAllGovernoratesByCountryid']);
// Route::get('/subSpecialty/{specialty_id}', [SubSpecialtyController::class,'getSubSpecialtyBySpecialtyid']);
// Route::get('/areasByGovernorateid/{governorate_id}', [AreaController::class,'getAllAreasByGovernorateid']);

