<?php
$subDir = 2;
include "../../../beranda/app/get_user.php";
if (@$_SESSION['level_login'] == 'superadmin') {
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="../../../img/logoInstitusi.png" type="image/x-icon">
        <title>Real-time Tag Log Viewer</title>

        <title>Datepicker Example</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/dark.css">

        <style>
            body {
                font-family: monospace;
                margin: 0;
                padding: 0;
                overflow: hidden;
                /* Menyembunyikan scrollbar utama */
            }

            #header {
                display: flex;
                background-color: #333;
                color: #fff;
                box-shadow: 2px 5px 5px #000;
            }

            #header img {
                width: 50px;
            }

            h1 {
                padding: 10px;
                margin: 0;
            }

            #viewbox {
                max-height: 100vh;
                padding: 0px 10px 10px 10px;
                background-color: #888;
                background-image: linear-gradient(to top, #888, #eee);
                border-radius: 0px 0px 10px 10px;
                box-shadow: 2px 2px 5px #000;
            }

            #log-container {
                max-height: 90vh;
                /* Maksimalkan tinggi container sesuai tinggi layar */
                overflow-y: auto;
                /* Mengurangi padding */
                background-color: #000;
                color: #fff;
                white-space: pre-wrap;
                margin: 0;
                padding-left: 10px;
                line-height: 1;
                border-radius: 0px 0px 10px 10px;
                /* Menyesuaikan nilai line-height */
            }

            #log-container .log-line {
                border-bottom: 1px solid #111;
                /* Menambahkan garis pada setiap baris */
                padding: 5px 0;
                /* Menyesuaikan padding pada setiap baris */
            }

            #log-container .highlight {
                font-weight: bold;
                color: deepskyblue;
            }

            /* Menyesuaikan scrollbar untuk browser WebKit (Chrome, Safari) */
            #log-container::-webkit-scrollbar {
                width: 10px;
            }

            #log-container::-webkit-scrollbar-thumb {
                background-color: midnightblue;
                border-radius: 5px;
            }

            #log-container::-webkit-scrollbar-track {
                background-color: #f4f4f4;
            }

            #inputDate {
                margin: 2px;
                padding: 2px;
            }

            #inputDate .form-control {
                margin: 5px;
                padding: 5px;
                font-size: 18px;
                border-radius: 10px;
                box-shadow: #000 2px 1px;
                text-align: center;
            }

            #inputDate .flatpickr-mobile {
                margin: 5px;
                padding: 5px;
                font-size: 12px;
                border-radius: 10px;
                box-shadow: #000 2px 1px;
                text-align: center;
            }

            .btn-back-link {
                position: absolute;
                right: 0;
                margin-top: 20px;
                margin-right: 10px;
            }

            .btn-back-link-name {
                color: #555;
                text-decoration: none;
                background-color: #eee;
                border-radius: 5px;
                padding: 5px;
                text-align: center;
            }

            @media only screen and (max-width: 768px) {
                #header {
                    display: flex;
                    flex-direction: column;
                    text-align: center;
                }

                #header img {
                    display: block;
                    margin-left: auto;
                    margin-right: auto;
                }

                h1 {
                    font-size: 12px;
                }

                #inputDate .flatpickr-mobile {
                    margin: 5px;
                    padding: 5px;
                    font-size: 12px;
                    border-radius: 10px;
                    box-shadow: #000 2px 1px;
                    text-align: center;
                }
            }
        </style>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    </head>

    <body onload="getDataValue();">

        <div id="header">
            <img src="../../../img/app/rfid-unscreen.gif" alt="">
            <h1>
                Real-time Tag-RFID Log Viewer
            </h1>
            <div id="inputDate">
                <input type="date" class="" name="getdate" id="date-value" onchange="getDataValue();">
            </div>
            <div class="btn-back-link">
                <a href="#" onclick="window.history.back();" class="btn-back-link-name">Kembali</a>
                <a href="../../../" class="btn-back-link-name">Home</a>
            </div>
        </div>

        <div id="viewbox">
            <div id="log-container"></div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        <script>
            var selectedDate = "";

            function getDataValue() {
                var dateInput = document.getElementById("date-value");

                selectedDate = dateInput.value;

                if (!selectedDate) {
                    var today = new Date();
                    var year = today.getFullYear();
                    var month = String(today.getMonth() + 1).padStart(2, '0');
                    var day = String(today.getDate()).padStart(2, '0');

                    selectedDate = year + '-' + month + '-' + day;
                    dateInput.value = selectedDate;

                    flatpickr("#date-value", {
                        altInput: true,
                        altFormat: "j F Y",
                        dateFormat: "Y-m-d",
                    });
                }
            }

            $(document).ready(function() {
                var currentLogLength = 0;

                function formatLogLine(line) {
                    // Menyoroti teks yang diapit oleh tanda kurung siku []
                    return line.replace(/\[([^\]]+)\]/g, '<span class="highlight">[$1]</span>');
                }

                function fetchLog() {
                    $.ajax({
                        url: 'get_log.php?date=' + selectedDate,
                        success: function(data) {
                            var newLogLength = data.length;

                            if (!newLogLength) {
                                // alert("tidak ada data untuk tanggal " + selectedDate);
                                selectedDate = "";
                                location.reload();
                            }

                            if (newLogLength !== currentLogLength) {
                                // Wrap each formatted line in a div with the class 'log-line'
                                var formattedData = data.split('\n').map(function(line) {
                                    return '<div class="log-line">' + formatLogLine(line) + '</div>';
                                }).join('');

                                $('#log-container').html(formattedData);
                                $('#log-container').scrollTop($('#log-container')[0].scrollHeight);
                                currentLogLength = newLogLength;
                            }
                        },
                        complete: function() {
                            setTimeout(fetchLog, 1000);
                        }
                    });
                }

                fetchLog();
            });
        </script>
    </body>

    </html>

<?php } else { ?>
    <script>
        window.location.href = "../../../";
    </script>
<?php } ?>