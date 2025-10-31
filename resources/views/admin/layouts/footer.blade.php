</div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <strong>Copyright &copy; {{ date('Y') }} <a href="{{ route('index') }}">{!! $settings->where('type', 'company_name')->first()->value !!}</a>.</strong> All rights reserved.
  </footer>
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
</body>
</html>
