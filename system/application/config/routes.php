<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| 	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['scaffolding_trigger'] = 'scaffolding';
|
| This route lets you set a "secret" word that will trigger the
| scaffolding feature for added security. Note: Scaffolding must be
| enabled in the controller in which you intend to use it.   The reserved
| routes must come before any wildcard or regular expression routes.
|
*/

if (isset($_SERVER['SERVER_NAME']) && ($_SERVER['SERVER_NAME'] == "sms.lacak-mobil.com"))
{
        $route['default_controller'] = "sms";
}
else
{
        $route['default_controller'] = "home";
}

$route['scaffolding_trigger']                                  = "";
// ATTACHMENT VIEW
$route['attachment/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)'] = 'attachmentview/evidence/$1/$2/$3/$4/$5/$6';
$route['attachment/playbackhistory/(:any)/(:any)/(:any)']      = 'attachmentview/playbackhistory/$1/$2/$3';
// SINGLE URL
$route['view/heatmap']                                         = 'maps/singleheatmap'; // HEATMAP
$route['view/quickcount']                                      = 'maps/singlequickcount'; // QUICKCOUNT
$route['view/rom']                                             = 'maps/singlerom'; // ROM
$route['view/port']                                            = 'maps/singleport'; // PORT
$route['view/pool']                                            = 'maps/singlepool'; // POOL
$route['view/outofhauling']                                    = 'maps/singleoutofhauling'; // OUT OF HAULING
$route['view/offlinevehicle']                                  = 'maps/singleofflinevehicle'; // OFFLINE VEHICLE
$route['view/mapsstandard']                                    = 'maps/singlemapsstandard'; // MAPS STANDARD
// POC IN ROM
$route['dashboard/view/rom']                                   = 'dashboardview/viewrom';
$route['dashboard/view/mapsstandard']                          = 'dashboardview/viewmapsstandard';
//WIM
$route['wim/replacement/report']                               = 'wim/replacementreport'; // REPLACEMENT REPORT
//REPORT
$route['report/history/view']                                  = 'historyfmsnew/formhistory';
$route['driverdetected']                                       = 'driverchange';
// KHUSUS JALUR TIA
$route['view/tia/operation']                                   = 'maps/quickcounttiaunit';
$route['view/tia/violation']                                   = 'violation/quickcounttiaviolation';
// KHUSUS VEHICLE BIB
$route['view/bib/vehicle']                                     = 'maps/vehicletrackingbib';
// VIEW VIOLATION MONITORING HISTORIKAL
// $route['violation/table']                                      = 'violation/violation_historikal'; // SEMENTARA DIMATIKAN
$route['dev/dashboard-intervention']                           = 'violation/violation_historikal';
$route['violation/historikal']                                 = 'violation/violation_historikalreport';
$route['dev/dashboard-post-event']                             = 'development/dashboardpostevent';
$route['dashboard/post-event']                                 = 'dashboardberau/dashboardpostevent';
$route['dashboard/intervention']                               = 'dashboardberau/posteventcontrolroom';

$route['dashboard/intervention-1']                             = 'dashboard_intervensi_postevent/dashboardintervention';
$route['dashboard/intervention-2']                             = 'dashboard_intervensi_postevent/dashboardintervention_2';

$route['dashboard/intervention/pushalert-1']                   = 'dashboard_intervensi_postevent_pushalert/dashboardintervention';
$route['dashboard/intervention/pushalert-2']                   = 'dashboard_intervensi_postevent_pushalert/dashboardintervention_2';


// HRM
$route['view/hrm/maps']                                        = 'maps/mapsstandardhrm';


// DASHBOARD SMARTWATCH ABDITEK
$route['abdiwatch/dashboard']                                   = 'dashboardview/abdiwatch_dashboard';





// ROUTE TRANSPORTER BARU
// $route['user'] = "newtransporter_user/";
// $route['user'] = 'newtransporter_user/add';


/* End of file routes.php */
/* Location: ./system/application/config/routes.php */
