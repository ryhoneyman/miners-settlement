
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

            <li class="nav-header"><b>MONSTER</b></li>

            <li class="nav-item">
               <a href="/monster/viewer/" class="nav-link<?php if (preg_match('~^/monster/viewer/~i',$_SERVER['REQUEST_URI'])) { echo " active"; } ?>">
                  <i class="nav-icon fa fa-skull"></i>
                  <p> Monster Viewer </p>
               </a>
            </li>

            <li class="nav-header"><b>RUNES</b></li>

            <li class="nav-item">
               <a href="/runepost" class="nav-link<?php if (preg_match('~^/runepost/~i',$_SERVER['REQUEST_URI'])) { echo " active"; } ?>">
                  <i class="nav-icon fa fa-sign"></i>
                  <p> Runeposts </p>
               </a>
            </li>

            <li class="nav-header"><b>ITEMS</b></li>

            <li class="nav-item">
               <a href="/item/crafting/scheme/" class="nav-link<?php if (preg_match('~^/item/crafting/sceheme/~i',$_SERVER['REQUEST_URI'])) { echo " active"; } ?>">
                  <i class="nav-icon fa fa-puzzle-piece"></i>
                  <p> Scheme Crafting </p>
               </a>
            </li>

            <li class="nav-item">
               <a href="/item/gear/list" class="nav-link<?php if (preg_match('~^/item/gear/list/~i',$_SERVER['REQUEST_URI'])) { echo " active"; } ?>">
                  <i class="nav-icon fa fa-sword"></i>
                  <p> Gear List </p>
               </a>
            </li>

            <li class="nav-item">
               <a href="/item/analytics" class="nav-link<?php if (preg_match('~^/item/analytics/~i',$_SERVER['REQUEST_URI'])) { echo " active"; } ?>">
                  <i class="nav-icon fa fa-analytics"></i>
                  <p> Item Analytics </p>
               </a>
            </li>

            <li class="nav-item">
               <a href="/item/enhancement" class="nav-link<?php if (preg_match('~^/item/enhancement/~i',$_SERVER['REQUEST_URI'])) { echo " active"; } ?>">
                  <i class="nav-icon fa fa-sparkles"></i>
                  <p> Item Enhancement </p>
               </a>
            </li>

            <li class="nav-header"><b>SIMULATION</b></li>

            <li class="nav-item">
               <a href="/simulation/" class="nav-link<?php if (preg_match('~^/simulation/~i',$_SERVER['REQUEST_URI'])) { echo " active"; } ?>">
                  <i class="nav-icon fa fa-user-chart"></i>
                  <p> Simulation </p>
               </a>
            </li>

            <li class="nav-header"><b>TOOLS</b></li>

            <li class="nav-item">
               <a href="/tools/colorpicker" class="nav-link<?php if (preg_match('~^/tools/colorpicker/~i',$_SERVER['REQUEST_URI'])) { echo " active"; } ?>">
                  <i class="nav-icon fa fa-palette"></i>
                  <p> Color Picker </p>
               </a>
            </li>

            <li class="nav-header mt-3"><b></b></li>

            <li class="nav-item">
               <a href="/notes/history/" class="nav-link<?php if(preg_match("~^/notes/history/~i",$_SERVER['PHP_SELF'])){ echo " active"; } ?>">
                  <i class="nav-icon fa fa-comment-lines"></i>
                  <p> Patch Notes </p>
               </a>
            </li>

         </ul>
      </nav>
   </div>
</aside>
