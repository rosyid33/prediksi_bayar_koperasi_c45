<?php
if (($_SESSION['koperasi_c45_id'])==2) {
    header("location:index.php?menu=forbidden");
}

include_once "database.php";
include_once "fungsi.php";
include_once "import/excel_reader2.php";
include_once 'proses_mining.php';

//object database class
$db_object = new database();
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
                        <h1>Data Mining </h1>
                    </div>
                    <!-- end: PAGE TITLE & BREADCRUMB -->
                </div>
            </div>
            <!-- end: PAGE HEADER -->
            <!-- start: PAGE CONTENT -->
            <?php
            $pesan_error = $pesan_success = "";
            if (isset($_GET['pesan_error'])) {
                $pesan_error = $_GET['pesan_error'];
            }
            if (isset($_GET['pesan_success'])) {
                $pesan_success = $_GET['pesan_success'];
            }

            if (!isset($_POST['proses_mining'])) {//tidak muncul jika diklik proses mining
                $sql = "SELECT * FROM data_latih";
                $query = $db_object->db_query($sql);
                $jumlah = $db_object->db_num_rows($query);
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <!--UPLOAD EXCEL FORM-->
                        <form method="post" enctype="multipart/form-data" action="">
                            <div class="form-group">
                                <!--<input name="submit" type="submit" value="Upload Data" class="btn btn-success">-->
                                <button name="proses_mining" type="submit"  class="btn btn-default" onclick="">
                                    <i class="fa fa-check"></i> Proses Mining
                                </button>
                            </div>
                        </form>

                        <?php
                    }
                    if (!empty($pesan_error)) {
                        display_error($pesan_error);
                    }
                    if (!empty($pesan_success)) {
                        display_success($pesan_success);
                    }

                    if (!isset($_POST['proses_mining'])) {//tidak muncul jika diklik proses mining
                        echo "Jumlah data: " . $jumlah . "<br>";
                        if ($jumlah == 0) {
                            echo "Data kosong...";
                        } else {
                            ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="sample-table-1">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Status Pernikahan</th>
                                            <th>Status Rumah</th>
                                            <th>Penghasilan</th>
                                            <th>Umur</th>
                                            <th>Kelas Asli</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        while ($row = $db_object->db_fetch_array($query)) {
                                            echo "<tr>";
                                            echo "<td>" . $no . "</td>";
                                            echo "<td>" . $row['nama'] . "</td>";
                                            echo "<td>" . $row['status_pernikahan'] . "</td>";
                                            echo "<td>" . $row['status_rumah'] . "</td>";
                                            echo "<td>" . $row['penghasilan'] . "</td>";
                                            echo "<td>" . $row['umur'] . "</td>";
                                            echo "<td>" . $row['kelas_asli'] . "</td>";
                                            echo "</tr>";
                                            $no++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                        }
                    }

                    if (isset($_POST['proses_mining'])) {
                        $awal = microtime(true);

                        $db_object->db_query("TRUNCATE t_keputusan");
                        pembentukan_tree($db_object, "", "");
                        echo "<br><h3><center>---PROSES SELESAI---</center></h3>";
                        //echo "<center><a href='index.php?menu=pohon_keputusan' accesskey='5' "
                        //. "title='pohon keputusan'>Lihat pohon keputusan yang terbentuk</a></center>";

                        $akhir = microtime(true);
                        $lama = $akhir - $awal;
                        //echo "<br>Lama eksekusi script adalah: ".$lama." detik";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <!-- //typography-page -->

</div>

