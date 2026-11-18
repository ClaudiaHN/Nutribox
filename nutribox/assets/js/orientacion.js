/* ============================================================
   Nutribox — Balance diario (RF08, RN09) y sugerencias (RF09, RN10–RN12)
   Todo ocurre en el navegador. No se usa fetch, cookies ni localStorage.
   ============================================================ */
(function () {
    'use strict';

    const productos = JSON.parse(document.getElementById('datosProductos').textContent);
    const porId = Object.fromEntries(productos.map(p => [String(p.id), p]));

    const fmt = (n, dec) => Number(n).toLocaleString('es-PE', { minimumFractionDigits: 0, maximumFractionDigits: dec });
    const esc = (s) => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    /* ---------------------- RF08: BALANCE DIARIO ---------------------- */
    const inputs = Array.from(document.querySelectorAll('.porcion'));
    const meta = document.getElementById('metaProteina');
    const barra = document.getElementById('barraMeta');
    const metaTexto = document.getElementById('metaTexto');
    const avisoParcial = document.getElementById('avisoParcial');
    const nutrientes = [
        { clave: 'proteinas', id: 'totProteinas', unidad: 'g', nombre: 'proteínas' },
        { clave: 'energia',   id: 'totEnergia',   unidad: 'kcal', nombre: 'energía' },
        { clave: 'hierro',    id: 'totHierro',    unidad: 'mg', nombre: 'hierro' },
        { clave: 'azucares',  id: 'totAzucares',  unidad: 'g', nombre: 'azúcares' },
    ];

    function porcionesDe(input) {
        const v = parseInt(input.value, 10);
        if (!Number.isInteger(v) || v < 0 || v > 10) {
            input.classList.add('is-invalid');
            return 0;                       // RN09: solo enteros 0..10; un valor inválido no suma
        }
        input.classList.remove('is-invalid');
        return v;
    }

    function calcularBalance() {
        const totales = { proteinas: 0, energia: 0, hierro: 0, azucares: 0 };
        const parciales = { proteinas: [], energia: [], hierro: [], azucares: [] };
        let haySeleccion = false;

        inputs.forEach(inp => {
            const n = porcionesDe(inp);
            if (n === 0) return;
            haySeleccion = true;
            const p = porId[inp.dataset.id];
            nutrientes.forEach(nu => {
                if (p[nu.clave] === null || p[nu.clave] === undefined) {
                    parciales[nu.clave].push(p.nombre);        // dato ausente: no se cuenta como cero
                } else {
                    totales[nu.clave] += p[nu.clave] * n;
                }
            });
        });

        const faltantes = [];
        nutrientes.forEach(nu => {
            const el = document.getElementById(nu.id);
            const incompleto = parciales[nu.clave].length > 0;
            el.classList.toggle('nd', incompleto && totales[nu.clave] === 0);
            if (incompleto && totales[nu.clave] === 0) {
                el.textContent = 'No disponible';
            } else {
                el.textContent = fmt(totales[nu.clave], 2) + ' ' + nu.unidad + (incompleto ? ' *' : '');
            }
            if (incompleto) faltantes.push(nu.nombre + ' (' + parciales[nu.clave].join(', ') + ')');
        });

        if (faltantes.length) {
            avisoParcial.textContent = '* Total parcial: falta el dato de ' + faltantes.join('; ') + '. Los valores ausentes no se cuentan como cero.';
            avisoParcial.classList.remove('d-none');
        } else {
            avisoParcial.classList.add('d-none');
        }

        /* Meta de proteína opcional, > 0 (RN09) */
        const metaVal = meta.value.trim() === '' ? null : Number(meta.value);
        const metaOk = metaVal !== null && Number.isFinite(metaVal) && metaVal > 0;
        meta.classList.toggle('is-invalid', metaVal !== null && !metaOk);

        if (!metaOk) {
            barra.style.width = '0%';
            barra.textContent = '';
            metaTexto.textContent = metaVal === null ? 'Sin meta' : 'Meta inválida';
            return;
        }
        const pct = Math.round((totales.proteinas / metaVal) * 100);   // sin división por cero: metaVal > 0
        barra.style.width = Math.min(pct, 100) + '%';
        barra.textContent = pct + ' %';
        barra.classList.toggle('sobre', pct > 100);
        metaTexto.textContent = fmt(totales.proteinas, 2) + ' g de ' + fmt(metaVal, 0) + ' g (' + pct + ' %)';
    }

    inputs.forEach(i => i.addEventListener('input', calcularBalance));
    meta.addEventListener('input', calcularBalance);
    document.getElementById('btnReiniciar').addEventListener('click', () => {
        inputs.forEach(i => { i.value = 0; i.classList.remove('is-invalid'); });
        meta.value = '';
        meta.classList.remove('is-invalid');
        calcularBalance();
    });
    calcularBalance();

    /* ---------------------- RF09: SUGERENCIAS ---------------------- */
    const OBJETIVO_A_PERFIL = { proteina: 'Proteína', porciones: 'Control de porciones', energia: null };
    const OBJETIVO_TEXTO = { proteina: 'más proteína', energia: 'energía para el día', porciones: 'control de porciones / bajo en azúcar' };

    function tarjeta(html, limite) {
        return '<div class="sugerencia' + (limite ? ' limite' : '') + '">' + html + '</div>';
    }

    function fichaCaja(p) {
        const val = (v, u) => (v === null ? 'no disponible' : fmt(v, 2) + ' ' + u);
        return '<div class="small mt-2"><strong>Aporte por porción</strong> (' + esc(p.porcion) + '): proteínas ' + val(p.proteinas, 'g') +
            ' · energía ' + val(p.energia, 'kcal') + ' · hierro ' + val(p.hierro, 'mg') + ' · azúcares ' + val(p.azucares, 'g') +
            '<br><strong>Alérgenos:</strong> ' + esc(p.alergenos) +
            '<br><span class="' + (p.verificado ? 'badge-verificado' : 'badge-ficticio') + '">' + esc(p.fuente) + '</span></div>';
    }

    function sugerirPorObjetivo(obj) {
        const perfil = OBJETIVO_A_PERFIL[obj];
        let caja = null;
        if (perfil) {
            caja = productos.find(p => p.perfil === perfil && p.validada) || productos.find(p => p.perfil === perfil) || null;
        } else {
            caja = productos.find(p => p.objetivo === 'Energía') || null;
        }
        if (!caja) {
            return tarjeta('<strong>Sin sugerencia específica.</strong> No hay una caja activa etiquetada para <em>' + esc(OBJETIVO_TEXTO[obj]) +
                '</em>. Puedes comparar las cajas en el balance diario.', true);
        }
        const motivo = perfil
            ? 'Su etiqueta de recomendación es «' + esc(caja.perfil) + '», que coincide con tu objetivo de ' + esc(OBJETIVO_TEXTO[obj]) + '.'
            : 'Su objetivo declarado es «Energía», que coincide con lo que buscas.';
        const nota = caja.validada
            ? ''
            : '<div class="small text-muted mt-2">Orientación general por objetivo: la ficha de esta caja aún no ha sido revisada por un profesional de nutrición, por lo que no se emite una sugerencia de salud.</div>';
        return tarjeta('<strong>Caja sugerida: ' + esc(caja.nombre) + '</strong><br><span class="small">Motivo: ' + motivo + '</span>' + fichaCaja(caja) + nota, false);
    }

    function orientarIMC(peso, talla, adulto, noGestante) {
        const pesoDado = peso.trim() !== '';
        const tallaDada = talla.trim() !== '';
        if (!pesoDado && !tallaDada) return null;                      // no se pidió IMC

        if (!adulto || !noGestante) {
            return tarjeta('<strong>Orientación por IMC no disponible.</strong> El cálculo de IMC se ofrece solo a personas adultas (18 años o más) que no estén en gestación. ' +
                'Puedes seguir usando el catálogo y el balance diario; te orientamos por objetivo.', true);
        }
        const p = Number(peso), t = Number(talla);
        if (!pesoDado || !tallaDada) {
            return tarjeta('<strong>Datos incompletos.</strong> Para orientar por IMC se necesitan peso (kg) y talla (m); con un solo dato no se clasifica. Te orientamos por objetivo.', true);
        }
        if (!Number.isFinite(p) || !Number.isFinite(t) || p <= 0 || t <= 0 || p > 400 || t < 0.5 || t > 2.5) {
            return tarjeta('<strong>Datos inválidos.</strong> Peso y talla deben ser positivos (talla en metros, por ejemplo 1.70). No se calcula el IMC.', true);
        }
        const imc = p / (t * t);
        const imcTxt = fmt(imc, 2);
        if (imc < 25) {
            return { html: tarjeta('<strong>IMC de referencia: ' + imcTxt + '</strong><br><span class="small">Está por debajo de 25, fuera de los rangos de sobrepeso u obesidad de referencia general (no es un diagnóstico individual). Te orientamos por objetivo.</span>', false), rango: 'normal', imc };
        }
        const rango = imc < 30 ? 'sobrepeso' : 'obesidad';
        const caja = productos.find(q => q.perfil === 'Control de porciones' && q.validada);
        let html = '<strong>IMC de referencia: ' + imcTxt + '</strong><br><span class="small">Tu IMC está en el rango de ' + rango +
            ' de referencia general (RN10); no es un diagnóstico individual. ';
        if (caja) {
            html += 'Puedes revisar esta caja para comparar sus porciones y aporte energético.</span>' + fichaCaja(caja);
            return { html: tarjeta(html, false), rango, imc };
        }
        html += 'No hay por ahora una caja con etiqueta de control de porciones validada por un profesional de nutrición, así que no se emite una sugerencia específica de salud; puedes comparar porciones y energía en el balance diario.</span>';
        return { html: tarjeta(html, true), rango, imc };
    }

    function orientarAnemia() {
        const caja = productos.find(q => q.perfil === 'Aporte de hierro' && q.validada && q.hierro !== null);
        if (caja) {
            return tarjeta('<strong>Declaraste un diagnóstico previo de anemia.</strong><br><span class="small">Esta caja aporta ' + fmt(caja.hierro, 2) +
                ' mg de hierro por porción según su ficha; consulta su incorporación con el profesional que te atiende. No es un tratamiento.</span>' + fichaCaja(caja), false);
        }
        return tarjeta('<strong>Declaraste un diagnóstico previo de anemia.</strong><br><span class="small">No hay una sugerencia específica validada: ninguna caja activa tiene aporte de hierro documentado y revisado por un profesional de nutrición (RN11). ' +
            'Consulta con el profesional que te atiende; puedes comparar el hierro por porción en el balance diario.</span>', true);
    }

    document.getElementById('btnSugerir').addEventListener('click', () => {
        const obj = document.getElementById('objetivo').value;
        const peso = document.getElementById('peso').value;
        const talla = document.getElementById('talla').value;
        const adulto = document.getElementById('adulto').checked;
        const noGestante = document.getElementById('noGestante').checked;
        const anemia = document.getElementById('anemia').checked;      // RN11: solo declaración voluntaria

        const partes = [];
        const imc = orientarIMC(peso, talla, adulto, noGestante);
        const imcEnRango = imc && typeof imc === 'object' && (imc.rango === 'sobrepeso' || imc.rango === 'obesidad');

        if (imcEnRango && anemia) {
            /* RN12: ante varias condiciones no se combinan recomendaciones clínicas */
            partes.push(sugerirPorObjetivo(obj));
            partes.push(tarjeta('<strong>Varias condiciones declaradas.</strong><br><span class="small">Tu IMC de referencia es ' + fmt(imc.imc, 2) +
                ' (rango de ' + imc.rango + ') y declaraste un diagnóstico de anemia. Para no combinar recomendaciones clínicas, solo te orientamos por objetivo y te sugerimos consultar con un profesional de nutrición.</span>', true));
        } else {
            partes.push(sugerirPorObjetivo(obj));
            if (imc) partes.push(typeof imc === 'string' ? imc : imc.html);
            if (anemia) partes.push(orientarAnemia());
        }
        partes.push('<p class="aviso mb-0">Estas sugerencias son orientativas, no diagnostican ni prometen bajar de peso o tratar anemia. Los datos ingresados no se envían ni se guardan.</p>');
        document.getElementById('resultadoSugerencia').innerHTML = partes.join('');
    });
})();
