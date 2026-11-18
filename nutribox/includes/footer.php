</main>

<footer class="nb-footer">
    <div class="container">
        <p class="mb-1">© <?= date('Y') ?> Nutribox · Snacks saludables en Lima y Callao.</p>
        <p class="mb-0 small">
            El pago y la entrega se coordinan por WhatsApp (Yape o Plin). La información nutricional es orientativa y no constituye
            diagnóstico ni tratamiento. Proyecto académico — Tecnologías E-Business, UNFV.
        </p>
    </div>
</footer>

<script src="<?= $base ?>assets/js/bootstrap.bundle.min.js"></script>
<?php if (!empty($scriptsExtra)): ?>
    <?php foreach ($scriptsExtra as $s): ?>
        <script src="<?= $base . e($s) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>
