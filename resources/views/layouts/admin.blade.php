<html>
        <head>
                <meta charset="utf-8"/>
				<title>ERPLaPalma | Consulta</title>
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta content="width=device-width, initial-scale=1" name="viewport"/>
				<meta content="" name="description"/>
				<meta content="" name="author"/>
				<!-- BEGIN GLOBAL MANDATORY STYLES -->
				<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
				<link href="{$config.assets}global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
				<link href="{$config.assets}global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
				<link href="{$config.assets}global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
				<link href="{$config.assets}global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
				<link href="{$config.assets}global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
				<!-- END GLOBAL MANDATORY STYLES -->
				<!-- BEGIN PAGE STYLES -->
				<!--<link href="{$config.assets}admin/pages/css/tasks.css" rel="stylesheet" type="text/css"/>-->
				<!-- END PAGE STYLES -->
				<!-- BEGIN PAGE LEVEL STYLES -->

				<link rel="stylesheet" type="text/css" href="{$config.assets}global/plugins/select2/select2.css"/>
				<link rel="stylesheet" type="text/css" href="{$config.assets}global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
				<link rel="stylesheet" type="text/css" href="{$config.assets}global/plugins/bootstrap-datepicker/css/datepicker.css"/>
        <!--<link rel="stylesheet" type="text/css" href="{$config.assets}global/plugins/bootstrap/css/bootstrap.css"/>-->

				<!-- END PAGE LEVEL STYLES -->
				<!-- BEGIN THEME STYLES -->
				<link href="{$config.assets}global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
				<link href="{$config.assets}global/css/plugins.css" rel="stylesheet" type="text/css"/>
				<link href="{$config.assets}admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
				<link href="{$config.assets}admin/layout/css/themes/darkblue.css" rel="stylesheet" type="text/css"/>
				<link href="{$config.assets}admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
				<!-- END THEME STYLES -->

				<script src="{$config.assets}global/plugins/jquery.min.js" type="text/javascript"></script>
                

				<link rel="shortcut icon" href="{$config.assets}global/img/logo_cherry_64.png"/>
        </head>
        <body class="page-header-fixed page-quick-sidebar-over-content">
<!-- BEGIN HEADER -->
<div class="page-header -i navbar navbar-fixed-top">
	<!-- BEGIN HEADER INNER -->
	<div class="page-header-inner">
		<!-- BEGIN LOGO -->
		<div class="page-logo">
			<a href="{$config.route}">
			<img src="{$config.assets}admin/layout/img/logo.png" alt="logo" class="logo-default" style="margin-top:8px"/>
			</a>
			<div class="menu-toggler sidebar-toggler hide">
				<!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
			</div>
		</div>
		<!-- END LOGO -->
		<!-- BEGIN RESPONSIVE MENU TOGGLER -->
		<a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
		</a>
		<!-- END RESPONSIVE MENU TOGGLER -->
		<!-- BEGIN TOP NAVIGATION MENU -->
		<div class="top-menu">
			<ul class="nav navbar-nav pull-right">


				<!-- END TODO DROPDOWN -->
				<!-- BEGIN USER LOGIN DROPDOWN -->
				<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
				<li class="dropdown dropdown-user">
					<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
					<img alt="" class="img-circle" src="{$config.assets}admin/layout/img/avatar.png"/>
					<span class="username username-hide-on-mobile">
					{if $user_data.login}{$user_data.name}{/if} </span>
					<span class="username">
					{if $user_data.login}<b>&nbsp&nbsp({$user_data.entidad})</b>{/if} </span>
					<i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-default">
						<li>
							<a href="{$config.route}profile">
							<i class="icon-user"></i> Mis datos </a>
						</li>
						<li class="divider">
						</li>
						<!--<li>
							<a href="extra_lock.html">
							<i class="icon-lock"></i> Bloquear pantalla </a>
						</li>-->
						<li>
							<a href="{$config.route}exit">
							<i class="icon-key"></i> Salir </a>
						</li>
					</ul>
				</li>
				<!-- END USER LOGIN DROPDOWN -->
				<!-- BEGIN QUICK SIDEBAR TOGGLER -->
				<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
				<!--<li class="dropdown dropdown-quick-sidebar-toggler">
					<a href="{$config.route}exit" class="dropdown-toggle">
					<i class="icon-logout"></i>
					</a>
				</li>-->
				<!-- END QUICK SIDEBAR TOGGLER -->
			</ul>
		</div>
		<!-- END TOP NAVIGATION MENU -->
	</div>
	<!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<div class="clearfix">
</div>
<div class="page-container">
