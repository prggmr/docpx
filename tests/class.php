<?php
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

require_once 'test_init.php';

import('unittest');

/**
 * Test parsing a class.
 */
unittest\suite(function($suite) {

    $suite->setup(function($test){
        $src = <<<'SRC'
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Oraculum - <?php echo $title ?></title>

        <meta name="description" content="" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <!-- basic styles -->
        <link rel="stylesheet" href="<?php echo base_url('css/bootstrap.min.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('css/bootstrap-responsive.min.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('css/font-awesome.min.css'); ?>">

        <!--[if IE 7]>
          <link rel="stylesheet" href="<?php echo base_url('css/font-awesome-ie7.min.css'); ?>">
        <![endif]-->

        <!-- ace styles -->
        <link rel="stylesheet" href="<?php echo base_url('css/ace.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('css/ace-responsive.css'); ?>">

        <!-- oraculum styles -->
        <link rel="stylesheet" href="<?php echo base_url('css/screen.css'); ?>">

        <!--[if lt IE 9]>
          <link rel="stylesheet" href="<?php echo base_url('css/ace-ie.css'); ?>">
        <![endif]-->

        <!-- jQuery -->
        <script src="<?php echo base_url('js/jquery-1.9.1.min.js'); ?>"></script>
    </head>

    <body class="login-layout">
        <div class="container-fluid" id="main-container">
            <div id="main-content">
                <div class="row-fluid">
                    <div class="span12">
                        <?php echo get_confirmation(); ?>

                        <div class="login-container">
                            <div class="row-fluid">
                                <div class="center">
                                    <h1><span class="white">Oraculum</span></h1>
                                </div>
                            </div>

                            <div class="space-6"></div>

                            <div class="row-fluid">
                                <div class="position-relative">
                                    <div id="login-box" class="login-box visible widget-box no-border">
                                        <div class="widget-body">
                                            <div class="widget-main">
                                                <h4 class="header blue lighter bigger">
                                                    <i class="icon-key"></i>
                                                    <?php echo lang('login_enter_credentials') ?>
                                                </h4>

                                                <div class="space-6"></div>

                                                <form name="login" action="<?php echo base_url() ?>" method="POST" class="login-form">
                                                    <input type="hidden" name="ref" value="<?php echo $ref; ?>">
                                                    <fieldset>
                                                        <div class="control-group">
                                                            <label>
                                                                <span class="block input-icon input-icon-right">
                                                                    <input type="text" name="login" id="login" class="span12" placeholder="<?php echo   lang('login_login') ?>" />
                                                                    <i class="icon-user"></i>
                                                                </span>
                                                            </label>
                                                        </div>

                                                        <div class="control-group">
                                                            <label>
                                                                <span class="block input-icon input-icon-right">
                                                                    <input type="password" name="password" id="password" class="span12" placeholder="<?php echo lang('login_password') ?>" />
                                                                    <i class="icon-lock"></i>
                                                                </span>
                                                            </label>
                                                        </div>

                                                        <div class="space"></div>

                                                        <div class="row-fluid">
                                                            <label class="span8">
                                                                <input type="checkbox" name="remember_me" id="remember_me" value="1" />
                                                                <span class="lbl"><?php echo lang('login_remember_me') ?></span>
                                                            </label>

                                                            <input type="hidden" name="button_value" value="login" />
                                                            <button type="submit" name="login_submit" class="span4 btn btn-small btn-primary">
                                                                <?php echo lang('login_login') ?>
                                                            </button>
                                                        </div>
                                                    </fieldset>
                                                </form>
                                            </div><!--/widget-main-->

                                            <div class="toolbar clearfix">
                                                <div>
                                                    <a href="#" class="forgot-password-link">
                                                        <i class="icon-arrow-left"></i>
                                                        <?php echo lang('login_i_forgot_my_password') ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div><!--/widget-body-->
                                    </div><!--/login-box-->

                                    <div id="forgot-box" class="forgot-box widget-box no-border">
                                        <div class="widget-body">
                                            <div class="widget-main">
                                                <h4 class="header red lighter bigger">
                                                    <i class="icon-key"></i>
                                                    <?php echo lang('login_retrieve_password') ?>
                                                </h4>

                                                <div class="space-6"></div>
                                                <p>
                                                    <?php echo lang('login_retrieve_password_description') ?>
                                                </p>

                                                <form name="retrieve_password" action="<?php echo base_url() ?>" method="POST" class="retrieve-password-form">
                                                    <fieldset>
                                                        <div class="control-group">
                                                            <label>
                                                                <span class="block input-icon input-icon-right">
                                                                    <input type="email" name="email" id="email" class="span12" placeholder="<?php echo lang('login_email') ?>" />
                                                                    <i class="icon-envelope"></i>
                                                                </span>
                                                            </label>
                                                        </div>

                                                        <div class="row-fluid">
                                                            <input type="hidden" name="button_value" value="retrieve_password" />
                                                            <button type="submit" name="retrieve_password_submit" class="span5 offset7 btn btn-small btn-danger">
                                                                <?php echo lang('login_send') ?>
                                                            </button>
                                                        </div>
                                                    </fieldset>
                                                </form>
                                            </div><!--/widget-main-->

                                            <div class="toolbar center">
                                                <a href="#" class="back-to-login-link">
                                                    <?php echo lang('login_back_to_login') ?>
                                                    <i class="icon-arrow-right"></i>
                                                </a>
                                            </div>
                                        </div><!--/widget-body-->
                                    </div><!--/forgot-box-->
                                </div><!--/position-relative-->
                            </div>
                        </div>
                    </div><!--/span-->
                </div><!--/row-->
            </div>
        </div><!--/.fluid-container-->

        <!--basic scripts-->
        <script type="text/javascript" src="<?php echo base_url('js/bootstrap.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('js/jquery.validate.min.js'); ?>"></script>

        <!--page specific plugin scripts-->

        <!--inline scripts related to this page-->

        <script type="text/javascript">
        $(document).ready(function() {
            $('#login').focus();
            $('.back-to-login-link').on('click', function() {
                show_box('login-box');
            });
            $('.forgot-password-link').on('click', function() {
                show_box('forgot-box');
            });
        });

        $(document).on('click', ".login-form button[type=submit]", function() {
            var $form = $("form.login-form");
            if ($form.valid()) {
                $form[0].submit();
            }
        });

        $(document).on('click', ".retrieve-password-form button[type=submit]", function() {
            var $form = $("form.retrieve-password-form");
            if ($form.valid()) {
                $form[0].submit();
            }
        });

        function show_box(id) {
            $('.widget-box.visible').removeClass('visible');
            $box = $('#'+id);
            $box.addClass('visible');
            $box.find('input').filter(':first').focus();
        }

        <?php echo $jquery_validation ?>
        </script>
    </body>
</html>
SRC;
        file_put_contents('/tmp/test_class.php', $src);
    });

    $suite->teardown(function(){
        unlink('/tmp/test_class.php');
    });

    $suite->test(function(){

        $doc = new \docpx\Doc(
            '/tmp/test_class.php',
            new \docpx\Tokens('/tmp/test_class.php')
        );

        var_dump($doc->parse());
    });

});

