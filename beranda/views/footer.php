   <!-- tombol kembali ke halaman atas -->
   <style>
       .tombolkeatas {
           width: 60px;
           height: 60px;
           line-height: 60px;
           font-size: 30px;
           right: 50px;
           bottom: 60px;
           left: 95%;
           transition: all 0.5s;
           position: fixed;
           /* visibility: hidden; */
           /* opacity: 0; */
           z-index: 996;
       }

       /* .tombolkeatas:hover {
           cursor: pointer;
       } */

       /* .tombolkeatas.active {
           visibility: visible;
           opacity: 1;
       } */

       @media screen and (max-width: 992px) {
           .tombolkeatas {
               left: 92%;
           }
       }

       @media only screen and (max-width: 600px) {
           .tombolkeatas {
               left: 89%;
           }
       }

       @media only screen and (max-width: 500px) {
           .tombolkeatas {
               left: 85%;
               bottom: 80px;
           }
       }

       /* main-footer-fixbottom */
       .main-footer-fixbottom {
           background-color: #fff;
           border-top: 1px solid #dee2e6;
           color: #869099;
           padding: 1rem;
           margin-left: 250px !important;
       }

       @media only screen and (max-width: 991px) {
           .main-footer-fixbottom {
               margin-left: 0 !important;
           }
       }
   </style>
   <a href="#" class="tombolkeatas scrollup text-center d-block fixed-bottom" id="tombolkeatas">
       <button class="btn btn-primary bg-gradient-primary elevation-3" style="border: none; padding: 5px 12px 5px 12px;">
           <i class="fas fa-angle-up"></i>
       </button>

   </a>

   <script type="text/javascript">
       let backtotop = select('.tombolkeatas')
       if (backtotop) {
           const toggleBacktotop = () => {
               if (window.scrollY > 100) {
                   backtotop.classList.add('active')
               } else {
                   backtotop.classList.remove('active')
               }
           }
           window.addEventListener('load', toggleBacktotop)
           onscroll(document, toggleBacktotop)
       }
   </script>
   <!-- /.tombol kembali ke halaman atas -->

   </div>
   <!-- /.content-wrapper -->
   <footer class="main-footer-fixbottom">
       <strong>&copy;<?= date('Y'); ?>&nbsp;-&nbsp;<?= date('Y') + 1; ?>&nbsp;<a href="https://smknbansari.sch.id" style="text-decoration: none;">
               <!-- icon bumi -->
               <!-- <i class="fas fa-solid fa-globe text-danger"></i> -->
               <img src="<?= @$dir; ?>dist/img/logoInstitusi.png" style="height: 20px; margin: auto auto 3px auto;">&nbsp;
               SMK NEGERI BANSARI
           </a>
       </strong>
       <div class="float-right d-none d-sm-inline-block">
           <b>Version</b> 1.2.0
       </div>
   </footer>

   <!-- Control Sidebar -->
   <aside class="control-sidebar control-sidebar-dark">
       <!-- Control sidebar content goes here -->
   </aside>
   <!-- /.control-sidebar -->
   </div>
   <!-- ./wrapper -->
   <!-- jQuery -->
   <script src="<?= @$dir; ?>plugins/jquery/jquery.min.js"></script>
   <!-- jQuery UI 1.11.4 -->
   <script src="<?= @$dir; ?>plugins/jquery-ui/jquery-ui.min.js"></script>
   <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
   <script>
       $.widget.bridge('uibutton', $.ui.button)
   </script>
   <!-- Bootstrap 4 -->
   <script src="<?= @$dir; ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

   <!-- Option 1: Bootstrap Bundle with Popper -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

   <!-- Option 2: Separate Popper and Bootstrap JS -->
   <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>

   <!-- ChartJS -->
   <script src="<?= @$dir; ?>plugins/chart.js/Chart.min.js"></script>
   <!-- Sparkline -->
   <script src="<?= @$dir; ?>plugins/sparklines/sparkline.js"></script>
   <!-- JQVMap -->
   <script src="<?= @$dir; ?>plugins/jqvmap/jquery.vmap.min.js"></script>
   <script src="<?= @$dir; ?>plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
   <!-- jQuery Knob Chart -->
   <script src="<?= @$dir; ?>plugins/jquery-knob/jquery.knob.min.js"></script>
   <!-- daterangepicker -->
   <script src="<?= @$dir; ?>plugins/moment/moment.min.js"></script>
   <script src="<?= @$dir; ?>plugins/daterangepicker/daterangepicker.js"></script>
   <!-- Tempusdominus Bootstrap 4 -->
   <script src="<?= @$dir; ?>plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
   <!-- Summernote -->
   <script src="<?= @$dir; ?>plugins/summernote/summernote-bs4.min.js"></script>
   <!-- overlayScrollbars -->
   <script src="<?= @$dir; ?>plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
   <!-- AdminLTE App -->
   <script src="<?= @$dir; ?>dist/js/adminlte.js"></script>
   <!-- AdminLTE for demo purposes -->
   <!-- <script src="<?= @$dir; ?>dist/js/demo.js"></script> -->
   <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
   <!-- fullCalendar 2.2.5 -->
   <script src="<?= @$dir; ?>plugins/moment/moment.min.js"></script>
   <script src="<?= @$dir; ?>plugins/fullcalendar/main.js"></script>
   <script src="<?= @$dir; ?>dist/js/pages/dashboard1.js"></script>
   <script src="<?= @$dir; ?>dist/js/pages/dashboard3.js"></script>
   <!-- DataTables  & Plugins -->
   <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
   <script src="<?= @$dir; ?>plugins/datatables/jquery.dataTables.min.js"></script>
   <script src="<?= @$dir; ?>plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
   <script src="<?= @$dir; ?>plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
   <script src="<?= @$dir; ?>plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
   <script src="<?= @$dir; ?>plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
   <script src="<?= @$dir; ?>plugins/datatables-buttons/js/buttons.bootstrap4.min_.js"></script>
   <!-- <script src="<?= @$dir; ?>plugins/datatables-buttons/js/buttons.bootstrap4.js"></script> -->
   <script src="<?= @$dir; ?>plugins/jszip/jszip.min.js"></script>
   <script src="<?= @$dir; ?>plugins/pdfmake/pdfmake.min.js"></script>
   <script src="<?= @$dir; ?>plugins/pdfmake/vfs_fonts.js"></script>
   <script src="<?= @$dir; ?>plugins/datatables-buttons/js/buttons.html5.min.js"></script>
   <script src="<?= @$dir; ?>plugins/datatables-buttons/js/buttons.print.min.js"></script>
   <!-- <script src="<?= @$dir; ?>plugins/datatables-buttons/js/buttons.colVis.min_.js"></script> -->
   <script src="<?= @$dir; ?>plugins/datatables-buttons/js/buttons.colVis_.js"></script>
   <!--  -->
   <script src="<?= @$dir; ?>plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
   <!-- icon https://icon-sets.iconify.design/ion/ -->
   <script src="https://code.iconify.design/2/2.2.1/iconify.min.js"></script>

   </body>

   </html>