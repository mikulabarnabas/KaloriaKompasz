<script setup>
import { computed } from 'vue'

const year = new Date().getFullYear()

const version = 'v0.1.0'
</script>

<template>
  <footer class="footer">
    <!-- BAL OSZLOP -->
    <div class="column">
      <h3 class="title">KalóriaKompasz</h3>
      <p class="description">
        Egyszerű ételnapló és kalóriaszámláló alkalmazás
        a tudatosabb mindennapokhoz.
      </p>
      <small class="muted">
        © {{ year }} • {{ version }}
      </small>
    </div>

    <!-- JOBB OSZLOP -->
    <div class="column">
      <h4 class="subtitle">Információ</h4>
      <ul class="list">
        <li><i class="pi pi-info-circle" /> Verzió: {{ version }}</li>
        <li><i class="pi pi-shield" /> Adatvédelem</li>
        <li>
          <i class="pi pi-envelope" />
          Kapcsolat:
          <a
            href="mailto:support@kaloriakompasz.hu"
            class="link"
          >
            support@kaloriakompasz.hu
          </a>
        </li>
        <li><i class="pi pi-check-circle" /> Rendszer: aktív</li>
      </ul>
    </div>
  </footer>
</template>

<style scoped>
.footer {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 2rem;
  padding: 2rem;
  border-top: 1px solid var(--surface-border);
  background: var(--surface-0);
}

.column {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.title {
  font-weight: 600;
  font-size: 1.1rem;
}

.subtitle {
  font-weight: 500;
}

.description {
  font-size: 0.9rem;
  color: var(--text-color-secondary);
}

.list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.list li {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
}

.muted {
  color: var(--text-color-secondary);
}
</style>
