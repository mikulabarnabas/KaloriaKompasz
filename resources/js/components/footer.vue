<template>
  <footer class="bg-background-dark border-t border-neutral-border/30 pt-16 pb-8 px-6 font-sans">
    <div class="max-w-7xl mx-auto">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">

        <div class="space-y-4">
          <div class="flex items-center gap-2">
            <div class="size-8 bg-primary rounded-lg flex items-center justify-center">
              <span class="material-symbols-outlined text-background-dark font-black">explore</span>
            </div>
            <span class="text-2xl font-black tracking-tighter uppercase text-main-text">
              Kalória<span class="text-primary">Kompasz</span>
            </span>
          </div>
          <p class="text-secondary-text text-xs leading-relaxed max-w-xs font-medium uppercase tracking-wider opacity-70">
            Modern étrend- és edzésnapló, amely segít elérni a céljaidat. Pontos adatok, tudományos alapok.
          </p>
        </div>

        <div class="space-y-6">
          <h3 class="text-[10px] font-black text-primary uppercase tracking-[0.3em]">Dokumentumok</h3>
          <ul class="space-y-3">
            <li>
             <a href="/documents/ASZF.pdf"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-main-text/60 hover:text-primary text-xs font-bold uppercase tracking-widest transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">description</span>
                    Általános Szerződési Feltételek
                </a>
            </li>
            <li>
                <a href="/documents/adatkez.pdf"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-main-text/60 hover:text-primary text-xs font-bold uppercase tracking-widest transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">shield</span>
                    Adatkezelési Tájékoztató
                </a>
            </li>
          </ul>
        </div>

        <div class="space-y-6">
          <h3 class="text-[10px] font-black text-primary uppercase tracking-[0.3em]">Üzemeltető</h3>
          <div class="text-secondary-text text-[11px] font-bold uppercase tracking-widest space-y-2">
            <p class="text-main-text/80">{{ 'Mikula Barnabás & Bóta Gergely' }}</p>
            <p>Tárhely: {{ 'Rackhost Zrt.' }}</p>
          </div>
        </div>

        <div class="space-y-6">
          <h3 class="text-[10px] font-black text-primary uppercase tracking-[0.3em]">Kapcsolat</h3>
          <div class="space-y-3">
            <a href="mailto:mikula.barnabas1234@gmail.com" class="block text-main-text/60 hover:text-primary text-xs font-bold transition-colors">
              mikula.barnabas1234@gmail.com
            </a>
            <p class="text-main-text/40 text-[10px] font-black uppercase tracking-widest italic">
              Technikumi vizsga projekt (2026)
            </p>
          </div>
        </div>
      </div>

      <div class="pt-8 border-t border-neutral-border/10 flex flex-col md:row justify-between items-center gap-4">
        <p class="text-[9px] font-black text-secondary-text uppercase tracking-[0.4em] opacity-40">
          &copy; 2026 KalóriaKompasz. Minden jog fenntartva.
        </p>
        <div class="flex gap-6 opacity-30">
          <span class="material-symbols-outlined text-main-text text-lg">ecg_heart</span>
          <span class="material-symbols-outlined text-main-text text-lg">fitness_center</span>
          <span class="material-symbols-outlined text-main-text text-lg">nutrition</span>
        </div>
      </div>
    </div>
  </footer>
</template>
