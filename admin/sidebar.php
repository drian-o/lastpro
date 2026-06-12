<?php 
  // Menangkap nama halaman yang sedang aktif biar menu sidebar bisa nyala (highlight) otomatis
  // Disesuaikan: bisa menangkap dari parameter ?halaman=... atau dari nama file .php nya langsung
  $page_active = isset($_GET['halaman']) ? $_GET['halaman'] : basename($_SERVER['PHP_SELF'], ".php");
?>

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
    
    <li class="menu-item <?php echo ($page_active == 'dasbor') ? 'active' : ''; ?>" id="dasbor">
      <a href="<?php echo $alamat_admin.'dasbor'; ?>" class="menu-link">
        <i class="menu-icon tf-icons mdi mdi-monitor-dashboard"></i>
        <div>Dasbor</div>
      </a>
    </li>

    <li class="menu-item <?php echo in_array($page_active, ['provider', 'permainan', 'anggota']) ? 'active open' : ''; ?>">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons mdi mdi-database-outline"></i>
        <div>Master Data</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item <?php echo ($page_active == 'provider') ? 'active' : ''; ?>" id="provider">
          <a href="<?php echo $alamat_admin.'provider'; ?>" class="menu-link">
            <div>Provider</div>
          </a>
        </li>
        <li class="menu-item <?php echo ($page_active == 'permainan') ? 'active' : ''; ?>" id="permainan">
          <a href="<?php echo $alamat_admin.'permainan'; ?>" class="menu-link">
            <div>Permainan</div>
          </a>
        </li> 
        <li class="menu-item <?php echo ($page_active == 'anggota') ? 'active' : ''; ?>" id="anggota">
          <a href="<?php echo $alamat_admin.'anggota'; ?>" class="menu-link">
            <div>Anggota</div>
          </a>
        </li>
      </ul>
    </li>

    <li class="menu-item <?php echo in_array($page_active, ['deposit', 'withdraw', 'rekap', 'saldo', 'rekening']) ? 'active open' : ''; ?>">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons mdi mdi-wallet-outline"></i>
        <div>Keuangan</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item <?php echo ($page_active == 'deposit') ? 'active' : ''; ?>" id="deposit">
          <a href="<?php echo $alamat_admin.'deposit'; ?>" class="menu-link">
            <div>Deposit</div>
          </a>
        </li>
        <li class="menu-item <?php echo ($page_active == 'withdraw') ? 'active' : ''; ?>" id="withdraw">
          <a href="<?php echo $alamat_admin.'withdraw'; ?>" class="menu-link">
            <div>Withdraw</div>
          </a>
        </li>
        <li class="menu-item <?php echo ($page_active == 'rekap') ? 'active' : ''; ?>" id="rekap">
          <a href="<?php echo $alamat_admin.'rekap'; ?>" class="menu-link">
            <div>Rekap</div>
          </a>
        </li>
        <li class="menu-item <?php echo ($page_active == 'saldo') ? 'active' : ''; ?>" id="saldo">
          <a href="<?php echo $alamat_admin.'saldo'; ?>" class="menu-link">
            <div>Saldo</div>
          </a>
        </li>
        <li class="menu-item <?php echo ($page_active == 'rekening') ? 'active' : ''; ?>" id="rekening">
          <a href="<?php echo $alamat_admin.'rekening'; ?>" class="menu-link">
            <div>Rekening</div>
          </a>
        </li>
      </ul>
    </li>

    <li class="menu-item <?php echo in_array($page_active, ['promosi', 'bonus', 'refferal', 'bukti_jp']) ? 'active open' : ''; ?>">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons mdi mdi-bullhorn-outline"></i>
        <div>Pemasaran & Event</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item <?php echo ($page_active == 'promosi') ? 'active' : ''; ?>" id="promosi">
          <a href="<?php echo $alamat_admin.'promosi'; ?>" class="menu-link">
            <div>Promosi</div>
          </a>
        </li>
        <li class="menu-item <?php echo ($page_active == 'bonus') ? 'active' : ''; ?>" id="bonus">
          <a href="<?php echo $alamat_admin.'bonus'; ?>" class="menu-link">
            <div>Bonus</div>
          </a>
        </li>
        <li class="menu-item <?php echo ($page_active == 'refferal') ? 'active' : ''; ?>" id="refferal">
          <a href="<?php echo $alamat_admin.'refferal'; ?>" class="menu-link">
            <div>Refferal</div>
          </a>
        </li>
        <li class="menu-item <?php echo ($page_active == 'bukti_jp') ? 'active' : ''; ?>" id="bukti_jp">
          <a href="<?php echo $alamat_admin.'bukti_jp'; ?>" class="menu-link">
            <div>Bukti JP</div>
          </a>
        </li>
      </ul>
    </li>

    <li class="menu-item <?php echo in_array($page_active, ['add_domain', 'tambah_domain', 'ikon_mengambang', 'pemberitahuan']) ? 'active open' : ''; ?>">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons mdi mdi-web"></i>
        <div>Manajemen Web</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item <?php echo ($page_active == 'add_domain' || $page_active == 'tambah_domain') ? 'active' : ''; ?>" id="add_domain"> 
          <a href="<?php echo $alamat_admin.'add_domain'; ?>" class="menu-link">
            <div>Tambah Domain</div>
          </a>
        </li>
        <li class="menu-item <?php echo ($page_active == 'ikon_mengambang') ? 'active' : ''; ?>" id="ikon_mengambang">
          <a href="<?php echo $alamat_admin.'ikon_mengambang'; ?>" class="menu-link">
            <div>Ikon Mengambang</div>
          </a>
        </li>
        <li class="menu-item <?php echo ($page_active == 'pemberitahuan') ? 'active' : ''; ?>" id="pemberitahuan">
          <a href="<?php echo $alamat_admin.'pemberitahuan'; ?>" class="menu-link">
            <div>Pemberitahuan</div>
          </a>
        </li>
      </ul>
    </li>

    <li class="menu-item <?php echo in_array($page_active, ['staff', 'profil', 'pengaturan', 'log_aktifitas']) ? 'active open' : ''; ?>">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons mdi mdi-cog-outline"></i>
        <div>Sistem & Akses</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item <?php echo ($page_active == 'staff') ? 'active' : ''; ?>" id="staff">
          <a href="<?php echo $alamat_admin.'staff'; ?>" class="menu-link">
            <div>Staff</div>
          </a>
        </li>
        <li class="menu-item <?php echo ($page_active == 'profil') ? 'active' : ''; ?>" id="profil">
          <a href="<?php echo $alamat_admin.'profil'; ?>" class="menu-link">
            <div>Profil</div>
          </a>
        </li>
        <li class="menu-item <?php echo ($page_active == 'pengaturan') ? 'active' : ''; ?>" id="pengaturan">
          <a href="<?php echo $alamat_admin.'pengaturan'; ?>" class="menu-link">
            <div>Pengaturan</div>
          </a>
        </li>
        <li class="menu-item <?php echo ($page_active == 'log_active') ? 'active' : ''; ?>" id="log_aktifitas">
          <a href="<?php echo $alamat_admin.'log_active'; ?>" class="menu-link">
            <div>Log Aktivitas</div>
          </a>
        </li>
      </ul>
    </li>

    <li class="menu-item mt-4">
      <a href="<?php echo $alamat_admin.'keluar.php'; ?>" class="menu-link text-danger">
        <i class="menu-icon tf-icons mdi mdi-power"></i>
        <div>Keluar</div>
      </a>
    </li>
    
  </ul>
</aside>
