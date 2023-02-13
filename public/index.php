<?php

$php =['serialize','unserialize','urlencode','urldecode','base64_encode','base64_decode','strtotime','hash','password_hash'];
$submenus = ['user','password_hash','hash'];

$data = $_REQUEST['data'];
$error = false;

if (isset($_REQUEST['transmogrification'])) {
    $transmogrification = $_REQUEST['transmogrification'];
	switch($transmogrification) {
		case 'unserialize':
			$data = print_r(unserialize($_REQUEST['data']), true);
			break;
		case 'hash':
			$data = hash($_REQUEST['hash'], $_REQUEST['data']);
			break;
		case 'password_hash':
			$data = password_hash($_REQUEST['data'], $_REQUEST['password_hash']);
			break;
        case 'user':
            $transmogrification = $_REQUEST['user'];
            // intentional fall-through
        default:
            try {
                $data = call_user_func($transmogrification, $_REQUEST['data']);
            } catch (Throwable $e) {
                $error = str_replace('call_user_func(): Argument #1 ($callback) must be a valid callback, ','', $e->getMessage());
            }
	}
}

function optionsFromArray($arr, $key, $default) {
    $options = [];
    $selector = $_REQUEST[$key] ?? $default;
    foreach($arr as $opt) {
        $selected = $opt == $selector ? ' selected' : '';
        $options[] = <<<EOT
<option value="{$opt}"{$selected}>{$opt}</option>
EOT;
    }
    return join(PHP_EOL, $options);
}

?>
<!doctype html lang="en">
<html>
	<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Transmogrifier</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
        <style>
            .data-entry {
                font-family: monospace;
            }
            .btn.transmogrify img {
                height: 1em;
            }
            .hidden {
                display: none;
            }
        </style>
  	</head>
	<body>
        <div class="container">
            <div class="alert alert-danger<?= $error ? '' : ' hidden'?>" role="alert"><?= $error ?></div>
		    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                <div class="input-group">
                    <select name="transmogrification" id="transmogrification" class="form-control">
                        <optgroup label="PHP">
                        <?= optionsFromArray($php, 'transmogrification', 'urlencode') ?>
                        </optgroup>
                        <optgroup>
                        <option value="user"<?= !empty($_REQUEST['transmogrification']) && $_REQUEST['transmogrification'] == 'user' ? ' selected' : '' ?>>Manually entered</option>
                        </optgroup>
                    </select>
                    <input type="text" name="user" id="user" value="<?= $_REQUEST['user'] ?? ''?>" class="form-control"/>
                    <select name="hash" class="form-control<?= $error ? ' error' : '' ?>" id="hash">
					    <?= optionsFromArray(hash_algos(), 'hash', 'md5')?>
				    </select>
                    <select name="password_hash" class="form-control" id="password_hash">
                        <?= optionsFromArray(function_exists('password_algos') ? password_algos() : [PASSWORD_DEFAULT], 'password_hash', PASSWORD_DEFAULT)?>
                    </select>
                    <button type="submit" class="btn btn-primary transmogrify"><img src="transmogrifier.png" /></button>
                </div>
			    <div id="data-entry">
				    <textarea class="form-control" cols="80" rows="<?= max(20, substr_count($data, "\n") + 3) ?>" id="data" name="data"><?= $data ?></textarea>
			    </div>
		    </form>
        </div>
        <script>
            const t = document.getElementById('transmogrification');
            <?php foreach ($submenus as $submenu) { echo <<< EOT
            const {$submenu} = document.getElementById('{$submenu}');
            EOT; } ?>
            function updateHashVisibility(transmogrification) {
            <?php foreach($submenus as $submenu) { echo <<< EOT
                if (transmogrification.value == '{$submenu}') {
                    {$submenu}.style.display = 'inline-block';
                } else {
                    {$submenu}.style.display = 'none';
                }
            EOT; } ?>
                if (transmogrification.value == 'user') {
                    user.style.display = 'inline-block';
                } else {
                    user.style.display = 'none';
                }
            }
            t.addEventListener('change', (event) => {
                updateHashVisibility(event.target)
            }, {passive: true});

            updateHashVisibility(t);

            document.getElementById('data').select();
        </script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
	</body>
</html>
