<?php
require __DIR__ . '/includes/init.php';

fermerSession();
rediriger('index.php', 'info', 'Tu es déconnecté. À bientôt dans l\'arène !');
