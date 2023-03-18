
<aside class="main-sidebar sidebar-dark-primary elevation-4">
   <!-- Brand Logo -->
   <a href="/index.php" class="brand-link">
      <img src="/images/logo.jpg" alt="Logo" class="brand-image img-circle" style="opacity: .8">
      <span class="brand-text font-weight-light">Miners Tools</span>
   </a>

   <!-- Sidebar -->
   <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2" style='font-size:0.9em;'>
         <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
            <li class="nav-item">
               <a href="/index.php" class="nav-link<?php if(preg_match("~^(|/index.php)$~i",$_SERVER['PHP_SELF'])){ echo " active"; } ?>">
                  <i class="nav-icon fad fa-home-alt"></i>
                  <p> Home </p>
               </a>
            </li>

            <li class="nav-header"><b>ITEMS</b></li>

            <li class="nav-item">
               <a href="/item/analytics.php" class="nav-link<?php if (preg_match('~^/item/analytics.php~i',$_SERVER['PHP_SELF'])) { echo " active"; } ?>">
                  <i class="nav-icon fal fa-analytics"></i>
                  <p> Item Analytics </p>
               </a>
            </li>
<!--
            <li class="nav-header"><b>MONSTERS</b></li>

            <li class="nav-item">
               <a href="//" class="nav-link<?php if(preg_match("~^//~i",$_SERVER['PHP_SELF'])){ echo " active"; } ?>">
                  <i class="nav-icon fal fa-skull"></i>
                  <p> Dungeon </p>
               </a>
            </li>

            <li class="nav-item">
               <a href="//" class="nav-link<?php if(preg_match("~^//~i",$_SERVER['PHP_SELF'])){ echo " active"; } ?>">
                  <i class="nav-icon fal fa-skull"></i>
                  <p> Caves </p>
               </a>
            </li>

            <li class="nav-item">
               <a href="//" class="nav-link<?php if(preg_match("~^//~i",$_SERVER['PHP_SELF'])){ echo " active"; } ?>">
                  <i class="nav-icon fal fa-skull"></i>
                  <p> Mitar </p>
               </a>
            </li>

            <li class="nav-item">
               <a href="//" class="nav-link<?php if(preg_match("~^//~i",$_SERVER['PHP_SELF'])){ echo " active"; } ?>">
                  <i class="nav-icon fal fa-skull"></i>
                  <p> Einlor </p>
               </a>
            </li>

            <li class="nav-item">
               <a href="//" class="nav-link<?php if(preg_match("~^//~i",$_SERVER['PHP_SELF'])){ echo " active"; } ?>">
                  <i class="nav-icon fal fa-skull"></i>
                  <p> Scrolls </p>
               </a>
            </li>

            <li class="nav-item">
               <a href="//" class="nav-link<?php if(preg_match("~^//~i",$_SERVER['PHP_SELF'])){ echo " active"; } ?>">
                  <i class="nav-icon fal fa-skull"></i>
                  <p> Dragons </p>
               </a>
            </li>
-->

         </ul>
      </nav>
   </div>
</aside>
