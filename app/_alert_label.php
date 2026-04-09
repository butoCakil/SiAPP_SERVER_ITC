<?php
if (@$_SESSION['pesan_login'] || @$_SESSION['pesan'] || @$_SESSION['pesan_error']) {

    if (@$_SESSION['pesan_login']) {
        $pesan = (($_SESSION['pesan_login']));
        $alert = "primary";

        if (@$_SESSION['pesan']) {
            $pesan_tambah = $_SESSION['pesan'];
            unset($_SESSION['pesan']);
            if (@$_SESSION['alert1']) {
                $alert1 = $_SESSION['alert1'];
                unset($_SESSION['alert1']);
            } else {
                $alert1 = "success";
            }
?>
            <div role="alert">
                <div id="alert_user" class="mb-2 shadow_1 alert alert-dismissible alert-<?= $alert1; ?>">
                    <button type="button" class="close mr-3" data-dismiss="alert">&times;</button>
                    <?= $pesan_tambah; ?>
                </div>
            </div>
    <?php
        }
    } elseif (@$_SESSION['pesan_error']) {
        $pesan = $_SESSION['pesan_error'];
        $alert = "danger";
        unset($_SESSION['pesan_error']);
    } else {
        $pesan = (isset($_SESSION['pesan']) ? ($_SESSION['pesan']) : "");
        // @$_SESSION['alert'] ? $alert = $_SESSION['alert'] : $alert = "success";
        if (@$_SESSION['alert']) {
            $alert = $_SESSION['alert'];
            unset($_SESSION['alert']);
        } else {
            $alert = "success";
        }
        // $alert = "success";
        unset($_SESSION['pesan']);
    }
    ?>
    <div role="alert">
        <div id="alert_user" class="shadow_1 alert alert-dismissible alert-<?= $alert; ?>">
            <button type="button" class="close mr-3" data-dismiss="alert">&times;</button>
            <?= $pesan; ?>
        </div>
    </div>
<?php } ?>