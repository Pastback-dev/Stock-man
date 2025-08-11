<?php
  require('session.php');
  confirm_logged_in();

  $user_type = $_SESSION['TYPE'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <style type="text/css">
    #overlay {
      position: fixed;
      display: none;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0,0,0,0.5);
      z-index: 2;
      cursor: pointer;
    }
    #text{
      position: absolute;
      top: 50%;
      left: 50%;
      font-size: 50px;
      color: white;
      transform: translate(-50%,-50%);
      -ms-transform: translate(-50%,-50%);
    }
  </style>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Stock Control Management System</title>
  <link rel="icon" href="https://www.freeiconspng.com/uploads/sales-icon-7.png">

  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,700,900" rel="stylesheet">
  <link href="../css/sb-admin-2.min.css" rel="stylesheet">
  <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body id="page-top">

<div id="wrapper">

  <!-- Sidebar -->
  <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
      <div class="sidebar-brand-icon rotate-n-15">
        <i class="fas fa-laugh-wink"></i>
      </div>
      <div class="sidebar-brand-text mx-3">Stock Control System</div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item">
      <a class="nav-link" href="index.php">
        <i class="fas fa-fw fa-home"></i>
        <span>Accueil</span>
      </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">Général</div>

    <?php if ($user_type == 'Admin'): ?>
      <li class="nav-item">
        <a class="nav-link" href="Famille.php">
          <i class="fas fa-fw fa-user"></i>
          <span>Gestion des membres actifs</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="famille_retraite.php">
          <i class="fas fa-fw fa-user"></i>
          <span>Gestion des membres retraités</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="enfants_en_charge.php">
          <i class="fas fa-fw fa-user"></i>
          <span>Enfants à charge</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="liste_rouges.php">
          <i class="fas fa-fw fa-table"></i>
          <span>Listes rouges</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="allmembersencharge.php">
          <i class="fas fa-fw fa-archive"></i>
          <span>Liste globale</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="liste_des_adherents.php">
          <i class="fas fa-fw fa-retweet"></i>
          <span>Gestion des paiements d'adhésion</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="liste_des_adherents_retraites.php">
          <i class="fas fa-fw fa-retweet"></i>
          <span>Gestion des paiements d'adhésion (Retraités)</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="liste_des_adherents_history.php">
          <i class="fas fa-fw fa-history"></i>
          <span>Historique de la liste des adhérents</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="liste_des_adherents_history_RT.php">
          <i class="fas fa-fw fa-history"></i>
          <span>Historique de la liste des adhérents (Retraités)</span>
        </a>
      </li>

    <?php elseif ($user_type == 'Retraité'): ?>
      <li class="nav-item">
        <a class="nav-link" href="famille_retraite.php">
          <i class="fas fa-fw fa-user"></i>
          <span>Gestion des membres retraités</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="liste_des_adherents_retraites.php">
          <i class="fas fa-fw fa-retweet"></i>
          <span>Gestion des paiements d'adhésion (Retraités)</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="liste_des_adherents_history_RT.php">
          <i class="fas fa-fw fa-history"></i>
          <span>Historique de la liste des adhérents (Retraités)</span>
        </a>
      </li>

    <?php elseif ($user_type == 'active'): ?>
      <li class="nav-item">
        <a class="nav-link" href="Famille.php">
          <i class="fas fa-fw fa-user"></i>
          <span>Gestion des membres actifs</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="enfants_en_charge.php">
          <i class="fas fa-fw fa-user"></i>
          <span>Enfants à charge</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="allmembersencharge.php">
          <i class="fas fa-fw fa-archive"></i>
          <span>Liste des adhérents</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="liste_des_adherents.php">
          <i class="fas fa-fw fa-retweet"></i>
          <span>Gestion des paiements d'adhésion</span>
        </a>
      </li>

    <?php elseif ($user_type == 'global'): ?>
      <li class="nav-item">
        <a class="nav-link" href="allmembersencharge.php">
          <i class="fas fa-fw fa-archive"></i>
          <span>Tous les membres à charge</span>
        </a>
      </li>
    <?php endif; ?>

    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline">
      <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
  </ul>
  <!-- End of Sidebar -->

  <?php include_once 'topbar.php'; ?>
