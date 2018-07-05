<?php
//session_start();
if (!isset($_SESSION['koperasic45_id'])) {
    header("location:index.php?menu=forbidden");
}

include_once "database.php";
include_once "fungsi.php";
include_once "proses_mining.php";
//include_once "fungsi_proses.php";
?>
<div class="content"><!-- start: PAGE -->
    <div class="main-content">
        <div class="container">
            <!-- start: PAGE HEADER -->
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    //include "styleSelectorBox.php";
                    ?>
                    <!-- start: PAGE TITLE & BREADCRUMB -->

                    <div class="page-header">
                        <h1>Prediksi </h1>
                    </div>
                    <!-- end: PAGE TITLE & BREADCRUMB -->
                </div>
            </div>
            <?php
            //object database class
            $db_object = new database();

            $pesan_error = $pesan_success = "";
            if (isset($_GET['pesan_error'])) {
                $pesan_error = $_GET['pesan_error'];
            }
            if (isset($_GET['pesan_success'])) {
                $pesan_success = $_GET['pesan_success'];
            }

            if (isset($_POST['submit'])) {
                $success = true;
                $lihat_hasil = false;
                $pesan_gagal = $pesan_sukses = "";
                if ($success) {
                    $n_nama = $_POST['nama'];
                    $n_status_pernikahan = $_POST['status_pernikahan'];
                    $n_status_rumah = $_POST['status_rumah'];
                    $n_penghasilan = $_POST['penghasilan'];
                    $n_umur = $_POST['umur'];
                    
                    $hasil = klasifikasi($db_object, $n_status_pernikahan, $n_status_rumah, $n_penghasilan, $n_umur);

                    //simpan ke table hasil
                    $sql_in_hasil = "INSERT INTO data_hasil_klasifikasi
                                (nama, status_pernikahan, status_rumah, penghasilan, umur,
                                kelas_hasil, id_rule)
                                VALUES
                                ('$n_nama', '" . $n . "', " . $siswa['usia'] . ", '" . $siswa['sekolah'] . "', "
                            . $jawaban_a . ", " . $jawaban_b . ", " . $jawaban_c . ", " . $jawaban_d . ", "
                            . "'" . $hasil['keputusan'] . "', '" . $hasil['id_rule'] . "')";
                    $db_object->db_query($sql_in_hasil);

                        //simpan ke data uji
//                        $sql_data_uji = "INSERT INTO data_uji "
//                                . "(nama, jenis_kelamin, usia, sekolah, jawaban_a, jawaban_b, jawaban_c, jawaban_d, kelas_asli) "
//                                . " VALUES "
//                                . "('" . $siswa['nama_siswa'] . "', '" . $siswa['jenis_kelamin'] . "', '" . $siswa['usia'] . "'"
//                                . ", '" . $siswa['sekolah'] . "', '" . $jawaban_a . "', '" . $jawaban_b . "'"
//                                . ", '" . $jawaban_c . "', '" . $jawaban_d . "', '" . $hasil['keputusan'] . "')";
//                        $db_object->db_query($sql_data_uji);
                    
                }


                if ($success) {
                    echo "<br>";
                    echo "<br>";
                    echo "<br>";
                    echo "<center>"
                    . "<h3 class='typoh2'>"
                    . "Hasil Prediksi: "
                    . "</h3>"
                    . "<h2 class='typoh2'>"
                    . $hasil['keputusan']
                    . "</h2>"
                    . "</center>";
                } else {
                    display_error($pesan_gagal);
                }
            }

            if (!isset($_POST['submit'])) {
                ?>

                <form method="post" action="" class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="form-field-1">
                            Nama
                        </label>
                        <div class="col-sm-9">
                            <input type="text" name="nama" id="form-field-1" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="form-field-1">
                            Status Pernikahan
                        </label>
                        <div class="col-sm-9">
                            <label class="radio-inline">
                                <input type="radio" class="square-black" value="menikah" name="status_pernikahan">
                                Menikah
                            </label>
                            <label class="radio-inline">
                                <input type="radio" class="square-black" value="janda"  name="status_pernikahan">
                                Janda
                            </label>
                            <label class="radio-inline">
                                <input type="radio" class="square-black" value="single"  name="status_pernikahan">
                                Single
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="form-field-1">
                            Status Rumah
                        </label>
                        <div class="col-sm-9">
                            <label class="radio-inline">
                                <input type="radio" class="square-black" value="rumah sendiri" name="status_rumah">
                                Rumah sendiri
                            </label>
                            <label class="radio-inline">
                                <input type="radio" class="square-black" value="kontrak"  name="status_rumah">
                                Kontrak
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="form-field-1">
                            Penghasilan
                        </label>
                        <div class="col-sm-9">
                            <input type="text" name="penghasilan" id="form-field-1" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="form-field-1">
                            Umur
                        </label>
                        <div class="col-sm-9">
                            <input type="text" name="umur" id="form-field-1" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-9 pull-right">
                            <input name="submit" type="submit" value="Submit" class="control-label btn btn-success">
                        </div>
                    </div>
                </form>
                <?php
            }
            ?>
        </div>
    </div>
</div>


