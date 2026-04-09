<?php 
ini_set('display_errors', 1);//Atau
error_reporting(E_ALL && ~E_NOTICE);
include('app/get_user.php');

if ($level_login == 'admin' || $level_login == 'superadmin') {
    if (@$_GET['page'] == '') {
        header("location: statistik.php?page=chart");
    }

    $title = 'Statistik';
    $navlink = 'statistik';
    include('views/header.php');
    include 'views/_statistik.php';
    include('views/footer.php');
?>

    <!-- Custom chart -->

    <script>
        $(document).ready(function() {
            showGraph();
        });


        function showGraph() {
            {
                $.post("app/data.php",
                    function(data) {
                        console.log(data);
                        var name = [];
                        var marks = [];

                        for (var i in data) {
                            name.push(data[i].student_name);
                            marks.push(data[i].marks);
                        }

                        var chartdata = {
                            labels: name,
                            datasets: [{
                                label: 'Student Marks',
                                backgroundColor: '#49e2ff',
                                borderColor: '#46d5f1',
                                hoverBackgroundColor: '#CCCCCC',
                                hoverBorderColor: '#666666',
                                data: marks
                            }]
                        };

                        var graphTarget = $("#graphCanvas");

                        var barGraph = new Chart(graphTarget, {
                            type: 'bar',
                            data: chartdata
                        });
                    });
            }
        }
    </script>
<?php } else {

    echo '<script>window.location.href="../";</script>';
} ?>