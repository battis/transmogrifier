<?php

$data = '';
$error = false;
if (isset($_REQUEST['transmogrification'])) {
    $transmogrification = $_REQUEST['transmogrification'];
	switch($transmogrification) {
		case 'unserialize': {
			$data = print_r(unserialize($_REQUEST['data']), true);
			break;
		}
		case 'hash': {
			$data = hash($_REQUEST['hash'], $_REQUEST['data']);
			break;
		}
		case 'password_hash': {
			$data = password_hash($_REQUEST['data'], PASSWORD_DEFAULT);
			break;
		}
        case 'user':
            $transmogrification = $_REQUEST['user'];
        default: {
            try {
            $data = call_user_func($transmogrification, $_REQUEST['data']);
        } catch (Error $e) {
            $error = true;
            $data = $_REQUEST['data'];
        }
        }
	}
}

$php =['serialize','unserialize','urlencode','urldecode','base64_encode','base64_decode','strtotime','hash','password_hash']

?>
<!DOCTYPE html lang="en">
<html>
	<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Transmogrifier</title>
		<style>
			textarea {
				font-family: monospace;
			}
		</style>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
	</head>
	<body onload="document.getElementById('data').select();">
        <div class="container">
		<form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                <div class="input-group">
                <select name="transmogrification" id="transmogrification" class="form-control">
                    <optgroup label="PHP">
                    <?php
                    foreach($php as $function) {
                        echo "<option value=\"$function\"".($function == $_REQUEST['transmogrification'] ? ' selected':'').">$function</option>";
                    }
                    ?>
                    </optgroup>
                    <optgroup>
                    <option value="user"<?= !empty($_REQUEST['transmogrification']) && $_REQUEST['transmogrification'] == 'user' ? ' selected' : '' ?>>Manually entered</option>
                    </optgroup>
                </select>
                <input type="text" name="user" id="user" value="<?= $_REQUEST['user'] ?? ''?>" class="form-control"/>
                <select name="hash" class="form-control<?= $error ? ' error' : '' ?>" id="hash">
					<?php
						foreach (hash_algos() as $hash) {
							echo "<option value=\"$hash\"" . ($hash == $_REQUEST['hash']? ' selected' : '') . ">$hash</option>";
						}
					?>
				</select>
                <button type="submit" class="btn btn-primary">Transmogrify</button>
                </div>
			<div id="data-entry">
				<textarea class="form-control" cols="80" rows="<?= max(20, substr_count($data, "\n") + 3) ?>" id="data" name="data"><?= $data ?></textarea>
			</div>
		</form>
        </div>
        <script>
            const t = document.getElementById('transmogrification');
            const algorithm = document.getElementById('hash');
            const user = document.getElementById('user');
            function updateHashVisibility(transmogrification) {
                if (transmogrification.value == 'hash') {
                    algorithm.style.display = 'inline-block';
                } else {
                    algorithm.style.display = 'none';
                }

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
        </script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
	</body>
</html>
