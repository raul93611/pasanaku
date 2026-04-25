// Pasanaku — app.js

// ── MOBILE SIDEBAR ──────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  const toggle  = document.getElementById('sidebar-toggle');
  const sidebar = document.querySelector('.sidebar');
  const overlay = document.querySelector('.sidebar-overlay');

  function closeSidebar() {
    sidebar?.classList.remove('open');
    overlay?.classList.remove('open');
  }

  toggle?.addEventListener('click', () => {
    sidebar?.classList.toggle('open');
    overlay?.classList.toggle('open');
  });

  overlay?.addEventListener('click', closeSidebar);

  // Auto-close flash messages
  setTimeout(() => {
    document.querySelectorAll('.flash-msg').forEach(el => {
      el.style.transition = 'opacity 0.5s';
      el.style.opacity = '0';
      setTimeout(() => el.remove(), 500);
    });
  }, 4000);
});

// ── TOAST ───────────────────────────────────────────────────────────────────
function showToast(msg, type = 'success') {
  let container = document.querySelector('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }
  const icons = { success: 'check-circle', warning: 'exclamation-triangle', error: 'x-circle', info: 'info-circle' };
  const toast = document.createElement('div');
  toast.className = `toast-item ${type}`;
  toast.innerHTML = `<i class="bi bi-${icons[type] || 'info-circle'}"></i> ${msg}`;
  container.appendChild(toast);
  setTimeout(() => {
    toast.style.transition = 'opacity 0.3s, transform 0.3s';
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(20px)';
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// ── MODAL HELPERS ────────────────────────────────────────────────────────────
function openModal(id) {
  document.getElementById(id)?.classList.remove('d-none');
}
function closeModal(id) {
  document.getElementById(id)?.classList.add('d-none');
}

// Close modal on overlay click
document.addEventListener('click', (e) => {
  if (e.target.classList.contains('modal-overlay')) {
    e.target.classList.add('d-none');
  }
});

// ── PAY CELL TOGGLE ──────────────────────────────────────────────────────────
function initPayCells() {
  document.querySelectorAll('.pay-cell[data-part-id]').forEach(cell => {
    cell.addEventListener('click', async () => {
      const partId    = cell.dataset.partId;
      const ronda     = cell.dataset.ronda;
      const pasanakuId = cell.dataset.pasanakuId;

      try {
        const res = await fetch('?page=pasanaku&action=togglePago', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({ participante_id: partId, ronda, pasanaku_id: pasanakuId })
        });
        const data = await res.json();
        if (!data.ok) return;

        if (data.pagado) {
          cell.className = 'pay-cell pay-paid';
          cell.innerHTML = '<i class="bi bi-check-lg"></i>';
          cell.title = 'Pagó ✓ — clic para marcar pendiente';
        } else {
          cell.className = 'pay-cell pay-pending';
          cell.innerHTML = '<i class="bi bi-clock"></i>';
          cell.title = 'Pendiente — clic para marcar pagado';
        }

        // Update counts
        const countEl = document.getElementById('pagados-count');
        const totalEl = document.getElementById('total-count');
        if (countEl && totalEl) {
          countEl.textContent = data.pagados_count;
          const total = parseInt(totalEl.textContent);
          const pct = Math.round((data.pagados_count / total) * 100);
          document.getElementById('pagados-pct')?.style &&
            (document.getElementById('pagados-pct').style.width = pct + '%');
          document.getElementById('pagados-pct-label')?.textContent &&
            (document.getElementById('pagados-pct-label').textContent = pct + '%');
          // Recaudado
          const montoBase = parseInt(document.getElementById('monto-base')?.value || 0);
          const recaudadoEl = document.getElementById('recaudado-amount');
          if (recaudadoEl && montoBase) recaudadoEl.textContent = 'Bs ' + (data.pagados_count * montoBase).toLocaleString();
        }

        showToast(data.pagado ? 'Pago registrado' : 'Pago revertido', data.pagado ? 'success' : 'warning');
      } catch (err) {
        showToast('Error al actualizar pago', 'error');
      }
    });
  });
}

// ── SORTABLE (drag & drop) ───────────────────────────────────────────────────
function initSortable() {
  const list = document.getElementById('sortable-participants');
  if (!list || typeof Sortable === 'undefined') return;

  Sortable.create(list, {
    handle: '.drag-handle',
    animation: 150,
    ghostClass: 'sortable-ghost',
    onEnd: async () => {
      const ids = [...list.querySelectorAll('[data-part-id]')].map(el => el.dataset.partId);
      const pasanakuId = list.dataset.pasanakuId;

      try {
        const body = new URLSearchParams();
        ids.forEach((id, i) => body.append('orden[]', id));
        body.append('pasanaku_id', pasanakuId);

        const res = await fetch('?page=pasanaku&action=reordenar', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body
        });
        const data = await res.json();
        if (data.ok) {
          // Update visual order numbers
          list.querySelectorAll('.p-num').forEach((el, i) => el.textContent = i + 1);
          showToast('Orden actualizado', 'success');
        }
      } catch (err) {
        showToast('Error al guardar orden', 'error');
      }
    }
  });
}

// ── ENTREGA CONFIRM ──────────────────────────────────────────────────────────
function confirmarEntrega() {
  document.getElementById('modal-entrega')?.classList.remove('d-none');
}
function cancelarEntrega() {
  document.getElementById('modal-entrega')?.classList.add('d-none');
}
async function submitEntrega(pasanakuId, participanteId, ronda) {
  try {
    const res = await fetch('?page=pasanaku&action=registrarEntrega', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ pasanaku_id: pasanakuId, participante_id: participanteId, ronda })
    });
    const data = await res.json();
    if (data.ok) {
      cancelarEntrega();
      showToast('¡Entrega registrada exitosamente!', 'success');
      setTimeout(() => location.reload(), 1200);
    } else {
      showToast(data.msg || 'Error al registrar entrega', 'error');
      cancelarEntrega();
    }
  } catch (err) {
    showToast('Error de conexión', 'error');
    cancelarEntrega();
  }
}

// ── PERSONA MODALS ───────────────────────────────────────────────────────────
function openEditPersona(id, nombre, telefono) {
  const modal = document.getElementById('modal-edit-persona');
  if (!modal) return;
  modal.querySelector('[name="id"]').value     = id;
  modal.querySelector('[name="nombre"]').value  = nombre;
  modal.querySelector('[name="telefono"]').value = telefono || '';
  modal.classList.remove('d-none');
}
function openDeletePersona(id, nombre) {
  const modal = document.getElementById('modal-delete-persona');
  if (!modal) return;
  modal.querySelector('[name="id"]').value = id;
  modal.querySelector('.delete-persona-name').textContent = nombre;
  modal.classList.remove('d-none');
}

// ── ADD PARTICIPANTE MODAL ───────────────────────────────────────────────────
function openAddParticipante() {
  document.getElementById('modal-add-participante')?.classList.remove('d-none');
}

// ── INIT ─────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  initPayCells();
  initSortable();
});
