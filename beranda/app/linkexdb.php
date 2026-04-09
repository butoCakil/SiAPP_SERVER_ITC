<?php
// print_r($_POST);
$key = @$_POST['key'];

if ($key == '$1-9(SiApp)') {
    $set_db = @$_POST['set'];

    if ($set_db) {
        $tabel_value_db = @$_POST['select_value'];
        $link_value_db = @$_POST['textarea_value'];
        $id_db = @$_POST['id_db'];
        $apikey = @$_POST['apikey'];

        include "../../config/konesi.php";

        if ($set_db == 'inputlink') {
            if (!$konek) {
                echo "Eh, gak konek ke database ee\n";
            }

            // cek dulu, data itu ada nggakkk
            $stmt = mysqli_stmt_init($konek);
            if (mysqli_stmt_prepare($stmt, "SELECT * FROM exportdb WHERE link = ?")) {
                mysqli_stmt_bind_param(
                    $stmt,
                    "s",
                    $link_value_db
                );
                mysqli_stmt_execute($stmt);

                $hasil_cek_db = mysqli_stmt_get_result($stmt);
                if ($row = mysqli_fetch_assoc($hasil_cek_db)) {
                    // jika data ditemukan / ada
                    echo "Pemeriksaan berhasil.\n";
                    $ijin = $row['status'];
                    $id_get = $row['id'];
                    if ($ijin == 'diijinkan') {
                        echo "Link telah ada.\n";

                        // update
                        $query_update_link_db = "UPDATE `exportdb` SET `db` = ?, `link` = ?, keyapi = ? WHERE link = ?";
                        $stmt_update_link_db = mysqli_stmt_init($konek);
                        if (mysqli_stmt_prepare($stmt_update_link_db, $query_update_link_db)) {
                            mysqli_stmt_bind_param(
                                $stmt_update_link_db,
                                "ssss",
                                $tabel_value_db,
                                $link_value_db,
                                $apikey,
                                $link_value_db
                            );
                            $update = mysqli_stmt_execute($stmt_update_link_db);

                            if ($update) {
                                echo "Berhasil update $tabel_value_db\n";
                            } else {
                                echo "Gagal update $tabel_value_db\n";
                            }
                        } else {
                            echo "Gagal mempersiapkan Query\n";
                        }
                        // Tutup prepared statement
                        mysqli_stmt_close($stmt_update_link_db);
                    } else if ($ijin == 'diblokir') {
                        echo "Link telah diblokir!\nHubungi Administrator\n";
                    } else {
                        echo "Link belum dikonvirmasi.\nHubungi Administrator\n";
                    }
                } else {
                    // cek dulu id nya
                    $stmt_cek_id = mysqli_stmt_init($konek);
                    if (mysqli_stmt_prepare($stmt_cek_id, "SELECT * FROM exportdb WHERE id = ?")) {
                        mysqli_stmt_bind_param(
                            $stmt_cek_id,
                            "s",
                            $id_db
                        );
                        mysqli_stmt_execute($stmt_cek_id);

                        $hasil_cek_db = mysqli_stmt_get_result($stmt_cek_id);
                        if ($row = mysqli_fetch_assoc($hasil_cek_db)) {
                            // update
                            $query_update_link_db = "UPDATE `exportdb` SET `db` = ?, `link` = ?, keyapi = ? WHERE id = ?";
                            $stmt_update_link_db = mysqli_stmt_init($konek);
                            if (mysqli_stmt_prepare($stmt_update_link_db, $query_update_link_db)) {
                                mysqli_stmt_bind_param(
                                    $stmt_update_link_db,
                                    "ssss",
                                    $tabel_value_db,
                                    $link_value_db,
                                    $apikey,
                                    $id_db
                                );
                                $update = mysqli_stmt_execute($stmt_update_link_db);

                                if ($update) {
                                    echo "Berhasil update $tabel_value_db\n";
                                } else {
                                    echo "Gagal update $tabel_value_db\n";
                                }
                            } else {
                                echo "Gagal mempersiapkan Query\n";
                            }
                            // Tutup prepared statement
                            mysqli_stmt_close($stmt_update_link_db);
                        } else {
                            echo "data belum ada\n";
                            // insert
                            $status_db = "diijinkan";
                            $stmt_insert = mysqli_stmt_init($konek);
                            if (mysqli_stmt_prepare($stmt_insert, "INSERT INTO exportdb (db, link, keyapi, `status`) VALUES (?, ?, ?, ?)")) {
                                mysqli_stmt_bind_param(
                                    $stmt_insert,
                                    "ssss",
                                    $tabel_value_db,
                                    $link_value_db,
                                    $apikey,
                                    $status_db
                                );

                                $insert = mysqli_stmt_execute($stmt_insert);
                                if ($insert) {
                                    echo "Berhasil ditambahkan $tabel_value_db\n";
                                } else {
                                    echo "gAGAL ditambahkan $tabel_value_db\n";
                                }
                                mysqli_stmt_close($stmt_insert);
                            } else {
                                echo "Gagal Mempersiapkan Insert\n";
                            }
                        }
                    }
                    mysqli_stmt_close($stmt_cek_id);
                }
            } else {
                echo "persiapan cek data error";
            }

            mysqli_stmt_close($stmt);
        } elseif ($set_db == 'deletelink') {
            $stmt_delete = mysqli_stmt_init($konek);
            if (mysqli_stmt_prepare($stmt_delete, "DELETE FROM `exportdb` WHERE id = ?")) {
                mysqli_stmt_bind_param(
                    $stmt_delete,
                    "s",
                    $id_db
                );
                $delete = mysqli_stmt_execute($stmt_delete);
                if ($delete) {
                    echo "Berhasil Hapus $tabel_value_db\n";
                } else {
                    echo "gAGAL Hapus $tabel_value_db\n";
                }
                mysqli_stmt_close($stmt_delete);
            } else {
                echo "Gagal Mempersiapkan delete\n";
            }
        } else {
            echo "permintaan tidak dapat diproses";
        }
    }
} else {
    echo "Akses ditolak";
}
