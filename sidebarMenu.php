<?php 
$menu = ''; //variable untuk menampung menu
if (isset($_GET['menu'])) {
    $menu = $_GET['menu'];
}
?>
<div class="navbar-content">
    <!-- start: SIDEBAR -->
    <div class="main-navigation navbar-collapse collapse">
        <!-- start: MAIN MENU TOGGLER BUTTON -->
        <div class="navigation-toggler">
            <i class="clip-chevron-left"></i>
            <i class="clip-chevron-right"></i>
        </div>
        <!-- end: MAIN MENU TOGGLER BUTTON -->
        <!-- start: MAIN NAVIGATION MENU -->
        <ul class="main-navigation-menu">
            <li <?php if(empty($menu)) echo "class='active open'"; ?> >
                <a href="index.php">
                    <span class="title"> Home </span><span class="selected"></span>
                </a>
            </li>
            <li <?php if($menu=='olah_data') echo "class='active open'"; ?> >
                <a href="index.php?menu=olah_data">
                    <span class="title"> Olah Data </span><span class="selected"></span>
                </a>
            </li>
            <li <?php if($menu=='data_mining') echo "class='active open'"; ?> >
                <a href="index.php?menu=data_mining">
                    <span class="title"> Data Mining </span><span class="selected"></span>
                </a>
            </li>
            <li <?php if($menu=='pohon_keputusan') echo "class='active open'"; ?> >
                <a href="index.php?menu=pohon_keputusan">
                    <span class="title"> Pohon Keputusan </span><span class="selected"></span>
                </a>
            </li>
            <li <?php if($menu=='prediksi') echo "class='active open'"; ?> >
                <a href="index.php?menu=prediksi">
                    <span class="title"> Prediksi </span><span class="selected"></span>
                </a>
            </li>
            <li <?php if($menu=='hasil') echo "class='active open'"; ?> >
                <a href="index.php?menu=hasil">
                    <span class="title"> Hasil </span><span class="selected"></span>
                </a>
            </li>
            
        </ul>
        <!-- end: MAIN NAVIGATION MENU -->
    </div>
    <!-- end: SIDEBAR -->
</div>