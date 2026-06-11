<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="<?php echo $alamat_admin.'dasbor'; ?>" class="app-brand-link">
      <span class="app-brand-logo demo">
        <img src="assets/img/logo.png" alt="Logo">
      </span>
      <span class="app-brand-text demo menu-text fw-bold ms-2">Panel Admin</span>
    </a>
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M11.4854 4.88844C11.0081 4.41121 10.2344 4.41121 9.75715 4.88844L4.51028 10.1353C4.03297 10.6126 4.03297 11.3865 4.51028 11.8638L9.75715 17.1107C10.2344 17.5879 11.0081 17.5879 11.4854 17.1107C11.9626 16.6334 11.9626 15.8597 11.4854 15.3824L7.96672 11.8638C7.48942 11.3865 7.48942 10.6126 7.96672 10.1353L11.4854 6.61667C11.9626 6.13943 11.9626 5.36568 11.4854 4.88844Z" fill="currentColor" fill-opacity="0.6"></path>
        <path d="M15.8683 4.88844L10.6214 10.1353C10.1441 10.6126 10.1441 11.3865 10.6214 11.8638L15.8683 17.1107C16.3455 17.5879 17.1192 17.5879 17.5965 17.1107C18.0737 16.6334 18.0737 15.8597 17.5965 15.3824L14.0778 11.8638C13.6005 11.3865 13.6005 10.6126 14.0778 10.1353L17.5965 6.61667C18.0737 6.13943 18.0737 5.36568 17.5965 4.88844C17.1192 4.41121 16.3455 4.41121 15.8683 4.88844Z" fill="currentColor" fill-opacity="0.38"></path>
      </svg>
    </a>
  </div>
  <div class="menu-inner-shadow"></div>
  <ul class="menu-inner py-1">
    
    <li class="menu-item" id="dasbor">
      <a href="<?php echo $alamat_admin.'dasbor'; ?>" class="menu-link">
        <i class="menu-icon tf-icons mdi mdi-monitor-dashboard"></i>
        <div>Dasbor</div>
      </a>
    </li>

    <li class="menu-header fw-light mt-4">
      <span class="menu-header-text">Data Utama</span>
    </li>
    <li class="menu-item" id="provider">
      <a href="<?php echo $alamat_admin.'provider'; ?>" class="menu-link">
        <i class="menu-icon tf-icons mdi mdi-gamepad-circle"></i>
        <div>Provider</div>
      </a>
    </li>
    <li class="menu-item" id="permainan">
      <a href="<?php echo $alamat_admin.'permainan'; ?>" class="menu-link">
        <i class="menu-icon tf-icons mdi mdi-slot-machine"></i>
        <div>Permainan</div>
      </a>
    </li> 
    <li class="menu-item" id="anggota">
      <a href="<?php echo $alamat_admin.'anggota'; ?>" class="menu-link">
        <i class="menu-icon tf-icons mdi mdi-account-multiple"></i>
        <div>Anggota</div>
      </a>
    </li>

    <li class="menu-header fw-light mt-4">
      <span class="menu-header-text">Keuangan</span>
    </li>
    <li class="menu-item" id="deposit">
      <a href="<?php echo $alamat_admin.'deposit'; ?>" class="menu-link">
        <i class="menu-icon tf-icons mdi mdi-cash-plus"></i>
        <div>Deposit</div>
      </a>
    </li>
    <li class="menu-item" id="withdraw">
      <a href="<?php echo $alamat_admin.'withdraw'; ?>" class="menu-link">
        <i class="menu-icon tf-icons mdi mdi-cash-minus"></i>
        <div>Withdraw</div>
      </a>
    </li>
    <li class="menu-item" id="rekap">
      <a href="<?php echo $alamat_admin.'rekap'; ?>" class="menu-link">
        <i class="menu-icon tf-icons mdi mdi-text-box-outline"></i>
        <div>Rekap</div>
      </a>
    </li>
    <li class="menu-item" id="saldo">
      <a href="<?php echo $alamat_admin.'saldo'; ?>" class="menu-link">
        <i class="menu-icon tf-icons mdi mdi-cash"></i>
        <div>Saldo</div>
      </a>
    </li>
    <li class="menu-item" id="rekening">
      <a href="<?php echo $alamat_admin.'rekening'; ?>" class="menu-link">
        <i class="menu-icon tf-icons mdi mdi-bank"></i>
        <div>Rekening</div>
      </a>
    </li>

    <li class="menu-header fw-light mt-4">
      <span class="menu-header-text">Pemasaran & Event</span>
    </li>
    <li class="menu-item" id="promosi">
      <a href="<?php echo $alamat_admin.'promosi'; ?>" class="menu-link">
        <i class="menu-icon tf-icons mdi mdi-image"></i>
        <div>Promosi</div>
      </a>
    </li>
    <li class="menu-item" id="bonus">
      <a href="<?php echo $alamat_admin.'bonus'; ?>" class="menu-link">
        <i class="menu-icon tf-icons mdi mdi-gift"></i>
        <div>Bonus</div>
      </a>
    </li>
    <li class="menu-item" id="refferal">
      <a href="<?php echo $alamat_admin.'refferal'; ?>" class="menu-link">
        <i class="menu-icon tf-icons mdi mdi-account-network"></i>
        <div>Refferal</div>
      </a>
    </li>
    <li class="menu-item" id="bukti_jp">
      <a href="<?php echo $alamat_admin.'bukti_jp'; ?>" class="menu-link">
        <i class="menu-icon tf-icons mdi mdi-star-outline"></i>
        <div>Bukti JP</div>
      </a>
    </li>

    <li class="menu-header fw-light mt-4">
      <span class="menu-header-text">Manajemen Web</span>
    </li>
    <li class="menu-item" id="add_domain"> 
      <a href="<?php echo $alamat_admin.'add_domain'; ?>" class="menu-link">
        <i class="menu-icon tf-icons mdi mdi-web"></i>
        <div>Tambah Domain</div>
      </a>
    </li>
    <li class="menu-item" id="ikon_mengambang">
      <a href="<?php echo $alamat_admin.'ikon_mengambang'; ?>" class="menu-link">
        <i class="menu-icon tf-icons mdi mdi-shape-outline"></i>
        <div>Ikon Mengambang</div>
      </a>
    </li>
    <li class="menu-item" id="pemberitahuan">
      <a href="<?php echo $alamat_admin.'pemberitahuan'; ?>" class="menu-link">
        <i class="menu-icon tf-icons mdi mdi-bell-outline"></i>
        <div>Pemberitahuan</div>
      </a>
    </li>

    <li class="menu-header fw-light mt-4">
      <span class="menu-header-text">Sistem & Akses</span>
    </li>
    <li class="menu-item" id="staff">
      <a href="<?php echo $alamat_admin.'staff'; ?>" class="menu-link">
        <i class="menu-icon tf-icons mdi mdi-account-group"></i>
        <div>Staff</div>
      </a>
    </li>
    <li class="menu-item" id="profil">
      <a href="<?php echo $alamat_admin.'profil'; ?>" class="menu-link">
        <i class="menu-icon tf-icons mdi mdi-account"></i>
        <div>Profil</div>
      </a>
    </li>
    <li class="menu-item" id="pengaturan">
      <a href="<?php echo $alamat_admin.'pengaturan'; ?>" class="menu-link">
        <i class="menu-icon tf-icons mdi mdi-cog"></i>
        <div>Pengaturan</div>
      </a>
    </li>
    <li class="menu-item">
      <a href="<?php echo $alamat_admin.'keluar.php'; ?>" class="menu-link">
        <i class="menu-icon tf-icons mdi mdi-power"></i>
        <div>Keluar</div>
      </a>
    </li>
    
  </ul>
</aside>
