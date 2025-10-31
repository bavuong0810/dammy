const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        //
    ]);

//mix admin
mix.scripts([
    'public/js/popper.min.js',
    'public/plugins/jquery/jquery.min.js',
    'public/plugins/jquery-ui/jquery-ui.min.js',
    'public/js/bootstrap.min.js',
    'public/plugins/bootstrap/js/bootstrap.bundle.min.js',
    'public/plugins/moment/moment.min.js',
    'public/plugins/daterangepicker/daterangepicker.js',
    'public/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js',
    'public/plugins/bootstrap-toggle/bootstrap-toggle.min.js',
    'public/plugins/summernote/summernote-bs4.min.js',
    'public/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js',
    'public/plugins/datatables/jquery.dataTables.min.js',
    'public/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js',
    'public/plugins/datatables-responsive/js/dataTables.responsive.min.js',
    'public/plugins/datatables-responsive/js/responsive.bootstrap4.min.js',
    'public/plugins/jquery-validation/jquery.validate.min.js',
    'public/plugins/jquery-validation/additional-methods.min.js',
    'public/plugins/bootstrap-multiselect/bootstrap-multiselect.js',
    'public/plugins/select2/js/select2.full.min.js',
    'public/plugins/sweetalert2/sweetalert2.min.js',
    'public/js/adminlte.js',
    'public/js/jquery-confirm.min.js',
    'public/js/slugify.js',
    'public/js/demo.js'
], 'public/js/js-admin.min.js');

mix.styles([
    'public/css/fontawesome-all.min.css',
    'public/css/ionicons.min.css',
    'public/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css',
    'public/plugins/bootstrap-toggle/bootstrap-toggle.min.css',
    'public/plugins/icheck-bootstrap/icheck-bootstrap.min.css',
    'public/plugins/jquery-ui/jquery-ui.min.css',
    'public/css/adminlte.min.css',
    'public/css/bootstrap.min.css',
    'public/css/bootstrap-theme.min.css',
    'public/plugins/overlayScrollbars/css/OverlayScrollbars.min.css',
    'public/plugins/daterangepicker/daterangepicker.css',
    'public/plugins/summernote/summernote-bs4.min.css',
    'public/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css',
    'public/plugins/bootstrap-multiselect/bootstrap-multiselect.css',
    'public/plugins/datatables-responsive/css/responsive.bootstrap4.min.css',
    'public/plugins/select2/css/select2.min.css',
    'public/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css',
    'public/css/jquery-confirm.min.css',
    'public/plugins/jQuery-File-Upload/css/jquery.fileupload.css',
    'public/plugins/jQuery-File-Upload/css/jquery.fileupload-ui.css',
    'public/plugins/sweetalert2/sweetalert2.min.css',
], 'public/css/style-admin.min.css');

//mix client
mix.scripts([
    'public/assets/js/bootstrap.bundle.min.js',
    'public/assets/js/jquery.min.js',
    'public/plugins/jquery-ui/jquery-ui.min.js',
    'public/assets/plugins/simplebar/js/simplebar.min.js',
    'public/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js',
    'public/assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js',
    'public/assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js',
    'public/plugins/sweetalert2/sweetalert2.min.js',
    'public/plugins/OwlCarousel2-2.3.4/dist/owl.carousel.min.js',
    'public/plugins/lazyload/lazyload.min.js',
    'public/assets/js/app.js',
], 'public/assets/app.min.js');

mix.styles([
    'public/assets/plugins/vectormap/jquery-jvectormap-2.0.2.css',
    'public/plugins/jquery-ui/jquery-ui.min.css',
    'public/assets/plugins/simplebar/css/simplebar.css',
    'public/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css',
    'public/assets/css/bootstrap.min.css',
    'public/assets/css/bootstrap-extended.css',
    'public/assets/css/icons.css',
    'public/plugins/sweetalert2/sweetalert2.min.css',
    'public/plugins/OwlCarousel2-2.3.4/dist/assets/owl.carousel.min.css',
    'public/plugins/OwlCarousel2-2.3.4/dist/assets/owl.theme.default.min.css',
    'public/assets/css/dark-theme.css',
    'public/assets/css/semi-dark.css',
    'public/assets/css/header-colors.css',
    'public/assets/css/app.css',
], 'public/assets/app.min.css');

mix.styles([
    'public/css/style_noel.css',
], 'public/assets/style-noel.min.css');
