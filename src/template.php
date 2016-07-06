<?php

namespace Builder;

/** @var Project $project */
/** @var string $dest */
/** @var bool $success */

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Phansible role builder</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
</head>
<body>

<div class="container">
    <h1>Phansible box builder</h1>
    <?php if (!$success) { ?>
        <p class="alert alert-warning">You are setting up the box located at <strong><?= htmlentities($dest) ?></strong>. Please make sure this is the correct directory.</p>
    <?php } else { ?>
        <p class="alert alert-success">Your chosen settings were succesfully applied to the box located at <strong><?= htmlentities($dest) ?></strong>.</p>
    <?php } ?>
    <form method="post">
        <p class="text-right">
            <input type="submit" name="build" value="Start build" class="btn btn-primary">
            <input type="reset" value="Reset" class="btn btn-danger">
        </p>
        <div class="row">
            <div class="col-sm-6 col-xs-12">
                <h2>Roles</h2>
                <?php foreach(['server', 'vagrant_local', 'app'] as $role) { ?>
                    <div class="checkbox">
                        <label>
                            <input type="hidden" name="role[<?= $role ?>]" value="1">
                            <input type="checkbox" id="<?= $role ?>" disabled checked>
                            <?= $role ?>
                        </label>
                    </div>
                <?php } ?>
                <?php foreach(['git-prompt', 'php7', 'composer', 'nginx', 'mongodb', 'zephir', 'phalcon'] as $role) { ?>
                    <div class="checkbox">
                        <label>
                            <input type="hidden" name="role[<?= $role ?>]" value="0">
                            <input type="checkbox" value="1" id="<?= $role ?>" name="role[<?= $role ?>]"<?php if ($project->hasRole($role)) { ?> checked<?php } ?>>
                            <?= $role ?>
                        </label>
                    </div>
                <?php } ?>

                <h2>System packages</h2>
                <?php foreach(['git', 'imagemagick', 'vim', 'nodejs', 'npm', 'pkg-config', 'libssl-dev', 'libsslcommon2-dev'] as $package) { ?>
                    <div class="checkbox">
                        <label>
                            <input type="hidden" name="system[<?= $package ?>]" value="0">
                            <input type="checkbox" value="1" id="<?= $package ?>" name="system[<?= $package ?>]"<?php if ($project->hasPackage($package)) { ?> checked<?php } ?>>
                            <?= $package ?>
                        </label>
                    </div>
                <?php } ?>

                <h2>PHP extensions</h2>
                <?php foreach([
                    'mongodb' => 'pkg-config libssl-dev libsslcommon2-dev'
                ] as $extension => $dependencies) { ?>
                    <div class="checkbox">
                        <label>
                            <input type="hidden" name="pecl[mongodb]" value="0">
                            <input type="checkbox" value="1" id="<?= $extension ?>" name="pecl[<?= $extension ?>]" data-require="<?= $dependencies ?>"<?php if ($project->hasPhpExtension($extension)) { ?> checked<?php } ?>>
                            <?= $extension ?>
                        </label>
                    </div>
                <?php } ?>
            </div>
            <div class="col-sm-6 col-xs-12">
                <h2>PHP packages</h2>
                <?php foreach([
                          'php7.0-bcmath', 'php7.0-bz2', 'php7.0-curl', 'php7.0-dba', 'php7.0-enchant', 'php7.0-gd', 'php7.0-gmp', 'php7.0-imap', 'php7.0-interbase',
                          'php7.0-intl', 'php7.0-json', 'php7.0-ldap', 'php7.0-mbstring', 'php7.0-mcrypt', 'php7.0-mysql', 'php7.0-odbc', 'php7.0-opcache',
                          'php7.0-pgsql', 'php7.0-pspell', 'php7.0-readline', 'php7.0-recode', 'php7.0-snmp', 'php7.0-soap', 'php7.0-sqlite3', 'php7.0-sybase',
                          'php7.0-tidy', 'php7.0-xml', 'php7.0-xmlrpc', 'php7.0-xsl', 'php7.0-zip'
                ] as $package) { ?>
                    <div class="checkbox">
                        <label>
                            <input type="hidden" name="php[<?= $package ?>]" value="0">
                            <input type="checkbox" value="1" id="<?= $package ?>" name="php[<?= $package ?>]"<?php if ($project->hasPhpPackage($package)) { ?> checked<?php } ?>>
                            <?= $package ?>
                        </label>
                    </div>
                <?php } ?>
            </div>
        </div>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<script>
    $(function() {
        $('[data-require]').change(function(e) {
            var $this = $(this);
            var checked = $this.is(':checked');
            var req = $this.data('require').split(' ');
            $.each(req, function(index, el) {
                var $el = $('#' + el);

                var count = parseInt($el.data('required')) || 0;
                if (checked) count++;
                else if (count > 0) count--;
                $el.data('required', count);

                $el.prop('checked', count > 0);
            });
        });
    });
</script>
</body>
</html>