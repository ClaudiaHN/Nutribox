<?php
/** Protege las páginas internas: sin sesión de administrador redirige al login (RF06, RNF03). */
if (empty($_SESSION['admin'])) {
    redirigir('login.php');
}
