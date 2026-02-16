<script src="<?php echo APP_URL; ?>app/views/assets/js/jquery-3.7.1.min.js"></script>
<script src="<?php echo APP_URL; ?>app/views/assets/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo APP_URL; ?>app/views/assets/js/sweetalert2.all.min.js"></script>

<script src="<?php echo APP_URL; ?>app/views/assets/js/jquery.dataTables.min.js"></script>
<script src="<?php echo APP_URL; ?>app/views/assets/js/dataTables.bootstrap5.min.js"></script>

<script src="<?php echo APP_URL; ?>app/views/assets/js/dataTables.responsive.min.js"></script>
<script src="<?php echo APP_URL; ?>app/views/assets/js/responsive.bootstrap5.min.js"></script>

<script src="<?php echo APP_URL; ?>app/views/assets/js/jszip.min.js"></script>
<script src="<?php echo APP_URL; ?>app/views/assets/js/pdfmake.min.js"></script>
<script src="<?php echo APP_URL; ?>app/views/assets/js/vfs_fonts.js"></script>
<script src="<?php echo APP_URL; ?>app/views/assets/js/dataTables.buttons.min.js"></script>
<script src="<?php echo APP_URL; ?>app/views/assets/js/buttons.bootstrap5.min.js"></script>
<script src="<?php echo APP_URL; ?>app/views/assets/js/buttons.html5.min.js"></script>
<script src="<?php echo APP_URL; ?>app/views/assets/js/buttons.print.min.js"></script>
<script src="<?php echo APP_URL; ?>app/views/assets/js/buttons.colVis.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="<?php echo APP_URL; ?>app/views/assets/js/script.js?v=<?php echo time(); ?>"></script>

<script>
if ('serviceWorker' in navigator) {
  window.addEventListener('load', function() {
    // Ajusta la ruta si tu proyecto no está en la raíz del dominio
    navigator.serviceWorker.register('<?php echo APP_URL; ?>sw.js')
      .then(function(registration) {
        console.log('ServiceWorker registrado con éxito: ', registration.scope);
      }, function(err) {
        console.log('Fallo al registrar ServiceWorker: ', err);
      });
  });
}
</script>