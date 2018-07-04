<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING | E_DEPRECATED));
include_once "database.php";
include_once "import/excel_reader2.php";
include_once "fungsi.php";
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
                        <h1>Uji Rule </h1>
                    </div>
                    <!-- end: PAGE TITLE & BREADCRUMB -->
                </div>
            </div>
            <!-- end: PAGE HEADER -->
            <!-- start: PAGE CONTENT -->
            <?php
            if (isset($_POST['upload'])) {
                $data = new Spreadsheet_Excel_Reader($_FILES['file_data']['tmp_name']);

                $baris = $data->rowcount($sheet_index = 0);
                $column = $data->colcount($sheet_index = 0);
                //import data excel dari baris kedua, karena baris pertama adalah nama kolom
                // $temp_date = $temp_produk = "";
                for ($i = 2; $i <= $baris; $i++) {
                    if (!empty($data->val($i, 2))) {
                        $penghasilan = str_replace(".", "", $data->val($i, 5));
                        $value = "(\"" . $data->val($i, 2) . "\", '" . strtolower(trim($data->val($i, 3))) . "', '"
                                . strtolower(trim($data->val($i, 4))) . "' , '" . $penghasilan . "', "
                                . $data->val($i, 6) . ", '" . strtolower(trim($data->val($i, 7))) . "')";
                        $sql = "INSERT INTO data_uji "
                                . " (nama, status_pernikahan, status_rumah, penghasilan, umur, kelas_asli)"
                                . " VALUES " . $value;
                        $result = $db_object->db_query($sql);
                    }
                }
                if ($result) {
                    ?>
                    <script> location.replace("?menu=uji_rule&pesan_success=Data berhasil disimpan");</script>
                    <?php
                } else {
                    ?>
                    <script> location.replace("?menu=uji_rule&pesan_error=Data gagal disimpan");</script>
                    <?php
                }
            }

            if (isset($_GET['act'])) {
                $action = $_GET['act'];
                //delete semua data
                if ($action == 'delete_all') {
                    $db_object->db_query("TRUNCATE data_uji");
                    //header('location:?menu=uji_rule');
                    ?>
                    <script> location.replace("?menu=uji_rule&pesan_success=Data uji berhasil dihapus");</script>
                    <?php
                }
            } else {
                if (isset($_POST['submit'])) {
                    include "hitung_akurasi.php";
                } else {
                    $query = $db_object->db_query("SELECT * FROM data_uji order by(id)");
                    $jumlah = $db_object->db_num_rows($query);
                    echo "<br><br>";
                    ?>

                    <form method="post" enctype="multipart/form-data" action="">
                        <div class="form-group">
                            <div class="input-group">
                                <label>Import data from excel</label>
                                <input name="file_data" type="file" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <input name="upload" type="submit" value="Upload Data" class="btn btn-success">
                            <a href="?menu=uji_rule&act=delete_all" class="btn btn-danger"
                               onClick="return confirm('Anda yakin akan hapus semua data?')">
                                <i class="fa fa-trash"></i> Delete All Data uji
                            </a>
                            <a href="?menu=uji_rule" class="btn btn-default">
                                <i class="fa fa-refresh"></i> Refresh
                            </a>
                        </div>
                    </form>
                    <?php
                    if ($jumlah == 0) {
                        echo "<center><h3>Data uji masih kosong...</h3></center>";
                    } else {
                        ?>
                        <center>
                            <form method="POST" action=''>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="submit" name="submit" value="HitungAkurasi" class="btn btn-success">
                                    </div>
                                </div>
                            </form>
                        </center>
                        Jumlah data uji: <?php echo $jumlah; ?>

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
            }
            ?>
        </div>
    </div>
</div>