/* RF03 — Recalcula el subtotal del pedido al cambiar la caja o la cantidad (RN02). */
(function () {
    const selCaja = document.getElementById('caja');
    const inCant = document.getElementById('cantidad');
    const resCaja = document.getElementById('resCaja');
    const resPrecio = document.getElementById('resPrecio');
    const resCant = document.getElementById('resCantidad');
    const resTotal = document.getElementById('resTotal');
    if (!selCaja || !inCant) return;

    const soles = (n) => 'S/ ' + n.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    function actualizar() {
        const opt = selCaja.options[selCaja.selectedIndex];
        const precio = opt && opt.dataset.precio ? parseFloat(opt.dataset.precio) : NaN;
        const cant = parseInt(inCant.value, 10);
        const cantOk = Number.isInteger(cant) && cant >= 1 && cant <= 10;

        resCaja.textContent = opt && opt.value ? opt.textContent.split('·')[0].trim() : '—';
        resPrecio.textContent = isNaN(precio) ? '—' : soles(precio);
        resCant.textContent = inCant.value || '—';
        resTotal.textContent = (!isNaN(precio) && cantOk) ? soles(Math.round(precio * cant * 100) / 100) : '—';
        inCant.classList.toggle('is-invalid', inCant.value !== '' && !cantOk);
    }

    selCaja.addEventListener('change', actualizar);
    inCant.addEventListener('input', actualizar);
    actualizar();
})();
